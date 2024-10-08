<?php

/**
 * Functions used by plugins
 */
/**
 * Queue updates for the Angell EYE Updater
 */
if (!function_exists('angelleye_queue_update')) {

    /**
     * Queue updates for the Angell EYE Updater
     *
     * @param array $file Get the file.
     * @param string $file_id Get the file_id.
     * @param int $product_id Get the product_id.
     *
     * @since 0.1.0
     *
     * @return void
     */
    function angelleye_queue_update($file, $file_id, $product_id) {
        global $angelleye_queued_updates;

        if (!isset($angelleye_queued_updates))
            $angelleye_queued_updates = array();

        $plugin = new stdClass();
        $plugin->file = $file;
        $plugin->file_id = $file_id;
        $plugin->product_id = $product_id;

        $angelleye_queued_updates[] = $plugin;
    }
}

/**
 * Load installer for the AngellEYE Updater.
 * @return $api Object
 */
if (!class_exists('AngellEYE_Updater') && !function_exists('angell_updater_install')) {

    /**
     * Load installer for the AngellEYE Updater.
     *
     * @param object $api Get the api object.
     * @param string $action Get the action.
     * @param object $args Get the arguments object.
     *
     * @since 0.1.0
     *
     * @return mixed|stdClass
     */
    function angell_updater_install($api, $action, $args) {
        $download_url = AEU_ZIP_URL;

        if ('plugin_information' != $action ||
                false !== $api ||
                !isset($args->slug) ||
                'angelleye-updater' != $args->slug
        )
            return $api;

        $api = new stdClass();
        $api->name = 'AngellEYE Updater';
        $api->version = '';
        $api->download_link = esc_url($download_url);
        return $api;
    }

    add_filter('plugins_api', 'angell_updater_install', 10, 3);
}

/**
 * AngellEYE Installation Prompts
 */
if (!class_exists('AngellEYE_Updater') && !function_exists('angell_updater_notice')) {

    /**
     * Display a notice if the "AngellEYE Updater" plugin hasn't been installed.
     *
     * @since 2.3.22
     *
     * @return void
     */
    function angell_updater_notice() {
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
        if (in_array('angelleye-updater/angelleye-updater.php', $active_plugins))
            return;

        $slug = 'angelleye-updater';
        $install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $slug), 'install-plugin_' . $slug);
        $activate_url = 'plugins.php?action=activate&plugin=' . urlencode('angelleye-updater/angelleye-updater.php') . '&plugin_status=all&paged=1&s&_wpnonce=' . urlencode(wp_create_nonce('activate-plugin_angelleye-updater/angelleye-updater.php'));

        $message = '<a href="' . esc_url($install_url) . '">Install the Angell EYE Updater plugin</a> to get updates for your Angell EYE plugins.';
        $is_downloaded = false;
        $plugins = array_keys(get_plugins());
        foreach ($plugins as $plugin) {
            if (strpos($plugin, 'angelleye-updater.php') !== false) {
                $is_downloaded = true;
                $message = '<a href="' . esc_url(admin_url($activate_url)) . '"> Activate the Angell EYE Updater plugin</a> to get updates for your Angell EYE plugins.';
            }
        }
        echo '<div id="angelleye-updater-notice" class="updated notice updater-dismissible"><p>' . $message . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>' . "\n";
    }

    /**
     * Dismiss updater admin notice.
     *
     * @since 0.1.0
     *
     * @return void
     */
    function angelleye_updater_dismissible_admin_notice() {
        set_transient( 'angelleye_updater_notice_hide', 'yes', MONTH_IN_SECONDS );
    }

    /**
     * Enqueue angelleye updater scripts
     *
     * @since 0.1.0
     *
     * @return void
     */
	function enqueue_angelleye_updater_scripts(){
		wp_enqueue_script('angelleye-updater-script', plugins_url('../admin/assets/js/update_handler.js', __FILE__), array('jquery'), Angelleye_Offers_For_Woocommerce::VERSION);
	}

	if ( false === ( $angelleye_updater_notice_hide = get_transient( 'angelleye_updater_notice_hide' ) ) ) {
		add_action('admin_notices', 'angell_updater_notice');
		add_action('admin_enqueue_scripts', 'enqueue_angelleye_updater_scripts');
	}
    add_action( 'wp_ajax_angelleye_updater_dismissible_admin_notice', 'angelleye_updater_dismissible_admin_notice' );
}

/**
 * Update offer single use purchase status.
 *
 * since 2.3.23
 *
 * @param int $offer_id Get offer id.
 * @return void
 */
function ofw_manage_offer_single_use( $offer_id ) {

    if( empty( $offer_id ) || $offer_id <= 0 ) {
        return;
    }

    $offer_single_use = get_post_meta($offer_id,'offer_single_use', true);

    if( !empty($offer_single_use) && '1' === $offer_single_use ) {
        update_post_meta($offer_id,'_offer_single_use_purchase', true);
    }
}