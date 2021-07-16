<?php

class Angelleye_Offers_For_Woocommerce_Paypal{

	public $ec_debug;

	public $test_mode;

	public $api_username;

	public $api_password;

	public $api_signature;

	public $paypal;

	public function __construct() {

		$method_id = 'paypal';
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
		$payment_method     = isset( $available_gateways[ $method_id ] ) ? $available_gateways[ $method_id ] : false;

		$test_mode = !empty($payment_method->settings['testmode']) ? $payment_method->settings['testmode'] : '';
		$api_username = !empty($payment_method->settings['api_username']) ? $payment_method->settings['api_username'] : '';
		$api_password = !empty($payment_method->settings['api_password']) ? $payment_method->settings['api_password'] : '';
		$api_signature = !empty($payment_method->settings['api_signature']) ? $payment_method->settings['api_signature'] : '';

		if( !empty($test_mode) && $test_mode == 'yes' ) {
			$api_username = !empty($payment_method->settings['sandbox_api_username']) ? $payment_method->settings['sandbox_api_username'] : '';
			$api_password = !empty($payment_method->settings['sandbox_api_password']) ? $payment_method->settings['sandbox_api_password'] : '';
			$api_signature = !empty($payment_method->settings['sandbox_api_signature']) ? $payment_method->settings['sandbox_api_signature'] : '';
		}

		$this->test_mode = $test_mode;
		$this->api_username = $api_username;
		$this->api_password = $api_password;
		$this->api_signature = $api_signature;

		$PayPalConfig = array(
			'Sandbox' => $this->test_mode,
			'APIUsername' => $this->api_username,
			'APIPassword' => $this->api_password,
			'APISignature' => $this->api_signature
		);

		if ( !class_exists('Angelleye_PayPal_WC') ) {
			require_once( OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/lib/paypal-php-library/paypal.class.php' );
		}

		$this->ec_debug = 'yes';
		$this->paypal = new Angelleye_PayPal_WC($PayPalConfig);
	}

	public function do_void( $order_id ) {

		if(empty($order_id)) {
			return;
		}

		$order = wc_get_order( $order_id );
		$transaction_id = $order->get_transaction_id();

		$DVFields = array(
			'authorizationid' => $transaction_id,
			'note' => '',
			'msgsubid' => ''
		);
		$PayPalRequestData = array('DVFields' => $DVFields);
		$do_void_result = $this->paypal->DoVoid($PayPalRequestData);

		$ack = strtoupper($do_void_result["ACK"]);

		if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {

			$order->add_order_note(__('PayPal DoVoid', 'paypal-for-woocommerce') . ' ( Response Code: ' . $do_void_result["ACK"] . ", " . ' DoVoid AUTHORIZATIONID: ' . $do_void_result['AUTHORIZATIONID'] . ' )' );
			$order->set_transaction_id($do_void_result['AUTHORIZATIONID']);
		} else {
			$ErrorCode = urldecode($do_void_result["L_ERRORCODE0"]);
			$ErrorShortMsg = urldecode($do_void_result["L_SHORTMESSAGE0"]);
			$ErrorLongMsg = urldecode($do_void_result["L_LONGMESSAGE0"]);
			$ErrorSeverityCode = urldecode($do_void_result["L_SEVERITYCODE0"]);
			$this->ec_add_log(__('PayPal DoVoid API call failed. ', 'paypal-for-woocommerce'));
			$this->ec_add_log(__('Detailed Error Message: ', 'paypal-for-woocommerce') . $ErrorLongMsg);
			$this->ec_add_log(__('Short Error Message: ', 'paypal-for-woocommerce') . $ErrorShortMsg);
			$this->ec_add_log(__('Error Code: ', 'paypal-for-woocommerce') . $ErrorCode);
			$this->ec_add_log(__('Error Severity Code: ', 'paypal-for-woocommerce') . $ErrorSeverityCode);
			$order->add_order_note(__('PayPal DoVoid API call failed. ', 'offers-for-woocommerce') . ' ( Detailed Error Message: ' . $ErrorLongMsg . ", " . ' Short Error Message: ' . $ErrorShortMsg . ' )' . ' Error Code: ' . $ErrorCode . ' )' .  ' Error Severity Code: ' . $ErrorSeverityCode . ' )' );
		}
	}

	public function ec_add_log($message, $level = 'info') {
		if ($this->ec_debug == 'yes') {
			if (version_compare(WC_VERSION, '3.0', '<')) {
				if (empty($this->log)) {
					$this->log = new WC_Logger();
				}
				$this->log->add($this->payment_method, $message);
			} else {
				if (empty($this->log)) {
					$this->log = wc_get_logger();
				}
				$this->log->log($level, $message, array('source' => $this->payment_method));
			}
		}
	}
}