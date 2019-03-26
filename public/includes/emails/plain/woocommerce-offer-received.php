<?php
/**
 * Offer Received email (plain text)
 *
 * @since	0.1.0
 * @package public/includes/emails/plain
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo sprintf( __('We have received your offer on', 'offers-for-woocommerce') . ' %s. ' . __('Your offer will be processed as soon as possible.', 'offers-for-woocommerce'), get_bloginfo( 'name' ) ) . "\n\n";

echo "****************************************************\n";

echo sprintf( __( 'Offer ID:', 'offers-for-woocommerce') .' %s', $offer_args['offer_id'] ) . "\n";

echo "\n";

echo __( 'Product', 'offers-for-woocommerce' ) . ': ' . stripslashes($offer_args['product_title_formatted']) . "\n";
echo __( 'Regular Price', 'offers-for-woocommerce' ) . ': ' . wc_price( $offer_args['product']->get_regular_price() ) . "\n";
echo __( 'Quantity', 'offers-for-woocommerce' ) . ': ' . number_format( $offer_args['product_qty'], 0 ) . "\n";
echo __( 'Price', 'offers-for-woocommerce' ) . ': ' . wc_price( $offer_args['product_price_per'] ) . "\n";
echo __( 'Subtotal', 'offers-for-woocommerce' ) . ': ' . wc_price( $offer_args['product_total'] ) . "\n";

if( isset($offer_args['product_shipping_cost']) && $offer_args['product_shipping_cost'] != '0.00' && !empty($offer_args['product_shipping_cost'])) {
    $product_total = number_format(($offer_args['product_total'] + $offer_args['product_shipping_cost']), wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator()) . "\n";
    echo __( 'Shipping', 'offers-for-woocommerce' ) . ': ' . wc_price( $offer_args['product_shipping_cost'] ). "\n";
    echo __( 'Total', 'offers-for-woocommerce' ) . ': ' . wc_price( $product_total ) . "\n";
}

echo "\n\n";

if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '')
{
    echo __( 'Offer Notes:', 'offers-for-woocommerce' ) . ' ' . $offer_args['offer_notes'];
}

echo "\n****************************************************\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );