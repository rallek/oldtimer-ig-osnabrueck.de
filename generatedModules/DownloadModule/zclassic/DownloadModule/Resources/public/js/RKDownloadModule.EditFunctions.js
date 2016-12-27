'use strict';


/**
 * Resets the value of an upload / file input field.
 */
function rKDownloadResetUploadField(fieldName)
{
    jQuery('#' + fieldName).attr('type', 'input');
    jQuery('#' + fieldName).attr('type', 'file');
}

/**
 * Initialises the reset button for a certain upload input.
 */
function rKDownloadInitUploadField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKDownloadResetUploadField(fieldName);
    }).removeClass('hidden');
}

/**
 * Resets the value of a date or datetime input field.
 */
function rKDownloadResetDateField(fieldName)
{
    jQuery('#' + fieldName).val('');
    jQuery('#' + fieldName + 'cal').html(Zikula.__('No date set.', 'rkdownloadmodule_js'));
}

/**
 * Initialises the reset button for a certain date input.
 */
function rKDownloadInitDateField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKDownloadResetDateField(fieldName);
    }).removeClass('hidden');
}

