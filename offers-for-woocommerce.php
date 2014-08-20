<?php
/**
 * Offers for WooCommerce
 *
 * @TODO:	// Description of plugin here
 *
 * @package   Angelleye_Offers_For_Woocommerce
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://angelleye.com
 * @copyright 2014 AngellEYE
 *
 * @wordpress-plugin
 * Plugin Name:       Offers for WooCommerce
 * Plugin URI:        http://www.angelleye.com/product/offers-for-woocommerce-plugin/
 * Description:       Offers for WooCommerce // INSERT DESCRIPTION HERE
 * Version:           0.1.0
 * Author:            Angell EYE
 * Author URI:        http://www.angelleye.com
 * Text Domain:       offers-for-woocommerce
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/angelleye/offers-for-woocommerce
 * Offers-For-Woocommerce: v0.1.0
 */

/**
 * Abort if called directly
 *
 * @since	0.1.0
 */
if(!defined('WPINC'))
{
	die;
}

/**
 * DEBUG ERRORS 
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

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
if(is_admin() && (!defined('DOING_AJAX') || ! DOING_AJAX))
{
	require_once(plugin_dir_path(__FILE__). 'admin/class-offers-for-woocommerce-admin.php');
	add_action('plugins_loaded', array('Angelleye_Offers_For_Woocommerce_Admin', 'get_instance'));
}



/* Add "Make Offer" button code parts - Before add to cart */
add_action('woocommerce_before_add_to_cart_button', 'angelleye_ofwc_before_add_to_cart_button');

/* Add "Make Offer" button code parts - After add to cart */
add_action('woocommerce_after_add_to_cart_button', 'angelleye_ofwc_after_add_to_cart_button');

/* Add "Make Offer" button code parts - After shop loop item */
add_action('woocommerce_after_shop_loop_item', 'angelleye_ofwc_after_show_loop_item', 10, 2);

/* Add "Make Offer" product tab on product single view */
add_filter('woocommerce_product_tabs', 'angelleye_ofwc_add_custom_woocommerce_product_tab');


/**
 * Add extra div wrap before add to cart button 
 *
 * @since	0.1.0
 */ 
function angelleye_ofwc_before_add_to_cart_button()
{
	global $post;
	$custom_tab_options_offers = array(
		'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),                
	);

	if ( $custom_tab_options_offers['enabled'] == 'yes' ){
		echo '<div class="offers-for-woocommerce-make-offer-button-cleared"></div>
		<div class="offers-for-woocommerce-add-to-cart-wrap"><div>';
	}
}

/**
 * Add Make Offer button after add to cart button 
 *
 * @since	0.1.0
 */ 
function angelleye_ofwc_after_add_to_cart_button()
{
	global $post;
	$custom_tab_options_offers = array(
		'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),                
	);

	if ( $custom_tab_options_offers['enabled'] == 'yes' ){
		echo '</div><div class="single_variation_wrap ofwc_offer_tab_form_wrap"><a href="javascript:;" id="offers-for-woocommerce-make-offer-button-id-'.$post->ID.'" class="offers-for-woocommerce-make-offer-button-single-product button alt">Make Offer</a></div>';
		echo '</div>';		
	}
}

/**
 * Callback - Add Make Offer button after add to cart button on Catalog view
 *
 * @since	0.1.0
 */ 
function angelleye_ofwc_after_show_loop_item($post) 
{
	global $post;
	$custom_tab_options_offers = array(
		'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),                
	);

	if ( $custom_tab_options_offers['enabled'] == 'yes' ){
		echo '<a href="'.get_permalink($post->ID).'" id="offers-for-woocommerce-make-offer-button-id-'.$post->ID.'" class="offers-for-woocommerce-make-offer-button-catalog button alt">Make Offer</a>';
	}
}

/**
 * Filter - Add new tab on woocommerce product single view
 *
 * @since	0.1.0
 */
