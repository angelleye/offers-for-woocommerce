<?php
/**
 * Customer Offer Countered email (plain text)
 *
 * @since	0.1.0
 * @package admin/includes/emails/plain
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

$link_insert = ( strpos( $offer_args['product_url'], '?') ) ? '&' : '?';
echo sprintf( __( 'We have provided you with a counter offer on', 'offers-for-woocommerce' ) .' %s.', get_bloginfo( 'name' ) ) . "\n\n";
echo sprintf( __( 'To pay for this order please use the following link:', 'offers-for-woocommerce' ) .' %s', $offer_args['product_url']. $link_insert .'__aewcoapi=1&woocommerce-offer-id=' . $offer_args['offer_id'].'&woocommerce-offer-uid=' .$offer_args['offer_uid'] );

if(isset($offer_args['offer_expiration_date']) && $offer_args['offer_expiration_date'])
{
    echo "\n\n" . sprintf( __( 'Offer expires on:', 'offers-for-woocommerce' ).' %s</strong></p>', date("m-d-Y", strtotime($offer_args['offer_expiration_date'])) );
}

if(isset($offer_args['final_offer']) && $offer_args['final_offer'] == '1') {
    echo "\n\n" . __( 'This is a final offer.', 'offers-for-woocommerce' );
} else {
    echo "\n\n" . sprintf( __('To make a counter offer use the following link:', 'offers-for-woocommerce') .'%s', $offer_args['product_url'] . $link_insert . 'aewcobtn=1&offer-pid=' . $offer_args['offer_id'] . '&offer-uid=' . $offer_args['offer_uid']);
}

echo "\n\n****************************************************\n";

echo sprintf( __( 'Offer ID:', 'offers-for-woocommerce') .' %s', $offer_args['offer_id'] ) . "\n";

echo "\n";

echo __( 'Product', 'offers-for-woocommerce' ) . ': ' . stripslashes($offer_args['product_title_formatted']) . "\n";
echo __( 'Regular Price', 'offers-for-woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product']->get_regular_price(), wc_get_price_decimals(), wc_get_price_decimal_separator(),wc_get_price_thousand_separator() ) . "\n";
echo __( 'Quantity', 'offers-for-woocommerce' ) . ': ' . number_format( $offer_args['product_qty'], 0 ) . "\n";
echo __( 'Price', 'offers-for-woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_price_per'], wc_get_price_decimals(), wc_get_price_decimal_separator(),wc_get_price_thousand_separator() ) . "\n";
echo __( 'Subtotal', 'offers-for-woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_total'],wc_get_price_decimals(), wc_get_price_decimal_separator(),wc_get_price_thousand_separator() ) . "\n";

if( isset($offer_args['product_shipping_cost']) && $offer_args['product_shipping_cost'] != '0.00' && !empty($offer_args['product_shipping_cost'])) {
    $product_total = number_format(round($offer_args['product_total'] + $offer_args['product_shipping_cost'], 2), wc_get_price_decimals(), wc_get_price_decimal_separator(),wc_get_price_thousand_separator()) . "\n";
    echo __( 'Shipping', 'offers-for-woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_shipping_cost'], wc_get_price_decimals(), wc_get_price_decimal_separator(),wc_get_price_thousand_separator() ). "\n";
    echo __( 'Total', 'offers-for-woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $product_total, wc_get_price_decimals(), wc_get_price_decimal_separator(),wc_get_price_thousand_separator() ) . "\n";
}
if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '')
{
    echo "\n\n" . __( 'Offer Notes:', 'offers-for-woocommerce' ) . ' ' . $offer_args['offer_notes'];
}

echo "\n****************************************************\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );