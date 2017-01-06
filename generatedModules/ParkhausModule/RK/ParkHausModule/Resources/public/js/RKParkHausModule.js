'use strict';

function rKParkHausCapitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.substring(1);
}

/**
 * Initialise the quick navigation panel in list views.
 */
function rKParkHausInitQuickNavigation()
{
    var quickNavForm;
    var objectType;

    if (jQuery('.rkparkhausmodule-quicknav').length < 1) {
        return;
    }

    quickNavForm = jQuery('.rkparkhausmodule-quicknav').first();
    objectType = quickNavForm.attr('id').replace('rKParkHausModule', '').replace('QuickNavForm', '');

    quickNavForm.find('select').change(function (event) {
        quickNavForm.submit();
    });

    var fieldPrefix = 'rkparkhausmodule_' + objectType.toLowerCase() + 'quicknav_';
    // we can hide the submit button if we have no visible quick search field
    if (jQuery('#' + fieldPrefix + 'q').length < 1 || jQuery('#' + fieldPrefix + 'q').parent().parent().hasClass('hidden')) {
        jQuery('#' + fieldPrefix + 'updateview').addClass('hidden');
    }
}

/**
 * Toggles a certain flag for a given item.
 */
function rKParkHausToggleFlag(objectType, fieldName, itemId)
{
    jQuery.ajax({
        type: 'POST',
        url: Routing.generate('rkparkhausmodule_ajax_toggleflag'),
        data: {
            ot: objectType,
            field: fieldName,
            id: itemId
        }
    }).done(function(res) {
        // get data returned by the ajax response
        var fieldNameCapitalised, idSuffix, data;

        fieldNameCapitalised = rKParkHausCapitaliseFirstLetter(fieldName);
        idSuffix = fieldNameCapitalised + itemId;
        data = res.data;

        /*if (data.message) {
            rKParkHausSimpleAlert(jQuery('#toggle' + idSuffix), Translator.__('Success'), data.message, 'toggle' + idSuffix + 'DoneAlert', 'success');
        }*/

        idSuffix = idSuffix.toLowerCase();
        if (true === data.state) {
            jQuery('#no' + idSuffix).addClass('hidden');
            jQuery('#yes' + idSuffix).removeClass('hidden');
        } else {
            jQuery('#yes' + idSuffix).addClass('hidden');
            jQuery('#no' + idSuffix).removeClass('hidden');
        }
    });
}

/**
 * Initialise ajax-based toggle for all affected boolean fields on the current page.
 */
function rKParkHausInitAjaxToggles()
{
    jQuery('.rkparkhaus-ajax-toggle').click(function (event) {
        var objectType;
        var fieldName;
        var itemId;

        event.preventDefault();
        objectType = jQuery(this).data('object-type');
        fieldName = jQuery(this).data('field-name');
        itemId = jQuery(this).data('item-id');

        rKParkHausToggleFlag(objectType, fieldName, itemId);
    }).removeClass('hidden');
}

/**
 * Simulates a simple alert using bootstrap.
 */
function rKParkHausSimpleAlert(beforeElem, title, content, alertId, cssClass)
{
    var alertBox;

    alertBox = ' \
        <div id="' + alertId + '" class="alert alert-' + cssClass + ' fade"> \
          <button type="button" class="close" data-dismiss="alert">&times;</button> \
          <h4>' + title + '</h4> \
          <p>' + content + '</p> \
        </div>';

    // insert alert before the given element
    beforeElem.before(alertBox);

    jQuery('#' + alertId).delay(200).addClass('in').fadeOut(4000, function () {
        jQuery(this).remove();
    });
}

/**
 * Initialises the mass toggle functionality for admin view pages.
 */
function rKParkHausInitMassToggle()
{
    if (jQuery('.rkparkhaus-mass-toggle').length > 0) {
        jQuery('.rkparkhaus-mass-toggle').click(function (event) {
            jQuery('.rkparkhaus-toggle-checkbox').prop('checked', jQuery(this).prop('checked'));
        });
    }
}

/**
 * Creates a dropdown menu for the item actions.
 */
function rKParkHausInitItemActions(context)
{
    var containerSelector;
    var containers;
    var listClasses;

    containerSelector = '';
    if (context == 'view') {
        containerSelector = '.rkparkhausmodule-view';
        listClasses = 'list-unstyled dropdown-menu dropdown-menu-right';
    } else if (context == 'display') {
        containerSelector = 'h2, h3';
        listClasses = 'list-unstyled dropdown-menu';
    }

    if (containerSelector == '') {
        return;
    }

    containers = jQuery(containerSelector);
    if (containers.length < 1) {
        return;
    }

    containers.find('.dropdown > ul').removeClass('list-inline').addClass(listClasses);
    containers.find('.dropdown > ul a').each(function (index) {
        jQuery(this).html(jQuery(this).html() + jQuery(this).find('i').first().data('original-title'));
    });
    containers.find('.dropdown > ul a i').addClass('fa-fw');
    containers.find('.dropdown-toggle').removeClass('hidden').dropdown();

/**
 * Helper function to create new Bootstrap modal window instances.
 */
function rKParkHausInitInlineWindow(containerElem)
{
    var newWindowId;
    var modalTitle;

    // show the container (hidden for users without JavaScript)
    containerElem.removeClass('hidden');

    // define name of window
    newWindowId = containerElem.attr('id') + 'Dialog';

    containerElem.unbind('click').click(function(e) {
        e.preventDefault();

        // check if window exists already
        if (jQuery('#' + newWindowId).length < 1) {
            // create new window instance
            jQuery('<div id="' + newWindowId + '"></div>')
                .append(
                    jQuery('<iframe width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" />')
                        .attr('src', containerElem.attr('href'))
                )
                .dialog({
                    autoOpen: false,
                    show: {
                        effect: 'blind',
                        duration: 1000
                    },
                    hide: {
                        effect: 'explode',
                        duration: 1000
                    },
                    title: containerElem.data('modal-title'),
                    width: 600,
                    height: 400,
                    modal: false
                });
        }

        // open the window
        jQuery('#' + newWindowId).dialog('open');
    });

    // return the dialog selector id;
    return newWindowId;
}

/**
 * Initialises modals for inline display of related items.
 */
function rKParkHausInitQuickViewModals()
{
    jQuery('.rkparkhaus-inline-window').each(function (index) {
        rKParkHausInitInlineWindow(jQuery(this));
    });
}

jQuery(document).ready(function() {
    var isViewPage;
    var isDisplayPage;

    isViewPage = jQuery(#.rkparkhausmodule-view').length > 0;
    isDisplayPage = jQuery(#.rkparkhausmodule-display').length > 0;

    jQuery('a.lightbox').lightbox();

    if (isViewPage) {
        rKParkHausInitQuickNavigation();
        rKParkHausInitMassToggle();
        rKParkHausInitItemActions('view');
        rKParkHausInitAjaxToggles();
    } else if (isDisplayPage) {
        rKParkHausInitItemActions('display');
        rKParkHausInitAjaxToggles();
    }

    rKParkHausInitQuickViewModals();
});
