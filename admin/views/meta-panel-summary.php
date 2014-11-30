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
    <div class="angelleye-woocommerce-col-1-3">

        <ul class="offer-original-meta-values-wrap">
            <li>Original Offer Amount: <?php echo (isset($postmeta['orig_offer_amount'][0])) ? $postmeta['orig_offer_amount'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
            <li>Original Offer Qty: <?php echo (isset($postmeta['orig_offer_quantity'][0])) ? $postmeta['orig_offer_quantity'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
            <li>Original Offer Price/Per: <?php echo (isset($postmeta['orig_offer_price_per'][0])) ? $postmeta['orig_offer_price_per'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
        </ul>
    </div>
    <div class="angelleye-woocommerce-col-1-3">
        <h5>Orignal Offer Data</h5>
    </div>
    <div class="angelleye-woocommerce-col-1-3">
        <h5>Orignal Offer Data</h5>
    </div>


</div>
<?php } ?>