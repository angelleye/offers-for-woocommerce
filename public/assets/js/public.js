(function ( $ ) {
	"use strict";
	$(function () {
		// Public-facing JavaScript
				
		$(document).ready(function(){
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
                    angelleyeOpenMakeOfferForm();
                }
            }

            var makeOfferBtnPosition = $('#offers-for-woocommerce-add-to-cart-wrap').attr('data-ofwc-position');
            if(makeOfferBtnPosition !== '' && makeOfferBtnPosition !== 'default')
            {
                var makeOfferBtnhtml = $('.single_variation_wrap_angelleye.ofwc_offer_tab_form_wrap').html();

                // after price
                if(makeOfferBtnPosition == 'after_price') {
                    $('.product .summary .price:first').after("<div class='offers-for-woocommerce-add-to-cart-wrap ofwc-no-float'>"+ makeOfferBtnhtml +"");
                }

                // after product tabs
                if(makeOfferBtnPosition == 'after_tabs') {
                    $('.product .woocommerce-tabs').after("<div class='offers-for-woocommerce-add-to-cart-wrap ofwc-no-float'>"+ makeOfferBtnhtml +"");
                }

                // before add to cart (after single_variation)
                if(makeOfferBtnPosition == 'before_add') {
                    $('#offers-for-woocommerce-add-to-cart-wrap div:first').before("<div class='offers-for-woocommerce-add-to-cart-wrap ofwc-no-float'>" + makeOfferBtnhtml + "");
                }
            }

            $(".offers-for-woocommerce-make-offer-button-single-product").click(function(){
                angelleyeOpenMakeOfferForm();
            });

            $(".tab_custom_ofwc_offer_tab a").on('click', function()
            {
                angelleyeOpenMakeOfferForm();
            });

            $("#lightbox_custom_ofwc_offer_form_close_btn").on('click', function()
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

            var offerSubmitBtnDefaultVal = $('#woocommerce-make-offer-form').find( ':submit' ).attr('data-orig-val');
            if( offerSubmitBtnDefaultVal == '')
                offerSubmitBtnDefaultVal = 'Submit Offer';
            $('#woocommerce-make-offer-form').find( ':submit' ).attr('value', offerSubmitBtnDefaultVal);
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
            $('#woocommerce-make-offer-form').submit(function()
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

                var offerName = $("input[name='offer_name']").val();
                var offerEmail = $("input[name='offer_email']").val();
                var offerPhone = $("input[name='offer_phone']").val();
                var offerCompanyName = $("input[name='offer_company_name']").val();

                var offerNotes = $("#angelleye-offer-notes").val();

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

                    var formData = {};
                    formData['offer_name'] = offerName;
                    formData['offer_email'] = offerEmail;
                    formData['offer_phone'] = offerPhone;
                    formData['offer_company_name'] = offerCompanyName;
                    formData['offer_quantity'] = offerQuantity;
                    formData['offer_price_each'] = offerPriceEach;
                    formData['offer_product_id'] = offerProductId;
                    formData['offer_variation_id'] = offerVariationId;
                    formData['parent_offer_id'] = parentOfferId;
                    formData['parent_offer_uid'] = parentOfferUid;
                    formData['offer_notes'] = offerNotes;

                    // ajax submit offer
                    var ajaxtarget = '?woocommerceoffer_post=1';

                    // abort any pending request
                    if (request) {
                        request.abort();
                    }

                    // fire off the request
                    var request = $.ajax({
                        url: ajaxtarget,
                        type: "post",
                        data: formData
                    });

                    // callback handler that will be called on success
                    request.done(function (response, textStatus, jqXHR){
                        if(request.statusText == 'OK'){

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
                $(".woocommerce-tabs .tabs li").removeClass("active");
                $(".woocommerce-tabs .tabs li.tab_custom_ofwc_offer_tab").addClass("active");
                $(".woocommerce-tabs div.panel").css("display", "none");
                $(".woocommerce-tabs div#tab-tab_custom_ofwc_offer").css("display", "block");

                if( $("#woocommerce-make-offer-form-quantity").attr('type') == 'hidden' )
                {
                    $("#woocommerce-make-offer-form-price-each").focus();
                }
                else
                {
                    $("#woocommerce-make-offer-form-quantity").focus();
                }

                var targetTab = $(".tab_custom_ofwc_offer_tab");
                $('html, body').animate({
                    scrollTop: $(targetTab).offset().top - '100'
                }, 'fast');
            }

            return false;
        }
		
		$(window).load(function(){
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
		$(window).load(function(){
			var datFunction = function () {
				$('.variations select').change(function() {
					
					$('#tab_custom_ofwc_offer_tab_alt_message_2').hide();
					$('#tab_custom_ofwc_offer_tab_alt_message_success').hide();
					$('#tab_custom_ofwc_offer_tab_inner fieldset').show();
					
					var selectedVariantOption = $('.variations select').val();
					//var variantDisplay = $('.single_variation_wrap.ofwc_offer_tab_form_wrap').css('display');
					
					// Toggle form based on visibility
					if(selectedVariantOption == '')
					{
						$('#tab_custom_ofwc_offer_tab_inner').hide();
						$('#tab_custom_ofwc_offer_tab_alt_message').show();
					}
					else
					{
						$('#tab_custom_ofwc_offer_tab_inner').show();
						$('#tab_custom_ofwc_offer_tab_alt_message').hide();				
					}
				});
			}();
			datFunction;
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
