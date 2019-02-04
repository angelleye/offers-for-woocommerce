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
echo sprintf( __( 'We have accepted your offer on', 'offers-for-woocommerce').' %s.' . __('To pay for this order please use the following link:', 'offers-for-woocommerce').' %s', get_bloginfo( 'name' ),  $offer_args['product_url'] . $link_insert .'__aewcoapi=1&woocommerce-offer-id='.$offer_args['offer_id'].'&woocommerce-offer-uid=' .$offer_args['offer_uid'] ) . "\n\n";

if(isset($offer_args['offer_expiration_date']) && $offer_args['offer_expiration_date'])
{
    echo sprintf( '<p><strong>'. __( 'Offer expires on:', 'offers-for-woocommerce' ).' %s.</strong></p>', date("m-d-Y", strtotime($offer_args['offer_expiration_date'])) ) . "\n\n";
}

echo "****************************************************\n";

echo sprintf( __( 'Offer ID:', 'offers-for-woocommerce') .' %s', $offer_args['offer_id'] ) . "\n";

echo "\n";

echo __( 'Product', 'offers-for-woocommerce' ) . ': ' . stripslashes($offer_args['product_title_formatted']) . "\n";
echo __( 'Regular Price', 'offers-for-woocommerce' ) . ': ' . wc_price($offer_args['product']->get_regular_price()) . "\n";
echo __( 'Quantity', 'offers-for-woocommerce' ) . ': ' . number_format( $offer_args['product_qty'], 0 ) . "\n";
echo __( 'Price', 'offers-for-woocommerce' ) . ': ' . wc_price($offer_args['product_price_per']) . "\n";
echo __( 'Subtotal', 'offers-for-woocommerce' ) . ': ' . wc_price($offer_args['product_total']);

if( isset($offer_args['product_shipping_cost']) && $offer_args['product_shipping_cost'] != '0.00' && !empty($offer_args['product_shipping_cost'])) {
    $product_total = number_format(round($offer_args['product_total'] + $offer_args['product_shipping_cost'], 2), 2, '.', '') . "\n";
    echo __( 'Shipping', 'offers-for-woocommerce' ) . ': ' . wc_price( $offer_args['product_shipping_cost']). "\n";
    echo __( 'Total', 'offers-for-woocommerce' ) . ': ' . wc_price( $product_total) . "\n";
}
if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '')
{
    echo "\n\n" . __( 'Offer Notes:', 'offers-for-woocommerce' ) . ' ' . $offer_args['offer_notes'];
}

echo "\n****************************************************\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );