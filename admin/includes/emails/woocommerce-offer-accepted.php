<?php
/**
 * Customer Offer Accepted email
 *
 * @since	0.1.0
 * @package admin/includes/emails
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php $link_insert = ( strpos( $offer_args['product_url'], '?') ) ? '&' : '?'; ?>

<?php printf( __( '<p><strong>We have accepted your offer on %s.</strong><br />To pay for this order please use the following link:</p> %s', 'angelleye_offers_for_woocommerce' ), get_bloginfo( 'name' ), '<a style="background:#EFEFEF; color:#161616; padding:8px 15px; margin:10px; border:1px solid #CCCCCC; text-decoration:none; " href="'.$offer_args['product_url'] . $link_insert .'__aewcoapi=1&woocommerce-offer-id='.$offer_args['offer_id'].'&woocommerce-offer-uid=' .$offer_args['offer_uid'].'"><span style="border-bottom:1px dotted #666; ">' . __( 'Click to Pay', 'offers-for-woocommerce' ) . '</span></a>' ); ?>

<?php if($offer_args['offer_expiration_date']) {
    printf( __( '<br><br>Offer expires on: %s', 'angelleye_offers_for_woocommerce' ), date("m-d-Y", strtotime($offer_args['offer_expiration_date'])) );
}?>

<h2><?php echo __( 'Offer ID:', 'angelleye_offers_for_woocommerce' ) . ' ' . $offer_args['offer_id']; ?> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', time() ), date_i18n( wc_date_format(), time() ) ); ?>)</h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <thead>
    <tr>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'woocommerce' ); ?></th>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'woocommerce' ); ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?php echo stripslashes($offer_args['product_title_formatted']); ?></td>
        <td><?php echo number_format( $offer_args['product_qty'], 0 ); ?></td>
        <td><?php echo get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_price_per'], 2 ); ?></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee;"><?php echo 'Subtotal'; ?></th>
        <td style="text-align:left; border: 1px solid #eee; border-top-width: 4px; "><?php echo get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_total'], 2 ); ?></td>
    </tr>
    </tfoot>
</table>

<?php if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '') { echo '<h4>'. __( 'Offer Notes:', 'angelleye_offers_for_woocommerce' ) .'</h4>'. stripslashes($offer_args['offer_notes']); } ?>

<?php do_action( 'woocommerce_email_footer' ); ?>