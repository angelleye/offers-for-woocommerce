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
    <a class="add-new-h2" href="edit.php?post_type=woocommerce_offer">Manage Offers</a>
    </h2>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=offers-for-woocommerce&tab=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'nav-tab-active' : ''; ?>">General Settings</a>
        <a href="?page=offers-for-woocommerce&tab=display_settings" class="nav-tab <?php echo $active_tab == 'display_settings' ? 'nav-tab-active' : ''; ?>">Display Settings</a>
        <a href="?page=offers-for-woocommerce&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>">Tools</a>
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
        <p><strong>Here we have provided useful tools for managing Offers for WooCommerce.</strong>
            <br>Available Tools: <a href="#ofwc-t1">Bulk enable/disable offers</a>
        </p>
        <a name="ofwc-t1"></a>
        <div class="angelleye-offers-tools-wrap">
            <h3>Bulk enable/disable offers for products</h3>
            <div>Select options below and click process button to perform bulk action.</div>
            <div>Page will auto-redirect after action is complete.</div>

            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-type">
                <label for="ofwc-bulk-action-type">Action</label>
                <div>
                    <select name="ofwc_bulk_action_type" id="ofwc-bulk-action-type" required="required">
                        <option value="">- Select option</option>
                        <option value="enable">Enable Offers</option>
                        <option value="disable">Disable Offers</option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-type">
                <label for="ofwc-bulk-action-target-type">Target</label>
                <div>
                    <select name="ofwc_bulk_action_target_type" id="ofwc-bulk-action-target-type" required="required">
                        <option value="">- Select option</option>
                        <option value="all">All products</option>
                        <option value="featured">Featured products</option>
                        <option value="where">Where...</option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-where-type angelleye-hidden">
                <label for="ofwc-bulk-action-target-where-type">Where</label>
                <div>
                    <select name="ofwc_bulk_action_target_where_type" id="ofwc-bulk-action-target-where-type">
                        <option value="">- Select option</option>
                        <option value="category">Category...</option>
                        <option value="product_type">Product type...</option>
                        <option value="price_greater">Price greater than...</option>
                        <option value="price_less">Price less than...</option>
                        <option value="stock_greater">Stock greater than...</option>
                        <option value="stock_less">Stock less than...</option>
                        <option value="instock">In-stock</option>
                        <option value="outofstock">Out-of-stock</option>
                        <option value="sold_individually">Sold individually</option>
                    </select>
                </div>
            </div>
            <div class="angelleye-offers-tools-bulk-action-section ofwc-bulk-action-target-where-category angelleye-hidden">
                <label for="ofwc-bulk-action-target-where-category">Category</label>
                <div>
                    <select name="ofwc_bulk_action_target_where_category" id="ofwc-bulk-action-target-where-category">
                        <option value="">- Select option</option>
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
                        <option value="">- Select option</option>
                        <option value="simple">Simple</option>
                        <option value="variable">Variable</option>
                        <option value="grouped">Grouped</option>
                        <option value="external">External</option>
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
                    <button class="button button-primary" id="bulk-enable-tool-submit" name="bulk_enable_tool_submit">Process</button>
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