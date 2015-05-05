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
    <a class="add-new-h2" href="edit.php?post_type=woocommerce_offer"><?php echo __('Manage Offers', $this->plugin_slug); ?></a>
    </h2>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=offers-for-woocommerce&tab=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'nav-tab-active' : ''; ?>"><?php echo __('General Settings', $this->plugin_slug); ?></a>
        <a href="?page=offers-for-woocommerce&tab=display_settings" class="nav-tab <?php echo $active_tab == 'display_settings' ? 'nav-tab-active' : ''; ?>"><?php echo __('Display Settings', $this->plugin_slug); ?></a>
        <a href="?page=offers-for-woocommerce&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>"><?php echo __('Tools', $this->plugin_slug); ?></a>
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
            <h3><?php echo __('Bulk Edit Tool for Products', $this->plugin_slug); ?></h3>
            <div><?php echo __('Select from the options below to enable / disable offers on multiple products at once.', $this->plugin_slug); ?></div>

            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-type">
                <label for="ofwc-bulk-action-type"><?php echo __('Action', $this->plugin_slug); ?></label>
                <div>
                    <select name="ofwc_bulk_action_type" id="ofwc-bulk-action-type" required="required">
                        <option value=""><?php echo __('- Select option', $this->plugin_slug); ?></option>
                        <option value="enable"><?php echo __('Enable Offers', $this->plugin_slug); ?></option>
                        <option value="disable"><?php echo __('Disable Offers', $this->plugin_slug); ?></option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-type">
                <label for="ofwc-bulk-action-target-type"><?php echo __('Target', $this->plugin_slug); ?></label>
                <div>
                    <select name="ofwc_bulk_action_target_type" id="ofwc-bulk-action-target-type" required="required">
                        <option value=""><?php echo __('- Select option', $this->plugin_slug); ?></option>
                        <option value="all"><?php echo __('All products', $this->plugin_slug); ?></option>
                        <option value="featured"><?php echo __('Featured products', $this->plugin_slug); ?></option>
                        <option value="where"><?php echo __('Where...', $this->plugin_slug); ?></option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-where-type angelleye-hidden">
                <label for="ofwc-bulk-action-target-where-type"><?php echo __('Where', $this->plugin_slug); ?></label>
                <div>
                    <select name="ofwc_bulk_action_target_where_type" id="ofwc-bulk-action-target-where-type">
                        <option value=""><?php echo __('- Select option', $this->plugin_slug); ?></option>
                        <option value="category"><?php echo __('Category...', $this->plugin_slug); ?></option>
                        <option value="product_type"><?php echo __('Product type...', $this->plugin_slug); ?></option>
                        <option value="price_greater"><?php echo __('Price greater than...', $this->plugin_slug); ?></option>
                        <option value="price_less"><?php echo __('Price less than...', $this->plugin_slug); ?></option>
                        <option value="stock_greater"><?php echo __('Stock greater than...', $this->plugin_slug); ?></option>
                        <option value="stock_less"><?php echo __('Stock less than...', $this->plugin_slug); ?></option>
                        <option value="instock"><?php echo __('In-stock', $this->plugin_slug); ?></option>
                        <option value="outofstock"><?php echo __('Out-of-stock', $this->plugin_slug); ?></option>
                        <option value="sold_individually"><?php echo __('Sold individually', $this->plugin_slug); ?></option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-where-category angelleye-hidden">
                <label for="ofwc-bulk-action-target-where-category"><?php echo __('Category', $this->plugin_slug); ?></label>
                <div>
                    <select name="ofwc_bulk_action_target_where_category" id="ofwc-bulk-action-target-where-category">
                        <option value=""><?php echo __('- Select option', $this->plugin_slug); ?></option>
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
                        <option value=""><?php echo __('- Select option', $this->plugin_slug); ?></option>
                        <option value="simple"><?php echo __('Simple', $this->plugin_slug); ?></option>
                        <option value="variable"><?php echo __('Variable', $this->plugin_slug); ?></option>
                        <option value="grouped"><?php echo __('Grouped', $this->plugin_slug); ?></option>
                        <option value="external"><?php echo __('External', $this->plugin_slug); ?></option>
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
                    <button class="button button-primary" id="bulk-enable-tool-submit" name="bulk_enable_tool_submit"><?php echo __('Process', $this->plugin_slug); ?></button>
                </div>
            </div>
            <div class="angelleye-offers-clearfix"></div>
        </div>
        </form>

    <?php } else { ?>
        <form method="post" action="options.php" id="woocommerce_offers_options_form">
    <?php
        settings_fields( 'offers_for_woocommerce_options_general' );
        do_settings_sections( 'offers_for_woocommerce_general_settings' );

        submit_button();
    ?>
        </form>
    <?php } ?>
</div>