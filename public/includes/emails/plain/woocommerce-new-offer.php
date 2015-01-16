<?php
/**
 * New Offer email (plain text)
 *
 * @since	0.1.0
 * @package public/includes/emails/plain
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo sprintf( __( 'New offer submitted on %s. To manage this offer please visit the following url: %s', 'offers-for-woocommerce' ), get_bloginfo( 'name' ),  admin_url( 'post.php?post='. $offer_args['offer_id']  .'&action=edit' ) ) . "\n\n";

echo "****************************************************\n\n";

echo sprintf( __( 'Offer ID: %s', 'offers-for-woocommerce'), $offer_args['offer_id'] ) . "\n";
echo sprintf( __( 'Offer date: %s', 'offers-for-woocommerce'), date("Y-m-d H:i:s", current_time('timestamp', 0 )) ) . "\n";

echo "\n";

echo __( 'Product', 'woocommerce' ) . ': ' . $offer_args['product']->post_title;
echo __( 'Quantity', 'woocommerce' ) . ': ' . $offer_args['product_qty'];
echo __( 'Price', 'woocommerce' ) . ': ' .$offer_args['product_price_per'];
echo "\n\n";
echo 'Subtotal' . ': ' . $offer_args['product_total'];
echo "\n\n";

if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '')
{
    echo __( 'Offer Notes:', 'offers-for-woocommerce' ) . $offer_args['offer_notes'];
}

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );