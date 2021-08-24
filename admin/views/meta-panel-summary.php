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

<?php if( isset($postmeta) ){ ?>
<div id="angelleye-woocommerce-offer-meta-summary">
    <div class="angelleye-col-m-1-1">
        <div class="angelleye-col-1-2 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5><?php echo __('Product Details', 'offers-for-woocommerce'); ?></h5>
                <?php
                if(!isset($_product)) {
                    echo __('Product not found', 'offers-for-woocommerce');
                } else { ?>                    
                        
                    <ul class="offer-product-meta-image-wrap"><a href="<?php echo $_product_permalink; ?>" target="_blank" title="<?php echo __('Click to view product', 'offers-for-woocommerce'); ?>"><?php echo $_product_image; ?></a></ul>
                    <ul class="offer-product-meta-values-wrap">
                        <li><span><?php echo __('Product:', 'offers-for-woocommerce');?>&nbsp;</span><?php echo (isset($_product_formatted_name)) ? '<a href="'.$_product_permalink.'" target="_blank" title="' . __('Click to view product', 'offers-for-woocommerce') . '">'.$_product_formatted_name.'</a>&nbsp;-&nbsp;<a href="post.php?post='.$_product->get_id().'&action=edit" title="' . __('Click to edit product', 'offers-for-woocommerce') . '"><span>('.$_product->get_id().')</span></a>' : __('Missing Meta Value', 'offers-for-woocommerce'); ?></li>
                        <?php if( isset($_product_attributes) && is_array($_product_attributes) && !empty($_product_attributes) ) { ?>
                        <li><span><?php echo __('Attributes:', 'offers-for-woocommerce');?>&nbsp;</span><?php echo ucwords( implode( ", ", array_values($_product_attributes)) ); ?></li>
                        <?php } ?>
                        <li><span><?php echo __('Regular Price:', 'offers-for-woocommerce'); ?>&nbsp;</span><?php echo (!empty($_product_regular_price)) ?  wc_price($_product_regular_price, array('currency' => $offer_currency)) : __('Missing Meta Value', 'offers-for-woocommerce'); ?></li>
                        <?php if($_product_sale_price) { ?>
                        <li><span><?php echo __('Sale Price:', 'offers-for-woocommerce');?>&nbsp;</span><?php echo (!empty($_product_sale_price)) ? wc_price($_product_sale_price, array('currency' => $offer_currency))  : __('Missing Meta Value', 'offers-for-woocommerce'); ?></li>
                        <?php } ?>
                        <?php if(isset($_product_stock) && $_product_stock == 0  && $_product_managing_stock ) { ?>
                            <li>
                                <span><?php echo __('Stock:', 'offers-for-woocommerce');?>&nbsp;</span><?php echo (isset($_product_stock) && $_product_stock != '' ) ? $_product_stock : '0'; ?>
                                <?php if($_product_backorders_allowed) { ?>
                                    <?php echo ' ('. __('can be backordered', 'offers-for-woocommerce') . ')'; ?>
                                <?php } ?>
                            </li>
                        <?php } else { ?>
                            <li>
                                <span>Stock: </span><?php echo (isset($_product_stock) && $_product_stock != '' && $_product_managing_stock ) ? $_product_stock : ' ('. __('not managed', 'offers-for-woocommerce') . ')'; ?>
                                <?php if($_product_backorders_allowed) { ?>
                                    <?php echo ' ('. __('can be backordered', 'offers-for-woocommerce') . ')'; ?>
                                <?php } ?>
                            </li>
                        <?php } ?>
                        <?php if( !$_product_in_stock && (!$_product_stock || $_product_stock == '') ) { ?>
                            <li>
                                <span class="out-of-stock-offer"><?php echo __('Out of Stock', 'offers-for-woocommerce'); ?></span>
                            </li>
                        <?php } elseif( !$_product_in_stock && $_product_stock ) { ?>
                            <li>
                                <span class="out-of-stock-offer"><?php echo __('Not enough stock to fulfill offer', 'offers-for-woocommerce'); ?></span>
                            </li>
                        <?php } ?>
                        <input id="offer-max-stock-available" type="hidden" value="<?php echo ( isset($_product_stock) ) ? $_product_stock : '' ?>">
                        <input id="offer-backorders-allowed" type="hidden" value="<?php echo ( $_product_backorders_allowed ) ? 'true' : 'false';?>">
                        
                    </ul>
                <?php } ?>
            </div>
        </div>        
        <div class="angelleye-col-1-2 angelleye-col-s-1-1">
            <?php 
            if(!$is_anonymous_communication_enable) { ?>
                <div class="angelleye-col-container">
                    <h5>
                        <?php echo __('Buyer Details', 'offers-for-woocommerce'); ?>
                        <?php if( $author_data ) { ?>
                            <a id="angelleye-offer-buyer-stats-toggle" class="angelleye-offer-buyer-stats-toggle" href="javascript:;" title="<?php echo __('View offer history', 'offers-for-woocommerce');?>"><span id="angelleye-offer-buyer-stats-counter"><?php echo __('Buyer History', 'offers-for-woocommerce'). ': <span class="total-offers-count">'. $author_data->offer_counts['all'] . '</span>'; ?></span></a>
                        <?php } ?>
                    </h5>
                    <ul class="offer-buyer-meta-values-wrap">
                        <li><span><?php echo __('Name:', 'offers-for-woocommerce'); ?>&nbsp;</span><?php echo (isset($postmeta['offer_name'][0])) ? stripslashes($postmeta['offer_name'][0]) : __('Missing Meta Value', 'offers-for-woocommerce'); ?></li>
                        <li><span><?php echo __('Email:', 'offers-for-woocommerce'); ?>&nbsp;</span><?php echo (isset($postmeta['offer_email'][0])) ? '<a href="mailto:'.$postmeta['offer_email'][0].'" target="_blank" title="Click to email">'.$postmeta['offer_email'][0].'</a>' : __('Missing Meta Value', 'offers-for-woocommerce'); ?></li>
                        <li><span><?php echo __('Phone:', 'offers-for-woocommerce'); ?>&nbsp;</span><?php echo (isset($postmeta['offer_phone'][0])) ? stripslashes($postmeta['offer_phone'][0]) : __('Missing Meta Value', 'offers-for-woocommerce'); ?></li>
                        <li><span><?php echo __('Company:', 'offers-for-woocommerce'); ?>&nbsp;</span><?php echo (isset($postmeta['offer_company_name'][0])) ? stripslashes($postmeta['offer_company_name'][0]) : __('Missing Meta Value', 'offers-for-woocommerce'); ?></li>
                        <?php 
                        global $post;
                        do_action('make_offer_after_buyer_meta_display', $post->ID); ?>
                    </ul>
                </div>
            <?php } ?>
            <div class="angelleye-col-container" id="angelleye-offer-buyer-history">
                <?php if( $author_data ) { ?>
                <h5><?php echo __('Buyer Offer History', 'offers-for-woocommerce'); ?>
                    <a id="angelleye-offer-buyer-stats-close" class="angelleye-offer-buyer-stats-toggle" href="javascript:;" title="<?php echo __('Close offer history', 'offers-for-woocommerce');?>"><?php echo __('close', 'offers-for-woocommerce');?></a>
                </h5>
                <ul class="offer-buyer-history-values-wrap">
                    <table id="offer-buyer-history">
                        <?php foreach($author_data->offer_counts as $key => $count) { ?>
                            <?php if(strtolower($key) != 'all') { ?>
                        <tr>
                            <th><?php echo ucwords(str_replace('buyercountered', 'Buyer-Countered', str_replace('_', ' ', $key)) ) .': '; ?></th>
                            <td><div>
                                <?php echo '<span>'. $count .'</span>';?>
                                <?php if($count > 0) {
                                    $post_status_part = ($key == 'pending') ? 'publish' : $key .'-offer';
                                echo '<a href="edit.php?author=' . $post->post_author . '&post_type=woocommerce_offer&post_status='. $post_status_part .'" class="angelleye-view-buyer-offer-history">' . __('view', 'offers-for-woocommerce') . '</a>';
                                } else {
                                    echo '<a href="javascript:;" class="angelleye-view-buyer-offer-history no-offer-history">' . __('view', 'offers-for-woocommerce') . '</a>';
                                }?>
                                </div>
                            </td>
                        </tr>
                            <?php } ?>
                        <?php } ?>
                    </table>
                </ul>
                <?php } ?>
            </div>
        </div>
        <div class="angelleye-clearfix"></div>
    </div>
    <?php 
        $active_plugins = (array) get_option( 'active_plugins', array() );        
        if ( is_multisite() ) $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        if(in_array( 'woocommerce-product-addons/woocommerce-product-addons.php', $active_plugins ) || array_key_exists( 'woocommerce-product-addons/woocommerce-product-addons.php', $active_plugins )){
    ?>
    <div class="angelleye-col-m-1-1">
        <div class="angelleye-col-1-1 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5><?php echo __('Product Add-ons Data', 'offers-for-woocommerce'); ?></h5>
            </div>                                    
            <?php
                foreach($offers_product_addon as $key => $offerProducts){
                    echo '<div class="angelleye-col-1-4 angelleye-col-m-1-2 angelleye-col-s-1-1">';
                    echo '<ul class="offer-product-meta-values-wrap">';
                    echo "<li><span>Group&nbsp;</span>{$offerProducts['group']}</li>";
                    echo "<li><span>Type&nbsp;</span>{$offerProducts['type']}</li>";
                    echo "<li><span>Options Selected : &nbsp;</span></li>";
                    foreach ($offerProducts['options'] as $labelPrices){
                        echo "<li><span>Label&nbsp;</span>{$labelPrices['label']} | <span>&nbsp;Price&nbsp;</span>{$labelPrices['price']}</li>";
                        echo "<li><span>Value&nbsp;</span>{$labelPrices['value']}";
                    }                        
                    echo "</ul>";
                    echo "</div>";                                    
                }                
            ?>
        </div>
    </div>
        <?php } ?>
    <div class="angelleye-col-m-1-1">
        <div class="angelleye-col-1-4 angelleye-col-m-1-2 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5><?php echo __('Original Offer', 'offers-for-woocommerce');?></h5>
                <div class="offer-original-meta-values-wrap">
                    <label for="original-offer-quantity"><?php echo __('Orig. Quantity', 'offers-for-woocommerce'); ?></label>
                    <div>
                        <input style="cursor: not-allowed" type="number" id="original-offer-quantity" value="<?php echo (isset($postmeta['orig_offer_quantity'][0])) ? $postmeta['orig_offer_quantity'][0] : __('Missing Meta Value', 'offers-for-woocommerce'); ?>" disabled="disabled" />
                    </div>
                    <label for="original-offer-price-per"><?php echo __('Orig. Price Per', 'offers-for-woocommerce'); ?></label>
                    <div class="angelleye-input-group">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <input style="cursor: not-allowed" type="text" id="original-offer-price-per" value="<?php echo (isset($postmeta['orig_offer_price_per'][0])) ? $postmeta['orig_offer_price_per'][0] : __('Missing Meta Value', 'offers-for-woocommerce'); ?>" disabled="disabled" />
                    </div>
                    <label for="original-offer-amount"><?php echo __('Orig. Amount', 'offers-for-woocommerce'); ?></label>
                    <div class="angelleye-input-group">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <input style="cursor: not-allowed" type="text" id="original-offer-amount" value="<?php echo (isset($postmeta['orig_offer_amount'][0])) ? $postmeta['orig_offer_amount'][0] : __('Missing Meta Value', 'offers-for-woocommerce'); ?>" disabled="disabled" />
                    </div>
                </div>
                <?php if( isset($query_counter_offers_history_result) && !empty($query_counter_offers_history_result) ) { ?>
                <h5><?php echo __('Offer History', 'offers-for-woocommerce');?></h5>
                <?php 
                
                $offer_history = '<ul class="counter-offers-history-wrap">';
                
                 foreach($query_counter_offers_history_result as $offers_history_result){
                     
                    $offer_status_value = '';
                    $offer_signle_entry = '';
                    
                    if($offers_history_result->comment_id) {
                        
                        $offer_status = get_comment_meta( $offers_history_result->comment_id, 'offer_status', true );
                        $offer_quantity = get_comment_meta( $offers_history_result->comment_id, 'offer_quantity', true );
                        $offer_amount = get_comment_meta( $offers_history_result->comment_id, 'offer_amount', true );
                        
                        if( $offer_status == '1') {
                            $offer_status_value = 'Buyers original offer';
                        } elseif ( $offer_status == '2' ) {
                            $offer_status_value = 'Buyer counter-offers';
                        } elseif ( $offer_status == '3' ) {
                            $offer_status_value = 'Seller counter-offers';
                        } elseif ( $offer_status == '4' ) {
                            $offer_status_value = 'Buyer completes the purchase';
                        } elseif ( $offer_status == '5' ) {
                            $offer_status_value = 'Seller original offer';
                        }
                            
                        if( !empty($offer_amount) ) {

                            $offer_quantity_value = (isset($offer_quantity) && !empty($offer_quantity)) ? 'QTY '. $offer_quantity : 'QTY 0';
                            $offer_amount_value = ( isset($offer_amount) && !empty($offer_amount) ) ? wc_price($offer_amount, array('currency' => $offer_currency)) : '';

                            $offer_signle_entry = $offer_status_value .' '. $offer_quantity_value .' at '. $offer_amount_value;
                            $offer_history .= '<li>'. $offer_signle_entry .'</li>';
                        
                        } elseif( !empty ($offer_status_value) ) {
                            $offer_history .= '<li>'. $offer_status_value .'</li>';
                        }
                    }
                }
                
                $offer_history .= '</ul>';
                echo $offer_history;
                
                ?>
                <?php } ?>
            </div>
        </div>
        <div class="angelleye-col-1-4 angelleye-col-m-1-2 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5><?php echo __('Counter Offer', 'offers-for-woocommerce'); ?></h5>
                <div class="offer-counter-offer-values-wrap">
                    <label for="offer-quantity"><?php echo __('Quantity', 'offers-for-woocommerce'); ?></label>
                    <div>
                        <?php if( isset( $current_status_value ) && $current_status_value == 'buyercountered-offer' ) { ?>
                            <input type="number" class="offer-counter-value-input" required="required" name="offer_quantity" id="offer-quantity" value="<?php echo (isset($postmeta['offer_buyer_counter_quantity'][0])) ? $postmeta['offer_buyer_counter_quantity'][0] : ''; ?>" />
                        <?php } else { ?>
                            <input type="number" class="offer-counter-value-input" required="required" name="offer_quantity" id="offer-quantity" value="<?php echo (isset($postmeta['offer_quantity'][0])) ? $postmeta['offer_quantity'][0] : ''; ?>" autocomplete="off" />
                        <?php } ?>
                    </div>
                    <label for="offer-price-per"><?php echo __('Price Per', 'offers-for-woocommerce'); ?></label>
                    <div class="angelleye-input-group">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <?php if( isset( $current_status_value ) && $current_status_value == 'buyercountered-offer' ) { ?>
                            <input type="text" name="offer_price_per" id="offer-price-per" pattern="([0-9]|\$|,|.)+" data-a-sign="" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" required="required" value="<?php echo (isset($postmeta['offer_buyer_counter_price_per'][0])) ? number_format($postmeta['offer_buyer_counter_price_per'][0],wc_get_price_decimals(),wc_get_price_decimal_separator(),wc_get_price_thousand_separator())  : ''; ?>" autocomplete="off" />
                        <?php } else { ?>
                            <input type="text" name="offer_price_per" id="offer-price-per" pattern="([0-9]|\$|,|.)+" data-a-sign="" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" required="required" value="<?php echo (isset($postmeta['offer_price_per'][0])) ? number_format($postmeta['offer_price_per'][0],wc_get_price_decimals(),wc_get_price_decimal_separator(),wc_get_price_thousand_separator()) : ''; ?>" autocomplete="off" />
                        <?php } ?>
                    </div>
                    <div style="display: none" id="counter_offer_notice" class="note_content">
                        <p style="color: red;background: whitesmoke;border: solid 1px #f1f1f1;padding: 7px;"><?php _e('Original Price and Counter Price should not be same.','offers-for-woocommerce') ?></p>
                    </div>
                    <label for="offer-shipping-cost"><input type="checkbox" id="ofwc_enable_shipping" name="enable_shipping_cost" value="1" style="width: auto !important;" <?php if( $postmeta['enable_shipping_cost'][0] == 1) echo 'checked'; ?>><?php echo __('Include Shipping with Offer', 'offers-for-woocommerce'); ?></label>
                    <div class="angelleye-input-group offer_shipping">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <?php if( isset( $current_status_value ) && $current_status_value == 'buyercountered-offer' ) { ?>
                            <input type="text" name="offer_shipping_cost" id="offer_shipping_cost" pattern="([0-9]|\$|,|.)+" data-a-sign="" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" value="<?php echo (isset($postmeta['offer_shipping_cost'][0])) ? number_format($postmeta['offer_shipping_cost'][0],wc_get_price_decimals(),wc_get_price_decimal_separator(),wc_get_price_thousand_separator()) : ''; ?>" autocomplete="off" />
                        <?php } else { ?>
                            <input type="text" name="offer_shipping_cost" id="offer_shipping_cost" pattern="([0-9]|\$|,|.)+" data-a-sign="" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" value="<?php echo (isset($postmeta['offer_shipping_cost'][0])) ? number_format($postmeta['offer_shipping_cost'][0],wc_get_price_decimals(),wc_get_price_decimal_separator(),wc_get_price_thousand_separator()) : ''; ?>" autocomplete="off" />
                        <?php } ?>
                    </div>
                    
                    <label for="offer-total"><?php echo __('Total', 'offers-for-woocommerce'); ?></label>
                    <div class="angelleye-input-group">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <?php if( isset( $current_status_value ) && $current_status_value == 'buyercountered-offer' ) { ?>
                            <input type="text" name="offer_amount" id="offer-total" class="form-control" data-currency-symbol="<?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?>" value="<?php echo (isset($postmeta['offer_buyer_counter_amount'][0])) ? number_format($postmeta['offer_buyer_counter_amount'][0],wc_get_price_decimals(),wc_get_price_decimal_separator(),wc_get_price_thousand_separator()) : ''; ?>" disabled="disabled" autocomplete="off" />
                        <?php } else { ?>
                            <input type="text" name="offer_amount" id="offer-total" class="form-control" data-currency-symbol="<?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?>" value="<?php echo (isset($postmeta['offer_amount'][0])) ? number_format($postmeta['offer_amount'][0],wc_get_price_decimals(),wc_get_price_decimal_separator(),wc_get_price_thousand_separator()) : ''; ?>" disabled="disabled" autocomplete="off" />
                        <?php } ?>
                    </div>                    
                </div>                
            </div>
        </div>
    </div>
    <div class="angelleye-col-m-1-1">
       <?php if(!$is_anonymous_communication_enable) { ?>
        <div class="angelleye-col-1-4 angelleye-col-m-1-2 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5><?php echo __('Offer Note to Buyer', 'offers-for-woocommerce'); ?></h5>
                <textarea name="angelleye_woocommerce_offer_status_notes" id="angelleye_woocommerce_offer_status_notes" class="" autocomplete="off"></textarea>
                <p class="description"><?php echo __('Enter a note here to be included in the email notification to the buyer when the offer status is updated.', 'offers-for-woocommerce'); ?></p>
            </div>
        </div>
        <?php } ?>
        <div class="angelleye-col-1-4 angelleye-col-m-1-2 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5><?php echo __('Offer Status', 'offers-for-woocommerce'); ?></h5>
                <?php if( isset( $current_status_value ) && $current_status_value == 'completed-offer' ) { } else { ?>
                    <div class="offer-post-status-input-wrap">
                        <select id="woocommerce_offer_post_status" name="post_status" autocomplete="off" required="required" <?php if (isset($current_status_value) && $current_status_value == 'completed-offer') echo ' disabled="disabled"'; ?>>
                            <?php if ( (isset($current_status_value) && ( $current_status_value == 'publish' || $current_status_value == 'buyercountered-offer' || $current_status_value == 'expired-offer' ) ) || ( !isset($current_status_value) ) ) { ?>
                            <option value=""><?php echo __('- Select status', 'offers-for-woocommerce'); ?></option>
                            <?php } ?>
                            <option value="accepted-offer" <?php if (isset($current_status_value) && $current_status_value == 'accepted-offer') echo 'selected="selected"'; ?>><?php echo __('Accepted Offer', 'offers-for-woocommerce'); ?></option>
                            <option value="countered-offer" <?php if (isset($current_status_value) && $current_status_value == 'countered-offer') echo 'selected="selected"'; ?>><?php echo __('Countered Offer', 'offers-for-woocommerce'); ?></option>
                            <option value="declined-offer" <?php if (isset($current_status_value) && $current_status_value == 'declined-offer') echo 'selected="selected"'; ?>><?php echo __('Declined Offer', 'offers-for-woocommerce'); ?></option>
                            <option value="completed-offer" <?php if (isset($current_status_value) && $current_status_value == 'completed-offer') echo 'selected="selected"'; ?>><?php echo __('Completed Offer', 'offers-for-woocommerce'); ?></option>
                            <option value="on-hold-offer" <?php if (isset($current_status_value) && $current_status_value == 'on-hold-offer') echo 'selected="selected"'; ?>><?php echo __('On Hold', 'offers-for-woocommerce'); ?></option>
                        </select>
                    </div>
                <?php } ?>
                <input type="hidden" name="woocommerce_offer_summary_metabox_noncename" id="woocommerce_offer_summary_metabox_noncename" value="<?php echo wp_create_nonce( 'woocommerce_offer'.$post->ID ); ?>" />
                <input type="hidden" name="post_previous_status" id="post_previous_status" value="<?php echo (isset($current_status_value)) ? $current_status_value : ''; ?>">

                <div class="woocommerce-offer-final-offer-wrap">
                    <label for="offer-final-offer"><?php echo __('Final Offer', 'offers-for-woocommerce'); ?></label>
                    <div>
                        <input type="checkbox" name="offer_final_offer" id="offer-final-offer" value="1" <?php echo(isset($postmeta['offer_final_offer'][0]) && $postmeta['offer_final_offer'][0] == '1') ? 'checked="checked"' : ''?> autocomplete="off">
                    </div>
                </div>

                <div class="woocommerce-offer-send-coupon-wrap angelleye-hidden">
                    <?php 
                        $coupon_list = get_posts('post_type=shop_coupon');
                        if($coupon_list) { ?>
                        <label for="ofw_coupon_list"><?php _e( 'Coupon List', 'offers-for-woocommerce' ); ?></label>
                        <select id="ofw_coupon_list" name="ofw_coupon_list">
                            <option value="" ><?php _e( 'Select Coupon', 'offers-for-woocommerce' ); ?></option>
                            <?php foreach ( $coupon_list as $coupon  ) : ?>
                                <option value="<?php echo $coupon->post_name; ?>"><?php echo $coupon->post_title; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php }
                     ?>
                </div>
                
                <div class="woocommerce-offer-expiration-wrap">
                    <label for="offer-expiration-date"><?php echo __('Offer Expires', 'offers-for-woocommerce'); ?></label>
                    <?php
                        if(isset($postmeta['offer_expiration_date'][0]) && !empty($postmeta['offer_expiration_date'][0])){
                            $date_format = get_option('date_format');
                            $expiry_date_formated = date($date_format, strtotime($postmeta['offer_expiration_date'][0]));
                        }
                        else{
                            $expiry_date_formated = '';
                        }                        
                    ?>
                    <input type="text" name="offer_expiration_date" class="datepicker" id="offer-expiration-date" value="<?php echo $expiry_date_formated; ?>" autocomplete="off">
                    <input type="hidden" name="offer_expiration_date_hidden" id="offer_expiration_date_hidden" value="" />
                </div>

                <?php $show_notice_msg = ( isset($show_offer_inventory_msg) && $show_offer_inventory_msg ) ? TRUE : FALSE; ?>
                <div id="angelleye-woocommerce-offer-meta-summary-notice-msg" <?php echo (!$show_notice_msg) ? ' class="angelleye-hidden"' : '';?>">
                    <div class="aeofwc-notice-msg-inner"><?php echo (isset($offer_inventory_msg)) ? $offer_inventory_msg : '';?></div>
                </div>

                <div id="angelleye-woocommerce-offer-meta-summary-expire-notice-msg" class="angelleye-hidden">
                    <div class="aeofwc-notice-msg-inner"><?php echo __('Expiration date has passed.', 'offers-for-woocommerce'); ?></div>
                </div>

                <div class="woocommerce-offer-edit-submit-btn-wrap">
                    <?php if( isset( $current_status_value ) && $current_status_value == 'completed-offer' ) { ?>
                    <input name="submit" id="submit" class="button button-completed-offer" value="<?php echo __('Completed Offer', 'offers-for-woocommerce'); ?>" type="submit" disabled="disabled">
                    <?php } else { ?>
                    <input name="submit" id="meta-box-offers-submit" class="button button-primary" value="<?php echo __('Update', 'offers-for-woocommerce'); ?>" type="submit">
                    <?php } ?>
                    <div class="angelleye-clearfix"></div>
                </div>

            <div id="aeofwc-delete-action">
                <a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID );?>"><?php echo __('Move to Trash', 'offers-for-woocommerce'); ?></a>
            </div>

                <?php if( isset( $current_status_value ) && $current_status_value == 'completed-offer' ) { ?>
                <div class="offer-order-meta">
                    <h5><?php echo __('Related Orders', 'offers-for-woocommerce'); ?></h5>
                    <?php if( isset( $offer_order_meta ) ) { ?>
                    <dl class="">
                        <?php foreach( $offer_order_meta as $key => $metavalue ) { ?>
                            <?php echo '<dt class="">'. $key . ': ' . $metavalue .'</dt>'; ?>
                        <?php }?>
                    </dl>
                    <?php } ?>
                </div>
                <?php } ?>
                <div class="angelleye-clearfix"></div>
            </div>
            <div class="angelleye-clearfix"></div>
        </div>
    </div>
    <div class="angelleye-clearfix"></div>
    <?php do_action('angelleye_display_extra_product_details', $post->ID); ?>
</div>
<div class="angelleye-clearfix"></div>
<?php } ?>
