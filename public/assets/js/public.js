(function ( $ ) {
	"use strict";
	$(function () {
        // Public-facing JavaScript
        $(document).ready(function(){
            $('.ofwc_no_price_product').remove();
            function manage_button_for_variation(){
                var variationId = $("input[name='variation_id']").val();
                if(variationId > 0){
                    $(".offers-for-woocommerce-make-offer-button-single-product").removeClass('disabled');
                } else {
                    $(".offers-for-woocommerce-make-offer-button-single-product").addClass('disabled');
                }
            }
            
            if (offers_for_woocommerce_js_params.is_product_type_variable === 'true') {
                manage_button_for_variation();
                $( ".variations_form" ).on( "woocommerce_variation_has_changed", function () {
                    manage_button_for_variation();
                    var variationId = $("input[name='variation_id']").val();
                    $('#tab_custom_ofwc_offer_tab_alt_message_2').hide();
                    $('#tab_custom_ofwc_offer_tab_alt_message_success').hide();
                    $('#tab_custom_ofwc_offer_tab_inner fieldset').show();
                    if(variationId > 0){
                        $('#tab_custom_ofwc_offer_tab_inner').show();
                        $('#tab_custom_ofwc_offer_tab_alt_message').hide();
                    } else {
                        $('#tab_custom_ofwc_offer_tab_inner').hide();
                        $('#tab_custom_ofwc_offer_tab_alt_message').show();
                    }
                } );
            }
                
            var get = [];
            location.search.replace('?', '').split('&').forEach(function (val) {
                var split = val.split("=", 2);
                get[split[0]] = split[1];
            });

            if(get["aewcobtn"] && !( $("div.woocommerce-message").length > 0 ) ){
                if( !$(".woocommerce-error:first").hasClass('aeofwc-woocommerce-error') )
                {
                    // do nothing
                }
                else
                {
                    if (offers_for_woocommerce_js_params.is_product_type_variable === 'true') {
                        var variationId = $("input[name='variation_id']").val();
                        if(variationId > 0){
                            angelleyeOpenMakeOfferForm();
                        } else {
                            alert(offers_for_woocommerce_js_params.i18n_make_a_selection_text);
                        }
                    } else {
                        var productId = $("input[name='add-to-cart']").val();
                        if(productId > 0){
                            angelleyeOpenMakeOfferForm();
                        } else {
                            alert(offers_for_woocommerce_js_params.i18n_unavailable_text);
                        }
                    }
                }
            }

            var makeOfferBtnPosition = $('#offers-for-woocommerce-add-to-cart-wrap').attr('data-ofwc-position');
            var makeOfferBtnhtml = $('.single_variation_wrap_angelleye.ofwc_offer_tab_form_wrap').html();

            // after price
            if(makeOfferBtnhtml != null && makeOfferBtnhtml.length > 0){
                if(makeOfferBtnPosition == 'after_price') {
                    $('.product .summary .price:first').after("<div class='offers-for-woocommerce-add-to-cart-wrap ofwc-no-float'>"+ makeOfferBtnhtml +"");
                }
            }

            $(".offers-for-woocommerce-make-offer-button-single-product").click(function(){
                if (offers_for_woocommerce_js_params.is_product_type_variable === 'true') {
                    var variationId = $("input[name='variation_id']").val();
                    if(variationId > 0){
                        angelleyeOpenMakeOfferForm();
                    } else {
                        alert(offers_for_woocommerce_js_params.i18n_make_a_selection_text);
                    }
                } else {
                    //var productId = $("input[name='add-to-cart']").val();                    
                    if($("input[name='add-to-cart']").val() > 0 || $("button[name='add-to-cart']").val() > 0){
                        angelleyeOpenMakeOfferForm();
                    } else {
                        alert(offers_for_woocommerce_js_params.i18n_unavailable_text);
                    }
                }
            });

            $("#lightbox_custom_ofwc_offer_form_close_btn, #aeofwc-close-lightbox-link").on('click', function()
            {
                $("#lightbox_custom_ofwc_offer_form").removeClass('active');
                $("#lightbox_custom_ofwc_offer_form").hide();
                $("#lightbox_custom_ofwc_offer_form_close_btn").hide();
            });

            $('#woocommerce-make-offer-form-quantity').autoNumeric('init',
                {
                    vMin: '0',
                    mDec: '0',
                    lZero: 'deny',
                    aForm: false
                }
            );

            $('#woocommerce-make-offer-form-price-each').autoNumeric('init',
                {
                    mDec: '2',
                    aSign: '',
                    //wEmpty: 'sign',
                    lZero: 'allow',
                    aForm: false
                }
            );

            $('#woocommerce-make-offer-form').find( ':submit' ).removeAttr( 'disabled','disabled' );

            (function($){
                $.fn.money_field = function(opts) {
                    var defaults = { width: null, symbol: 'dddd' };
                    var opts = $.extend(defaults, opts);
                    return this.each(function() {
                        if(opts.width)
                            $(this).css('width', opts.width + 'px');
                        $(this).wrap("<div class='angelleye-input-group'>").before("<span class='angelleye-input-group-addon'>" + opts.symbol + "</span>");
                    });
                };
            })(jQuery);

            // Submit offer form
            $("form[name='woocommerce-make-offer-form']").submit(function()
            {
                $('.tab_custom_ofwc_offer_tab_alt_message_2').hide();

                var offerCheckMinValuesPassed = true;

                if($('#woocommerce-make-offer-form-price-each').autoNumeric('get') == '0')
                {
                    $('#woocommerce-make-offer-form-price-each').autoNumeric('set', '' );
                    $('#woocommerce-make-offer-form-price-each').autoNumeric('update',
                        {
                            mDec: '2',
                            aSign: '',
                            //wEmpty: 'sign',
                            lZero: 'allow',
                            aForm: false
                        }
                    );
                    offerCheckMinValuesPassed = false;
                }

                if($('#woocommerce-make-offer-form-quantity').autoNumeric('get') == '0')
                {
                    $('#woocommerce-make-offer-form-quantity').autoNumeric('set', '' );
                    $('#woocommerce-make-offer-form-quantity').autoNumeric('update',
                        {
                            vMin: '0',
                            mDec: '0',
                            lZero: 'deny',
                            aForm: false
                        }
                    );
                    offerCheckMinValuesPassed = false;
                }

                if( offerCheckMinValuesPassed === false )
                {
                    return false;
                }

                var parentOfferId = $("input[name='parent_offer_id']").val();
                var parentOfferUid = $("input[name='parent_offer_uid']").val();

                var offerProductId = '';
                var offerVariationId = '';
                var offerProductId = $("input[name='add-to-cart']").val();
                var offerVariationId = $("input[name='variation_id']").val();

                var join_our_mailing_list = "no";
                
                if($("#join_our_mailing_list").length > 0) {
                    if($('#join_our_mailing_list').attr('checked')) {
                        join_our_mailing_list = "yes";
                    } 
                }
                
                var offerQuantity = $("input[name='offer_quantity']").autoNumeric('get');
                var offerPriceEach = $("input[name='offer_price_each']").autoNumeric('get');

                var offerForm = $('#woocommerce-make-offer-form');

                if(offerProductId != '')
                {
                    // disable submit button
                    $( offerForm ).find( ':submit' ).attr( 'disabled','disabled' );

                    // hide error divs
                    $('#tab_custom_ofwc_offer_tab_alt_message_2').hide();
                    $('#tab_custom_ofwc_offer_tab_alt_message_custom').hide();

                    // show loader image
                    $('#offer-submit-loader').show();

                    // abort any pending request
                    if (request) {
                        request.abort();
                    }
                    
                    var post_data_array = $("#woocommerce-make-offer-form").serializeArray();
                    post_data_array.push({name: 'offer_product_id', value: offerProductId});
                    post_data_array.push({name: 'offer_variation_id', value: offerVariationId});
                    post_data_array.push({name: 'parent_offer_id', value: parentOfferId});
                    post_data_array.push({name: 'parent_offer_uid', value: parentOfferUid});
                    post_data_array.push({name: 'offer_quantity', value: offerQuantity});
                    post_data_array.push({name: 'offer_price_each', value: offerPriceEach});
                    post_data_array.push({name: 'join_our_mailing_list', value: join_our_mailing_list});
                    
                    jQuery("div.product-addon").each(function(){
                         var group_name = jQuery.trim(jQuery(this).find('h3.addon-name').text());
                         //var input_tag = jQuery(this).find('input');
                         var input_tag = jQuery(this).find(":input[name^='addon-9']");
                         
                         input_tag.each(function(){
                            if(jQuery(this).is(':checkbox') || jQuery(this).is(':radio')){
                                if(jQuery(this).is(':checked')){
                                    post_data_array.push({name: jQuery(this).attr("name"), value: jQuery(this).val()});
                                    post_data_array.push({name: jQuery(this).attr("name"), price: jQuery(this).attr('data-raw-price')});
                                    post_data_array.push({name: jQuery(this).attr("name"), group: group_name});
                                }
                            }
                            else{
                                if(jQuery(this).val() !== ''){
                                    post_data_array.push({name: jQuery(this).attr("name"), value: jQuery(this).val()});
                                    post_data_array.push({name: jQuery(this).attr("name"), price: jQuery(this).attr('data-raw-price')});
                                    post_data_array.push({name: jQuery(this).attr("name"), group: group_name});
                                }
                            }
                        });
                    });                                               
                    var data_make_offer = {
                        action: 'new_offer_form_submit',
                        security: offers_for_woocommerce_js_params.offers_for_woocommerce_params_nonce,
                        value: post_data_array
                    };

                    // fire off the request
                    var request = $.ajax({
                        url: offers_for_woocommerce_js_params.ajax_url,
                        type: "post",
                        data: data_make_offer
                    });

                    // callback handler that will be called on success
                    request.done(function (response, textStatus, jqXHR){                        
                        if(request.statusText == 'OK' || request.statusText == 'success'){
                            var myObject = JSON.parse(request.responseText);
                            var responseStatus = myObject['statusmsg'];
                            var responseStatusDetail = myObject['statusmsgDetail'];

                            if(responseStatus == 'failed')
                            {
                                //console.log('failed');
                                // Hide loader image
                                $('#offer-submit-loader').hide();
                                // Show error message DIV
                                $('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');
                                $( offerForm ).find( ':submit' ).removeAttr( 'disabled','disabled' );
                            }
                            else if(responseStatus == 'failed-custom')
                            {
                                //console.log('failed-custom-msg');
                                // Hide loader image
                                $('#offer-submit-loader').hide();
                                // Show error message DIV
                                $('#tab_custom_ofwc_offer_tab_alt_message_custom ul #alt-message-custom').html("<strong>Error: </strong>"+responseStatusDetail);
                                $('#tab_custom_ofwc_offer_tab_alt_message_custom').slideToggle('fast');
                                $( offerForm ).find( ':submit' ).removeAttr( 'disabled','disabled' );
                            }
                            else if(responseStatus == 'failed-spam')
                            {
                                //console.log('failed-custom-msg');
                                // Hide loader image
                                $('#offer-submit-loader').hide();
                                // Show error message DIV
                                $('#tab_custom_ofwc_offer_tab_alt_message_custom ul #alt-message-custom').html("<strong>Error: </strong>"+responseStatusDetail);
                                $('#tab_custom_ofwc_offer_tab_alt_message_custom').slideToggle('fast');
                                $( offerForm ).find( ':submit' ).removeAttr( 'disabled','disabled' );
                                $('#tab_custom_ofwc_offer_tab_inner fieldset').hide();
                            } else if(responseStatus == 'accepted-offer') {
                                window.location = decodeURI(myObject['redirect']);
                            }
                            else
                            {
                                // SUCCESS
                                // Hide loader image
                                $('#offer-submit-loader').hide();
                                $( offerForm ).find( ':submit' ).removeAttr( 'disabled','disabled' );
                                $('#tab_custom_ofwc_offer_tab_inner fieldset').hide();
                                $('#tab_custom_ofwc_offer_tab_alt_message_success').slideToggle('fast');
                            }

                        } else {
                            //console.log('error received');
                            //alert('Timeout has likely occured, please refresh this page to reinstate your session');
                            // Hide loader image
                            $('#offer-submit-loader').hide();
                            $('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');
                            $( offerForm ).find( ':submit' ).removeAttr( 'disabled','disabled' );
                        }
                    });

                    // callback handler that will be called on failure
                    request.fail(function (jqXHR, textStatus, errorThrown){
                        // log the error to the console
                        // Hide loader image
                        $('#offer-submit-loader').hide();
                        $('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');
                    });
                }
                else
                {
                    // Hide loader image
                    $('#offer-submit-loader').hide();
                    $('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');
                }
                return false;
            });
        });

        function angelleyeOpenMakeOfferForm(){

            if( $(".offers-for-woocommerce-make-offer-button-single-product").hasClass("offers-for-woocommerce-make-offer-button-single-product-lightbox") )
            {
                if( $("#lightbox_custom_ofwc_offer_form").hasClass('active') )
                {
                    $("#lightbox_custom_ofwc_offer_form").hide();
                    $("#lightbox_custom_ofwc_offer_form").removeClass('active');
                    $("#lightbox_custom_ofwc_offer_form_close_btn").hide();
                }
                else
                {
                    $("#lightbox_custom_ofwc_offer_form").addClass('active');
                    $("#lightbox_custom_ofwc_offer_form").show();
                    $("#lightbox_custom_ofwc_offer_form_close_btn").show();
                }

                if( $("#woocommerce-make-offer-form-quantity").attr('type') == 'hidden' )
                {
                    $("#woocommerce-make-offer-form-price-each").focus();
                }
                else
                {
                    $("#woocommerce-make-offer-form-quantity").focus();
                }
            }
            else
            {
                if( $('.woocommerce-tabs .tabs li.tab_custom_ofwc_offer_tab').length > 0 ){
                    $(".woocommerce-tabs .tabs li.tab_custom_ofwc_offer_tab a").click();
                }

                if( $("#woocommerce-make-offer-form-quantity").attr('type') == 'hidden' )
                {
                    $("#woocommerce-make-offer-form-price-each").focus();
                }
                else
                {
                    $("#woocommerce-make-offer-form-quantity").focus();
                }

               if ( $( ".tab_custom_ofwc_offer_tab" ).length > 0 ) {
                    var targetTab = $(".tab_custom_ofwc_offer_tab");
                    $('html, body').animate({
                        scrollTop: $(targetTab).offset().top - '100'
                    }, 'fast');
                }
            }

            return false;
        }
        
        $(window).load(function(){
            if( $(".offers-for-woocommerce-make-offer-button-single-product").hasClass("offers-for-woocommerce-make-offer-button-single-product-lightbox") )
            {
                $("#aeofwc-close-lightbox-link").css('display','block');
            }
            var variantDisplay = $('.single_variation_wrap').css('display');
            if($('body.woocommerce.single-product #content div.product').hasClass('product-type-variable') && variantDisplay != 'block')
            {
                if( $(".offers-for-woocommerce-make-offer-button-single-product").hasClass("offers-for-woocommerce-make-offer-button-single-product-lightbox") )
                {
                    $("#lightbox_custom_ofwc_offer_form").hide();
                    $("#lightbox_custom_ofwc_offer_form").removeClass('active');
                    $("#lightbox_custom_ofwc_offer_form_close_btn").hide();
                }
                else
                {
                    $('#tab_custom_ofwc_offer_tab_inner').hide();
                }
                $('#tab_custom_ofwc_offer_tab_alt_message').show();
            }
        });

        // offer quantity input keyup
        $('#woocommerce-make-offer-form-quantity').keyup(function() {  
            updateTotal();
        });

        // offer price each input keyup
        $('#woocommerce-make-offer-form-price-each').keyup(function() {  
            updateTotal();
        });

        // Update totals
        var updateTotal = function () {
            var input1 = $('#woocommerce-make-offer-form-quantity').autoNumeric('get');
            var input2 = $('#woocommerce-make-offer-form-price-each').autoNumeric('get');
            if (isNaN(input1) || isNaN(input2)) {
                    $('#woocommerce-make-offer-form-total').val('');
            } else {
                var theTotal = (input1 * input2);
                var currencySymbol = $('#woocommerce-make-offer-form-total').attr('data-currency-symbol');
                if(!currencySymbol) {
                    currencySymbol = '$';
                }
                $('#woocommerce-make-offer-form-total').val(parseFloat(theTotal, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
            }
        };

        /**
         * Adds bn code for PayPal Standard
         * @since   0.1.0
         */
        var CheckPayPalStdBn = function () {
            if ($('input[name="business"]').length > 0) {
                if ($('input[name="bn"]').length > 0) {
                    $('input[name="bn"]').val("AngellEYE_PHPClass");

                }
                else {
                    $('input[name="business"]').after("<input type='hidden' name='bn' value='AngellEYE_PHPClass' />");
                }
            }
        };

        // Check for PayPal Standard bn
        CheckPayPalStdBn();
    });
}(jQuery));
