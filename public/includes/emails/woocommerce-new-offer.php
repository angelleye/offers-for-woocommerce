<?php
/**
 * New Offer email
 *
 * @since	0.1.0
 * @package public/includes/emails
 * @author  AngellEYE <andrew@angelleye.com>
 */
if (!defined('ABSPATH')) exit; 
?>

<?php do_action('woocommerce_email_header', $email_heading, $email); ?>
<?php if (is_plugin_active('offers-for-woocommerce-wc-vendors/offers-for-woocommerce-wc-vendors.php')  && is_plugin_active('wc-vendors-pro/wcvendors-pro.php')) {
    $vendor_dashboard_page_url = angelleye_get_vendor_dashboard_page_url();
    printf('<div class="ofw_email_button"><p style="font-size: 16px;text-align: center;font-family: inherit;"><strong>' . __('New offer submitted on', 'offers-for-woocommerce') . ' %s.</strong><br />' . __('To manage this offer please use the following link:', 'offers-for-woocommerce') . '</p> %s', get_bloginfo('name'), '<a style="background-color: #008CBA;border: none;color: white;padding: 12px 20px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;" href="' . $vendor_dashboard_page_url . 'woocommerce_offer/edit/' . $offer_args['offer_id'] . '">' . __('Manage Offer', 'offers-for-woocommerce') . '</a>');
} else {
    printf(
        '<div class="ofw_email_button"><p style="font-size: 16px;text-align: center;font-family: inherit;"><strong>' . __('New offer submitted on', 'offers-for-woocommerce') . ' %s.</strong><br />' . __('To manage this offer please use the following link:', 'offers-for-woocommerce') . '</p> %s',
        get_bloginfo('name'),
        '<p style="text-align: center;"><a style="background-color: #008CBA;border: none;color: white;padding: 12px 20px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;" href="' . admin_url('post.php?post=' . $offer_args['offer_id']  . '&action=edit') . '">' . __('Manage Offer', 'offers-for-woocommerce') . '</a>
							 <a style="background-color: #f44336;border: none;color: white;padding: 12px 20px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;" href="' . admin_url('post.php?post=' . $offer_args['offer_id']  . '&action=edit&from_email_action=declineOfferFromGrid&ofw_from_email=true&targetID=' . $offer_args['offer_id']) . '">' . __('Decline Offer', 'offers-for-woocommerce') . '</a>
							 <a style="background-color: #4CAF50;border: none;color: white;padding: 12px 20px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;" href="' . admin_url('post.php?post=' . $offer_args['offer_id']  . '&action=edit&from_email_action=approveOfferFromGrid&ofw_from_email=true&targetID=' . $offer_args['offer_id']) . '">' . __('Approve Offer', 'offers-for-woocommerce') . '</a><p></div">'
    );
} ?>
<br>
<h2 style="text-align:center"><?php echo __('Offer ID:', 'offers-for-woocommerce') . ' ' . $offer_args['offer_id']; ?> (<?php printf('<time datetime="%s">%s</time>', date_i18n('c', time()), date_i18n(wc_date_format(), time())); ?>)</h2>
<br>
<br>
<table cellspacing="0" cellpadding="6" style="width: 100%;">
    <thead>
        <tr>
            <th scope="col" style="text-align:left; background-color: #f2f2f2; border-bottom: 1px solid #ddd;"><?php _e('Product', 'offers-for-woocommerce'); ?></th>
            <th scope="col" style="text-align:left; background-color: #f2f2f2; border-bottom: 1px solid #ddd;"><?php _e('Regular Price', 'offers-for-woocommerce'); ?></th>
            <th scope="col" style="text-align:left; background-color: #f2f2f2; border-bottom: 1px solid #ddd;"><?php _e('Quantity', 'offers-for-woocommerce'); ?></th>
            <th scope="col" style="text-align:left; background-color: #f2f2f2; border-bottom: 1px solid #ddd;"><?php _e('Price', 'offers-for-woocommerce'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo stripslashes($offer_args['product_title_formatted']); ?></td>
            <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo wc_price($offer_args['product']->get_regular_price()); ?></td>
            <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo $offer_args['product_qty']; ?></td>
            <td style="text-align:left; vertical-align:middle;  padding:12px; border-bottom: 1px solid #ddd;"><?php echo wc_price($offer_args['product_price_per']); ?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th scope="row" colspan="3" style="text-align:left; border-bottom: 1px solid #ddd;"><?php _e('Subtotal', 'offers-for-woocommerce'); ?></th>
            <td style="border-bottom: 1px solid #ddd; text-align:center"><?php echo wc_price($offer_args['product_total']); ?></td>
        </tr>
    </tfoot>
</table>
<?php if (!$offer_args['is_anonymous_communication_enable']) { ?>
    <h4><?php echo __('Offer Contact Details:', 'offers-for-woocommerce'); ?></h4>
    <?php echo (isset($offer_args['offer_name']) && $offer_args['offer_name'] != '') ? '<strong>' . __('Name:', 'offers-for-woocommerce') . '&nbsp;</strong>' . stripslashes($offer_args['offer_name']) : ''; ?>
    <?php echo (isset($offer_args['offer_company_name']) && $offer_args['offer_company_name'] != '') ? '<br /><strong>' . __('Company Name:', 'offers-for-woocommerce') . '&nbsp;</strong>' . stripslashes($offer_args['offer_company_name']) : ''; ?>
    <?php echo (isset($offer_args['offer_email']) && $offer_args['offer_email'] != '') ? '<br /><strong>' . __('Email:', 'offers-for-woocommerce') . '&nbsp;</strong>' . stripslashes($offer_args['offer_email']) : ''; ?>
    <?php echo (isset($offer_args['offer_phone']) && $offer_args['offer_phone'] != '') ? '<br /><strong>' . __('Phone:', 'offers-for-woocommerce') . '&nbsp;</strong>' . stripslashes($offer_args['offer_phone']) : ''; ?>
<?php } ?>
<?php do_action('make_offer_email_display_custom_field_after_buyer_contact_details', $offer_args['offer_id']); ?>
<?php if (isset($offer_args['offer_notes']) && $offer_args['offer_notes'] != '') {
    echo '<h4>' . __('Offer Notes:', 'offers-for-woocommerce') . '</h4>' . stripslashes($offer_args['offer_notes']);
} ?>
<?php do_action('woocommerce_email_footer'); ?>