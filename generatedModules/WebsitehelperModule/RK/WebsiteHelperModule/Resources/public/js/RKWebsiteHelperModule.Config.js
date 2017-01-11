'use strict';

function websitToggleShrinkSettings(fieldName) {
    var idSuffix = fieldName.replace('rkwebsitehelpermodule_appsettings_', '');
    jQuery('#shrinkDetails' + idSuffix).toggleClass('hidden', !jQuery('#rkwebsitehelpermodule_appsettings_enableShrinkingFor' + idSuffix).prop('checked'));
}

jQuery(document).ready(function() {
    jQuery('.shrink-enabler').each(function (index) {
        jQuery(this).bind('click keyup', function (event) {
            websitToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
        });
        websitToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
    });
});
