<?php

/**
 *
 * This class defines all code necessary to Recaptcha functionality 
 *
 * @since       1.0.0
 * @package     offers-for-woocommerce
 * @subpackage  offers-for-woocommerce
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Offers_for_Woocommerce_Recaptcha_Helper {

    /**
     * Subscribe User to Recaptcha
     *
     * @since    1.2.0
     * @access   static
     */

    public function __construct() {
        
    }

    /**
     * @since    1.2.0
     * @return string
     */
    public function ofw_recaptcha_setting_field() {

        $fields[] = array('title' => __('Google reCAPTCHA Integration', 'offers-for-woocommerce'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array('title' => __('Enable Google reCAPTCHA', 'offers-for-woocommerce'), 'type' => 'checkbox', 'desc' => '', 'id' => 'ofw_enable_recaptcha');

        $fields[] = array(
            'title' => __('Site key', 'offers-for-woocommerce'),
            'desc' => __('Enter Site key. <a target="_blank" href="https://www.google.com/recaptcha/admin">Get your Site key</a>', 'offers-for-woocommerce'),
            'id' => 'ofw_recaptcha_site_key',
            'type' => 'text',
            'css' => 'min-width:355px;',
        );

        $fields[] = array(
            'title' => __('Secret key', 'offers-for-woocommerce'),
            'desc' => __('Enter Secret key. <a target="_blank" href="https://www.google.com/recaptcha/admin">Get your Secret key</a>', 'offers-for-woocommerce'),
            'id' => 'ofw_recaptcha_secret_key',
            'type' => 'text',
            'css' => 'min-width:355px;',
        );

        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');

        return $fields;
    }

}