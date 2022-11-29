<?php
/**
 * Email Reminder Class
 * @since      0.1.0
 * @package   Angelleye_Offers_For_Woocommerce_Admin
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AngellEYE_Offers_for_Woocommerce_Email_reminder {

	protected $data = array(
		'product_ids' => array(),
	);

	protected $changes = array();

	public function __construct() {
		add_action( 'admin_init', array( $this, 'ofw_save_template' ), 10 );
		add_action( 'wp_ajax_ofw_er_preview_email_send', array( $this, 'preview_email_send' ) );
		add_action( 'wp_ajax_nopriv_ofw_er_preview_email_send', array( $this, 'preview_email_send' ) );
		add_action( 'ofw_email_cron_hook', array( $this, 'ofw_send_email_reminder_cron' ), 10, 3 );

	}

    /**
     * Send offer reminder email using cron job.
     *
     * @param $template_id
     * @param $product_id
     * @param $offer_id
     * @return void
     *
     * @since 2.3.19
     */
	public function ofw_send_email_reminder_cron( $template_id, $product_id, $offer_id ) {

		$template_id = !empty( $template_id ) ? $template_id : '';
		$product_ids = !empty( $product_id ) ? $product_id : '';
		$offer_id = !empty( $offer_id ) ? $offer_id : '';

		if( empty( $template_id ) && empty( $product_ids ) && empty( $offer_id ) ){
			return ;
		}

		$all_templates = get_option( 'ofw_email_reminders_templates', array() );

		if ( ! empty( $all_templates ) && is_array( $all_templates ) ) {

			$current_template = $all_templates[$template_id];
			if( empty( $current_template ) ){
				return ;
			}

			if ( isset( $current_template['ofw_email_reminder_is_active'] ) && true === $current_template['ofw_email_reminder_is_active'] ) {

				$trigger_time         = ! empty( $current_template['ofw_email_frequency'] ) ? esc_attr( (int) $current_template['ofw_email_frequency'] ) : '';
				$trigger_time_unit    = ! empty( $current_template['ofw_email_frequency_unit'] ) ? esc_attr(  $current_template['ofw_email_frequency_unit'] ) : '';

				$trigger_time_in_secs = $this->get_time_in_sec($trigger_time, $trigger_time_unit);

                $current_template_id = !empty( $current_template['id'] ) ? $current_template['id'] : '';
                $email_reminders_sent_data = get_post_meta($offer_id,'ofw_email_reminders', true);
                $email_reminders_sent_data = !empty( $email_reminders_sent_data ) ? $email_reminders_sent_data : array();

                $user_full_name   = get_post_meta( $offer_id, 'orig_offer_name', true );
                $offer_product_id = get_post_meta( $offer_id, 'orig_offer_product_id', true );

                if ( !empty( $current_template_id ) && ! empty( $offer_product_id ) && ( empty( $email_reminders_sent_data[$current_template_id] ) || 'sent' !== $email_reminders_sent_data[$current_template_id] ) ) {

                    $offer_expiry       = get_post_meta( $offer_id, 'offer_expiration_date', true );
                    $offer_expiry_epoch = strtotime( $offer_expiry );

                    $offer_expiry_formatted = date("Y-m-d H:i:s",$offer_expiry_epoch);
                    $trigger_time_formatted = date("Y-m-d H:i:s",$offer_expiry_epoch - $trigger_time_in_secs);
                    if ( empty( $offer_expiry_epoch ) ) {
                        return;
                    }
					$send_mail_bool = time() < strtotime($trigger_time_formatted);
					//var_dump($send_mail_bool);

//                    if(strtotime($offer_expiry_formatted) < strtotime($trigger_time_formatted) && time() < strtotime($offer_expiry_formatted) ){
                    //if ( $offer_expiry_formatted < $trigger_time_formatted && time() < $offer_expiry_formatted ) {
                    if (  $send_mail_bool ) {
                        $email_data        = new stdClass();
                        $email_data->email = get_post_meta( $offer_id, 'orig_offer_email', true );
                        $email_data->email_template_id = $current_template_id;
                        $email_data->email_body        = ! empty( $current_template['ofw_email_body'] ) ? wp_kses_post( $current_template['ofw_email_body'] ) : "";
                        $email_data->email_subject     = ! empty( $current_template['ofw_email_subject'] ) ? esc_attr( $current_template['ofw_email_subject'] ) : "";
                        $email_data->other_fields      = serialize(
                            array(
                                'ofw_full_name' => !empty( $user_full_name ) ? esc_attr( $user_full_name ) : "",
                                'offer_id'      => !empty( $offer_id ) ? esc_attr( $offer_id ) : "",
                                'product_id'    => !empty( $current_product_id ) ? esc_attr( $current_product_id ) : "",
                                'offer_expiry'  => !empty( $offer_expiry ) ? esc_attr( $offer_expiry ) : "",
                            )
                        );

                        $send_email = $this->send_email_templates( $email_data );

                        if($send_email){
                            $email_reminders_sent_data[ $current_template_id ] = 'sent';
                            update_post_meta($offer_id,'ofw_email_reminders',$email_reminders_sent_data);
                        }
                    }

                } //
			}
		}
	}

	/**
	 * Get time in seconds.
	 *
	 * @param $trigger_time
	 * @param $trigger_time_unit
	 *
	 * @return float|int
	 */
	public function get_time_in_sec( $trigger_time, $trigger_time_unit ){
		switch ( $trigger_time_unit ) {
			case 'minute':
				$trigger_time_in_secs = $trigger_time * 60;
				break;
			case 'hour':
				$trigger_time_in_secs = $trigger_time * 60 * 60;
				break;
			case 'day':
				$trigger_time_in_secs = $trigger_time * 60 * 60 * 24;
				break;
			default :
				$trigger_time_in_secs = 0;
		}
		return $trigger_time_in_secs;
	}

	/**
	 * Send email preview of template
	 *
	 * @return void
	 *
	 * @since 2.3.19
	 */
	public function preview_email_send() {

		$status  = false;
		$message = __( 'Mail sending failed!', 'offers-for-woocommerce' );

		$mail_result = $this->send_email_templates( null, true );

		if ( $mail_result ) {
			$status  = true;
			$message = __( 'Mail has been sent successfully!', 'offers-for-woocommerce' );
		}

		$data = array(
			'status'  => $status,
			'message' => $message,
		);

		wp_send_json( $data );
	}

	/**
	 * Callback function to send email templates.
	 *
	 * @param array $email_data email data  .
	 * @param boolean $preview_email preview email.
	 *
	 * @return bool
	 *
	 * @since 2.3.19
	 */
	public function send_email_templates( $email_data, $preview_email = false ) {

		if ( $preview_email ) {
			$email_data = $this->create_session_for_preview_email();
		}

		if ( ! empty( $email_data->email ) && sanitize_email( $email_data->email ) ) {
			if ( ! $preview_email ) {
				$other_fields    = unserialize( $email_data->other_fields );
				$offer_expiry    = ! empty( $other_fields['offer_expiry'] ) ? sanitize_text_field( $other_fields['offer_expiry'] ) : '';
				$user_full_name  = ! empty( $other_fields['ofw_full_name'] ) ? sanitize_text_field( ucfirst( $other_fields['ofw_full_name'] ) ) : '';
				$current_product = !empty( $other_fields['product_id'] ) ? wc_get_product( $other_fields['product_id'] ) : "";
				$product_name    = !empty( $current_product ) ? $current_product->get_name() : "";
				$product_price   = !empty( $current_product ) ? $current_product->get_price() : "0";
			}
			$admin_user            = get_users(
				array(
					'role'   => 'Administrator',
					'number' => 1,
				)
			);
			
			$admin_user            = !empty( $admin_user ) ? $admin_user : "" ;
			$admin_first_name      = !empty( $admin_user->user_firstname ) ? $admin_user->user_firstname : $admin_user->user_nicename;
			$from_email_name       = $admin_first_name;
			$reply_name_preview    = $admin_first_name;
			$from_email_preview    = get_bloginfo( 'admin_email' );
			$subject_email_preview = stripslashes( html_entity_decode( $email_data->email_subject, ENT_QUOTES, 'UTF-8' ) );
			$subject_email_preview = str_replace( '{{customer.fullname}}', $user_full_name, $subject_email_preview );
			$body_email_preview    = html_entity_decode( $email_data->email_body, ENT_COMPAT, 'UTF-8' );
			$body_email_preview    = convert_smilies( $body_email_preview );
			$body_email_preview    = html_entity_decode( $body_email_preview, ENT_COMPAT, 'UTF-8' );
			if ( ! $preview_email ) {
				$body_email_preview = str_replace( '{{customer.fullname}}', $user_full_name, $body_email_preview );
				if ( 0 != $offer_expiry ) {
					$body_email_preview = str_replace( '{{offer.expiry}}', $offer_expiry, $body_email_preview );
				} else {
					$body_email_preview = str_replace( '{{offer.expiry}}', ' ', $body_email_preview );
				}
				$body_email_preview = str_replace( '{{product.name}}', $product_name, $body_email_preview );
				$body_email_preview = str_replace( '{{product.price}}', $product_price, $body_email_preview );
			}

			$host               = wp_parse_url( get_site_url() );
			$body_email_preview = str_replace( '{{site.url}}', $host['host'], $body_email_preview );
			$admin_first_name   = !empty( $admin_user->user_firstname ) ? $admin_user->user_firstname : $admin_user->user_nicename;
			$body_email_preview = str_replace( '{{admin.firstname}}', $admin_first_name, $body_email_preview );
			$body_email_preview = str_replace( '{{admin.company}}', get_bloginfo( 'name' ), $body_email_preview );
			$headers            = 'From: ' . $from_email_name . ' <' . $from_email_preview . '>' . "\r\n";
			$headers            .= 'Content-Type: text/html' . "\r\n";
			$headers            .= 'Reply-To:  ' . $reply_name_preview . ' ' . "\r\n";
			$body_email_preview = wpautop( $body_email_preview );

			ob_start();

			wc_get_template( 'emails/email-header.php', array( 'email_heading' => $subject_email_preview ) );

			$email_body_template_header = ob_get_clean();

			ob_start();

			wc_get_template( 'emails/email-footer.php' );
			$email_body_template_footer = ob_get_clean();

			$site_title                 = get_bloginfo( 'name' );
			$email_body_template_footer = str_ireplace( '{site_title}', $site_title, $email_body_template_footer );
			$final_email_body           = $email_body_template_header . $body_email_preview . $email_body_template_footer;
			wc_mail( $email_data->email, $subject_email_preview, stripslashes( $final_email_body ), $headers );

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create a dummy object for the preview email.
	 *
	 * @return stdClass
	 * @since 2.3.19
	 */
	public function create_session_for_preview_email() {
		$email_data                    = new stdClass();
		$current_user                  = wp_get_current_user();
		$email_send_to                 = ! empty( $_POST['email_send_to'] ) ? sanitize_text_field( $_POST['email_send_to'] ) : "";
		$email_data->email_body        = !empty( $_POST['email_body'] ) ? wp_kses_post( $_POST['email_body'] ) : "";
		$email_data->email_subject     = ! empty( $_POST['email_subject'] ) ? sanitize_text_field( $_POST['email_subject'] ) : "";
		$email_data->email             = $email_send_to ? $email_send_to : $current_user->user_email;
		$email_data->email_body        = html_entity_decode( $email_data->email_body, ENT_COMPAT, 'UTF-8' );
		$email_data->other_fields      = serialize(
			array(
				'ofw_full_name' => $current_user->user_firstname . $current_user->user_lastname,
			)
		);
		return $email_data;
	}

	/**
	 * Save the new template.
	 *
	 * @return void
	 * @since 2.3.19
	 */
	public function ofw_save_template() {
		$email_reminder_action = ! empty( $_POST['email_reminder_action'] ) ? sanitize_text_field( $_POST['email_reminder_action'] ) : "";
		if ( !empty( $email_reminder_action ) && ( 'update' === $email_reminder_action || 'edit' === $email_reminder_action ) ) {

            if ( ! isset( $_POST['email_reminder_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['email_reminder_nonce'] ), 'submit_email-reminder' ) ) {
				return;
			} else {

				$saved_templates = get_option( 'ofw_email_reminders_templates' );
				$saved_templates = !empty( $saved_templates ) ? $saved_templates : array();
				$template_is_active            = ! empty( $_POST['ofw_email_reminder_is_active'] );
				$template_product_ids          = ! empty( $_POST['product_ids'] ) ?  $_POST['product_ids']  : '';
				$template_name                 = ! empty( $_POST['ofw_template_name'] ) ? sanitize_text_field( $_POST['ofw_template_name'] ) : '';
				$template_email_subject        = ! empty( $_POST['ofw_email_subject'] ) ? sanitize_text_field( $_POST['ofw_email_subject'] ) : '';
				$template_email_body           = ! empty( $_POST['ofw_email_body'] ) ? wp_kses_post( $_POST['ofw_email_body'] ) : '';
				$template_email_frequency      = ! empty( $_POST['ofw_email_frequency'] ) ? sanitize_text_field( $_POST['ofw_email_frequency'] ) : '';
				$template_email_frequency_unit = ! empty( $_POST['ofw_email_frequency_unit'] ) ? sanitize_text_field( $_POST['ofw_email_frequency_unit'] ) : '';
				if ( 'update' === $email_reminder_action ) {
					$template_identifier = ! empty( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';

                    if ( ! empty( $saved_templates ) && is_array( $saved_templates ) ) {
						foreach ( $saved_templates as $index => $templates ) {
                            if ( !empty( $templates['id'] ) && !empty( $template_identifier ) &&  $templates['id'] === $template_identifier ) {
								$updated_template          = array(
									'id'                           => $template_identifier,
									'ofw_email_reminder_is_active' => $template_is_active,
									'product_ids'                  => $template_product_ids,
									'ofw_template_name'            => $template_name,
									'ofw_email_subject'            => $template_email_subject,
									'ofw_email_body'               => $template_email_body,
									'ofw_email_frequency'          => $template_email_frequency,
									'ofw_email_frequency_unit'     => $template_email_frequency_unit,
								);
								$saved_templates[ $index ] = $updated_template;
                                update_option( 'ofw_email_reminders_templates', $saved_templates );
                                $this->create_cron_job($updated_template['ofw_email_frequency'],$updated_template['ofw_email_frequency_unit'], $templates['id'],$updated_template['product_ids']);
                                break;
							}
						}
					}
				} else {
					$uid                     = $template_name;
					$uid                     = preg_replace( '#[ -]+#', '-', $uid );
					$uid                     = strtolower( $uid );
					$new_template            = array(
						'id'                           => $uid,
						'ofw_email_reminder_is_active' => $template_is_active,
						'product_ids'                  => $template_product_ids,
						'ofw_template_name'            => $template_name,
						'ofw_email_subject'            => $template_email_subject,
						'ofw_email_body'               => $template_email_body,
						'ofw_email_frequency'          => $template_email_frequency,
						'ofw_email_frequency_unit'     => $template_email_frequency_unit,
					);

					$saved_templates[ $uid ] = $new_template;


					update_option( 'ofw_email_reminders_templates', $saved_templates );

                    $cron = $this->create_cron_job($new_template['ofw_email_frequency'], $new_template['ofw_email_frequency_unit'], $uid, $new_template['product_ids']);
                }
			}

		} else if ( isset( $_GET['delete'] ) && "1" === sanitize_text_field( $_GET['delete'] ) && isset( $_GET['id'] ) ){
			$key           = ! empty( $_GET['id'] ) ? sanitize_text_field( $_GET['id'] ) : '';
			$all_templates = get_option( 'ofw_email_reminders_templates', array() );
			if ( ! empty( $all_templates ) && is_array( $all_templates ) ) {
				foreach ( $all_templates as $index => $current_template ) {
					if ( ! empty( $current_template['id'] ) && $key === $current_template['id'] ) {
						unset( $all_templates[ $index ] );
						update_option( 'ofw_email_reminders_templates', $all_templates );
						break;
					}
				}
			}
		}
	}

	/**
	 * Schedule cron job on add template.
	 *
	 * @param $ofw_email_frequency
	 * @param $trigger_time_unit
	 * @param $product_ids
	 *
	 * @return void
	 */
    public function create_cron_job( $ofw_email_frequency, $trigger_time_unit, $template_id, $product_ids = array() ){

        $offers = get_posts( [
            'post_type'      => 'woocommerce_offer',
            'post_status'    => array( 'countered-offer', 'accepted-offer'),
            'fields'         => 'ids',
            'posts_per_page' => -1,
        ] );

        if( !empty( $offers ) && is_array( $offers ) ){
            foreach( $offers as $offer ){
                $orig_offer_product_id = get_post_meta( $offer, 'orig_offer_product_id', true );
                $new_product_ids = !empty( $product_ids ) ? $product_ids : array();
                if( empty( $new_product_ids ) ){
                    $new_product_ids = (array)$orig_offer_product_id;
                }

                if( !empty( $new_product_ids ) && in_array( $orig_offer_product_id, $new_product_ids ) ){
                    $offer_expiration_date = get_post_meta( $offer, 'offer_expiration_date', true );
                    $trigger_time_in_secs = $this->get_time_in_sec($ofw_email_frequency, $trigger_time_unit);
                    $trigger_time_formatted = date("Y-m-d H:i:s",strtotime( $offer_expiration_date ) - $trigger_time_in_secs);

                    $args = array(
                        'offer_id' => $offer,
                        'product_id'   => $new_product_ids,
                        'template_id'  => $template_id
                    );

                    wp_schedule_single_event( strtotime($trigger_time_formatted ),'ofw_email_cron_hook', $args );

                }

            }
        }
    }


	/**
	 * Get product IDs this Email Reminder can apply to.
	 *
	 * @param $id
	 * @return mixed|string
	 * @since 2.3.19
	 */
	public function get_product_ids( $id ) {
		$template = $this->get_template_by_id( $id );

		return ! empty( $template['product_ids'] ) ? $template['product_ids'] : "";
	}

	/**
	 * Returns Template on basis of ID, return false in case of no template found
	 *
	 * @param $id
	 * @return false|mixed
	 * @since 2.3.19
	 */
	public function get_template_by_id( $id ) {
		$all_templates = get_option( 'ofw_email_reminders_templates', array() );
		if ( ! empty( $all_templates ) ) {
			foreach ( $all_templates as $template ) {
				if ( $template['id'] == $id ) {
					return $template;
				}
			}
		}
		return false;
	}
}

new AngellEYE_Offers_for_Woocommerce_Email_reminder();