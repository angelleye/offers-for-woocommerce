<?php
/**
 * Customer Offer Accepted email (plain text)
 *
 * @since	0.1.0
 * @package admin/includes/emails/plain
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

$link_insert = ( strpos( $offer_args['product_url'], '?') ) ? '&' : '?';
echo sprintf( __( 'We have accepted your offer on', 'angelleye-offers-for-woocommerce').' %s.' . __('To pay for this order please use the following link:', 'angelleye-offers-for-woocommerce').' %s', get_bloginfo( 'name' ),  $offer_args['product_url'] . $link_insert .'__aewcoapi=1&woocommerce-offer-id='.$offer_args['offer_id'].'&woocommerce-offer-uid=' .$offer_args['offer_uid'] ) . "\n\n";

if($offer_args['offer_expiration_date'])
{
    echo sprintf( '<p><strong>'. __( 'Offer expires on:', 'angelleye-offers-for-woocommerce' ).' %s.</strong></p>', date("m-d-Y", strtotime($offer_args['offer_expiration_date'])) ) . "\n\n";
}

echo "****************************************************\n";

echo sprintf( __( 'Offer ID:', 'angelleye-offers-for-woocommerce') .' %s', $offer_args['offer_id'] ) . "\n";

echo "\n";

echo __( 'Product', 'woocommerce' ) . ': ' . stripslashes($offer_args['product_title_formatted']) . "\n";
echo __( 'Quantity', 'woocommerce' ) . ': ' . number_format( $offer_args['product_qty'], 0 ) . "\n";
echo __( 'Price', 'woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_price_per'], 2 ) . "\n";
echo __( 'Subtotal', 'woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_total'], 2 );

if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '')
{
    echo "\n\n" . __( 'Offer Notes:', 'angelleye-offers-for-woocommerce' ) . ' ' . $offer_args['offer_notes'];
}

echo "\n****************************************************\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );