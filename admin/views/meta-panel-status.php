<?php
/**
 * Admin view
 *
 *
 * @since	  0.1.0
 * @package   Angelleye_Offers_For_Woocommerce_Admin
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 * @copyright 2014 AngellEYE
 */
?>

<?php 
echo '<div class="woocommerce-offer-edit-status-inputs">';
echo '<div class="woocommerce-offer-edit-status-radio-wrap"><input required="required" autocomplete="off" type="radio" name="post_status" value="accepted-offer"'; if ($current_status_value == 'accepted-offer') echo "checked=1"; echo '> Accepted</div>';
echo '<div class="woocommerce-offer-edit-status-radio-wrap"><input required="required" autocomplete="off" type="radio" name="post_status" value="declined-offer"'; if ($current_status_value == 'declined-offer') echo "checked=1"; echo '> Declined</div>';
echo '<div class="woocommerce-offer-edit-status-radio-wrap"><input required="required" autocomplete="off" type="radio" name="post_status" value="completed-offer"'; if ($current_status_value == 'completed-offer') echo "checked=1"; echo '> Completed</div>';

echo '<input type="hidden" name="woocommerce_offer_status_metabox_noncename" id="woocommerce_offer_status_metabox_noncename" value="'; echo wp_create_nonce( 'woocommerce_offer'.$post->ID ); echo '" />';
echo '</div>';
echo '<div class="woocommerce-offer-edit-submit-btn-wrap"><input name="submit" id="submit" class="button button-primary" value="Update" type="submit"></div>';
echo '<div class="angelleye-clearfix"></div>'
?>