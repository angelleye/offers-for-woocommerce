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
     * Display the recaptcha settings fields.
     *
     * @since    1.2.0
     * @return string
     */
    public function ofw_recaptcha_setting_field() {

        $fields[] = array('title' => __('Google reCAPTCHA Integration', 'offers-for-woocommerce'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array('title' => __('Enable Google reCAPTCHA', 'offers-for-woocommerce'), 'type' => 'checkbox', 'desc' => '', 'id' => 'ofw_enable_recaptcha');

        $fields[] = array(
            'title' => __('reCAPTCHA Version', 'paypal-for-woocommerce'),
            'label' => __('Where to display PayPal Express Checkout button(s).', 'paypal-for-woocommerce'),
            'id' => 'ofw_recaptcha_version',
            'type' => 'select',
            'class' => 'angelleye_g_captch',
            'options' => array(
                'v2' => 'reCAPTCHA v2',
                'v3' => 'reCAPTCHA v3',
            ),
            'default' => 'v2',
        );

        $fields[] = array(
            'title' => __('Site Key', 'offers-for-woocommerce'),
            'desc' => __('Enter site key. <a target="_blank" href="https://www.google.com/recaptcha/admin">Get your site key</a>.', 'offers-for-woocommerce'),
            'id' => 'ofw_recaptcha_site_key',
            'type' => 'text',
            'class' => 'angelleye_g_captch',
            'css' => 'min-width:355px;',
        );

        $fields[] = array(
            'title' => __('Secret Key', 'offers-for-woocommerce'),
            'desc' => __('Enter secret key. <a target="_blank" href="https://www.google.com/recaptcha/admin">Get your secret key</a>.', 'offers-for-woocommerce'),
            'id' => 'ofw_recaptcha_secret_key',
            'type' => 'text',
            'class' => 'angelleye_g_captch',
            'css' => 'min-width:355px;',
        );

        $fields[] = array(
            'title' => __('Site Key', 'offers-for-woocommerce'),
            'desc' => __('Enter site key. <a target="_blank" href="https://www.google.com/recaptcha/admin">Get your site key</a>.', 'offers-for-woocommerce'),
            'id' => 'ofw_recaptcha_site_key_v3',
            'type' => 'text',
            'class' => 'angelleye_g_captch',
            'css' => 'min-width:355px;',
        );

        $fields[] = array(
            'title' => __('Secret Key', 'offers-for-woocommerce'),
            'desc' => __('Enter secret key. <a target="_blank" href="https://www.google.com/recaptcha/admin">Get your secret key</a>.', 'offers-for-woocommerce'),
            'id' => 'ofw_recaptcha_secret_key_v3',
            'type' => 'text',
            'class' => 'angelleye_g_captch',
            'css' => 'min-width:355px;',
        );

        $fields[] = array(
            'type' => 'hidden',
            'id' => '_recaptcha_integration_nonce',
            'value' => wp_create_nonce('_recaptcha_integration_nonce')
        );
        
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');

        return $fields;
    }

}
