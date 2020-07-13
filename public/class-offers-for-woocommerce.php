<?php 
/**
 * Offers for WooCommerce - public
 *
 * @package   Angelleye_Offers_For_Woocommerce
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 */

/**
 * Plugin class - public
 *
 * @since	0.1.0
 * @package Angelleye_Offers_For_Woocommerce
 * @author  AngellEYE <andrew@angelleye.com>
 */
class Angelleye_Offers_For_Woocommerce {

    /**
     * Plugin version
     *
     * @since   0.1.0
     *
     * @var     string
     */
    const VERSION = '2.2.1';

    /**
     *
     * Unique pluginidentifier
     *
     * @since    0.1.0
     *
     * @var      string
     */
    protected $plugin_slug = 'offers-for-woocommerce';

    /**
     * Instance of this class
     *
     * @since    0.1.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since    0.1.0
     */
    private function __construct() {
        /**
         * Define email templates path
         */
        define('OFWC_PUBLIC_EMAIL_TEMPLATE_PATH', untrailingslashit(plugin_dir_path(__FILE__)) . '/includes/emails/');
        
        include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/angelleye-offers-for-woocommerce-function.php';
        
        if (!defined('OFWC_EMAIL_TEMPLATE_PATH')) {
            define('OFWC_EMAIL_TEMPLATE_PATH', untrailingslashit(OFW_PLUGIN_URL) . '/admin/includes/emails/');
        }

        /**
         * Activate plugin when new blog is added
         */
        add_action('wpmu_new_blog', array($this, 'activate_new_site'));

        /**
         * Load public-facing style sheet and javascript
         */
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        /*         * *****************************
         * Define custom functionality *
         * ******************************
         */

        /**
         * Init - New Offer Form Submit
         * @since	0.1.0
         */
        add_action( 'wp_ajax_new_offer_form_submit', array( $this, 'new_offer_form_submit' ) );
        add_action( 'wp_ajax_nopriv_new_offer_form_submit', array( $this, 'new_offer_form_submit' ) );

        /* Maybe Add "Make Offer" to Single Product Page */
        add_action('woocommerce_before_single_product', array($this, 'angelleye_ofwc_maybe_add_make_offer_to_single_product'), 1);

        /* Add "Make Offer" button code parts - After shop loop item */
        add_action('woocommerce_after_shop_loop_item', array($this, 'angelleye_ofwc_after_show_loop_item'), 99, 2);

        /* Add query vars for api endpoint
         * Used for add offer to cart
         * @since   0.1.0
         */
        add_filter('query_vars', array($this, 'add_query_vars'), 0);

        /* Add api endpoint listener
         * Used for add offer to cart
         * @since   0.1.0
         */
        add_action('parse_request', array($this, 'sniff_api_requests'), 0);

        /**
         * Sets qty and price on any offer items in cart
         * @param $cart_object
         * @since   0.1.0
         */
        add_action('woocommerce_before_calculate_totals', array($this, 'my_woocommerce_before_calculate_totals'),99,1);

        /*
         * Filter - get_cart_items_from_session
         * @since   0.1.0
         */
        add_filter('woocommerce_get_cart_item_from_session', array($this, 'get_cart_items_from_session'), 1, 3);

        /*
         * Filter - Add email class to WooCommerce for 'Accepted Offer'
         * @since   0.1.0
         */
        add_filter('woocommerce_email_classes', array($this, 'add_woocommerce_email_classes'));

        /**
         * Action - woocommerce_before_checkout_process
         * Checks for valid offer before checkout process
         * @since   0.1.0
         */
        add_action('woocommerce_before_checkout_process', array($this, 'ae_ofwc_woocommerce_before_checkout_process'));

        add_action('auto_accept_auto_decline_handler', array($this, 'ofwc_auto_accept_auto_decline_handler'), 10, 4);

        add_action('init', array($this, 'ofw_create_required_files'), 0);

        add_action('woocommerce_make_offer_form_end', array($this, 'woocommerce_make_offer_form_end_own'), 10, 1);

        add_action('woocommerce_after_offer_submit', array($this, 'ofw_mailing_list_handler'), 10, 2);

        add_filter( 'woocommerce_coupons_enabled', array($this, 'ofw_coupons_enabled' ), 10, 1);
        
        //add_action( 'woocommerce_after_my_account', array($this, 'ofw_woocommerce_after_my_account'));
        
        add_filter( 'woocommerce_shipping_methods', array($this, 'add_your_shipping_method' ), 10, 1);
        add_action( 'woocommerce_shipping_init', array($this, 'your_shipping_method_init' ));
        add_filter( 'woocommerce_package_rates', array($this, 'hide_shipping_when_offer_for_woocommerce_is_available'), 10, 2 );
        add_shortcode('aeofwc_highest_current_offer', array($this, 'ofw_display_highest_current_offer_shortcode'), 10);
        add_filter('woocommerce_is_purchasable',array($this,'angelleye_ofwc_woocommerce_is_purchasable'),999,2);
        add_action( 'woocommerce_before_customer_login_form', array($this, 'ofw_before_customer_login_form'));
        add_filter('woocommerce_login_redirect',array($this,'ofw_login_redirect'),10,1);
        add_filter('woocommerce_registration_redirect',array($this,'ofw_login_redirect'),10,1);
        add_filter('woocommerce_loop_add_to_cart_link',array($this,'ofw_woocommerce_loop_add_to_cart_link'),10,2);
        add_action( 'admin_bar_menu', array($this, 'ofwc_manage_offer_admin_bar_callback'), 999 );
        /*this will add shortcode for recent offers */
        add_shortcode('aeofwc_recent_offers', array($this, 'angelleye_ofw_recent_offers'));
        
        add_filter('woocommerce_order_item_display_meta_key', array($this, 'ofwc_translate_order_item_display_meta_key'), 99, 1 );
        
        // when admin create open offer from admin side and it will allow customer to buy.
        add_filter('ofw_not_allow_invalid_offer_status', array($this, 'ofw_not_allow_invalid_offer_status'), 10, 2);
        
        add_filter('ofw_admin_created_offer_status', array($this, 'ofw_admin_created_offer_status'), 10, 2);
        
        add_filter('woocommerce_cart_item_quantity', array($this, 'ofw_woocommerce_cart_item_quantity'), 10, 3);
        
        add_filter('woocommerce_account_menu_items', array($this, 'ofw_woocommerce_account_menu_items'), 10);
        add_filter('woocommerce_get_query_vars', array($this, 'ofw_woocommerce_get_query_vars'), 10, 1);
        add_action( 'init', array($this, 'ofw_add_offer_endpoint') );
        add_action( 'woocommerce_account_offers_endpoint', array($this, 'ofw_my_offer_content') );
        add_filter( 'woocommerce_endpoint_offers_title', array($this, 'ofw_woocommerce_endpoint_offers_title'), 10, 2);
        
        /* this will display the data of Product addon if plugin is activated - Start */
        
        $active_plugins = (array) get_option( 'active_plugins', array() );        
        if (is_multisite())
            $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        if(in_array( 'woocommerce-product-addons/woocommerce-product-addons.php', $active_plugins ) || array_key_exists( 'woocommerce-product-addons/woocommerce-product-addons.php', $active_plugins )){
            add_filter( 'woocommerce_cart_item_name', array($this,'render_meta_on_cart_item'), 1, 3 );    
        }
        
        
        /* this will display the data of Product addon if plugin is activated - End */
    }       

    public function angelleye_ofwc_after_add_to_cart_form(){
        global $post;
        $parent_offer_id = (isset($_GET['offer-pid']) && $_GET['offer-pid'] != '') ? wc_clean($_GET['offer-pid']) : '';
        $parent_post_status = get_post_status($parent_offer_id);
        $on_exit_enabled = get_post_meta($post->ID, 'offers_for_woocommerce_onexit_only', true);
        $on_exit_enabled = (isset($on_exit_enabled) && $on_exit_enabled == 'yes') ? true : false;
        if($on_exit_enabled){
            if($parent_offer_id > 0 && isset($parent_post_status) && $parent_post_status == 'countered-offer'){
                $this->ofw_display_highest_current_offer();
                $this->ofw_display_pending_offer_lable_product_details_page($post->ID);
            }
        } else {
            $this->ofw_display_highest_current_offer();
            $this->ofw_display_pending_offer_lable_product_details_page($post->ID);
        }
    }    
    
    /* Below function works as shortcode to display recent offers table*/
    public function angelleye_ofw_recent_offers() {
        include_once(OFW_PLUGIN_URL . 'public/views/my-offers.php');
    }
    /* Below function works as shortcode to display recent offers table End*/ 

    /* this will display the data of Product addon if plugin is activated - Start */
    public function render_meta_on_cart_item($title = null, $cart_item = null, $cart_item_key = null) {
        if ($cart_item_key && is_cart()) {
            $offers_product_addon = get_post_meta($cart_item['woocommerce_offer_id'], 'offers_product_addon', true);
            if (!empty($offers_product_addon)) {
                echo $title;
                foreach ($offers_product_addon as $key => $offerProducts) {
                    foreach ($offerProducts['options'] as $labelPrices) {
                        echo "<dl class='variation'><dt class=''><p>{$offerProducts['group']} - {$labelPrices['label']} ({$labelPrices['price']})</p></dt></dl>";
                        echo "<dl><dd><p>{$labelPrices['value']}</p></dd></dl>";
                    }
                }
            } else {
                echo $title;
            }            
        } else {
            echo $title;
        }
    }

    /* this will display the data of Product addon if plugin is activated - End */

        /**
     * display notice on login form if user login is required
     *
     * @since	0.1.0
     */
    public function ofw_before_customer_login_form() {
        if(isset($_GET['ref']) && $_GET['ref'] == 'make-offer' && !is_user_logged_in()){
            wc_print_notice( __( 'Please log in to make an offer.', 'offers-for-woocommerce' ), 'error' );
        }
    }
    
    /**
     * Redirect back to product page after login
     *
     * @since	0.1.0
     */
    public function ofw_login_redirect($redirect) {
        if(isset($_GET['backto']) && !empty($_GET['backto']) && $_GET['ref'] == 'make-offer'){
            $join_url = (strpos(esc_url_raw($_GET['backto']), '?') !== false) ? '&aewcobtn=1' : '?aewcobtn=1';
            return esc_url_raw($_GET['backto']).$join_url;
        }
        return $redirect;
    }
   
   /**
     * Conditionally add the Make an Offer Markup to Single Products
     *
     * @since   1.4.12
     */
    public function angelleye_ofwc_maybe_add_make_offer_to_single_product() {
        global $product;
        if(  'yes' == $product->get_meta( 'offers_for_woocommerce_enabled', true ) ) {
            /* Add "Make Offer" button code parts - Before add to cart */
            add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'angelleye_ofwc_before_add_to_cart_button' ) );
            
