<?php

/**
 *
 * This class defines all code necessary to MailChimp functionality 
 *
 * @since       1.0.0
 * @package     offers-for-woocommerce
 * @subpackage  offers-for-woocommerce
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Offers_for_Woocommerce_MailChimp_Helper {

    /**
     * Subscribe User to MailChimp
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
    public function ofw_mailchimp_handler($posted) {

        if (!isset($posted) || empty($posted)) {
            return;
        }

        $debug = (get_option('ofw_log_enable_mailchimp') == 'yes') ? 'yes' : 'no';

        if ('yes' == $debug) {
            include_once('class-offers-for-woocommerce-logger.php');
            $log = new Angelleye_Offers_For_Woocommerce_Logger();
        }
        $apikey = get_option('ofw_mailchimp_api_key');
        $listId = get_option('ofw_mailchimp_lists');

        $first_name = $posted['offer_name'] ?? '';
        $last_name = '';
        $offer_email = $posted['offer_email'] ?? '';
        $merge_vars = array('FNAME' => $first_name, 'LNAME' => $last_name);

        if (isset($apikey) && !empty($apikey)) {
            include_once('class-offers-for-woocommerce-mailchimp-mcapi.php');
            $api = new AngellEYE_Offers_for_Woocommerce_MailChimp_MCAPI($apikey);
            $retval = $api->listSubscribe($listId, $offer_email, $merge_vars, $email_type = 'html');

            if ('yes' == $debug) {
                if ($retval == '1') {
                    $log_message = $offer_email . ' Email insert into selected MailChimp Subscribe list';
                } elseif ('0' == $retval) {
                    $log_message = $offer_email . ' Email already exist in selected MailChimp Subscribe list';
                } else {
                    $log_message = $retval;
                }
                $log->add('MailChimp', 'MailChimp listSubscribe response: ' . $log_message);
            }
        } else {
            if ('yes' == $debug) {
                $log->add('MailChimp', 'MailChimp API Key not exist');
            }
        }
	    return null;
    }

    /**
     * @since    1.2.0
     * @return string
     */
    public function offers_for_woocommerce_mcapi_setting_fields() {

        $fields[] = array('title' => __('MailChimp Integration', 'offers-for-woocommerce'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array('title' => __('Enable MailChimp', 'offers-for-woocommerce'), 'type' => 'checkbox', 'desc' => '', 'id' => 'ofw_enable_mailchimp');

        $fields[] = array(
            'title' => __('MailChimp API Key', 'offers-for-woocommerce'),
            'desc' => __('Enter your API Key. <a target="_blank" href="http://admin.mailchimp.com/account/api-key-popup">Get your API key</a>', 'offers-for-woocommerce'),
            'id' => 'ofw_mailchimp_api_key',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );

        $fields[] = array(
            'title' => __('MailChimp lists', 'offers-for-woocommerce'),
            'desc' => __('After you add your MailChimp API Key above and save it this list will be populated.', 'Option'),
            'id' => 'ofw_mailchimp_lists',
            'css' => 'min-width:300px;',
            'type' => 'select',
            'options' => $this->angelleye_get_ofw_mailchimp_lists(get_option('ofw_mailchimp_api_key'))
        );

        $fields[] = array(
            'title' => __('Force MailChimp lists refresh', 'offers-for-woocommerce'),
            'desc' => __("Check and 'Save changes' this if you've added a new MailChimp list and it's not showing in the list above.", 'offers-for-woocommerce'),
            'id' => 'ofw_mailchimp_force_refresh',
            'type' => 'checkbox',
        );


        $fields[] = array(
            'title' => __('Debug Log', 'offers-for-woocommerce'),
            'id' => 'ofw_log_enable_mailchimp',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'offers-for-woocommerce'),
            'default' => 'no',
            'desc' => sprintf(__('Log MailChimp events, inside <code>%s</code>', 'offers-for-woocommerce'), OFFERS_FOR_WOOCOMMERCE_LOG_DIR)
        );


        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');

        return $fields;
    }

    /**
     *  @since    1.2.0
     *  Get List from MailChimp
     */
    public function angelleye_get_ofw_mailchimp_lists($apikey) {
        $mailchimp_lists = array();
        $enable_mailchimp = get_option('ofw_enable_mailchimp');
        if (isset($enable_mailchimp) && $enable_mailchimp == 'yes') {
            $mailchimp_lists = unserialize(get_transient('ofw_mailchimp_mailinglist'));
            $mailchimp_debug_log = (get_option('ofw_log_enable_mailchimp') == 'yes') ? 'yes' : 'no';
            if ('yes' == $mailchimp_debug_log) {
                if (!class_exists('Angelleye_Offers_For_Woocommerce_Logger')) {
                    include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-logger.php';
                }
                $log = new Angelleye_Offers_For_Woocommerce_Logger();
            }
            if (empty($mailchimp_lists) || get_option('ofw_mailchimp_force_refresh') == 'yes') {
                include_once 'class-offers-for-woocommerce-mailchimp-mcapi.php';
                $mailchimp_api_key = get_option('ofw_mailchimp_api_key');
                $apikey = (isset($mailchimp_api_key)) ? $mailchimp_api_key : '';
                $api = new AngellEYE_Offers_for_Woocommerce_MailChimp_MCAPI($apikey);
                $retval = $api->lists();
                if ($api->errorCode) {
                    unset($mailchimp_lists);
                    $mailchimp_lists['false'] = __("Unable to load MailChimp lists, check your API Key.", 'doation-button');
                    if ('yes' == $mailchimp_debug_log) {
                        $log->add('MailChimp', 'Unable to load MailChimp lists, check your API Key.');
                    }
                } else {
                    unset($mailchimp_lists);
                    if ( ! count( $retval ) ) {
                        if ('yes' == $mailchimp_debug_log) {
                            $log->add('MailChimp', 'You have not created any lists at MailChimp.');
                        }
                        $mailchimp_lists['false'] = __("You have not created any lists at MailChimp", 'doation-button');
                        return $mailchimp_lists;
                    }
                    foreach ($retval['lists'] as $key => $list) {
                        $mailchimp_lists[$list['id']] = $list['name'];
                    }
                    if ('yes' == $mailchimp_debug_log) {
                        $log->add('MailChimp', 'MailChimp Get List Success..');
                    }
                    set_transient('ofw_mailchimp_mailinglist', serialize($mailchimp_lists), 86400);
                    update_option('ofw_mailchimp_force_refresh', 'no');
                }
            }
        }
        return $mailchimp_lists;
    }

}
