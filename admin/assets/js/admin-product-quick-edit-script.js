

jQuery(document).ready(function($){
        //Prepopulating our quick-edit post info
    var $inline_editor = inlineEditPost.edit;
    inlineEditPost.edit = function(id){

        //call old copy 
        $inline_editor.apply( this, arguments);

        //our custom functionality below
        var post_id = 0;
        if( typeof(id) == 'object'){
            post_id = parseInt(this.getId(id));
        }

        //if we have our post
        if(post_id != 0){

            //find our row
            $row = $('#edit-' + post_id);
            var $ofw_inline_data = $( '#offers_for_woocommerce_inline_' + post_id );
            var product_type = $ofw_inline_data.find('.wc_product_type_for_manage_offer').text();            
            if(product_type === 'external'){                
                $('.manage_offer_inline_edit_based_on_product_type','.inline-edit-row' ).addClass('hidden');
            }
            else{
                $('.manage_offer_inline_edit_based_on_product_type','.inline-edit-row' ).removeClass('hidden');
                var ofw_enabled = $ofw_inline_data.find( '.offers_for_woocommerce_enabled' ).text();
                var ofw_onexit = $ofw_inline_data.find( '.offers_for_woocommerce_onexit_only' ).text();
                var ofw_aae = $ofw_inline_data.find( '.offers_for_woocommerce_auto_accept_enabled' ).text();
                var ofw_ade = $ofw_inline_data.find( '.offers_for_woocommerce_auto_decline_enabled' ).text();
                var ofw_aap = $ofw_inline_data.find( '.offers_for_woocommerce_auto_accept_percentage' ).text();
                var ofw_adp = $ofw_inline_data.find( '.offers_for_woocommerce_auto_decline_percentage' ).text();


                if ( 'yes' === ofw_enabled ) {
                        $( 'input[name="offers_for_woocommerce_enabled"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
                        $( 'input[name="offers_for_woocommerce_enabled"]', '.inline-edit-row' ).val('yes');
                } else {
                        $( 'input[name="offers_for_woocommerce_enabled"]', '.inline-edit-row' ).removeAttr( 'checked' );
                        $( 'input[name="offers_for_woocommerce_enabled"]', '.inline-edit-row' ).val('no');
                }

                if ( 'yes' === ofw_onexit ) {
                        $( 'input[name="offers_for_woocommerce_onexit_only"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
                        $( 'input[name="offers_for_woocommerce_onexit_only"]', '.inline-edit-row' ).val('yes');
                } else {
                        $( 'input[name="offers_for_woocommerce_onexit_only"]', '.inline-edit-row' ).removeAttr( 'checked' );
                        $( 'input[name="offers_for_woocommerce_onexit_only"]', '.inline-edit-row' ).val('no');
                }

                if ( 'yes' === ofw_aae ) {
                        $( 'input[name="_offers_for_woocommerce_auto_accept_enabled"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
                        $( 'input[name="_offers_for_woocommerce_auto_accept_enabled"]', '.inline-edit-row' ).val('yes');
                } else {
                        $( 'input[name="_offers_for_woocommerce_auto_accept_enabled"]', '.inline-edit-row' ).removeAttr( 'checked' );
                        $( 'input[name="_offers_for_woocommerce_auto_accept_enabled"]', '.inline-edit-row' ).val('no');
                }

                if ( 'yes' === ofw_ade ) {
                        $( 'input[name="_offers_for_woocommerce_auto_decline_enabled"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
                        $( 'input[name="_offers_for_woocommerce_auto_decline_enabled"]', '.inline-edit-row' ).val('yes');
                } else {
                        $( 'input[name="_offers_for_woocommerce_auto_decline_enabled"]', '.inline-edit-row' ).removeAttr( 'checked' );
                        $( 'input[name="_offers_for_woocommerce_auto_decline_enabled"]', '.inline-edit-row' ).val('no');
                }

                $( 'input[name="_offers_for_woocommerce_auto_accept_percentage"]', '.inline-edit-row' ).val( ofw_aap );
                $( 'input[name="_offers_for_woocommerce_auto_decline_percentage"]', '.inline-edit-row' ).val( ofw_adp );

            }
                                      
        }

    }
        
    $( '#the-list' ).on( 'change', '.inline-edit-row input[name="offers_for_woocommerce_enabled"]', function() {
        if ( $( this ).is( ':checked' ) ) {
                $( this ).attr( 'checked', 'checked' );
                $( this ).val('yes');
        } else {
                $( this ).removeAttr( 'checked');
                $( this ).val('no');
        }
    });
    
    $( '#the-list' ).on( 'change', '.inline-edit-row input[name="offers_for_woocommerce_onexit_only"]', function() {
        if ( $( this ).is( ':checked' ) ) {
                $( this ).attr( 'checked', 'checked' );
                $( this ).val('yes');
        } else {
                $( this ).removeAttr( 'checked');
                $( this ).val('no');
        }
    });
    
    $( '#the-list' ).on( 'change', '.inline-edit-row input[name="_offers_for_woocommerce_auto_accept_enabled"]', function() {
        if ( $( this ).is( ':checked' ) ) {
                $( this ).attr( 'checked', 'checked' );
                $( this ).val('yes');
        } else {
                $( this ).removeAttr( 'checked');
                $( this ).val('no');
        }
    });
    
    $( '#the-list' ).on( 'change', '.inline-edit-row input[name="_offers_for_woocommerce_auto_decline_enabled"]', function() {
        if ( $( this ).is( ':checked' ) ) {
                $( this ).attr( 'checked', 'checked' );
                $( this ).val('yes');
        } else {
                $( this ).removeAttr( 'checked');
                $( this ).val('no');
        }
    });
    
});