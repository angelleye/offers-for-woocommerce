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
 * @copyright 2014 AngellEYE
 */
?>

<!-- This file is used to markup the public facing aspect of the plugin. -->
<div id="tab_custom_ofwc_offer_tab_alt_message" class="tab_custom_ofwc_offer_tab_inner_content">
    <ul class="woocommerce-error">
        <li><strong>Selection Required: </strong>Select product options above before making new offer.</li>
    </ul>        
</div>
<div id="tab_custom_ofwc_offer_tab_alt_message_success" class="tab_custom_ofwc_offer_tab_inner_content">
    <ul class="woocommerce-message">
        <li><strong>Offer Sent! </strong>Your offer has been received and will be processed as soon as possible.</li>
    </ul>        
</div>
<div id="tab_custom_ofwc_offer_tab_alt_message_2" class="tab_custom_ofwc_offer_tab_inner_content">
    <ul class="woocommerce-error">
        <li><strong>Error: </strong>There was an error sending your offer, please try again. If this problem persists, please contact us.</li>
    </ul>        
</div>
<div id="tab_custom_ofwc_offer_tab_inner" class="tab_custom_ofwc_offer_tab_inner_content">
    <fieldset>
    	<div class="make-offer-form-intro">
            <h2><?php echo ($button_display_options['display_setting_custom_make_offer_btn_text']) ? $button_display_options['display_setting_custom_make_offer_btn_text'] : 'Make Offer'; ?></h2>
            <div class="make-offer-form-intro-text">To make an offer please complete the form below:</div>
        </div>
        <form id="woocommerce-make-offer-form" name="woocommerce-make-offer-form" method="POST" autocomplete="on">
            <input type="hidden" name="parent_offer_id" id="parent_offer_id" value="<?php echo ($parent_offer_id) ? $parent_offer_id : ''; ?>">

            <div class="woocommerce-make-offer-form-section">
            	<div class="woocommerce-make-offer-form-part-left">
                    <label for="woocommerce-make-offer-form-quantity">Quantity</label>
                    <br /><input type="text" name="offer_quantity" id="woocommerce-make-offer-form-quantity" data-m-dec="0" data-l-zero="deny" data-a-form="false" required="required" />
                </div>
                <div class="woocommerce-make-offer-form-part-left">
                	<label for="woocommerce-make-offer-form-price-each">Price Each</label>
	                <br /><input type="text" name="offer_price_each" id="woocommerce-make-offer-form-price-each" pattern="([0-9]|\$|,|.)+" data-a-sign="$" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" required="required" />
                </div>
                <div class="woocommerce-make-offer-form-part-left">
                	<label for="woocommerce-make-offer-form-total">Total Offer Amount</label>
	                <br /><input type="text" name="offer_total" id="woocommerce-make-offer-form-total" disabled="disabled" />
                </div>
            </div>
            <div class="woocommerce-make-offer-form-section">
                <label for="offer-name" id="woocommerce-make-offer-form-label">Your Name</label>
                <br /><input type="text" id="offer-name" name="offer_name" required="required" value="<?php echo (isset($offer_name)) ? $offer_name : ''; ?>" />
            </div>
            <div class="woocommerce-make-offer-form-section">
                <label for="offer-name" id="woocommerce-make-offer-form-label">Company Name</label>
                <br /><input type="text" id="offer-company-name" name="offer_company_name" value="<?php echo (isset($offer_company_name)) ? $offer_company_name: ''; ?>" />
            </div>
            <div class="woocommerce-make-offer-form-section">
                <label for="offer-name" id="woocommerce-make-offer-form-label">Phone Number</label>
                <br /><input type="text" id="offer-phone" name="offer_phone" value="<?php echo (isset($offer_phone)) ? $offer_phone: ''; ?>" />
            </div>
            <div class="woocommerce-make-offer-form-section">
                <label for="woocommerce-make-offer-form-email">Your Email Address</label>
                <br /><input type="email" name="offer_email" id="woocommerce-make-offer-form-email" required="required" value="<?php echo ($offer_email) ? $offer_email: ''; ?>" />
            </div>
            <div class="woocommerce-make-offer-form-section">
                <label for="offer-notes">Offer Notes (optional)</label>
                <br /><textarea name="offer_notes" id="angelleye-offer-notes" /></textarea>
            </div>
            <div class="woocommerce-make-offer-form-section woocommerce-make-offer-form-section-submit">
                <input type="submit" class="button" id="woocommerce-make-offer-form-submit-button" value="Submit Offer" />
                <div class="offer-submit-loader" id="offer-submit-loader">Please wait...</div>
            </div>
        </form>
    </fieldset>
</div>
