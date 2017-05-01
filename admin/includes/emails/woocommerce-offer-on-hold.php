<?php
/**
 * Customer Offer On Hold email
 *
 * @since	1.0.1
 * @package admin/includes/emails
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>
    <p><strong><?php _e( 'Your offer has been placed on hold.', 'offers-for-woocommerce' ); ?></strong></p>

<?php if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '') { echo '<h4>'. __( 'Offer Notes:', 'offers-for-woocommerce' ) .'</h4>'. stripslashes($offer_args['offer_notes']); } ?>

    <h2><?php echo __( 'Offer ID:', 'offers-for-woocommerce' ) . ' ' . $offer_args['offer_id']; ?> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', time() ), date_i18n( wc_date_format(), time() ) ); ?>)</h2>

    <table cellspacing="0" cellpadding="6" style="width: 100%;">
        <thead>
        <tr>
            <th scope="col" style="text-align:left; background-color: #f2f2f2; border-bottom: 1px solid #ddd;"><?php _e( 'Product', 'offers-for-woocommerce' ); ?></th>
            <th scope="col" style="text-align:left; background-color: #f2f2f2; border-bottom: 1px solid #ddd;"><?php _e( 'Regular Price', 'offers-for-woocommerce' ); ?></th>
            <th scope="col" style="text-align:left; background-color: #f2f2f2; border-bottom: 1px solid #ddd;"><?php _e( 'Quantity', 'offers-for-woocommerce' ); ?></th>
            <th scope="col" style="text-align:left; background-color: #f2f2f2; border-bottom: 1px solid #ddd;"><?php _e( 'Price', 'offers-for-woocommerce' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo stripslashes($offer_args['product_title_formatted']); ?></td>
            <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product']->get_regular_price(), 2, '.', '' ); ?></td>
            <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo number_format( $offer_args['product_qty'], 0 ); ?></td>
            <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_price_per'], 2, '.', '' ); ?></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th scope="row" colspan="3"  style="text-align:left; border-bottom: 1px solid #ddd;"><?php _e( 'Subtotal', 'offers-for-woocommerce' ); ?></th>
            <td style="border-bottom: 1px solid #ddd; text-align:center"><?php echo get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_total'], 2 ); ?></td>
        </tr>
        </tfoot>
    </table>

<?php do_action( 'woocommerce_email_footer' ); ?>