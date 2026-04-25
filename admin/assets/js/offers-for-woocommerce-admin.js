jQuery(function ($) {
    document.addEventListener("DOMContentLoaded", function () {
        jQuery('[id^=angelleye_notification]').each(function () {
            jQuery('[id="' + this.id + '"]').slice(1).remove();
        });

        jQuery('.angelleye-notice').fadeIn(750);
    });
});


