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
//            $woocommerce_offer_for_woocommerce_shipping_settings = get_option('woocommerce_offer-for-woocommerce-shipping_settings');
//            $woocommerce_offer_for_woocommerce_ship = maybe_unserialize($woocommerce_offer_for_woocommerce_shipping_settings);
//            if( isset($woocommerce_offer_for_woocommerce_ship['title']) && !empty($woocommerce_offer_for_woocommerce_ship['title'])) {
//                $this->title = $woocommerce_offer_for_woocommerce_ship['title'];
//            } else {
//                $this->title  = "Offers Shipping Cost";
//            }
            $this->init();
        }

        function init() {
            $this->init_form_fields();
            $this->init_settings();
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        public function calculate_shipping($package) {
            if ($this->is_offer_product_in_cart()) {
                $rate = array(
                    'id' => $this->id,
                    'label' => 'Offer Shiipng Cost',
                    'cost' => '10.99',
                    'calc_tax' => 'per_item'
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
        

    


                


