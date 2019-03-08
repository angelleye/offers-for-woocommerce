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

<?php printf( '<p style="font-size: 16px;text-align: center;font-family: inherit;"><strong>'. __('We have accepted your offer on', 'offers-for-woocommerce').' %s.</strong><br />'. __('To pay for this order please use the following link:', 'offers-for-woocommerce').'</p> %s', get_bloginfo( 'name' ), '<p style="text-align:center"><a style="background-color: #008CBA;border: none;color: white;padding: 12px 20px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;" href="'.$offer_args['product_url'] . $link_insert .'__aewcoapi=1&woocommerce-offer-id='.$offer_args['offer_id'].'&woocommerce-offer-uid=' .$offer_args['offer_uid'].'">' . __( 'Click to Pay', 'offers-for-woocommerce' ) . '</a></p>' ); ?>

<?php if( isset($offer_args['offer_expiration_date']) && $offer_args['offer_expiration_date']) {
    printf( '<br><br><p style="font-size: 16px;text-align: center;font-family: inherit;">'. __('Offer expires on:', 'offers-for-woocommerce').' %s' .'</p>', date("m-d-Y", strtotime($offer_args['offer_expiration_date'])) );
}?>
<br>
<h2 style="text-align:center"><?php echo __( 'Offer ID:', 'offers-for-woocommerce' ) . ' ' . $offer_args['offer_id']; ?> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', time() ), date_i18n( wc_date_format(), time() ) ); ?>)</h2>

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
        <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo wc_price( $offer_args['product']->get_regular_price()); ?></td>
        <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo $offer_args['product_qty']; ?></td>
        <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo wc_price( $offer_args['product_price_per']); ?></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th scope="row" colspan="3" style="text-align:left; border-bottom: 1px solid #ddd;"><?php _e( 'Subtotal', 'offers-for-woocommerce' ); ?></th>
        <td style="border-bottom: 1px solid #ddd; text-align:center"><?php echo wc_price( $offer_args['product_total'] ); ?></td>
    </tr>
    <tr>
        <?php 
        if( isset($offer_args['product_shipping_cost']) && $offer_args['product_shipping_cost'] != '0.00' && !empty($offer_args['product_shipping_cost'])) {
            $product_total = ($offer_args['product_total'] + $offer_args['product_shipping_cost']);
          ?>
            <th scope="row" colspan="3" style="text-align:left; border-bottom: 1px solid #ddd;"><?php _e( 'Shipping', 'offers-for-woocommerce' ); ?></th>
            <td style="border-bottom: 1px solid #ddd; text-align:center"><?php echo wc_price( $offer_args['product_shipping_cost']); ?></td>
           <?php 
        }
        ?>
    </tr>
    <tr>
        <?php 
        if( isset($offer_args['product_shipping_cost']) && $offer_args['product_shipping_cost'] != '0.00' && !empty($offer_args['product_shipping_cost'])) {
          ?>
            <th scope="row" colspan="2" style="text-align:left; border-bottom: 1px solid #ddd;"><?php _e( 'Total', 'offers-for-woocommerce' ); ?></th>
            <td style="border-bottom: 1px solid #ddd; text-align:center"><?php echo wc_price( $product_total); ?></td>
           <?php 
        }
        ?>
    </tr>
    </tfoot>
</table>

<?php if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '') { echo '<h4>'. __( 'Offer Notes:', 'offers-for-woocommerce') .'</h4>'. stripslashes($offer_args['offer_notes']); } ?>

<?php do_action( 'woocommerce_email_footer' ); ?>