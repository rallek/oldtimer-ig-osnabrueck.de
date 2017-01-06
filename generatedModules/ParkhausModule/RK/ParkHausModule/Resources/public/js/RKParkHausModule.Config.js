'use strict';

function parkhaToggleShrinkSettings(fieldName) {
    var idSuffix = fieldName.replace('rkparkhausmodule_appsettings_', '');
    jQuery('#shrinkDetails' + idSuffix).toggleClass('hidden', !jQuery('#rkparkhausmodule_appsettings_enableShrinkingFor' + idSuffix).prop('checked'));
}

jQuery(document).ready(function() {
    jQuery('.shrink-enabler').each(function (index) {
        jQuery(this).bind('click keyup', function (event) {
            parkhaToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
        });
        parkhaToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
    });
});
