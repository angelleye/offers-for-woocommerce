<?php
/**
 * Represents the view for the public-facing component of the plugin.
 *
 * This typically includes any information, if any, that is rendered to the
 * frontend of the theme when the plugin is activated.
 *
 * @package   Angelleye_Offers_For_Woocommerce
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 */
?>

<!-- This file is used to markup the public facing aspect of the plugin. -->
<div id="tab_custom_ofwc_offer_tab_alt_message" class="tab_custom_ofwc_offer_tab_inner_content">
    <ul class="woocommerce-error aeofwc-woocommerce-error">
        <li><strong>Selection Required: </strong>Select product options above before making new offer.</li>
    </ul>        
</div>
<div id="tab_custom_ofwc_offer_tab_alt_message_success" class="tab_custom_ofwc_offer_tab_inner_content">
    <ul class="woocommerce-message">
        <li><strong>Offer Sent! </strong>Your offer has been received and will be processed as soon as possible.</li>
    </ul>        
</div>
<div id="tab_custom_ofwc_offer_tab_alt_message_2" class="tab_custom_ofwc_offer_tab_inner_content">
    <ul class="woocommerce-error aeofwc-woocommerce-error">
        <li><strong>Error: </strong>There was an error sending your offer, please try again. If this problem persists, please contact us.</li>
    </ul>
</div>
<div id="tab_custom_ofwc_offer_tab_alt_message_custom" class="tab_custom_ofwc_offer_tab_inner_content">
    <ul class="woocommerce-error aeofwc-woocommerce-error">
        <li id="alt-message-custom"></li>
    </ul>
</div>
<?php if($parent_offer_error && $parent_offer_error_message) { ?>
<div id="tab_custom_ofwc_offer_tab_alt_message_3" class="tab_custom_ofwc_offer_tab_inner_content tab_custom_ofwc_offer_tab_alt_message_2">
    <ul class="woocommerce-error aeofwc-woocommerce-error">
        <li><strong>Error: </strong><?php echo $parent_offer_error_message;?></li>
    </ul>
</div>
<? } ?>
<div id="tab_custom_ofwc_offer_tab_inner" class="tab_custom_ofwc_offer_tab_inner_content">
    <fieldset>
    	<div class="make-offer-form-intro">
            <h2>
                <?php $is_counter_offer = (isset($parent_offer_id) && $parent_offer_id != '') ? true : false; ?>
                <?php if($is_counter_offer)
                {
                    echo 'Make Counter Offer';
                }
                else
                {
                    if(isset($button_display_option['display_setting_custom_make_offer_btn_text']) && !empty($button_display_option['display_setting_custom_make_offer_btn_text']))
                    {
                        echo $button_display_options['display_setting_custom_make_offer_btn_text'];
                    }
                    else
                    {
                        echo 'Make Offer';
                    }
                }
                ?>
            </h2>
            <div class="make-offer-form-intro-text">To make <?php echo ($is_counter_offer) ? 'a counter ' : 'an '; ?>offer please complete the form below:</div>
        </div>
        <form id="woocommerce-make-offer-form" name="woocommerce-make-offer-form" method="POST" autocomplete="off">
            <?php if($is_counter_offer) {?>
            <input type="hidden" name="parent_offer_id" id="parent_offer_id" value="<?php echo (isset($parent_offer_id) && $parent_offer_id != '') ? $parent_offer_id : ''; ?>">
            <input type="hidden" name="parent_offer_uid" id="parent_offer_uid" value="<?php echo (isset($parent_offer_uid) && $parent_offer_uid != '') ? $parent_offer_uid : ''; ?>">
            <? } ?>
            <div class="woocommerce-make-offer-form-section">
                <?php if(isset($is_sold_individually) && $is_sold_individually ) { ?>
                    <input type="hidden" name="offer_quantity" id="woocommerce-make-offer-form-quantity" data-m-dec="0" data-l-zero="deny" data-a-form="false" required="required" value="1" />
                <?php } else { ?>
            	<div class="woocommerce-make-offer-form-part-left">
                    <label for="woocommerce-make-offer-form-quantity">Quantity</label>
                    <br /><input type="text" name="offer_quantity" id="woocommerce-make-offer-form-quantity" data-m-dec="0" data-l-zero="deny" data-a-form="false" required="required" />
                </div>
                <?php } ?>
                <div class="woocommerce-make-offer-form-part-left">
                	<label for="woocommerce-make-offer-form-price-each">Price Each</label>
                    <br />
                    <div class="angelleye-input-group">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <input type="text" name="offer_price_each" id="woocommerce-make-offer-form-price-each" pattern="([0-9]|\$|,|.)+" data-a-sign="$" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" required="required" />
                    </div>
                </div>
                <div class="woocommerce-make-offer-form-part-left">
                    <?php if(isset($is_sold_individually) && $is_sold_individually ) { ?>
                        <input type="hidden" name="offer_total" id="woocommerce-make-offer-form-total" class="form-control" data-currency-symbol="<?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?>" disabled="disabled" />
                    <?php } else { ?>
                    <label for="woocommerce-make-offer-form-total">Total Offer Amount</label>
	                <br />
                    <div class="angelleye-input-group">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <input type="text" name="offer_total" id="woocommerce-make-offer-form-total" class="form-control" data-currency-symbol="<?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?>" disabled="disabled" />
                    </div>
                    <?php } ?>
                 </div>
            </div>
            <div class="woocommerce-make-offer-form-section">
                <label for="offer-name" class="woocommerce-make-offer-form-label">Your Name</label>
                <br /><input type="text" id="offer-name" name="offer_name" required="required" <?php echo ($is_counter_offer) ? ' disabled="disabled"' : '' ?> value="<?php echo (isset($offer_name)) ? $offer_name : ''; ?>" />
            </div>
            <div class="woocommerce-make-offer-form-section">
                <label for="offer-name" class="woocommerce-make-offer-form-label">Company Name</label>
                <br /><input type="text" id="offer-company-name" name="offer_company_name" <?php echo ($is_counter_offer) ? ' disabled="disabled"' : '' ?> value="<?php echo (isset($offer_company_name)) ? $offer_company_name: ''; ?>" />
            </div>
            <div class="woocommerce-make-offer-form-section">
                <label for="offer-name" class="woocommerce-make-offer-form-label">Phone Number</label>
                <br /><input type="text" id="offer-phone" name="offer_phone" <?php echo ($is_counter_offer) ? ' disabled="disabled"' : '' ?> value="<?php echo (isset($offer_phone)) ? $offer_phone: ''; ?>" />
            </div>
            <div class="woocommerce-make-offer-form-section">
                <label for="woocommerce-make-offer-form-email">Your Email Address</label>
                <br /><input type="email" name="offer_email" id="woocommerce-make-offer-form-email" required="required" <?php echo ($is_counter_offer) ? ' disabled="disabled"' : '' ?> value="<?php echo (isset($offer_email)) ? $offer_email: ''; ?>" />
            </div>
            <div class="woocommerce-make-offer-form-section">
                <label for="angelleye-offer-notes">Offer Notes (optional)</label>
                <br /><textarea name="offer_notes" id="angelleye-offer-notes" rows="4"></textarea>
            </div>
            <div class="woocommerce-make-offer-form-section woocommerce-make-offer-form-section-submit">
                <input type="submit" class="button" id="woocommerce-make-offer-form-submit-button" data-orig-val="Submit <?php echo ($is_counter_offer) ? ' Counter ' : ''; ?>Offer" value="Submit <?php echo ($is_counter_offer) ? ' Counter ' : ''; ?>Offer" />
                <div class="offer-submit-loader" id="offer-submit-loader">Please wait...</div>
            </div>
        </form>
    </fieldset>
</div>