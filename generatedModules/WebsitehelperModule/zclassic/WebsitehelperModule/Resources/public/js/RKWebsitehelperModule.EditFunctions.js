'use strict';


/**
 * Resets the value of an upload / file input field.
 */
function rKWebsiteHelperResetUploadField(fieldName)
{
    jQuery('#' + fieldName).attr('type', 'input');
    jQuery('#' + fieldName).attr('type', 'file');
}

/**
 * Initialises the reset button for a certain upload input.
 */
function rKWebsiteHelperInitUploadField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKWebsiteHelperResetUploadField(fieldName);
    }).removeClass('hidden');
}

/**
 * Resets the value of a date or datetime input field.
 */
function rKWebsiteHelperResetDateField(fieldName)
{
    jQuery('#' + fieldName).val('');
    jQuery('#' + fieldName + 'cal').html(Zikula.__('No date set.', 'rkwebsitehelpermodule_js'));
}

/**
 * Initialises the reset button for a certain date input.
 */
function rKWebsiteHelperInitDateField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKWebsiteHelperResetDateField(fieldName);
    }).removeClass('hidden');
}

