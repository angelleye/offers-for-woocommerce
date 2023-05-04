<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Offer_Received_Email' ) ) :

/**
 * A custom Offer Received WooCommerce Email class
 *
 * @since 0.1.0
 * @extends WC_Email
 */
class WC_Offer_Received_Email extends WC_Email {
    /**
     * Set email defaults
     *
     * @since 0.1.0
     */
    public function __construct() {
        // set ID, this simply needs to be a unique name
        $this->id = 'wc_offer_received';

        // this is the title in WooCommerce Email settings
        $this->title = __('Offer received', 'offers-for-woocommerce');

        // this is the description in WooCommerce email settings
        $this->description = __('Offer received notification emails are sent to a customer when a customer submits offer', 'offers-for-woocommerce');

        // these are the default heading and subject lines that can be overridden using the settings
        $this->heading = __('Offer Received', 'offers-for-woocommerce');
        $this->subject = __('[{site_title}] Offer Received ({offer_number}) - {offer_date}', 'offers-for-woocommerce');

        // Set email template paths
        $this->template_html 	= 'woocommerce-offer-received.php';
        $this->template_plain 	= 'plain/woocommerce-offer-received.php';

        // Call parent constructor to load any other defaults not explicitly defined here
        parent::__construct();

        // Set the recipient
        $this->recipient = $this->get_option( 'recipient' );

        $this->template_base = OFWC_PUBLIC_EMAIL_TEMPLATE_PATH;
    }

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @since 0.1.0
     *
     * @param array $offer_args Get the offer_args.
     * @return void
     */
    public function trigger( $offer_args ) {

        $this->recipient = $offer_args['recipient'];
        $this->offer_args = $offer_args;

        if ( ! $this->is_enabled() || ! $this->recipient )
        {
            return;
        }

        $this->find['offer_date']      = '{offer_date}';
        $this->find['offer_number']    = '{offer_number}';

        $this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( date( 'Y-m-d H:i:s') ));
        $this->replace['offer_number'] = $this->offer_args['offer_id'];

	    do_action('angelleye_offer_for_woocommerce_before_email_send', $offer_args, $this );

        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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
	            'offer_args'    => $this->offer_args,
	            'email_heading' => $this->get_heading(),
	            'sent_to_admin' => false,
	            'plain_text'    => false,
	            'email'         => $this
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
	            'offer_args'    => $this->offer_args,
	            'email_heading' => $this->get_heading(),
	            'sent_to_admin' => false,
	            'plain_text'    => true,
	            'email'         => $this
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
     *
     * @return void
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled'    => array(
                'title'   => __('Enable/Disable', 'offers-for-woocommerce'),
                'type'    => 'checkbox',
                'label'   => __('Enable this email notification', 'offers-for-woocommerce'),
                'default' => 'yes'
            ),
            'subject'    => array(
                'title'       => __('Subject', 'offers-for-woocommerce'),
                'type'        => 'text',
                'description' => sprintf( __('This controls the email subject line. Leave blank to use the default subject:', 'offers-for-woocommerce') . ' <code>%s</code>.', $this->subject ),
                'placeholder' => '',
                'default'     => ''
            ),
            'heading'    => array(
                'title'       => __('Email Heading', 'offers-for-woocommerce'),
                'type'        => 'text',
                'description' => sprintf( __('This controls the main heading contained within the email notification. Leave blank to use the default heading:', 'offers-for-woocommerce') . ' <code>%s</code>.', $this->heading ),
                'placeholder' => '',
                'default'     => ''
            ),
            'email_type' => array(
                'title'       => __('Email type', 'offers-for-woocommerce'),
                'type'        => 'select',
                'description' => __('Choose which format of email to send.', 'offers-for-woocommerce'),
                'default'     => 'html',
                'class'       => 'email_type',
                'options'     => array(
                    'plain'     => __('Plain text', 'offers-for-woocommerce'),
                    'html'      => 'HTML',
                    'multipart' => 'Multipart',
                )
            )
        );
    }
}

endif;
