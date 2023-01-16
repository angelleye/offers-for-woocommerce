<?php

if (! defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}



if (! class_exists('WC_Accepted_Offer_Email') && class_exists('WC_Email') ) :

	abstract class OFW_Email extends WC_Email
	{
		/**
		 * Set email defaults
		 *
		 */
		public function __construct() {

		}

		/**
		 * Initialize Settings Form Fields
		 *
		 */
		public function init_form_fields()
		{

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
					'description' => sprintf(__('This controls the email subject line. Leave blank to use the default subject:', 'offers-for-woocommerce').' <code>%s</code>', $this->subject),
					'placeholder' => '',
					'default'     => ''
				),
				'heading'    => array(
					'title'       => __('Email Heading', 'offers-for-woocommerce'),
					'type'        => 'text',
					'description' => sprintf(__('This controls the main heading contained within the email notification. Leave blank to use the default heading:', 'offers-for-woocommerce').' <code>%s</code>', $this->heading),
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