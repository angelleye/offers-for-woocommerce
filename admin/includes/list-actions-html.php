<?php
/**
 * Admin List View - Actions Column Html
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @since	  0.1.0
 * @package   Angelleye_Offers_For_Woocommerce_Admin
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 */
?>
<div class="woocommerce-offer-list-actions-wrap">
    <ul>
<?php
    if(isset($post_status) && $post_status == 'accepted-offer')
    {
        echo '<li><a href="'.get_edit_post_link( $post_id).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post_id.'">' . __('Manage&nbsp;Offer') . '</a></li>';
        echo '<li><a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" title="Set Offer Status to Declined" id="woocommerce-offer-post-action-link-decline-id-'.$post_id.'" data-target="'.$post_id.'">' . __('Decline') . '</a></li>';
    }
    elseif(isset($post_status) && $post_status == 'countered-offer')
    {
        echo '<li><a href="'.get_edit_post_link( $post_id).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post_id.'">' . __('Manage&nbsp;Offer') . '</a></li>';
        echo '<li><a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" title="Set Offer Status to Declined" id="woocommerce-offer-post-action-link-decline-id-'.$post_id.'" data-target="'.$post_id.'">' . __('Decline') . '</a></li>';
    }
    elseif(isset($post_status) && $post_status == 'declined-offer')
    {
        echo '<li><a href="'.get_edit_post_link( $post_id).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post_id.'">' . __('Manage&nbsp;Offer') . '</a></li>';
    }
    elseif(isset($post_status) && $post_status == 'on-hold-offer')
    {
        echo '<li><a href="'.get_edit_post_link( $post_id).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post_id.'">' . __('Manage&nbsp;Offer') . '</a></li>';
    }
    elseif(isset($post_status) && $post_status == 'expired-offer')
    {
        echo '<li><a href="'.get_edit_post_link( $post_id).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post_id.'">' . __('Manage&nbsp;Offer') . '</a></li>';
    }
    elseif(isset($post_status) && $post_status == 'completed-offer')
    {
        echo '<li><a href="'.get_edit_post_link( $post_id).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post_id.'">' . __('Manage&nbsp;Offer') . '</a></li>';
    }
    elseif(isset($post_status) && ( $post_status == 'publish' || $post_status == 'buyercountered-offer') )
    {
        echo '<li><a href="'.get_edit_post_link( $post_id).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post_id.'">' . __('Manage&nbsp;Offer') . '</a></li>';
        echo '<li><a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-accept" title="Set Offer Status to Accepted" id="woocommerce-offer-post-action-link-accept-id-'.$post_id.'" data-target="'.$post_id.'">' . __('Accept') . '</a></li>';
        echo '<li><a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" title="Set Offer Status to Declined" id="woocommerce-offer-post-action-link-decline-id-'.$post_id.'" data-target="'.$post_id.'">' . __('Decline') . '</a></li>';
    }
    ?>
    </ul>
</div>