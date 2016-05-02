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
 */
?>
<form name="addOfferNoteForm" id="addOfferNoteForm" autocomplete="off" action="<?php echo admin_url('post.php?post='.$post->ID.'&noheader=true'); ?>" method="post">
<?php
echo '<div class="woocommerce-offer-edit-addnote-inputs">';
    echo '<textarea name="note_text" id="angelleye-woocommerce-offers-ajax-addnote-text" autocomplete="off"></textarea>';
echo '</div>';
echo '<div class="woocommerce-offer-edit-addnote-btn-wrap">';
    echo '<div class="private-note-checkbox-wrap"><input type="checkbox" name="note_send_to_buyer" id="angelleye-woocommerce-offers-ajax-addnote-send-to-buyer" value="1" autocomplete="off"><label for="angelleye-woocommerce-offers-ajax-addnote-send-to-buyer">' . __('Send to buyer', 'offers-for-woocommerce') . '</label></div>';
    echo '<div class="addnote-btn-wrap"><button name="addnote-btn" id="angelleye-woocommerce-offers-ajax-addnote-btn" class="button" type="button" data-target="'.$post->ID.'">' . __('Add', 'offers-for-woocommerce') . '</button></div>';
echo '</div>';
?>
</form>