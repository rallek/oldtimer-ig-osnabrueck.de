'use strict';

var currentRKParkHausModuleEditor = null;
var currentRKParkHausModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getRKParkHausModulePopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',scrollbars,resizable';
}

/**
 * Open a popup window with the finder triggered by a CKEditor button.
 */
function RKParkHausModuleFinderCKEditor(editor, parkhaUrl)
{
    // Save editor for access in selector window
    currentRKParkHausModuleEditor = editor;

    editor.popup(
        Routing.generate('rkparkhausmodule_external_finder', { objectType: 'vehicle', editor: 'ckeditor' }),
        /*width*/ '80%', /*height*/ '70%',
        'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'
    );
}


var rKParkHausModule = {};

rKParkHausModule.finder = {};

rKParkHausModule.finder.onLoad = function (baseId, selectedId)
{
    jQuery('select').not("[id$='pasteas']").change(rKParkHausModule.finder.onParamChanged);
    
    jQuery('.btn-default').click(rKParkHausModule.finder.handleCancel);

    var selectedItems = jQuery('#rkparkhausmoduleItemContainer li a');
    selectedItems.bind('click keypress', function (e) {
        e.preventDefault();
        rKParkHausModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

rKParkHausModule.finder.onParamChanged = function ()
{
    jQuery('#rKParkHausModuleSelectorForm').submit();
};

rKParkHausModule.finder.handleCancel = function ()
{
    var editor;

    editor = jQuery("[id$='editor']").first().val();
    if ('tinymce' === editor) {
        rKParkHausClosePopup();
    } else if ('ckeditor' === editor) {
        rKParkHausClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function rKParkHausGetPasteSnippet(mode, itemId)
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
rKParkHausModule.finder.selectItem = function (itemId)
{
    var editor, html;

    editor = jQuery("[id$='editor']").first().val();
    if ('tinymce' === editor) {
        html = rKParkHausGetPasteSnippet('html', itemId);
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        // other tinymce commands: mceImage, mceInsertLink, mceReplaceContent, see http://www.tinymce.com/wiki.php/Command_identifiers
    } else if ('ckeditor' === editor) {
        if (null !== window.opener.currentRKParkHausModuleEditor) {
            html = rKParkHausGetPasteSnippet('html', itemId);

            window.opener.currentRKParkHausModuleEditor.insertHtml(html);
        }
    } else {
        alert('Insert into Editor: ' + editor);
    }
    rKParkHausClosePopup();
};

function rKParkHausClosePopup()
{
    window.opener.focus();
    window.close();
}




//=============================================================================
// RKParkHausModule item selector for Forms
//=============================================================================

rKParkHausModule.itemSelector = {};
rKParkHausModule.itemSelector.items = {};
rKParkHausModule.itemSelector.baseId = 0;
rKParkHausModule.itemSelector.selectedId = 0;

rKParkHausModule.itemSelector.onLoad = function (baseId, selectedId)
{
    rKParkHausModule.itemSelector.baseId = baseId;
    rKParkHausModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#rKParkHausModuleObjectType').change(rKParkHausModule.itemSelector.onParamChanged);

    jQuery('#' + baseId + '_catidMain').change(rKParkHausModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + '_catidsMain').change(rKParkHausModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'Id').change(rKParkHausModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(rKParkHausModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(rKParkHausModule.itemSelector.onParamChanged);
    jQuery('#rKParkHausModuleSearchGo').click(rKParkHausModule.itemSelector.onParamChanged);
    jQuery('#rKParkHausModuleSearchGo').keypress(rKParkHausModule.itemSelector.onParamChanged);

    rKParkHausModule.itemSelector.getItemList();
};

rKParkHausModule.itemSelector.onParamChanged = function ()
{
    jQuery('#ajax_indicator').removeClass('hidden');

    rKParkHausModule.itemSelector.getItemList();
};

rKParkHausModule.itemSelector.getItemList = function ()
{
    var baseId;
    var params;

    baseId = parkhaus.itemSelector.baseId;
    params = {
        ot: baseId
        sort: jQuery('#' + baseId + 'Sort').val(),
        sortdir: jQuery('#' + baseId + 'SortDir').val(),
        q: jQuery('#' + baseId + 'SearchTerm').val()
    }
    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        params[catidMain] = jQuery('#' + baseId + '_catidMain').val();
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        params[catidsMain] = jQuery('#' + baseId + '_catidsMain').val();
    }

    jQuery.ajax({
        type: 'POST',
        url: Routing.generate('rkparkhausmodule_ajax_getitemlistfinder'),
        data: params
    }).done(function(res) {
        // get data returned by the ajax response
        var baseId;
        baseId = rKParkHausModule.itemSelector.baseId;
        rKParkHausModule.itemSelector.items[baseId] = res.data;
        jQuery('#ajax_indicator').addClass('hidden');
        rKParkHausModule.itemSelector.updateItemDropdownEntries();
        rKParkHausModule.itemSelector.updatePreview();
    });
};

rKParkHausModule.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = rKParkHausModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = rKParkHausModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.options[i] = new Option(item.title, item.id, false);
    }

    if (rKParkHausModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(rKParkHausModule.itemSelector.selectedId);
    }
};

rKParkHausModule.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = rKParkHausModule.itemSelector.baseId;
    items = rKParkHausModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (rKParkHausModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id === rKParkHausModule.itemSelector.selectedId) {
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

rKParkHausModule.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = rKParkHausModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    preview = window.atob(rKParkHausModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    rKParkHausModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
};
