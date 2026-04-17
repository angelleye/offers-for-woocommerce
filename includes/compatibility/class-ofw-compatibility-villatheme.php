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
            add_filter('wmc_product_get_price_condition', array($this, 'skip_conversion_for_offer_products'), 99, 3);
        }

        public function convert_product_price($converted, $price, $currency) {
            if ($converted !== null) {
                return $converted;
            }
            if (!class_exists('WOOMULTI_CURRENCY_Data')) {
                return $price;
            }
            $settings = WOOMULTI_CURRENCY_Data::get_ins();
            $currencies = $settings->get_list_currencies();
            if (empty($currencies[$currency]['rate'])) {
                return $price;
            }
            $target_rate = (float) $currencies[$currency]['rate'];
            if ($target_rate <= 0) {
                return $price;
            }

            $vt_skipped = (bool) apply_filters('wmc_get_price_condition', is_admin() && !wp_doing_ajax());
            if ($vt_skipped) {
                $base_price = (float) $price;
            } else {
                $current = $settings->get_current_currency();
                $current_rate = !empty($currencies[$current]['rate']) ? (float) $currencies[$current]['rate'] : 1.0;
                if ($current_rate <= 0) {
                    return $price;
                }
                $base_price = (float) $price / $current_rate;
            }

            return $base_price * $target_rate;
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

        public function skip_conversion_for_offer_products($condition, $price, $product) {
            if (!$condition) {
                return $condition;
            }
            if (!is_object($product) || !method_exists($product, 'get_id')) {
                return $condition;
            }
            if (!did_action('wp_loaded') || !isset(WC()->cart) || sizeof(WC()->cart->get_cart()) === 0) {
                return $condition;
            }
            $product_id = $product->get_id();
            foreach (WC()->cart->get_cart() as $cart_item) {
                if (empty($cart_item['woocommerce_offer_id']) || empty($cart_item['woocommerce_offer_price_per'])) {
                    continue;
                }
                $offer_product_id = !empty($cart_item['variation_id'])
                    ? (int) $cart_item['variation_id']
                    : (int) $cart_item['product_id'];
                if ($offer_product_id !== (int) $product_id) {
                    continue;
                }
                if (abs((float) $price - (float) $cart_item['woocommerce_offer_price_per']) < 0.0001) {
                    return false;
                }
            }
            return $condition;
        }
    }
}
