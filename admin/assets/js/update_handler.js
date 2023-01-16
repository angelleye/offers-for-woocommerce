(function ( $ ) {
    "use strict";
    $(function () {
        $(document).off('click', '#angelleye-updater-notice .notice-dismiss').on('click', '#angelleye-updater-notice .notice-dismiss',function(event) {
            var r = confirm("If you do not install the Updater plugin you will not receive automated updates for Angell EYE products going forward!");
            if (r === true) {
                var data = {
                    action : 'angelleye_updater_dismissible_admin_notice'
                };
                $.post(ajaxurl, data, function (response) {
                    var $el = document.getElementById('angelleye-updater-notice');
                    event.preventDefault();
                    $el.fadeTo( 100, 0, function() {
                        $el.slideUp( 100, function() {
                            $el.remove();
                        });
                    });
                });
            }
        });

    });
}(jQuery));

