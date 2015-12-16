<?php
/**
 * Customer Offer Declined email (plain text)
 *
 * @since	0.1.0
 * @package admin/includes/emails/plain
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

$link_insert = ( strpos( $offer_args['product_url'], '?') ) ? '&' : '?';
if( isset($offer_args['coupon_code']) && !empty($offer_args['coupon_code']) ) {
    echo sprintf( __( 'We have declined your offer on', 'angelleye_offers_for_woocommerce' ) . ' %s, however, we do have an active coupon code that we can offer you for use on our website.', get_bloginfo( 'name' ) ) . "\n\n";
    
    echo "****************************************************\n";
    echo sprintf(  __('Coupon Code: ', 'offers-for-woocommerce'). ' %s', $offer_args['coupon_code'] ). "\n";
} else {
    echo sprintf( __( 'We have declined your offer on', 'angelleye_offers_for_woocommerce' ) . ' %s.', get_bloginfo( 'name' ) ) . "\n\n";

    echo "****************************************************\n";
}

echo sprintf( __( 'Offer ID:', 'offers-for-woocommerce') .' %s', $offer_args['offer_id'] ) . "\n";

echo "\n";

echo __( 'Product', 'woocommerce' ) . ': ' . stripslashes($offer_args['product_title_formatted']) . "\n";
echo __( 'Quantity', 'woocommerce' ) . ': ' . number_format( $offer_args['product_qty'], 0 ) . "\n";
echo __( 'Price', 'woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_price_per'], 2 ) . "\n";
echo __( 'Subtotal', 'woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_total'], 2 );

if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '')
{
    echo "\n\n" . __( 'Offer Notes:', 'angelleye_offers_for_woocommerce' ) . ' '. $offer_args['offer_notes'];
}

echo "\n****************************************************\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );