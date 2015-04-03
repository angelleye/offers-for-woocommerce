(function ( $ ) {
    "use strict";

    $(function () {

        $(document).ready(function() {

            // change target type -- toggle where input
            $('#ofwc-bulk-action-target-type').change(function(){
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
                $('#bulk-enable-tool-submit').html('<i class="spinner"></i> Processing, please wait...');
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
                    'actionTargetWhereStockValue': actionTargetWhereStockValue
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
                return false;
            });

        });
    });

}(jQuery));