(function ($) {
    "use strict";
    $(function () {
        var regex = {
            phone: /^1?(\d{3})(\d{3})(\d{4})$/
        };

        var is_ready_to_submit = function () {
            var form_product = $('.cart').last();
            if (form_product.data('tc_validator')) {
                if (form_product.tc_valid() === true) {
                    return true;
                } else {
                    form_product.tc_validate().form();
                    return false;
                }
            }
            return true;
        };
        
        function angelleye_populate_form(frm, data) {   
            $.each(data, function(key, value){  
                var $ctrl = $('[name='+key+']', frm); 
                if($ctrl.is('select')){
                    $("option",$ctrl).each(function(){
                        if (this.value==value) { this.selected=true; }
                    });
                }
                else {
                    switch($ctrl.attr("type"))  
                    {  
                        case "text" :   case "hidden":  case "textarea":  
                            $ctrl.val(value);   
                            break;   
                        case "radio" : case "checkbox":   
                            $ctrl.each(function(){
                               if($(this).attr('value') == value) {  $(this).attr("checked",value); } });   
                            break;
                    } 
                } 
            });  
         };

        $(document).ready(function () {
            
            if( offers_for_woocommerce_js_params.angelleye_post_data !== '' ) {
                angelleye_populate_form($('.cart'), offers_for_woocommerce_js_params.angelleye_post_data);
            }

            $('.ofwc_no_price_product').remove();

            $(".variations_form").on("hide_variation", function () {
                if ($('.single_add_to_cart_button').hasClass('disabled')) {
                    $('.single_offer_button').addClass('disabled');
                } else {
                    $('.single_offer_button').removeClass('disabled');
                }
            });

            $(".variations_form").on("show_variation", function () {
                if ($('.single_add_to_cart_button').hasClass('disabled')) {
                    $('.single_offer_button').addClass('disabled');
                } else {
                    $('.single_offer_button').removeClass('disabled');
                }
            });

            var get = [];
            location.search.replace('?', '').split('&').forEach(function (val) {
                var split = val.split("=", 2);
                get[split[0]] = split[1];
            });

            if (get["aewcobtn"] && !($("div.woocommerce-message").length > 0)) {
               
                if($('.tm-epo-counter').length > 0) {
                   // return false;
                }
                if (!$(".woocommerce-error:first").hasClass('aeofwc-woocommerce-error'))
                {
                    // do nothing
                } else
                {
                    if (offers_for_woocommerce_js_params.is_product_type_variable === 'true') {
                        var variationId = $("input[name='variation_id']").val();
                        if (variationId > 0) {
                            angelleyeOpenMakeOfferForm(false);
                        } else {
                            alert(offers_for_woocommerce_js_params.i18n_make_a_selection_text);
                        }
                    } else {
                        //var productId = $("button[name='add-to-cart']").val();
                        if ($("input[name='add-to-cart']").val() > 0 || $("button[name='add-to-cart']").val() > 0) {
                            angelleyeOpenMakeOfferForm(false);
                        } else {
                            alert(offers_for_woocommerce_js_params.i18n_unavailable_text);
                        }
                    }
                }
            }

            var makeOfferBtnPosition = $('#offers-for-woocommerce-add-to-cart-wrap').attr('data-ofwc-position');
            var makeOfferBtnhtml = $('.single_variation_wrap_angelleye.ofwc_offer_tab_form_wrap').html();

            // after price
            if (makeOfferBtnhtml != null && makeOfferBtnhtml.length > 0) {
                if (makeOfferBtnPosition == 'after_price') {
                    $('.product .summary .price:first').after("<div class='offers-for-woocommerce-add-to-cart-wrap ofwc-no-float'>" + makeOfferBtnhtml + "");
                }
            }

            $(".offers-for-woocommerce-make-offer-button-single-product").click(function () {
                if (is_ready_to_submit() === false) {
                    return false;
                }
                if (offers_for_woocommerce_js_params.is_product_type_variable === 'true') {
                    if (offers_for_woocommerce_js_params.is_woo_variations_table_installed === '1') {
                        var variationId = $(this).parents("tr").find("input[name='variation_id']").val();
                        $("input[name='offer_variations_table_variation_id']").val(variationId);
                    } else {
                        var variationId = $("input[name='variation_id']").val();
                    }

                    if (variationId > 0) {
                        angelleyeOpenMakeOfferForm(true);
                    } else {
                        alert(offers_for_woocommerce_js_params.i18n_make_a_selection_text);
                    }
                } else {
                    //var productId = $("input[name='add-to-cart']").val();                    
                    if ($("input[name='add-to-cart']").val() > 0 || $("button[name='add-to-cart']").val() > 0) {
                        angelleyeOpenMakeOfferForm(true);
                    } else {
                        alert(offers_for_woocommerce_js_params.i18n_unavailable_text);
                    }
                }
            });

            $("#lightbox_custom_ofwc_offer_form_close_btn, #aeofwc-close-lightbox-link").on('click', function ()
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
                        aForm: false,
                        aSep: offers_for_woocommerce_js_params.ofw_public_js_thousand_separator,
                        aDec: offers_for_woocommerce_js_params.ofw_public_js_decimal_separator,
                    }
            );

            $('#woocommerce-make-offer-form-price-each').autoNumeric('init',
                    {
                        aForm: false, /* Controls if default values are formatted on page ready (load) */
                        aSep: offers_for_woocommerce_js_params.ofw_public_js_thousand_separator, /* Thousand separator */
                        aDec: offers_for_woocommerce_js_params.ofw_public_js_decimal_separator, /* Decimal separator */
                        //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                        vMin: '0.00', /* Enter the minimum value allowed */
                        lZero: 'allow', /* Controls if leading zeros are allowed */
                        wEmpty: 'sign', /* controls input display behavior. */
                        mDec: offers_for_woocommerce_js_params.ofw_public_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                        aSign: '',
                    }
            );

            $('#woocommerce-make-offer-form-total').autoNumeric('init',
                    {
                        aForm: false, /* Controls if default values are formatted on page ready (load) */
                        aSep: offers_for_woocommerce_js_params.ofw_public_js_thousand_separator, /* Thousand separator */
                        aDec: offers_for_woocommerce_js_params.ofw_public_js_decimal_separator, /* Decimal separator */
                        //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                        lZero: 'allow', /* Controls if leading zeros are allowed */
                        wEmpty: 'zero', /* controls input display behavior. */
                        mDec: offers_for_woocommerce_js_params.ofw_public_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                    }
            );

            $('#woocommerce-make-offer-form').find(':submit').removeAttr('disabled', 'disabled');

            (function ($) {
                $.fn.money_field = function (opts) {
                    var defaults = {width: null, symbol: 'dddd'};
                    var opts = $.extend(defaults, opts);
                    return this.each(function () {
                        if (opts.width)
                            $(this).css('width', opts.width + 'px');
                        $(this).wrap("<div class='angelleye-input-group'>").before("<span class='angelleye-input-group-addon'>" + opts.symbol + "</span>");
                    });
                };
            })(jQuery);

            /* Submit offer form */
            $("form[name='woocommerce-make-offer-form']").submit(function ()
            {
                if (is_ready_to_submit() === false) {
                    return false;
                }
                $('.tab_custom_ofwc_offer_tab_alt_message_2').hide();
                var offerCheckMinValuesPassed = true;

                if ($('#woocommerce-make-offer-form-price-each').autoNumeric('get') == '0')
                {
                    $('#woocommerce-make-offer-form-price-each').autoNumeric('set', '');
                    $('#woocommerce-make-offer-form-price-each').autoNumeric('update',
                            {
                                aForm: false, /* Controls if default values are formatted on page ready (load) */
                                aSep: offers_for_woocommerce_js_params.ofw_public_js_thousand_separator, /* Thousand separator */
                                aDec: offers_for_woocommerce_js_params.ofw_public_js_decimal_separator, /* Decimal separator */
                                //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                                vMin: '0.00', /* Enter the minimum value allowed */
                                lZero: 'allow', /* Controls if leading zeros are allowed */
                                wEmpty: 'sign', /* controls input display behavior. */
                                mDec: offers_for_woocommerce_js_params.ofw_public_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                                aSign: '',
                            }
                    );
                    offerCheckMinValuesPassed = false;
                }

                if ($('#woocommerce-make-offer-form-quantity').autoNumeric('get') == '0')
                {
                    $('#woocommerce-make-offer-form-quantity').autoNumeric('set', '');
                    $('#woocommerce-make-offer-form-quantity').autoNumeric('update',
                            {
                                vMin: '0',
                                mDec: '0',
                                lZero: 'deny',
                                aForm: false,
                                aSep: offers_for_woocommerce_js_params.ofw_public_js_thousand_separator,
                                aDec: offers_for_woocommerce_js_params.ofw_public_js_decimal_separator,
                            }
                    );
                    offerCheckMinValuesPassed = false;
                }

                if (offerCheckMinValuesPassed === false)
                {
                    return false;
                }

                var parentOfferId = $("input[name='parent_offer_id']").val();
                var parentOfferUid = $("input[name='parent_offer_uid']").val();

                var offerProductId = '';
                var offerVariationId = '';
                var offerProductPrice = '';
                if (offers_for_woocommerce_js_params.is_woo_variations_table_installed === '1') {
                    offerVariationId = $("input[name='offer_variations_table_variation_id']").val();
                }
                if ($('input[name="variation_id"]').val() && $('input[name="variation_id"]').val() !== 0) {
                    offerVariationId = $('input[name="variation_id"]').val();
                }

                /* old WC version condition start*/
                if ($("input[name='add-to-cart']").val() > 0) {
                    var offerProductId = $("input[name='add-to-cart']").val();
                }
                /* old WC version condition end */

                /* New WC version condition start*/
                if ($("button[name='add-to-cart']").val() > 0) {
                    var offerProductId = $("button[name='add-to-cart']").val();
                }
                /* New WC version condition end */

                /* Recognise product type and Product price  : start */
                var productType = $("input[name='ofwc_hidden_price_type']").val();
                if (productType == 'variable') {
                    var variationPrice = $('.woocommerce-variation-price .amount').text().replace(/ /g, '');
                    if (variationPrice === '') {
                        offerProductPrice = jQuery('.summary .price .woocommerce-Price-amount').text();
                    } else {
                        offerProductPrice = variationPrice;
                    }
                } else if (productType == 'simple') {
                    offerProductPrice = $('.summary  .price .amount').text();
                } else if (productType == 'sale_product') {
                    offerProductPrice = $('.summary  .price ins').text();
                } else if (productType == 'grouped') {
                    offerProductPrice = $('.summary  .price .amount').text();
                } else if (productType == 'external') {
                    offerProductPrice = $('.summary  .price .amount').text();
                } else {
                    offerProductPrice = '';
                }

                /* End */

                var join_our_mailing_list = "no";

                if ($("#join_our_mailing_list").length > 0) {
                    if ($('#join_our_mailing_list').attr('checked')) {
                        join_our_mailing_list = "yes";
                    }
                }

                var offerQuantity = $("input[name='offer_quantity']").autoNumeric('get');
                var offerPriceEach = $("input[name='offer_price_each']").autoNumeric('get');
                var offerTotal = $("input[name='offer_total']").val();
                var offerForm = $('#woocommerce-make-offer-form');

                if (offerProductId != '')
                {
                    // disable submit button
                    $(offerForm).find(':submit').attr('disabled', 'disabled');

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
                    post_data_array.push({name: 'offer_product_price', value: offerProductPrice});
                    post_data_array.push({name: 'offer_total', value: offerTotal});
                    post_data_array.push({name: 'offer_variation_id', value: offerVariationId});
                    post_data_array.push({name: 'parent_offer_id', value: parentOfferId});
                    post_data_array.push({name: 'parent_offer_uid', value: parentOfferUid});
                    post_data_array.push({name: 'offer_quantity', value: offerQuantity});
                    post_data_array.push({name: 'offer_price_each', value: offerPriceEach});
                    post_data_array.push({name: 'join_our_mailing_list', value: join_our_mailing_list});
                    var product_addon_array_js = [];
                    jQuery("div.product-addon").each(function (key, index) {
                        var group_name = jQuery.trim(jQuery(this).find('h3.addon-name').text());
                        var input_tag = jQuery(this).find(":input[name^='addon-']");

                        input_tag.each(function () {
                            if (jQuery(this).is(':checkbox') || jQuery(this).is(':radio')) {
                                if (jQuery(this).is(':checked')) {
                                    var label_text = jQuery(this).closest('label').text().substr(0, jQuery(this).closest('label').text().indexOf('('));
                                    product_addon_array_js.push({position: key, group: group_name, label: jQuery.trim(label_text), value: jQuery(this).val(), price: jQuery(this).attr('data-raw-price'), type: jQuery(this).attr('type')});
                                }
                            }
                            if (jQuery(this).is('textarea')) {
                                if (jQuery(this).val() !== '') {
                                    var label_text = jQuery(this).parent().find('label').text().substr(0, jQuery(this).parent().find('label').text().indexOf('('));
                                    product_addon_array_js.push({position: key, group: group_name, label: jQuery.trim(label_text), value: jQuery(this).val(), price: jQuery(this).attr('data-raw-price'), type: "custom_textarea"});
                                }
                            }
                            if (jQuery(this).hasClass('input-text addon addon-custom-price')) {
                                if (jQuery(this).val() !== '') {
                                    var label_text = jQuery(this).parent().find('label').text();
                                    product_addon_array_js.push({position: key, group: group_name, label: jQuery.trim(label_text), value: jQuery(this).val(), price: jQuery(this).attr('data-raw-price'), type: "custom_price"});
                                }
                            }
                            if (jQuery(this).hasClass('input-text addon addon-input_multiplier')) {
                                if (jQuery(this).val() !== '') {
                                    var label_text = jQuery(this).parent().find('label').text();
                                    product_addon_array_js.push({position: key, group: group_name, label: jQuery.trim(label_text), value: jQuery(this).val(), price: jQuery(this).attr('data-raw-price'), type: "input_multiplier"});
                                }
                            }
                            if (jQuery(this).hasClass('addon addon-select')) {
                                if (jQuery(this).val() !== '') {
                                    var label_text = jQuery(this).find(":selected").text().substr(0, jQuery(this).find(":selected").text().indexOf('('));
                                    product_addon_array_js.push({position: key, group: group_name, label: jQuery.trim(label_text), value: jQuery(this).val(), price: jQuery(this).find(":selected").attr('data-raw-price'), type: "select"});
                                }
                            }
                            if (jQuery(this).hasClass('input-text addon addon-custom')) {
                                if (jQuery(this).val() !== '') {
                                    var label_text = jQuery(this).parent().find('label').text();
                                    product_addon_array_js.push({position: key, group: group_name, label: jQuery.trim(label_text), value: jQuery(this).val(), price: jQuery(this).attr('data-raw-price'), type: "custom"});
                                }
                            }
                        });

                    });
                    if (product_addon_array_js.length > 0) {
                        var updatedPrice = jQuery(".product-addon-totals .amount").last().text();
                        if (updatedPrice !== '') {
                            offerProductPrice = updatedPrice;
                        }
                        post_data_array.push({name: 'offer_product_price', value: offerProductPrice});
                    }

                    var data = {
                        'attributes': {}
                    };

                    var form = $('form.cart');
                    var field_pairs = form.serializeArray();

                    for (var i = 0; i < field_pairs.length; i++) {

                        if (-1 !== field_pairs[ i ].name.indexOf('attribute_')) {
                            data.attributes[ field_pairs[ i ].name ] = field_pairs[ i ].value;
                            continue;
                        }

                        data[ field_pairs[ i ].name ] = field_pairs[ i ].value;
                    }

                    post_data_array.push({product_data: data});
                    post_data_array.push({product_addon_array: product_addon_array_js});
                    var data_make_offer = {
                        action: 'new_offer_form_submit',
                        security: offers_for_woocommerce_js_params.offers_for_woocommerce_params_nonce,
                        value: post_data_array
                    };

                    // fire off the request
                    var request = $.ajax({
                        url: offers_for_woocommerce_js_params.ajax_url,
                        type: "post",
                        dataType: 'json',
                        data: data_make_offer
                    });

                    // callback handler that will be called on success
                    request.done(function (response, textStatus, jqXHR) {
                        if (200 === request.status) {
                            var myObject = JSON.parse(request.responseText);
                            var responseStatus = myObject['statusmsg'];
                            var responseStatusDetail = myObject['statusmsgDetail'];

                            if (responseStatus == 'failed')
                            {
                                //console.log('failed');
                                // Hide loader image
                                $('#offer-submit-loader').hide();
                                // Show error message DIV
                                $('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');
                                $(offerForm).find(':submit').removeAttr('disabled', 'disabled');
                            } else if (responseStatus == 'failed-custom')
                            {
                                //console.log('failed-custom-msg');
                                // Hide loader image
                                $('#offer-submit-loader').hide();
                                // Show error message DIV
                                $('#tab_custom_ofwc_offer_tab_alt_message_custom ul #alt-message-custom').html("<strong>Error: </strong>" + responseStatusDetail);
                                $('#tab_custom_ofwc_offer_tab_alt_message_custom').slideToggle('fast');
                                $(offerForm).find(':submit').removeAttr('disabled', 'disabled');
                            } else if (responseStatus == 'failed-spam')
                            {
                                //console.log('failed-custom-msg');
                                // Hide loader image
                                $('#offer-submit-loader').hide();
                                // Show error message DIV
                                $('#tab_custom_ofwc_offer_tab_alt_message_custom ul #alt-message-custom').html("<strong>Error: </strong>" + responseStatusDetail);
                                $('#tab_custom_ofwc_offer_tab_alt_message_custom').slideToggle('fast');
                                $(offerForm).find(':submit').removeAttr('disabled', 'disabled');
                                $('#tab_custom_ofwc_offer_tab_inner fieldset').hide();
                            } else if (responseStatus == 'accepted-offer') {
                                window.location = decodeURI(myObject['redirect']);
                            } else
                            {
                                // SUCCESS
                                // Hide loader image
                                $('#offer-submit-loader').hide();
                                $(offerForm).find(':submit').removeAttr('disabled', 'disabled');
                                $('#tab_custom_ofwc_offer_tab_inner fieldset').hide();
                                $('#tab_custom_ofwc_offer_tab_alt_message_success').slideToggle('fast');
                                if ($('#lightbox_custom_ofwc_offer_form').length) {
                                    $('#aeofwc-popup-counter-box').show();
                                    var count = 5;
                                    var counter = setInterval(function () {
                                        if (count <= 0) {
                                            clearInterval(counter);
                                            $('#aeofwc-close-lightbox-link').trigger('click');
                                        }
                                        $('#aeofwc-lightbox-message-counter').html(count);
                                        count -= 1;
                                    }, 500);
                                }

                            }

                        } else {
                            //console.log('error received');
                            //alert('Timeout has likely occured, please refresh this page to reinstate your session');
                            // Hide loader image
                            $('#offer-submit-loader').hide();
                            $('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');
                            $(offerForm).find(':submit').removeAttr('disabled', 'disabled');
                        }
                    });

                    // callback handler that will be called on failure
                    request.fail(function (jqXHR, textStatus, errorThrown) {
                        // log the error to the console
                        // Hide loader image
                        $('#offer-submit-loader').hide();
                        $('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');
                    });
                } else
                {
                    // Hide loader image
                    $('#offer-submit-loader').hide();
                    $('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');
                }
                return false;
            });
        });

        function angelleyeOpenMakeOfferForm(is_product_page) {
            if (is_ready_to_submit() === false) {
                return false;
            }
            if ($(".offers-for-woocommerce-make-offer-button-single-product").hasClass("offers-for-woocommerce-make-offer-button-single-product-lightbox"))
            {
                if ($("#lightbox_custom_ofwc_offer_form").hasClass('active'))
                {
                    $("#lightbox_custom_ofwc_offer_form").hide();
                    $("#lightbox_custom_ofwc_offer_form").removeClass('active');
                    $("#lightbox_custom_ofwc_offer_form_close_btn").hide();
                } else
                {
                    $("#lightbox_custom_ofwc_offer_form").addClass('active');
                    $("#lightbox_custom_ofwc_offer_form").show();
                    $("#lightbox_custom_ofwc_offer_form_close_btn").show();
                }

                if ($("#woocommerce-make-offer-form-quantity").attr('type') == 'hidden')
                {
                    $("#woocommerce-make-offer-form-price-each").focus();
                } else
                {
                    $("#woocommerce-make-offer-form-quantity").focus();
                }
            } else
            {
                if ($('.woocommerce-tabs .tabs li.tab_custom_ofwc_offer_tab').length > 0) {
                    $(".woocommerce-tabs .tabs li.tab_custom_ofwc_offer_tab a").click();
                }

                $('.tab_custom_ofwc_offer_tab').click();

                var targetTab = $(".tab_custom_ofwc_offer_tab");
                if ($(".tab_custom_ofwc_offer_tab").length > 0) {
                    setTimeout(function () {
                        $('html, body').animate({
                            scrollTop: $(".tab_custom_ofwc_offer_tab").offset().top - 150
                        }, 'fast');
                        if ($("#woocommerce-make-offer-form-quantity").attr('type') == 'hidden')
                        {
                            $("#woocommerce-make-offer-form-price-each").focus();
                        } else
                        {
                            $("#woocommerce-make-offer-form-quantity").focus();
                        }
                    }, 300);
                }
            }

            return false;
        }

        $(window).load(function () {
            if ($(".offers-for-woocommerce-make-offer-button-single-product").hasClass("offers-for-woocommerce-make-offer-button-single-product-lightbox"))
            {
                $("#aeofwc-close-lightbox-link").css('display', 'block');
            }
            var variantDisplay = $('.single_variation_wrap').css('display');
            if ($('body.woocommerce.single-product #content div.product .summary ').hasClass('product-type-variable') && variantDisplay != 'block' && offers_for_woocommerce_js_params.is_woo_variations_table_installed === '0')
            {
                if ($(".offers-for-woocommerce-make-offer-button-single-product").hasClass("offers-for-woocommerce-make-offer-button-single-product-lightbox"))
                {
                    $("#lightbox_custom_ofwc_offer_form").hide();
                    $("#lightbox_custom_ofwc_offer_form").removeClass('active');
                    $("#lightbox_custom_ofwc_offer_form_close_btn").hide();
                } else
                {
                    $('#tab_custom_ofwc_offer_tab_inner').hide();
                }
            }
        });

        // offer quantity input keyup
        $('#woocommerce-make-offer-form-quantity').keyup(function () {
            updateTotal();
        });

        // offer price each input keyup
        $('#woocommerce-make-offer-form-price-each').keyup(function () {
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
                if (!currencySymbol) {
                    currencySymbol = '$';
                }

                $('#woocommerce-make-offer-form-total').autoNumeric('set', theTotal);

                $('#woocommerce-make-offer-form-total').autoNumeric('update',
                        {
                            aForm: false, /* Controls if default values are formatted on page ready (load) */
                            aSep: offers_for_woocommerce_js_params.ofw_public_js_thousand_separator, /* Thousand separator */
                            aDec: offers_for_woocommerce_js_params.ofw_public_js_decimal_separator, /* Decimal separator */
                            //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                            lZero: 'allow', /* Controls if leading zeros are allowed */
                            wEmpty: 'zero', /* controls input display behavior. */
                            mDec: offers_for_woocommerce_js_params.ofw_public_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                        }
                );

            }
        };
    });
}(jQuery));
