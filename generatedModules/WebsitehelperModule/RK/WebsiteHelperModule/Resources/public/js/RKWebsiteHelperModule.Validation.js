'use strict';

function rKWebsiteHelperToday(format)
{
    var timestamp, todayDate, month, day, hours, minutes, seconds;

    timestamp = new Date();
    todayDate = '';
    if (format !== 'time') {
        month = new String((parseInt(timestamp.getMonth()) + 1));
        if (month.length === 1) {
            month = '0' + month;
        }
        day = new String(timestamp.getDate());
        if (day.length === 1) {
            day = '0' + day;
        }
        todayDate += timestamp.getFullYear() + '-' + month + '-' + day;
    }
    if (format === 'datetime') {
        todayDate += ' ';
    }
    if (format != 'date') {
        hours = new String(timestamp.getHours());
        if (hours.length === 1) {
            hours = '0' + hours;
        }
        minutes = new String(timestamp.getMinutes());
        if (minutes.length === 1) {
            minutes = '0' + minutes;
        }
        seconds = new String(timestamp.getSeconds());
        if (seconds.length === 1) {
            seconds = '0' + seconds;
        }
        todayDate += hours + ':' + minutes;// + ':' + seconds;
    }

    return todayDate;
}

// returns YYYY-MM-DD even if date is in DD.MM.YYYY
function rKWebsiteHelperReadDate(val, includeTime)
{
    // look if we have YYYY-MM-DD
    if (val.substr(4, 1) === '-' && val.substr(7, 1) === '-') {
        return val;
    }

    // look if we have DD.MM.YYYY
    if (val.substr(2, 1) === '.' && val.substr(5, 1) === '.') {
        var newVal = val.substr(6, 4) + '-' + val.substr(3, 2) + '-' + val.substr(0, 2);
        if (true === includeTime) {
            newVal += ' ' + val.substr(11, 5);
        }

        return newVal;
    }
}

var lastCarouselItemSingleItemIdentifier = '';

/**
 * Performs a duplicate check for unique fields
 */
function rKWebsiteHelperUniqueCheck(ucOt, val, elem, ucEx)
{
    if (elem.val() == window['last' + rKWebsiteHelperCapitaliseFirstLetter(ucOt) + rKWebsiteHelperCapitaliseFirstLetter(elem.attr('id')) ]) {
        return true;
    }

    window['last' + rKWebsiteHelperCapitaliseFirstLetter(ucOt) + rKWebsiteHelperCapitaliseFirstLetter(elem.attr('id')) ] = elem.val();

    var result = true;

    jQuery.ajax({
        type: 'POST',
        url: Routing.generate('rkwebsitehelpermodule_ajax_checkforduplicate'),
        data: {
            ot: ucOt,
            fn: encodeURIComponent(elem.attr('id')),
            v: encodeURIComponent(val),
            ex: ucEx
        },
        async: false
    }).done(function(res) {
        if (null == res.data || true === res.data.isDuplicate) {
            result = false;
        }
    });

    return result;
}

function rKWebsiteHelperValidateNoSpace(val)
{
    var valStr;
    valStr = new String(val);

    return (valStr.indexOf(' ') === -1);
}

function rKWebsiteHelperValidateHtmlColour(val)
{
    var valStr;
    valStr = new String(val);

    return valStr === '' || (/^#[0-9a-f]{3}([0-9a-f]{3})?$/i.test(valStr));
}

function rKWebsiteHelperValidateUploadExtension(val, elem)
{
    var fileExtension, allowedExtensions;
    if (val === '') {
        return true;
    }

    fileExtension = '.' + val.substr(val.lastIndexOf('.') + 1);
    allowedExtensions = jQuery('#' + elem.attr('id') + 'FileExtensions').text();
    allowedExtensions = '(.' + allowedExtensions.replace(/, /g, '|.').replace(/,/g, '|.') + ')$';
    allowedExtensions = new RegExp(allowedExtensions, 'i');

    return allowedExtensions.test(val);
}

function rKWebsiteHelperValidateDateFuture(val)
{
    var valStr, cmpVal;
    valStr = new String(val);
    cmpVal = rKWebsiteHelperReadDate(valStr, false);

    return valStr === '' || (cmpVal > rKWebsiteHelperToday('date'));
}

function rKWebsiteHelperValidateDateRangeCarouselItem(val)
{
    var cmpVal, cmpVal2, result;
    cmpVal = rKWebsiteHelperReadDate(jQuery("[id$='itemStartDate']").val(), false);
    cmpVal2 = rKWebsiteHelperReadDate(jQuery("[id$='intemEndDate']").val(), false);

    if (typeof cmpVal == 'undefined' && typeof cmpVal2 == 'undefined') {
        result = true;
    } else {
        result = (cmpVal <= cmpVal2);
    }

    return result;
}

/**
 * Runs special validation rules.
 */
function rKWebsiteHelperExecuteCustomValidationConstraints(objectType, currentEntityId)
{
    jQuery('.validate-nospace').each( function() {
        if (!rKWebsiteHelperValidateNoSpace(jQuery(this).val())) {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity(Translator.__('This value must not contain spaces.'));
        } else {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity('');
        }
    });
    jQuery('.validate-htmlcolour').each( function() {
        if (!rKWebsiteHelperValidateHtmlColour(jQuery(this).val())) {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity(Translator.__('Please select a valid html colour code.'));
        } else {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity('');
        }
    });
    jQuery('.validate-upload').each( function() {
        if (!rKWebsiteHelperValidateUploadExtension(jQuery(this).val(), jQuery(this))) {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity(Translator.__('Please select a valid file extension.'));
        } else {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity('');
        }
    });
    jQuery('.validate-date-future').each( function() {
        if (!rKWebsiteHelperValidateDateFuture(jQuery(this).val())) {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity(Translator.__('Please select a value in the future.'));
        } else {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity('');
        }
    });
    jQuery('.validate-daterange-carouselitem').each( function() {
        if (typeof jQuery(this).attr('id') != 'undefined') {
        if (!rKWebsiteHelperValidateDateRangeCarouselItem(jQuery(this).val())) {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity(Translator.__('The start must be before the end.'));
        } else {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity('');
        }
        }
    });
    jQuery('.validate-unique').each( function() {
        if (!rKWebsiteHelperUniqueCheck(jQuery(this).attr('id'), jQuery(this).val(), jQuery(this), currentEntityId)) {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity(Translator.__('This value is already assigned, but must be unique. Please change it.'));
        } else {
            document.getElementById(jQuery(this).attr('id')).setCustomValidity('');
        }
    });
}
