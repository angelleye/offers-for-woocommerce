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

<?php if( isset($postmeta) ){ ?>
<div id="angelleye-woocommerce-offer-meta-summary">
    <div class="angelleye-col-1-3">
        <div class="angelleye-col-container">
            <h5>Original Data</h5>
            <ul class="offer-original-meta-values-wrap">
                <li>Original Offer Qty: <?php echo (isset($postmeta['orig_offer_quantity'][0])) ? $postmeta['orig_offer_quantity'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                <li>Original Offer Price/Per: <?php echo (isset($postmeta['orig_offer_price_per'][0])) ? get_woocommerce_currency_symbol().$postmeta['orig_offer_price_per'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                <li>Original Offer Amount: <?php echo (isset($postmeta['orig_offer_amount'][0])) ? get_woocommerce_currency_symbol().$postmeta['orig_offer_amount'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
            </ul>
        </div>
    </div>
    <div class="angelleye-col-1-3">
        <div class="angelleye-col-container">
            <h5>Add Notes/Comments</h5>
            <textarea name="angelleye_woocommerce_offer_status_notes" id="angelleye_woocommerce_offer_status_notes" class=""></textarea>
        </div>
    </div>
    <div class="angelleye-col-1-3">
        <div class="angelleye-col-container">
            <h5>Status</h5>
            <ul id="angelleye-offer-edit-actions">
                <li class="angelleye_woocommerce_offers_status_btn"><a class="button <?php if($current_status_value == 'accepted-offer') { echo 'current-offer-status'; } ?>" id="angelleye-woocommerce-offers-status-btn-accept-offer" <?php if($current_status_value == 'accepted-offer') { echo 'disabled'; } ?>><?php if($current_status_value == 'accepted-offer') { echo 'Accepted'; } else { echo 'Accept'; } ?></a></li>
                <li class="angelleye_woocommerce_offers_status_btn"><a class="button" id="angelleye-woocommerce-offers-status-btn-counter-offer">Counter</a></li>
                <li class="angelleye_woocommerce_offers_status_btn"><a class="button" id="angelleye-woocommerce-offers-status-btn-decline-offer">Decline</a></li>
                <li class="angelleye_woocommerce_offers_status_btn"><a class="button" id="angelleye-woocommerce-offers-status-btn-complete-offer">Mark as Completed</a></li>
            </ul>
            <hr>
            <?php
            echo '<div class="woocommerce-offer-edit-status-inputs">';
                echo '<div class="woocommerce-offer-edit-status-radio-wrap"><input required="required" autocomplete="off" type="radio" name="post_status" value="accepted-offer"'; if ($current_status_value == 'accepted-offer') echo "checked=1"; echo '> Accepted</div>';
                echo '<div class="woocommerce-offer-edit-status-radio-wrap"><input required="required" autocomplete="off" type="radio" name="post_status" value="declined-offer"'; if ($current_status_value == 'declined-offer') echo "checked=1"; echo '> Declined</div>';
                echo '<div class="woocommerce-offer-edit-status-radio-wrap"><input required="required" autocomplete="off" type="radio" name="post_status" value="completed-offer"'; if ($current_status_value == 'completed-offer') echo "checked=1"; echo '> Completed</div>';

                echo '<input type="hidden" name="woocommerce_offer_summary_metabox_noncename" id="woocommerce_offer_summary_metabox_noncename" value="'; echo wp_create_nonce( 'woocommerce_offer'.$post->ID ); echo '" />';
                echo '<input type="hidden" name="post_previous_status" id="post_previous_status" value="'.$current_status_value.'"';
            echo '</div>';
            echo '<div class="woocommerce-offer-edit-submit-btn-wrap"><input name="submit" id="submit" class="button button-primary" value="Update" type="submit"><div class="angelleye-clearfix"></div></div>';
            ?>
            <div class="angelleye-clearfix"></div>

        </div>
        <div class="angelleye-clearfix"></div>
    </div>
    <div class="angelleye-clearfix"></div>
</div>
<div class="angelleye-clearfix"></div>
<?php } ?>
