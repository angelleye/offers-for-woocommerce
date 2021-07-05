(function ( $ ) {
	"use strict";

	$(function () {

		// Place your administration-specific JavaScript here
        $(document).ready(function(){

            /**
             * Init datepicker for offer expiration date
             * @since   1.0.1
             */

            var currentDate = new Date();
            $('.datepicker').datepicker({
                minDate: currentDate
            });

            $('#meta-box-offers-submit').click(function(){                               
                var theDate = new Date(Date.parse($('.datepicker').datepicker('getDate')));
                if (theDate == 'Invalid Date')
                {
                    $("#offer_expiration_date_hidden").val('');
                }
                else{
                    var dateFormatted = $.datepicker.formatDate('yy-mm-dd', theDate);
                    $("#offer_expiration_date_hidden").val(dateFormatted);                    
                }
            });

            $('#original-offer-price-per').autoNumerics('init',
                {
                    aForm: true,      /* Controls if default values are formatted on page ready (load) */
                    aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                    aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                    //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                    vMin : '0.00',    /* Enter the minimum value allowed */
                    lZero: 'allow',   /* Controls if leading zeros are allowed */
                    wEmpty: 'sign',   /* controls input display behavior. */
                    mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                }
            );

            $('#original-offer-amount').autoNumerics('init',
                {
                    aForm: true,      /* Controls if default values are formatted on page ready (load) */
                    aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                    aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                    //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                    lZero: 'allow',   /* Controls if leading zeros are allowed */
                    wEmpty: 'zero',   /* controls input display behavior. */
                    mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                }
            );


            $('#offer-price-per').autoNumerics('init',
                {
                    aForm: true,      /* Controls if default values are formatted on page ready (load) */
                    aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                    aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                    //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                    vMin : '0.00',    /* Enter the minimum value allowed */
                    lZero: 'allow',   /* Controls if leading zeros are allowed */
                    wEmpty: 'sign',   /* controls input display behavior. */
                    mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                }
            );

            $('#offer-total').autoNumerics('init',
                {
                    aForm: true,      /* Controls if default values are formatted on page ready (load) */
                    aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                    aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                    //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                    lZero: 'allow',   /* Controls if leading zeros are allowed */
                    wEmpty: 'zero',   /* controls input display behavior. */
                    mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                }
            );
    
            $('#offer_shipping_cost').autoNumerics('init',
                {
                    aForm: false,      /* Controls if default values are formatted on page ready (load) */
                    aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                    aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                    //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                    mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                    lZero: 'allow',   /* Controls if leading zeros are allowed */
                    wEmpty: 'zero',   /* controls input display behavior. */
                }
            );

            var currentPostStatus = $('#woocommerce_offer_post_status').val();
            
            if(currentPostStatus !== 'countered-offer') {
                $('.woocommerce-offer-final-offer-wrap').hide();
            } else {
                $('.woocommerce-offer-final-offer-wrap').show();
            }
            
            if(currentPostStatus === 'declined-offer') {
                $('.woocommerce-offer-send-coupon-wrap').show();
                $('.woocommerce-offer-expiration-wrap').hide();
            } else {
                $('.woocommerce-offer-send-coupon-wrap').hide();
                if(currentPostStatus === 'completed-offer') {
                    $('.woocommerce-offer-expiration-wrap').hide();
                } 
            }

            $('#woocommerce_offer_post_status').change(function(){
                if( $(this).val() == 'countered-offer') {
                    $('.woocommerce-offer-final-offer-wrap').fadeIn('fast');
                } else {
                    $('.woocommerce-offer-final-offer-wrap').slideUp();
                }
                if( $(this).val() == 'declined-offer') { 
                    $('.woocommerce-offer-send-coupon-wrap').fadeIn('fast');
                    $('.woocommerce-offer-expiration-wrap').hide();
                } else {
                    $('.woocommerce-offer-send-coupon-wrap').slideUp();
                    if( $(this).val() !== 'completed-offer') {
                        $('.woocommerce-offer-expiration-wrap').show();
                    }
                }
                return false;
            });
            if(ofw_param.ofw_offer_expiration_date_show === 'true')
            {
                $('#angelleye-woocommerce-offer-meta-summary-expire-notice-msg').show();
            }

            updateTotal();

            // Submit post
            $('body.wp-admin.post-php.post-type-woocommerce_offer #post').submit(function()
            {
                var offerCheckMinValuesPassed = true;

                if ($('#offer-price-per').autoNumerics('get') == '0') {
                    $('#offer-price-per').autoNumerics('set', '');
                    $('#offer-price-per').autoNumerics('update',
                        {
                            mDec: ofw_param.ofw_admin_js_number_of_decimals,
                            aForm: true,      /* Controls if default values are formatted on page ready (load) */
                            aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                            aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                            //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                            vMin : '0.00',    /* Enter the minimum value allowed */
                            lZero: 'allow',   /* Controls if leading zeros are allowed */
                            wEmpty: 'zero',   /* controls input display behavior. */
                        }
                    );
                    offerCheckMinValuesPassed = false;
                }


                if( offerCheckMinValuesPassed === false )
                {
                    return false;
                }
            });

            // AJAX - Add Offer Note
            $('#angelleye-woocommerce-offers-ajax-addnote-btn').click(function()
            {
                var targetID = $(this).attr('data-target');
                var noteContent = $('#angelleye-woocommerce-offers-ajax-addnote-text').val();

                if(noteContent.length < 3)
                {
                    alert('Your note is not long enough!');
                    return false;
                }

                if( $('#angelleye-woocommerce-offers-ajax-addnote-send-to-buyer').is(':checked') )
                {
                    var noteSendToBuyer = $('#angelleye-woocommerce-offers-ajax-addnote-send-to-buyer').val();
                }
                else
                {
                    var noteSendToBuyer = '';
                }

                var data = {
                    'action': 'addOfferNote',
                    'targetID': targetID,
                    'noteContent': noteContent,
                    'noteSendToBuyer': noteSendToBuyer
                };

                // post it
                $.post(ajaxurl, data, function(response) {
                    if ( 'failed' !== response )
                    {
                        var redirectUrl = response;
                        top.location.replace(redirectUrl);
                        return true;
                    }
                    else
                    {
                        alert('add note failed');
                        return false;
                    }
                });
                /*End Post*/
            });
        });

        // Update totals
        var updateTotal = function () {
            var input1 = $('#offer-quantity').val();
            var input2 = $('#offer-price-per').autoNumerics('get');
            var offer_shiipng_cost = $('#offer_shipping_cost').autoNumerics('get');
            if (isNaN(input1) || isNaN(input2)) {
                $('#offer-total').autoNumerics('set','');
            } else {
                var theTotal = (input1 * input2);
                
                var theTotal = (parseFloat(theTotal) + parseFloat(offer_shiipng_cost));

                $('#offer-total').autoNumerics('set',theTotal);

                $('#offer-total').autoNumerics('update',
                    {
                        aForm: true,      /* Controls if default values are formatted on page ready (load) */
                        aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                        aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                        //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                        lZero: 'allow',   /* Controls if leading zeros are allowed */
                        wEmpty: 'zero',   /* controls input display behavior. */
                        mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                    }
                );
            }

            // show notice if offer quantity exceeds stock and backorders not allowed
            var maxStockAvailable = $('#offer-max-stock-available').val();
            var backordersAllowed = $('#offer-backorders-allowed').val();
            if( backordersAllowed !== 'true' )
            {
                if(parseInt(maxStockAvailable) != '')
                {
                    if ( parseInt(maxStockAvailable) < parseInt(input1) ) {
                        $('#angelleye-woocommerce-offer-meta-summary-notice-msg').show();
                    }
                    else
                    {
                        $('#angelleye-woocommerce-offer-meta-summary-notice-msg').hide();
                    }
                }
            }
        };

        $(document).on('keyup click', '#offer-quantity, #offer-price-per, #offer_shipping_cost', function (event) {
            updateTotal();
        });
        

        // toggle buyer offer history panel
        $('.angelleye-offer-buyer-stats-toggle').click(function(){
            $('#angelleye-offer-buyer-history').slideToggle('800');
            return false;
        });

        // Move to Trash confirmation
        $('#aeofwc-delete-action .deletion').click(function(){

            if(!confirm('are you sure?'))
            {
                return false;
            }
        });

        if($('#ofwc_enable_shipping').is(":checked")) {
            $('#offer_shipping_cost').parent('.angelleye-input-group').show();
        } else {
            $('#offer_shipping_cost').parent('.angelleye-input-group').hide();
        }

        $("#ofwc_enable_shipping").click(function() {
            if($(this).is(":checked")) {
                $('#offer_shipping_cost').parent('.angelleye-input-group').show();
            } else {
                $('#offer_shipping_cost').parent('.angelleye-input-group').hide();
            }
        });
        
        $(document).on('click','#meta-box-offers-submit',function() {
            var original_price = $('#original-offer-price-per').val();
            var countered_price = $('#offer-price-per').val();            
            var current_status = $('#woocommerce_offer_post_status').val();
            if(original_price === countered_price && current_status==='countered-offer'){
                $('#counter_offer_notice').show();
                return false;
            }
        });


	});
}(jQuery));

