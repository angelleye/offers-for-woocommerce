<?php

/**
 *
 * This class defines all code necessary to MailPoet functionality 
 *
 * @since       1.0.0
 * @package     offers-for-woocommerce
 * @subpackage  offers-for-woocommerce
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Offers_for_Woocommerce_MailPoet_Helper {

    /**
     * Subscribe User to MailPoet
     *
     * @since    1.2.0
     * @access   static
     */
    public $plugin_slug = null;

    public function __construct() {
       
    }

    /**
     * @since    1.2.0
     * @param type $posted
     * @return type
     */
    public function ofw_mailpoet_handler($posted) {
        @ini_set( 'display_errors', '0' );
        error_reporting(0);
        if (!isset($posted) || empty($posted)) {
            return null;
        }
        $debug = (get_option('ofw_log_enable_mailpoet') == 'yes') ? 'yes' : 'no';
        if ('yes' == $debug) {
            include_once('class-offers-for-woocommerce-logger.php');
            $log = new Angelleye_Offers_For_Woocommerce_Logger();
        }
        $listId = get_option('ofw_mailpoet_lists');
        $first_name = isset($posted['offer_name']) ? $posted['offer_name'] : '';
        $last_name = '';
        $offer_email = isset($posted['offer_email']) ? $posted['offer_email'] : '';
        if (isset($listId) && !empty($listId)) {
            $user_data = array(
                'email' => $offer_email,
                'firstname' => $first_name,
                'lastname' => $last_name
            );
            $data_subscriber = array(
                'user' => $user_data,
                'user_list' => array('list_ids' => array('0' => $listId))
            );
            if (!class_exists( 'WYSIJA' )) {
                if ('yes' == $debug) {
                    $log->add('MailPoet', 'WYSIJA class not loaded');
                }
                return false;
            }
            $userHelper = WYSIJA::get('user', 'helper');
            $return_value = $userHelper->addSubscriber($data_subscriber);
            if ('yes' == $debug) {
                if (is_bool($return_value)) {
                    $log_message = $offer_email . ' Email already exist in MailPoet Subscribe list';
                } else {
                    $log_message = $offer_email . ' Email insert into selected MailPoet Subscribe list';
                }
                $log->add('MailPoet', 'MailPoet listSubscribe response: ' . $log_message);
            }
        } else {
            if ('yes' == $debug) {
                $log->add('MailPoet', 'MailPoet List not selected');
            }
        }
	    return null;
    }

    /**
     * @since    1.2.0
     * @return string
     */
    public function offers_for_woocommerce_mailpoet_setting_fields() {

        $fields[] = array('title' => __('MailPoet Integration', 'offers-for-woocommerce'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array('title' => __('Enable MailPoet', 'offers-for-woocommerce'), 'type' => 'checkbox', 'desc' => '', 'id' => 'ofw_enable_mailpoet');

        $fields[] = array(
            'title' => __('MailPoet lists', 'offers-for-woocommerce'),
            'desc' => __('After you add your MailPoet API Key above and save it this list will be populated.', 'Option'),
            'id' => 'ofw_mailpoet_lists',
            'css' => 'min-width:300px;',
            'type' => 'select',
            'options' => $this->angelleye_get_ofw_mailpoet_lists()
        );

        $fields[] = array(
            'title' => __('Force MailPoet lists refresh', 'offers-for-woocommerce'),
            'desc' => __("Check and 'Save changes' this if you've added a new MailPoet list and it's not showing in the list above.", 'offers-for-woocommerce'),
            'id' => 'ofw_mailpoet_force_refresh',
            'type' => 'checkbox',
        );

        $fields[] = array(
            'title' => __('Debug Log', 'offers-for-woocommerce'),
            'id' => 'ofw_log_enable_mailpoet',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'offers-for-woocommerce'),
            'default' => 'no',
            'desc' => sprintf(__('Log MailPoet events, inside <code>%s</code>', 'offers-for-woocommerce'), OFFERS_FOR_WOOCOMMERCE_LOG_DIR)
        );

        $fields[] = array(
            'type' => 'hidden',
            'id' => '_constantContact_integration_nonce',
            'value' => wp_create_nonce('_constantContact_integration_nonce')
        );

        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');

        return $fields;
    }

    /**
     * @since    1.2.0
     *  Get List from MailPoet
     */
    public function angelleye_get_ofw_mailpoet_lists() {
        $mailpoet_lists_array = array();
        $enable_mailpoet = get_option('ofw_enable_mailpoet');

        $mailpoet_lists_array = unserialize(get_transient('ofw_mailpoet_mailinglist'));

        if (empty($mailpoet_lists_array) || get_option('ofw_mailpoet_force_refresh') == 'yes') {

            if (!( class_exists('WYSIJA') )) {
                return null;
            }

            unset($mailpoet_lists_array);
            $mailpoet_debug_log = (get_option('ofw_log_enable_mailpoet') == 'yes') ? 'yes' : 'no';
            if ('yes' == $mailpoet_debug_log) {
                if (!class_exists('Angelleye_Offers_For_Woocommerce_Logger')) {
                    include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-logger.php';
                }
                $log = new Angelleye_Offers_For_Woocommerce_Logger();
            }

            // get MailPoet / Wysija lists
            $model_list = WYSIJA::get('list', 'model');
            $mailpoet_lists = $model_list->get(array('name', 'list_id'), array('is_enabled' => 1));

            foreach ($mailpoet_lists as $list) {
                $mailpoet_lists_array[$list['list_id']] = $list['name'];
            }
            if ('yes' == $mailpoet_debug_log) {
                $log->add('MailPoet', 'MailPoet Get List Success..');
            }
            set_transient('ofw_mailpoet_mailinglist', serialize($mailpoet_lists_array), 86400);
            update_option('ofw_mailpoet_force_refresh', 'no');
        }

        return $mailpoet_lists_array;
    }

}
