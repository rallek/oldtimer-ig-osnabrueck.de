'use strict';

var currentRKDownLoadModuleEditor = null;
var currentRKDownLoadModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getRKDownLoadModulePopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',scrollbars,resizable';
}

/**
 * Open a popup window with the finder triggered by a CKEditor button.
 */
function RKDownLoadModuleFinderCKEditor(editor, downloUrl)
{
    // Save editor for access in selector window
    currentRKDownLoadModuleEditor = editor;

    editor.popup(
        Routing.generate('rkdownloadmodule_external_finder', { objectType: 'file', editor: 'ckeditor' }),
        /*width*/ '80%', /*height*/ '70%',
        'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'
    );
}


var rKDownLoadModule = {};

rKDownLoadModule.finder = {};

rKDownLoadModule.finder.onLoad = function (baseId, selectedId)
{
    jQuery('select').not("[id$='pasteas']").change(rKDownLoadModule.finder.onParamChanged);
    
    jQuery('.btn-default').click(rKDownLoadModule.finder.handleCancel);

    var selectedItems = jQuery('#rkdownloadmoduleItemContainer li a');
    selectedItems.bind('click keypress', function (e) {
        e.preventDefault();
        rKDownLoadModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

rKDownLoadModule.finder.onParamChanged = function ()
{
    jQuery('#rKDownLoadModuleSelectorForm').submit();
};

rKDownLoadModule.finder.handleCancel = function ()
{
    var editor;

    editor = jQuery("[id$='editor']").first().val();
    if ('tinymce' === editor) {
        rKDownLoadClosePopup();
    } else if ('ckeditor' === editor) {
        rKDownLoadClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function rKDownLoadGetPasteSnippet(mode, itemId)
{
    var quoteFinder, itemUrl, itemTitle, itemDescription, pasteMode;

    quoteFinder = new RegExp('"', 'g');
    itemUrl = jQuery('#url' + itemId).val().replace(quoteFinder, '');
    itemTitle = jQuery('#title' + itemId).val().replace(quoteFinder, '').trim();
    itemDescription = jQuery('#desc' + itemId).val().replace(quoteFinder, '').trim();
    pasteMode = jQuery("[id$='pasteas']").first().val();

    if (pasteMode === '2' || pasteMode !== '1') {
        return '' + itemId;
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
rKDownLoadModule.finder.selectItem = function (itemId)
{
    var editor, html;

    editor = jQuery("[id$='editor']").first().val();
    if ('tinymce' === editor) {
        html = rKDownLoadGetPasteSnippet('html', itemId);
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        // other tinymce commands: mceImage, mceInsertLink, mceReplaceContent, see http://www.tinymce.com/wiki.php/Command_identifiers
    } else if ('ckeditor' === editor) {
        if (null !== window.opener.currentRKDownLoadModuleEditor) {
            html = rKDownLoadGetPasteSnippet('html', itemId);

            window.opener.currentRKDownLoadModuleEditor.insertHtml(html);
        }
    } else {
        alert('Insert into Editor: ' + editor);
    }
    rKDownLoadClosePopup();
};

function rKDownLoadClosePopup()
{
    window.opener.focus();
    window.close();
}




//=============================================================================
// RKDownLoadModule item selector for Forms
//=============================================================================

rKDownLoadModule.itemSelector = {};
rKDownLoadModule.itemSelector.items = {};
rKDownLoadModule.itemSelector.baseId = 0;
rKDownLoadModule.itemSelector.selectedId = 0;

rKDownLoadModule.itemSelector.onLoad = function (baseId, selectedId)
{
    rKDownLoadModule.itemSelector.baseId = baseId;
    rKDownLoadModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#rKDownLoadModuleObjectType').change(rKDownLoadModule.itemSelector.onParamChanged);

    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        jQuery('#' + baseId + '_catidMain').change(rKDownLoadModule.itemSelector.onParamChanged);
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        jQuery('#' + baseId + '_catidsMain').change(rKDownLoadModule.itemSelector.onParamChanged);
    }
    jQuery('#' + baseId + 'Id').change(rKDownLoadModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(rKDownLoadModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(rKDownLoadModule.itemSelector.onParamChanged);
    jQuery('#rKDownLoadModuleSearchGo').click(rKDownLoadModule.itemSelector.onParamChanged);
    jQuery('#rKDownLoadModuleSearchGo').keypress(rKDownLoadModule.itemSelector.onParamChanged);

    rKDownLoadModule.itemSelector.getItemList();
};

rKDownLoadModule.itemSelector.onParamChanged = function ()
{
    jQuery('#ajax_indicator').removeClass('hidden');

    rKDownLoadModule.itemSelector.getItemList();
};

rKDownLoadModule.itemSelector.getItemList = function ()
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
        baseId = rKDownLoadModule.itemSelector.baseId;
        rKDownLoadModule.itemSelector.items[baseId] = res.data;
        jQuery('#ajax_indicator').addClass('hidden');
        rKDownLoadModule.itemSelector.updateItemDropdownEntries();
        rKDownLoadModule.itemSelector.updatePreview();
    });
};

rKDownLoadModule.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = rKDownLoadModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = rKDownLoadModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.options[i] = new Option(item.title, item.id, false);
    }

    if (rKDownLoadModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(rKDownLoadModule.itemSelector.selectedId);
    }
};

rKDownLoadModule.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = rKDownLoadModule.itemSelector.baseId;
    items = rKDownLoadModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (rKDownLoadModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id === rKDownLoadModule.itemSelector.selectedId) {
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

rKDownLoadModule.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = rKDownLoadModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    preview = window.atob(rKDownLoadModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    rKDownLoadModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
};
