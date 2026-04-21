<?php
/**
 * Loader for third-party plugin compatibility modules.
 *
 * To add a new compatibility module:
 *   1. Create includes/compatibility/class-ofw-compatibility-{slug}.php
 *   2. Define a class OFW_Compatibility_{Slug} extending OFW_Compatibility.
 *   3. Add its class name to self::$classes below.
 *
 * @package offers-for-woocommerce/includes/compatibility
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('OFW_Compatibility_Loader')) {

    class OFW_Compatibility_Loader {

        private static $classes = array(
            'OFW_Compatibility_Aelia',
            'OFW_Compatibility_Villatheme',
        );

        public static function load() {
            require_once __DIR__ . '/abstract-class-ofw-compatibility.php';

            $classes = apply_filters('angelleye_ofw_compatibility_classes', self::$classes);

            foreach ($classes as $class) {
                $file = __DIR__ . '/' . self::class_to_filename($class);
                if (file_exists($file)) {
                    require_once $file;
                }
                if (class_exists($class) && is_subclass_of($class, 'OFW_Compatibility') && $class::is_active()) {
                    $instance = new $class();
                    $instance->register_hooks();
                }
            }
        }

        private static function class_to_filename($class) {
            return 'class-' . str_replace('_', '-', strtolower($class)) . '.php';
        }
    }
}
