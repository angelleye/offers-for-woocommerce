/**
 * Created by kcwebmedia on 3/14/2015.
 */
(function ( $ ) {
    "use strict";

    $(function () {

        // Place your administration-specific JavaScript here
        $(document).ready(function () {

            $('#product-type').change(function(){
               if( $(this).val() == 'external' )
                {
                    $('#custom_tab_offers_for_woocommerce').addClass('custom_tab_offers_for_woocommerce_hidden');
                }
                else
                {
                    $('#custom_tab_offers_for_woocommerce').removeClass('custom_tab_offers_for_woocommerce_hidden');
                }
            });

        });
    });

}(jQuery));