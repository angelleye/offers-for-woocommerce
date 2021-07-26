jQuery(function ($) {

    $(document).ready(function() {

        jQuery('[id^=angelleye_notification]').each(function (i) {
            jQuery('[id="' + this.id + '"]').slice(1).remove();
        });

        var el_notice = jQuery(".angelleye-notice");
        el_notice.fadeIn(750);

        jQuery(".angelleye-notice-dismiss").click(function (e) {
            e.preventDefault();
            jQuery(this).parent().parent(".angelleye-notice").fadeOut(600, function () {
                jQuery(this).parent().parent(".angelleye-notice").remove();
            });
            notify_wordpress(jQuery(this).data("msg"));
        });

        jQuery("#enable_binding_offer_authorization").change(function () {
            if (jQuery(this).is(':checked')) {
                jQuery('#enable_make_offer_payment_authorization').closest('tr').show();
            } else{
                jQuery('#enable_make_offer_payment_authorization').closest('tr').hide();
            }
        }).change();
    });

    function notify_wordpress(message) {
        var param = {
            action: 'angelleye_offers_for_woocommerce_adismiss_notice',
            data: message
        };
        jQuery.post(ajaxurl, param);
    }
});


