'use strict';


/**
 * Resets the value of an upload / file input field.
 */
function rKDownLoadResetUploadField(fieldName)
{
    jQuery('#' + fieldName).attr('type', 'input');
    jQuery('#' + fieldName).attr('type', 'file');
}

/**
 * Initialises the reset button for a certain upload input.
 */
function rKDownLoadInitUploadField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKDownLoadResetUploadField(fieldName);
    }).removeClass('hidden');
}

/**
 * Resets the value of a date or datetime input field.
 */
function rKDownLoadResetDateField(fieldName)
{
    jQuery('#' + fieldName).val('');
    jQuery('#' + fieldName + 'cal').html(Zikula.__('No date set.', 'rkdownloadmodule_js'));
}

/**
 * Initialises the reset button for a certain date input.
 */
function rKDownLoadInitDateField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKDownLoadResetDateField(fieldName);
    }).removeClass('hidden');
}

