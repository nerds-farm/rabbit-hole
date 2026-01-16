jQuery(window).load(function () {

    jQuery('#rabbit_hole .nav-tab').on('click', function () {
        jQuery('#rabbit_hole .nav-tab.nav-tab-active').removeClass('nav-tab-active');
        jQuery(this).addClass('nav-tab-active');
        jQuery('.rh-settings').hide();
        jQuery(jQuery(this).attr('href')).show();
        return false;
    });

    jQuery('.rabbit_hole_behavior').on('change', function () {
        if (jQuery(this).prop('checked')) {
            let rhsettings = jQuery(this).closest('.postbox, form').find('.accordion-section-content--redirect');
            if (jQuery(this).val() == '30x') {
                rhsettings.show();
            } else {
                rhsettings.hide();
            }
        }
    });
    jQuery('.rabbit_hole_behavior:checked').trigger('change');

    jQuery('.rh-disable-bypassing').on('change', function () {
        let rhsettings = jQuery(this).closest('.postbox').find('.accordion-section-content--roles');
        if (jQuery(this).prop('checked')) {
            rhsettings.show().css('display', 'block');
        } else {
            rhsettings.hide();
        }
    });
    jQuery('.rh-disable-bypassing:checked').trigger('change');

    jQuery('.rh-disable-message').on('change', function () {
        let rhsettings = jQuery(this).closest('.postbox').find('.accordion-section-content--message');
        if (jQuery(this).prop('checked')) {
            rhsettings.show().css('display', 'block');
        } else {
            rhsettings.hide();
        }
    });
    jQuery('.rh-disable-message:checked').trigger('change');
});