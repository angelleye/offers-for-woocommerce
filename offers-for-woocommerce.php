<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Offers for WooCommerce
 * Plugin URI:        http://www.angelleye.com/product/offers-for-woocommerce
 * Description:       Accept offers for products on your website.  Respond with accept, deny, or counter-offer, and manage all active offers/counters easily.
 * Version:           1.1.3
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
    $plugin = Angelleye_Offers_For_Woocommerce::get_instance();
    $plugin_slug = $plugin->get_plugin_slug();
    $locale = apply_filters( 'plugin_locale', get_locale(), $plugin_slug );
    $plugin_rel_path = plugin_dir_path( __FILE__ );
    $mo_file_path = $plugin_rel_path . 'languages/'.$plugin_slug.'-'. $locale . '.mo';

    if($locale != 'en_US')
    {
        $loadplugintextdomain = load_plugin_textdomain( $plugin_slug, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        if(!$loadplugintextdomain)
        {
            if( file_exists($mo_file_path) && is_admin() )
            {
                echo '<div class="notice error">';
                echo '<p>'. __('<strong>Error: </strong>Offers for WooCommerce language translation file could not be loaded', $plugin_slug). '</p>';
                echo '</div>';
            }
        }
    }
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
 * Include plugin admin class
 *
 * @NOTE:	!The code below is intended to to give the lightest footprint possible
 * @NOTE:	If you want to include Ajax within the dashboard, change the following
 * conditional to: if ( is_admin() ) { ... }
 *
 * @since	0.1.0
 */
if( is_admin() )
{
	require_once(plugin_dir_path(__FILE__). 'admin/class-offers-for-woocommerce-admin.php');
	add_action('plugins_loaded', array('Angelleye_Offers_For_Woocommerce_Admin', 'get_instance'));
}
