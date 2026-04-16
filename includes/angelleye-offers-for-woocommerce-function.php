<?php

if (!function_exists('ofwc_get_general_settings')) {
    /**
     * Get general plugin settings with request-scoped memoization.
     *
     * @param bool $force_refresh Force a fresh read from the options table.
     * @return array
     */
    function ofwc_get_general_settings( $force_refresh = false ) {
        static $settings = null;

        if ($force_refresh || null === $settings) {
            $settings = get_option('offers_for_woocommerce_options_general', array());
            if (!is_array($settings)) {
                $settings = array();
            }
        }

        return $settings;
    }
}

if (!function_exists('ofwc_get_display_settings')) {
    /**
     * Get display plugin settings with request-scoped memoization.
     *
     * @param bool $force_refresh Force a fresh read from the options table.
     * @return array
     */
    function ofwc_get_display_settings( $force_refresh = false ) {
        static $settings = null;

        if ($force_refresh || null === $settings) {
            $settings = get_option('offers_for_woocommerce_options_display', array());
            if (!is_array($settings)) {
                $settings = array();
            }
        }

        return $settings;
    }
}

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
            $button_options_general = ofwc_get_general_settings();
        }

        $disable_offers_for_sale_items = ! empty( $button_options_general['general_setting_disabled_make_offer_on_product_sale'] );

        if ( $disable_offers_for_sale_items && method_exists( $product, 'is_on_sale' ) && $product->is_on_sale() ) {
            return false;
        }

        return true;
    }
}

if (!function_exists('ofwc_current_user_can_submit_offers')) {
    /**
     * Check whether the current user is allowed to submit offers.
     *
     * @param array|null $button_options_general Optional general settings array.
     * @return true|\WP_Error
     */
    function ofwc_current_user_can_submit_offers( $button_options_general = null ) {
        if ( null === $button_options_general ) {
            $button_options_general = ofwc_get_general_settings();
        }

        if ( ! empty( $button_options_general['general_setting_enable_offers_only_logged_in_users'] ) && ! is_user_logged_in() ) {
            return new WP_Error( 'login_required', __( 'Please log in to submit an offer.', 'offers-for-woocommerce' ) );
        }

        if ( ! empty( $button_options_general['general_setting_allowed_roles'] ) ) {
            if ( ! is_user_logged_in() ) {
                return new WP_Error( 'role_not_allowed', __( 'You are not allowed to submit offers.', 'offers-for-woocommerce' ) );
            }

            $current_user = wp_get_current_user();
            $user_roles   = ! empty( $current_user->roles ) ? (array) $current_user->roles : array();
            $allowed      = array_intersect( $user_roles, (array) $button_options_general['general_setting_allowed_roles'] );

            if ( empty( $allowed ) ) {
                return new WP_Error( 'role_not_allowed', __( 'You are not allowed to submit offers.', 'offers-for-woocommerce' ) );
            }
        }

        return true;
    }
}

if (!function_exists('ofwc_is_supported_offer_product_type')) {
    /**
     * Check whether a product type supports offer submission.
     *
     * @param object $product WooCommerce product object.
     * @return bool
     */
    function ofwc_is_supported_offer_product_type( $product ) {
        $product_type = ofwc_get_product_type( $product );

        return in_array( $product_type, array( 'simple', 'variable' ), true );
    }
}

if (!function_exists('ofwc_get_required_offer_form_fields')) {
    /**
     * Get the required offer form fields from display settings.
     *
     * @param array|null $button_display_options Optional display settings array.
     * @return array
     */
    function ofwc_get_required_offer_form_fields( $button_display_options = null ) {
        if ( null === $button_display_options ) {
            $button_display_options = ofwc_get_display_settings();
        }

        return array(
            'offer_name'         => true,
            'offer_email'        => true,
            'offer_company_name' => ! empty( $button_display_options['display_setting_make_offer_form_field_offer_company_name'] ) && ! empty( $button_display_options['display_setting_make_offer_form_field_offer_company_name_required'] ),
            'offer_phone'        => ! empty( $button_display_options['display_setting_make_offer_form_field_offer_phone'] ) && ! empty( $button_display_options['display_setting_make_offer_form_field_offer_phone_required'] ),
            'offer_notes'        => ! empty( $button_display_options['display_setting_make_offer_form_field_offer_notes'] ) && ! empty( $button_display_options['display_setting_make_offer_form_field_offer_notes_required'] ),
        );
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
        if (empty($price) || empty($currency)) {
            return $price;
        }

        $converted = apply_filters('angelleye_ofw_convert_product_price', null, $price, $currency);
        if ($converted !== null) {
            return $converted;
        }

        if (class_exists('WC_Aelia_CurrencyPrices_Manager')) {
            $woocommerce_currency = get_woocommerce_currency();
            if (empty($woocommerce_currency)) {
                return $price;
            }
            try {
                $aelia_manager = new WC_Aelia_CurrencyPrices_Manager();
                return $aelia_manager->convert_from_base($price, $currency, $woocommerce_currency);
            } catch (Exception $ex) {
                return $price;
            }
        }

        if (function_exists('wmc_get_price')) {
            return wmc_get_price($price, $currency);
        }

        return $price;
    }

}
