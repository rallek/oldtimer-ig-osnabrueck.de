'use strict';

var currentRKSponsoringModuleEditor = null;
var currentRKSponsoringModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getRKSponsoringModulePopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',scrollbars,resizable';
}

/**
 * Open a popup window with the finder triggered by a CKEditor button.
 */
function RKSponsoringModuleFinderCKEditor(editor, sponsoUrl)
{
    // Save editor for access in selector window
    currentRKSponsoringModuleEditor = editor;

    editor.popup(
        Routing.generate('rksponsoringmodule_external_finder', { objectType: 'sponsor', editor: 'ckeditor' }),
        /*width*/ '80%', /*height*/ '70%',
        'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'
    );
}


var rKSponsoringModule = {};

rKSponsoringModule.finder = {};

rKSponsoringModule.finder.onLoad = function (baseId, selectedId)
{
    jQuery('select').change(rKSponsoringModule.finder.onParamChanged);
    jQuery('.btn-success').addClass('hidden');
    jQuery('.btn-default').click(rKSponsoringModule.finder.handleCancel);

    var selectedItems = jQuery('#rksponsoringmoduleItemContainer li a');
    selectedItems.bind('click keypress', function (e) {
        e.preventDefault();
        rKSponsoringModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

rKSponsoringModule.finder.onParamChanged = function ()
{
    jQuery('#rKSponsoringModuleSelectorForm').submit();
};

rKSponsoringModule.finder.handleCancel = function ()
{
    var editor, w;

    editor = jQuery("[id$='editor']").first().val();
    if (editor === 'tinymce') {
        rKSponsoringClosePopup();
    } else if (editor === 'ckeditor') {
        rKSponsoringClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function rKSponsoringGetPasteSnippet(mode, itemId)
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
rKSponsoringModule.finder.selectItem = function (itemId)
{
    var editor, html;

    editor = jQuery("[id$='editorName']").first().val();
    if ('tinymce' === editor) {
        html = rKSponsoringGetPasteSnippet('html', itemId);
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        // other tinymce commands: mceImage, mceInsertLink, mceReplaceContent, see http://www.tinymce.com/wiki.php/Command_identifiers
    } else if ('ckeditor' === editor) {
        if (null !== window.opener.currentRKSponsoringModuleEditor) {
            html = rKSponsoringGetPasteSnippet('html', itemId);

            window.opener.currentRKSponsoringModuleEditor.insertHtml(html);
        }
    } else {
        alert('Insert into Editor: ' + editor);
    }
    rKSponsoringClosePopup();
};

function rKSponsoringClosePopup()
{
    window.opener.focus();
    window.close();
}




//=============================================================================
// RKSponsoringModule item selector for Forms
//=============================================================================

rKSponsoringModule.itemSelector = {};
rKSponsoringModule.itemSelector.items = {};
rKSponsoringModule.itemSelector.baseId = 0;
rKSponsoringModule.itemSelector.selectedId = 0;

rKSponsoringModule.itemSelector.onLoad = function (baseId, selectedId)
{
    rKSponsoringModule.itemSelector.baseId = baseId;
    rKSponsoringModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#rKSponsoringModuleObjectType').change(rKSponsoringModule.itemSelector.onParamChanged);

    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        jQuery('#' + baseId + '_catidMain').change(rKSponsoringModule.itemSelector.onParamChanged);
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        jQuery('#' + baseId + '_catidsMain').change(rKSponsoringModule.itemSelector.onParamChanged);
    }
    jQuery('#' + baseId + 'Id').change(rKSponsoringModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(rKSponsoringModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(rKSponsoringModule.itemSelector.onParamChanged);
    jQuery('#rKSponsoringModuleSearchGo').click(rKSponsoringModule.itemSelector.onParamChanged);
    jQuery('#rKSponsoringModuleSearchGo').keypress(rKSponsoringModule.itemSelector.onParamChanged);

    rKSponsoringModule.itemSelector.getItemList();
};

rKSponsoringModule.itemSelector.onParamChanged = function ()
{
    jQuery('#ajax_indicator').removeClass('hidden');

    rKSponsoringModule.itemSelector.getItemList();
};

rKSponsoringModule.itemSelector.getItemList = function ()
{
    var baseId, params;

    baseId = sponsoring.itemSelector.baseId;
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
        url: Routing.generate('rksponsoringmodule_ajax_getitemlistfinder'),
        data: params
    }).done(function(res) {
        // get data returned by the ajax response
        var baseId;
        baseId = rKSponsoringModule.itemSelector.baseId;
        rKSponsoringModule.itemSelector.items[baseId] = res.data;
        jQuery('#ajax_indicator').addClass('hidden');
        rKSponsoringModule.itemSelector.updateItemDropdownEntries();
        rKSponsoringModule.itemSelector.updatePreview();
    });
};

rKSponsoringModule.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = rKSponsoringModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = rKSponsoringModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.options[i] = new Option(item.title, item.id, false);
    }

    if (rKSponsoringModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(rKSponsoringModule.itemSelector.selectedId);
    }
};

rKSponsoringModule.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = rKSponsoringModule.itemSelector.baseId;
    items = rKSponsoringModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (rKSponsoringModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id === rKSponsoringModule.itemSelector.selectedId) {
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

rKSponsoringModule.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = rKSponsoringModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    preview = window.atob(rKSponsoringModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    rKSponsoringModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
};
