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
<div id="aeofwc-close-lightbox-link"><a href="javascript:void(0);">&times;</a></div>
<div id="tab_custom_ofwc_offer_tab_alt_message" class="tab_custom_ofwc_offer_tab_inner_content">
    <ul class="woocommerce-error aeofwc-woocommerce-error">
        <li><strong><?php echo __('Selection Required:', 'offers-for-woocommerce'); ?>&nbsp;</strong><?php echo __('Select product options above before making new offer.', 'offers-for-woocommerce'); ?></li>
    </ul>        
</div>
<div id="tab_custom_ofwc_offer_tab_alt_message_success" class="tab_custom_ofwc_offer_tab_inner_content">
    <ul class="woocommerce-message">
        <li><strong><?php echo __('Offer Sent!', 'offers-for-woocommerce'); ?>&nbsp;</strong><?php echo __('Your offer has been received and will be processed as soon as possible.', 'offers-for-woocommerce'); ?></li>
    </ul>        
</div>
<div id="tab_custom_ofwc_offer_tab_alt_message_2" class="tab_custom_ofwc_offer_tab_inner_content">
    <ul class="woocommerce-error aeofwc-woocommerce-error">
        <li><strong><?php echo __('Error:', 'offers-for-woocommerce');?>&nbsp;</strong><?php echo __('There was an error sending your offer, please try again. If this problem persists, please contact us.', 'offers-for-woocommerce'); ?></li>
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
        <li><strong><?php echo __('Error:', 'offers-for-woocommerce'); ?>&nbsp;</strong><?php echo $parent_offer_error_message;?></li>
    </ul>
