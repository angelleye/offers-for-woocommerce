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
                <h5>Product Data</h5>
                <?php
                if(!isset($_product)) {
                    echo __('Product not found', 'angelleye_offers_for_woocommerce');
                } else { ?>

                    <ul class="offer-product-meta-image-wrap"><a href="<?php echo $_product_permalink; ?>" target="_blank" title="Click to view product"><?php echo $_product_image; ?></a></ul>
                    <ul class="offer-product-meta-values-wrap">
                        <li><span>Product: </span><?php echo (isset($_product_formatted_name)) ? '<a href="'.$_product_permalink.'" target="_blank" title="Click to view product">'.$_product_formatted_name.'</a>&nbsp;-&nbsp;<a href="post.php?post='.$_product->post->ID.'&action=edit" title="Click to edit product"><span>('.$_product->post->ID.')</span></a>' : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                        <?php if( $_product_attributes ) { ?>
                            <li><span>Attributes: </span><?php echo ucwords( implode( ", ", $_product_attributes ) ); ?></li>
                        <?php } ?>
                        <li><span>Regular Price: </span><?php echo (isset($_product_regular_price)) ? get_woocommerce_currency_symbol().number_format( str_replace(",", "", $_product_regular_price), 2) : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                        <?php if($_product_sale_price) { ?>
                            <li><span>Sale Price: </span><?php echo (isset($_product_sale_price)) ? get_woocommerce_currency_symbol().number_format( str_replace(",", "", $_product_sale_price), 2) : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                        <?php } ?>
                        <?php if(isset($_product_stock) && $_product_stock == 0  && $_product_managing_stock ) { ?>
                            <li>
                                <span>Stock: </span><?php echo (isset($_product_stock) && $_product_stock != '' ) ? $_product_stock : '0'; ?>
                                <?php if($_product_backorders_allowed) { ?>
                                    <?php echo ' ('. __('can be backordered', 'angelleye_offers_for_woocommerce') . ')'; ?>
                                <? } ?>
                            </li>
                        <?php } else { ?>
                            <li>
                                <span>Stock: </span><?php echo (isset($_product_stock) && $_product_stock != '' && $_product_managing_stock ) ? $_product_stock : ' ('. __('not managed','angelleye_offers_for_woocommerce') . ')'; ?>
                                <?php if($_product_backorders_allowed) { ?>
                                    <?php echo ' ('. __('can be backordered', 'angelleye_offers_for_woocommerce') . ')'; ?>
                                <? } ?>
                            </li>
                        <? } ?>
                        <?php if( !$_product_in_stock && (!$_product_stock || $_product_stock == '') ) { ?>
                            <li>
                                <span class="out-of-stock-offer"><?php echo __('Out of Stock', 'angelleye_offers_for_woocommerce' ); ?></span>
                            </li>
                        <? } elseif( !$_product_in_stock && $_product_stock ) { ?>
                            <li>
                                <span class="out-of-stock-offer"><?php echo __('Not enough stock to fulfill offer', 'angelleye_offers_for_woocommerce' ); ?></span>
                            </li>
                        <? } ?>
                        <input id="offer-max-stock-available" type="hidden" value="<?php echo ( isset($_product_stock) ) ? $_product_stock : '' ?>">
                        <input id="offer-backorders-allowed" type="hidden" value="<?php echo ( $_product_backorders_allowed ) ? 'true' : 'false';?>">
                    </ul>
                <? } ?>
            </div>
        </div>
        <div class="angelleye-col-1-2 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5>
                    Buyer Data
                    <?php if( $author_data ) { ?>
                        <a id="angelleye-offer-buyer-stats-toggle" class="angelleye-offer-buyer-stats-toggle" href="javascript:;" title="<?php echo __('View offer history', 'angelleye_offers_for_woocommerce');?>"><span id="angelleye-offer-buyer-stats-counter"><?php echo __('Buyer History', 'angelleye_offers_for_woocommerce'). ': <span class="total-offers-count">'. $author_data->offer_counts['all'] . '</span>'; ?></span></a>
                    <?php } ?>
                </h5>
                <ul class="offer-buyer-meta-values-wrap">
                    <li><span>Name: </span><?php echo (isset($postmeta['offer_name'][0])) ? stripslashes($postmeta['offer_name'][0]) : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                    <li><span>Email: </span><?php echo (isset($postmeta['offer_email'][0])) ? '<a href="mailto:'.$postmeta['offer_email'][0].'" target="_blank" title="Click to email">'.$postmeta['offer_email'][0].'</a>' : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                    <li><span>Phone: </span><?php echo (isset($postmeta['offer_phone'][0])) ? stripslashes($postmeta['offer_phone'][0]) : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                    <li><span>Company: </span><?php echo (isset($postmeta['offer_company_name'][0])) ? stripslashes($postmeta['offer_company_name'][0]) : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?></li>
                </ul>
            </div>

            <div class="angelleye-col-container" id="angelleye-offer-buyer-history">
                <?php if( $author_data ) { ?>
                <h5>Buyer Offer History
                    <a id="angelleye-offer-buyer-stats-close" class="angelleye-offer-buyer-stats-toggle" href="javascript:;" title="<?php echo __('Close offer history', 'angelleye_offers_for_woocommerce');?>"><?php echo __('close', 'angelleye_offers_for_woocommerce');?></a>
                </h5>
                <ul class="offer-buyer-history-values-wrap">
                    <table id="offer-buyer-history">
                        <?php foreach($author_data->offer_counts as $key => $count) { ?>
                            <?php if(strtolower($key) != 'all') { ?>
                        <tr>
                            <th><?php echo ucfirst($key) .': '; ?></th>
                            <td><div>
                                <?php echo '<span>'. $count .'</span>';?>
                                <?php if($count > 0) {
                                    $post_status_part = ($key == 'pending') ? 'publish' : $key .'-offer';
                                echo '<a href="edit.php?author=' . $post->post_author . '&post_type=woocommerce_offer&post_status='. $post_status_part .'" class="angelleye-view-buyer-offer-history">view</a>';
                                } else {
                                    echo '<a href="javascript:;" class="angelleye-view-buyer-offer-history no-offer-history">view</a>';
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
    <div class="angelleye-col-m-1-1">
        <div class="angelleye-col-1-4 angelleye-col-m-1-2 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5>Original Data</h5>
                <div class="offer-original-meta-values-wrap">
                    <label for="original-offer-quantity">Orig. Quantity</label>
                    <div>
                        <input type="text" id="original-offer-quantity" value="<?php echo (isset($postmeta['orig_offer_quantity'][0])) ? $postmeta['orig_offer_quantity'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?>" disabled="disabled" />
                    </div>
                    <label for="original-offer-price-per">Orig. Price Per</label>
                    <div>
                        <input type="text" id="original-offer-amount" value="<?php echo (isset($postmeta['orig_offer_price_per'][0])) ? get_woocommerce_currency_symbol().$postmeta['orig_offer_price_per'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?>" disabled="disabled" />
                    </div>
                    <label for="original-offer-price-per">Orig. Amount</label>
                    <div>
                        <input type="text" id="original-offer-price-per" value="<?php echo (isset($postmeta['orig_offer_amount'][0])) ? get_woocommerce_currency_symbol().$postmeta['orig_offer_amount'][0] : __('Missing Meta Value', 'angelleye_offers_for_woocommerce' ); ?>" disabled="disabled" />
                    </div>
                </div>
            </div>
        </div>
        <div class="angelleye-col-1-4 angelleye-col-m-1-2 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5>Counter Values</h5>
                <div class="offer-counter-offer-values-wrap">
                    <label for="offer-quantity">Quantity</label>
                    <div>
                        <?php if( isset( $current_status_value ) && $current_status_value == 'buyercountered-offer' ) { ?>
                            <input type="text" class="offer-counter-value-input" data-m-dec="0" data-l-zero="deny" data-a-form="false" required="required" name="offer_quantity" id="offer-quantity" value="<?php echo (isset($postmeta['offer_buyer_counter_quantity'][0])) ? $postmeta['offer_buyer_counter_quantity'][0] : ''; ?>" autocomplete="off" />
                        <?php } else { ?>
                            <input type="text" class="offer-counter-value-input" data-m-dec="0" data-l-zero="deny" data-a-form="false" required="required" name="offer_quantity" id="offer-quantity" value="<?php echo (isset($postmeta['offer_quantity'][0])) ? $postmeta['offer_quantity'][0] : ''; ?>" autocomplete="off" />
                        <?php } ?>
                    </div>
                    <label for="offer-price-per">Price Per</label>
                    <div class="angelleye-input-group">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <?php if( isset( $current_status_value ) && $current_status_value == 'buyercountered-offer' ) { ?>
                            <input type="text" name="offer_price_per" id="offer-price-per" pattern="([0-9]|\$|,|.)+" data-a-sign="" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" required="required" value="<?php echo (isset($postmeta['offer_buyer_counter_price_per'][0])) ? $postmeta['offer_buyer_counter_price_per'][0] : ''; ?>" autocomplete="off" />
                        <?php } else { ?>
                            <input type="text" name="offer_price_per" id="offer-price-per" pattern="([0-9]|\$|,|.)+" data-a-sign="" data-m-dec="2" data-w-empty="" data-l-zero="keep" data-a-form="false" required="required" value="<?php echo (isset($postmeta['offer_price_per'][0])) ? $postmeta['offer_price_per'][0] : ''; ?>" autocomplete="off" />
                        <?php } ?>
                    </div>
                    <label for="offer-total">Total</label>
                    <div class="angelleye-input-group">
                        <span class="angelleye-input-group-addon"><?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?></span>
                        <?php if( isset( $current_status_value ) && $current_status_value == 'buyercountered-offer' ) { ?>
                            <input type="text" name="offer_amount" id="offer-total" class="form-control" data-currency-symbol="<?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?>" value="<?php echo (isset($postmeta['offer_buyer_counter_amount'][0])) ? $postmeta['offer_buyer_counter_amount'][0] : ''; ?>" disabled="disabled" autocomplete="off" />
                        <?php } else { ?>
                            <input type="text" name="offer_amount" id="offer-total" class="form-control" data-currency-symbol="<?php echo (isset($currency_symbol)) ? $currency_symbol : '$';?>" value="<?php echo (isset($postmeta['offer_amount'][0])) ? $postmeta['offer_amount'][0] : ''; ?>" disabled="disabled" autocomplete="off" />
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="angelleye-col-m-1-1">
        <div class="angelleye-col-1-4 angelleye-col-m-1-2 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5>Buyer Note</h5>
                <textarea name="angelleye_woocommerce_offer_status_notes" id="angelleye_woocommerce_offer_status_notes" class=""></textarea>
            </div>
        </div>
        <div class="angelleye-col-1-4 angelleye-col-m-1-2 angelleye-col-s-1-1">
            <div class="angelleye-col-container">
                <h5>Status</h5>
                <?php if( isset( $current_status_value ) && $current_status_value == 'completed-offer' ) { } else { ?>
                    <div class="offer-post-status-input-wrap">
                        <select name="post_status" autocomplete="off" required="required" <?php if (isset($current_status_value) && $current_status_value == 'completed-offer') echo ' disabled="disabled"'; ?>>
                            <?php if ( (isset($current_status_value) && ( $current_status_value == 'publish' || $current_status_value == 'buyercountered-offer' ) ) || ( !isset($current_status_value) ) ) { ?>
                            <option value="">- Select status</option>
                            <? } ?>
                            <option value="accepted-offer" <?php if (isset($current_status_value) && $current_status_value == 'accepted-offer') echo 'selected="selected"'; ?>>Accepted Offer</option>
                            <option value="countered-offer" <?php if (isset($current_status_value) && $current_status_value == 'countered-offer') echo 'selected="selected"'; ?>>Countered Offer</option>
                            <option value="declined-offer" <?php if (isset($current_status_value) && $current_status_value == 'declined-offer') echo 'selected="selected"'; ?>>Declined Offer</option>
                            <option value="completed-offer" <?php if (isset($current_status_value) && $current_status_value == 'completed-offer') echo 'selected="selected"'; ?>>Completed Offer</option>
                            <option value="on-hold-offer" <?php if (isset($current_status_value) && $current_status_value == 'on-hold-offer') echo 'selected="selected"'; ?>>On Hold</option>
                        </select>
                    </div>
                <?php } ?>
                <input type="hidden" name="woocommerce_offer_summary_metabox_noncename" id="woocommerce_offer_summary_metabox_noncename" value="<?php echo wp_create_nonce( 'woocommerce_offer'.$post->ID ); ?>" />
                <input type="hidden" name="post_previous_status" id="post_previous_status" value="<?php echo (isset($current_status_value)) ? $current_status_value : ''; ?>">

                <div class="woocommerce-offer-final-offer-wrap">
                    <label for="offer-final-offer">Final Offer</label>
                    <div>
                        <input type="checkbox" name="offer_final_offer" id="offer-final-offer" value="1" <?php echo(isset($postmeta['offer_final_offer'][0]) && $postmeta['offer_final_offer'][0] == '1') ? 'checked="checked"' : ''?>>
                    </div>
                </div>

                <div class="woocommerce-offer-expiration-wrap">
                    <label for="offer-expiration-date">Offer Expires</label>
                    <input type="text" name="offer_expiration_date" class="datepicker" id="offer-expiration-date" value="<?php echo(isset($postmeta['offer_expiration_date'][0]) && $postmeta['offer_expiration_date'][0] != '') ? date("Y-m-d", strtotime( $postmeta['offer_expiration_date'][0] )) : ''?>">
                </div>

                <?php $show_notice_msg = ( isset($offer_inventory_msg) && $offer_inventory_msg != '') ? TRUE : FALSE; ?>
                <div id="angelleye-woocommerce-offer-meta-summary-notice-msg" <?php echo (!$show_notice_msg) ? ' class="angelleye-hidden"' : '';?>">
                    <div class="aeofwc-notice-msg-inner"><?php echo ($show_notice_msg) ? $offer_inventory_msg : '';?></div>
                </div>

                <div class="woocommerce-offer-edit-submit-btn-wrap">
                    <?php if( isset( $current_status_value ) && $current_status_value == 'completed-offer' ) { ?>
                    <input name="submit" id="submit" class="button button-completed-offer" value="<?php echo __('Completed Offer', 'angelleye-offers-for-woocommerce'); ?>" type="submit" disabled="disabled">
                    <?php } else { ?>
                    <input name="submit" id="submit" class="button button-primary" value="Update" type="submit">
                    <?php } ?>
                    <div class="angelleye-clearfix"></div>
                </div>

            <div id="aeofwc-delete-action">
                <a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID );?>">Move to Trash</a>
            </div>

                <?php if( isset( $current_status_value ) && $current_status_value == 'completed-offer' ) { ?>
                <div class="offer-order-meta">
                    <h5>Related Orders</h5>
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
</div>
<div class="angelleye-clearfix"></div>
<?php } ?>
