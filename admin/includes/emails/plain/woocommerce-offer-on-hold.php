<?php
/**
 * Customer Offer On Hold email (plain text)
 *
 * @since   1.0.1
 * @package admin/includes/emails/plain
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo __( 'Your offer has been placed on hold.', 'offers-for-woocommerce' );
echo "\n\n";

if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '')
{
    echo $offer_args['offer_notes'];
}

echo "\n\n****************************************************\n";

echo sprintf( __( 'Offer ID:', 'offers-for-woocommerce') .' %s', $offer_args['offer_id'] ) . "\n";

echo "\n";

echo __( 'Product', 'offers-for-woocommerce' ) . ': ' . stripslashes($offer_args['product_title_formatted']) . "\n";
echo __( 'Regular Price', 'offers-for-woocommerce' ) . ': ' . wc_price( $offer_args['product']->get_regular_price()) . "\n";
echo __( 'Quantity', 'offers-for-woocommerce' ) . ': ' . number_format( $offer_args['product_qty'], 0 ) . "\n";
echo __( 'Price', 'offers-for-woocommerce' ) . ': ' . wc_price( $offer_args['product_price_per']) . "\n";
echo __( 'Subtotal', 'offers-for-woocommerce' ) . ': ' . wc_price( $offer_args['product_total']);

echo "\n****************************************************\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );