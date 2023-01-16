(function ( $ ) {
    "use strict";

    $(function () {
        const change_trigger = new Event("change");
            $('.chosen-select').chosen({width: "100%"});

            $('#general_setting_default_expire_days').autoNumerics('init', {
                vMin: '0',
                mDec: '0',
                lZero: 'allow',
                aForm: false
            });

            $('#ofwc-bulk-action-target-where-price-value').autoNumerics('init', {
                aForm: false,      /* Controls if default values are formatted on page ready (load) */
                aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                /*pSign : 'p',    // Placement of the currency : p = left and s = right */
                vMin : '0.00',    /* Enter the minimum value allowed */
                lZero: 'allow',   /* Controls if leading zeros are allowed */
                wEmpty: 'sign',   /* controls input display behavior. */
                mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
            });

            $('#ofw-bulk-tool-action-target-where-price-value').autoNumerics('init', {
                aForm: false,      /* Controls if default values are formatted on page ready (load) */
                aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                /*pSign : 'p',    // Placement of the currency : p = left and s = right */
                vMin : '0.00',    /* Enter the minimum value allowed */
                lZero: 'allow',   /* Controls if leading zeros are allowed */
                wEmpty: 'sign',   /* controls input display behavior. */
                mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
            });

            $('input[name="ofw_minimum_offer_price_target_where_price_value"]').autoNumerics('init', {
                aForm: false,      /* Controls if default values are formatted on page ready (load) */
                aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                /*pSign : 'p',    // Placement of the currency : p = left and s = right */
                vMin : '0.00',    /* Enter the minimum value allowed */
                lZero: 'allow',   /* Controls if leading zeros are allowed */
                wEmpty: 'sign',   /* controls input display behavior. */
                mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
            });


            /* Minimum Offer Price bulk toogle */
            if( document.getElementById('ofw-minimum-offer-price-target-type') !== null ){

                document.getElementById('ofw-minimum-offer-price-target-type').addEventListener('change' ,function(){
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-category').style.display = 'none';
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-product-type').style.display = 'none';
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').style.display = 'none';
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').style.display = 'none';
                    document.getElementById('ofw-minimum-offer-price-target-where-category').removeAttribute('required');
                    document.getElementById('ofw-minimum-offer-price-target-where-product-type').removeAttribute('required');
                    document.getElementById('ofw-minimum-offer-price-target-where-price-value').removeAttribute('required');
                    document.getElementById('ofw-minimum-offer-price-target-where-stock-value').removeAttribute('required');

                    if( this.value === 'where' ) {
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-type').style.display = 'block';
                        document.getElementById('ofw-minimum-offer-price-target-where-type').setAttribute('required', 'required');
                    } else {
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-type').style.display = 'none';
                        document.getElementById('ofw-minimum-offer-price-target-where-type').removeAttribute('required');

                    }

                });
            }
        /* change target where type -- toggle categories/value inputs */
            if( document.getElementById('ofw-minimum-offer-price-target-where-type') !== null ) {
                document.getElementById('ofw-minimum-offer-price-target-where-type').addEventListener('change', function () {
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-category').style.display = 'none';
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-product-type').style.display = 'none';
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').style.display = 'none';
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').style.display = 'none';
                    document.getElementById('ofw-minimum-offer-price-target-where-category').removeAttribute('required');
                    document.getElementById('ofw-minimum-offer-price-target-where-product-type').removeAttribute('required');
                    document.getElementById('ofw-minimum-offer-price-target-where-price-value').removeAttribute('required');
                    document.getElementById('ofw-minimum-offer-price-target-where-stock-value').removeAttribute('required');

                    if (this.value === 'category') {
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-category').style.display = 'block';
                        document.getElementById('ofw-minimum-offer-price-target-where-category').setAttribute('required', 'required');

                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-product-type').style.display = 'none';
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').style.display = 'none';
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').style.display = 'none';
                        document.getElementById('ofw-minimum-offer-price-target-where-product-type').removeAttribute('required');
                        document.getElementById('ofw-minimum-offer-price-target-where-price-value').removeAttribute('required');
                        document.getElementById('ofw-minimum-offer-price-target-where-stock-value').removeAttribute('required');
                    } else if (this.value === 'product_type') {
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-product-type').style.display = 'block';
                        document.getElementById('ofw-minimum-offer-price-target-where-product-type').setAttribute('required', 'required');

                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-category').style.display = 'none';
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').style.display = 'none';
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').style.display = 'none';
                        document.getElementById('ofw-minimum-offer-price-target-where-category').removeAttribute('required');
                        document.getElementById('ofw-minimum-offer-price-target-where-price-value').removeAttribute('required');
                        document.getElementById('ofw-minimum-offer-price-target-where-stock-value').removeAttribute('required');
                    } else {
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-category').style.display = 'none';
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-product-type').style.display = 'none';
                        document.getElementById('ofw-minimum-offer-price-target-where-category').removeAttribute('required');
                        document.getElementById('ofw-minimum-offer-price-target-where-product-type').removeAttribute('required');

                        if (this.value === 'price_greater' || this.value === 'price_less') {
                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').style.display = 'block';
                            document.getElementById('ofw-minimum-offer-price-target-where-price-value').setAttribute('required', 'required');

                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').style.display = 'none';
                            document.getElementById('ofw-minimum-offer-price-target-where-stock-value').removeAttribute('required');
                        } else if (this.value === 'stock_greater' || this.value === 'stock_less') {
                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').style.display = 'none';
                            document.getElementById('ofw-minimum-offer-price-target-where-price-value').removeAttribute('required');

                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').style.display = 'block';
                            document.getElementById('ofw-minimum-offer-price-target-where-stock-value').setAttribute('required', 'required');
                        } else {
                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').style.display = 'none';
                            document.getElementById('ofw-minimum-offer-price-target-where-price-value').removeAttribute('required');

                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').style.display = 'none';
                            document.getElementById('ofw-minimum-offer-price-target-where-stock-value').removeAttribute('required');
                        }
                    }
                });
            }

            if(document.getElementById('ofw-minimum-offer-price-type') !== null) {
                document.getElementById('ofw-minimum-offer-price-type').addEventListener('change', function () {
                    if ('percentage' === this.value) {
                        document.getElementById('ae_offer_type_chnage_lable').innerHTML = 'Minimum Offer Percentage';
                    } else {
                        document.getElementById('ae_offer_type_chnage_lable').innerHTML = 'Minimum Offer Price';
                    }
                });
            }

            if(document.getElementById('ofw-minimum-offer-action-type') !== null) {
                document.getElementById('ofw-minimum-offer-action-type').addEventListener('change', function () {
                    if ('ofwc_minimum_offer_price_enable' === this.value) {
                        document.getElementsByClassName('ofwc-minimun-offer-price-input').style.display = 'block';
                        document.getElementsByClassName('ofwc-minimun-offer-price-input').setAttribute('required', 'required');

                        document.getElementsByClassName('ofw-minimum-offer-price-type').style.display = 'block';
                        document.getElementsByClassName('ofw-minimum-offer-price-type').setAttribute('required', 'required');
                    } else {
                        document.getElementsByClassName('ofwc-minimun-offer-price-input').style.display = 'none';
                        document.getElementsByClassName('ofwc-minimun-offer-price-input').removeAttribute('required');

                        document.getElementsByClassName('ofw-minimum-offer-price-type').style.display = 'none';
                        document.getElementsByClassName('ofw-minimum-offer-price-type').removeAttribute('required');
                    }
                });
            }

            /* AJAX - Bulk enable/disable tool */
            if(document.getElementById('ofw_tool_minimun_offer_price_form') !== null) {
                document.getElementById('ofw_tool_minimun_offer_price_form').addEventListener('submit', function () {
                    /* show processing status */
                    document.getElementById('ofw-minimum-offer-price-tool-submit').setAttribute('disabled', 'disabled');
                    document.getElementById('ofw-minimum-offer-price-tool-submit').classList.remove('button-primary');
                    document.getElementById("ofw-minimum-offer-price-tool-submit").innerHTML = '<i class="ofwc-spinner"></i> Processing, please wait...';
                    document.querySelectorAll('#ofw-minimum-offer-price-tool-submit i.spinner').style.display = 'block';

                    var minimum_price = document.getElementById('minimun-offer-price-input').value;
                    var price_type = document.getElementById('ofw-minimum-offer-price-type').value;
                    var actionType = document.getElementById('ofw-minimum-offer-action-type').value;
                    var actionTargetType = document.getElementById('ofw-minimum-offer-price-target-type').value;
                    var actionTargetWhereType = document.getElementById('ofw-minimum-offer-price-target-where-type').value;
                    var actionTargetWhereCategory = document.getElementById('ofw-minimum-offer-price-target-where-category').value;
                    var actionTargetWhereProductType = document.getElementById('ofw-minimum-offer-price-target-where-product-type').value;
                    var actionTargetWherePriceValue = document.getElementById('ofw-minimum-offer-price-target-where-price-value').value;
                    var actionTargetWhereStockValue = document.getElementById('ofw-minimum-offer-price-target-where-stock-value').value;
                    var meta_value = 'ofwc_minimum_offer_price';
                    var adminToolSetMinimumOfferPriceNonce = document.getElementById('adminToolSetMinimumOfferPriceNonce').value;
                    var data = {
                        'action': 'adminToolSetMinimumOfferPrice',
                        'actionType': actionType,
                        'minimumPrice': minimum_price,
                        'priceType': price_type,
                        'actionTargetType': actionTargetType,
                        'actionTargetWhereType': actionTargetWhereType,
                        'actionTargetWhereCategory': actionTargetWhereCategory,
                        'actionTargetWhereProductType': actionTargetWhereProductType,
                        'actionTargetWherePriceValue': actionTargetWherePriceValue,
                        'actionTargetWhereStockValue': actionTargetWhereStockValue,
                        'ofw_meta_key_value': meta_value,
                        'security': adminToolSetMinimumOfferPriceNonce
                    };

                    /* post it */
                    $.post(ajaxurl, data, function (response) {
                        if ('failed' !== response) {
                            var redirectUrl = response;
                            top.location.replace(redirectUrl);
                            return true;
                        } else {
                            alert('Error updating records.');
                            return false;
                        }
                    });

                    /*End Post*/
                    return false;
                });
            }
            
            /* change target type -- toggle where input */
            if( document.getElementById('ofwc-bulk-action-target-type') !== null ) {
                document.getElementById('ofwc-bulk-action-target-type').addEventListener('change', function () {
                    /* show processing status */
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-category').style.display = 'none';
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-product-type').style.display = 'none';
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').style.display = 'none';
                    document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').style.display = 'none';

                    document.getElementById('ofwc-bulk-action-target-where-category').removeAttribute('required');
                    document.getElementById('ofwc-bulk-action-target-where-product-type').removeAttribute('required');
                    document.getElementById('ofwc-bulk-action-target-where-price-value').removeAttribute('required');
                    document.getElementById('ofwc-bulk-action-target-where-stock-value').removeAttribute('required');

                    if (this.value === 'where') {
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-type').style.display = 'block';
                        document.getElementById('ofwc-bulk-action-target-where-type').setAttribute('required', 'required');
                    } else {
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-type').style.display = 'none';
                        document.getElementById('ofwc-bulk-action-target-where-type').removeAttribute('required');
                    }
                });
            }

            /* change target where type -- toggle categories/value inputs */
            if( document.getElementById('ofwc-bulk-action-target-where-type') !== null ) {
                document.getElementById('ofwc-bulk-action-target-where-type').addEventListener('change', function () {
                    /* show processing status */
                    if (this.value === 'category') {
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-category').style.display = 'block';
                        document.getElementById('ofwc-bulk-action-target-where-category').setAttribute('required', 'required');

                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-product-type').style.display = 'none';
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').style.display = 'none';
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').style.display = 'none';
                        document.getElementById('ofwc-bulk-action-target-where-product-type').removeAttribute('required');
                        document.getElementById('ofwc-bulk-action-target-where-price-value').removeAttribute('required');
                        document.getElementById('ofwc-bulk-action-target-where-stock-value').removeAttribute('required');

                    } else if (this.value === 'product_type') {
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-product-type').style.display = 'block';
                        document.getElementById('ofwc-bulk-action-target-where-product-type').setAttribute('required', 'required');

                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-category').style.display = 'none';
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').style.display = 'none';
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').style.display = 'none';
                        document.getElementById('ofwc-bulk-action-target-where-category').removeAttribute('required');
                        document.getElementById('ofwc-bulk-action-target-where-price-value').removeAttribute('required');
                        document.getElementById('ofwc-bulk-action-target-where-stock-value').removeAttribute('required');
                    } else {
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-category').style.display = 'none';
                        document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-product-type').style.display = 'none';
                        document.getElementById('ofwc-bulk-action-target-where-category').removeAttribute('required');
                        document.getElementById('ofwc-bulk-action-target-where-product-type').removeAttribute('required');

                        if (this.value === 'price_greater' || this.value === 'price_less') {
                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').style.display = 'block';
                            document.getElementById('ofwc-bulk-action-target-where-price-value').setAttribute('required', 'required');

                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').style.display = 'none';
                            document.getElementById('ofwc-bulk-action-target-where-stock-value').removeAttribute('required');
                        } else if (this.value === 'stock_greater' || this.value === 'stock_less') {
                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').style.display = 'none';
                            document.getElementById('ofwc-bulk-action-target-where-price-value').removeAttribute('required');

                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').style.display = 'block';
                            document.getElementById('ofwc-bulk-action-target-where-stock-value').setAttribute('required', 'required');
                        } else {
                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').style.display = 'none';
                            document.getElementById('ofwc-bulk-action-target-where-price-value').removeAttribute('required');

                            document.querySelectorAll('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').style.display = 'none';
                            document.getElementById('ofwc-bulk-action-target-where-stock-value').removeAttribute('required');
                        }
                    }
                });
            }

            /* AJAX - Bulk enable/disable tool */
            if( document.getElementById('woocommerce_offers_options_form_bulk_tool_enable_offers') !== null ) {
                document.getElementById('woocommerce_offers_options_form_bulk_tool_enable_offers').addEventListener('submit', function () {
                    /* show processing status */
                    document.getElementById('bulk-enable-tool-submit').setAttribute('disabled', 'disabled');
                    document.getElementById('bulk-enable-tool-submit').classList.remove('button-primary');
                    document.getElementById('bulk-enable-tool-submit').innerHTML = '<i class="ofwc-spinner"></i> Processing, please wait...';
                    document.querySelectorAll('#bulk-enable-tool-submit i.spinner').style.display = 'block';

                    var actionType = document.getElementById('ofwc-bulk-action-type').value;
                    var actionTargetType = document.getElementById('ofwc-bulk-action-target-type').value;
                    var actionTargetWhereType = document.getElementById('ofwc-bulk-action-target-where-type').value;
                    var actionTargetWhereCategory = document.getElementById('ofwc-bulk-action-target-where-category').value;
                    var actionTargetWhereProductType = document.getElementById('ofwc-bulk-action-target-where-product-type').value;
                    var actionTargetWherePriceValue = document.getElementById('ofwc-bulk-action-target-where-price-value').value;
                    var actionTargetWhereStockValue = document.getElementById('ofwc-bulk-action-target-where-stock-value').value;
                    var meta_value = 'offers_for_woocommerce_enabled';

                    if (actionType === 'enable_onexit' || actionType === 'disable_onexit') {
                        meta_value = 'offers_for_woocommerce_onexit_only';
                    }
                    var woocommerce_offers_options_form_bulk_tool_enable_offersNonce = document.getElementById('woocommerce_offers_options_form_bulk_tool_enable_offersNonce').value;
                    var data = {
                        'action': 'adminToolBulkEnableDisable',
                        'actionType': actionType,
                        'actionTargetType': actionTargetType,
                        'actionTargetWhereType': actionTargetWhereType,
                        'actionTargetWhereCategory': actionTargetWhereCategory,
                        'actionTargetWhereProductType': actionTargetWhereProductType,
                        'actionTargetWherePriceValue': actionTargetWherePriceValue,
                        'actionTargetWhereStockValue': actionTargetWhereStockValue,
                        'ofw_meta_key_value': meta_value,
                        'security': woocommerce_offers_options_form_bulk_tool_enable_offersNonce
                    };

                    /* post it */
                    $.post(ajaxurl, data, function (response) {
                        if ('failed' !== response) {
                            var redirectUrl = response;
                            top.location.replace(redirectUrl);
                            return true;
                        } else {
                            alert('Error updating records.');
                            return false;
                        }
                    });
                    /*End Post*/
                    return false;
                });
            }

            if( document.getElementById('ofw-bulk-tool-action-type') !== null ) {
                document.getElementById('ofw-bulk-tool-action-type').addEventListener('change', function () {
                    if (this.value === 'accept_enable') {
                        document.getElementsByClassName('ofw-bulk-tool-auto-accept-percentage').style.display = 'block';
                        document.getElementById('ofw-bulk-tool-auto-accept-percentage').setAttribute('required', 'required');
                    } else {
                        document.getElementsByClassName('ofw-bulk-tool-auto-accept-percentage').style.display = 'none';
                        document.getElementById('ofw-bulk-tool-auto-accept-percentage').removeAttribute('required');
                    }
                    if (this.value === 'decline_enable') {
                        document.getElementsByClassName('ofw-bulk-tool-auto-decline-percentage').style.display = 'block';
                        document.getElementById('ofw-bulk-tool-auto-decline-percentage').setAttribute('required', 'required');
                    } else {
                        document.getElementsByClassName('ofw-bulk-tool-auto-decline-percentage').style.display = 'none';
                        document.getElementById('ofw-bulk-tool-auto-decline-percentage').removeAttribute('required');
                    }
                });
            }

            if( document.getElementById('ofw-bulk-tool-action-target-type') !== null ) {
                document.getElementById('ofw-bulk-tool-action-target-type').addEventListener('change', function () {
                    document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-category').style.display = 'none';
                    document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-product-type').style.display = 'none';
                    document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').style.display = 'none';
                    document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').style.display = 'none';
                    document.getElementById('ofw-bulk-tool-target-where-category').removeAttribute('required');
                    document.getElementById('ofw-bulk-tool-target-where-product-type').removeAttribute('required');
                    document.getElementById('ofw-bulk-tool-action-target-where-price-value').removeAttribute('required');
                    document.getElementById('ofw-bulk-tool-target-where-stock-value').removeAttribute('required');

                    if (this.value === 'where') {
                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-type').style.display = 'block';
                        document.getElementById('ofw-bulk-tool-action-target-where-type').setAttribute('required', 'required');
                    } else {
                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-type').style.display = 'none';
                        document.getElementById('ofw-bulk-tool-action-target-where-type').removeAttribute('required');
                    }
                });
            }

            /* change target where type -- toggle categories/value inputs */
            if( document.getElementById('ofw-bulk-tool-action-target-where-type') !== null ) {
                document.getElementById('ofw-bulk-tool-action-target-where-type').addEventListener('change', function () {
                    if (this.value === 'category') {
                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-category').style.display = 'block';
                        document.getElementById('ofw-bulk-tool-target-where-category').setAttribute('required', 'required');

                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-product-type').style.display = 'none';
                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').style.display = 'none';
                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').style.display = 'none';
                        document.getElementById('ofw-bulk-tool-target-where-product-type').removeAttribute('required');
                        document.getElementById('ofw-bulk-tool-action-target-where-price-value').removeAttribute('required');
                        document.getElementById('ofw-bulk-tool-target-where-stock-value').removeAttribute('required');

                    } else if (this.value === 'product_type') {
                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-product-type').style.display = 'block';
                        document.getElementById('ofw-bulk-tool-target-where-product-type').setAttribute('required', 'required');

                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-category').style.display = 'none';
                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').style.display = 'none';
                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').style.display = 'none';
                        document.getElementById('ofw-bulk-tool-target-where-category').removeAttribute('required');
                        document.getElementById('ofw-bulk-tool-action-target-where-price-value').removeAttribute('required');
                        document.getElementById('ofw-bulk-tool-target-where-stock-value').removeAttribute('required');
                    } else {
                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-category').style.display = 'none';
                        document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-product-type').style.display = 'none';
                        document.getElementById('ofw-bulk-tool-target-where-category').removeAttribute('required');
                        document.getElementById('ofw-bulk-tool-target-where-product-type').removeAttribute('required');

                        if (this.value === 'price_greater' || this.value === 'price_less') {
                            document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').style.display = 'block';
                            document.getElementById('ofw-bulk-tool-action-target-where-price-value').setAttribute('required', 'required');

                            document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').style.display = 'none';
                            document.getElementById('ofw-bulk-tool-target-where-stock-value').removeAttribute('required');
                        } else if (this.value === 'stock_greater' || this.value === 'stock_less') {
                            document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').style.display = 'none';
                            document.getElementById('ofw-bulk-tool-action-target-where-price-value').removeAttribute('required');

                            document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').style.display = 'block';
                            document.getElementById('ofw-bulk-tool-target-where-stock-value').setAttribute('required', 'required');
                        } else {
                            document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').style.display = 'none';
                            document.getElementById('ofw-bulk-tool-action-target-where-price-value').removeAttribute('required');

                            document.querySelectorAll('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').style.display = 'none';
                            document.getElementById('ofw-bulk-tool-target-where-stock-value').removeAttribute('required');
                        }
                    }
                });
            }

            /* AJAX - Bulk enable/disable tool */
            if( document.getElementById('ofw_tool_enable_auto_accept_decline') !== null ) {
                document.getElementById('ofw_tool_enable_auto_accept_decline').addEventListener('submit', function () {
                    /* show processing status */
                    document.getElementById('ofw_bulk-acd_enable-tool-submit').setAttribute('disabled', 'disabled');
                    document.getElementById('ofw_bulk-acd_enable-tool-submit').classList.remove('button-primary');
                    document.getElementById('ofw_bulk-acd_enable-tool-submit').innerHTML = '<i class="ofwc-spinner"></i> Processing, please wait...';
                    document.querySelectorAll('#ofw_bulk-acd_enable-tool-submit i.spinner').style.display = 'block';

                    var actionType = document.getElementById('ofw-bulk-tool-action-type').value;
                    var actionTargetType = document.getElementById('ofw-bulk-tool-action-target-type').value;
                    var actionTargetWhereType = document.getElementById('ofw-bulk-tool-action-target-where-type').value;
                    var actionTargetWhereCategory = document.getElementById('ofw-bulk-tool-target-where-category').value;
                    var actionTargetWhereProductType = document.getElementById('ofw-bulk-tool-target-where-product-type').value;
                    var actionTargetWherePriceValue = document.getElementById('ofw-bulk-tool-action-target-where-price-value').value;
                    var actionTargetWhereStockValue = document.getElementById('ofw-bulk-tool-target-where-stock-value').value;

                    var auto_accept_or_decline_enable = "";
                    var auto_accept_or_decline_percentage = "";

                    if (actionType === "accept_enable") {
                        auto_accept_or_decline_enable = "_offers_for_woocommerce_auto_accept_enabled";
                        auto_accept_or_decline_percentage = document.getElementById('ofw-bulk-tool-auto-accept-percentage').value;
                    }
                    if (actionType === "accept_disable") {
                        auto_accept_or_decline_enable = "_offers_for_woocommerce_auto_accept_enabled";
                        auto_accept_or_decline_percentage = document.getElementById('ofw-bulk-tool-auto-accept-percentage').value;
                    }
                    if (actionType === "decline_enable") {
                        auto_accept_or_decline_enable = "_offers_for_woocommerce_auto_decline_enabled";
                        auto_accept_or_decline_percentage = document.getElementById('ofw-bulk-tool-auto-decline-percentage').value;
                    }
                    if (actionType === "decline_disable") {
                        auto_accept_or_decline_enable = "_offers_for_woocommerce_auto_decline_enabled";
                        auto_accept_or_decline_percentage = document.getElementById('ofw-bulk-tool-auto-decline-percentage').value;
                    }

                    var data = {
                        'action': 'adminToolBulkEnableDisable',
                        'actionType': actionType,
                        'actionTargetType': actionTargetType,
                        'actionTargetWhereType': actionTargetWhereType,
                        'actionTargetWhereCategory': actionTargetWhereCategory,
                        'actionTargetWhereProductType': actionTargetWhereProductType,
                        'actionTargetWherePriceValue': actionTargetWherePriceValue,
                        'actionTargetWhereStockValue': actionTargetWhereStockValue,
                        'ofw_meta_key_value': auto_accept_or_decline_enable,
                        'autoAcceptDeclinePercentage': auto_accept_or_decline_percentage
                    };

                    /* post it */
                    $.post(ajaxurl, data, function (response) {
                        if ('failed' !== response) {
                            var redirectUrl = response;
                            top.location.replace(redirectUrl);
                            return true;
                        } else {
                            alert('Error updating records.');
                            return false;
                        }
                    });
                    /*End Post*/
                    return false;
                });
            }
            
            /* Require login for offer button checkbox.*/
            if( document.getElementById('general_setting_enable_offers_only_logged_in_users') !== null ) {
                if (document.getElementById('general_setting_enable_offers_only_logged_in_users').checked) {
                    document.getElementById('general_setting_enable_offers_hide_untill_logged_in_users').closest('tr').style.display = 'block';
                    document.getElementById('general_setting_allowed_roles').closest('tr').style.display = 'block';
                } else {
                    document.getElementById('general_setting_enable_offers_hide_untill_logged_in_users').checked = false;
                    document.getElementById('general_setting_enable_offers_hide_untill_logged_in_users').closest('tr').style.display = 'none';
                    jQuery('#general_setting_allowed_roles').val('').trigger('chosen:updated');
                    document.getElementById('general_setting_allowed_roles').closest('tr').style.display = 'none';
                }

                document.getElementById('general_setting_enable_offers_only_logged_in_users').addEventListener('click' ,function(){
                    if(this.checked) {
                        document.getElementById('general_setting_enable_offers_hide_untill_logged_in_users').closest('tr').style.display = 'block';
                        document.getElementById('general_setting_allowed_roles').closest('tr').style.display = 'block';
                    } else {
                        $("#general_setting_enable_offers_hide_untill_logged_in_users").attr('checked', false);
                        $("#general_setting_enable_offers_hide_untill_logged_in_users").closest('tr').hide();
                        $('#general_setting_allowed_roles').val('').trigger('chosen:updated');
                        $("#general_setting_allowed_roles").closest('tr').hide();
                    }
                });
            }
            /* Display setting tab : Form Field Sortable */
            $( "#angelleye-settings-ul-checkboxes-sortable" ).sortable({
                placeholder: "ui-state-highlight",
                items: "li:not(.ui-state-disabled)",               
                update: function(e, ui) {                                                            
                    var nm = 3;
                    var data = {action:'displaySettingFormFieldPosition'};                    
                    $('.angelleye-settings-li').each(function( index, value ) {
                        if($(this).attr('data-sequence-id') !=''){
                           $(this).attr('data-sequence-id',nm);
                           data[nm] = ((($(this).find('input[type=checkbox]').attr('name')).split('['))[1]).slice(0,-1);
                           nm++;                           
                       }                       
                    });                   
                   
                    $.post(ajaxurl, data, function(response) {
                        return true;  
                    });
                }
            });

            $( "#angelleye-settings-ul-checkboxes-sortable" ).disableSelection();

            if( document.getElementById('ofw_enable_recaptcha') !== null ) {
                document.getElementById('ofw_enable_recaptcha').addEventListener('change', function () {
                    const angelleye_g_captch = document.getElementsByClassName('angelleye_g_captch');
                    if (this.checked) {
                        document.getElementById('ofw_recaptcha_version').style.display = 'block';
                        document.getElementById('ofw_recaptcha_version').closest('tr').style.display = 'block';
                        if (document.getElementById('ofw_recaptcha_version').value === 'v3') {
                            document.getElementById('ofw_recaptcha_site_key').style.display = 'none';
                            document.getElementById('ofw_recaptcha_site_key').closest('tr').style.display = 'none';
                            document.getElementById('ofw_recaptcha_secret_key').style.display = 'none';
                            document.getElementById('ofw_recaptcha_secret_key').closest('tr').style.display = 'none';
                            document.getElementById('ofw_recaptcha_site_key_v3').style.display = 'block';
                            document.getElementById('ofw_recaptcha_site_key_v3').closest('tr').style.display = 'block';
                            document.getElementById('ofw_recaptcha_secret_key_v3').style.display = 'block';
                            document.getElementById('ofw_recaptcha_secret_key_v3').closest('tr').style.display = 'block';
                        } else {
                            document.getElementById('ofw_recaptcha_site_key').style.display = 'block';
                            document.getElementById('ofw_recaptcha_site_key').closest('tr').style.display = 'block';
                            document.getElementById('ofw_recaptcha_secret_key').style.display = 'block';
                            document.getElementById('ofw_recaptcha_secret_key').closest('tr').style.display = 'block';
                            document.getElementById('ofw_recaptcha_site_key_v3').style.display = 'none';
                            document.getElementById('ofw_recaptcha_site_key_v3').closest('tr').style.display = 'none';
                            document.getElementById('ofw_recaptcha_secret_key_v3').style.display = 'none';
                            document.getElementById('ofw_recaptcha_secret_key_v3').closest('tr').style.display = 'none';
                        }
                    } else {
                        for( var i = 0 ; i < angelleye_g_captch.length ; i++ ){
                            angelleye_g_captch[i].style.display = 'none';
                            angelleye_g_captch[i].closest('tr').style.display = 'none';
                        }
                    }

                });

                const ofw_enable_recaptcha = document.getElementById('ofw_enable_recaptcha');
                ofw_enable_recaptcha.dispatchEvent(change_trigger);
            }

            const ofw_recaptcha_version = document.getElementById('ofw_recaptcha_version');
            if( ofw_recaptcha_version !== null ) {

                ofw_recaptcha_version.addEventListener('change', function () {
                    if (this.value === 'v3') {
                        document.getElementById('ofw_recaptcha_site_key').style.display = 'none';
                        document.getElementById('ofw_recaptcha_site_key').closest('tr').style.display = 'none';
                        document.getElementById('ofw_recaptcha_secret_key').style.display = 'none';
                        document.getElementById('ofw_recaptcha_secret_key').closest('tr').style.display = 'none';
                        document.getElementById('ofw_recaptcha_site_key_v3').style.display = 'block';
                        document.getElementById('ofw_recaptcha_site_key_v3').closest('tr').style.display = 'block';
                        document.getElementById('ofw_recaptcha_secret_key_v3').style.display = 'block';
                        document.getElementById('ofw_recaptcha_secret_key_v3').closest('tr').style.display = 'block';
                    } else {
                        document.getElementById('ofw_recaptcha_site_key').style.display = 'block';
                        document.getElementById('ofw_recaptcha_site_key').closest('tr').style.display = 'block';
                        document.getElementById('ofw_recaptcha_secret_key').style.display = 'block';
                        document.getElementById('ofw_recaptcha_secret_key').closest('tr').style.display = 'block';
                        document.getElementById('ofw_recaptcha_site_key_v3').style.display = 'none';
                        document.getElementById('ofw_recaptcha_site_key_v3').closest('tr').style.display = 'none';
                        document.getElementById('ofw_recaptcha_secret_key_v3').style.display = 'none';
                        document.getElementById('ofw_recaptcha_secret_key_v3').closest('tr').style.display = 'none';
                    }
                });
                ofw_recaptcha_version.dispatchEvent(change_trigger);
            }
    });

}(jQuery));