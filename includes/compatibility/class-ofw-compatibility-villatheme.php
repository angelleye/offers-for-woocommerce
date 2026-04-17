<?php
/**
 * VillaTheme WooCommerce Multi Currency compatibility.
 *
 * @package offers-for-woocommerce/includes/compatibility
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('OFW_Compatibility_Villatheme')) {

    class OFW_Compatibility_Villatheme extends OFW_Compatibility {

        public static function is_active() {
            return function_exists('wmc_get_price');
        }

        public function register_hooks() {
            add_filter('angelleye_ofw_convert_product_price', array($this, 'convert_product_price'), 10, 3);
            add_filter('wmc_get_current_currency', array($this, 'force_offer_currency'), 99, 1);
            add_filter('wmc_is_change_price', array($this, 'skip_conversion_for_offer_items'), 99, 3);
        }

        public function convert_product_price($converted, $price, $currency) {
            if ($converted !== null) {
                return $converted;
            }
            return wmc_get_price($price, $currency);
        }

        public function force_offer_currency($current_currency) {
            if (!did_action('wp_loaded') || !isset(WC()->cart) || sizeof(WC()->cart->get_cart()) === 0) {
                return $current_currency;
            }
            foreach (WC()->cart->get_cart() as $cart_item) {
                if (empty($cart_item['woocommerce_offer_id'])) {
                    continue;
                }
                $offer_currency = !empty($cart_item['woocommerce_offer_currency'])
                    ? $cart_item['woocommerce_offer_currency']
                    : get_post_meta($cart_item['woocommerce_offer_id'], 'offer_currency', true);
                if (!empty($offer_currency)) {
                    return $offer_currency;
                }
            }
            return $current_currency;
        }

        public function skip_conversion_for_offer_items($is_change_price, $price = null, $currency = null) {
            if (!did_action('wp_loaded') || !isset(WC()->cart) || sizeof(WC()->cart->get_cart()) === 0) {
                return $is_change_price;
            }
            foreach (WC()->cart->get_cart() as $cart_item) {
                if (!empty($cart_item['woocommerce_offer_id'])) {
                    return false;
                }
            }
            return $is_change_price;
        }
    }
}
