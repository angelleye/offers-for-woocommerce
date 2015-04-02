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

                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').hide();
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').hide();
                }
                else
                {
                    $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-category').hide();
                    $('#ofwc-bulk-action-target-where-category').removeAttr('required');

                    if(  $(this).val() == 'price_greater' || $(this).val() == 'price_less' )
                    {
                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').show();
                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').hide();
                    }
                    else if(  $(this).val() == 'stock_greater' || $(this).val() == 'stock_less' )
                    {
                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').hide();
                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').show();
                    }
                    else
                    {
                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-price-value').hide();
                        $('.angelleye-offers-tools-bulk-action-section.ofwc-bulk-action-target-where-stock-value').hide();
                    }
                }
            });

        });
    });

}(jQuery));