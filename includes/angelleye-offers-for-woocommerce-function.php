<?php

if (!function_exists('ofwc_get_product_id')) {
    /**
     * Get a product ID using modern WooCommerce APIs with a legacy fallback.
     *
     * @param object $product WooCommerce product object.
     * @return int
     */
    function ofwc_get_product_id( $product ) {
        if ( ! is_object( $product ) ) {
            return 0;
        }

        if ( method_exists( $product, 'get_id' ) ) {
            return (int) $product->get_id();
        }

        if ( isset( $product->id ) ) {
            return (int) $product->id;
        }

        if ( isset( $product->post ) && isset( $product->post->ID ) ) {
            return (int) $product->post->ID;
        }

        return 0;
    }
}

if (!function_exists('ofwc_get_product_type')) {
    /**
     * Get a product type using modern WooCommerce APIs with a legacy fallback.
     *
     * @param object $product WooCommerce product object.
     * @return string
     */
    function ofwc_get_product_type( $product ) {
        if ( ! is_object( $product ) ) {
            return '';
        }

        if ( method_exists( $product, 'get_type' ) ) {
            return (string) $product->get_type();
        }

        return isset( $product->product_type ) ? (string) $product->product_type : '';
    }
}

if (!function_exists('ofwc_get_product_stock_quantity')) {
    /**
     * Get stock quantity using the current API with a legacy fallback.
     *
     * @param object $product WooCommerce product object.
     * @return int|null
     */
    function ofwc_get_product_stock_quantity( $product ) {
        if ( ! is_object( $product ) ) {
            return null;
        }

        if ( method_exists( $product, 'get_stock_quantity' ) ) {
            return $product->get_stock_quantity();
        }

        if ( method_exists( $product, 'get_total_stock' ) ) {
            return $product->get_total_stock();
        }

        return null;
    }
}

if (!function_exists('ofwc_get_myaccount_page_url')) {
    /**
     * Get the My Account page URL using WooCommerce's current helper with a fallback.
     *
     * @return string
     */
    function ofwc_get_myaccount_page_url() {
        if ( function_exists( 'wc_get_page_permalink' ) ) {
            return wc_get_page_permalink( 'myaccount' );
        }

        return get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
    }
}

if (!function_exists('ofwc_product_allows_offers')) {
    /**
     * Check whether offers are allowed for a product under the current sale-product setting.
     *
     * @param object $product WooCommerce product object.
     * @param array|null $button_options_general Optional general settings array.
     * @return bool
     */
    function ofwc_product_allows_offers( $product, $button_options_general = null ) {
        if ( ! is_object( $product ) ) {
            return false;
        }

        if ( null === $button_options_general ) {
            $button_options_general = get_option( 'offers_for_woocommerce_options_general' );
        }

        $disable_offers_for_sale_items = ! empty( $button_options_general['general_setting_disabled_make_offer_on_product_sale'] );

        if ( $disable_offers_for_sale_items && method_exists( $product, 'is_on_sale' ) && $product->is_on_sale() ) {
            return false;
        }

        return true;
    }
}

if (!function_exists('angelleye_get_vendor_dashboard_page_url')) {
    /**
     * Get vendor dashboard page url.
     *
     * @since 0.1.0
     *
     * @return mixed|null
     */
    function angelleye_get_vendor_dashboard_page_url() {
        if (version_compare(WCV_VERSION, '2.0.0', '<')) {
            $wc_prd_vendor_options = get_option('wc_prd_vendor_options');
            if (class_exists('WCVendors_Pro')) {
                $dashbaord_id = $wc_prd_vendor_options['dashboard_page_id'];
            } else {
                $dashbaord_id = $wc_prd_vendor_options['vendor_dashboard_page'];
            }
        } else {
            if (class_exists('WCVendors_Pro')) {
                $dashboard_page_ids = (array) get_option('wcvendors_dashboard_page_id');
                $dashbaord_id = reset($dashboard_page_ids);
            } else {
                $dashbaord_id = get_option('wcvendors_vendor_dashboard_page_id');
            }
        }

        $vendor_dashboard_page_url = get_permalink($dashbaord_id);

        return apply_filters('aeofwc_offer_vendor_dashboard_page_url', $vendor_dashboard_page_url, $dashbaord_id);
    }

}

if (!function_exists('angelleye_ofw_get_product_price_multi_currency')) {

    /**
     * Get product price in multi currency.
     *
     * @since 0.1.0
     *
     * @param float $price Get the price.
     * @param string $currency Get the currency.
     * @return mixed
     */
    function angelleye_ofw_get_product_price_multi_currency($price, $currency) {
        if (class_exists('WC_Aelia_CurrencyPrices_Manager')) {
            $aelia_manager = new WC_Aelia_CurrencyPrices_Manager();
            $woocommerce_currency = get_woocommerce_currency();
            if(empty($woocommerce_currency)) {
                return $price;
            }
            if(empty($currency)) {
                return $price;
            }
            try {
                $converted_price = $aelia_manager->convert_from_base($price, $currency, $woocommerce_currency);
            } catch (Exception $ex) {
                return $price;
            }
            return $converted_price;
        } else {
            return $price;
        }
    }

}
