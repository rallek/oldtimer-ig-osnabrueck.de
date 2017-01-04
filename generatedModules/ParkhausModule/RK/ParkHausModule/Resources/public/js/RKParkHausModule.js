'use strict';

function rKParkHausCapitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.substring(1);
}

/**
 * Submits a quick navigation form.
 */
function rKParkHausSubmitQuickNavForm(objectType)
{
    jQuery('#rkparkhausmodule' + rKParkHausCapitaliseFirstLetter(objectType) + 'QuickNavForm').submit();
}

/**
 * Initialise the quick navigation panel in list views.
 */
function rKParkHausInitQuickNavigation(objectType)
{
    if (jQuery('#rkparkhausmodule' + rKParkHausCapitaliseFirstLetter(objectType) + 'QuickNavForm').length < 1) {
        return;
    }

    var fieldPrefix = 'rkparkhausmodule_' + objectType.toLowerCase() + 'quicknav_';
    if (jQuery('#' + fieldPrefix + 'catid').length > 0) {
        jQuery('#' + fieldPrefix + 'catid').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#' + fieldPrefix + 'sortBy').length > 0) {
        jQuery('#' + fieldPrefix + 'sortBy').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#' + fieldPrefix + 'sortDir').length > 0) {
        jQuery('#' + fieldPrefix + 'sortDir').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#' + fieldPrefix + 'num').length > 0) {
        jQuery('#' + fieldPrefix + 'num').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
    }

    switch (objectType) {
    case 'vehicle':
        if (jQuery('#' + fieldPrefix + 'workflowState').length > 0) {
            jQuery('#' + fieldPrefix + 'workflowState').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'vehicleType').length > 0) {
            jQuery('#' + fieldPrefix + 'vehicleType').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'owner').length > 0) {
            jQuery('#' + fieldPrefix + 'owner').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'showVehicleOwner').length > 0) {
            jQuery('#' + fieldPrefix + 'showVehicleOwner').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'stillMyOwn').length > 0) {
            jQuery('#' + fieldPrefix + 'stillMyOwn').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
        }
        break;
    case 'vehicleImage':
        if (jQuery('#' + fieldPrefix + 'vehicle').length > 0) {
            jQuery('#' + fieldPrefix + 'vehicle').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'workflowState').length > 0) {
            jQuery('#' + fieldPrefix + 'workflowState').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'vehicleOwner').length > 0) {
            jQuery('#' + fieldPrefix + 'vehicleOwner').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'viewImage').length > 0) {
            jQuery('#' + fieldPrefix + 'viewImage').change(function () { rKParkHausSubmitQuickNavForm(objectType); });
        }
        break;
    default:
        break;
    }
}

/**
 * Helper function to create new Bootstrap modal window instances.
 */
function rKParkHausInitInlineWindow(containerElem, title)
{
    var newWindowId;

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
                    title: title,
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
 * Initialise ajax-based toggle for boolean fields.
 */
function rKParkHausInitToggle(objectType, fieldName, itemId)
{
    var idSuffix = rKParkHausCapitaliseFirstLetter(fieldName) + itemId;
    if (jQuery('#toggle' + idSuffix).length < 1) {
        return;
    }
    jQuery('#toggle' + idSuffix).click( function() {
        rKParkHausToggleFlag(objectType, fieldName, itemId);
    }).removeClass('hidden');
}


/**
 * Toggles a certain flag for a given item.
 */
function rKParkHausToggleFlag(objectType, fieldName, itemId)
{
    var fieldNameCapitalised = rKParkHausCapitaliseFirstLetter(fieldName);
    var params = 'ot=' + objectType + '&field=' + fieldName + '&id=' + itemId;

    jQuery.ajax({
        type: 'POST',
        url: Routing.generate('rkparkhausmodule_ajax_toggleflag'),
        data: params
    }).done(function(res) {
        // get data returned by the ajax response
        var idSuffix, data;

        idSuffix = fieldName + '_' + itemId;
        data = res.data;

        /*if (data.message) {
            rKParkHausSimpleAlert(jQuery('#toggle' + idSuffix), Translator.__('Success'), data.message, 'toggle' + idSuffix + 'DoneAlert', 'success');
        }*/

        idSuffix = idSuffix.toLowerCase();
        var state = data.state;
        if (true === state) {
            jQuery('#no' + idSuffix).addClass('hidden');
            jQuery('#yes' + idSuffix).removeClass('hidden');
        } else {
            jQuery('#yes' + idSuffix).addClass('hidden');
            jQuery('#no' + idSuffix).removeClass('hidden');
        }
    });
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
