<?php
/**
 * Customer Offer Note email (plain text)
 *
 * @since	0.1.0
 * @package admin/includes/emails/plain
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '')
{
    echo $offer_args['offer_notes'];
}

echo "\n\n****************************************************\n";

echo sprintf( __( 'Offer ID:', 'offers-for-woocommerce') .' %s', $offer_args['offer_id'] ) . "\n";

echo "\n";

echo __( 'Product', 'offers-for-woocommerce' ) . ': ' . stripslashes($offer_args['product_title_formatted']) . "\n";
echo __( 'Regular Price', 'offers-for-woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product']->get_regular_price(), wc_get_price_decimals(), wc_get_price_decimal_separator(),wc_get_price_thousand_separator() ) . "\n";
echo __( 'Quantity', 'offers-for-woocommerce' ) . ': ' . number_format( $offer_args['product_qty'], 0 ) . "\n";
echo __( 'Price', 'offers-for-woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_price_per'], wc_get_price_decimals(), wc_get_price_decimal_separator(),wc_get_price_thousand_separator() ) . "\n";
echo __( 'Subtotal', 'offers-for-woocommerce' ) . ': ' . get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_total'], wc_get_price_decimals(), wc_get_price_decimal_separator(),wc_get_price_thousand_separator());

echo "\n****************************************************\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );