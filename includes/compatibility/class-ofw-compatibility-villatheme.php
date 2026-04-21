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
            add_filter('angelleye_ofw_offer_price_in_base_currency', array($this, 'offer_price_in_base_currency'), 10, 3);
        }

        /**
         * Convert a base-currency amount to the target currency. Used to display the
         * product regular price in the offer's currency on emails and admin panels.
         *
         * Bypasses wmc_get_price() because it short-circuits in admin context.
         * Replicates VillaTheme's internal conversion math
         * (frontend/price.php:128 — $price * rate) while accepting either a base
         * or already-converted input by first normalizing to base.
         */
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

        /**
         * Convert an offer amount (in offer currency) to the store's base currency.
         * Stored alongside the offer so the cart can set a base-currency price and
         * let VillaTheme handle display conversion naturally.
         */
        public function offer_price_in_base_currency($base, $price, $currency) {
            if ($base !== null) {
                return $base;
            }
            if (!class_exists('WOOMULTI_CURRENCY_Data') || !$price || !$currency) {
                return $base;
            }
            $settings = WOOMULTI_CURRENCY_Data::get_ins();
            $currencies = $settings->get_list_currencies();
            if (empty($currencies[$currency]['rate'])) {
                return $base;
            }
            $rate = (float) $currencies[$currency]['rate'];
            if ($rate <= 0) {
                return $base;
            }
            return (float) $price / $rate;
        }
    }
}