function angelleye_ofwc_add_custom_woocommerce_product_tab($tabs)
{
	global $post;
	$custom_tab_options_offers = array(
		'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),                
	);

	if ( $custom_tab_options_offers['enabled'] == 'yes' ){
		// Add new tab "Make Offer"
		$tabs['tab_custom_ofwc_offer'] = array(
			'title' => __( 'Make Offer', 'angelleye_offers_for_woocommerce' ),
			'priority' => 50,
			'callback' => 'angelleye_ofwc_display_custom_woocommerce_product_tab_content');
			
		// Set priority of the new tab to 20 -- second place	
		$tabs['tab_custom_ofwc_offer']['priority'] = 20;
	}
	
	return $tabs; 
}

/**
 * Callback - Display "Make Offer" front-end form parts
 *
 * @since	0.1.0
 */
function angelleye_ofwc_display_custom_woocommerce_product_tab_content()
{
	// Set html content for output
	include_once( 'public/views/public.php' );		
}



/*
 * Action - Ajax 'approve offer' from manage list
 * @since	0.1.0
 */
add_action( 'wp_ajax_approveOfferFromGrid', 'approveOfferFromGridCallback');

/*
 * Action - Ajax 'approve offer' from manage list
 * @since	0.1.0
 */
function approveOfferFromGridCallback() {
	global $wpdb; // this is how you get access to the database
		$targetPostID = $_POST["targetID"];
		$table = "wp_posts";
		$data_array = array('post_status' => 'accepted-offer');
		$where = array('ID' => $targetPostID);
	$wpdb->update( $table, $data_array, $where );

	$whatever = intval( $_POST['whatever'] );
	$whatever += 10;
		echo $whatever;
	die(); // this is required to return a proper result
}


/*
 * Action - Ajax 'decline offer' from manage list
 * @since	0.1.0
 */
add_action( 'wp_ajax_declineOfferFromGrid', 'declineOfferFromGridCallback');

/*
 * Action - Ajax 'decline offer' from manage list
 * @since	0.1.0
 */
function declineOfferFromGridCallback() {
	global $wpdb; // this is how you get access to the database
		$targetPostID = $_POST["targetID"];
		$table = "wp_posts";
		$data_array = array('post_status' => 'declined-offer');
		$where = array('ID' => $targetPostID);
	$wpdb->update( $table, $data_array, $where );

	$whatever = intval( $_POST['whatever'] );
	$whatever += 10;
		echo $whatever;
	die(); // this is required to return a proper result
}

/**
 * Action - Add 'pending offer(s)' count to wp dashboard 'at a glance' widget
 * @since	0.1.0
 */
add_action( 'dashboard_glance_items', 'my_add_cpt_to_dashboard' );

/**
 * Callback - Action - Add 'pending offer(s)' count to wp dashboard 'at a glance' widget
 * @since	0.1.0
 */
function my_add_cpt_to_dashboard() 
{  
	$post_types = get_post_types( array( '_builtin' => false ), 'objects' );
	foreach ( $post_types as $post_type ) {
		if($post_type->name == 'woocommerce_offer') 
		{
			$num_posts = wp_count_posts( $post_type->name );
			$num = number_format_i18n( $num_posts->publish );
			$text = _n( 'Pending Offer', 'Pending Offers', $num_posts->publish );
			if( (is_super_admin()) || (current_user_can( 'manage_woocommerce')) ) {
				$output = '<a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a>';
				echo '<li class="page-count ' . $post_type->name . '-count">' . $output . '</td>';
			}			
		}
  	}	
}

/**
 * Action - Kick to error msg if trying to add new woocommerce_offer post manually
 * @since	0.1.0
 */
add_action( 'load-post-new.php', 'woocommerce_offer_disable_new_post' );

/**
 * Action - Kick to error msg if trying to add new woocommerce_offer post manually
 * @since	0.1.0
 */
function woocommerce_offer_disable_new_post()
{
    if ( get_current_screen()->post_type == 'woocommerce_offer' )
        wp_die( "We both know you can't do that! Pft." );
}



/**
 * END FILE!
 */