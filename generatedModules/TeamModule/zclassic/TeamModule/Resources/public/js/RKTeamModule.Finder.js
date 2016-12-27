'use strict';

var currentRKTeamModuleEditor = null;
var currentRKTeamModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getRKTeamModulePopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',scrollbars,resizable';
}

/**
 * Open a popup window with the finder triggered by a CKEditor button.
 */
function RKTeamModuleFinderCKEditor(editor, teamUrl)
{
    // Save editor for access in selector window
    currentRKTeamModuleEditor = editor;

    editor.popup(
        Routing.generate('rkteammodule_external_finder', { objectType: 'person', editor: 'ckeditor' }),
        /*width*/ '80%', /*height*/ '70%',
        'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'
    );
}


var rKTeamModule = {};

rKTeamModule.finder = {};

rKTeamModule.finder.onLoad = function (baseId, selectedId)
{
    jQuery('select').change(rKTeamModule.finder.onParamChanged);
    jQuery('.btn-success').addClass('hidden');
    jQuery('.btn-default').click(rKTeamModule.finder.handleCancel);

    var selectedItems = jQuery('#rkteammoduleItemContainer li a');
    selectedItems.bind('click keypress', function (e) {
        e.preventDefault();
        rKTeamModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

rKTeamModule.finder.onParamChanged = function ()
{
    jQuery('#rKTeamModuleSelectorForm').submit();
};

rKTeamModule.finder.handleCancel = function ()
{
    var editor, w;

    editor = jQuery("[id$='editor']").first().val();
    if (editor === 'tinymce') {
        rKTeamClosePopup();
    } else if (editor === 'ckeditor') {
        rKTeamClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function rKTeamGetPasteSnippet(mode, itemId)
{
    var quoteFinder, itemUrl, itemTitle, itemDescription, pasteMode;

    quoteFinder = new RegExp('"', 'g');
    itemUrl = jQuery('#url' + itemId).val().replace(quoteFinder, '');
    itemTitle = jQuery('#title' + itemId).val().replace(quoteFinder, '');
    itemDescription = jQuery('#desc' + itemId).val().replace(quoteFinder, '');
    pasteMode = jQuery("[id$='pasteas']").first().val();

    if (pasteMode === '2' || pasteMode !== '1') {
        return itemId;
    }

    // return link to item
    if (mode === 'url') {
        // plugin mode
        return itemUrl;
    }

    // editor mode
    return '<a href="' + itemUrl + '" title="' + itemDescription + '">' + itemTitle + '</a>';
}


// User clicks on "select item" button
rKTeamModule.finder.selectItem = function (itemId)
{
    var editor, html;

    editor = jQuery("[id$='editorName']").first().val();
    if ('tinymce' === editor) {
        html = rKTeamGetPasteSnippet('html', itemId);
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        // other tinymce commands: mceImage, mceInsertLink, mceReplaceContent, see http://www.tinymce.com/wiki.php/Command_identifiers
    } else if ('ckeditor' === editor) {
        if (null !== window.opener.currentRKTeamModuleEditor) {
            html = rKTeamGetPasteSnippet('html', itemId);

            window.opener.currentRKTeamModuleEditor.insertHtml(html);
        }
    } else {
        alert('Insert into Editor: ' + editor);
    }
    rKTeamClosePopup();
};

function rKTeamClosePopup()
{
    window.opener.focus();
    window.close();
}




//=============================================================================
// RKTeamModule item selector for Forms
//=============================================================================

rKTeamModule.itemSelector = {};
rKTeamModule.itemSelector.items = {};
rKTeamModule.itemSelector.baseId = 0;
rKTeamModule.itemSelector.selectedId = 0;

rKTeamModule.itemSelector.onLoad = function (baseId, selectedId)
{
    rKTeamModule.itemSelector.baseId = baseId;
    rKTeamModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#rKTeamModuleObjectType').change(rKTeamModule.itemSelector.onParamChanged);

    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        jQuery('#' + baseId + '_catidMain').change(rKTeamModule.itemSelector.onParamChanged);
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        jQuery('#' + baseId + '_catidsMain').change(rKTeamModule.itemSelector.onParamChanged);
    }
    jQuery('#' + baseId + 'Id').change(rKTeamModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(rKTeamModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(rKTeamModule.itemSelector.onParamChanged);
    jQuery('#rKTeamModuleSearchGo').click(rKTeamModule.itemSelector.onParamChanged);
    jQuery('#rKTeamModuleSearchGo').keypress(rKTeamModule.itemSelector.onParamChanged);

    rKTeamModule.itemSelector.getItemList();
};

rKTeamModule.itemSelector.onParamChanged = function ()
{
    jQuery('#ajax_indicator').removeClass('hidden');

    rKTeamModule.itemSelector.getItemList();
};

rKTeamModule.itemSelector.getItemList = function ()
{
    var baseId, params;

    baseId = team.itemSelector.baseId;
    params = 'ot=' + baseId + '&';
    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        params += 'catidMain=' + jQuery('#' + baseId + '_catidMain').val() + '&';
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        params += 'catidsMain=' + jQuery('#' + baseId + '_catidsMain').val() + '&';
    }
    params += 'sort=' + jQuery('#' + baseId + 'Sort').val() + '&' +
              'sortdir=' + jQuery('#' + baseId + 'SortDir').val() + '&' +
              'q=' + jQuery('#' + baseId + 'SearchTerm').val();

    jQuery.ajax({
        type: 'POST',
        url: Routing.generate('rkteammodule_ajax_getitemlistfinder'),
        data: params
    }).done(function(res) {
        // get data returned by the ajax response
        var baseId;
        baseId = rKTeamModule.itemSelector.baseId;
        rKTeamModule.itemSelector.items[baseId] = res.data;
        jQuery('#ajax_indicator').addClass('hidden');
        rKTeamModule.itemSelector.updateItemDropdownEntries();
        rKTeamModule.itemSelector.updatePreview();
    });
};

rKTeamModule.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = rKTeamModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = rKTeamModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.options[i] = new Option(item.title, item.id, false);
    }

    if (rKTeamModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(rKTeamModule.itemSelector.selectedId);
    }
};

rKTeamModule.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = rKTeamModule.itemSelector.baseId;
    items = rKTeamModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (rKTeamModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id === rKTeamModule.itemSelector.selectedId) {
                selectedElement = items[i];
                break;
            }
        }
    }

    if (null !== selectedElement) {
        jQuery('#' + baseId + 'PreviewContainer')
            .html(window.atob(selectedElement.previewInfo))
            .removeClass('hidden');
    }
};

rKTeamModule.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = rKTeamModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    preview = window.atob(rKTeamModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    rKTeamModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
};
