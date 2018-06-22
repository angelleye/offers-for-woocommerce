<?php
/**
 * Admin view
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @since	  0.1.0
 * @package   Angelleye_Offers_For_Woocommerce_Admin
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 */
?>
<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_settings';?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?>
    <a class="add-new-h2" href="edit.php?post_type=woocommerce_offer"><?php echo __('Manage Offers', 'offers-for-woocommerce'); ?></a>
    </h2>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=<?php echo 'offers-for-woocommerce'; ?>&tab=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'nav-tab-active' : ''; ?>"><?php echo __('General Settings', 'offers-for-woocommerce'); ?></a>
        <a href="?page=<?php echo 'offers-for-woocommerce'; ?>&tab=display_settings" class="nav-tab <?php echo $active_tab == 'display_settings' ? 'nav-tab-active' : ''; ?>"><?php echo __('Display Settings', 'offers-for-woocommerce'); ?></a>
        <a href="?page=<?php echo 'offers-for-woocommerce'; ?>&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>"><?php echo __('Tools', 'offers-for-woocommerce'); ?></a>
        <?php do_action('offers_for_woocommerce_setting_tab_content_save'); ?>
        <?php do_action('offers_for_woocommerce_setting_tab'); ?>
    </h2>

    <?php if( $active_tab == 'display_settings' ) { ?>
        <form method="post" action="options.php" id="woocommerce_offers_options_form">
    <?php
        settings_fields( 'offers_for_woocommerce_options_display' );
        do_settings_sections( 'offers_for_woocommerce_display_settings' );

        submit_button();
    ?>
        </form>
    <?php } elseif( $active_tab == 'tools' ) { ?>
    
        <form id="woocommerce_offers_options_form_bulk_tool_enable_offers" autocomplete="off" action="<?php echo admin_url('options-general.php?page=offers-for-woocommerce&tab=tools'); ?>" method="post">
        <!--<p><strong>Here we have provided useful tools for managing Offers for WooCommerce.</strong>
            <br>Available Tools: <a href="#ofwc-t1">Bulk enable/disable offers</a>
        </p>-->
        <a name="ofwc-t1"></a>
        <div class="angelleye-offers-tools-wrap">
            <h3><?php echo __('Bulk Edit Tool for Products', 'offers-for-woocommerce'); ?></h3>
            <div><?php echo __('Select from the options below to enable / disable offers on multiple products at once.', 'offers-for-woocommerce'); ?></div>

            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-type">
                <label for="ofwc-bulk-action-type"><?php echo __('Action', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofwc_bulk_action_type" id="ofwc-bulk-action-type" required="required">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="enable"><?php echo __('Enable Offers', 'offers-for-woocommerce'); ?></option>
                        <option value="disable"><?php echo __('Disable Offers', 'offers-for-woocommerce'); ?></option>
                        <option value="enable_onexit"><?php echo __('Enable Offers On Exit', 'offers-for-woocommerce'); ?></option>
                        <option value="disable_onexit"><?php echo __('Disable Offers On Exit', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-type">
                <label for="ofwc-bulk-action-target-type"><?php echo __('Target', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofwc_bulk_action_target_type" id="ofwc-bulk-action-target-type" required="required">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="all"><?php echo __('All products', 'offers-for-woocommerce'); ?></option>
                        <option value="featured"><?php echo __('Featured products', 'offers-for-woocommerce'); ?></option>
                        <option value="where"><?php echo __('Where...', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-where-type angelleye-hidden">
                <label for="ofwc-bulk-action-target-where-type"><?php echo __('Where', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofwc_bulk_action_target_where_type" id="ofwc-bulk-action-target-where-type">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="category"><?php echo __('Category...', 'offers-for-woocommerce'); ?></option>
                        <option value="product_type"><?php echo __('Product type...', 'offers-for-woocommerce'); ?></option>
                        <option value="price_greater"><?php echo __('Price greater than...', 'offers-for-woocommerce'); ?></option>
                        <option value="price_less"><?php echo __('Price less than...', 'offers-for-woocommerce'); ?></option>
                        <option value="stock_greater"><?php echo __('Stock greater than...', 'offers-for-woocommerce'); ?></option>
                        <option value="stock_less"><?php echo __('Stock less than...', 'offers-for-woocommerce'); ?></option>
                        <option value="instock"><?php echo __('In-stock', 'offers-for-woocommerce'); ?></option>
                        <option value="outofstock"><?php echo __('Out-of-stock', 'offers-for-woocommerce'); ?></option>
                        <option value="sold_individually"><?php echo __('Sold individually', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-where-category angelleye-hidden">
                <label for="ofwc-bulk-action-target-where-category"><?php echo __('Category', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofwc_bulk_action_target_where_category" id="ofwc-bulk-action-target-where-category">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <?php
                        if($product_cats)
                        {
                            foreach($product_cats as $cat)
                            {
                                echo '<option value="'.$cat->slug.'">'.$cat->cat_name.'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-where-product-type angelleye-hidden">
                <label for="ofwc-bulk-action-target-where-product-type">Product type</label>
                <div>
                    <select name="ofwc_bulk_action_target_where_product_type" id="ofwc-bulk-action-target-where-product-type">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="simple"><?php echo __('Simple', 'offers-for-woocommerce'); ?></option>
                        <option value="variable"><?php echo __('Variable', 'offers-for-woocommerce'); ?></option>
                        <option value="grouped"><?php echo __('Grouped', 'offers-for-woocommerce'); ?></option>
                        <option value="external"><?php echo __('External', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-where-price-value angelleye-hidden">
                <label for="ofwc-bulk-action-target-where-price-value"></label>
                <div>
                    <input type="text" name="ofwc_bulk_action_target_where_price_value" id="ofwc-bulk-action-target-where-price-value">
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-where-stock-value angelleye-hidden">
                <label for="ofwc-bulk-action-target-where-stock-value"></label>
                <div>
                    <input type="text" name="ofwc_bulk_action_target_where_stock_value" id="ofwc-bulk-action-target-where-stock-value">
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section">
                <label for="ofwc-bulk-action-target-where-stock-value"></label>
                <div>
                    <button class="button button-primary" id="bulk-enable-tool-submit" name="bulk_enable_tool_submit"><?php echo __('Process', 'offers-for-woocommerce'); ?></button>
                </div>
            </div>
            <div class="angelleye-offers-clearfix"></div>
        </div>
        </form>
    
        
        <form id="ofw_tool_enable_auto_accept_decline" autocomplete="off" action="<?php echo admin_url('options-general.php?page=offers-for-woocommerce&tab=tools'); ?>" method="post">
        <!--<p><strong>Here we have provided useful tools for managing Offers for WooCommerce.</strong>
            <br>Available Tools: <a href="#ofwc-t1">Bulk enable/disable offers</a>
        </p>-->
        <a name="ofwc-t1"></a>
        <div class="ofw-enable-auto-accept-decline">
            <h3><?php echo __('Bulk Edit Tool for Automatically Accepting or Declining Offers', 'offers-for-woocommerce'); ?></h3>
            <div><?php echo __('Select from the options below to enable or disable automated acceptance or declining of offers on multiple products at once.', 'offers-for-woocommerce'); ?></div>

            <div class="ofw-tool-auto-accept-decline-action-section ofw-bulk-tool-action-type">
                <label for="ofw-bulk-tool-action-type"><?php echo __('Action', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofw_bulk_tool_action_type" id="ofw-bulk-tool-action-type" required="required">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="accept_enable"><?php echo __('Enable auto accept offer', 'offers-for-woocommerce'); ?></option>
                        <option value="accept_disable"><?php echo __('Disable auto accept offer', 'offers-for-woocommerce'); ?></option>
                        <option value="decline_enable"><?php echo __('Enable auto decline offer', 'offers-for-woocommerce'); ?></option>
                        <option value="decline_disable"><?php echo __('Disable auto decline offer', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="ofw-tool-auto-accept-decline-action-section ofw-bulk-tool-auto-accept-percentage angelleye-hidden">
                <label for="ofw-bulk-tool-auto-accept-percentage"><?php echo __( 'Auto Accept Percentage', 'offers-for-woocommerce' ) ; ?></label>
                <div>
                    <input type="number" name="ofw_bulk_tool_auto_accept_percentage" id="ofw-bulk-tool-auto-accept-percentage" max="100" min="1" placeholder="Enter Percentage">
                </div>
            </div>
            <div class="ofw-tool-auto-accept-decline-action-section ofw-bulk-tool-auto-decline-percentage angelleye-hidden">
                <label for="ofw-bulk-tool-auto-decline-percentage"><?php echo __( 'Auto Decline Percentage', 'offers-for-woocommerce' ) ; ?></label>
                <div>
                    <input type="number" name="ofw_bulk_tool_auto_decline_percentage" id="ofw-bulk-tool-auto-decline-percentage" max="100" min="1" placeholder="Enter Percentage">
                </div>
            </div>
            <div class="ofw-tool-auto-accept-decline-action-section ofw-bulk-tool-action-target-type">
                <label for="ofw-bulk-tool-action-target-type"><?php echo __('Target', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofw_bulk_tool_action_target-type" id="ofw-bulk-tool-action-target-type" required="required">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="all"><?php echo __('All products', 'offers-for-woocommerce'); ?></option>
                        <option value="featured"><?php echo __('Featured products', 'offers-for-woocommerce'); ?></option>
                        <option value="where"><?php echo __('Where...', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="ofw-tool-auto-accept-decline-action-section ofw-bulk-tool-action-target-where-type angelleye-hidden">
                <label for="ofw-bulk-tool-action-target-where-type"><?php echo __('Where', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofw_bulk_tool_action_target_where_type" id="ofw-bulk-tool-action-target-where-type">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="category"><?php echo __('Category...', 'offers-for-woocommerce'); ?></option>
                        <option value="product_type"><?php echo __('Product type...', 'offers-for-woocommerce'); ?></option>
                        <option value="price_greater"><?php echo __('Price greater than...', 'offers-for-woocommerce'); ?></option>
                        <option value="price_less"><?php echo __('Price less than...', 'offers-for-woocommerce'); ?></option>
                        <option value="stock_greater"><?php echo __('Stock greater than...', 'offers-for-woocommerce'); ?></option>
                        <option value="stock_less"><?php echo __('Stock less than...', 'offers-for-woocommerce'); ?></option>
                        <option value="instock"><?php echo __('In-stock', 'offers-for-woocommerce'); ?></option>
                        <option value="outofstock"><?php echo __('Out-of-stock', 'offers-for-woocommerce'); ?></option>
                        <option value="sold_individually"><?php echo __('Sold individually', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="ofw-tool-auto-accept-decline-action-section ofw-bulk-tool-target-where-category angelleye-hidden">
                <label for="ofw-bulk-tool-target-where-category"><?php echo __('Category', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofw_bulk_tool_target_where_category" id="ofw-bulk-tool-target-where-category">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <?php
                        if($product_cats)
                        {
                            foreach($product_cats as $cat)
                            {
                                echo '<option value="'.$cat->slug.'">'.$cat->cat_name.'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="ofw-tool-auto-accept-decline-action-section ofw-bulk-tool-target-where-product-type angelleye-hidden">
                <label for="ofw-bulk-tool-target-where-product-type">Product type</label>
                <div>
                    <select name="ofw_bulk_tool_target_where_product_type" id="ofw-bulk-tool-target-where-product-type">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="simple"><?php echo __('Simple', 'offers-for-woocommerce'); ?></option>
                        <option value="variable"><?php echo __('Variable', 'offers-for-woocommerce'); ?></option>
                        <option value="grouped"><?php echo __('Grouped', 'offers-for-woocommerce'); ?></option>
                        <option value="external"><?php echo __('External', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="ofw-tool-auto-accept-decline-action-section ofw-bulk-tool-action-target-where-price-value angelleye-hidden">
                <label for="ofw-bulk-tool-action-target-where-price-value"></label>
                <div>
                    <input type="text" name="ofw_bulk_tool_action_target_where_price_value" id="ofw-bulk-tool-action-target-where-price-value">
                </div>
            </div>
            <div class="ofw-tool-auto-accept-decline-action-section ofw-bulk-tool-target-where-stock-value angelleye-hidden">
                <label for="ofw-bulk-tool-target-where-stock-value"></label>
                <div>
                    <input type="text" name="ofw_bulk_tool_target_where_stock_value" id="ofw-bulk-tool-target-where-stock-value">
                </div>
            </div>
            
            <div class="ofw-tool-auto-accept-decline-action-section">
                <label for="bulk_enable_auto_accept_decline_tool_submit"></label>
                <div>
                    <button class="button button-primary" id="ofw_bulk-acd_enable-tool-submit" name="bulk_enable_auto_accept_decline_tool_submit"><?php echo __('Process', 'offers-for-woocommerce'); ?></button>
                </div>
            </div>
            <div class="angelleye-offers-clearfix"></div>
        </div>
        </form>
    <?php
    /*
     *   Minimum Offer Price
     *   If I have 3 items that are $200, $400, & $600, then
     *   I can set a percentage to make the minimum offer exactly $99 on all three.     
    */
    ?>
    <form id="ofw_tool_minimun_offer_price_form" autocomplete="off" action="<?php echo admin_url('options-general.php?page=offers-for-woocommerce&tab=tools'); ?>" method="post">
        <a name="ofwc-t1"></a>
        <div class="angelleye-offers-tools-wrap">
            <h3><?php echo __('Minimum Offer Price', 'offers-for-woocommerce'); ?></h3>
            <div><?php echo __('Select from the options below to set Minimum Offer Price on multiple products at once.', 'offers-for-woocommerce'); ?></div>

            <div class="angelleye-offers-tools-bulk-action-section">
                <label for="ofw-minimum-offer-action-type"><?php echo __('Action', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofw_minimum_offer_action_type" id="ofw-minimum-offer-action-type" required="required">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="ofwc_minimum_offer_price_enable"><?php echo __('Enable', 'offers-for-woocommerce'); ?></option>
                        <option value="ofwc_minimum_offer_price_disable"><?php echo __('Disable', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="angelleye-offers-tools-bulk-action-section ofwc-minimun-offer-price-input angelleye-hidden">
                <label for="minimun-offer-price-input"><span id="ae_offer_type_chnage_lable"><?php echo __('Minimum Offer Price', 'offers-for-woocommerce'); ?></span></label>
                <div>
                    <input type="number" name="minimun_offer_price_input" id="minimun-offer-price-input" min="0">
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofw-minimum-offer-price-type angelleye-hidden">
                <label for="ofw-minimum-offer-price-type"><?php echo __('Offer Price Type', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofw_minimum_offer_price_type" id="ofw-minimum-offer-price-type">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="percentage"><?php echo __('Percentage', 'offers-for-woocommerce'); ?></option>
                        <option value="price"><?php echo __('Price', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="angelleye-offers-tools-bulk-action-section ofw-minimum-offer-price-target-type">
                <label for="ofw-bulk-tool-action-target-type"><?php echo __('Target', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofw_minimum_offer_price_target-type" id="ofw-minimum-offer-price-target-type" required="required">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="all"><?php echo __('All products', 'offers-for-woocommerce'); ?></option>
                        <option value="featured"><?php echo __('Featured products', 'offers-for-woocommerce'); ?></option>
                        <option value="where"><?php echo __('Where...', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofw-minimum-offer-price-target-where-type angelleye-hidden">
                <label for="ofw-bulk-tool-action-target-where-type"><?php echo __('Where', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofw_minimum_offer_price_target_where_type" id="ofw-minimum-offer-price-target-where-type">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="category"><?php echo __('Category...', 'offers-for-woocommerce'); ?></option>
                        <option value="product_type"><?php echo __('Product type...', 'offers-for-woocommerce'); ?></option>
                        <option value="price_greater"><?php echo __('Price greater than...', 'offers-for-woocommerce'); ?></option>
                        <option value="price_less"><?php echo __('Price less than...', 'offers-for-woocommerce'); ?></option>
                        <option value="stock_greater"><?php echo __('Stock greater than...', 'offers-for-woocommerce'); ?></option>
                        <option value="stock_less"><?php echo __('Stock less than...', 'offers-for-woocommerce'); ?></option>
                        <option value="instock"><?php echo __('In-stock', 'offers-for-woocommerce'); ?></option>
                        <option value="outofstock"><?php echo __('Out-of-stock', 'offers-for-woocommerce'); ?></option>
                        <option value="sold_individually"><?php echo __('Sold individually', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofw-minimum-offer-price-target-where-category angelleye-hidden">
                <label for="ofw-bulk-tool-target-where-category"><?php echo __('Category', 'offers-for-woocommerce'); ?></label>
                <div>
                    <select name="ofw_minimum_offer_price_target_where_category" id="ofw-minimum-offer-price-target-where-category">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <?php
                        if($product_cats)
                        {
                            foreach($product_cats as $cat)
                            {
                                echo '<option value="'.$cat->slug.'">'.$cat->cat_name.'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofw-minimum-offer-price-target-where-product-type angelleye-hidden">
                <label for="ofw-minimum-offer-price-target-where-product-type">Product type</label>
                <div>
                    <select name="ofw_minimum_offer_price_target_where_product_type" id="ofw-minimum-offer-price-target-where-product-type">
                        <option value=""><?php echo __('- Select option', 'offers-for-woocommerce'); ?></option>
                        <option value="simple"><?php echo __('Simple', 'offers-for-woocommerce'); ?></option>
                        <option value="variable"><?php echo __('Variable', 'offers-for-woocommerce'); ?></option>
                        <option value="grouped"><?php echo __('Grouped', 'offers-for-woocommerce'); ?></option>
                        <option value="external"><?php echo __('External', 'offers-for-woocommerce'); ?></option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofw-minimum-offer-price-target-where-price-value angelleye-hidden">
                <label for="ofw-bulk-tool-action-target-where-price-value"></label>
                <div>
                    <input type="text" name="ofw_minimum_offer_price_target_where_price_value" id="ofw-bulk-tool-action-target-where-price-value">
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofw-minimum-offer-price-target-where-stock-value angelleye-hidden">
                <label for="ofw-bulk-tool-target-where-stock-value"></label>
                <div>
                    <input type="text" name="ofw_minimum_offer_price_target_where_stock_value" id="ofw-minimum-offer-price-target-where-stock-value">
                </div>
            </div>
            
            <div class="angelleye-offers-tools-bulk-action-section">
                <label for="ofw_minimum_offer_price_tool_submit"></label>
                <div>
                    <button class="button button-primary" id="ofw-minimum-offer-price-tool-submit" name="ofw_minimum_offer_price_tool_submit"><?php echo __('Process', 'offers-for-woocommerce'); ?></button>
                </div>
            </div>
            <div class="angelleye-offers-clearfix"></div>
        </div>
        </form>

    <?php } elseif($active_tab == "general_settings") { ?>
        <form method="post" action="options.php" id="woocommerce_offers_options_form">
    <?php
        settings_fields( 'offers_for_woocommerce_options_general' );
        do_settings_sections( 'offers_for_woocommerce_general_settings' );

        submit_button();
    ?>
        </form>
    <?php } ?>
    <?php do_action('offers_for_woocommerce_setting_tab_content'); ?>
</div>