'use strict';


/**
 * Resets the value of an upload / file input field.
 */
function rKWebsitehelperResetUploadField(fieldName)
{
    jQuery('#' + fieldName).attr('type', 'input');
    jQuery('#' + fieldName).attr('type', 'file');
}

/**
 * Initialises the reset button for a certain upload input.
 */
function rKWebsitehelperInitUploadField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKWebsitehelperResetUploadField(fieldName);
    }).removeClass('hidden');
}

/**
 * Resets the value of a date or datetime input field.
 */
function rKWebsitehelperResetDateField(fieldName)
{
    jQuery('#' + fieldName).val('');
    jQuery('#' + fieldName + 'cal').html(Zikula.__('No date set.', 'rkwebsitehelpermodule_js'));
}

/**
 * Initialises the reset button for a certain date input.
 */
function rKWebsitehelperInitDateField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKWebsitehelperResetDateField(fieldName);
    }).removeClass('hidden');
}