            add_action('woocommerce_after_add_to_cart_form',array($this,'angelleye_ofwc_after_add_to_cart_form'));
            /* Add "Make Offer" button code parts - After add to cart */
            add_action('woocommerce_after_add_to_cart_button', array($this, 'angelleye_ofwc_after_add_to_cart_button'));
            /* Add "Make Offer" button code parts - After summary for products without price */
            add_action('woocommerce_after_single_product_summary', array($this, 'angelleye_ofwc_woocommerce_after_single_product_summary'), 11);
            /* Add "Lighbox Make Offer Form" before single product content */
            add_action('woocommerce_before_single_product', array($this, 'angelleye_ofwc_lightbox_make_offer_form'));
            /* Add "Make Offer" product tab on product single view */
            add_filter('woocommerce_product_tabs', array($this, 'angelleye_ofwc_add_custom_woocommerce_product_tab'),9, 1);
        }
    }
    
    /**
     * Generates output of offer button
     *
     * @since	0.1.0
     */
    public function angelleye_ofwc_offer_button_output($is_archive = false,$btn_position_class = '') {
        global $post, $wp;        
        global $current_user;
        $req_login = FALSE;
        
        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

        // enable offers for only logged in users
        if ($button_options_general && isset($button_options_general['general_setting_enable_offers_only_logged_in_users']) && $button_options_general['general_setting_enable_offers_only_logged_in_users'] != '') {
            if ($button_options_general && isset($button_options_general['general_setting_enable_offers_hide_untill_logged_in_users']) && $button_options_general['general_setting_enable_offers_hide_untill_logged_in_users'] != '' && !is_user_logged_in()) {
                return;
            } elseif (!is_user_logged_in()) {
                $req_login = TRUE;
            }
            if (isset($button_options_general['general_setting_allowed_roles']) && $button_options_general['general_setting_allowed_roles'] != '' && is_user_logged_in()) {
                $user_data = get_userdata($current_user->ID);
                $user_roles = $user_data->roles;
                $role_match = array_intersect($user_roles, $button_options_general['general_setting_allowed_roles']);
                if (empty($role_match)) {
                    return;
                }
            }
        }
                                        
        $_product = wc_get_product($post->ID);   
        if ( ! is_object( $_product ) ) {
            return;
        }        

        if(isset($button_options_general['general_setting_disabled_make_offer_on_product_sale']) && $button_options_general['general_setting_disabled_make_offer_on_product_sale'] == 1 && $_product->is_on_sale()){
            return;
        }
        $is_external_product = FALSE;
        if(version_compare(WC_VERSION, '3.0', '<')) {
            if(isset($_product->product_type) && $_product->product_type == 'external') {
                $is_external_product = TRUE;
            }
        } else {
            if($_product->get_type() == 'external') {
                $is_external_product = TRUE;
            }
        }
        $is_instock = $_product->is_in_stock();
        
        $custom_tab_options_offers = array(
            'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),
            'on_exit' => get_post_meta($post->ID, 'offers_for_woocommerce_onexit_only', true),
        );
        
        $btn_output = '';
        
        if ($custom_tab_options_offers['enabled'] == 'yes' && !$is_external_product && $is_instock && $custom_tab_options_offers['on_exit'] != 'yes') {
            $button_title = (isset($button_options_display['display_setting_custom_make_offer_btn_text']) && $button_options_display['display_setting_custom_make_offer_btn_text'] != '') ?  __($button_options_display['display_setting_custom_make_offer_btn_text'],'offers-for-woocommerce') : __('Make Offer', 'offers-for-woocommerce');

            $custom_styles_override = '';
            if ($button_options_display) {
                if (isset($button_options_display['display_setting_custom_make_offer_btn_text_color']) && $button_options_display['display_setting_custom_make_offer_btn_text_color'] != '') {
                    $custom_styles_override .= 'color:' . $button_options_display['display_setting_custom_make_offer_btn_text_color'] . ';';
                }
                if (isset($button_options_display['display_setting_custom_make_offer_btn_color']) && $button_options_display['display_setting_custom_make_offer_btn_color'] != '') {
                    $custom_styles_override .= ' background:' . $button_options_display['display_setting_custom_make_offer_btn_color'] . '; border-color:' . $button_options_display['display_setting_custom_make_offer_btn_color'] . ';';
                }
            }
            
            $lightbox_class = (isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') ? ' offers-for-woocommerce-make-offer-button-single-product-lightbox' : '';
            $button_class = (isset($button_options_display['display_setting_custom_make_offer_btn_class']) && $button_options_display['display_setting_custom_make_offer_btn_class'] != '') ? $button_options_display['display_setting_custom_make_offer_btn_class'] : '';            
            $aeofwc_offer_button_wrap_class = apply_filters('aeofwc_offer_button_wrap_class', 'single_variation_wrap_angelleye ofwc_offer_tab_form_wrap single_offer_button');
            $btn_output = '<div class=" ' . $aeofwc_offer_button_wrap_class.'">';
            $permalink = get_permalink($post->ID);
            $permalink.= (strpos($permalink, '?') !== false) ? '&aewcobtn=1' : '?aewcobtn=1';
            if($req_login){
                $redirect_url = '';
                if($is_archive){
                    $redirect_url = get_permalink( get_option('woocommerce_myaccount_page_id') ) . '?ref=make-offer&backto='.get_permalink($post->ID);
                    $button = '<a href="' . $redirect_url . '" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-catalog button alt '.$button_class.' '.$btn_position_class.'" style="' . $custom_styles_override . '">' . $button_title . '</a>';
                } else {
                    $redirect_url = get_permalink( get_option('woocommerce_myaccount_page_id') ) . '?ref=make-offer&backto='.home_url(add_query_arg(array(),$wp->request));
                    $button = '<a href="'.$redirect_url.'"><button type="button" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-single-product '.$button_class.'  ' . $lightbox_class . ' button alt '.$btn_position_class.'" style="' . $custom_styles_override . '">' . $button_title . '</button></a>';
                }
            } else {
                if($is_archive){
                    $button = '<a href="' . $permalink . '" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-catalog button alt  '.$button_class.' '.$btn_position_class.'" style="' . $custom_styles_override . '">' . $button_title . '</a>';
                } else {
                    $button = '<button type="button" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-single-product '.$button_class.' ' . $lightbox_class . ' button alt '.$btn_position_class.'" style="' . $custom_styles_override . '">' . $button_title . '</button>';
                }
            }            
            
            $btn_output .= $button;
            if($_product->get_price() === ''){
                $btn_output .= '<input type="hidden" name="add-to-cart" value="'. esc_attr( $post->ID ) .'" />';
            }
            $btn_output .= '<div class="angelleye-offers-clearfix"></div>';
            $btn_output .= '</div>';
        }
        
        return $btn_output;
    }
    
    /**
     * Add extra div wrap before add to cart button
     *
     * @since	0.1.0
     */
    public function angelleye_ofwc_before_add_to_cart_button() {
        global $post;
        $_product = wc_get_product($post->ID);
        if ( ! is_object( $_product ) ) {
            return;
        }
        $button_options_display = get_option('offers_for_woocommerce_options_display');
        $no_price_product_class = ( $_product->get_price() === '' ) ? 'ofwc_no_price_product' : '';
        $button_position = $button_options_display['display_setting_make_offer_button_position_single'];
        $is_on_right = ($button_position == 'right_of_add') ? 'ofwc-button-right-of-add-to-cart' : '';
        $button_options_display = get_option('offers_for_woocommerce_options_display');
        if($button_options_display['display_setting_make_offer_button_position_single'] == 'before_add'){
            echo $this->angelleye_ofwc_offer_button_output(false,'before_add_class');
        }
        echo '<div id="offers-for-woocommerce-add-to-cart-wrap" class="offers-for-woocommerce-add-to-cart-wrap '. $is_on_right . '" data-ofwc-position="' . $button_position . '"><div class="aofwc-first-div ' .$button_position .' '. $no_price_product_class . '">';
    }
    
    /**
     * Add Make Offer button after add to cart button
     *
     * @since	0.1.0
     */
    public function angelleye_ofwc_after_add_to_cart_button() {
        global $post;
        
        $button_options_display = get_option('offers_for_woocommerce_options_display');
        if($button_options_display['display_setting_make_offer_button_position_single'] == 'default' || $button_options_display['display_setting_make_offer_button_position_single'] == 'right_of_add'){
            //echo '<div class="angelleye-offers-clearfix"></div>';
            echo $this->angelleye_ofwc_offer_button_output(false,'default_add_class');
        }
        echo '</div>'; // #offers-for-woocommerce-add-to-cart-wrap
        echo '<div class="angelleye-offers-clearfix"></div>';
        echo '</div>'; // .aofwc-first-div
    }
    
    /**
     * Add extra div wrap after summary for products without price
     *
     * @since	0.1.0
     */
    public function angelleye_ofwc_woocommerce_after_single_product_summary() {
        $hidden_class = '';
        $button_options_display = get_option('offers_for_woocommerce_options_display');
        if($button_options_display['display_setting_make_offer_button_position_single'] != 'after_tabs'){
            $hidden_class = 'angelleye-ofwc-hidden';
        }
        echo '<div class="' . $hidden_class. '">';
        echo $this->angelleye_ofwc_offer_button_output();
        echo '</div>';
    }
    
    /**
     * Callback - Add Make Offer button after add to cart button on Catalog view
     *
     * @since	0.1.0
     */
    public function angelleye_ofwc_after_show_loop_item($post) {
        //$button_options_display = get_option('offers_for_woocommerce_options_display');
        $button_options_general = get_option('offers_for_woocommerce_options_general');
        $button_global_onoff_frontpage = ($button_options_general && isset($button_options_general['general_setting_enable_make_offer_btn_frontpage']) && $button_options_general['general_setting_enable_make_offer_btn_frontpage'] != '') ? true : false;
        $button_global_onoff_catalog = ($button_options_general && isset($button_options_general['general_setting_enable_make_offer_btn_catalog']) && $button_options_general['general_setting_enable_make_offer_btn_catalog'] != '') ? true : false;
        if (!((is_front_page() && !$button_global_onoff_frontpage) || (!is_front_page() && !$button_global_onoff_catalog))) {
            echo $this->angelleye_ofwc_offer_button_output(true);
        }
    }

    /**
     * Action - Add lightbox make offer form
     *
     * @since   0.1.0
     */
    public function angelleye_ofwc_lightbox_make_offer_form() {
        global $current_user,$post;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

        // enable offers for only logged in users
        if ($button_options_general && isset($button_options_general['general_setting_enable_offers_only_logged_in_users']) && $button_options_general['general_setting_enable_offers_only_logged_in_users'] != '') {
            if (!is_user_logged_in())
                return;
        }

        // enable offers for only certain user roles
        if (!empty($button_options_general['general_setting_allowed_roles'])) {
            if (is_user_logged_in()) {
                $user_data = get_userdata($current_user->ID);
                $user_roles = $user_data->roles;
                $role_match = array_intersect($user_roles, $button_options_general['general_setting_allowed_roles']);
                if (empty($role_match))
                    return;
            }
            else {
                return;
            }
        }

        $_product = wc_get_product($post->ID);
        if ( ! is_object( $_product ) ) {
            return;
        }
        $is_external_product = FALSE;
        if(version_compare(WC_VERSION, '3.0', '<')) {
            if(isset($_product->product_type) && $_product->product_type == 'external') {
                $is_external_product = TRUE;
            }
        } else {
            if($_product->get_type() == 'external') {
                $is_external_product = TRUE;
            }
        }
        $is_lightbox = ( isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') ? TRUE : FALSE;
        $on_exit_enabled = get_post_meta($post->ID, 'offers_for_woocommerce_onexit_only', true);
        $on_exit_enabled = (isset($on_exit_enabled) && $on_exit_enabled != '') ? $on_exit_enabled : 'no';
        
        if ($is_lightbox || $on_exit_enabled == "yes") {
            echo '<div id="lightbox_custom_ofwc_offer_form">';
            $this->angelleye_ofwc_display_custom_woocommerce_product_tab_content();
            echo '</div>';
            echo '<div id="lightbox_custom_ofwc_offer_form_close_btn"></div>';
        }
        if($on_exit_enabled == "yes" && !$is_external_product){
            ?>
            <script type="text/javascript">
                jQuery( document ).ready(function($) {
                    <?php 
                    $parent_offer_id = (isset($_GET['offer-pid']) && $_GET['offer-pid'] != '') ? wc_clean($_GET['offer-pid']) : '';
                    $parent_post_status = get_post_status($parent_offer_id);
            if ($parent_offer_id != '' && isset($parent_post_status) && $parent_post_status == 'countered-offer') {
                ?>
                        $("#lightbox_custom_ofwc_offer_form").addClass('active');
                        $("#lightbox_custom_ofwc_offer_form").show();
                        $("#lightbox_custom_ofwc_offer_form_close_btn").show();
                        $("#aeofwc-close-lightbox-link").css('display','block');
                    <?php } else { ?>
                    $(window).on('mouseout', function(e) {
                        var from = e.relatedTarget || e.toElement;
                        var onexit_cookie = 'onexit_cookie_<?php echo $post->ID; ?>';
                        var visited = Cookies.get(onexit_cookie);
                        if (visited === '1') {
                            return false;
                        } else if ((!from || from.nodeName == "HTML") && e.pageY < $(window).scrollTop()) {
                            // Launch the popup
                            $("#lightbox_custom_ofwc_offer_form").addClass('active');
                            $("#lightbox_custom_ofwc_offer_form").show();
                            $("#lightbox_custom_ofwc_offer_form_close_btn").show();
                            $("#aeofwc-close-lightbox-link").css('display','block');
                            var date = new Date();
                            date.setTime(date.getTime() + (60 * 1000)); //60 secounds time for cookie
                            Cookies.set(onexit_cookie, '1', {
                              expires: date,
                              path    : '<?php echo COOKIEPATH ? COOKIEPATH : "/"; ?>',
                            });
                        }
                    });
                    <?php } ?>
                });
            </script>
            <?php
        }
    }

    /**
     * Filter - Add new tab on woocommerce product single view
     *
     * @since	0.1.0
     */
    public function angelleye_ofwc_add_custom_woocommerce_product_tab($tabs) {
        global $post;
        global $current_user;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

        // enable offers for only logged in users
        if ($button_options_general && isset($button_options_general['general_setting_enable_offers_only_logged_in_users']) && $button_options_general['general_setting_enable_offers_only_logged_in_users'] != '') {
            if (!is_user_logged_in())
                return $tabs;
        }

        // enable offers for only certain user roles
        if (!empty($button_options_general['general_setting_allowed_roles'])) {
            if (is_user_logged_in()) {
                $user_data = get_userdata($current_user->ID);
                $user_roles = $user_data->roles;
                $role_match = array_intersect($user_roles, $button_options_general['general_setting_allowed_roles']);
                if (empty($role_match))
                    return $tabs;
            }
            else {
                return $tabs;
            }
        }                        
        /**
         * post is not avalable so create problem @line No.497,501 and also third party add-ons who uses tab filter
         * @ticket https://github.com/angelleye/offers-for-woocommerce/issues/246
         * @author Chirag Ips <chiragc@itpathsolutions.co.in>
         */ 
        if(is_null($post)){
            return $tabs;
        }
        
        $custom_tab_options_offers = array(
            'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),
            'on_exit' => get_post_meta($post->ID, 'offers_for_woocommerce_onexit_only', true),
        );
        /**
         * it was returning false that may breck filter function and Offers tabs are not visible on the front end
         * to avoid such a things we need to return $tabs
         * @ticket https://github.com/angelleye/offers-for-woocommerce/issues/244
         * @author Chirag Ips <chiragc@itpathsolutions.co.in>
         */
        $_product = wc_get_product($post->ID);
        if ( ! is_object( $_product ) ) {
            return;
        }
        if($_product == false) {
            return $tabs;
        }
        if(isset($button_options_general['general_setting_disabled_make_offer_on_product_sale']) && $button_options_general['general_setting_disabled_make_offer_on_product_sale'] == 1 && $_product->is_on_sale()){
            return $tabs;
        }
        $is_external_product = ( $_product->get_type() == 'external' ) ? TRUE : FALSE;
        $is_instock = ( $_product->is_in_stock() ) ? TRUE : FALSE;

        // if post has offers button enabled
        if ($custom_tab_options_offers['enabled'] == 'yes' && !$is_external_product && $is_instock && $custom_tab_options_offers['on_exit'] != 'yes') {
            if (isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') {
                return $tabs;
            }

            $tab_title = (isset($button_options_display['display_setting_custom_make_offer_btn_text']) && $button_options_display['display_setting_custom_make_offer_btn_text'] != '') ? __($button_options_display['display_setting_custom_make_offer_btn_text'],'offers-for-woocommerce') : __('Make Offer', 'offers-for-woocommerce');
            $tab_title = apply_filters('woocommerce_make_offer_form_tab_name', $tab_title);
            
            // Add new tab "Make Offer"
            $tabs['tab_custom_ofwc_offer'] = array(
                'title' => $tab_title,
                'priority' => 20,
                'callback' => array($this, 'angelleye_ofwc_display_custom_woocommerce_product_tab_content'));

        }
        return $tabs;
    }

    /**
     * Callback - Display "Make Offer" front-end form parts 
     *
     * @since	0.1.0
     */
    public function angelleye_ofwc_display_custom_woocommerce_product_tab_content() {
        global $post;
        global $current_user;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');
        $currency_symbol = get_woocommerce_currency_symbol();
        // enable offers for only logged in users
        if ($button_options_general && isset($button_options_general['general_setting_enable_offers_only_logged_in_users']) && $button_options_general['general_setting_enable_offers_only_logged_in_users'] != '') {
            if (!is_user_logged_in())
                return;
        }

        // enable offers for only certain user roles
        if (!empty($button_options_general['general_setting_allowed_roles'])) {
            if (is_user_logged_in()) {
                $user_data = get_userdata($current_user->ID);
                $user_roles = $user_data->roles;
                $role_match = array_intersect($user_roles, $button_options_general['general_setting_allowed_roles']);
                if (empty($role_match))
                    return;
            }
            else {
                return;
            }
        }

        $_product = wc_get_product($post->ID);
        if ( ! is_object( $_product ) ) {
            return;
        }
        $is_sold_individually = $_product->is_sold_individually();
        $is_backorders_allowed = $_product->backorders_allowed();
        $stock_quantity = $_product->get_stock_quantity();

        $global_limit_quantity_to_stock = ($button_options_general && isset($button_options_general['general_setting_limit_offer_quantity_by_stock']) && $button_options_general['general_setting_limit_offer_quantity_by_stock'] != '') ? true : false;

        $new_offer_quantity_limit = (!$is_backorders_allowed && $stock_quantity && $stock_quantity > 0 && $global_limit_quantity_to_stock) ? $stock_quantity : '';

        // set parent offer id if found in get var
        $parent_offer_id = (isset($_GET['offer-pid']) && $_GET['offer-pid'] != '') ? wc_clean($_GET['offer-pid']) : '';
        $parent_offer_uid = (isset($_GET['offer-uid']) && $_GET['offer-uid'] != '') ? wc_clean($_GET['offer-uid']) : '';
        $offer_name = (isset($_GET['offer-name']) && $_GET['offer-name'] != '') ? wc_clean($_GET['offer-name']) : '';
        $offer_email = (isset($_GET['offer-email']) && $_GET['offer-email'] != '') ? wc_clean($_GET['offer-email']) : '';

        // if having parent offer id, check for valid parent
        $parent_offer_error = false;
        if ($parent_offer_id != '') {
            $parent_post_status = get_post_status($parent_offer_id);
            $post_parent_type = get_post_type($parent_offer_id);
            $parent_post_offer_uid = get_post_meta($parent_offer_id, 'offer_uid', true);

            $final_offer = get_post_meta($parent_offer_id, 'offer_final_offer', true);
            $expiration_date = get_post_meta($parent_offer_id, 'offer_expiration_date', true);
            $expiration_date_formatted = ($expiration_date) ? date("Y-m-d 23:59:59", strtotime($expiration_date)) : FALSE;

            // check for valid parent offer ( must be a offer post type and accepted/countered and uid must match
            if ((isset($parent_post_status) && $parent_post_status != 'countered-offer') || ($post_parent_type != 'woocommerce_offer') || (!$parent_post_offer_uid) || ($parent_offer_uid == '') || ($parent_post_offer_uid != $parent_offer_uid)) {
                // If buyer already submitted 'buyer counter'
                if ($parent_post_status == 'buyercountered-offer') {
                    $parent_offer_id = '';
                    $parent_offer_error = true;
                    $parent_offer_error_message = __('You can not submit another counter offer at this time; Counter offer is currently being reviewed. You can submit a new offer using the form below.', 'offers-for-woocommerce');
                } else {
                    $offer = get_post($parent_offer_id);
                    if ( apply_filters( 'ofw_not_allow_invalid_offer_status', false,  $offer) ) {
                        $parent_offer_id = '';
                        $parent_offer_error = true;
                        $parent_offer_error_message = __('Invalid Parent Offer Id; See shop manager for assistance.', 'offers-for-woocommerce');
                    } else {
                        $offer_name = get_post_meta($parent_offer_id, 'offer_name', true);
                        $offer_company_name = get_post_meta($parent_offer_id, 'offer_company_name', true);
                        $offer_phone = get_post_meta($parent_offer_id, 'offer_phone', true);
                        $offer_email = get_post_meta($parent_offer_id, 'offer_email', true);
                    }
                }
            }
            // If offer counter was set to 'final offer'
            elseif ($final_offer == '1') {
                $parent_offer_id = '';
                $parent_offer_error = true;
                $parent_offer_error_message = __('You can not submit a counter offer at this time; Counter offer is a final offer. You can submit a new offer using the form below.', 'offers-for-woocommerce');
            }

            // If offer counter 'offer_expiration_date' is past
            elseif (($expiration_date_formatted) && ($expiration_date_formatted <= (date("Y-m-d H:i:s", current_time('timestamp', 0))) )) {
                $parent_offer_id = '';
                $parent_offer_error = true;
                $parent_offer_error_message = __('Counter offer has expired; You can not submit a counter offer at this time. You can submit a new offer using the form below.', 'offers-for-woocommerce');
            } else {
                // lookup original offer data to display buyer info
                $offer_name = get_post_meta($parent_offer_id, 'offer_name', true);
                $offer_company_name = get_post_meta($parent_offer_id, 'offer_company_name', true);
                $offer_phone = get_post_meta($parent_offer_id, 'offer_phone', true);
                $offer_email = get_post_meta($parent_offer_id, 'offer_email', true);
            }
        }

        // If name,phone,email not already specified, then try to pull logged in user data if user is logged in
        if (empty($offer_name)) {
            if (is_user_logged_in()) {
                $current_user = wp_get_current_user();

                $offer_email = $current_user->user_email;
                $offer_company_name = (!empty($current_user->billing_company)) ? $current_user->billing_company : '';
                $offer_phone = (!empty($current_user->billing_phone)) ? $current_user->billing_phone : '';

                // use WP user first/last name if available
                if (!empty($current_user->user_firstname)) {
                    $offer_name = $current_user->user_firstname;
                    $offer_name.= (!empty($current_user->user_lastname)) ? ' ' . $current_user->user_lastname : '';
                } else { // use WP/WC billing first/last name
                    if (!empty($current_user->billing_first_name)) {
                        $offer_name = $current_user->billing_first_name;
                        $offer_name.= (!empty($current_user->billing_last_name)) ? ' ' . $current_user->billing_last_name : '';
                    }
                }
            }
        }

        // get options for button display
        $button_display_options = get_option('offers_for_woocommerce_options_display');
        $button_display_position = get_option('angelleye_displaySettingFormFieldPosition');
        
        $currency_symbol = get_woocommerce_currency_symbol();
        $is_anonymous_communication_enable = $this->ofw_is_anonymous_communication_enable();
        // Set html content for output
        $is_recaptcha_enable = $this->is_recaptcha_enable();
        include(OFW_PLUGIN_URL . 'public/views/public.php');
    }

    /**
     * Return an instance of this class
     *
     * @since    0.1.0
     *
     * @return    object    A single instance of this class
     */
    public static function get_instance() {
        // If the single instance hasn't been set, set it now
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Fired when the plugin is activated
     *
     * @since    0.1.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog
     */
    public static function activate($network_wide) {
        if (function_exists('is_multisite') && is_multisite()) {
            if ($network_wide) {
                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {
                    switch_to_blog($blog_id);
                    self::single_activate();
                }

                restore_current_blog();
            } else {
                self::single_activate();
            }
        } else {
            self::single_activate();
        }
        update_option( 'woocommerce_queue_flush_rewrite_rules', 'yes' );

        /**
         * Log activation in Angell EYE database via web service.
         * @todo need to add option for people to enable this.
         */
        //$log_url = $_SERVER['HTTP_HOST'];
        //$log_plugin_id = 3;
        //$log_activation_status = 1;
        //wp_remote_request('http://www.angelleye.com/web-services/wordpress/update-plugin-status.php?url=' . $log_url . '&plugin_id=' . $log_plugin_id . '&activation_status=' . $log_activation_status);
    }

    /**
     * Fired when the plugin is deactivated
     *
     * @since    0.1.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog
     */
    public static function deactivate($network_wide) {
        if (function_exists('is_multisite') && is_multisite()) {
            if ($network_wide) {
                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {
                    switch_to_blog($blog_id);
                    self::single_deactivate();
                }

                restore_current_blog();
            } else {
                self::single_deactivate();
            }
        } else {
            self::single_deactivate();
        }
        update_option( 'woocommerce_queue_flush_rewrite_rules', 'yes' );

        /**
         * Log deactivation in Angell EYE database via web service.
         * @todo need to add option for people to enable this.
         */
        //$log_url = $_SERVER['HTTP_HOST'];
        //$log_plugin_id = 3;
        //$log_activation_status = 0;
        //wp_remote_request('http://www.angelleye.com/web-services/wordpress/update-plugin-status.php?url='.$log_url.'&plugin_id='.$log_plugin_id.'&activation_status='.$log_activation_status);
    }

    /**
     * Fired when a new site is activated with a WPMU environment
     *
     * @since    0.1.0
     *
     * @param    int    $blog_id    ID of the new blog
     */
    public function activate_new_site($blog_id) {
        if (1 !== did_action('wpmu_new_blog')) {
            return;
        }

        switch_to_blog($blog_id);
        self::single_activate();
        restore_current_blog();
    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    0.1.0
     *
     * @return   array|false    The blog ids, false if no matches
     */
    private static function get_blog_ids() {
        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";
        return $wpdb->get_col($sql);
    }

    /**
     * Fired for each blog when the plugin is activated
     *
     * @since    0.1.0
     */
    private static function single_activate() {
        // @TODO: Define activation functionality here
    }

    /**
     * Fired for each blog when the plugin is deactivated
     *
     * @since    0.1.0
     */
    private static function single_deactivate() {
        // @TODO: Define deactivation functionality here
    }

    /**
     * Register and enqueue public-facing style sheet
     *
     * @since    0.1.0
     */
    public function enqueue_styles() {
        wp_enqueue_style('offers-for-woocommerce-plugin-styles', plugins_url('assets/css/public.css', __FILE__), array(), self::VERSION);
    }

    /**
     * Register and enqueues public-facing JavaScript files
     *
     * @since    0.1.0
     */
    public function enqueue_scripts() {
        global $post;
        if(is_object($post)) {
            $is_product_type_variable = 'false';
            if (function_exists('wc_get_product')) {
                $product = wc_get_product($post);
                if($product) {
                    if ($product->is_type('variable') && is_single()) {
                        $is_product_type_variable = 'true';
                    }
                }
            }
            /* this code checks for if Woocommerce variable table plugin activated then it will add one extra field to jquery */
            $active_plugins = (array) get_option('active_plugins', array());
            if (is_multisite())
                $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
            if (in_array('woo-variations-table/woo-variations-table.php', $active_plugins) || array_key_exists('woo-variations-table/woo-variations-table.php', $active_plugins)) {
                $is_woo_variations_table_installed='1';
            } else {
                $is_woo_variations_table_installed='0';
            }
            /* End */
            $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_script( 'js-cookie', WC()->plugin_url() . '/assets/js/js-cookie/js.cookie' . $suffix . '.js', array(), '2.1.4', true );
            wp_enqueue_script('offers-for-woocommerce-plugin-script', plugins_url('assets/js/public.js', __FILE__), array('jquery'), self::VERSION);
            wp_enqueue_script('offers-for-woocommerce-plugin-script-jquery-auto-numeric-1-9-24', plugins_url('assets/js/autoNumeric-1-9-24.js', __FILE__), self::VERSION);
            if (wp_script_is('offers-for-woocommerce-plugin-script')) {
                wp_localize_script('offers-for-woocommerce-plugin-script', 'offers_for_woocommerce_js_params', apply_filters('offers_for_woocommerce_js_params', array(
                    'is_product_type_variable' => $is_product_type_variable,
                    'is_woo_variations_table_installed' => $is_woo_variations_table_installed,
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'offers_for_woocommerce_params_nonce' => wp_create_nonce("offers_for_woocommerce_params_nonce"),
                    'i18n_make_a_selection_text' => esc_attr__( 'Please select some product options before making offer for this product.', 'offers-for-woocommerce' ),
                    'i18n_unavailable_text' => esc_attr__( 'Sorry, this product is unavailable. Please choose a different product.', 'offers-for-woocommerce' ),
                    'ofw_public_js_currency_position' => get_option('woocommerce_currency_pos'),
                    'ofw_public_js_thousand_separator' => wc_get_price_thousand_separator(),
                    'ofw_public_js_decimal_separator' => wc_get_price_decimal_separator(),
                    'ofw_public_js_number_of_decimals' => wc_get_price_decimals(),
                )));
            }
            if($this->is_recaptcha_enable()) {
                wp_enqueue_script('offers-for-woocommerce-recaptcha', 'https://www.google.com/recaptcha/api.js', array('jquery'), self::VERSION);
            }
        }
    }

    public function ofwc_minimum_offer($product_id,$product_price,$offer_total,$offer_quantity){
        if(empty($product_price) || empty($product_id)){
            return 3;
        }
        else{
            $ofwc_minimum_offer_price = get_post_meta($product_id, 'ofwc_minimum_offer_price', true);
            $ofwc_minimum_offer_price_type = get_post_meta($product_id, 'ofwc_minimum_offer_price_type', true);
            if($ofwc_minimum_offer_price_type == 'price'){
                if($offer_total < $ofwc_minimum_offer_price){
                    return array('status' =>'failed', 'minimum_offer_price' => $ofwc_minimum_offer_price ,'type' => 'price');
                }
                else{
                    return true;
                }
            }
            elseif($ofwc_minimum_offer_price_type == 'percentage'){
                $product_price = substr($product_price, 1);
                $offer_multi_price =($product_price * $offer_quantity);
                $total = (($ofwc_minimum_offer_price / 100) * $offer_multi_price);
                $mop_after_percentage = ($offer_multi_price - $total);
                if($offer_total < $mop_after_percentage){
                    return array('status' =>'failed', 
                                 'minimum_offer_price' => $mop_after_percentage,
                                 'type' => 'percentage',
                                 'qty' => $offer_quantity,
                                 'percent' => $ofwc_minimum_offer_price
                        );
                }
                else{
                    return true;
                }
            }
            else{
                return 2;
            }
        }
    }

    public function new_offer_form_submit() {
        ob_start();
        $post_data = $formData = $newPostData = array();            
        $arr_main_array = wc_clean($_POST['value']);        
        $nmArray = array();
        $arr_main_array = apply_filters('angelleye_ofw_pre_offer_request', $arr_main_array);
        $active_plugins = (array) get_option( 'active_plugins', array() );        
        if (is_multisite())
            $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        if(in_array( 'woocommerce-product-addons/woocommerce-product-addons.php', $active_plugins ) || array_key_exists( 'woocommerce-product-addons/woocommerce-product-addons.php', $active_plugins )){
            foreach($arr_main_array as $key => $value){            
                if(array_key_exists('product_addon_array',$arr_main_array[$key])){
                    $p = $position = 0;
                    foreach($value['product_addon_array'] as $pro){
                        if($position != $pro['position']){
                            $p = 0;
                        }
                        $position = $pro['position'];
                        $nmArray[$pro['position']]['group'] = $pro['group'];
                        $nmArray[$pro['position']]['position'] = $pro['position'];
                        $nmArray[$pro['position']]['type'] = $pro['type'];
                        $nmArray[$pro['position']]['options'][$p]['label'] = $pro['label'];
                        $nmArray[$pro['position']]['options'][$p]['price'] = $pro['price'];
                        $nmArray[$pro['position']]['options'][$p]['value'] = $pro['value'];
                        $p++;
                    }
                }
            }
        }  
        if(isset($nmArray)){
            $formData['offers_product_addon']=  $nmArray;
        }

        if (is_ajax()) {
            if( isset($_POST['value']) && !empty($_POST['value']) ) {
                $post_data = wc_clean($_POST['value']);
                if( !empty($post_data) ) {
                    foreach ($post_data as $key => $post_data_value) {
                       if( !empty($post_data_value['name']) ) {
                            $post[$post_data_value['name']] = !empty($post_data_value['value']) ? wc_clean($post_data_value['value']) : '';
                       }
                    }
                }
            }
        } else {
            if( isset($_POST['value']) && !empty($_POST['value']) ) {
                $post_data = wc_clean($_POST['value']);
                if( !empty($post_data)) {
                    foreach ($post_data as $key => $post_data_value) {
                       if( !empty($post_data_value) ) {
                            $post[$key] = !empty($post_data_value) ? wc_clean($post_data_value) : '';
                       }
                    }
                }
            }
        }


        global $wpdb,$woocommerce; // this is how you get access to the database
                    // Check if form was posted and select task accordingly
        if (isset($post["offer_product_id"]) && $post["offer_product_id"] != '') {            
            // set postmeta original vars
            $formData['orig_offer_name'] = !empty($post['offer_name']) ? wc_clean($post['offer_name']) : '';
            $formData['orig_offer_company_name'] = !empty($post['offer_company_name']) ? wc_clean($post['offer_company_name']) : '';
            $post_offer_phone = !empty($post['offer_phone']) ? wc_format_phone_number($post['offer_phone']) : '';
            $formData['orig_offer_phone'] = (WC_Validation::is_phone($post_offer_phone)) ? $post_offer_phone : '';
            $formData['orig_offer_email'] = !empty($post['offer_email']) ? wc_clean($post['offer_email']) : '';
            $formData['orig_offer_product_id'] = !empty($post['offer_product_id']) ? wc_clean($post['offer_product_id']) : '';
            $formData['orig_offer_variation_id'] = !empty($post['offer_variation_id']) ? wc_clean($post['offer_variation_id']) : '';
            $formData['orig_offer_quantity'] = !empty($post['offer_quantity']) ? wc_clean($post['offer_quantity']) : '0';
            $formData['orig_offer_price_per'] = !empty($post['offer_price_each']) ? wc_clean($post['offer_price_each']) : '0';
            $formData['orig_offer_amount'] = ($formData['orig_offer_quantity'] * $formData['orig_offer_price_per']);
            $formData['orig_offer_uid'] = uniqid('aewco-');
            $formData['parent_offer_uid'] = !empty($post['parent_offer_uid']) ? wc_clean($post['parent_offer_uid']) : '';
            $formData['offer_product_price'] = !empty($post['offer_product_price']) ? wc_clean($post['offer_product_price']) : '';
            $formData['offer_total'] = !empty($post['offer_total']) ? Angelleye_Offers_For_Woocommerce_Admin::ofwc_format_localized_price(wc_clean($post['offer_total']))  : '';

            if($this->is_recaptcha_enable()) {
                if( isset( $post['g-recaptcha-response'] ) && !empty($post['g-recaptcha-response']) ){
                    $response = $this->recaptcha_verify_response($post['g-recaptcha-response']);
                    if(empty($response)) {
                       echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __('Please check the captcha.', 'offers-for-woocommerce')));
                        exit; 
                    } else {
                        $response_array = json_decode($response, true);
                        if( $response_array['success'] != true ) {
                            echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __('Please check the captcha.', 'offers-for-woocommerce')));
                            exit;
                        }
                    }
                } else {
                    echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __('Please check the captcha.', 'offers-for-woocommerce')));
                    exit;
                }
            }
            /**
             * Check minimum quantity and minimum price
             */
            // check for valid offer quantity (not zero)
            if (($formData['orig_offer_quantity'] == '' || $formData['orig_offer_quantity'] == 0)) {
                if (is_ajax()) {
                    echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __('Please enter a positive value for \'Offer Quantity\'', 'offers-for-woocommerce')));
                    exit;
                } else {
                    return false;
                }
            }
            // check for valid offer price (not zero)
            if (($formData['orig_offer_price_per'] == '' || $formData['orig_offer_price_per'] == 0 || $formData['orig_offer_price_per'] == "0.00")) {
                if (is_ajax()) {
                    echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __('Please enter a positive value for \'Offer Amount\'', 'offers-for-woocommerce')));
                    exit;
                } else {
                    $this->set_session('ofwpa_issue', 'Please enter a positive value for Offer Amount');
                    return false;
                }
            }
            
            /*Check for minimum offer set or not */
            $ofwc_minimum_offer_price_enabled = get_post_meta($formData['orig_offer_product_id'], 'ofwc_minimum_offer_price_enabled', true);
            if(!empty($ofwc_minimum_offer_price_enabled) && $ofwc_minimum_offer_price_enabled ==='yes'){                  
                $return = $this->ofwc_minimum_offer($formData['orig_offer_product_id'],
                                                    $formData['offer_product_price'],
                                                    $formData['offer_total'],
                                                    $formData['orig_offer_quantity']);
                $symbol = get_woocommerce_currency_symbol();
                if(is_array($return) && $return['status'] =='failed' && $return['type']=='price'){
                    echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __('Minimum Offer price is '.$symbol.$return['minimum_offer_price'], 'offers-for-woocommerce')));
                    exit;
                }
                elseif(is_array($return) && $return['status'] =='failed' && $return['type']=='percentage'){                  
                    echo json_encode(array("statusmsg" => 'failed-custom',
                                     "statusmsgDetail" => __('Minimum Offer price must be '.$return['percent'].'%. For '.$return['qty'].' quantity Minimum offer price is '.wc_price($return['minimum_offer_price']), 'offers-for-woocommerce')));
                    exit;                 
                }
            }
            else{
                //echo "in else odder condition";
            }
            //exit;
            
            // set postmeta vars
            $formData['offer_name'] = $formData['orig_offer_name'];
            $formData['offer_company_name'] = $formData['orig_offer_company_name'];
            $formData['offer_phone'] = $formData['orig_offer_phone'];
            $formData['offer_email'] = $formData['orig_offer_email'];
            $formData['offer_product_id'] = $formData['orig_offer_product_id'];
            $formData['offer_variation_id'] = $formData['orig_offer_variation_id'];
            $formData['offer_quantity'] = $formData['orig_offer_quantity'];
            $formData['offer_price_per'] = $formData['orig_offer_price_per'];
            $formData['offer_amount'] = $formData['orig_offer_amount'];
            $formData['offer_uid'] = $formData['orig_offer_uid'];

            // if not logged in, check for matching wp user by email
            // set author_data
            $author_data = (!is_user_logged_in() ) ? get_user_by('email', $formData['offer_email']) : false;

            if ($author_data) {
                $newPostData['post_author'] = $author_data->ID;
            }

            // set post vars
            $newPostData['post_date'] = date("Y-m-d H:i:s", current_time('timestamp', 0));
            $newPostData['post_date_gmt'] = gmdate("Y-m-d H:i:s", time());
            $newPostData['post_type'] = 'woocommerce_offer';
            $newPostData['post_status'] = 'publish';
            $newPostData['post_title'] = $formData['offer_email'];

            // set offer comments
            $comments = (isset($post['offer_notes']) && $post['offer_notes'] != '') ? strip_tags(nl2br($post['offer_notes']), '<br><p>') : '';

            /**
             * Akismet spam check
             * Passes back true (it's spam) or false (it's ham)
             * @since   1.2.0
             */
            $akismet_api_key = '9a57112207be';
            $data = array('blog' => get_site_url(),
                'user_ip' => wc_clean(wp_unslash($_SERVER['REMOTE_ADDR'])),
                'user_agent' => wc_clean($_SERVER['HTTP_USER_AGENT']),
                'referrer' => '',
                'permalink' => get_permalink($formData['offer_product_id']),
                'comment_type' => 'woocommerce_offer',
                'comment_author' => $formData['offer_name'],
                'comment_author_email' => $formData['offer_email'],
                'comment_author_url' => '',
                'comment_content' => $comments
            );
            if ($this->aeofwc_akismet_comment_check($akismet_api_key, $data) && FALSE) {
                // is spam
                if (is_ajax()) {
                    echo json_encode(array("statusmsg" => 'failed-spam', "statusmsgDetail" => __('Invalid Offer Submission; See shop manager for assistance', 'offers-for-woocommerce')));
                    exit;
                } else {
                    $this->set_session('ofwpa_issue', 'Invalid Offer Submission; See shop manager for assistance');
                    return false;
                }
            }

            // check for parent post id
            $parent_post_id = (isset($post['parent_offer_id'])) ? $post['parent_offer_id'] : '';
            $parent_post_status = get_post_status($parent_post_id);
            $post_parent_type = get_post_type($parent_post_id);

            // If has valid parent offer id post
            $is_counter_offer = ( $parent_post_id != '' ) ? true : false;
            do_action('woocommerce_before_offer_submit', $is_counter_offer, $post,$formData,$newPostData);
            
            if ($is_counter_offer) {
                // check for parent offer unique id
                $parent_post_offer_uid = get_post_meta($parent_post_id, 'offer_uid', true);

                // check for valid parent offer ( must be a offer post type and accepted/countered and uid must match
                if ((isset($parent_post_status) && $parent_post_status != 'countered-offer') || ($post_parent_type != 'woocommerce_offer') || ($parent_post_offer_uid != $formData['parent_offer_uid'])) {
                    $offer = get_post($parent_post_id);
                    if ( apply_filters( 'ofw_not_allow_invalid_offer_status', false,  $offer) ) {
                        if (is_ajax()) {
                            echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __('Invalid Parent Offer Id; See shop manager for assistance', 'offers-for-woocommerce')));
                            exit;
                        } else {
                            $this->set_session('ofwpa_issue', 'Invalid Parent Offer Id; See shop manager for assistance');
                            return false;
                        }
                    }
                }

                $parent_post = array(
                    'ID' => $parent_post_id,
                    'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0)),
                    'post_modified_gmt' => gmdate("Y-m-d H:i:s", current_time('timestamp', 0)),
                    'post_status' => 'buyercountered-offer'
                );

                if ($author_data) {
                    $parent_post['post_author'] = $newPostData['post_author'];
                }

                // Update the parent post into the database
                wp_update_post($parent_post);

                $formDataUpdated = array();

                $formDataUpdated['offer_buyer_counter_quantity'] = $formData['offer_quantity'];
                $formDataUpdated['offer_buyer_counter_price_per'] = $formData['offer_price_per'];
                $formDataUpdated['offer_buyer_counter_amount'] = $formData['offer_amount'];

                $formDataUpdated['offer_quantity'] = $formData['offer_quantity'];
                $formDataUpdated['offer_price_per'] = $formData['offer_price_per'];
                $formDataUpdated['offer_amount'] = $formData['offer_amount'];
                 if(isset($nmArray)){
                    $formDataUpdated['offers_product_addon']=  serialize($nmArray);
                 }

                // Insert new Post Meta Values
                foreach ($formDataUpdated as $k => $v) {
                    $newPostMetaData = array();
                    $newPostMetaData['post_id'] = $parent_post_id;
                    $newPostMetaData['meta_key'] = $k;
                    $newPostMetaData['meta_value'] = $v;

                    update_post_meta($parent_post_id, $newPostMetaData['meta_key'], $newPostMetaData['meta_value']);
                }

                // Insert WP comment
                $comment_text = "<span>" . __('Buyer Submitted Counter Offer', 'offers-for-woocommerce') . "</span>";

                if ($comments != '') {
                    // Insert WP comment
                    $comment_text.= '<br />' . $comments;
                }

                $data = array(
                    'comment_post_ID' => '',
                    'comment_author' => 'admin',
                    'comment_author_email' => '',
                    'comment_author_url' => '',
                    'comment_content' => $comment_text,
                    'comment_type' => 'offers-history',
                    'comment_parent' => 0,
                    'user_id' => '',
                    'comment_author_IP' => wc_clean($_SERVER['REMOTE_ADDR']),
                    'comment_agent' => '',
                    'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0)),
                    'comment_approved' => 'post-trashed',
                );
                $new_comment_id = wp_insert_comment($data);

                // insert comment meta
                if ($new_comment_id) {
                    add_comment_meta($new_comment_id, 'angelleye_woocommerce_offer_id', $parent_post_id, true);
                    add_comment_meta($new_comment_id, 'offer_quantity', $formData['offer_quantity'], true);
                    add_comment_meta($new_comment_id, 'offer_amount', $formData['offer_amount'], true);
                    add_comment_meta($new_comment_id, 'offer_price_per', $formData['offer_price_per'], true);
                    add_comment_meta($new_comment_id, 'offer_status', '2', true);
                }
            } else {
                // Insert new Post
                $parent_post_id = wp_insert_post($newPostData);
                if ($parent_post_id) {
                    // Insert new Post Meta Values
                    foreach ($formData as $k => $v) {
                        $newPostMetaData = array();
                        $newPostMetaData['post_id'] = $parent_post_id;
                        $newPostMetaData['meta_key'] = $k;
                        $newPostMetaData['meta_value'] = $v;
                        add_post_meta($newPostMetaData['post_id'], $newPostMetaData['meta_key'], $newPostMetaData['meta_value']);
                    }

                    // Insert WP comment
                    $comment_text = "<span>" . __('Created New Offer', 'offers-for-woocommerce') . "</span>";

                    if ($comments != '') {
                        // Insert WP comment
                        $comment_text.= '<br />' . $comments;
                    }

                    $data = array(
                        'comment_post_ID' => '',
                        'comment_author' => 'admin',
                        'comment_author_email' => '',
                        'comment_author_url' => '',
                        'comment_content' => $comment_text,
                        'comment_type' => 'offers-history',
                        'comment_parent' => 0,
                        'user_id' => 1,
                        'comment_author_IP' => '127.0.0.1',
                        'comment_agent' => '',
                        'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0)),
                        'comment_approved' => 'post-trashed',
                    );
                    $new_comment_id = wp_insert_comment($data);

                    // insert comment meta
                    if ($new_comment_id) {
                        add_comment_meta($new_comment_id, 'angelleye_woocommerce_offer_id', $parent_post_id, true);
                        add_comment_meta($new_comment_id, 'offer_quantity', $formData['offer_quantity'], true);
                        add_comment_meta($new_comment_id, 'offer_amount', $formData['offer_amount'], true);
                        add_comment_meta($new_comment_id, 'offer_price_per', $formData['offer_price_per'], true);
                        add_comment_meta($new_comment_id, 'offer_status', '1', true);
                    }
                } else {
                    // return error msg
                    if (is_ajax()) {
                        echo json_encode(array("statusmsg" => 'failed', "statusmsgDetail" => 'database error'));
                        exit;
                    } else {
                        $this->set_session('ofwpa_issue', 'database error');
                        return false;
                    }
                }
            }

            do_action('make_offer_after_save_form_data', $parent_post_id, $post);
            
                      
            /**
             * Email Out - admin email notification of new or countered offer
             * @since   0.1.0
             */
            $offer_id = $parent_post_id;

            $offer_name = get_post_meta($parent_post_id, 'offer_name', true);
            $offer_phone = get_post_meta($parent_post_id, 'offer_phone', true);
            $offer_company_name = get_post_meta($parent_post_id, 'offer_company_name', true);
            $offer_email = get_post_meta($parent_post_id, 'offer_email', true);

            $product_id = get_post_meta($parent_post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($parent_post_id, 'offer_variation_id', true);

            $product = ( $variant_id ) ? wc_get_product($variant_id) : wc_get_product($product_id);

            $product_qty = $formData['offer_quantity'];
            $product_price_per = $formData['offer_price_per'];
            $product_total = $formData['offer_amount'];

            $product_shipping_cost = get_post_meta($parent_post_id, 'product_shipping_cost', true);

            $offer_args = array(
                'offer_email' => $offer_email,
                'offer_name' => $offer_name,
                'offer_phone' => $offer_phone,
                'offer_company_name' => $offer_company_name,
                'offer_id' => $offer_id,
                'product_id' => $product_id,
                'product_url' => get_permalink($product_id),
                'variant_id' => $variant_id,
                'product' => $product,
                'product_qty' => $product_qty,
                'product_price_per' => $product_price_per,
                'product_shipping_cost' => $product_shipping_cost,
                'product_total' => $product_total,
                'offer_notes' => $comments
            );
            
            $offer_args['offer_email'] = apply_filters('angelleye_ofw_pre_email_sent', $offer_email, $offer_args);

            if ($variant_id) {
                if ($product->get_sku()) {
                    $identifier = $product->get_sku();
                } else {
                    $identifier = '#' . $product->get_id();
                }

                $attributes = $product->get_variation_attributes();
                $extra_data = ' &ndash; ' . implode(', ', $attributes);
                $offer_args['product_title_formatted'] = sprintf(__('%s &ndash; %s%s', 'offers-for-woocommerce'), $identifier, $product->get_title(), $extra_data);
            } else {
                if (!empty($product) && $product->get_sku()) {
                    $identifier = $product->get_sku();
                } else {
                    $identifier = '#' . $product_id;
                }

                $offer_args['product_title_formatted'] = sprintf(__('%s &ndash; %s', 'offers-for-woocommerce'), $identifier, $product->get_title());
            }
            
            
            /*
             * Below code is work for Disabled email notification for admin user when offers is auto decline for product
             *  Setting ->  General -> Disable Admin Email on Auto Decline Offer
             *  Start
             */
            $button_options_general = get_option('offers_for_woocommerce_options_general');
            $option_for_admin_disable_email_auto_decline  = isset($button_options_general['general_setting_admin_disable_email_auto_decline']) ? $button_options_general['general_setting_admin_disable_email_auto_decline'] : '';
            $offer_is_auto_decline ='';
            if($option_for_admin_disable_email_auto_decline == '1'){
                $productData = $this->ofwc_get_product_detail($offer_id, $product_id, $variant_id);
                $offer_price = $productData['offer_price'];
                $user_offer_percentage = $productData['user_offer_percentage'];
                $product_url = $productData['product_url'];
                $offer_uid = $productData['offer_uid'];
                if( isset($post_meta_auto_decline_enabled) && $post_meta_auto_decline_enabled == 'yes') {
                    $auto_decline_percentage = get_post_meta($product_id, '_offers_for_woocommerce_auto_decline_percentage', true);
                    if( isset($offer_price) && !empty($offer_price) && isset($auto_decline_percentage) && !empty($auto_decline_percentage) ) {
                        if( (int) $auto_decline_percentage >= (int) $user_offer_percentage) {
                          $offer_is_auto_decline = 'yes';
                        }
                    }
                }
            }            
            /* End */
            
            if ($is_counter_offer) {
                $offer_args['is_counter_offer'] = true;

                /**
                 * send admin 'New counter offer' email template
                 */
                // the email we want to send
                $email_class = 'WC_New_Counter_Offer_Email';
            } else {
                $offer_args['is_counter_offer'] = false;

                /**
                 * send admin 'New offer' email template
                 */
                // the email we want to send
                $email_class = 'WC_New_Offer_Email';
            }

            // load the WooCommerce Emails
            if( isset($_POST['value']['emails_object']) && !empty($_POST['value']['emails_object']) ) {
                $emails = wc_clean($_POST['value']['emails_object']);
            } else {
                $emails = $woocommerce->mailer()->get_emails();
            }

            
            
            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];

            // set plugin slug in email class
            //$new_email->plugin_slug = 'offers-for-woocommerce';

            if ($is_counter_offer) {
                // define email template/path (html)
                $new_email->template_html = 'woocommerce-new-counter-offer.php';
                $new_email->template_html_path = plugin_dir_path(__FILE__) . 'includes/emails/';

                // define email template/path (plain)
                $new_email->template_plain = 'woocommerce-new-counter-offer.php';
                $new_email->template_plain_path = plugin_dir_path(__FILE__) . 'includes/emails/plain/';
            } else {                
                // define email template/path (html)
                $new_email->template_html = 'woocommerce-new-offer.php';
                $new_email->template_html_path = plugin_dir_path(__FILE__) . 'includes/emails/';

                // define email template/path (plain)
                $new_email->template_plain = 'woocommerce-new-offer.php';
                $new_email->template_plain_path = plugin_dir_path(__FILE__) . 'includes/emails/plain/';
            }
            $offer_args['is_anonymous_communication_enable'] = $this->ofw_is_anonymous_communication_enable();
            if($offer_is_auto_decline == '' && $option_for_admin_disable_email_auto_decline == ''){
                $new_email->trigger($offer_args);
            }
            /**
             * Send buyer 'offer received' email notification
             */
            // the email we want to send
            $email_class = 'WC_Offer_Received_Email';
            // set recipient
            $recipient = $offer_email;
            $offer_args['recipient'] = $offer_email;
            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

            // set plugin slug in email class
            $new_email->plugin_slug = 'offers-for-woocommerce';

            // define email template/path (html)
            $new_email->template_html = 'woocommerce-offer-received.php';
            $new_email->template_html_path = plugin_dir_path(__FILE__) . 'includes/emails/';

            // define email template/path (plain)
            $new_email->template_plain = 'woocommerce-offer-received.php';
            $new_email->template_plain_path = plugin_dir_path(__FILE__) . 'includes/emails/plain/';
            
            

            if($offer_is_auto_decline == '' && $option_for_admin_disable_email_auto_decline == ''){
                $new_email->trigger($offer_args);
            }
            
            if (is_ajax()) {
                do_action('auto_accept_auto_decline_handler', $offer_id, $product_id, $variant_id, $emails);
            }
            do_action('woocommerce_after_offer_submit', $is_counter_offer, $post, $offer_args);

            // Success
            if (is_ajax()) {
                echo json_encode(array("statusmsg" => 'success'));
                exit;
            } else {
                return false;
            }
        }
        
        return ob_get_clean();
    }
    
    /**
     * Add public query vars for API requests
     * @param array $vars List of current public query vars
     * @return array $vars
     */
    public function add_query_vars($vars) {
        $vars[] = '__aewcoapi';
        $vars[] = 'woocommerce-offer-id';
        $vars[] = 'woocommerce-offer-uid';
        return $vars;
    }

    /**
     * Sniff Api Requests
     * This is where we hijack all API requests
     * @return die if API request
     */
    public function sniff_api_requests() {
        global $wp;
        if (isset($wp->query_vars['__aewcoapi'])) {
            $this->handle_request();
        }
    }

    /** Handle API Requests
     * @return void
     */
    protected function handle_request() {
        global $wp;
        $request_error = false;
        $pid = (isset($wp->query_vars['woocommerce-offer-id'])) ? $wp->query_vars['woocommerce-offer-id'] : '';
        if ($pid == '' || !is_numeric($pid)) {
            $this->send_api_response(__('Missing or Invalid Offer Id; See shop manager for assistance', 'offers-for-woocommerce'));
        } else {
            /**
             * Lookup Offer
             * - Make sure valid 'accepted-offer' or 'countered-offer' status
             */
            $offer = get_post($pid);

            // check for parent offer unique id
            $offer_uid = get_post_meta($offer->ID, 'orig_offer_uid', true);

            // check offer expiration date
            $expiration_date = get_post_meta($offer->ID, 'offer_expiration_date', true);
            $expiration_date_formatted = ($expiration_date) ? date("Y-m-d 23:59:59", strtotime($expiration_date)) : FALSE;

            // Invalid Offer Id
            if ($offer == '') {
                $this->send_api_response(__('Invalid or Expired Offer Id; See shop manager for assistance', 'offers-for-woocommerce'));
            }
            // check for valid uid match
            elseif (( $offer_uid != $wp->query_vars['woocommerce-offer-uid'])) {
                error_log("1572");
                $this->send_api_response(__('Invalid Offer Status or Expired Offer Id; See shop manager for assistance', 'offers-for-woocommerce'));
            }
            // If offer counter 'offer_expiration_date' is past
            elseif (($expiration_date_formatted) && ($expiration_date_formatted <= (date("Y-m-d H:i:s", current_time('timestamp', 0))) )) {
                $request_error = true;
                $this->send_api_response(__('Offer has expired; You can submit a new offer using the form below.', 'offers-for-woocommerce'));
            } else {
                // Get offer meta
                $offer_meta = get_post_meta($offer->ID, '', true);

                // Error - Offer On Hold
                if ($offer->post_status == 'on-hold-offer') {
                    $request_error = true;
                    $this->send_api_response(__('Offer is currently On Hold; We will notify you when offer status is updated.', 'offers-for-woocommerce'));
                }
                // Error - Offer Not Accepted/Countered
                elseif ($offer->post_status != 'accepted-offer' && $offer->post_status != 'countered-offer' && $offer->post_status != 'buyercountered-offer') {
                    if ( apply_filters( 'ofw_not_allow_invalid_offer_status', true,  $offer) ) {
                        error_log("1591");
                        $request_error = true;
                        $this->send_api_response(__('Invalid Offer Status or Expired Offer Id; See shop manager for assistance', 'offers-for-woocommerce'));
                    }
                }
            

                // Define product id
                $product_id = (isset($offer_meta['orig_offer_product_id'][0]) && is_numeric($offer_meta['orig_offer_product_id'][0]) ) ? $offer_meta['orig_offer_product_id'][0] : '';

                // Error - Missing Product Id on the offer meta
                if ($product_id == '' || !is_numeric($product_id)) {
                    $request_error = true;
                    $this->send_api_response(__('Error - Product Not Found; See shop manager for assistance', 'offers-for-woocommerce'));
                }

                // Lookup Product
                $product = new WC_Product($product_id);

                // Error - Invalid Product
                $invalid_if_product_id = version_compare(WC_VERSION, '3.0', '<') ? $product->post->ID : $product->get_id();
                if (!isset($product->post) || $invalid_if_product_id == '' || !is_numeric($product_id)) {
                    $request_error = true;
                    $this->send_api_response(__('Error - Product Not Found; See shop manager for assistance', 'offers-for-woocommerce'));
                }

                if (!$request_error) {
                    do_action('before_add_offer_to_cart', $offer->ID);
                    // Add offer to cart
                    if ($this->add_offer_to_cart($offer, $offer_meta)) {
                        $this->send_api_response(__('Successfully added Offer to cart', 'offers-for-woocommerce'), json_decode($pid));
                    }
                }
            }
        }
    }

    /**
     * Add offer to cart
     * @since   0.1.0
     */
    protected function add_offer_to_cart($offer = array(), $offer_meta = array()) {
        if (!is_admin()) {
            global $woocommerce;

            $quantity = $offer_meta['offer_quantity'][0];
            $product_id = $offer_meta['orig_offer_product_id'][0];
            $product_variation_id = isset($offer_meta['orig_offer_variation_id'][0]) ? $offer_meta['orig_offer_variation_id'][0] : '';

            $_product = ( $product_variation_id ) ? wc_get_product($product_variation_id) : wc_get_product($product_id);
            $_product_stock = version_compare(WC_VERSION, '3.0', '<') ? $_product->get_total_stock() : $_product->get_stock_quantity();

            // lookup product meta by id or variant id
            if ($product_variation_id) {
                $product_variation_data = $_product->get_variation_attributes();
            }

            $product_variation_data['Offer ID'] = $offer->ID;

            $product_meta['woocommerce_offer_id'] = $offer->ID;
            $product_meta['woocommerce_offer_quantity'] = $offer_meta['offer_quantity'][0];
            $product_meta['woocommerce_offer_price_per'] = $offer_meta['offer_price_per'][0];

            $found = false;
           
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                // check if offer id already in cart
                if (isset($cart_item['woocommerce_offer_id']) && $cart_item['woocommerce_offer_id'] == $offer->ID) {
                    if(isset($cart_item['woocommerce_offer_quantity']) && isset($cart_item['woocommerce_offer_price_per'])){
                        WC()->cart->remove_cart_item( $cart_item_key );
                    } else {
                        $found = true;
                        $message = sprintf(
                                '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __('View Cart', 'offers-for-woocommerce'), __('Offer already added to cart', 'offers-for-woocommerce'));
                        $this->send_api_response($message);
                    }
                } 
            }
            
            if (!$found) {
               // WC()->cart->empty_cart();
                $item_id = $woocommerce->cart->add_to_cart($product_id, $quantity, $product_variation_id, $product_variation_data, $product_meta);
            }

            if (isset($item_id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $purchasable
     * @param $_product
     * @return bool
     *
     * https://github.com/angelleye/offers-for-woocommerce/issues/156
     */
    public function angelleye_ofwc_woocommerce_is_purchasable($purchasable, $_product) {
        if ($purchasable === false && $_product->get_price() === '') {
            return true;
	} else {
            return $purchasable;
	}
    }

    /** API Response Handler
     */
    public function send_api_response($msg, $pid = '') {
        global $woocommerce;
        $response['message'] = $msg;
        $response['type'] = 'error';

        if ($pid) {
            $response['pid'] = $pid;
            $response['type'] = 'success';
            wc_add_notice($response['message'], $response['type']);
            wp_safe_redirect(wc_get_cart_url());
            exit;
        }

        wc_add_notice($response['message'], $response['type']);
    }

    /**
     * Sets qty and price on any offer items in cart
     * @param $cart_object
     * @since   0.1.0
     */
    public function my_woocommerce_before_calculate_totals($cart_object) {
        global $woocommerce;

        // loop cart contents to find offers -- force price to offer price per
        foreach ($cart_object->cart_contents as $key => $value) {
            // if offer item found
            if (isset($value['woocommerce_offer_price_per']) && $value['woocommerce_offer_price_per'] != '') {
                $value['data']->set_price($value['woocommerce_offer_price_per']);
                $woocommerce->cart->set_quantity($key, $value['woocommerce_offer_quantity'], false);
            }
        }

        $showerror = false;
        // updating cart with posted values
        if (isset($_POST['cart'])) {
            // loop cart contents to find offers -- force quantity to offer quantity
            foreach ($cart_object->cart_contents as $key => $value) {
                // if offer item found
                if (isset($value['woocommerce_offer_price_per']) && $value['woocommerce_offer_price_per'] != '') {
                    if (array_key_exists($key, $_POST['cart'])) {
                        // post values match with item that is an offer
                        // check if values match original meta VALUES
                        if ($value['woocommerce_offer_quantity'] != $_POST['cart'][$key]['qty']) {
                            $showerror = true;
                            $woocommerce->cart->set_quantity($key, $value['woocommerce_offer_quantity'], false);
                        }
                    }
                }
            }

            // add error notice
            if ($showerror) {
                $message_type = 'error';
                $message = __('Offer quantity cannot be modified', 'offers-for-woocommerce');
                wc_add_notice($message, $message_type);
            }
        }
    }

    /**
     * Set cart items extra meta from session data
     * @param $item
     * @param $values
     * @param $key
     * @return mixed
     */
    function get_cart_items_from_session($item, $values, $key) {
        if (array_key_exists('woocommerce_offer_id', $values)) {
            $item['woocommerce_offer_id'] = $values['woocommerce_offer_id'];
        }
        if (array_key_exists('woocommerce_offer_quantity', $values)) {
            $item['woocommerce_offer_quantity'] = $values['woocommerce_offer_quantity'];
        }
        if (array_key_exists('woocommerce_offer_price_per', $values)) {
            $item['woocommerce_offer_price_per'] = $values['woocommerce_offer_price_per'];
        }        
        return $item;
    }

    /**
     *  Add a custom email to the list of emails WooCommerce should load
     *
     * @since 0.1
     * @param array $email_classes available email classes
     * @return array filtered available email classes
     */
    public function add_woocommerce_email_classes($email_classes) {

        // include our custom email classes
        include_once( 'includes/class-wc-new-offer-email.php' );
        include_once( 'includes/class-wc-new-counter-offer-email.php' );
        include_once( 'includes/class-wc-offer-received-email.php' );

        // add the email class to the list of email classes that WooCommerce loads
        $email_classes['WC_New_Offer_Email'] = new WC_New_Offer_Email();
        $email_classes['WC_New_Counter_Offer_Email'] = new WC_New_Counter_Offer_Email();
        $email_classes['WC_Offer_Received_Email'] = new WC_Offer_Received_Email();

        include_once( untrailingslashit(OFW_PLUGIN_URL) . '/admin/includes/class-wc-accepted-offer-email.php' );
        include_once( untrailingslashit(OFW_PLUGIN_URL) . '/admin/includes/class-wc-declined-offer-email.php' );
        include_once( untrailingslashit(OFW_PLUGIN_URL) . '/admin/includes/class-wc-countered-offer-email.php' );
        include_once( untrailingslashit(OFW_PLUGIN_URL) . '/admin/includes/class-wc-open-offer-email.php' );

        // add the email class to the list of email classes that WooCommerce loads
        $email_classes['WC_Accepted_Offer_Email'] = new WC_Accepted_Offer_Email();
        $email_classes['WC_Declined_Offer_Email'] = new WC_Declined_Offer_Email();
        $email_classes['WC_Countered_Offer_Email'] = new WC_Countered_Offer_Email();
        $email_classes['WC_Open_Offer_Email'] = new WC_Open_Offer_Email();

        return $email_classes;
    }

    /**
     * Action - woocommerce_before_checkout_process
     * Checks for valid offer before checkout process
     * @since   0.1.0
     */
    public function ae_ofwc_woocommerce_before_checkout_process() {
        global $woocommerce;
        foreach ($woocommerce->cart->get_cart() as $cart_item) {
            // check if offer id already in cart
            if (isset($cart_item['woocommerce_offer_id'])) {
                $pid = $cart_item['woocommerce_offer_id'];

                /**
                 * Lookup Offer
                 * - Make sure valid 'accepted-offer' or 'countered-offer' status
                 */
                $offer = get_post($pid);

                // Invalid Offer Id
                if ($offer == '') {
                    $this->send_api_response(__('Invalid or Expired Offer Id; See shop manager for assistance', 'offers-for-woocommerce'), '1');
                } else {
                    // Get offer meta
                    $offer_meta = get_post_meta($offer->ID, '', true);

                    // Error - Offer Not Accepted/Countered
                    if ($offer->post_status != 'accepted-offer' && $offer->post_status != 'countered-offer' && $offer->post_status != 'buyercountered-offer') {
                        if ( apply_filters( 'ofw_not_allow_invalid_offer_status', false,  $offer) ) {
                            error_log("1836");
                            $request_error = true;
                            $this->send_api_response(__('Invalid Offer Status or Expired Offer Id; See shop manager for assistance', 'offers-for-woocommerce'), '0');
                        }
                    }
                }
            }
        }
    }

    /**
     * Akismet spam check
     * Passes back true (it's spam) or false (it's ham)
     * @param $key
     * @param $data
     * @return bool
     * @since   1.2.0
     */
    public function aeofwc_akismet_comment_check($key, $data) {
        $request = 'blog=' . urlencode($data['blog']) .
                '&user_ip=' . urlencode($data['user_ip']) .
                '&user_agent=' . urlencode($data['user_agent']) .
                '&referrer=' . urlencode($data['referrer']) .
                '&permalink=' . urlencode($data['permalink']) .
                '&comment_type=' . urlencode($data['comment_type']) .
                '&comment_author=' . urlencode($data['comment_author']) .
                '&comment_author_email=' . urlencode($data['comment_author_email']) .
                '&comment_author_url=' . urlencode($data['comment_author_url']) .
                '&comment_content=' . urlencode($data['comment_content']);
        $host = $http_host = $key . '.rest.akismet.com';
        $path = '/1.1/comment-check';
        $port = 443;
        $akismet_ua = "WordPress/3.8.1 | Akismet/2.5.9";
        $content_length = strlen($request);
        $http_request = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $http_request .= "Content-Length: {$content_length}\r\n";
        $http_request .= "User-Agent: {$akismet_ua}\r\n";
        $http_request .= "\r\n";
        $http_request .= $request;
        $response = '';
        if (false != ( $fs = @fsockopen('ssl://' . $http_host, $port, $errno, $errstr, 10) )) {

            fwrite($fs, $http_request);

            while (!feof($fs))
                $response .= fgets($fs, 1160); // One TCP-IP packet
            fclose($fs);

            $response = explode("\r\n\r\n", $response, 2);
        }

        if (isset($response[1]) && 'true' == $response[1]) {
            return true;
        } else {
            return false;
    }
    }

    /**
     * 
     * @global type $wpdb
     * @param type $offer_id
     * @param type $emails
     * @since   1.2.0
     */
    public function ofw_auto_approve_offer($offer_id = null, $emails = null, $is_approve = true) {
        global $wpdb, $woocommerce;
        if (isset($_POST["targetID"]) && !empty($_POST["targetID"])) {
            $post_id = absint($_POST["targetID"]);
        } else {
            $post_id = $offer_id;
        }
        if (isset($post_id) && $post_id > 0) {
            $post_data = get_post($post_id);
            $table = $wpdb->prefix . "posts";
            if (empty($emails)) {
                $emails = $woocommerce->mailer()->get_emails();
            }
            if($is_approve){
                $post_status = 'accepted-offer';
                $post_status_text = __('Accepted', 'offers-for-woocommerce');
                $email_class = 'WC_Accepted_Offer_Email';
                $template_name = 'woocommerce-offer-accepted.php';
            } else {
                $post_status = 'declined-offer';
                $post_status_text = __('Declined', 'offers-for-woocommerce');
                $email_class = 'WC_Declined_Offer_Email';
                $template_name = 'woocommerce-offer-declined.php';
            }
            $data_array = array(
                'post_status' => $post_status,
                'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0)),
                'post_modified_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1))
            );
            $where = array('ID' => $post_id);
            $wpdb->update($table, $data_array, $where);
            $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? wc_clean($_POST['angelleye_woocommerce_offer_status_notes']) : '';
            $recipient = get_post_meta($post_id, 'offer_email', true);
            
            $offer_id = $post_id;
            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
            $offer_name = get_post_meta($post_id, 'offer_name', true);
            $offer_email = $recipient;
            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
            $product = ( $variant_id ) ? wc_get_product($variant_id) : wc_get_product($product_id);
            $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;
            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
            $product_total = ($product_qty * $product_price_per);
            if ($is_offer_buyer_countered_status) {
                update_post_meta($post_id, 'offer_quantity', $product_qty);
                update_post_meta($post_id, 'offer_price_per', $product_price_per);
                update_post_meta($post_id, 'offer_shipping_cost', $product_shipping_cost);
                update_post_meta($post_id, 'offer_amount', $product_total);
            }
            $offer_args = array(
                'recipient' => $recipient,
                'offer_email' => $offer_email,
                'offer_name' => $offer_name,
                'offer_id' => $offer_id,
                'offer_uid' => $offer_uid,
                'product_id' => $product_id,
                'product_url' => $product->get_permalink(),
                'variant_id' => $variant_id,
                'product' => $product,
                'product_qty' => $product_qty,
                'product_price_per' => $product_price_per,
                'product_shipping_cost' => $product_shipping_cost,
                'product_total' => $product_total,
                'offer_notes' => $offer_notes
            );
            if ($variant_id) {
                if ($product->get_sku()) {
                    $identifier = $product->get_sku();
                } else {
                    $identifier = '#' . $product->get_id();
                }
                $attributes = $product->get_variation_attributes();
                $extra_data = ' &ndash; ' . implode(', ', $attributes);
                $offer_args['product_title_formatted'] = sprintf(__('%s &ndash; %s%s', 'offers-for-woocommerce'), $identifier, $product->get_title(), $extra_data);
            } else {
                $offer_args['product_title_formatted'] = $product->get_formatted_name();
            }
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;
            $new_email->plugin_slug = 'offers-for-woocommerce';
            $new_email->template_html_path = untrailingslashit(OFW_PLUGIN_URL) . '/admin/includes/emails/';
            $new_email->template_plain_path = untrailingslashit(OFW_PLUGIN_URL) . '/admin/includes/emails/plain/';
            $new_email->template_html = $template_name;
            $new_email->template_plain = $template_name;
            $new_email->trigger($offer_args);
            $comment_text = "<span>" . __('Updated - Status:', 'offers-for-woocommerce') . "&nbsp;</span>";
            $comment_text.= $post_status_text;
            if (isset($offer_notes) && $offer_notes != '') {
                $comment_text.= '</br>' . nl2br($offer_notes);
            }
            $data = array(
                'comment_post_ID' => '',
                'comment_author' => 'admin',
                'comment_author_email' => '',
                'comment_author_url' => '',
                'comment_content' => $comment_text,
                'comment_type' => '',
                'comment_parent' => 0,
                'user_id' => get_current_user_id(),
                'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                'comment_agent' => '',
                'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0)),
                'comment_approved' => 'post-trashed',
            );
            $new_comment_id = wp_insert_comment($data);
            if ($new_comment_id) {
                add_comment_meta($new_comment_id, 'angelleye_woocommerce_offer_id', $post_id, true);
            }
        }
    }

    /**
     * 
     * @param type $offer_id
     * @param type $product_id
     * @param type $variant_id
     * @param type $emails
     * @return boolean
     * @since   1.2.0
     */
    public function ofwc_auto_accept_auto_decline_handler($offer_id, $product_id, $variant_id, $emails) {
        $post_meta_auto_accept_enabled = get_post_meta($product_id, '_offers_for_woocommerce_auto_accept_enabled', true);
        $post_meta_auto_decline_enabled = get_post_meta($product_id, '_offers_for_woocommerce_auto_decline_enabled', true);
        $productData = $this->ofwc_get_product_detail($offer_id, $product_id, $variant_id);
        $offer_price = $productData['offer_price'];
        $user_offer_percentage = $productData['user_offer_percentage'];
        $product_url = $productData['product_url'];
        $offer_uid = $productData['offer_uid'];
        if( isset($post_meta_auto_accept_enabled) && $post_meta_auto_accept_enabled == 'yes') {
            $auto_accept_percentage = get_post_meta($product_id, '_offers_for_woocommerce_auto_accept_percentage', true);
            if( isset($offer_price) && !empty($offer_price) && isset($auto_accept_percentage) && !empty($auto_accept_percentage) ) {

                if( (int) $auto_accept_percentage <= (int) $user_offer_percentage) {
                    do_action('ofw_before_auto_approve_offer', $offer_id, $product_id, $variant_id, $emails);
                    $this->ofw_auto_approve_offer($offer_id, $emails, true);
                    do_action('ofw_after_auto_approve_offer', $offer_id, $product_id, $variant_id, $emails);
                    $link_insert = ( strpos( $product_url, '?') ) ? '&' : '?';
                    $redirect = $product_url. $link_insert .'__aewcoapi=1&woocommerce-offer-id='.$offer_id.'&woocommerce-offer-uid=' .$offer_uid;
                    echo json_encode(array("statusmsg" => 'accepted-offer', 'redirect' => $redirect));
                    exit;
                    //return true;
                }
            }
        }

        if( isset($post_meta_auto_decline_enabled) && $post_meta_auto_decline_enabled == 'yes') {
            $auto_decline_percentage = get_post_meta($product_id, '_offers_for_woocommerce_auto_decline_percentage', true);
            if( isset($offer_price) && !empty($offer_price) && isset($auto_decline_percentage) && !empty($auto_decline_percentage) ) {
                if( (int) $auto_decline_percentage >= (int) $user_offer_percentage) {
                    do_action('ofw_before_auto_decline_offer', $offer_id, $product_id, $variant_id, $emails);
                    $this->ofw_auto_approve_offer($offer_id, $emails, false);
                    do_action('ofw_after_auto_decline_offer', $offer_id, $product_id, $variant_id, $emails);
                    //return true;
                }
            }
        }
    }

    /**
     * 
     * @param type $num_amount
     * @param type $num_total
     * @return type
     * @since   1.2.0
     */
    public function ofwc_get_percentage($num_amount, $num_total) {
        $count_devide = $num_amount / $num_total;
        $count_multi = $count_devide * 100;
        $percentage = number_format($count_multi, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
        return $percentage;
    }

    /**
     * @since   1.2.0
     */
    public function ofw_create_required_files() {
        // Install files and folders for uploading files and prevent hotlinking
        $upload_dir = wp_upload_dir();

        $files = array(
            array(
                'base' => OFFERS_FOR_WOOCOMMERCE_LOG_DIR,
                'file' => '.htaccess',
                'content' => 'deny from all'
            ),
            array(
                'base' => OFFERS_FOR_WOOCOMMERCE_LOG_DIR,
                'file' => 'index.html',
                'content' => ''
            )
        );

        foreach ($files as $file) {
            if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) {
                if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) {
                    fwrite($file_handle, $file['content']);
                    fclose($file_handle);
                }
            }
        }
    }

    /**
     * @since   1.2.0
     * @return boolean
     */
    public function is_mailchimp_enable() {
        $enable_mailchimp = get_option('ofw_enable_mailchimp');
        $ofw_mailchimp_api_key = get_option('ofw_mailchimp_api_key');
        $ofw_mailchimp_lists = get_option('ofw_mailchimp_lists');
        if ((isset($enable_mailchimp) && $enable_mailchimp == 'yes') && (isset($ofw_mailchimp_api_key) && !empty($ofw_mailchimp_api_key)) && (isset($ofw_mailchimp_lists) && !empty($ofw_mailchimp_lists))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @since   1.2.0
     * @return boolean
     */
    public function is_constant_contact_enable() {
        $enable_constant_contact = get_option('ofwenable_constant_contact');
        $ofw_constant_contact_api_key = get_option('ofw_constantcontact_api_key');
        $ofw_constant_contact_lists = get_option('ofw_constantcontact_lists');
        $ofw_constantcontact_access_token = get_option('ofw_constantcontact_access_token');
        if ((isset($enable_constant_contact) && $enable_constant_contact == 'yes') && (isset($ofw_constant_contact_api_key) && !empty($ofw_constant_contact_api_key)) && (isset($ofw_constant_contact_lists) && !empty($ofw_constant_contact_lists)) && (isset($ofw_constantcontact_access_token) && !empty($ofw_constantcontact_access_token))) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @since   1.2.0
     * @return boolean
     */
    public function is_mailpoet_enable() {
        $enable_mailpoet = get_option('ofw_enable_mailpoet');
        $ofw_mailpoet_lists = get_option('ofw_mailpoet_lists');
        if ((isset($enable_mailpoet) && $enable_mailpoet == 'yes') && (isset($ofw_mailpoet_lists) && !empty($ofw_mailpoet_lists))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @since   1.2.0
     * @return boolean
     */
    public function ofw_is_mailling_list_enable() {
        if ($this->is_mailchimp_enable() || $this->is_constant_contact_enable() || $this->is_mailpoet_enable()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @since   1.2.0
     * @param type $is_counter_offer
     */
    public function woocommerce_make_offer_form_end_own($is_counter_offer) {
        if ($this->ofw_is_mailling_list_enable() && !$is_counter_offer) {
            ?>
            <div class="woocommerce-make-offer-form-section">
                <div class="woocommerce-make-offer-form-part-left">
                    <input name="join_our_mailing_list" id="join_our_mailing_list" type="checkbox" value="yes" checked="checked"><label for="join_our_mailing_list" class="checkbox"><?php echo str_repeat('&nbsp;', 1); ?> <?php echo apply_filters('aeofwc-offer-form-label-join-our-mailing-list', __('Join Our Mailing List', 'offers-for-woocommerce')); ?></label>
                </div>
            </div>
            
            <?php
        }
    }

    /**
     * @since   1.2.0
     * @param type $is_counter_offer
     */
    public function ofw_mailing_list_handler($is_counter_offer, $post_data) {
        if ($this->ofw_is_mailling_list_enable() && !$is_counter_offer) {
            if (isset($post_data['join_our_mailing_list']) && $post_data['join_our_mailing_list'] == "yes") {
                 if (isset($post_data) && !empty($post_data)) {
                    if ($this->is_mailchimp_enable()) {
                        include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-mailchimp-helper.php';
                        $OFW_MailChimp_Helper = new AngellEYE_Offers_for_Woocommerce_MailChimp_Helper();
                        $OFW_MailChimp_Helper->ofw_mailchimp_handler($post_data);
                    } elseif($this->is_constant_contact_enable()) {
                        include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-constant-contact-helper.php';
                        $OFW_MailChimp_Helper = new AngellEYE_Offers_for_Woocommerce_ConstantContact_Helper();
                        $OFW_MailChimp_Helper->ofw_constantcontact_handler($post_data);
                    } elseif($this->is_mailpoet_enable()) {
                        include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-mailpoet-helper.php';
                        $OFW_MailChimp_Helper = new AngellEYE_Offers_for_Woocommerce_MailPoet_Helper();
                        $OFW_MailChimp_Helper->ofw_mailpoet_handler($post_data);
                    }
                }
            }
            return true;
        }
    }
    
    public function set_session($key, $value) {
        WC()->session->$key = $value;
    }
    
    public function ofw_coupons_enabled($boolean) {
	    if ( !did_action( 'wp_loaded' ) )
	        return $boolean;

        $button_options_general = get_option('offers_for_woocommerce_options_general');
        if(!is_admin() && !empty(WC()->cart) && !WC()->cart->is_empty() && (isset($button_options_general['general_setting_disable_coupon']) && $button_options_general['general_setting_disable_coupon'] != '')) {
            foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                if( isset($values['woocommerce_offer_id']) && !empty($values['woocommerce_offer_id'])) {
                    if( !empty(WC()->cart->get_applied_coupons()) ) {
                        WC()->cart->set_applied_coupons(array());
                        WC()->cart->calculate_totals();
                    }
                    return false;
                }
            }
        }
        return $boolean;
    }

    public function ofw_woocommerce_after_my_account(){
        include_once(OFW_PLUGIN_URL . 'public/views/my-offers.php');
    }

    public function ofw_is_anonymous_communication_enable() {
        $offers_for_woocommerce_options_general = get_option('offers_for_woocommerce_options_general');
        if( isset($offers_for_woocommerce_options_general['general_setting_enable_anonymous_communication']) && $offers_for_woocommerce_options_general['general_setting_enable_anonymous_communication'] == 1 ) {
            return true;
        } 
        return false;
    }

     public function is_recaptcha_enable() {
         $ofw_enable_recaptcha = get_option('ofw_enable_recaptcha');
         if( empty($ofw_enable_recaptcha) || $ofw_enable_recaptcha == 'no' ) {
             return false;
         }
         $ofw_recaptcha_site_key = get_option('ofw_recaptcha_site_key');
         if( empty($ofw_recaptcha_site_key) ) {
             return false;
         }
         $ofw_recaptcha_secret_key = get_option('ofw_recaptcha_secret_key');
         if( empty($ofw_recaptcha_secret_key) ) {
             return false;
         }
         return true;
     }
     
     public function recaptcha_verify_response($response) {
         if($this->is_recaptcha_enable()) {
             $recaptcha_url = add_query_arg( array( 'secret' => get_option('ofw_recaptcha_secret_key'), 'response' => $response, 'remoteip' => $_SERVER['REMOTE_ADDR']), 'https://www.google.com/recaptcha/api/siteverify' );
             return wp_remote_retrieve_body( wp_remote_get($recaptcha_url) );
         }
     }
     
     public function ofw_display_pending_offer_lable_product_details_page($product_id) {
        if($this->ofw_is_show_pending_offer_enable()) {
            global $wpdb;
            $args = array(
                'post_type'  => 'woocommerce_offer',
                'post_status'  => 'publish',
                'posts_per_page'  => -1,
                'meta_query' => array(
                    array(
                        'key'     => 'offer_product_id',
                        'value'   => $product_id,
                        'compare' => '=',
                    ),
                ),
            );
            $query = new WP_Query( $args );
            $total_offer = $query->post_count;
            if($total_offer > 0) {
                echo '<div class="ofw-info"> ' . sprintf( _n( '%d offer is currently pending.', '%d offers are currently pending.', $total_offer, 'offers-for-woocommerce' ), $total_offer ) . '</div>';
            }
        }
     }
     
    public function ofw_is_show_pending_offer_enable() {
        $offers_for_woocommerce_options_general = get_option('offers_for_woocommerce_options_general');
        if( isset($offers_for_woocommerce_options_general['general_setting_show_pending_offer']) && $offers_for_woocommerce_options_general['general_setting_show_pending_offer'] == 1 ) {
            return true;
        } 
        return false;
    }

    public function add_your_shipping_method( $methods ) {
        $methods[] = 'Angelleye_Offers_For_Woocommerce_Shipping_Method';
        return $methods;
    }
        
    public function your_shipping_method_init() {
        if ( ! class_exists( 'Angelleye_Offers_For_Woocommerce_Shipping_Method' ) ) {
            include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-shipping.php';
        }
    }
    
    public function hide_shipping_when_offer_for_woocommerce_is_available( $rates, $package ) {
        if($this->is_offer_product_in_cart()) {
            if ( isset( $rates['offer_for_woocommerce_shipping'] ) ) {
                $offer_for_woocommerce_shipping = $rates['offer_for_woocommerce_shipping'];
                $rates = array();
                $rates['offer_for_woocommerce_shipping'] = $offer_for_woocommerce_shipping;
            }
        }
	return $rates;
    }
    
    public function is_offer_product_in_cart() {
        $count = 0;
        $has_product = FALSE;
        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
            if( isset($values['woocommerce_offer_id']) && !empty($values['woocommerce_offer_id'])) {
                $has_product = true;
            } else {
                $count++;
            }
        }
        if($count < 1 && $has_product) {
            return true;
        } else {
            return false;
        }
    }
    
    public function ofw_display_highest_current_offer() {
        global $post, $wpdb;
        if($this->ofw_is_highest_current_bid_enable()) {
            $highest_offers = $this->ofw_get_highest_current_offer_data();
            $post_id = (isset($highest_offers['post_id']) && !empty($highest_offers['post_id'])) ? $highest_offers['post_id'] : 0;
            $max_offer = (isset($highest_offers['max_offer']) && !empty($highest_offers['max_offer'])) ? $highest_offers['max_offer'] : 0;
            if($post_id > 0 && $max_offer > 0) {
                echo '<div class="ofw-info"> ' . sprintf( _n( 'Highest Current Offer: %s%s', 'Highest Current Offer: %s%s', get_woocommerce_currency_symbol(), wc_format_decimal( $max_offer, wc_get_price_decimals() ), 'offers-for-woocommerce' ), get_woocommerce_currency_symbol(), wc_format_decimal( $max_offer, wc_get_price_decimals() ) ) . '</div>';
            }
        }
    }
    
    public function ofw_get_highest_current_offer_data() {
        global $post, $wpdb;
        $total_result = $wpdb->get_results($wpdb->prepare("
            SELECT MAX( postmeta.meta_value ) AS max_offer, posts.ID as post_id
            FROM $wpdb->postmeta AS postmeta
            JOIN $wpdb->postmeta pm2 ON pm2.post_id = postmeta.post_id
            INNER JOIN $wpdb->posts AS posts ON ( posts.post_type = 'woocommerce_offer' AND posts.post_status IN ('publish'))
            WHERE postmeta.meta_key LIKE 'offer_price_per' AND pm2.meta_key LIKE 'offer_product_id' AND pm2.meta_value LIKE %d
            AND postmeta.post_id = posts.ID
        ", $post->ID), ARRAY_A);
        return $total_result[0];
    }
    
    public function ofw_display_highest_current_offer_shortcode() {
        global $post, $wpdb;
        $this->ofw_display_highest_current_offer();
    }

    public function ofw_is_highest_current_bid_enable() {
        $offers_for_woocommerce_options_general = get_option('offers_for_woocommerce_options_general');
        if( isset($offers_for_woocommerce_options_general['general_setting_show_highest_current_bid']) && $offers_for_woocommerce_options_general['general_setting_show_highest_current_bid'] == 1 ) {
            return true;
        } 
        return false;
    }
    
    public function ofwc_body_class($classes) {
        $offers_for_woocommerce_options_general = get_option('offers_for_woocommerce_options_general');
        if( isset($offers_for_woocommerce_options_general['general_setting_enable_make_offer_btn_catalog']) && $offers_for_woocommerce_options_general['general_setting_enable_make_offer_btn_catalog'] == 1 && is_shop() ) {
            $classes[] = 'ofwc-shop-page';
        }
        return $classes;
    }
    
    /**
     * Wrap add to cart button with our div to hide it from products loop.
     *
     * @since	0.1.0
     */
    public function ofw_woocommerce_loop_add_to_cart_link( $link, $product ) {
        if($product->get_price() === ''){
            return '<div class="ofwc_no_price_product">'.$link.'</div>';
        }
        return $link;
    }
    
    public function ofwc_get_pending_offer_count_by_product_id($product_id, $count_only = false) {
	global $wpdb;
	$args = array(
		'post_type' => 'woocommerce_offer',
		'post_status' => array( 'publish','countered-offer','buyercountered-offer' ),
		'posts_per_page' => -1,
		'meta_key' => 'offer_product_id',
		'meta_value' => $product_id,
		'meta_compare' => '==',
	);
	$query = new WP_Query( $args );
	$count = $query->post_count;
        if($count_only)
            return $count;
	return $query;
    }
    
    function ofwc_manage_offer_admin_bar_callback( $wp_admin_bar ) {
        if ( ! is_user_logged_in() )
            return;
        
        $user = wp_get_current_user();
        $allowed_roles = array('vendor', 'administrator', 'shop_manager');
        if ( ! array_intersect($allowed_roles, $user->roles ) )
            return;
        
        if(!is_singular( array( 'product' ) ) || is_admin())
            return;
        
        global $wpdb, $post;
        
        if($this->ofwc_get_pending_offer_count_by_product_id($post->ID, true) > 0){
            $args = array(
                'id'    => 'manage_offers',
                'title' => 'Manage Offers',
                'href'  => get_edit_post_link($post->ID).'#ofwc_product_offers',
                'meta'  => array( 'class' => 'ofwc-admin-bar-offers' )
            );
            $wp_admin_bar->add_node( $args );
        }
    }
    /*
     * Fetch product variant price or regular/sale price.
     *  @since	1.4.8      
     */
    
    public function ofwc_get_product_detail($offer_id, $product_id, $variant_id) {
        $productData = array();
        if( isset($variant_id) && !empty($variant_id) ) {
            $variable_product = new WC_Product_Variation( $variant_id );
            $actual_regular_price = (isset($variable_product->regular_price) && !empty($variable_product->regular_price) ) ? $variable_product->regular_price : 0;
            $actual_sales_price = $variable_product->sale_price;
        } else {
            $actual_regular_price = get_post_meta( $product_id, '_regular_price', true);
            $actual_sales_price = get_post_meta( $product_id, '_sale_price', true);
            $actual_regular_price = (isset($actual_regular_price) && !empty($actual_regular_price) ) ? $actual_regular_price : 0;
        }
        $product_price = (isset($actual_sales_price) && !empty($actual_sales_price)) ? $actual_sales_price : $actual_regular_price;
        $productData['offer_price'] = $offer_price = get_post_meta($offer_id, 'offer_price_per', true);
        $productData['user_offer_percentage'] = $user_offer_percentage = $this->ofwc_get_percentage($offer_price, $product_price);
        $product = ( $variant_id ) ? wc_get_product($variant_id) : wc_get_product($product_id);
        $productData['product_url'] = $product_url = $product->get_permalink();
        $productData['offer_uid'] = $offer_uid = get_post_meta($offer_id, 'offer_uid', true);
        return $productData;
    }

    public function ofwc_translate_order_item_display_meta_key($display_key) {
        if($display_key == 'Offer ID') {
            return __($display_key.':', 'offers-for-woocommerce');
        }
        return $display_key;
    }
    
    public function ofw_not_allow_invalid_offer_status($bool,  $offer) {
        if( isset($offer->post_author) && !empty($offer->post_author) && isset($offer->post_status) && $offer->post_status == 'publish') {
            $ofw_created_by = get_post_meta($offer->ID, 'ofw_created_by', true);
            if($ofw_created_by == 'admin') {
                return false;
            }
        }
        return $bool;
    }
    
    public function ofw_admin_created_offer_status($post_status, $post_id) {
        $ofw_created_by = get_post_meta($post_id, 'ofw_created_by', true);
        if($ofw_created_by == 'admin') {
            return 'countered-offer';
        }
        return $post_status;
    }
    
    public function ofw_woocommerce_cart_item_quantity($product_quantity, $cart_item_key, $cart_item) {
        if (isset(WC()->cart) && sizeof(WC()->cart->get_cart()) > 0) {
            foreach (WC()->cart->get_cart() as $cart_key => $cart_item) {
                if( isset($cart_item['woocommerce_offer_id']) && $cart_item_key == $cart_key) {
                    $product_quantity = sprintf( '%s <input type="hidden" name="cart[%s][qty]" value="%s" />', $cart_item['quantity'], $cart_item_key, $cart_item['quantity']);
                }
            }
        }
        return $product_quantity;
    }
    
    public function ofw_add_offer_endpoint() {
        add_rewrite_endpoint( 'offers', EP_PAGES );
    }

    public function ofw_woocommerce_account_menu_items($items) {
        if( !empty($items)) {
             $items['offers'] = __('Offers', 'offers-for-woocommerce');
        }
        return $items;
    }
    
    public function ofw_woocommerce_get_query_vars($query_vars) {
        if( !empty($query_vars) ) {
            $query_vars['offers'] = 'offers';
        }
        return $query_vars;
    }
    
    public function ofw_my_offer_content() {
        try {
            include_once(OFW_PLUGIN_URL . 'public/views/my-offers.php');
        } catch (Exception $ex) {

        }
    }
    
    public function ofw_woocommerce_endpoint_offers_title($title, $endpoint) {
        if($endpoint === 'offers') {
            $title = __('Offers', 'offers-for-woocommerce');
        }
        return $title;
    }

}
