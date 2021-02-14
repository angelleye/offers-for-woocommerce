<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Offers for WooCommerce
 * Plugin URI:        http://www.angelleye.com/product/offers-for-woocommerce
 * Description:       Accept offers for products on your website.  Respond with accept, deny, or counter-offer, and manage all active offers/counters easily.
 * Version:           2.3.1
 * Author:            Angell EYE
 * Author URI:        http://www.angelleye.com/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       offers-for-woocommerce
 * Domain Path:       /languages/
 * GitHub Plugin URI: https://github.com/angelleye/offers-for-woocommerce
 * Requires at least: 3.8
 * Tested up to: 5.6.1
 * WC requires at least: 3.0.0
 * WC tested up to: 5.0.0
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
if (!defined('OFW_PLUGIN_URL')) {
    define('OFW_PLUGIN_URL', plugin_dir_path( __FILE__ ));
}

if (!defined('OFFERS_FOR_WOOCOMMERCE_LOG_DIR')) {
    $upload_dir = wp_upload_dir();
    define('OFFERS_FOR_WOOCOMMERCE_LOG_DIR', $upload_dir['basedir'] . '/offers-for-woocommerce-logs/');
}

if (!defined('OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR')) {
    define('OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR', dirname(__FILE__));
}

if (!defined('OFFERS_FOR_WOOCOMMERCE_ASSET_URL')) {
    define('OFFERS_FOR_WOOCOMMERCE_ASSET_URL', plugin_dir_url(__FILE__));
}


if (!defined('AEU_ZIP_URL')) {
    define('AEU_ZIP_URL', 'https://updates.angelleye.com/ae-updater/angelleye-updater/angelleye-updater.zip');
}

if (!defined('PAYPAL_FOR_WOOCOMMERCE_PUSH_NOTIFICATION_WEB_URL')) {
    define('PAYPAL_FOR_WOOCOMMERCE_PUSH_NOTIFICATION_WEB_URL', 'https://www.angelleye.com/');
}
/**
 *******************************
 * Public-Facing Functionality *
 *******************************
 */

/**
 * Require plugin class
 *
 * @since	0.1.0
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-offers-for-woocommerce.php' );

/**
 * Load plugin text domain
 */
add_action('plugins_loaded', 'angelleye_ofwc_load_plugin_textdomain');

/**
 * Load the plugin text domain for translation
 *
 * @since    1.1.3
 */
function angelleye_ofwc_load_plugin_textdomain()
{
    load_plugin_textdomain( 'offers-for-woocommerce', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

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
 * Required functions
 */
if (!function_exists('angelleye_queue_update')) {
    require_once( 'includes/angelleye-functions.php' );
}
angelleye_queue_update(plugin_basename(__FILE__), '101', 'offers-for-woocommerce');
/**
 * Include plugin admin class
 *
 * @NOTE:	!The code below is intended to to give the lightest footprint possible
 * @NOTE:	If you want to include Ajax within the dashboard, change the following
 * conditional to: if ( is_admin() ) { ... }
 *
 * @since	0.1.0
 */
function ofwc_get_active_plugins(){ 
    update_option( 'woocommerce_queue_flush_rewrite_rules', 'yes' );
    $active_plugins = (array) get_option( 'active_plugins', array() );
    if ( is_multisite() ) $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
    return $active_plugins; 
}

function ofwc_is_wcvendors_pro_active() {
    $active_plugins = ofwc_get_active_plugins(); 
    if( !empty($active_plugins)) {
        foreach ($active_plugins as $key => $value) {
            if (strpos($value, 'wcvendors-pro.php') !== false) {
                return true;
            }
        }
    }
    return false;
}
if( is_admin() || ofwc_is_wcvendors_pro_active() )
{
    require_once(plugin_dir_path(__FILE__). 'admin/class-offers-for-woocommerce-admin.php');
    add_action('plugins_loaded', array('Angelleye_Offers_For_Woocommerce_Admin', 'get_instance'));
}
