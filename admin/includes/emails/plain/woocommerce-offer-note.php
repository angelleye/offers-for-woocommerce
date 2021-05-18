<?php
/**
 * Customer Offer Note email (plain text)
 *
 * @since	0.1.0
 * @package admin/includes/emails/plain
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$offer_currency = get_post_meta($offer_args['offer_id'], 'offer_currency', true);
if (empty($offer_currency)) {
    $offer_currency = get_woocommerce_currency();
}
$product_price = angelleye_ofw_get_product_price_multi_currency($offer_args['product']->get_regular_price(), $offer_currency);

echo $email_heading . "\n\n";

if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '')
{
    echo $offer_args['offer_notes'];
}

echo "\n\n****************************************************\n";

echo sprintf( __( 'Offer ID:', 'offers-for-woocommerce') .' %s', $offer_args['offer_id'] ) . "\n";

echo "\n";

echo __( 'Product', 'offers-for-woocommerce' ) . ': ' . stripslashes($offer_args['product_title_formatted']) . "\n";
echo __( 'Regular Price', 'offers-for-woocommerce' ) . ': ' . wc_price( $product_price, array('currency' => $offer_currency)) . "\n";
echo __( 'Quantity', 'offers-for-woocommerce' ) . ': ' . $offer_args['product_qty'] . "\n";
echo __( 'Price', 'offers-for-woocommerce' ) . ': ' . wc_price( $offer_args['product_price_per'], array('currency' => $offer_currency)) . "\n";
echo __( 'Subtotal', 'offers-for-woocommerce' ) . ': ' . wc_price( $offer_args['product_total'], array('currency' => $offer_currency) );

echo "\n****************************************************\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );