<?php
/**
 * New Counter Offer email
 *
 * @since	0.1.0
 * @package public/includes/emails
 * @author  AngellEYE <andrew@angelleye.com>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php printf( '<p><strong>'. __('New counter offer submitted on', 'offers-for-woocommerce'). ' %s.</strong><br />' . __('To manage this counter offer please use the following link:', 'offers-for-woocommerce') . '</p> %s', get_bloginfo( 'name' ), '<a style="background:#EFEFEF; color:#161616; padding:8px 15px; margin:10px; border:1px solid #CCCCCC; text-decoration:none; " href="'. admin_url( 'post.php?post='. $offer_args['offer_id']  .'&action=edit' ) .'"><span style="border-bottom:1px dotted #666; ">' . __( 'Manage Counter Offer', 'offers-for-woocommerce' ) . '</span></a>' ); ?>

<h2><?php echo __( 'Offer ID:', 'offers-for-woocommerce' ) . ' ' . $offer_args['offer_id']; ?> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', time() ), date_i18n( wc_date_format(), time() ) ); ?>)</h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <thead>
    <tr>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'offers-for-woocommerce' ); ?></th>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'offers-for-woocommerce' ); ?></th>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'offers-for-woocommerce' ); ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; padding:12px;"><?php echo stripslashes($offer_args['product_title_formatted']); ?></td>
        <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; padding:12px;"><?php echo number_format( $offer_args['product_qty'], 0 ); ?></td>
        <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; padding:12px;"><?php echo get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_price_per'], 2, '.', '' ); ?></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; border-top-width: 4px; "><?php _e( 'Subtotal', 'offers-for-woocommerce' ); ?></th>
        <td style="text-align:left; border: 1px solid #eee; border-top-width: 4px; "><?php echo get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_total'], 2, '.', '' ); ?></td>
    </tr>
    <tr>
        <?php 
        if( isset($offer_args['product_shipping_cost']) && $offer_args['product_shipping_cost'] != '0.00' && !empty($offer_args['product_shipping_cost'])) {
            $product_total = number_format(round($offer_args['product_total'] + $offer_args['product_shipping_cost'], 2), 2, '.', '');
          ?>
            <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; border-top-width: 4px; "><?php _e( 'Shipping', 'offers-for-woocommerce' ); ?></th>
            <td style="text-align:left; border: 1px solid #eee; border-top-width: 4px; "><?php echo get_woocommerce_currency_symbol() . ' ' . number_format( $offer_args['product_shipping_cost'], 2, '.', '' ); ?></td>
           <?php 
        }
        ?>
    </tr>
    <tr>
        <?php 
        if( isset($offer_args['product_shipping_cost']) && $offer_args['product_shipping_cost'] != '0.00' && !empty($offer_args['product_shipping_cost'])) {
          ?>
            <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; border-top-width: 4px; "><?php _e( 'Total', 'offers-for-woocommerce' ); ?></th>
            <td style="text-align:left; border: 1px solid #eee; border-top-width: 4px; "><?php echo get_woocommerce_currency_symbol() . ' ' . number_format( $product_total, 2, '.', '' ); ?></td>
           <?php 
        }
        ?>
    </tr>
    </tfoot>
</table>
<?php if( !$offer_args['is_anonymous_communication_enable'] ) { ?>
<h4><?php echo __('Offer Contact Details:', 'offers-for-woocommerce'); ?></h4>
<?php echo (isset($offer_args['offer_name']) && $offer_args['offer_name'] != '') ? '<strong>' . __('Name:', 'offers-for-woocommerce') . '&nbsp;</strong>'.stripslashes($offer_args['offer_name']) : ''; ?>
<?php echo (isset($offer_args['offer_company_name']) && $offer_args['offer_company_name'] != '') ? '<br /><strong>' . __('Company Name:', 'offers-for-woocommerce') . '&nbsp;</strong>'.stripslashes($offer_args['offer_company_name']) : ''; ?>
<?php echo (isset($offer_args['offer_email']) && $offer_args['offer_email'] != '') ? '<br /><strong>' . __('Email:', 'offers-for-woocommerce') . '&nbsp;</strong>'.stripslashes($offer_args['offer_email']) : ''; ?>
<?php echo (isset($offer_args['offer_phone']) && $offer_args['offer_phone'] != '') ? '<br /><strong>' . __('Phone:', 'offers-for-woocommerce') . '&nbsp;</strong>'.stripslashes($offer_args['offer_phone']) : ''; ?>
<?php } ?>
<?php do_action('make_offer_email_display_custom_field_after_buyer_contact_details', $offer_args['offer_id']); ?>

<?php if(isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '') { echo '<h4>'. __( 'Offer Notes:', 'offers-for-woocommerce' ) .'</h4>'. stripslashes($offer_args['offer_notes']); } ?>

<?php do_action( 'woocommerce_email_footer' ); ?>