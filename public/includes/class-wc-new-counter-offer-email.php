<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_New_Counter_Offer_Email' ) ) :

/**
 * A custom New Counter Offer WooCommerce Email class
 *
 * @since 0.1.0
 * @extends WC_Email
 */
class WC_New_Counter_Offer_Email extends WC_Email {
    /**
     * Set email defaults
     *
     * @since 0.1.0
     */
    public function __construct() {
        /**
         * Set plugin slug
         * @since	1.1.2
         */
        $this->plugin_slug = 'angelleye-offers-for-woocommerce';

        // set ID, this simply needs to be a unique name
        $this->id = 'wc_new_counter_offer';

        // this is the title in WooCommerce Email settings
        $this->title = __('New counter offer', $this->plugin_slug);

        // this is the description in WooCommerce email settings
        $this->description = __('New Counter Offer Notification emails are sent to the admin when customer submits a counter offer', $this->plugin_slug);

        // these are the default heading and subject lines that can be overridden using the settings
        $this->heading = __('New Counter Offer', $this->plugin_slug);
        $this->subject = __('[{site_title}] New Counter Offer ({offer_number}) - {offer_date}', $this->plugin_slug);

        // Set email template paths
        $this->template_html 	= 'woocommerce-new-counter-offer.php';
        $this->template_plain 	= 'plain/woocommerce-new-counter-offer.php';

        // Call parent constructor to load any other defaults not explicitly defined here
        parent::__construct();

        // Set the recipient
        $this->recipient = $this->get_option( 'admin_email' );

        // Other settings
        $this->template_base = OFWC_PUBLIC_EMAIL_TEMPLATE_PATH;
    }

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @since 0.1.0
     */
    public function trigger( $offer_args ) {

        $this->offer_args = $offer_args;
        $this->recipient = $this->get_option( 'recipient' );

        if( !$this->recipient )
        {
            $this->recipient = get_option('admin_email');
        }

        if ( ! $this->is_enabled() )
        {
            return;
        }
        
        $this->find['offer_date']      = '{offer_date}';
        $this->find['offer_number']    = '{offer_number}';

        $this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( date( 'Y-m-d H:i:s') ));
        $this->replace['offer_number'] = $this->offer_args['offer_id'];

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
                'title'   => __('Enable/Disable', $this->plugin_slug),
                'type'    => 'checkbox',
                'label'   => __('Enable this email notification', $this->plugin_slug),
                'default' => 'yes'
            ),
            'recipient'    => array(
                'title'       => __('Recipient(s)', $this->plugin_slug),
                'type'        => 'text',
                'description' => sprintf( __('Enter recipients (comma separated) for this email. Defaults to', $this->plugin_slug) . ' <code>%s</code>.', esc_attr( get_option('admin_email') ) ),
                'placeholder' => '',
                'default'     => ''
            ),
            'subject'    => array(
                'title'       => __('Subject', $this->plugin_slug),
                'type'        => 'text',
                'description' => sprintf( __('This controls the email subject line. Leave blank to use the default subject:', $this->plugin_slug) . ' <code>%s</code>.', $this->subject ),
                'placeholder' => '',
                'default'     => ''
            ),
            'heading'    => array(
                'title'       => __('Email Heading', $this->plugin_slug),
                'type'        => 'text',
                'description' => sprintf( __('This controls the main heading contained within the email notification. Leave blank to use the default heading:', $this->plugin_slug) . ' <code>%s</code>.', $this->heading ),
                'placeholder' => '',
                'default'     => ''
            ),
            'email_type' => array(
                'title'       => __('Email type', $this->plugin_slug),
                'type'        => 'select',
                'description' => __('Choose which format of email to send.', $this->plugin_slug),
                'default'     => 'html',
                'class'       => 'email_type',
                'options'     => array(
                    'plain'     => __('Plain text', $this->plugin_slug),
                    'html'      => 'HTML',
                    'multipart' => 'Multipart',
                )
            )
        );
    }
} // end \WC_New_Counter_Offer_Email class

endif;