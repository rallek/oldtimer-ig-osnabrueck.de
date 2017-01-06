'use strict';

/**
 * Initialises a user field with auto completion.
 */
function rKParkHausInitUserField(fieldName, getterName)
{
    if (jQuery('#' + fieldName + 'LiveSearch').length < 1) {
        return;
    }
    jQuery('#' + fieldName + 'LiveSearch').removeClass('hidden');

    jQuery('#' + fieldName + 'Selector').typeahead({
        highlight: true,
        hint: true,
        minLength: 2
    }, {
        limit: 15,
        // The data source to query against. Receives the query value in the input field and the process callbacks.
        source: function (query, syncResults, asyncResults) {
            // Retrieve data from server using "query" parameter as it contains the search string entered by the user
            jQuery('#' + fieldName + 'Indicator').removeClass('hidden');
            jQuery.getJSON(Routing.generate('rkparkhausmodule_ajax_' + getterName.toLowerCase(), { fragment: query }), function( data ) {
                jQuery('#' + fieldName + 'Indicator').addClass('hidden');
                asyncResults(data);
            });
        },
        templates: {
            empty: '<div class="empty-message">' + jQuery('#' + fieldName + 'NoResultsHint').text() + '</div>',
            suggestion: function(user) {
                var html;

                html = '<div class="typeahead">';
                html += '<div class="media"><a class="pull-left" href="javascript:void(0)">' + user.avatar + '</a>';
                html += '<div class="media-body">';
                html += '<p class="media-heading">' + user.uname + '</p>';
                html += '</div>';
                html += '</div>';

                return html;
            }
        }
    }).bind('typeahead:select', function(ev, user) {
        // Called after the user selects an item. Here we can do something with the selection.
        jQuery('#' + fieldName).val(user.uid);
        jQuery(this).typeahead('val', user.uname);
    });
}


/**
 * Resets the value of an upload / file input field.
 */
function rKParkHausResetUploadField(fieldName)
{
    jQuery('#' + fieldName).attr('type', 'input');
    jQuery('#' + fieldName).attr('type', 'file');
}

/**
 * Initialises the reset button for a certain upload input.
 */
function rKParkHausInitUploadField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKParkHausResetUploadField(fieldName);
    }).removeClass('hidden');
}

var editedObjectType;
var editedEntityId;
var editForm;
var formButtons;
var triggerValidation = true;

function rKParkHausTriggerFormValidation()
{
    rKParkHausPerformCustomValidationRules(editedObjectType, editedEntityId);

    if (!editForm.get(0).checkValidity()) {
        // This does not really submit the form,
        // but causes the browser to display the error message
        editForm.find(':submit').first().click();
    }
}

function rKParkHausHandleFormSubmit (event) {
    if (triggerValidation) {
        rKParkHausTriggerFormValidation();
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
function rKParkHausInitEditForm(mode, entityId)
{
    if (jQuery('.rkparkhaus-edit-form').length < 1) {
        return;
    }

    editForm = jQuery('.rkparkhaus-edit-form').first();
    editedObjectType = editForm.attr('id').replace('EditForm', '');
    editedEntityId = entityId;

    var allFormFields = editForm.find('input, select, textarea');
    allFormFields.change(rKParkHausExecuteCustomValidationConstraints);

    formButtons = editForm.find('.form-buttons input');
    editForm.find('.btn-danger').first().bind('click keypress', function (event) {
        if (!window.confirm(Translator.__('Do you really want to delete this entry?'))) {
            event.preventDefault();
        }
    });
    editForm.find('button[type=submit]').bind('click keypress', function (event) {
        triggerValidation = !jQuery(this).attr('formnovalidate');
    });
    editForm.submit(rKParkHausHandleFormSubmit);
    if (mode != 'create') {
        rKParkHausTriggerFormValidation();
    }
}

