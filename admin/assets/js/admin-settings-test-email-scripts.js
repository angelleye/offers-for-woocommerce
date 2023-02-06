jQuery(document).ready(function($){

    $('#ofw_preview_email').click(function (){

        let email_body = '';
        $('#mail_response_msg').html("");
        if (
            jQuery( '#wp-ofw_email_body-wrap' ).hasClass( 'tmce-active' )
        ) {
            email_body = tinyMCE.get( 'ofw_email_body' ).getContent();
        } else {
            email_body = jQuery( '#ofw_email_body' ).val();
        }

        const email_subject = $( '#ofw_email_subject' ).val();
        const email_send_to = $( '#ofw_send_test_email' ).val();
        const wp_nonce = $( '#email_reminder_nonce' ).val();

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
                $('#mail_response_msg').html( messagehtml(data.message, true) );
            }
        });


    });

    $('#ofw_template_name').keyup(function (){
        var textval = $('#ofw_template_name').val();
        if( textval !== undefined && textval !== "" ){
            $('#ofw_template_name').val(textval.replace(/[^a-zA-Z0-9 ]/g, ""));
        }
    });
    $('#ofw_email_frequency_unit').change(function (){
        switch ($('#ofw_email_frequency_unit').val()){
            case 'day':
                $('#ofw_email_frequency').attr("max" , 364);
                $('#ofw_email_frequency').attr("min" , 1);
                break;
            case 'minute':
                $('#ofw_email_frequency').attr("max",59);
                $('#ofw_email_frequency').attr("min",1);
                break;
            case 'hour':
                $('#ofw_email_frequency').attr("max",23);
                $('#ofw_email_frequency').attr("min",1);
                break;
        }
    })
});

function messagehtml( message, success = true ){
    if( success ){
        var message = '<div id="message" class="updated notice is-dismissible"><p>'+ message +'</p></div>';
    }
    return message;
}