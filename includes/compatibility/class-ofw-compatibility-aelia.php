<?php
/**
 * Aelia Currency Switcher compatibility.
 *
 * @package offers-for-woocommerce/includes/compatibility
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('OFW_Compatibility_Aelia')) {

    class OFW_Compatibility_Aelia extends OFW_Compatibility {

        private $is_notice_set = false;

        public static function is_active() {
            return class_exists('WC_Aelia_CurrencyPrices_Manager');
        }

        public function register_hooks() {
            add_filter('angelleye_ofw_convert_product_price', array($this, 'convert_product_price'), 10, 3);
            add_filter('wc_aelia_cs_selected_currency', array($this, 'filter_selected_currency'), 99, 1);
            add_action('wp_loaded', array($this, 'sync_currency_cookie'), 10);
        }

        public function convert_product_price($converted, $price, $currency) {
            if ($converted !== null) {
                return $converted;
            }
            $base = get_woocommerce_currency();
            if (empty($base)) {
                return $price;
            }
            try {
                $aelia_manager = new WC_Aelia_CurrencyPrices_Manager();
                return $aelia_manager->convert_from_base($price, $currency, $base);
            } catch (Exception $e) {
                return $price;
            }
        }

        public function filter_selected_currency($aelia_currency) {
            if (!did_action('wp_loaded') || !isset(WC()->cart) || sizeof(WC()->cart->get_cart()) === 0) {
                return $aelia_currency;
            }
            foreach (WC()->cart->get_cart() as $cart_item) {
                if (empty($cart_item['woocommerce_offer_id'])) {
                    continue;
                }
                $offer_currency = get_post_meta($cart_item['woocommerce_offer_id'], 'offer_currency', true);
                if (empty($offer_currency)) {
                    continue;
                }
                if ($aelia_currency !== $offer_currency && $this->is_notice_set === false) {
                    $this->is_notice_set = true;
                    wc_clear_notices();
                    $message = apply_filters(
                        'ofw_aelia_notice',
                        sprintf(__('Aelia Currency Switcher is temporarily disabled as the cart contains an offer product linked to %s currency.', 'offers-for-woocommerce'), $offer_currency),
                        $offer_currency
                    );
                    wc_add_notice($message, 'notice');
                }
                $user_id = get_current_user_id();
                if (!empty($user_id)) {
                    update_user_meta($user_id, 'aelia_cs_selected_currency', $offer_currency);
                }
                return $offer_currency;
            }
            return $aelia_currency;
        }

        public function sync_currency_cookie() {
            if (!did_action('wp_loaded') || !isset(WC()->cart) || sizeof(WC()->cart->get_cart()) === 0) {
                return;
            }
            foreach (WC()->cart->get_cart() as $cart_item) {
                if (empty($cart_item['woocommerce_offer_id'])) {
                    continue;
                }
                $offer_currency = get_post_meta($cart_item['woocommerce_offer_id'], 'offer_currency', true);
                if (empty($offer_currency)) {
                    continue;
                }
                $_POST['aelia_cs_currency'] = $offer_currency;
                $user_id = get_current_user_id();
                if (!empty($user_id)) {
                    update_user_meta($user_id, 'aelia_cs_selected_currency', $offer_currency);
                    wc_setcookie('aelia_cs_selected_currency', $offer_currency);
                }
            }
        }
    }
}
