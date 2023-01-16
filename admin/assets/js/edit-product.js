/**
 * Created by kcwebmedia on 3/14/2015.
 */
 (function ( $ ) {
    "use strict";

    $(function () {

        /*Place your administration-specific JavaScript here*/
        if( document.getElementById('product-type') !== null ){
            document.getElementById('product-type').addEventListener('change', function () {
                if( this.value === 'external' ) {
                    document.getElementById('custom_tab_offers_for_woocommerce').classList.add('custom_tab_offers_for_woocommerce_hidden')
                } else {
                    document.getElementById('custom_tab_offers_for_woocommerce').classList.remove('custom_tab_offers_for_woocommerce_hidden')
                }
            });
        }
    });

}(jQuery));