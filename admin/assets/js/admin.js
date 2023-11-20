(function ( $ ) {
    "use strict";
    $(function () {
        let offer_price_per = document.getElementById('offer-price-per');

        /*Place your administration-specific JavaScript here*/
        let original_offer_price_per = document.getElementById('original-offer-price-per');
        let original_offer_amount = document.getElementById('original-offer-amount');
        let offer_shipping_cost = document.getElementById('offer_shipping_cost');
        let offer_expiration_date = document.getElementById('offer_expiration_date_hidden');
        let offer_total = document.getElementById('offer-total');
        let final_offer_wrap = getElementFirstClass('woocommerce-offer-final-offer-wrap');
        let single_use_offer_wrap = getElementFirstClass('woocommerce-offer-single-use-wrap');
        let send_coupon_wrap = getElementFirstClass('woocommerce-offer-send-coupon-wrap');
        let expiration_wrap = getElementFirstClass('woocommerce-offer-expiration-wrap');
        let post_status = document.getElementById('woocommerce_offer_post_status');

        /**
         * Init datepicker for offer expiration date
         * @since   1.0.1
         */
        var currentDate = new Date();
        $('.datepicker').datetimepicker({
            minDate: currentDate,
            format: 'M d, Y H:i'
        });

        var meta_box_offers_submit = document.getElementById('meta-box-offers-submit');
        if( undefined !== meta_box_offers_submit && meta_box_offers_submit !== null ){
            meta_box_offers_submit.addEventListener('click' ,function(){
                var theDate = new Date(Date.parse(jQuery('.datepicker').datetimepicker('getValue')));
                if (theDate === 'Invalid Date') {
                    offer_expiration_date.value = '';
                }
                else{
                    offer_expiration_date.value = customDateFormat(theDate);
                }
            });

        }

        $(original_offer_price_per).autoNumerics('init', {
            aForm: true,      /* Controls if default values are formatted on page ready (load) */
            aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
            aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
            /*pSign : 'p',    // Placement of the currency : p = left and s = right */
            vMin : '0.00',    /* Enter the minimum value allowed */
            lZero: 'allow',   /* Controls if leading zeros are allowed */
            wEmpty: 'sign',   /* controls input display behavior. */
            mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
        });

        $(original_offer_amount).autoNumerics('init', {
            aForm: true,      /* Controls if default values are formatted on page ready (load) */
            aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
            aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
            /*pSign : 'p',     //Placement of the currency : p = left and s = right */
            lZero: 'allow',   /* Controls if leading zeros are allowed */
            wEmpty: 'zero',   /* controls input display behavior. */
            mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
        });

        $(offer_price_per).autoNumerics('init', {
            aForm: true,      /* Controls if default values are formatted on page ready (load) */
            aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
            aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
            /*pSign : 'p',    // Placement of the currency : p = left and s = right */
            vMin : '0.00',    /* Enter the minimum value allowed */
            lZero: 'allow',   /* Controls if leading zeros are allowed */
            wEmpty: 'sign',   /* controls input display behavior. */
            mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
        });

        $(offer_total).autoNumerics('init', {
            aForm: true,      /* Controls if default values are formatted on page ready (load) */
            aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
            aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
            /*pSign : 'p',    // Placement of the currency : p = left and s = right */
            lZero: 'allow',   /* Controls if leading zeros are allowed */
            wEmpty: 'zero',   /* controls input display behavior. */
            mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
        });

        $(offer_shipping_cost).autoNumerics('init', {
            aForm: false,      /* Controls if default values are formatted on page ready (load) */
            aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
            aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
            /*pSign : 'p',    // Placement of the currency : p = left and s = right */
            mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
            lZero: 'allow',   /* Controls if leading zeros are allowed */
            wEmpty: 'zero',   /* controls input display behavior. */
        });

        /*var currentPostStatus = $('#woocommerce_offer_post_status').val();*/
        let currentPostStatus = document.getElementById("woocommerce_offer_post_status").value;

        if( undefined !== currentPostStatus && currentPostStatus !== null ){

            if( currentPostStatus === 'accepted-offer' || currentPostStatus === 'countered-offer' ) {
                single_use_offer_wrap.style.display = '';
            } else {
                single_use_offer_wrap.style.display = 'none';
            }

            if(currentPostStatus !== 'countered-offer') {
                getElementFirstClass("woocommerce-offer-final-offer-wrap").style.display = 'none';
            } else {
                getElementFirstClass("woocommerce-offer-final-offer-wrap").style.display = "";
            }

            if(currentPostStatus === 'declined-offer') {
                getElementFirstClass("woocommerce-offer-send-coupon-wrap").style.display = "";
                getElementFirstClass("woocommerce-offer-expiration-wrap").style.display = 'none';
            } else {
                getElementFirstClass("woocommerce-offer-send-coupon-wrap").style.display = 'none';
                if(currentPostStatus === 'completed-offer') {
                    getElementFirstClass("woocommerce-offer-expiration-wrap").style.display = 'none';
                }
            }

            if( currentPostStatus === 'accepted-offer' || currentPostStatus === 'countered-offer' ) {
                $('.woocommerce-offer-single-use-wrap').fadeIn('fast');
            } else {
                $('.woocommerce-offer-single-use-wrap').slideUp();
            }
        }

        if( undefined !==  post_status && post_status !== null ){
            post_status.addEventListener('change', function ( event ) {
                let offerStatus = this.value;
                if( offerStatus === 'countered-offer') {
                    $('.woocommerce-offer-final-offer-wrap').fadeIn('fast');
                } else {
                    $('.woocommerce-offer-final-offer-wrap').slideUp();
                }

                if( offerStatus === 'declined-offer') {
                    $('.woocommerce-offer-send-coupon-wrap').fadeIn('fast');
                    $('.woocommerce-offer-expiration-wrap').hide();
                } else {
                    $('.woocommerce-offer-send-coupon-wrap').slideUp();
                    if( offerStatus !== 'completed-offer') {
                        $('.woocommerce-offer-expiration-wrap').show();
                    }
                }

                if( offerStatus === 'accepted-offer' || offerStatus === 'countered-offer' ) {
                    $('.woocommerce-offer-single-use-wrap').fadeIn('fast');
                } else {
                    $('.woocommerce-offer-single-use-wrap').slideUp();
                }
                return false;
            });
        }

        if( undefined !== ofw_param.ofw_offer_expiration_date_show && ofw_param.ofw_offer_expiration_date_show !== null && ofw_param.ofw_offer_expiration_date_show === 'true') {
            document.getElementById('angelleye-woocommerce-offer-meta-summary-expire-notice-msg').classList.remove("angelleye-hidden");
        }

        /*Submit post*/
        var offer_post = document.querySelector('body.wp-admin.post-php.post-type-woocommerce_offer #post');
        if(undefined !== offer_post && offer_post !== null){
            offer_post.addEventListener( 'submit', function (){
                var offerCheckMinValuesPassed = true;

                if ( $('#offer-price-per').autoNumerics('get') === '0') {
                    $('#offer-price-per').autoNumerics('set', '');
                    $('#offer-price-per').autoNumerics('update', {
                        mDec: ofw_param.ofw_admin_js_number_of_decimals,
                        aForm: true,      /* Controls if default values are formatted on page ready (load) */
                        aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                        aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                        /*pSign : 'p',    // Placement of the currency : p = left and s = right */
                        vMin : '0.00',    /* Enter the minimum value allowed */
                        lZero: 'allow',   /* Controls if leading zeros are allowed */
                        wEmpty: 'zero',   /* controls input display behavior. */
                    });
                    offerCheckMinValuesPassed = false;
                }

                if( offerCheckMinValuesPassed === false ) {
                    return false;
                }
            });
        }

        /*AJAX - Add Offer Note*/
        var wc_addnote_button = document.getElementById('angelleye-woocommerce-offers-ajax-addnote-btn');
        if( undefined !== wc_addnote_button && wc_addnote_button !== null ) {
            wc_addnote_button.addEventListener('click', function () {
                var targetID = this.getAttribute('data-target');
                var noteContent = document.getElementById('angelleye-woocommerce-offers-ajax-addnote-text').value

                if (noteContent.length < 3) {
                    alert('Your note is not long enough!');
                    return false;
                }

                var noteSendToBuyer = '';
                const sendToBuyer = document.getElementById("angelleye-woocommerce-offers-ajax-addnote-send-to-buyer");
                if (sendToBuyer.checked) {
                    noteSendToBuyer = sendToBuyer.value;
                }

                var data = {
                    'action': 'addOfferNote',
                    'targetID': targetID,
                    'noteContent': noteContent,
                    'noteSendToBuyer': noteSendToBuyer
                };

                /*post it*/
                $.post(ajaxurl, data, function (response) {
                    if ('failed' !== response) {
                        var redirectUrl = response;
                        top.location.replace(redirectUrl);
                        return true;
                    } else {
                        alert('add note failed');
                        return false;
                    }
                });
                /*End Post*/
            });
        }

        let updateTotal = function () {
            let input1 = document.getElementById('offer-quantity');
            let input2 = document.getElementById('offer-price-per');
            input2 = $(input2).autoNumerics('get');
            let offer_total = document.getElementById('offer-total');
            let offer_shipping_cost = document.getElementById('offer_shipping_cost');
            offer_shipping_cost = $(offer_shipping_cost).autoNumerics('get');
            let offer_meta_summary_notice_msg = document.getElementById('angelleye-woocommerce-offer-meta-summary-notice-msg');
            if (isNaN(input1.value) || isNaN(input2)){
                $(offer_total).autoNumerics('set','');
            } else {
                let theTotal = (input1.value * input2) ;
                theTotal = (parseFloat(theTotal) + parseFloat(offer_shipping_cost));
                $(offer_total).autoNumerics('set',theTotal);
                $(offer_total).autoNumerics('update', {
                    aForm: true,      /* Controls if default values are formatted on page ready (load) */
                    aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                    aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                    lZero: 'allow',   /* Controls if leading zeros are allowed */
                    wEmpty: 'zero',   /* controls input display behavior. */
                    mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                });
            }

            updateTotal();

            /*show notice if offer quantity exceeds stock and backorders not allowed*/
            let maxStockAvailable = document.getElementById('offer-max-stock-available');
            let backordersAllowed = document.getElementById('offer-backorders-allowed');
            if (backordersAllowed !== 'true'){
                if ( parseInt( maxStockAvailable.value) < parseInt( input1.value)){
                    offer_meta_summary_notice_msg.classList.remove("angelleye-hidden");
                } else {
                    offer_meta_summary_notice_msg.classList.add("angelleye-hidden");
                }
            }
        }

        $(document).on('keyup click', '#offer-quantity, #offer-price-per, #offer_shipping_cost', function (event) {
            updateTotal();
        });

        /*toggle buyer offer history panel*/
        document.querySelectorAll('.angelleye-offer-buyer-stats-toggle').forEach((object) => {
            object.addEventListener('click', function () {
                $('#angelleye-offer-buyer-history').slideToggle('800');
                return false;
            });
        });

        // Move to Trash confirmation
        document.querySelectorAll('#aeofwc-delete-action .deletion').forEach((object) => {
            object.addEventListener('click', function () {
                if(!confirm('are you sure?')) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        if(document.getElementById('ofwc_enable_shipping').checked) {
            $('#offer_shipping_cost').parent('.angelleye-input-group').show();
        } else {
            $('#offer_shipping_cost').parent('.angelleye-input-group').hide();
        }

        var ofwc_enable_shipping = document.getElementById('ofwc_enable_shipping');
        if( undefined !== ofwc_enable_shipping && ofwc_enable_shipping !== null ){
            ofwc_enable_shipping.addEventListener('click', function() {
                let ofwc_enable_shipping = document.getElementById('ofwc_enable_shipping');
                if(ofwc_enable_shipping.checked) {
                    $('#offer_shipping_cost').parent('.angelleye-input-group').show();
                } else {
                    $('#offer_shipping_cost').parent('.angelleye-input-group').hide();
                }
            });
        }

        if( undefined !== meta_box_offers_submit && meta_box_offers_submit !== null ) {
            meta_box_offers_submit.addEventListener('click', function (e) {
                let original_price = document.getElementById("original-offer-price-per").value;
                let countered_price = document.getElementById("offer-price-per").value;
                let current_status = document.getElementById("woocommerce_offer_post_status").value;

                if (original_price === countered_price && current_status === 'countered-offer') {
                    document.getElementById("counter_offer_notice").style.display = "";
                    e.preventDefault();
                    return false;
                }
            });
        }

    });
}(jQuery));

function getElementFirstClass(classname){
    let elements = document.getElementsByClassName(classname);
    if (elements.length === 1){
        return elements[0];
    } else {
        return null;
    }
}

function customDateFormat( dateobj = new Date() ){

    var year = dateobj.getFullYear();
    var month = ("0" + (dateobj.getMonth() + 1)).slice(-2);
    var date = ("0" + dateobj.getDate()).slice(-2);
    var hours =  ("0" + dateobj.getHours()).slice(-2);
    var minutes = ("0" + dateobj.getMinutes()).slice(-2);

    var full_date = year + '-' + month + '-' + date + ' ' + hours + ':' + minutes;

    return full_date;

}
