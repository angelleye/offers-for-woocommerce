<?php
/**
 * Uninstall methods
 *
 * @since	  0.1.0
 * @package   Angelleye_Offers_For_Woocommerce
 * @author    AngellEYE <andrew@angelleye.com>
 * @license	  GPL-2.0+
 * @link      http://www.angelleye.com
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

if ( is_multisite() ) {

    $blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
    /* delete all transient, options and files */
    delete_option('offers_for_woocommerce_options_general');
    delete_option('offers_for_woocommerce_options_display');
    if ( $blogs ) {

        foreach ( $blogs as $blog ) {
            switch_to_blog( $blog['blog_id'] );
            /* delete all transient, options and files */
            delete_option('offers_for_woocommerce_options_general');
            delete_option('offers_for_woocommerce_options_display');

            restore_current_blog();
        }
    }

} else {
    /* delete all transient, options and files */
    delete_option('offers_for_woocommerce_options_general');
    delete_option('offers_for_woocommerce_options_display');
}