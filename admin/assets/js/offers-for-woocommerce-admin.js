jQuery(function ($) {
    document.addEventListener("DOMContentLoaded", function(){

        jQuery('[id^=angelleye_notification]').each(function (i) {
            jQuery('[id="' + this.id + '"]').slice(1).remove();
        });

        var el_notice = document.getElementsByClassName('angelleye-notice');
        el_notice.fadeIn(750);

        $(document).on('click', '.angelleye-notice-dismiss', function (e){
            e.preventDefault();
            jQuery(this).parent().parent(".angelleye-notice").fadeOut(600, function () {
                jQuery(this).parent().parent(".angelleye-notice").remove();
            });
            notify_wordpress(jQuery(this).data("msg"));
        });

    });

    function notify_wordpress(message) {
        var param = {
            action: 'angelleye_offers_for_woocommerce_adismiss_notice',
            data: message
        };
        jQuery.post(ajaxurl, param);
    }
});


