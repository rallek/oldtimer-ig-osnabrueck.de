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
    jQuery('#' + fieldName + 'cal').html(Translator.__('No date set.'));
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

var editedObjectType;
var editedEntityId;
var editForm;
var formButtons;
var triggerValidation = true;

function rKDownLoadTriggerFormValidation()
{
    rKDownLoadExecuteCustomValidationConstraints(editedObjectType, editedEntityId);

    if (!editForm.get(0).checkValidity()) {
        // This does not really submit the form,
        // but causes the browser to display the error message
        editForm.find(':submit').first().click();
    }
}

function rKDownLoadHandleFormSubmit (event) {
    if (triggerValidation) {
        rKDownLoadTriggerFormValidation();
        if (!editForm.get(0).checkValidity()) {
            event.preventDefault();
            return false;
        }
    }

    // hide form buttons to prevent double submits by accident
    formButtons.each(function (index) {
        jQuery(this).addClass('hidden');
    });

    return true;
}

/**
 * Initialises an entity edit form.
 */
function rKDownLoadInitEditForm(mode, entityId)
{
    if (jQuery('.rkdownload-edit-form').length < 1) {
        return;
    }

    editForm = jQuery('.rkdownload-edit-form').first();
    editedObjectType = editForm.attr('id').replace('EditForm', '');
    editedEntityId = entityId;

    var allFormFields = editForm.find('input, select, textarea');
    allFormFields.change(function (event) {
        rKDownLoadExecuteCustomValidationConstraints(editedObjectType, editedEntityId);
    });

    formButtons = editForm.find('.form-buttons input');
    editForm.find('.btn-danger').first().bind('click keypress', function (event) {
        if (!window.confirm(Translator.__('Do you really want to delete this entry?'))) {
            event.preventDefault();
        }
    });
    editForm.find('button[type=submit]').bind('click keypress', function (event) {
        triggerValidation = !jQuery(this).attr('formnovalidate');
    });
    editForm.submit(rKDownLoadHandleFormSubmit);
    if (mode != 'create') {
        rKDownLoadTriggerFormValidation();
    }
}

