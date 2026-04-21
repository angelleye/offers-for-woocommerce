<?php
/**
 * Base class for third-party plugin compatibility modules.
 *
 * @package offers-for-woocommerce/includes/compatibility
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('OFW_Compatibility')) {

    abstract class OFW_Compatibility {

        /**
         * Return true when the target third-party plugin is installed and active.
         *
         * @return bool
         */
        abstract public static function is_active();

        /**
         * Register WordPress / WooCommerce hooks that integrate with the target plugin.
         *
         * @return void
         */
        abstract public function register_hooks();
    }
}
