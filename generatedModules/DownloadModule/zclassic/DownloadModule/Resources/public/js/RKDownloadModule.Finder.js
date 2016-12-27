'use strict';

var currentRKDownloadModuleEditor = null;
var currentRKDownloadModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getRKDownloadModulePopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',scrollbars,resizable';
}

/**
 * Open a popup window with the finder triggered by a CKEditor button.
 */
function RKDownloadModuleFinderCKEditor(editor, downloUrl)
{
    // Save editor for access in selector window
    currentRKDownloadModuleEditor = editor;

    editor.popup(
        Routing.generate('rkdownloadmodule_external_finder', { objectType: 'file', editor: 'ckeditor' }),
        /*width*/ '80%', /*height*/ '70%',
        'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'
    );
}


var rKDownloadModule = {};

rKDownloadModule.finder = {};

rKDownloadModule.finder.onLoad = function (baseId, selectedId)
{
    jQuery('select').change(rKDownloadModule.finder.onParamChanged);
    jQuery('.btn-success').addClass('hidden');
    jQuery('.btn-default').click(rKDownloadModule.finder.handleCancel);

    var selectedItems = jQuery('#rkdownloadmoduleItemContainer li a');
    selectedItems.bind('click keypress', function (e) {
        e.preventDefault();
        rKDownloadModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

rKDownloadModule.finder.onParamChanged = function ()
{
    jQuery('#rKDownloadModuleSelectorForm').submit();
};

rKDownloadModule.finder.handleCancel = function ()
{
    var editor, w;

    editor = jQuery("[id$='editor']").first().val();
    if (editor === 'tinymce') {
        rKDownloadClosePopup();
    } else if (editor === 'ckeditor') {
        rKDownloadClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function rKDownloadGetPasteSnippet(mode, itemId)
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
rKDownloadModule.finder.selectItem = function (itemId)
{
    var editor, html;

    editor = jQuery("[id$='editorName']").first().val();
    if ('tinymce' === editor) {
        html = rKDownloadGetPasteSnippet('html', itemId);
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        // other tinymce commands: mceImage, mceInsertLink, mceReplaceContent, see http://www.tinymce.com/wiki.php/Command_identifiers
    } else if ('ckeditor' === editor) {
        if (null !== window.opener.currentRKDownloadModuleEditor) {
            html = rKDownloadGetPasteSnippet('html', itemId);

            window.opener.currentRKDownloadModuleEditor.insertHtml(html);
        }
    } else {
        alert('Insert into Editor: ' + editor);
    }
    rKDownloadClosePopup();
};

function rKDownloadClosePopup()
{
    window.opener.focus();
    window.close();
}




//=============================================================================
// RKDownloadModule item selector for Forms
//=============================================================================

rKDownloadModule.itemSelector = {};
rKDownloadModule.itemSelector.items = {};
rKDownloadModule.itemSelector.baseId = 0;
rKDownloadModule.itemSelector.selectedId = 0;

rKDownloadModule.itemSelector.onLoad = function (baseId, selectedId)
{
    rKDownloadModule.itemSelector.baseId = baseId;
    rKDownloadModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#rKDownloadModuleObjectType').change(rKDownloadModule.itemSelector.onParamChanged);

    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        jQuery('#' + baseId + '_catidMain').change(rKDownloadModule.itemSelector.onParamChanged);
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        jQuery('#' + baseId + '_catidsMain').change(rKDownloadModule.itemSelector.onParamChanged);
    }
    jQuery('#' + baseId + 'Id').change(rKDownloadModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(rKDownloadModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(rKDownloadModule.itemSelector.onParamChanged);
    jQuery('#rKDownloadModuleSearchGo').click(rKDownloadModule.itemSelector.onParamChanged);
    jQuery('#rKDownloadModuleSearchGo').keypress(rKDownloadModule.itemSelector.onParamChanged);

    rKDownloadModule.itemSelector.getItemList();
};

rKDownloadModule.itemSelector.onParamChanged = function ()
{
    jQuery('#ajax_indicator').removeClass('hidden');

    rKDownloadModule.itemSelector.getItemList();
};

rKDownloadModule.itemSelector.getItemList = function ()
{
    var baseId, params;

    baseId = download.itemSelector.baseId;
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
        url: Routing.generate('rkdownloadmodule_ajax_getitemlistfinder'),
        data: params
    }).done(function(res) {
        // get data returned by the ajax response
        var baseId;
        baseId = rKDownloadModule.itemSelector.baseId;
        rKDownloadModule.itemSelector.items[baseId] = res.data;
        jQuery('#ajax_indicator').addClass('hidden');
        rKDownloadModule.itemSelector.updateItemDropdownEntries();
        rKDownloadModule.itemSelector.updatePreview();
    });
};

rKDownloadModule.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = rKDownloadModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = rKDownloadModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.options[i] = new Option(item.title, item.id, false);
    }

    if (rKDownloadModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(rKDownloadModule.itemSelector.selectedId);
    }
};

rKDownloadModule.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = rKDownloadModule.itemSelector.baseId;
    items = rKDownloadModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (rKDownloadModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id === rKDownloadModule.itemSelector.selectedId) {
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

rKDownloadModule.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = rKDownloadModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    preview = window.atob(rKDownloadModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    rKDownloadModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
};
