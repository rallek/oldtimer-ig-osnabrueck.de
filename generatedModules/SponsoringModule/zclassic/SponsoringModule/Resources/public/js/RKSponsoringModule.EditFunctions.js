'use strict';

/**
 * Initialises a user field with auto completion.
 */
function rKSponsoringInitUserField(fieldName, getterName)
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
            jQuery.getJSON(Routing.generate('rksponsoringmodule_ajax_' + getterName.toLowerCase(), { fragment: query }), function( data ) {
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
function rKSponsoringResetUploadField(fieldName)
{
    jQuery('#' + fieldName).attr('type', 'input');
    jQuery('#' + fieldName).attr('type', 'file');
}

/**
 * Initialises the reset button for a certain upload input.
 */
function rKSponsoringInitUploadField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKSponsoringResetUploadField(fieldName);
    }).removeClass('hidden');
}

/**
 * Resets the value of a date or datetime input field.
 */
function rKSponsoringResetDateField(fieldName)
{
    jQuery('#' + fieldName).val('');
    jQuery('#' + fieldName + 'cal').html(Zikula.__('No date set.', 'rksponsoringmodule_js'));
}

/**
 * Initialises the reset button for a certain date input.
 */
function rKSponsoringInitDateField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click( function (event) {
        event.stopPropagation();
        rKSponsoringResetDateField(fieldName);
    }).removeClass('hidden');
}

