<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Angelleye_Offers_For_Woocommerce_Shipping_Method')) {

    /**
     * This class define for extend WooCommerce shipping method
     *
     * @since       1.0.0
     * @package     offers-for-woocommerce
     * @subpackage  offers-for-woocommerce
     * @author      Angell EYE <service@angelleye.com>
     */
    class Angelleye_Offers_For_Woocommerce_Shipping_Method extends WC_Shipping_Method {

        /**
         * Constructor for the Shipping method extend.
         *
         * @access public
         * @return void
         */
        public function __construct() {
            $this->id = 'offer_for_woocommerce_shipping';
            $this->method_title = __('Offers for WooCommerce Shipping', 'offers-for-woocommerce');
            $this->method_description = __('Offers for WooCommerce Shipping', 'offers-for-woocommerce');
            $this->enabled = "yes";
            $this->title = $this->get_option('title', 'Offer Shipping Cost');
            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        /**
         * Initialise Gateway Settings Form Fields
         *
         * @since 0.1.0
         *
         * @return void
         */
        public function init_form_fields() {
            $this->form_fields = array(
                'title' => array(
                    'title' => __('Method Title', 'offers-for-woocommerce'),
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'offers-for-woocommerce'),
                    'default' => __('Offer Shipping Cost', 'offers-for-woocommerce'),
                    'desc_tip' => true
                )
            );
        }

        /**
         * Check product is available or not.
         *
         * @since 0.1.0
         *
         * @param $package
         * @return bool|mixed|null
         */
        public function is_available($package) {
            $is_available = false;
            if ($this->is_offer_product_in_cart()) {
                $is_available = true;
            }

            return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package);
        }

        /**
         * Calculate the shipping rate.
         *
         * @since 0.1.0
         *
         * @param $package
         * @return void
         */
        public function calculate_shipping($package = Array()) {
            if ($this->is_offer_product_in_cart()) {
                $total_shipping_cost = 0;
                foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                    if (isset($values['woocommerce_offer_id']) && !empty($values['woocommerce_offer_id'])) {
                        $enable_shipping_cost = get_post_meta($values['woocommerce_offer_id'], 'enable_shipping_cost', true);
                        $product_shipping_cost = get_post_meta($values['woocommerce_offer_id'], 'offer_shipping_cost', true);
                        if (isset($product_shipping_cost) && !empty($product_shipping_cost)) {
                            $total_shipping_cost = $total_shipping_cost + number_format($product_shipping_cost,wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
                        }
                    }
                }

                if($enable_shipping_cost == 1) {
                
                    $rate = array(
                        'id' => $this->id,
                        'label' => ($total_shipping_cost == 0) ? $this->title. __(" (Free)", "offers-for-woocommerce") : $this->title,
                        'cost' => number_format($total_shipping_cost, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator()),
                        'taxes' => false,
                        'package' => $package,
                    );
                    $this->add_rate($rate);
                }
            }
        }

        /**
         * Check product is in cart or not.
         *
         * @since 0.1.0
         *
         * @return bool
         */
        public function is_offer_product_in_cart() {
            $count = 0;$has_product = FALSE;
            foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                if( isset($values['woocommerce_offer_id']) && !empty($values['woocommerce_offer_id'])) {
                    $has_product = true;
                } else {
                    $count++;
                }
            }
            if($count < 1 && $has_product) {
                return true;
            } else {
                return false;
            }
            
        }

    }

}