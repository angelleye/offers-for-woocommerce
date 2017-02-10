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
    const VERSION = '1.3.1';

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

        /* Add "Make Offer" button code parts - Before add to cart */
	add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'angelleye_ofwc_before_add_to_cart_button' ) );

        /* Add "Make Offer" button code parts - After add to cart */
        add_action('woocommerce_after_add_to_cart_button', array($this, 'angelleye_ofwc_after_add_to_cart_button'));

        /* Add "Make Offer" button code parts - After shop loop item */
        add_action('woocommerce_after_shop_loop_item', array($this, 'angelleye_ofwc_after_show_loop_item'), 99, 2);
	
        /* Add "Make Offer" button code parts - After summary for products without price */
        add_action('woocommerce_after_single_product_summary', array($this, 'angelleye_ofwc_woocommerce_after_single_product_summary'), 11);

        /* Add "Lighbox Make Offer Form" before single product content */
        add_action('woocommerce_before_single_product', array($this, 'angelleye_ofwc_lightbox_make_offer_form'));

        /* Add "Make Offer" product tab on product single view */
        add_filter('woocommerce_product_tabs', array($this, 'angelleye_ofwc_add_custom_woocommerce_product_tab'),99);

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
        add_action('woocommerce_before_calculate_totals', array($this, 'my_woocommerce_before_calculate_totals'));

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
         * Action - woocommerce_checkout_order_processed
         * @since   0.1.0
         */
        add_action('woocommerce_checkout_order_processed', array($this, 'ae_ofwc_woocommerce_checkout_order_processed'));

        /**
         * Filter - ae_paypal_standard_additional_parameters
         * @since   0.1.0
         */
        add_filter('woocommerce_paypal_args', array($this, 'ae_paypal_standard_additional_parameters'));

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
        
        add_action( 'woocommerce_after_my_account', array($this, 'ofw_woocommerce_after_my_account'));
        
        add_filter( 'woocommerce_shipping_methods', array($this, 'add_your_shipping_method' ), 10, 1);
        add_action( 'woocommerce_shipping_init', array($this, 'your_shipping_method_init' ));
        add_filter( 'woocommerce_package_rates', array($this, 'hide_shipping_when_offer_for_woocommerce_is_available'), 10, 2 );
        add_shortcode( 'highest_current_offer', array($this, 'ofw_display_highest_current_offer_shortcode'), 10 );
        add_filter('woocommerce_is_purchasable',array($this,'angelleye_ofwc_woocommerce_is_purchasable'),999,2);
        add_action( 'woocommerce_before_customer_login_form', array($this, 'ofw_before_customer_login_form'));
        add_filter('woocommerce_login_redirect',array($this,'ofw_login_redirect'),10,1);
        add_filter('woocommerce_registration_redirect',array($this,'ofw_login_redirect'),10,1);
        add_filter('woocommerce_loop_add_to_cart_link',array($this,'ofw_woocommerce_loop_add_to_cart_link'),10,2);        
    }
    
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
            $join_url = (strpos($_GET['backto'], '?') !== false) ? '&aewcobtn=1' : '?aewcobtn=1';
            return $_GET['backto'].$join_url;
        }
        return $redirect;
        add_filter('body_class',array($this,'ofwc_body_class'));
    }
    
    /**
     * Generates output of offer button
     *
     * @since	0.1.0
     */
    public function angelleye_ofwc_offer_button_output($is_archive = false) {
        global $post,$wp;;
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
        $is_external_product = ( isset($_product->product_type) && $_product->product_type == 'external' ) ? TRUE : FALSE;
        $is_instock = $_product->is_in_stock();
        
        $custom_tab_options_offers = array(
            'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),
        );
        
        $btn_output = '';
        
        if ($custom_tab_options_offers['enabled'] == 'yes' && !$is_external_product && $is_instock) {
            $button_title = (isset($button_options_display['display_setting_custom_make_offer_btn_text']) && $button_options_display['display_setting_custom_make_offer_btn_text'] != '') ? $button_options_display['display_setting_custom_make_offer_btn_text'] : __('Make Offer', 'offers-for-woocommerce');

            $custom_styles_override = '';
            if ($button_options_display) {
                if (isset($button_options_display['display_setting_custom_make_offer_btn_text_color']) && $button_options_display['display_setting_custom_make_offer_btn_text_color'] != '') {
                    $custom_styles_override .= 'color:' . $button_options_display['display_setting_custom_make_offer_btn_text_color'] . ' !important;';
                }
                if (isset($button_options_display['display_setting_custom_make_offer_btn_color']) && $button_options_display['display_setting_custom_make_offer_btn_color'] != '') {
                    $custom_styles_override .= ' background:' . $button_options_display['display_setting_custom_make_offer_btn_color'] . ' !important; border-color:' . $button_options_display['display_setting_custom_make_offer_btn_color'] . ' !important;';
                }
            }
            
            $lightbox_class = (isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') ? ' offers-for-woocommerce-make-offer-button-single-product-lightbox' : '';
            $btn_output = '<div class="single_variation_wrap_angelleye ofwc_offer_tab_form_wrap">';
            $permalink = get_permalink($post->ID);
            $permalink.= (strpos($permalink, '?') !== false) ? '&aewcobtn=1' : '?aewcobtn=1';
            if($req_login){
                $redirect_url = '';
                if($is_archive){
                    $redirect_url = get_permalink( get_option('woocommerce_myaccount_page_id') ) . '?ref=make-offer&backto='.get_permalink($post->ID);
                    $button = '<a href="' . $redirect_url . '" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-catalog button alt" ' . $custom_styles_override . '>' . $button_title . '</a>';
                } else {
                    $redirect_url = get_permalink( get_option('woocommerce_myaccount_page_id') ) . '?ref=make-offer&backto='.home_url(add_query_arg(array(),$wp->request));
                    $button = '<a href="'.$redirect_url.'"><button type="button" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-single-product ' . $lightbox_class . ' button alt" style="' . $custom_styles_override . '">' . $button_title . '</button></a>';
                }
                
            } else {
                if($is_archive){
                    $button = '<a href="' . $permalink . '" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-catalog button alt" ' . $custom_styles_override . '>' . $button_title . '</a>';
                } else {
                    $button = '<button type="button" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-single-product ' . $lightbox_class . ' button alt" style="' . $custom_styles_override . '">' . $button_title . '</button>';
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
        $button_options_display = get_option('offers_for_woocommerce_options_display');
        $no_price_product_class = ( $_product->get_price() === '' ) ? 'ofwc_no_price_product' : '';
        $button_position = $button_options_display['display_setting_make_offer_button_position_single'];
        $is_on_right = ($button_position == 'right_of_add') ? 'ofwc-button-right-of-add-to-cart' : '';
        $button_options_display = get_option('offers_for_woocommerce_options_display');
        if($button_options_display['display_setting_make_offer_button_position_single'] == 'before_add'){
            echo $this->angelleye_ofwc_offer_button_output();
        }
        echo '<div id="offers-for-woocommerce-add-to-cart-wrap" class="offers-for-woocommerce-add-to-cart-wrap '. $is_on_right . '" data-ofwc-position="' . $button_position . '"><div class="aofwc-first-div ' . $no_price_product_class . '">';
    }
    
    /**
     * Add Make Offer button after add to cart button
     *
     * @since	0.1.0
     */
    public function angelleye_ofwc_after_add_to_cart_button() {
        global $post;
        echo '</div>'; // .aofwc-first-div
        $button_options_display = get_option('offers_for_woocommerce_options_display');
        if($button_options_display['display_setting_make_offer_button_position_single'] == 'default' || $button_options_display['display_setting_make_offer_button_position_single'] == 'right_of_add'){
            echo $this->angelleye_ofwc_offer_button_output();
        }
        echo '</div>'; // #offers-for-woocommerce-add-to-cart-wrap
        echo '<div class="angelleye-offers-clearfix"></div>';                
        $this->ofw_display_highest_current_offer();
        $this->ofw_display_pending_offer_lable_product_details_page($post->ID);
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

        $is_lightbox = ( isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') ? TRUE : FALSE;
        $on_exit_enabled = get_post_meta($post->ID, 'offers_for_woocommerce_onexit_only', true);
        $on_exit_enabled = (isset($on_exit_enabled) && $on_exit_enabled != '') ? $on_exit_enabled : 'no';
        
        if ($is_lightbox || $on_exit_enabled == "yes") {
            echo '<div id="lightbox_custom_ofwc_offer_form">';
            $this->angelleye_ofwc_display_custom_woocommerce_product_tab_content();
            echo '</div>';
            echo '<div id="lightbox_custom_ofwc_offer_form_close_btn"></div>';
        }
        if($on_exit_enabled == "yes"){
            ?>
            <script type="text/javascript">
                jQuery( document ).ready(function($) {
                    $(window).on('mouseout', function(e) {
                        var from = e.relatedTarget || e.toElement;
                        var visited = $.cookie("onexit_cookie");
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
                            $.cookie("onexit_cookie", '1', {
                              expires: date
                            });
                        }
                    });
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
        if($_product == false) {
            return $tabs;
        }
        $is_external_product = ( isset($_product->product_type) && $_product->product_type == 'external' ) ? TRUE : FALSE;
        $is_instock = ( $_product->is_in_stock() ) ? TRUE : FALSE;

        // if post has offers button enabled
        if ($custom_tab_options_offers['enabled'] == 'yes' && !$is_external_product && $is_instock && $custom_tab_options_offers['on_exit'] != 'yes') {
            if (isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') {
                return $tabs;
            }

            $tab_title = (isset($button_options_display['display_setting_custom_make_offer_btn_text']) && $button_options_display['display_setting_custom_make_offer_btn_text'] != '') ? $button_options_display['display_setting_custom_make_offer_btn_text'] : __('Make Offer', 'offers-for-woocommerce');
            $tab_title = apply_filters('woocommerce_make_offer_form_tab_name', $tab_title);
            
            // Add new tab "Make Offer"
            $tabs['tab_custom_ofwc_offer'] = array(
                'title' => $tab_title,
                'priority' => 50,
                'callback' => array($this, 'angelleye_ofwc_display_custom_woocommerce_product_tab_content'));

            // Set priority of the new tab to 20 -- second place
            $tabs['tab_custom_ofwc_offer']['priority'] = 20;
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
        $is_sold_individually = $_product->is_sold_individually();
        $is_backorders_allowed = $_product->backorders_allowed();
        $stock_quantity = $_product->get_stock_quantity();

        $global_limit_quantity_to_stock = ($button_options_general && isset($button_options_general['general_setting_limit_offer_quantity_by_stock']) && $button_options_general['general_setting_limit_offer_quantity_by_stock'] != '') ? true : false;

        $new_offer_quantity_limit = (!$is_backorders_allowed && $stock_quantity && $stock_quantity > 0 && $global_limit_quantity_to_stock) ? $stock_quantity : '';

        // set parent offer id if found in get var
        $parent_offer_id = (isset($_GET['offer-pid']) && $_GET['offer-pid'] != '') ? $_GET['offer-pid'] : '';
        $parent_offer_uid = (isset($_GET['offer-uid']) && $_GET['offer-uid'] != '') ? $_GET['offer-uid'] : '';
        $offer_name = (isset($_GET['offer-name']) && $_GET['offer-name'] != '') ? $_GET['offer-name'] : '';
        $offer_email = (isset($_GET['offer-email']) && $_GET['offer-email'] != '') ? $_GET['offer-email'] : '';

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
                    $parent_offer_id = '';
                    $parent_offer_error = true;
                    $parent_offer_error_message = __('Invalid Parent Offer Id; See shop manager for assistance.', 'offers-for-woocommerce');
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

        $currency_symbol = get_woocommerce_currency_symbol();
        $is_anonymous_communication_enable = $this->ofw_is_anonymous_communication_enable();
        // Set html content for output
        $is_recaptcha_enable = $this->is_recaptcha_enable();
        include_once( 'views/public.php' );
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
        flush_rewrite_rules();

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
        flush_rewrite_rules();

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
            wp_enqueue_script('offers-for-woocommerce-plugin-script', plugins_url('assets/js/public.js', __FILE__), array('jquery'), self::VERSION);
            wp_enqueue_script('offers-for-woocommerce-plugin-script-jquery-auto-numeric-1-9-24', plugins_url('assets/js/autoNumeric-1-9-24.js', __FILE__), self::VERSION);
            if (wp_script_is('offers-for-woocommerce-plugin-script')) {
                wp_localize_script('offers-for-woocommerce-plugin-script', 'offers_for_woocommerce_js_params', apply_filters('offers_for_woocommerce_js_params', array(
                    'is_product_type_variable' => $is_product_type_variable,
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'offers_for_woocommerce_params_nonce' => wp_create_nonce("offers_for_woocommerce_params_nonce"),
                    'i18n_make_a_selection_text' => esc_attr__( 'Please select some product options before making offer for this product.', 'offers-for-woocommerce' ),
                    'i18n_unavailable_text' => esc_attr__( 'Sorry, this product is unavailable. Please choose a different product.', 'offers-for-woocommerce' )
                )));
            }
            if($this->is_recaptcha_enable()) {
                wp_enqueue_script('offers-for-woocommerce-recaptcha', 'https://www.google.com/recaptcha/api.js', array('jquery'), self::VERSION);
            }
        }
    }

    public function new_offer_form_submit()
    {
        ob_start();
        $post_data = $formData = $newPostData = array();
        if (is_ajax()) {
            if( isset($_POST['value']) && !empty($_POST['value']) ) {
                $post_data = $_POST['value'];
                foreach ($post_data as $key => $post_data_value) {
                   if( isset($post_data_value['name']) && !empty($post_data_value['name']) ) {
                        $post[$post_data_value['name']] = ( isset($post_data_value['value']) && !empty($post_data_value['value']) ) ? $post_data_value['value'] : '';
                   }
                }
            }
        } else {
            if( isset($_POST['value']) && !empty($_POST['value']) ) {
                $post_data = $_POST['value'];
                foreach ($post_data as $key => $post_data_value) {
                   if( isset($post_data_value) && !empty($post_data_value) ) {
                        $post[$key] = ( isset($post_data_value) && !empty($post_data_value) ) ? $post_data_value : '';
                   }
                }
            }
        }


        global $wpdb,$woocommerce; // this is how you get access to the database

                    // Check if form was posted and select task accordingly
        if(isset($post["offer_product_id"]) && $post["offer_product_id"] != '')
        {
            // set postmeta original vars
            $formData['orig_offer_name'] = (isset($post['offer_name'])) ? $post['offer_name'] : '';
            $formData['orig_offer_company_name'] = (isset($post['offer_company_name'])) ? $post['offer_company_name'] : '';
            $formData['orig_offer_phone'] = (isset($post['offer_phone'])) ? $post['offer_phone'] : '';
            $formData['orig_offer_email'] = (isset($post['offer_email'])) ? $post['offer_email'] : '';
            $formData['orig_offer_product_id'] = (isset($post['offer_product_id'])) ? $post['offer_product_id'] : '';
            $formData['orig_offer_variation_id'] = (isset($post['offer_variation_id'])) ? $post['offer_variation_id'] : '';
                            $formData['orig_offer_quantity'] = (isset($post['offer_quantity'])) ? $post['offer_quantity'] : '0';
            $formData['orig_offer_price_per'] = (isset($post['offer_price_each'])) ? $post['offer_price_each'] : '0';
                            $formData['orig_offer_amount'] = number_format(round($formData['orig_offer_quantity'] * $formData['orig_offer_price_per'], 2), 2, '.', '');
            $formData['orig_offer_uid'] = uniqid('aewco-');;
            $formData['parent_offer_uid'] = (isset($post['parent_offer_uid'])) ? $post['parent_offer_uid'] : '';

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
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
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

            if ($is_counter_offer) {
                // check for parent offer unique id
                $parent_post_offer_uid = get_post_meta($parent_post_id, 'offer_uid', true);

                // check for valid parent offer ( must be a offer post type and accepted/countered and uid must match
                if ((isset($parent_post_status) && $parent_post_status != 'countered-offer') || ($post_parent_type != 'woocommerce_offer') || ($parent_post_offer_uid != $formData['parent_offer_uid'])) {
                    if (is_ajax()) {
                        echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __('Invalid Parent Offer Id; See shop manager for assistance', 'offers-for-woocommerce')));
                        exit;
                    } else {
                        $this->set_session('ofwpa_issue', 'Invalid Parent Offer Id; See shop manager for assistance');
                        return false;
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

                $formDataUpdated['offer_quantity'] = $formData['offer_amount'];
                $formDataUpdated['offer_price_per'] = $formData['offer_amount'];
                $formDataUpdated['offer_amount'] = $formData['offer_amount'];

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
                    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
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

            if ($variant_id) {
                if ($product->get_sku()) {
                    $identifier = $product->get_sku();
                } else {
                    $identifier = '#' . $product->variation_id;
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
                $emails = $_POST['value']['emails_object'];
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
            $new_email->trigger($offer_args);

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

            $new_email->trigger($offer_args);
            if (is_ajax()) {
                do_action('auto_accept_auto_decline_handler', $offer_id, $product_id, $variant_id, $emails);
            }
            do_action('woocommerce_after_offer_submit', $is_counter_offer, $post);

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
                    $request_error = true;
                    $this->send_api_response(__('Invalid Offer Status or Expired Offer Id; See shop manager for assistance', 'offers-for-woocommerce'));
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
                if (!isset($product->post) || $product->post->ID == '' || !is_numeric($product_id)) {
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
            $product_variation_id = $offer_meta['orig_offer_variation_id'][0];

            $_product = ( $product_variation_id ) ? wc_get_product($product_variation_id) : wc_get_product($product_id);
            $_product_stock = $_product->get_total_stock();

            // lookup product meta by id or variant id
            if ($product_variation_id) {
                $product_variation_data = $_product->get_variation_attributes();
            }

            $product_variation_data['Offer ID'] = $offer->ID;

            $product_meta['woocommerce_offer_id'] = $offer->ID;
            $product_meta['woocommerce_offer_quantity'] = $offer_meta['offer_quantity'][0];
            $product_meta['woocommerce_offer_price_per'] = $offer_meta['offer_price_per'][0];

            $found = false;
           
            foreach ($woocommerce->cart->get_cart() as $cart_item) {
                // check if offer id already in cart
                if (isset($cart_item['woocommerce_offer_id']) && $cart_item['woocommerce_offer_id'] == $offer->ID) {
                    $found = true;
                    $message = sprintf(
                            '<a href="%s" class="button wc-forward">%s</a> %s', $woocommerce->cart->get_cart_url(), __('View Cart', 'offers-for-woocommerce'), __('Offer already added to cart', 'offers-for-woocommerce'));
                    $this->send_api_response($message);
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
            wp_safe_redirect($woocommerce->cart->get_cart_url());
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

        // add the email class to the list of email classes that WooCommerce loads
        $email_classes['WC_Accepted_Offer_Email'] = new WC_Accepted_Offer_Email();
        $email_classes['WC_Declined_Offer_Email'] = new WC_Declined_Offer_Email();
        $email_classes['WC_Countered_Offer_Email'] = new WC_Countered_Offer_Email();

        return $email_classes;
    }

    /**
     * Action - woocommerce_checkout_order_processed
     * Adds offer postmeta  'offer_order_id'
     * @since   0.1.0
     */
    public function ae_ofwc_woocommerce_checkout_order_processed($order_id) {
        global $woocommerce;

        // Get Order
        $order = new WC_Order($order_id);
        // Get order items
        $order_items = $order->get_items();

        // Check for offer id
        foreach ($order_items as $key => $value) {
            $item_offer_id = $order->get_item_meta($key, 'Offer ID', true);

            /**
             * Update offer
             * Add postmeta value 'offer_order_id' for this order id
             * Set offer post status to 'completed-offer'
             */
            if ($item_offer_id) {
                // Update offer post args
                $offer_data = array();
                $offer_data['ID'] = $item_offer_id;
                $offer_data['post_status'] = 'completed-offer';

                // Update offer
                $offer_id = wp_update_post($offer_data);

                // Check for offer post id
                if ($offer_id != 0) {
                    // Add 'offer_order_id' postmeta to offer post
                    add_post_meta($item_offer_id, 'offer_order_id', $order_id, true);

                    // Insert WP comment on related 'offer'
                    $comment_text = "<span>" . __('Updated - Status:', 'offers-for-woocommerce') . "</span> " . __('Completed', 'offers-for-woocommerce');
                    $comment_text.= '<p>' . __('Related Order', 'offers-for-woocommerce') . ': ' . '<a href="post.php?post=' . $order_id . '&action=edit">#' . $order_id . '</a></p>';

                    $comment_data = array(
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
                    $new_comment_id = wp_insert_comment($comment_data);

                    // insert comment meta
                    if ($new_comment_id) {
                        add_comment_meta($new_comment_id, 'angelleye_woocommerce_offer_id', $item_offer_id, true);
                        add_comment_meta($new_comment_id, 'offer_status', '4', true);
                    }
                }
            }
        }
    }

    /**
     * Filter - ae_paypal_standard_additional_parameters
     * @since   0.1.0
     */
    public function ae_paypal_standard_additional_parameters($paypal_args) {
        $paypal_args['bn'] = 'AngellEYE_SP_WooCommerce';
        return $paypal_args;
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
                        $request_error = true;
                        $this->send_api_response(__('Invalid Offer Status or Expired Offer Id; See shop manager for assistance', 'offers-for-woocommerce'), '0');
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

        if ('true' == $response[1])
            return true;
        else
            return false;
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
            $post_id = $_POST["targetID"];
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
            $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? $_POST['angelleye_woocommerce_offer_status_notes'] : '';
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
            $product_total = number_format(round($product_qty * $product_price_per, 2), 2, '.', '');
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
                    $identifier = '#' . $product->variation_id;
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
        $offer_price = get_post_meta($offer_id, 'offer_price_per', true);
        $user_offer_percentage = $this->ofwc_get_percentage($offer_price, $product_price);
        $product = ( $variant_id ) ? wc_get_product($variant_id) : wc_get_product($product_id);
        $product_url = $product->get_permalink();
        $offer_uid = get_post_meta($offer_id, 'offer_uid', true);
        
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
        $percentage = number_format($count_multi, 2);
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
                <input name="join_our_mailing_list" id="join_our_mailing_list" type="checkbox" value="yes" checked="checked"><label for="join_our_mailing_list" class="checkbox"><?php echo str_repeat('&nbsp;', 1); ?> <?php echo apply_filters('aeofwc-offer-form-label-join-our-mailing-list', __('Join Our Mailing List', 'offers-for-woocommerce')); ?></label>
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
        $button_options_general = get_option('offers_for_woocommerce_options_general');
        if(!WC()->cart->is_empty() && (isset($button_options_general['general_setting_disable_coupon']) && $button_options_general['general_setting_disable_coupon'] != '')) {
            foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                if( isset($values['woocommerce_offer_id']) && !empty($values['woocommerce_offer_id'])) {
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
        $count = 0;$has_product = FALSE;
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
            $total_result = $wpdb->get_results($wpdb->prepare("
                    SELECT MAX( postmeta.meta_value ) AS max_offer, COUNT(posts.ID) as total_qty
                    FROM $wpdb->postmeta AS postmeta
                    JOIN $wpdb->postmeta pm2 ON pm2.post_id = postmeta.post_id
                    INNER JOIN $wpdb->posts AS posts ON ( posts.post_type = 'woocommerce_offer' AND posts.post_status NOT LIKE 'completed-offer')
                    WHERE postmeta.meta_key LIKE 'offer_price_per' AND pm2.meta_key LIKE 'offer_product_id' AND pm2.meta_value LIKE %d
                    AND postmeta.post_id = posts.ID LIMIT 0, 99
            ", $post->ID), ARRAY_A);
            $total_qty = (isset($total_result[0]['total_qty']) && !empty($total_result[0]['total_qty'])) ? $total_result[0]['total_qty'] : 0;
            $max_offer = (isset($total_result[0]['max_offer']) && !empty($total_result[0]['max_offer'])) ? $total_result[0]['max_offer'] : 0;
            if($total_qty > 0 && $max_offer > 0) {
                echo '<div class="ofw-info"> ' . sprintf( _n( 'Highest Current Offer: %s%s', 'Highest Current Offer: %s%s', get_woocommerce_currency_symbol(), wc_format_decimal( $max_offer, wc_get_price_decimals() ), 'offers-for-woocommerce' ), get_woocommerce_currency_symbol(), wc_format_decimal( $max_offer, wc_get_price_decimals() ) ) . '</div>';
            }
        }
    }
    
    public function ofw_display_highest_current_offer_shortcode() {
        global $post, $wpdb;
        $total_result = $wpdb->get_results($wpdb->prepare("
                SELECT MAX( postmeta.meta_value ) AS max_offer, COUNT(posts.ID) as total_qty
                FROM $wpdb->postmeta AS postmeta
                JOIN $wpdb->postmeta pm2 ON pm2.post_id = postmeta.post_id
                INNER JOIN $wpdb->posts AS posts ON ( posts.post_type = 'woocommerce_offer' AND posts.post_status NOT LIKE 'completed-offer')
                WHERE postmeta.meta_key LIKE 'offer_price_per' AND pm2.meta_key LIKE 'offer_product_id' AND pm2.meta_value LIKE %d
                AND postmeta.post_id = posts.ID LIMIT 0, 99
        ", $post->ID), ARRAY_A);
        $total_qty = (isset($total_result[0]['total_qty']) && !empty($total_result[0]['total_qty'])) ? $total_result[0]['total_qty'] : 0;
        $max_offer = (isset($total_result[0]['max_offer']) && !empty($total_result[0]['max_offer'])) ? $total_result[0]['max_offer'] : 0;
        if($total_qty > 0 && $max_offer > 0) {
            echo '<div class="ofw-info"> ' . sprintf( _n( 'Highest Current Offer: %s%s', 'Highest Current Offer: %s%s', get_woocommerce_currency_symbol(), wc_format_decimal( $max_offer, wc_get_price_decimals() ), 'offers-for-woocommerce' ), get_woocommerce_currency_symbol(), wc_format_decimal( $max_offer, wc_get_price_decimals() ) ) . '</div>';
        }
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
}
