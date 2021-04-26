<?php

if (!function_exists('angelleye_get_vendor_dashboard_page_url')) {

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

    function angelleye_ofw_get_product_price_multi_currency($price, $currency) {
        return $price;
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