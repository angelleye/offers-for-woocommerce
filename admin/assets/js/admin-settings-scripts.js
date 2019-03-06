(function ( $ ) {
    "use strict";

    $(function () {

        $(document).ready(function() {

            $('.chosen-select').chosen({width: "100%"});

            $('#general_setting_default_expire_days').autoNumeric('init',
                {
                    vMin: '0',
                    mDec: '0',
                    lZero: 'allow',
                    aForm: false}
            );

            $('#ofwc-bulk-action-target-where-price-value').autoNumeric('init',
                {
                    aForm: false,      /* Controls if default values are formatted on page ready (load) */
                    aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                    aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                    //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                    vMin : '0.00',    /* Enter the minimum value allowed */
                    lZero: 'allow',   /* Controls if leading zeros are allowed */
                    wEmpty: 'sign',   /* controls input display behavior. */
                    mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                }
            );

            $('#ofw-bulk-tool-action-target-where-price-value').autoNumeric('init',
                {
                    aForm: false,      /* Controls if default values are formatted on page ready (load) */
                    aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                    aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                    //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                    vMin : '0.00',    /* Enter the minimum value allowed */
                    lZero: 'allow',   /* Controls if leading zeros are allowed */
                    wEmpty: 'sign',   /* controls input display behavior. */
                    mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                }
            );

            $('input[name="ofw_minimum_offer_price_target_where_price_value"]').autoNumeric('init',
                {
                    aForm: false,      /* Controls if default values are formatted on page ready (load) */
                    aSep : ofw_param.ofw_admin_js_thousand_separator,       /* Thousand separator */
                    aDec : ofw_param.ofw_admin_js_decimal_separator,       /* Decimal separator */
                    //pSign : 'p',    /* Placement of the currency : p = left and s = right */
                    vMin : '0.00',    /* Enter the minimum value allowed */
                    lZero: 'allow',   /* Controls if leading zeros are allowed */
                    wEmpty: 'sign',   /* controls input display behavior. */
                    mDec: ofw_param.ofw_admin_js_number_of_decimals, /* enter the number of decimal places - this will over ides values set by vMin & vMax */
                }
            );


            /* Minimum Offer Price bulk toogle */            
            $('#ofw-minimum-offer-price-target-type').change(function(){

                $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-category').hide();
                $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-product-type').hide();
                $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').hide();
                $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').hide();
                $('#ofw-minimum-offer-price-target-where-category').removeAttr('required');
                $('#ofw-minimum-offer-price-target-where-product-type').removeAttr('required');
                $('#ofw-minimum-offer-price-target-where-price-value').removeAttr('required');
                $('#ofw-minimum-offer-price-target-where-stock-value').removeAttr('required'); 

                if(  $(this).val() == 'where' )
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-type').show();
                    $('#ofw-minimum-offer-price-target-where-type').attr('required', 'required');
                }
                else
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-type').hide();
                    $('#ofw-minimum-offer-price-target-where-type').removeAttr('required');
                }
            });
            
            /* change target where type -- toggle categories/value inputs */
            $('#ofw-minimum-offer-price-target-where-type').change(function(){
                if(  $(this).val() == 'category' )
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-category').show();
                    $('#ofw-minimum-offer-price-target-where-category').attr('required', 'required');

                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-product-type').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').hide();
                    $('#ofw-minimum-offer-price-target-where-product-type').removeAttr('required');
                    $('#ofw-minimum-offer-price-target-where-price-value').removeAttr('required');
                    $('#ofw-minimum-offer-price-target-where-stock-value').removeAttr('required');
                }
                else if(  $(this).val() == 'product_type' )
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-product-type').show();
                    $('#ofw-minimum-offer-price-target-where-product-type').attr('required', 'required');

                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-category').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').hide();
                    $('#ofw-minimum-offer-price-target-where-category').removeAttr('required');
                    $('#ofw-minimum-offer-price-target-where-price-value').removeAttr('required');
                    $('#ofw-minimum-offer-price-target-where-stock-value').removeAttr('required');
                }
                else
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-category').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-product-type').hide();
                    $('#ofw-minimum-offer-price-target-where-category').removeAttr('required');
                    $('#ofw-minimum-offer-price-target-where-product-type').removeAttr('required');

                    if(  $(this).val() == 'price_greater' || $(this).val() == 'price_less' )
                    {
                        $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').show();
                        $('#ofw-minimum-offer-price-target-where-price-value').attr('required', 'required');

                        $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').hide();
                        $('#ofw-minimum-offer-price-target-where-stock-value').removeAttr('required');
                    }
                    else if(  $(this).val() == 'stock_greater' || $(this).val() == 'stock_less' )
                    {
                        $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').hide();
                        $('#ofw-minimum-offer-price-target-where-price-value').removeAttr('required');

                        $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').show();
                        $('#ofw-minimum-offer-price-target-where-stock-value').attr('required', 'required');
                    }
                    else
                    {
                        $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-price-value').hide();
                        $('#ofw-minimum-offer-price-target-where-price-value').removeAttr('required');

                        $('.angelleye-offers-tools-bulk-action-section.ofw-minimum-offer-price-target-where-stock-value').hide();
                        $('#ofw-minimum-offer-price-target-where-stock-value').removeAttr('required');
                    }
                }
            });
            
            $('#ofw-minimum-offer-price-type').change(function(){
                var selected = $(this).val();
                if('percentage' === selected){
                    $('#ae_offer_type_chnage_lable').html('Minimum Offer Percentage');
                }
                else{
                    $('#ae_offer_type_chnage_lable').html('Minimum Offer Price');
                }
            });
            
            
            $('#ofw-minimum-offer-action-type').change(function(){
                var selected = $(this).val();
                if('ofwc_minimum_offer_price_enable' === selected){
                    $('.ofwc-minimun-offer-price-input').show();
                    $('.ofwc-minimun-offer-price-input').attr('required', 'required');
                    $('.ofw-minimum-offer-price-type').show();
                    $('.ofw-minimum-offer-price-type').attr('required', 'required');                    
                }
                else{
                    /* Disabled */
                    $('.ofwc-minimun-offer-price-input').hide();
                    $('.ofwc-minimun-offer-price-input').removeAttr('required');
                    $('.ofw-minimum-offer-price-type').hide();
                    $('.ofw-minimum-offer-price-type').removeAttr('required');                    
                }
            });

            /* AJAX - Bulk enable/disable tool */
            $('#ofw_tool_minimun_offer_price_form').submit(function(){                
                /* show processing status */
                $('#ofw-minimum-offer-price-tool-submit').attr('disabled', 'disabled');
                $('#ofw-minimum-offer-price-tool-submit').removeClass('button-primary');
                $('#ofw-minimum-offer-price-tool-submit').html('<i class="ofwc-spinner"></i> Processing, please wait...');
                $('#ofw-minimum-offer-price-tool-submit i.spinner').show();
                
                
                var minimum_price = $('#minimun-offer-price-input').val();
                var price_type = $('#ofw-minimum-offer-price-type').val();
                var actionType = $('#ofw-minimum-offer-action-type').val();
                var actionTargetType = $('#ofw-minimum-offer-price-target-type').val();
                var actionTargetWhereType = $('#ofw-minimum-offer-price-target-where-type').val();
                var actionTargetWhereCategory = $('#ofw-minimum-offer-price-target-where-category').val();
                var actionTargetWhereProductType = $('#ofw-minimum-offer-price-target-where-product-type').val();
                var actionTargetWherePriceValue = $('#ofw-minimum-offer-price-target-where-price-value').val();
                var actionTargetWhereStockValue = $('#ofw-minimum-offer-price-target-where-stock-value').val();
                var meta_value = 'ofwc_minimum_offer_price';                                

                var data = {
                    'action': 'adminToolSetMinimumOfferPrice',
                    'actionType' : actionType,
                    'minimumPrice': minimum_price,
                    'priceType': price_type,
                    'actionTargetType': actionTargetType,
                    'actionTargetWhereType': actionTargetWhereType,
                    'actionTargetWhereCategory': actionTargetWhereCategory,
                    'actionTargetWhereProductType': actionTargetWhereProductType,
                    'actionTargetWherePriceValue': actionTargetWherePriceValue,
                    'actionTargetWhereStockValue': actionTargetWhereStockValue,
                    'ofw_meta_key_value': meta_value
                };
                
                /* post it */
                $.post(ajaxurl, data, function(response) {
                    if ( 'failed' !== response )
                    {
                        var redirectUrl = response;

                        /** Debug **/
                        /** console.log(redirectUrl);
                         ** return false; **/

                        top.location.replace(redirectUrl);
                        return true;
                    }
                    else
                    {
                        alert('Error updating records.');
                        return false;
                    }
                });
                /*End Post*/
                return false;
            });
            
            /* change target type -- toggle where input */
            $('#ofwc-bulk-action-target-type').change(function(){

                $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-category').hide();
                $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-product-type').hide();
                $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').hide();
                $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').hide();
                $('#ofwc-bulk-action-target-where-category').removeAttr('required');
                $('#ofwc-bulk-action-target-where-product-type').removeAttr('required');
                $('#ofwc-bulk-action-target-where-price-value').removeAttr('required');
                $('#ofwc-bulk-action-target-where-stock-value').removeAttr('required');

                if(  $(this).val() == 'where' )
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-type').show();
                    $('#ofwc-bulk-action-target-where-type').attr('required', 'required');
                }
                else
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-type').hide();
                    $('#ofwc-bulk-action-target-where-type').removeAttr('required');
                }
            });

            /* change target where type -- toggle categories/value inputs */
            $('#ofwc-bulk-action-target-where-type').change(function(){
                if(  $(this).val() == 'category' )
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-category').show();
                    $('#ofwc-bulk-action-target-where-category').attr('required', 'required');

                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-product-type').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').hide();
                    $('#ofwc-bulk-action-target-where-product-type').removeAttr('required');
                    $('#ofwc-bulk-action-target-where-price-value').removeAttr('required');
                    $('#ofwc-bulk-action-target-where-stock-value').removeAttr('required');
                }
                else if(  $(this).val() == 'product_type' )
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-product-type').show();
                    $('#ofwc-bulk-action-target-where-product-type').attr('required', 'required');

                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-category').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').hide();
                    $('#ofwc-bulk-action-target-where-category').removeAttr('required');
                    $('#ofwc-bulk-action-target-where-price-value').removeAttr('required');
                    $('#ofwc-bulk-action-target-where-stock-value').removeAttr('required');
                }
                else
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-category').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-product-type').hide();
                    $('#ofwc-bulk-action-target-where-category').removeAttr('required');
                    $('#ofwc-bulk-action-target-where-product-type').removeAttr('required');

                    if(  $(this).val() == 'price_greater' || $(this).val() == 'price_less' )
                    {
                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').show();
                        $('#ofwc-bulk-action-target-where-price-value').attr('required', 'required');

                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').hide();
                        $('#ofwc-bulk-action-target-where-stock-value').removeAttr('required');
                    }
                    else if(  $(this).val() == 'stock_greater' || $(this).val() == 'stock_less' )
                    {
                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').hide();
                        $('#ofwc-bulk-action-target-where-price-value').removeAttr('required');

                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').show();
                        $('#ofwc-bulk-action-target-where-stock-value').attr('required', 'required');
                    }
                    else
                    {
                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').hide();
                        $('#ofwc-bulk-action-target-where-price-value').removeAttr('required');

                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').hide();
                        $('#ofwc-bulk-action-target-where-stock-value').removeAttr('required');
                    }
                }
            });

            /* AJAX - Bulk enable/disable tool */
            $('#woocommerce_offers_options_form_bulk_tool_enable_offers').submit(function()
            {
                /* show processing status */
                $('#bulk-enable-tool-submit').attr('disabled', 'disabled');
                $('#bulk-enable-tool-submit').removeClass('button-primary');
                $('#bulk-enable-tool-submit').html('<i class="ofwc-spinner"></i> Processing, please wait...');
                $('#bulk-enable-tool-submit i.spinner').show();

                var actionType = $('#ofwc-bulk-action-type').val();
                var actionTargetType = $('#ofwc-bulk-action-target-type').val();
                var actionTargetWhereType = $('#ofwc-bulk-action-target-where-type').val();
                var actionTargetWhereCategory = $('#ofwc-bulk-action-target-where-category').val();
                var actionTargetWhereProductType = $('#ofwc-bulk-action-target-where-product-type').val();
                var actionTargetWherePriceValue = $('#ofwc-bulk-action-target-where-price-value').val();
                var actionTargetWhereStockValue = $('#ofwc-bulk-action-target-where-stock-value').val();
                var meta_value = 'offers_for_woocommerce_enabled';
                
                if(actionType == 'enable_onexit' || actionType == 'disable_onexit'){
                    meta_value = 'offers_for_woocommerce_onexit_only';
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
                    'ofw_meta_key_value': meta_value
                };

                /* post it */
                $.post(ajaxurl, data, function(response) {
                    if ( 'failed' !== response )
                    {
                        var redirectUrl = response;

                        /** Debug **/
                        /** console.log(redirectUrl);
                         ** return false; **/

                        top.location.replace(redirectUrl);
                        return true;
                    }
                    else
                    {
                        alert('Error updating records.');
                        return false;
                    }
                });
                /*End Post*/
                return false;
            });
            
            $("#ofw-bulk-tool-action-type").change(function(){
                if(  $(this).val() == 'accept_enable' ) {
                    $('.ofw-bulk-tool-auto-accept-percentage').show();
                    $('#ofw-bulk-tool-auto-accept-percentage').attr('required', 'required');
                } else {
                    $('.ofw-bulk-tool-auto-accept-percentage').hide();
                    $('#ofw-bulk-tool-auto-accept-percentage').removeAttr('required', 'required');
                }
                if(  $(this).val() == 'decline_enable' ) {
                    $('.ofw-bulk-tool-auto-decline-percentage').show();
                    $('#ofw-bulk-tool-auto-decline-percentage').attr('required', 'required');
                } else {
                    $('.ofw-bulk-tool-auto-decline-percentage').hide();
                    $('#ofw-bulk-tool-auto-decline-percentage').removeAttr('required', 'required');
                }
            });
            
            $('#ofw-bulk-tool-action-target-type').change(function(){

                $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-category').hide();
                $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-product-type').hide();
                $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').hide();
                $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').hide();
                $('#ofw-bulk-tool-target-where-category').removeAttr('required');
                $('#ofw-bulk-tool-target-where-product-type').removeAttr('required');
                $('#ofw-bulk-tool-action-target-where-price-value').removeAttr('required');
                $('#ofw-bulk-tool-target-where-stock-value').removeAttr('required');

                if(  $(this).val() == 'where' )
                {
                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-type').show();
                    $('#ofw-bulk-tool-action-target-where-type').attr('required', 'required');
                }
                else
                {
                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-type').hide();
                    $('#ofw-bulk-tool-action-target-where-type').removeAttr('required');
                }
            });

            /* change target where type -- toggle categories/value inputs */
            $('#ofw-bulk-tool-action-target-where-type').change(function(){
                if(  $(this).val() == 'category' )
                {
                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-category').show();
                    $('#ofw-bulk-tool-target-where-category').attr('required', 'required');

                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-product-type').hide();
                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').hide();
                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').hide();
                    $('#ofw-bulk-tool-target-where-product-type').removeAttr('required');
                    $('#ofw-bulk-tool-action-target-where-price-value').removeAttr('required');
                    $('#ofw-bulk-tool-target-where-stock-value').removeAttr('required');
                }
                else if(  $(this).val() == 'product_type' )
                {
                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-product-type').show();
                    $('#ofw-bulk-tool-target-where-product-type').attr('required', 'required');

                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-category').hide();
                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').hide();
                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').hide();
                    $('#ofw-bulk-tool-target-where-category').removeAttr('required');
                    $('#ofw-bulk-tool-action-target-where-price-value').removeAttr('required');
                    $('#ofw-bulk-tool-target-where-stock-value').removeAttr('required');
                }
                else
                {
                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-category').hide();
                    $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-product-type').hide();
                    $('#ofw-bulk-tool-target-where-category').removeAttr('required');
                    $('#ofw-bulk-tool-target-where-product-type').removeAttr('required');

                    if(  $(this).val() == 'price_greater' || $(this).val() == 'price_less' )
                    {
                        $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').show();
                        $('#ofw-bulk-tool-action-target-where-price-value').attr('required', 'required');

                        $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').hide();
                        $('#ofw-bulk-tool-target-where-stock-value').removeAttr('required');
                    }
                    else if(  $(this).val() == 'stock_greater' || $(this).val() == 'stock_less' )
                    {
                        $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').hide();
                        $('#ofw-bulk-tool-action-target-where-price-value').removeAttr('required');

                        $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').show();
                        $('#ofw-bulk-tool-target-where-stock-value').attr('required', 'required');
                    }
                    else
                    {
                        $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-action-target-where-price-value').hide();
                        $('#ofw-bulk-tool-action-target-where-price-value').removeAttr('required');

                        $('.ofw-tool-auto-accept-decline-action-section.ofw-bulk-tool-target-where-stock-value').hide();
                        $('#ofw-bulk-tool-target-where-stock-value').removeAttr('required');
                    }
                }
            });

            /* AJAX - Bulk enable/disable tool */
            $('#ofw_tool_enable_auto_accept_decline').submit(function()
            {
                /* show processing status */
                $('#ofw_bulk-acd_enable-tool-submit').attr('disabled', 'disabled');
                $('#ofw_bulk-acd_enable-tool-submit').removeClass('button-primary');
                $('#ofw_bulk-acd_enable-tool-submit').html('<i class="ofwc-spinner"></i> Processing, please wait...');
                $('#ofw_bulk-acd_enable-tool-submit i.spinner').show();

                var actionType = $('#ofw-bulk-tool-action-type').val();
                var actionTargetType = $('#ofw-bulk-tool-action-target-type').val();
                var actionTargetWhereType = $('#ofw-bulk-tool-action-target-where-type').val();
                var actionTargetWhereCategory = $('#ofw-bulk-tool-target-where-category').val();
                var actionTargetWhereProductType = $('#ofw-bulk-tool-target-where-product-type').val();
                var actionTargetWherePriceValue = $('#ofw-bulk-tool-action-target-where-price-value').val();
                var actionTargetWhereStockValue = $('#ofw-bulk-tool-target-where-stock-value').val();
                
                var auto_accept_or_decline_enable = "";
                var auto_accept_or_decline_percentage = "";
                
                if(  $("#ofw-bulk-tool-action-type").val() === "accept_enable" ) {
                    auto_accept_or_decline_enable = "_offers_for_woocommerce_auto_accept_enabled";
                    auto_accept_or_decline_percentage =  $("#ofw-bulk-tool-auto-accept-percentage").val();
                } 
                if(  $("#ofw-bulk-tool-action-type").val() === "accept_disable" ) {
                    auto_accept_or_decline_enable = "_offers_for_woocommerce_auto_accept_enabled";
                    auto_accept_or_decline_percentage =  $("#ofw-bulk-tool-auto-accept-percentage").val();
                } 
                if(  $("#ofw-bulk-tool-action-type").val() === "decline_enable" ) {
                    auto_accept_or_decline_enable = "_offers_for_woocommerce_auto_decline_enabled";
                    auto_accept_or_decline_percentage =  $("#ofw-bulk-tool-auto-decline-percentage").val();
                } 
                if(  $("#ofw-bulk-tool-action-type").val() === "decline_disable" ) {
                    auto_accept_or_decline_enable = "_offers_for_woocommerce_auto_decline_enabled";
                    auto_accept_or_decline_percentage =  $("#ofw-bulk-tool-auto-decline-percentage").val();
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
                $.post(ajaxurl, data, function(response) {
                    if ( 'failed' !== response )
                    {
                        var redirectUrl = response;

                        /** Debug **/
                        /*console.log(redirectUrl);*/
                        /*return false;*/

                        top.location.replace(redirectUrl);
                        return true;
                    }
                    else
                    {
                        alert('Error updating records.');
                        return false;
                    }
                });
                /*End Post*/
                return false;
            });
            
            /* Require login for offer button checkbox.*/
            if($('#general_setting_enable_offers_only_logged_in_users').is(":checked")) {
                $("#general_setting_enable_offers_hide_untill_logged_in_users").closest('tr').show();
                $("#general_setting_allowed_roles").closest('tr').show();
            } else {
                $("#general_setting_enable_offers_hide_untill_logged_in_users").attr('checked', false);
                $("#general_setting_enable_offers_hide_untill_logged_in_users").closest('tr').hide();
                $('#general_setting_allowed_roles').val('').trigger('chosen:updated');
                $("#general_setting_allowed_roles").closest('tr').hide();
            }
            $("#general_setting_enable_offers_only_logged_in_users").click(function() {
                if($(this).is(":checked")) {
                    $("#general_setting_enable_offers_hide_untill_logged_in_users").closest('tr').show();
                    $("#general_setting_allowed_roles").closest('tr').show();
                } else {
                    $("#general_setting_enable_offers_hide_untill_logged_in_users").attr('checked', false);
                    $("#general_setting_enable_offers_hide_untill_logged_in_users").closest('tr').hide();
                    $('#general_setting_allowed_roles').val('').trigger('chosen:updated');
                    $("#general_setting_allowed_roles").closest('tr').hide();
                }
            });
            
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
                        console.log(response);
                        return true;  
                    });
                }
            });
            $( "#angelleye-settings-ul-checkboxes-sortable" ).disableSelection();
            
        });
    });

}(jQuery));