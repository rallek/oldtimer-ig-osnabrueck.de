'use strict';

function rKWebsiteHelperCapitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.substring(1);
}

/**
 * Submits a quick navigation form.
 */
function rKWebsiteHelperSubmitQuickNavForm(objectType)
{
    jQuery('#rkwebsitehelpermodule' + rKWebsiteHelperCapitaliseFirstLetter(objectType) + 'QuickNavForm').submit();
}

/**
 * Initialise the quick navigation panel in list views.
 */
function rKWebsiteHelperInitQuickNavigation(objectType)
{
    if (jQuery('#rkwebsitehelpermodule' + rKWebsiteHelperCapitaliseFirstLetter(objectType) + 'QuickNavForm').length < 1) {
        return;
    }

    var fieldPrefix = 'rkwebsitehelpermodule_' + objectType.toLowerCase() + 'quicknav_';
    if (jQuery('#' + fieldPrefix + 'catid').length > 0) {
        jQuery('#' + fieldPrefix + 'catid').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#' + fieldPrefix + 'sortBy').length > 0) {
        jQuery('#' + fieldPrefix + 'sortBy').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#' + fieldPrefix + 'sortDir').length > 0) {
        jQuery('#' + fieldPrefix + 'sortDir').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
    }
    if (jQuery('#' + fieldPrefix + 'num').length > 0) {
        jQuery('#' + fieldPrefix + 'num').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
    }

    switch (objectType) {
    case 'linker':
        if (jQuery('#' + fieldPrefix + 'workflowState').length > 0) {
            jQuery('#' + fieldPrefix + 'workflowState').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'linkerLanguage').length > 0) {
            jQuery('#' + fieldPrefix + 'linkerLanguage').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
        }
        break;
    case 'carouselItem':
        if (jQuery('#' + fieldPrefix + 'carousel').length > 0) {
            jQuery('#' + fieldPrefix + 'carousel').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'workflowState').length > 0) {
            jQuery('#' + fieldPrefix + 'workflowState').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'linkExternal').length > 0) {
            jQuery('#' + fieldPrefix + 'linkExternal').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
        }
        break;
    case 'carousel':
        if (jQuery('#' + fieldPrefix + 'workflowState').length > 0) {
            jQuery('#' + fieldPrefix + 'workflowState').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
        }
        if (jQuery('#' + fieldPrefix + 'controls').length > 0) {
            jQuery('#' + fieldPrefix + 'controls').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
        }
        break;
    case 'websiteImage':
        if (jQuery('#' + fieldPrefix + 'workflowState').length > 0) {
            jQuery('#' + fieldPrefix + 'workflowState').change(function () { rKWebsiteHelperSubmitQuickNavForm(objectType); });
        }
        break;
    default:
        break;
    }
}

/**
 * Helper function to create new Bootstrap modal window instances.
 */
function rKWebsiteHelperInitInlineWindow(containerElem, title)
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
 * Simulates a simple alert using bootstrap.
 */
function rKWebsiteHelperSimpleAlert(beforeElem, title, content, alertId, cssClass)
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
