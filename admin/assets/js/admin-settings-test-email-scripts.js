jQuery( document ).ready(function($) {
    if(document.getElementById('ofw_preview_email') !== null){
        document.getElementById('ofw_preview_email').addEventListener('click' , function(){
            let email_body = '';
            document.getElementById('mail_response_msg').innerHTML = '';
            if (document.getElementById('wp-ofw_email_body-wrap').classList.contains("tmce-active")) {
                email_body = tinyMCE.get('ofw_email_body').getContent();
            } else {
                email_body = document.getElementById('ofw_email_body').value;
            }

            const email_subject = document.getElementById('ofw_email_subject').value;
            const email_send_to = document.getElementById('ofw_send_test_email').value;
            const wp_nonce = document.getElementById('email_reminder_nonce').value;

            const data = {
                email_subject,
                email_body,
                email_send_to,
                action: 'ofw_er_preview_email_send',
                security: wp_nonce,
            };
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
            }).done(function (data) {
                if (data.status) {
                    document.getElementById('mail_response_msg').innerHTML = messagehtml(data.message, true);
                }
            });
        });
    }

    jQuery(document).on('keyup','#ofw_template_name',function (){
        var textval = document.getElementById('ofw_template_name').value;
        if (textval !== undefined && textval !== "") {
            document.getElementById('ofw_template_name').value = textval.replace(/[^a-zA-Z0-9 ]/g, "");
        }
    });

    let ofw_email_frequency_unit = document.getElementById('ofw_email_frequency_unit');
    if( ofw_email_frequency_unit !== null ) {
        for (var i = 0; i < ofw_email_frequency_unit.length; i++) {
            ofw_email_frequency_unit[i].addEventListener('change', function () {
                var ofw_email_frequency = document.getElementById('ofw_email_frequency');
                switch (document.getElementById('ofw_email_frequency_unit').value) {
                    case 'day':
                        ofw_email_frequency.setAttribute('max', 364);
                        ofw_email_frequency.setAttribute('min', 1);
                        break;
                    case 'minute':
                        ofw_email_frequency.setAttribute('max', 59);
                        ofw_email_frequency.setAttribute('min', 1);
                        break;
                    case 'hour':
                        ofw_email_frequency.setAttribute('max', 23);
                        ofw_email_frequency.setAttribute('min', 1);
                        break;
                }
            })
        }
    }
});

function messagehtml( message, success = true ){
    if( success ){
        var message = '<div id="message" class="updated notice is-dismissible"><p>'+ message +'</p></div>';
    }
    return message;
}

