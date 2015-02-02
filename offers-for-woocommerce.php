<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Offers for WooCommerce
 * Plugin URI:        http://www.angelleye.com/product/offers-for-woocommerce
 * Description:       Accept offers for products on your website.  Respond with accept, deny, or counter-offer, and manage all active offers/counters easily.
 * Version:           0.1.0
 * Author:            Angell EYE
 * Author URI:        http://www.angelleye.com/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:       /i18n/languages/
 * GitHub Plugin URI: https://github.com/angelleye/offers-for-woocommerce
 *

/**
 * Abort if called directly
 *
 * @since	0.1.0
 */
if(!defined('ABSPATH'))
{
	die;
}

/**
 *******************************
 * Public-Facing Functionality *
 *******************************
 */

/**
 * Override WooCommerce 'woocommerce_quantity_input' fx
 * Output the quantity input for add to cart forms.
 *
 * @param  array $args Args for the input
 * @param  WC_Product|null $product
 * @param  boolean $echo Whether to return or echo|string
 * @since   0.1.0
 */
function woocommerce_quantity_input( $args = array(), $product = null, $echo = true )
{
    global $woocommerce;

    if ( is_null( $product ) )
        $product = $GLOBALS['product'];

    $defaults = array(
        'input_name'    => 'quantity',
        'input_value'   => '1',
        'max_value'     => apply_filters( 'woocommerce_quantity_input_max', '', $product ),
        'min_value'     => apply_filters( 'woocommerce_quantity_input_min', '', $product ),
        'step'          => apply_filters( 'woocommerce_quantity_input_step', '1', $product )
    );

    $cart_key = (isset($args['input_name']) && strpos($args['input_name'], "cart[") == 0) ? str_replace("cart[", "", str_replace("][qty]", "", $args['input_name']) ) : '';
    $cart_object = $woocommerce->cart->get_cart();
    $is_offer = false;

    $args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

    if($cart_object) {
        // If product is found in cart with offer id, then set min/max values to the offer quantity
        // loop cart contents to find offers -- force quantity to offer quantity
        foreach ($cart_object as $key => $value) {
            // if offer item found
            if (isset($value['woocommerce_offer_quantity']) && $value['woocommerce_offer_quantity'] != '') {
                if ($cart_key == $key) {
                    $is_offer = true;
                    $args['input_value'] = $value['woocommerce_offer_quantity'];
                    $args['min_value'] = $value['woocommerce_offer_quantity'];
                    $args['max_value'] = $value['woocommerce_offer_quantity'];
                }
            }
        }
    }

    ob_start();
    if( $is_offer )
    {
        echo '<div class="quantity angelleye-woocommerce-quantity-input-disabled"><input type="number" step="'. esc_attr( $args['step'] ). '" min="' .esc_attr( $args['min_value'] ). '" max="' .esc_attr( $args['max_value'] ). '" name="' .esc_attr( $args['input_name'] ). '" value="' .esc_attr( $args['input_value'] ). '" title="' .__( 'Qty', 'Product quantity input tooltip', 'woocommerce' ). '" class="input-text qty text" size="4" disabled="disabled" /></div>';
    }
    else
    {
        wc_get_template( 'global/quantity-input.php', $args );
    }

    if ( $echo ) {
        echo ob_get_clean();
    } else {
        return ob_get_clean();
    }
}

/**
 * Require plugin class
 *
 * @since	0.1.0
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-offers-for-woocommerce.php' );

/**
 * Register hooks that are fired when the plugin is activated or deactivated
 * When the plugin is deleted, the uninstall.php file is loaded
 *
 * @since	0.1.0
 */
register_activation_hook( __FILE__ , array('Angelleye_Offers_For_Woocommerce', 'activate'));
register_deactivation_hook( __FILE__ , array('Angelleye_Offers_For_Woocommerce', 'deactivate'));

/**
 * Plugins Loaded init
 *
 * @since	0.1.0
 */
add_action( 'plugins_loaded', array( 'Angelleye_Offers_For_Woocommerce', 'get_instance' ) );

/**
 **********************************************
 * Dashboard and Administrative Functionality *
 **********************************************
 */

/**
 * Include plugin admin class
 *
 * @NOTE:	!The code below is intended to to give the lightest footprint possible
 * @NOTE:	If you want to include Ajax within the dashboard, change the following
 * conditional to: if ( is_admin() ) { ... }
 *
 * @since	0.1.0
 */
if(is_admin() )
{
	require_once(plugin_dir_path(__FILE__). 'admin/class-offers-for-woocommerce-admin.php');
	add_action('plugins_loaded', array('Angelleye_Offers_For_Woocommerce_Admin', 'get_instance'));
}