</div>
<?php } ?>
<div id="tab_custom_ofwc_offer_tab_inner" class="tab_custom_ofwc_offer_tab_inner_content">
    <fieldset>
    	<div class="make-offer-form-intro">
            <?php
                $is_counter_offer = (isset($parent_offer_id) && $parent_offer_id != '') ? true : false;
                $on_exit_enabled = get_post_meta($post->ID, 'offers_for_woocommerce_onexit_only', true);
                $on_exit_enabled = (isset($on_exit_enabled) && $on_exit_enabled == 'yes') ? true : false;
                if(isset($button_display_options['display_setting_custom_make_offer_btn_text']) && !empty($button_display_options['display_setting_custom_make_offer_btn_text'])) {
                    $tab_title = apply_filters('woocommerce_make_offer_form_tab_title', $button_display_options['display_setting_custom_make_offer_btn_text'], $is_counter_offer, $on_exit_enabled);
                } else {
                    $tab_title = apply_filters('woocommerce_make_offer_form_tab_title', __('Make Offer', 'offers-for-woocommerce'), $is_counter_offer, $on_exit_enabled);
                }
            ?>
            <?php 
                if($is_counter_offer) {
                    $tab_title = apply_filters('woocommerce_make_offer_form_tab_name', __('Make Counter Offer', 'offers-for-woocommerce'), $is_counter_offer, $on_exit_enabled);
                    $intro_html = '<h2>' . $tab_title . '</h2>';
                    $intro_html.= '<div class="make-offer-form-intro-text">' . __('To make a counter offer please complete the form below:', 'offers-for-woocommerce') . '</div>';
                } else if ($on_exit_enabled) {
                    $intro_html = '<h2>' . $tab_title . '</h2>';
                    $intro_html .= '<div class="make-offer-form-intro-text">' . __('Wait! Before you go, feel free to send us an offer. We may decide to go ahead and accept it!', 'offers-for-woocommerce') . '</div>';
                } else {
                    $intro_html = '<h2>' . $tab_title . '</h2>';
                    $intro_html .= '<div class="make-offer-form-intro-text">' . __('To make an offer please complete the form below:', 'offers-for-woocommerce') . '</div>';
                }
                echo apply_filters( 'aeofwc_offer_form_top_message', $intro_html, $is_counter_offer, $on_exit_enabled );
            ?>
        </div>
        <form id="woocommerce-make-offer-form" name="woocommerce-make-offer-form" method="POST" autocomplete="off" action="">
            <?php if($is_counter_offer) {?>
                <input type="hidden" name="parent_offer_id" id="parent_offer_id" value="<?php echo (isset($parent_offer_id) && $parent_offer_id != '') ? $parent_offer_id : ''; ?>">
                <input type="hidden" name="parent_offer_uid" id="parent_offer_uid" value="<?php echo (isset($parent_offer_uid) && $parent_offer_uid != '') ? $parent_offer_uid : ''; ?>">
            <?php } ?>
            <div class="woocommerce-make-offer-form-section">
                <?php if(isset($is_sold_individually) && $is_sold_individually ) { ?>
                    <input type="hidden" name="offer_quantity" id="woocommerce-make-offer-form-quantity" data-m-dec="0" data-l-zero="deny" data-a-form="false" required="required" value="1" />
                <?php } else { ?>
                    <div class="woocommerce-make-offer-form-part-left">
                        <label for="woocommerce-make-offer-form-quantity"><?php echo apply_filters( 'aeofwc_offer_form_label_quantity', __('Quantity', 'offers-for-woocommerce'), $is_counter_offer );?></label>
                        <br /><input type="text" name="offer_quantity" id="woocommerce-make-offer-form-quantity" data-m-dec="0" data-l-zero="deny" data-a-form="false" <?php echo ($new_offer_quantity_limit) ? ' data-v-max="'.$new_offer_quantity_limit.'"' : '';?> required="required" />
                    </div>
                <?php } ?>
                <div class="woocommerce-make-offer-form-part-left">
                	<label for="woocommerce-make-offer-form-price-each"><?php echo apply_filters( 'aeofwc_offer_form_label_price_each', __('Price Each', 'offers-for-woocommerce'), $is_counter_offer );?></label>
                    <br />
                    <div class="angelleye-input-group">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <input type="text" name="offer_price_each" id="woocommerce-make-offer-form-price-each" pattern="([0-9]|\$|,|.)+" data-a-sign="$" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" required="required" />
                    </div>
                </div>
                <?php do_action('make_offer_form_before_total_offer_amount', 'add_custom_field_make_offer_form', 10); ?>                    
                <div class="woocommerce-make-offer-form-part-left">
                    <?php if( (isset($is_sold_individually) && $is_sold_individually) || empty($button_display_options['display_setting_make_offer_form_field_offer_total'])) { ?>
                        <input type="hidden" name="offer_total" id="woocommerce-make-offer-form-total" class="form-control" data-currency-symbol="<?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?>" disabled="disabled" />
                    <?php } else { ?>
                    <label for="woocommerce-make-offer-form-total"><?php echo apply_filters( 'aeofwc_offer_form_label_total_offer_amount', __('Total Offer Amount', 'offers-for-woocommerce'), $is_counter_offer );?></label>
	                <br />
                    <div class="angelleye-input-group">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <input type="text" name="offer_total" id="woocommerce-make-offer-form-total" class="form-control" data-currency-symbol="<?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?>" disabled="disabled" />
                    </div>
                    <?php } ?>
                 </div>
                <?php do_action('make_offer_form_after_total_offer_amount', 'add_custom_field_make_offer_form', $is_counter_offer); ?>
            </div>
                
            <?php do_action('make_offer_form_before_your_name', 'add_custom_field_make_offer_form', $is_counter_offer); ?>
            <div class="woocommerce-make-offer-form-section">
                <label for="offer-name" class="woocommerce-make-offer-form-label"><?php echo apply_filters( 'aeofwc_offer_form_label_your_name', __('Your Name', 'offers-for-woocommerce'), $is_counter_offer );?></label>
                <br /><input type="text" id="offer-name" name="offer_name" required="required" <?php echo ($is_counter_offer) ? ' disabled="disabled"' : '' ?> value="<?php echo (isset($offer_name)) ? $offer_name : ''; ?>" />
            </div>
            <?php do_action('make_offer_form_after_your_name', 'add_custom_field_make_offer_form', $is_counter_offer); ?>
                             
            <?php do_action('make_offer_form_before_company_name', 'add_custom_field_make_offer_form', $is_counter_offer); ?>
            <?php if(!empty($button_display_options['display_setting_make_offer_form_field_offer_company_name'])) { ?>
                <div class="woocommerce-make-offer-form-section">
                    <label for="offer-name" class="woocommerce-make-offer-form-label"><?php echo apply_filters( 'aeofwc_offer_form_label_company_name', __('Company Name', 'offers-for-woocommerce'), $is_counter_offer );?></label>
                    <br />
                    <?php if(!empty($button_display_options['display_setting_make_offer_form_field_offer_company_name_required'])) { 
                            $company_name_required='required';
                          }
                          else{
                              $company_name_required='';
                          }
                    ?>
                    <input type="text" id="offer-company-name" name="offer_company_name" <?php echo ($is_counter_offer) ? ' disabled="disabled"' : '' ?> value="<?php echo (isset($offer_company_name)) ? $offer_company_name: ''; ?>" <?php echo $company_name_required; ?> />
                </div>
            <?php } else { ?>
                <input type="hidden" name="offer_company_name" id="offer-company-name" value="">
            <?php } ?>
            <?php do_action('make_offer_form_after_company_name', 'add_custom_field_make_offer_form', $is_counter_offer); ?>
            
                
            <?php do_action('make_offer_form_before_phone_number', 'add_custom_field_make_offer_form', $is_counter_offer); ?>                
            <?php if(!empty($button_display_options['display_setting_make_offer_form_field_offer_phone'])) { ?>
                <div class="woocommerce-make-offer-form-section">
                    <label for="offer-name" class="woocommerce-make-offer-form-label"><?php echo apply_filters( 'aeofwc_offer_form_label_phone_number', __('Phone Number', 'offers-for-woocommerce'), $is_counter_offer );?></label>
                    <br />
                    <?php if(!empty($button_display_options['display_setting_make_offer_form_field_offer_phone_required'])) {
                            $offer_phone_required='required';
                          }
                          else{
                              $offer_phone_required='';
                          }
                    ?>
                    <input type="text" id="offer-phone" name="offer_phone" <?php echo ($is_counter_offer) ? ' disabled="disabled"' : '' ?> value="<?php echo (isset($offer_phone)) ? $offer_phone: ''; ?>"  <?php echo $offer_phone_required; ?> />
                </div>
            <?php } else { ?>
                <input type="hidden" name="offer_phone" id="offer-phone" value="">
            <?php } ?>
            <?php do_action('make_offer_form_after_phone_number', 'add_custom_field_make_offer_form', $is_counter_offer, $on_exit_enabled); ?>      
            <?php do_action('make_offer_form_before_your_email_address', 'add_custom_field_make_offer_form', $is_counter_offer, $on_exit_enabled); ?>
            <div class="woocommerce-make-offer-form-section">
                <label for="woocommerce-make-offer-form-email"><?php echo apply_filters( 'aeofwc_offer_form_label_your_email_address', __('Your Email Address', 'offers-for-woocommerce'), $is_counter_offer );?></label>
                <br /><input type="email" name="offer_email" id="woocommerce-make-offer-form-email" required="required" <?php echo ($is_counter_offer) ? ' disabled="disabled"' : '' ?> value="<?php echo (isset($offer_email)) ? $offer_email: ''; ?>" />
            </div>
            <?php do_action('make_offer_form_after_your_email_address', 'add_custom_field_make_offer_form', $is_counter_offer, $on_exit_enabled); ?>
            <?php do_action('make_offer_form_before_offer_notes', 'add_custom_field_make_offer_form', $is_counter_offer, $on_exit_enabled); ?>
            <?php if(!empty($button_display_options['display_setting_make_offer_form_field_offer_notes']) && $is_anonymous_communication_enable == false ) { ?>
                <div class="woocommerce-make-offer-form-section">
                    <label for="angelleye-offer-notes"><?php echo apply_filters( 'aeofwc_offer_form_label_offer_notes', __('Offer Notes (optional)', 'offers-for-woocommerce'), $is_counter_offer );?></label>
                    <br />
                    <?php if(!empty($button_display_options['display_setting_make_offer_form_field_offer_notes_required'])) {
                            $offer_notes_required='required';
                          }
                          else{
                              $offer_notes_required='';
                          }
                    ?>
                    <textarea name="offer_notes" id="angelleye-offer-notes" rows="4" <?php echo $offer_notes_required; ?>></textarea>
                </div>
            <?php } else { ?>
                <input type="hidden" name="offer_notes" id="angelleye-offer-notes" value="">
            <?php } ?>
            <?php
            do_action('make_offer_form_after_offer_notes', 'add_custom_field_make_offer_form', $is_counter_offer, $on_exit_enabled);
            do_action('woocommerce_make_offer_form_end', $is_counter_offer, $on_exit_enabled);
            
            if($is_counter_offer) {
                $submit_offer_text = __( 'Submit Counter Offer', 'offers-for-woocommerce' );
            } else {
                $submit_offer_text = __( 'Submit Offer', 'offers-for-woocommerce' );
            }
            $submit_offer_text = apply_filters( 'aeofwc_offer_form_label_submit_button', $submit_offer_text, $is_counter_offer, $is_recaptcha_enable, $on_exit_enabled);
            
            do_action('make_offer_form_before_submit_button', $is_counter_offer, $on_exit_enabled);
            
            if($is_recaptcha_enable) {
                 printf( '<div class="woocommerce-make-offer-form-section"><div class="g-recaptcha" data-sitekey="%s"></div></div>', get_option('ofw_recaptcha_site_key') );
            }
            ?>
            <div class="woocommerce-make-offer-form-section <?php echo apply_filters( 'woocommerce_make_offer_form_submit_section_class', 'woocommerce-make-offer-form-section-submit' );?>">
                <input type="submit" class="button" id="woocommerce-make-offer-form-submit-button" data-orig-val="<?php echo $submit_offer_text; ?>" value="<?php echo $submit_offer_text; ?>" />
                <div class="offer-submit-loader" id="offer-submit-loader"><?php echo __('Please wait...', 'offers-for-woocommerce'); ?></div>
            </div>
        </form>
        <div class="make-offer-form-outro">
            <div class="make-offer-form-outro-text"><?php echo apply_filters( 'aeofwc_offer_form_bottom_message', '', $is_counter_offer, $on_exit_enabled );?></div>
        </div>
    </fieldset>
</div>