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

echo sprintf(__('<p><strong>We have declined your offer on %s.</strong></p>'), get_bloginfo('name')) . "\n\n";

if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '')
{
    echo "<strong>". __( 'Offer Notes: ', 'offers-for-woocommerce' ) ."</strong>". $offer_args['offer_notes'] . "\n\n";
}

echo "****************************************************\n\n";

echo sprintf( __( 'Offer ID: %s', 'angelleye_offers_for_woocommerce'), $offer_args['offer_id'] ) . "\n";

echo "\n";

echo "Product: " . $offer_args['product']->post_title . "\n";
echo "Quantity: " . $offer_args['product_qty'] . "\n";
echo "Price Per: " . $offer_args['product_price_per'] . "\n";
echo "Subtotal: " . $offer_args['product_total'] . "\n";

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );