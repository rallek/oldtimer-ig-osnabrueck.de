'use strict';

var currentRKWebsitehelperModuleEditor = null;
var currentRKWebsitehelperModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getRKWebsitehelperModulePopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',scrollbars,resizable';
}

/**
 * Open a popup window with the finder triggered by a CKEditor button.
 */
function RKWebsitehelperModuleFinderCKEditor(editor, websitUrl)
{
    // Save editor for access in selector window
    currentRKWebsitehelperModuleEditor = editor;

    editor.popup(
        Routing.generate('rkwebsitehelpermodule_external_finder', { objectType: 'linker', editor: 'ckeditor' }),
        /*width*/ '80%', /*height*/ '70%',
        'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'
    );
}


var rKWebsitehelperModule = {};

rKWebsitehelperModule.finder = {};

rKWebsitehelperModule.finder.onLoad = function (baseId, selectedId)
{
    jQuery('select').change(rKWebsitehelperModule.finder.onParamChanged);
    jQuery('.btn-success').addClass('hidden');
    jQuery('.btn-default').click(rKWebsitehelperModule.finder.handleCancel);

    var selectedItems = jQuery('#rkwebsitehelpermoduleItemContainer li a');
    selectedItems.bind('click keypress', function (e) {
        e.preventDefault();
        rKWebsitehelperModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

rKWebsitehelperModule.finder.onParamChanged = function ()
{
    jQuery('#rKWebsitehelperModuleSelectorForm').submit();
};

rKWebsitehelperModule.finder.handleCancel = function ()
{
    var editor, w;

    editor = jQuery("[id$='editor']").first().val();
    if (editor === 'tinymce') {
        rKWebsitehelperClosePopup();
    } else if (editor === 'ckeditor') {
        rKWebsitehelperClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function rKWebsitehelperGetPasteSnippet(mode, itemId)
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
rKWebsitehelperModule.finder.selectItem = function (itemId)
{
    var editor, html;

    editor = jQuery("[id$='editorName']").first().val();
    if ('tinymce' === editor) {
        html = rKWebsitehelperGetPasteSnippet('html', itemId);
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        // other tinymce commands: mceImage, mceInsertLink, mceReplaceContent, see http://www.tinymce.com/wiki.php/Command_identifiers
    } else if ('ckeditor' === editor) {
        if (null !== window.opener.currentRKWebsitehelperModuleEditor) {
            html = rKWebsitehelperGetPasteSnippet('html', itemId);

            window.opener.currentRKWebsitehelperModuleEditor.insertHtml(html);
        }
    } else {
        alert('Insert into Editor: ' + editor);
    }
    rKWebsitehelperClosePopup();
};

function rKWebsitehelperClosePopup()
{
    window.opener.focus();
    window.close();
}




//=============================================================================
// RKWebsitehelperModule item selector for Forms
//=============================================================================

rKWebsitehelperModule.itemSelector = {};
rKWebsitehelperModule.itemSelector.items = {};
rKWebsitehelperModule.itemSelector.baseId = 0;
rKWebsitehelperModule.itemSelector.selectedId = 0;

rKWebsitehelperModule.itemSelector.onLoad = function (baseId, selectedId)
{
    rKWebsitehelperModule.itemSelector.baseId = baseId;
    rKWebsitehelperModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#rKWebsitehelperModuleObjectType').change(rKWebsitehelperModule.itemSelector.onParamChanged);

    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        jQuery('#' + baseId + '_catidMain').change(rKWebsitehelperModule.itemSelector.onParamChanged);
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        jQuery('#' + baseId + '_catidsMain').change(rKWebsitehelperModule.itemSelector.onParamChanged);
    }
    jQuery('#' + baseId + 'Id').change(rKWebsitehelperModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(rKWebsitehelperModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(rKWebsitehelperModule.itemSelector.onParamChanged);
    jQuery('#rKWebsitehelperModuleSearchGo').click(rKWebsitehelperModule.itemSelector.onParamChanged);
    jQuery('#rKWebsitehelperModuleSearchGo').keypress(rKWebsitehelperModule.itemSelector.onParamChanged);

    rKWebsitehelperModule.itemSelector.getItemList();
};

rKWebsitehelperModule.itemSelector.onParamChanged = function ()
{
    jQuery('#ajax_indicator').removeClass('hidden');

    rKWebsitehelperModule.itemSelector.getItemList();
};

rKWebsitehelperModule.itemSelector.getItemList = function ()
{
    var baseId, params;

    baseId = websitehelper.itemSelector.baseId;
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
        url: Routing.generate('rkwebsitehelpermodule_ajax_getitemlistfinder'),
        data: params
    }).done(function(res) {
        // get data returned by the ajax response
        var baseId;
        baseId = rKWebsitehelperModule.itemSelector.baseId;
        rKWebsitehelperModule.itemSelector.items[baseId] = res.data;
        jQuery('#ajax_indicator').addClass('hidden');
        rKWebsitehelperModule.itemSelector.updateItemDropdownEntries();
        rKWebsitehelperModule.itemSelector.updatePreview();
    });
};

rKWebsitehelperModule.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = rKWebsitehelperModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = rKWebsitehelperModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.options[i] = new Option(item.title, item.id, false);
    }

    if (rKWebsitehelperModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(rKWebsitehelperModule.itemSelector.selectedId);
    }
};

rKWebsitehelperModule.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = rKWebsitehelperModule.itemSelector.baseId;
    items = rKWebsitehelperModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (rKWebsitehelperModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id === rKWebsitehelperModule.itemSelector.selectedId) {
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

rKWebsitehelperModule.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = rKWebsitehelperModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    preview = window.atob(rKWebsitehelperModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    rKWebsitehelperModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
};
