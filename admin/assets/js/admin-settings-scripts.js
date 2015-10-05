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

            // change target type -- toggle where input
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

            // change target where type -- toggle categories/value inputs
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

            // AJAX - Bulk enable/disable tool
            $('#woocommerce_offers_options_form_bulk_tool_enable_offers').submit(function()
            {
                // show processing status
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

                var data = {
                    'action': 'adminToolBulkEnableDisable',

                    'actionType': actionType,
                    'actionTargetType': actionTargetType,
                    'actionTargetWhereType': actionTargetWhereType,
                    'actionTargetWhereCategory': actionTargetWhereCategory,
                    'actionTargetWhereProductType': actionTargetWhereProductType,
                    'actionTargetWherePriceValue': actionTargetWherePriceValue,
                    'actionTargetWhereStockValue': actionTargetWhereStockValue,
                    'ofw_meta_key_value': 'offers_for_woocommerce_enabled'
                };

                // post it
                $.post(ajaxurl, data, function(response) {
                    if ( 'failed' !== response )
                    {
                        var redirectUrl = response;

                        /** Debug **/
                        //console.log(redirectUrl);
                        //return false;

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
                if(  $(this).val() == 'enable' ) {
                    $('.ofw-bulk-tool-auto-accept-decline-percentage').show();
                    $('#ofw-bulk-tool-auto-accept-decline-percentage').attr('required', 'required');
                } else {
                    $('.ofw-bulk-tool-auto-accept-decline-percentage').hide();
                    $('#ofw-bulk-tool-auto-accept-decline-percentage').removeAttr('required', 'required');
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

            // change target where type -- toggle categories/value inputs
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

            // AJAX - Bulk enable/disable tool
            $('#ofw_tool_enable_auto_accept_decline').submit(function()
            {
                // show processing status
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
                var autoAcceptDeclinePercentage = $('#ofw-bulk-tool-auto-accept-decline-percentage').val();

                var data = {
                    'action': 'adminToolBulkEnableDisable',

                    'actionType': actionType,
                    'actionTargetType': actionTargetType,
                    'actionTargetWhereType': actionTargetWhereType,
                    'actionTargetWhereCategory': actionTargetWhereCategory,
                    'actionTargetWhereProductType': actionTargetWhereProductType,
                    'actionTargetWherePriceValue': actionTargetWherePriceValue,
                    'actionTargetWhereStockValue': actionTargetWhereStockValue,
                    'ofw_meta_key_value': '_ofw_auto_accept_decline_enable',
                    'autoAcceptDeclinePercentage': autoAcceptDeclinePercentage
                };

                // post it
                $.post(ajaxurl, data, function(response) {
                    if ( 'failed' !== response )
                    {
                        var redirectUrl = response;

                        /** Debug **/
                        //console.log(redirectUrl);
                        //return false;

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
        });
    });

}(jQuery));