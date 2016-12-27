'use strict';

var currentRKWebsiteHelperModuleEditor = null;
var currentRKWebsiteHelperModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getRKWebsiteHelperModulePopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',scrollbars,resizable';
}

/**
 * Open a popup window with the finder triggered by a CKEditor button.
 */
function RKWebsiteHelperModuleFinderCKEditor(editor, websitUrl)
{
    // Save editor for access in selector window
    currentRKWebsiteHelperModuleEditor = editor;

    editor.popup(
        Routing.generate('rkwebsitehelpermodule_external_finder', { objectType: 'linker', editor: 'ckeditor' }),
        /*width*/ '80%', /*height*/ '70%',
        'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'
    );
}


var rKWebsiteHelperModule = {};

rKWebsiteHelperModule.finder = {};

rKWebsiteHelperModule.finder.onLoad = function (baseId, selectedId)
{
    jQuery('select').change(rKWebsiteHelperModule.finder.onParamChanged);
    jQuery('.btn-success').addClass('hidden');
    jQuery('.btn-default').click(rKWebsiteHelperModule.finder.handleCancel);

    var selectedItems = jQuery('#rkwebsitehelpermoduleItemContainer li a');
    selectedItems.bind('click keypress', function (e) {
        e.preventDefault();
        rKWebsiteHelperModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

rKWebsiteHelperModule.finder.onParamChanged = function ()
{
    jQuery('#rKWebsiteHelperModuleSelectorForm').submit();
};

rKWebsiteHelperModule.finder.handleCancel = function ()
{
    var editor, w;

    editor = jQuery("[id$='editor']").first().val();
    if (editor === 'tinymce') {
        rKWebsiteHelperClosePopup();
    } else if (editor === 'ckeditor') {
        rKWebsiteHelperClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function rKWebsiteHelperGetPasteSnippet(mode, itemId)
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
rKWebsiteHelperModule.finder.selectItem = function (itemId)
{
    var editor, html;

    editor = jQuery("[id$='editorName']").first().val();
    if ('tinymce' === editor) {
        html = rKWebsiteHelperGetPasteSnippet('html', itemId);
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        // other tinymce commands: mceImage, mceInsertLink, mceReplaceContent, see http://www.tinymce.com/wiki.php/Command_identifiers
    } else if ('ckeditor' === editor) {
        if (null !== window.opener.currentRKWebsiteHelperModuleEditor) {
            html = rKWebsiteHelperGetPasteSnippet('html', itemId);

            window.opener.currentRKWebsiteHelperModuleEditor.insertHtml(html);
        }
    } else {
        alert('Insert into Editor: ' + editor);
    }
    rKWebsiteHelperClosePopup();
};

function rKWebsiteHelperClosePopup()
{
    window.opener.focus();
    window.close();
}




//=============================================================================
// RKWebsiteHelperModule item selector for Forms
//=============================================================================

rKWebsiteHelperModule.itemSelector = {};
rKWebsiteHelperModule.itemSelector.items = {};
rKWebsiteHelperModule.itemSelector.baseId = 0;
rKWebsiteHelperModule.itemSelector.selectedId = 0;

rKWebsiteHelperModule.itemSelector.onLoad = function (baseId, selectedId)
{
    rKWebsiteHelperModule.itemSelector.baseId = baseId;
    rKWebsiteHelperModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#rKWebsiteHelperModuleObjectType').change(rKWebsiteHelperModule.itemSelector.onParamChanged);

    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        jQuery('#' + baseId + '_catidMain').change(rKWebsiteHelperModule.itemSelector.onParamChanged);
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        jQuery('#' + baseId + '_catidsMain').change(rKWebsiteHelperModule.itemSelector.onParamChanged);
    }
    jQuery('#' + baseId + 'Id').change(rKWebsiteHelperModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(rKWebsiteHelperModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(rKWebsiteHelperModule.itemSelector.onParamChanged);
    jQuery('#rKWebsiteHelperModuleSearchGo').click(rKWebsiteHelperModule.itemSelector.onParamChanged);
    jQuery('#rKWebsiteHelperModuleSearchGo').keypress(rKWebsiteHelperModule.itemSelector.onParamChanged);

    rKWebsiteHelperModule.itemSelector.getItemList();
};

rKWebsiteHelperModule.itemSelector.onParamChanged = function ()
{
    jQuery('#ajax_indicator').removeClass('hidden');

    rKWebsiteHelperModule.itemSelector.getItemList();
};

rKWebsiteHelperModule.itemSelector.getItemList = function ()
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
        baseId = rKWebsiteHelperModule.itemSelector.baseId;
        rKWebsiteHelperModule.itemSelector.items[baseId] = res.data;
        jQuery('#ajax_indicator').addClass('hidden');
        rKWebsiteHelperModule.itemSelector.updateItemDropdownEntries();
        rKWebsiteHelperModule.itemSelector.updatePreview();
    });
};

rKWebsiteHelperModule.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = rKWebsiteHelperModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = rKWebsiteHelperModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.options[i] = new Option(item.title, item.id, false);
    }

    if (rKWebsiteHelperModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(rKWebsiteHelperModule.itemSelector.selectedId);
    }
};

rKWebsiteHelperModule.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = rKWebsiteHelperModule.itemSelector.baseId;
    items = rKWebsiteHelperModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (rKWebsiteHelperModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id === rKWebsiteHelperModule.itemSelector.selectedId) {
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

rKWebsiteHelperModule.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = rKWebsiteHelperModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    preview = window.atob(rKWebsiteHelperModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    rKWebsiteHelperModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
};
