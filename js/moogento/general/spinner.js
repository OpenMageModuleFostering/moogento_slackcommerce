function showSpinner(el) {
//    jQuery(el).addClass('with-spinner').append('<div class="loader-switchbox"><div class="loader-switch"></div><div class="loader-switch loader-switch2"></div></div>');
    jQuery(el).addClass('with-spinner').append('<div class="moogento-spiner"></div>');
}
function hideSpinner(el) {
//    jQuery(el).removeClass('with-spinner').find('.loader-switchbox').remove();
    jQuery(el).removeClass('with-spinner').find('.moogento-spiner').remove();
}