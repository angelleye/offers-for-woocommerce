<?php
/**
 * Customer Offer Countered email
 *
 * @since	0.1.0
 * @package admin/includes/emails
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>
<?php printf( '<strong>'. _e('We have provided you with a counter offer on','angelleye-offers-for-woocommerce').' %s.</strong><br />', get_bloginfo( 'name' ) ) ;?>
<?php _e('To pay for this order please use the following link:', 'angelleye-offers-for-woocommerce');?>&nbsp;<a style="background:#EFEFEF; color:#161616; padding:8px 15px; margin:10px; border:1px solid #CCCCCC; text-decoration:none; " href="<?php echo $offer_args['product_url'];?><?php echo ( strpos( $offer_args['product_url'], '?') ) ? '&' : '?';?><?php echo '__aewcoapi=1&woocommerce-offer-id=' . $offer_args['offer_id'].'&woocommerce-offer-uid=' . $offer_args['offer_uid']; ?>"><span style="border-bottom:1px dotted #666; "><?php echo __( 'Click to Pay', 'angelleye-offers-for-woocommerce' ); ?></span></a>
<?php if(isset($offer_args['final_offer']) && $offer_args['final_offer'] == '1') {
    echo '<br><br><strong>'. __( 'This is a final offer.', 'angelleye-offers-for-woocommerce' ) .'</strong>';
} else { ?>
    <br><br><?php _e('To make a counter offer use the following link:', 'angelleye-offers-for-woocommerce'); ?>&nbsp;<a style="background:#EFEFEF; color:#161616; padding:8px 15px; margin:10px; border:1px solid #CCCCCC; text-decoration:none; " href="<?php echo $offer_args['product_url'] ;?><?php echo ( strpos( $offer_args['product_url'], '?') ) ? '&' : '?';?><?php echo 'aewcobtn=1&offer-pid='.$offer_args['offer_id']. '&offer-uid=' .$offer_args['offer_uid']; ?>"><span style="border-bottom:1px dotted #666; "><?php echo __( 'Click to Counter', 'angelleye-offers-for-woocommerce' ); ?></span></a>
<?php } ?>

<?php if(isset($offer_args['offer_expiration_date']) && $offer_args['offer_expiration_date']) {
    printf( '<br><br>'. __('Offer expires on:', 'angelleye-offers-for-woocommerce').' %s', date("m-d-Y", strtotime($offer_args['offer_expiration_date'])) );
}?>

<h2><?php echo __( 'Offer ID:', 'angelleye-offers-for-woocommerce' ) . ' ' . $offer_args['offer_id']; ?> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', time() ), date_i18n( wc_date_format(), time() ) ); ?>)</h2>

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
        <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Subtotal', 'woocommerce' ); ?></th>
        <td style="text-align:left; border: 1px solid #eee; border-top-width: 4px; "><?php echo get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_total'], 2 ); ?></td>
    </tr>
    </tfoot>
</table>

<?php if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '') { echo '<h4>'. __('Counter Offer Notes:', 'angelleye-offers-for-woocommerce') .'</h4>'. stripslashes($offer_args['offer_notes']); } ?>

<?php do_action( 'woocommerce_email_footer' ); ?>