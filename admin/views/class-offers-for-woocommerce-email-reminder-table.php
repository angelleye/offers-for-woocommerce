<?php
/**
 * Offers for WooCommerce - Email Reminder Table Class
 *
 * @package   Angelleye_Offers_For_Woocommerce_Admin
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 * @since     2.3.19
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Offers for Woocommerce Email Reminder Table Class
 * @since     2.3.19
 */
class AngellEYE_Offers_for_Woocommerce_Email_reminder_table extends WP_List_Table {
	/**
	 * URL of this page
	 *
	 * @var   string
	 * @since 2.3.19
	 */
	public $base_url;

	/**
	 * Table columns.
	 * @return array
	 * @since 2.3.19
	 */

	public function __construct() {
		parent::__construct();
	}

	public function get_columns() {
		$columns = array(
			'id'            => __( 'ID', 'offers-for-woocommerce' ),
			'template_name' => __( 'Template Name', 'offers-for-woocommerce' ),
			'email_subject' => __( 'Email Subject', 'offers-for-woocommerce' ),
			'trigger_time'  => __( 'Trigger Before', 'offers-for-woocommerce' ),
			'is_activated'  => __( 'Activate Template', 'offers-for-woocommerce' ),
			'actions'       => __( 'Actions', 'offers-for-woocommerce' )
		);

		return $columns;
	}


	/**
	 * @return void
	 * @since 2.3.19
	 */
	function prepare_items() {
		$all_email_reminders   = ! empty( get_option( 'ofw_email_reminders_templates' ) ) ? get_option( 'ofw_email_reminders_templates' ) : '';
		$total_items           = ( ! empty( $all_email_reminders ) ) ? count( $all_email_reminders ) : 0;
		$per_page              = 10;
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
		$this->items = $all_email_reminders;
	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return ! empty( $item['id'] ) ? $item['id'] : '';
			case 'template_name':
				return ! empty( $item['ofw_template_name'] ) ? $item['ofw_template_name'] : '';
			case 'email_subject':
				return ! empty( $item['ofw_email_subject'] ) ? $item['ofw_email_subject'] : '';
			case 'trigger_time':
				return ( ! empty( $item['ofw_email_frequency'] ) && ! empty( $item['ofw_email_frequency_unit'] ) ) ? $item['ofw_email_frequency'] . ' ' . ucfirst( $item['ofw_email_frequency_unit'] ) : '';
			case 'is_activated':
				return ! empty( $item['ofw_email_reminder_is_active'] ) ? __( 'Active', 'offers-for-woocommerce' ) : '';
			case 'actions' :
				$templateid = ! empty( $item['id'] ) ? $item['id'] : '';
				$edit_link  = admin_url( 'options-general.php?page=offers-for-woocommerce&tab=email_reminders&edit=1&is_form=1&id=' . $templateid );
				$del_link   = admin_url( 'options-general.php?page=offers-for-woocommerce&tab=email_reminders&delete=1&id=' . $templateid );
				$btn        = '<a href="' . $edit_link . '"> <button class="button ofw-er-edit "><span class="dashicons dashicons-welcome-write-blog " style="vertical-align: text-bottom;" ></span></button></a>';
				$del_btn    = '<a href="' . $del_link . '"> <button class="button ofw-er-delete "><span class="dashicons dashicons-trash " style="vertical-align: text-bottom;" ></span></button></a>';

				return $btn . $del_btn;
		}
	}
}
