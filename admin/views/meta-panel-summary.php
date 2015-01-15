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
    <div class="angelleye-col-m-1-1">
        <div class="angelleye-col-1-4 angelleye-col-m-1-2">
            <div class="angelleye-col-container">
                <h5>Original Data</h5>
                <ul class="offer-original-meta-values-wrap">
                    <li>Original Offer Qty: <?php echo (isset($postmeta['orig_offer_quantity'][0])) ? $postmeta['orig_offer_quantity'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                    <li>Original Offer Price/Per: <?php echo (isset($postmeta['orig_offer_price_per'][0])) ? get_woocommerce_currency_symbol().$postmeta['orig_offer_price_per'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                    <li>Original Offer Amount: <?php echo (isset($postmeta['orig_offer_amount'][0])) ? get_woocommerce_currency_symbol().$postmeta['orig_offer_amount'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                </ul>
            </div>
        </div>
        <div class="angelleye-col-1-4 angelleye-col-m-1-2">
            <div class="angelleye-col-container">
                <h5>Counter Offer Values</h5>
                <div class="offer-counter-offer-values-wrap">
                    <label  for="offer-quantity">Quantity</label>
                    <input type="text" class="offer-counter-value-input" required="required" name="offer_quantity" id="offer-quantity" value="<?php echo (isset($postmeta['offer_quantity'][0])) ? $postmeta['offer_quantity'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?>" />
                    <label  for="offer-quantity">Price Per</label>
                    <input type="text" class="offer-counter-value-input" required="required" name="offer_price_per" id="offer-price-per" value="<?php echo (isset($postmeta['offer_price_per'][0])) ? $postmeta['offer_price_per'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?>" />
                    <label  for="offer-quantity">Total</label>
                    <input type="text" class="offer-counter-value-input" required="required" name="offer_amount" id="offer-total" value="<?php echo (isset($postmeta['offer_amount'][0])) ? $postmeta['offer_amount'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?>" disabled="disabled" />
                </div>
            </div>
        </div>
    </div>
    <div class="angelleye-col-m-1-1">
        <div class="angelleye-col-1-4 angelleye-col-m-1-2">
            <div class="angelleye-col-container">
                <h5>Include Buyer Note</h5>
                <textarea name="angelleye_woocommerce_offer_status_notes" id="angelleye_woocommerce_offer_status_notes" class=""></textarea>
            </div>
        </div>
        <div class="angelleye-col-1-4 angelleye-col-m-1-2">
            <div class="angelleye-col-container">
                <h5>Status</h5>
                <div class="offer-post-status-input-wrap">
                    <select name="post_status" autocomplete="off" required="required">
                        <?php if ( (isset($current_status_value) && $current_status_value == 'publish') || ( !isset($current_status_value) ) ) { ?>
                        <option value="">- Select status</option>
                        <? } ?>
                        <option value="accepted-offer" <?php if (isset($current_status_value) && $current_status_value == 'accepted-offer') echo 'selected="selected"'; ?>>Accepted Offer</option>
                        <option value="countered-offer" <?php if (isset($current_status_value) && $current_status_value == 'countered-offer') echo 'selected="selected"'; ?>>Countered Offer</option>
                        <option value="declined-offer" <?php if (isset($current_status_value) && $current_status_value == 'declined-offer') echo 'selected="selected"'; ?>>Declined Offer</option>
                        <option value="completed-offer" <?php if (isset($current_status_value) && $current_status_value == 'completed-offer') echo 'selected="selected"'; ?>>Completed Offer</option>
                    </select>
                </div>
                <input type="hidden" name="woocommerce_offer_summary_metabox_noncename" id="woocommerce_offer_summary_metabox_noncename" value="<?php echo wp_create_nonce( 'woocommerce_offer'.$post->ID ); ?>" />
                <input type="hidden" name="post_previous_status" id="post_previous_status" value="<?php echo (isset($current_status_value)) ? $current_status_value : ''; ?>">

                <div class="woocommerce-offer-edit-submit-btn-wrap">
                    <input name="submit" id="submit" class="button button-primary" value="Update" type="submit">
                    <div class="angelleye-clearfix"></div>
                </div>

                <div class="angelleye-clearfix"></div>

            </div>
            <div class="angelleye-clearfix"></div>
        </div>
    </div>
    <div class="angelleye-clearfix"></div>
</div>
<div class="angelleye-clearfix"></div>
<?php } ?>
