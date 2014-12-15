<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Declined_Offer_Email' ) ) :

/**
 * A custom Declined  Offer WooCommerce Email class
 *
 * @since 0.1.0
 * @extends WC_Email
 */
class WC_Declined_Offer_Email extends WC_Email {
    /**
     * Set email defaults
     *
     * @since 0.1.0
     */
    public function __construct() {
        /**
         * Call $plugin_slug from public plugin class
         * @since	0.1.0
         */
        $plugin = Angelleye_Offers_For_Woocommerce::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        // set ID, this simply needs to be a unique name
        $this->id = 'wc_declined_offer';

        // this is the title in WooCommerce Email settings
        $this->title = 'Declined offer';

        // this is the description in WooCommerce email settings
        $this->description = 'Declined Offer Notification emails are sent when a customer offer is declined by the store admin';

        // these are the default heading and subject lines that can be overridden using the settings
        $this->heading = 'Declined Offer';
        $this->subject = 'Declined Offer';

        // these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
        $this->template_html  = 'woocommerce-offer-declined.php';
        $this->template_html_path = plugin_dir_path(__FILE__). 'emails/';

        $this->template_plain = 'woocommerce-offer-declined.php';
        $this->template_plain_path = plugin_dir_path(__FILE__). 'emails/plain/';

        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct();

        // Set the recipient
        $this->recipient = $this->get_option( 'recipient' );
    }

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @since 0.1.0
     * @param int $order_id
     */
    public function trigger( $offer_args ) {

        $this->recipient = $offer_args['recipient'];
        $this->offer_args = $offer_args;

        // woohoo, send the email!
        $this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    /**
     * get_content_html function.
     *
     * @since 0.1.0
     * @return string
     */
    public function get_content_html() {
        ob_start();
        wc_get_template( $this->template_html, array(
            'offer_args'         => $this->offer_args,
            'email_heading' => $this->get_heading(),
            'sent_to_admin'    => false,
            'plain_text'    => false
            ),
            '',
            $this->template_html_path
        );
        return ob_get_clean();
    }

    /**
     * get_content_plain function.
     *
     * @since 0.1.0
     * @return string
     */
    public function get_content_plain() {
        ob_start();
        wc_get_template( $this->template_plain, array(
            'offer_args'         => $this->offer_args,
            'email_heading' => $this->get_heading(),
            'sent_to_admin'    => false,
            'plain_text'    => true
            ),
            '',
            $this->template_plain_path
        );
        return ob_get_clean();
    }

    /**
     * Initialize Settings Form Fields
     *
     * @since 0.1.0
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled'    => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable this email notification',
                'default' => 'yes'
            ),
            'subject'    => array(
                'title'       => 'Subject',
                'type'        => 'text',
                'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
                'placeholder' => '',
                'default'     => ''
            ),
            'heading'    => array(
                'title'       => 'Email Heading',
                'type'        => 'text',
                'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
                'placeholder' => '',
                'default'     => ''
            ),
            'email_type' => array(
                'title'       => 'Email type',
                'type'        => 'select',
                'description' => 'Choose which format of email to send.',
                'default'     => 'html',
                'class'       => 'email_type',
                'options'     => array(
                    'plain'     => 'Plain text',
                    'html'      => 'HTML',
                    'multipart' => 'Multipart',
                )
            )
        );
    }
} // end \WC_Accepted_Offer_Email class

endif;