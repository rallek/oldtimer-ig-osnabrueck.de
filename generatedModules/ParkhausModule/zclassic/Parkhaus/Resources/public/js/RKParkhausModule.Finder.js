'use strict';

var currentRKParkhausModuleEditor = null;
var currentRKParkhausModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getRKParkhausModulePopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',scrollbars,resizable';
}

/**
 * Open a popup window with the finder triggered by a CKEditor button.
 */
function RKParkhausModuleFinderCKEditor(editor, parkhaUrl)
{
    // Save editor for access in selector window
    currentRKParkhausModuleEditor = editor;

    editor.popup(
        Routing.generate('rkparkhausmodule_external_finder', { objectType: 'vehicle', editor: 'ckeditor' }),
        /*width*/ '80%', /*height*/ '70%',
        'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'
    );
}


var rKParkhausModule = {};

rKParkhausModule.finder = {};

rKParkhausModule.finder.onLoad = function (baseId, selectedId)
{
    jQuery('select').change(rKParkhausModule.finder.onParamChanged);
    jQuery('.btn-success').addClass('hidden');
    jQuery('.btn-default').click(rKParkhausModule.finder.handleCancel);

    var selectedItems = jQuery('#rkparkhausmoduleItemContainer li a');
    selectedItems.bind('click keypress', function (e) {
        e.preventDefault();
        rKParkhausModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

rKParkhausModule.finder.onParamChanged = function ()
{
    jQuery('#rKParkhausModuleSelectorForm').submit();
};

rKParkhausModule.finder.handleCancel = function ()
{
    var editor, w;

    editor = jQuery("[id$='editor']").first().val();
    if (editor === 'tinymce') {
        rKParkhausClosePopup();
    } else if (editor === 'ckeditor') {
        rKParkhausClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function rKParkhausGetPasteSnippet(mode, itemId)
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
rKParkhausModule.finder.selectItem = function (itemId)
{
    var editor, html;

    editor = jQuery("[id$='editorName']").first().val();
    if ('tinymce' === editor) {
        html = rKParkhausGetPasteSnippet('html', itemId);
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        // other tinymce commands: mceImage, mceInsertLink, mceReplaceContent, see http://www.tinymce.com/wiki.php/Command_identifiers
    } else if ('ckeditor' === editor) {
        if (null !== window.opener.currentRKParkhausModuleEditor) {
            html = rKParkhausGetPasteSnippet('html', itemId);

            window.opener.currentRKParkhausModuleEditor.insertHtml(html);
        }
    } else {
        alert('Insert into Editor: ' + editor);
    }
    rKParkhausClosePopup();
};

function rKParkhausClosePopup()
{
    window.opener.focus();
    window.close();
}




//=============================================================================
// RKParkhausModule item selector for Forms
//=============================================================================

rKParkhausModule.itemSelector = {};
rKParkhausModule.itemSelector.items = {};
rKParkhausModule.itemSelector.baseId = 0;
rKParkhausModule.itemSelector.selectedId = 0;

rKParkhausModule.itemSelector.onLoad = function (baseId, selectedId)
{
    rKParkhausModule.itemSelector.baseId = baseId;
    rKParkhausModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#rKParkhausModuleObjectType').change(rKParkhausModule.itemSelector.onParamChanged);

    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        jQuery('#' + baseId + '_catidMain').change(rKParkhausModule.itemSelector.onParamChanged);
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        jQuery('#' + baseId + '_catidsMain').change(rKParkhausModule.itemSelector.onParamChanged);
    }
    jQuery('#' + baseId + 'Id').change(rKParkhausModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(rKParkhausModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(rKParkhausModule.itemSelector.onParamChanged);
    jQuery('#rKParkhausModuleSearchGo').click(rKParkhausModule.itemSelector.onParamChanged);
    jQuery('#rKParkhausModuleSearchGo').keypress(rKParkhausModule.itemSelector.onParamChanged);

    rKParkhausModule.itemSelector.getItemList();
};

rKParkhausModule.itemSelector.onParamChanged = function ()
{
    jQuery('#ajax_indicator').removeClass('hidden');

    rKParkhausModule.itemSelector.getItemList();
};

rKParkhausModule.itemSelector.getItemList = function ()
{
    var baseId, params;

    baseId = parkhaus.itemSelector.baseId;
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
        url: Routing.generate('rkparkhausmodule_ajax_getitemlistfinder'),
        data: params
    }).done(function(res) {
        // get data returned by the ajax response
        var baseId;
        baseId = rKParkhausModule.itemSelector.baseId;
        rKParkhausModule.itemSelector.items[baseId] = res.data;
        jQuery('#ajax_indicator').addClass('hidden');
        rKParkhausModule.itemSelector.updateItemDropdownEntries();
        rKParkhausModule.itemSelector.updatePreview();
    });
};

rKParkhausModule.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = rKParkhausModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = rKParkhausModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.options[i] = new Option(item.title, item.id, false);
    }

    if (rKParkhausModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(rKParkhausModule.itemSelector.selectedId);
    }
};

rKParkhausModule.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = rKParkhausModule.itemSelector.baseId;
    items = rKParkhausModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (rKParkhausModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id === rKParkhausModule.itemSelector.selectedId) {
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

rKParkhausModule.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = rKParkhausModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    preview = window.atob(rKParkhausModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    rKParkhausModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
};
