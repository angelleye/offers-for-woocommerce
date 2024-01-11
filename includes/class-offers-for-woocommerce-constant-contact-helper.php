<?php

/**
 *
 * This class defines all code necessary to MailChimp functionality 
 *
 * @since   1.2.0
 * @package     offers-for-woocommerce
 * @subpackage  offers-for-woocommerce/includes
 * @author      Angell EYE <service@angelleye.com>
 */
require_once 'Ctct/autoload.php';

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;
use Ctct\Auth\SessionDataStore;
use Ctct\Auth\CtctDataStore;
use Ctct\Services;

if (!class_exists('ConstantContact')) {
    require_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/Ctct/ConstantContact.php';
}

/**
 *
 * This class defines all code necessary to ConstantContact functionality
 *
 * @since       1.0.0
 * @package     offers-for-woocommerce
 * @subpackage  offers-for-woocommerce
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Offers_for_Woocommerce_ConstantContact_Helper {

    /**
     * Subscribe User to Constant Contact
     *
     * @since   1.2.0
     * @access   static
     */
    public $plugin_slug = null;

    /**
     * Constructor for the ConstantContact.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        
    }

    /**
     * Handle the constant contact functionality.
     *
     * @since   1.2.0
     * @param array $posted Get offer data.
     * @return void
     */
    public static function ofw_constantcontact_handler($posted) {

        if (!isset($posted) || empty($posted)) {
            return;
        }

        $debug = (get_option('ofw_log_enable_constant_contact') == 'yes') ? 'yes' : 'no';

        if ('yes' == $debug) {
            require_once('class-offers-for-woocommerce-logger.php');
            $log = new Angelleye_Offers_For_Woocommerce_Logger();
        }

        $concontact_api_key = get_option('ofw_constantcontact_api_key');
        $constantcontact_access_token = get_option('ofw_constantcontact_access_token');
        $cclistsid = get_option('ofw_constantcontact_lists');

        $first_name = isset($posted['offer_name']) ? $posted['offer_name'] : '';
        $last_name = '';
        $offer_email = isset($posted['offer_email']) ? $posted['offer_email'] : '';


        if ((isset($concontact_api_key) && !empty($concontact_api_key)) && ( isset($constantcontact_access_token) && !empty($constantcontact_access_token))) {

            $ConstantContact = new ConstantContact($concontact_api_key);

            $response = $ConstantContact->getContactByEmail($constantcontact_access_token, $offer_email);

            if (empty($response->results)) {
                $Contact = new Contact();
                $Contact->addEmail($offer_email);
                $Contact->first_name = $first_name;
                $Contact->last_name = $last_name;
                $Contact->addList($cclistsid);
                $NewContact = $ConstantContact->addContact($constantcontact_access_token, $Contact );
                if ('yes' == $debug) {
                    $log->add('ConstantContact', 'ConstantContact new contact ' . $offer_email . ' added to selected contact list');
                }
            } else {
                $Contact = $response->results[0];
                $Contact->first_name = $first_name;
                $Contact->last_name = $last_name;
                $Contact->addList($cclistsid);
                $new_contact = $ConstantContact->updateContact($constantcontact_access_token, $Contact );
                $log->add('ConstantContact', 'ConstantContact update contact ' . $offer_email . ' to selected contact list');
            }
        } else {
            if ('yes' == $debug) {
                $log->add('ConstantContact', 'Constant Contact API Key OR Constant Contact Access Token does not set');
            }
        }
	    return null;
    }

    /**
     * Display the constant contact settings fields.
     *
     * @since   1.2.0
     * @return array
     */
    public function ofw_ccapi_setting_field() {

        //return $constantcontact_lists;
        $fields[] = array('title' => __('Constant Contact Integration', 'offers-for-woocommerce'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array('title' => __('Enable Constant Contact', 'offers-for-woocommerce'), 'type' => 'checkbox', 'desc' => '', 'id' => 'ofwenable_constant_contact');

        $fields[] = array(
            'title' => __('Constant Contact API Key', 'offers-for-woocommerce'),
            'desc' => __('Enter your API Key. <a target="_blank" href="https://constantcontact.mashery.com/apps/mykeys">Get your API key</a>', 'offers-for-woocommerce'),
            'id' => 'ofw_constantcontact_api_key',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Constant Contact Access Token', 'offers-for-woocommerce'),
            'desc' => __('Enter Your Access Token', 'offers-for-woocommerce'),
            'id' => 'ofw_constantcontact_access_token',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );

        $fields[] = array(
            'title' => __('Constant Contact lists', 'offers-for-woocommerce'),
            'desc' => __('After you add your Constant Contact API Key above and save it this list will be populated.', 'Option'),
            'id' => 'ofw_constantcontact_lists',
            'css' => 'min-width:300px;',
            'type' => 'select',
            'options' => $this->angelleye_get_constantcontact_lists()
        );

        $fields[] = array(
            'title' => __('Force Constant Contact lists refresh', 'offers-for-woocommerce'),
            'desc' => __("Check and 'Save changes' this if you've added a new Constant Contact list and it's not showing in the list above.", 'offers-for-woocommerce'),
            'id' => 'ofw_constantcontact_force_refresh',
            'type' => 'checkbox',
        );

        $fields[] = array(
            'title' => __('Debug Log', 'offers-for-woocommerce'),
            'id' => 'ofw_log_enable_constant_contact',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'offers-for-woocommerce'),
            'default' => 'no',
            'desc' => sprintf(__('Log Constant Contact events, inside <code>%s</code>', 'offers-for-woocommerce'), OFFERS_FOR_WOOCOMMERCE_LOG_DIR)
        );


        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');

        return $fields;
    }

    /**
     * Get the constant contact lists.
     *
     * @since    1.2.0
     * @return array|mixed|void
     */
    public function angelleye_get_constantcontact_lists() {
        $constantcontact_lists = array();
        $concontact_api_key = get_option('ofw_constantcontact_api_key');
        $constantcontact_access_token = get_option('ofw_constantcontact_access_token');
        if ((isset($concontact_api_key) && !empty($concontact_api_key)) && ( isset($constantcontact_access_token) && !empty($constantcontact_access_token))) {
            $constant_contact_debug_log = (get_option('ofw_log_enable_constant_contact') == 'yes') ? 'yes' : 'no';
            if ('yes' == $constant_contact_debug_log) {
                if (!class_exists('Angelleye_Offers_For_Woocommerce_Logger')) {
                    require_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-logger.php';
                }
                $log = new Angelleye_Offers_For_Woocommerce_Logger();
            }
            $constantcontact_lists = unserialize(get_transient('ofw_constantcontact_mailinglist'));
            if (empty($constantcontact_lists) || get_option('ofw_constantcontact_force_refresh') == 'yes') {
                try {
                    $cc = new ConstantContact($concontact_api_key);
                    $list_name = $cc->getLists($constantcontact_access_token);
                    if (isset($list_name) && !empty($list_name)) {
                        unset($constantcontact_lists);
                        $constantcontact_lists = array();
                        foreach ($list_name as $list_namekey => $list_namevalue) {
                            $constantcontact_lists[$list_namevalue->id] = $list_namevalue->name;
                        }
                        set_transient('ofw_constantcontact_mailinglist', serialize($constantcontact_lists), 86400);
                        update_option('ofw_constantcontact_force_refresh', 'no');
                    } else {
                        $constantcontact_lists['false'] = __("Unable to load Constant Contact lists, check your API Key.", 'offers-for-woocommerce');
                    }
                } catch (CtctException $ex) {
                    unset($constantcontact_lists);
                    $constantcontact_lists = array();
                    $constantcontact_lists['false'] = __("Unable to load Constant Contact lists, check your API Key.", 'offers-for-woocommerce');
                    set_transient('ofw_constantcontact_mailinglist', serialize($constantcontact_lists), 86400);
                }
            }
            return $constantcontact_lists;
        }
	    return null;
    }

}
