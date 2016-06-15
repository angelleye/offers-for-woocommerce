<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Angelleye_Offers_For_Woocommerce_Shipping_Method')) {

    class Angelleye_Offers_For_Woocommerce_Shipping_Method extends WC_Shipping_Method {

        public function __construct() {
            $this->id = 'offer_for_woocommerce_shipping';
            $this->method_title = __('Offers for WooCommerce Shipping');
            $this->method_description = __('Offers for WooCommerce Shipping');
            $this->enabled = "yes";
            $this->init();
        }

        function init() {
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }
        
        public function is_available( $package ) {
            $is_available = false;
            if ($this->is_offer_product_in_cart()) {
		$is_available = true;
            }
            
            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
        }

        public function calculate_shipping($package = Array()) {
            if ($this->is_offer_product_in_cart()) {
                $total_shipping_cost = 0;
                foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                    if (isset($values['woocommerce_offer_id']) && !empty($values['woocommerce_offer_id'])) {
                        $product_shipping_cost = get_post_meta($values['woocommerce_offer_id'], 'offer_shipping_cost', true);
                        if (isset($product_shipping_cost) && !empty($product_shipping_cost)) {
                            $total_shipping_cost = $total_shipping_cost + number_format($product_shipping_cost, 2, '.', '');
                        }
                    }
                }

                $rate = array(
                    'id' => $this->id,
                    'label' => 'Offer Shiipng Cost',
                    'cost' => number_format($total_shipping_cost, 2, '.', ''),
                    'taxes'   => false,
                    'package' => $package,
                );
                $this->add_rate($rate);
            }
        }

        public function is_offer_product_in_cart() {
            foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                if (isset($values['woocommerce_offer_id']) && !empty($values['woocommerce_offer_id'])) {
                    return true;
                }
            }
            return false;
        }

    }
}