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
	const VERSION = '1.1.4';

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
	private function __construct()
	{
        /**
         * Define email templates path
         */
        define( 'OFWC_PUBLIC_EMAIL_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/emails/' );
        if (!defined('OFWC_EMAIL_TEMPLATE_PATH')) {
            define( 'OFWC_EMAIL_TEMPLATE_PATH', untrailingslashit( OFW_PLUGIN_URL ) . '/admin/includes/emails/' );
        }
        

		/**
		 * Activate plugin when new blog is added
		 */
		add_action('wpmu_new_blog', array( $this, 'activate_new_site' ) );
		
		/**
		 * Load public-facing style sheet and javascript
		 */
		add_action('wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/*******************************
		 * Define custom functionality *
		 *******************************
		 */							

		/**
		 * Init - New Offer Form Submit
		 * @since	0.1.0
		 */
		add_action( 'init', array( $this, 'new_offer_form_submit' ) );
		 
		/* Add "Make Offer" button code parts - Before add to cart */
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'angelleye_ofwc_before_add_to_cart_button' ) );

        /* Add "Make Offer" button code parts - After add to cart */
        add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'angelleye_ofwc_after_add_to_cart_button' ) );

        /* Add "Make Offer" button code parts - After shop loop item */
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'angelleye_ofwc_after_show_loop_item' ), 99, 2 );

        /* Add "Lighbox Make Offer Form" before single product content */
        add_action( 'woocommerce_before_single_product', array( $this, 'angelleye_ofwc_lightbox_make_offer_form') );
		
		/* Add "Make Offer" product tab on product single view */
		add_filter( 'woocommerce_product_tabs', array( $this, 'angelleye_ofwc_add_custom_woocommerce_product_tab' ) );

        /* Add query vars for api endpoint
         * Used for add offer to cart
         * @since   0.1.0
         */
        add_filter('query_vars', array( $this, 'add_query_vars' ), 0 );

        /* Add api endpoint listener
         * Used for add offer to cart
         * @since   0.1.0
         */
        add_action('parse_request', array( $this, 'sniff_api_requests' ), 0 );

        /**
         * Sets qty and price on any offer items in cart
         * @param $cart_object
         * @since   0.1.0
         */
        add_action( 'woocommerce_before_calculate_totals', array( $this, 'my_woocommerce_before_calculate_totals' ) );

        /*
         * Filter - get_cart_items_from_session
         * @since   0.1.0
         */
        add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_items_from_session' ), 1, 3 );

        /*
         * Filter - Add email class to WooCommerce for 'Accepted Offer'
         * @since   0.1.0
         */
        add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_email_classes' ) );

        /**
         * Action - woocommerce_checkout_order_processed
         * @since   0.1.0
         */
        add_action( 'woocommerce_checkout_order_processed', array( $this, 'ae_ofwc_woocommerce_checkout_order_processed' ) );

        /**
         * Filter - ae_paypal_standard_additional_parameters
         * @since   0.1.0
         */
        add_filter( 'woocommerce_paypal_args', array($this,'ae_paypal_standard_additional_parameters'));

        /**
         * Action - woocommerce_before_checkout_process
         * Checks for valid offer before checkout process
         * @since   0.1.0
         */
        add_action( 'woocommerce_before_checkout_process', array( $this, 'ae_ofwc_woocommerce_before_checkout_process' ) );
        
        
        add_action('auto_accept_auto_decline_handler', array( $this, 'ofwc_auto_accept_auto_decline_handler' ), 10, 4 );
       
    }

	/**
	 * Add extra div wrap before add to cart button
	 *
	 * @since	0.1.0
	 */
	public function angelleye_ofwc_before_add_to_cart_button()
	{
		global $post;
        global $current_user;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

        // enable offers for only logged in users
        if($button_options_general && isset($button_options_general['general_setting_enable_offers_only_logged_in_users']) && $button_options_general['general_setting_enable_offers_only_logged_in_users'] != '') {
            if ( !is_user_logged_in() ) return;
        }

        // enable offers for only certain user roles
        if(!empty($button_options_general['general_setting_allowed_roles']))
        {
            if ( is_user_logged_in() ) {
                $user_data = get_userdata($current_user->ID);
                $user_roles = $user_data->roles;
                $role_match = array_intersect($user_roles, $button_options_general['general_setting_allowed_roles'] );
                if( empty($role_match) ) return;
            }
            else
            {
                return;
            }
        }

		$custom_tab_options_offers = array(
			'enabled' => get_post_meta( $post->ID, 'offers_for_woocommerce_enabled', true ),
		);

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
        $is_external_product = ( isset( $_product->product_type ) && $_product->product_type == 'external' ) ? TRUE : FALSE;
        $is_instock = ( $_product->is_in_stock() ) ? TRUE : FALSE;

        $button_title = (isset($button_options_display['display_setting_custom_make_offer_btn_text']) && $button_options_display['display_setting_custom_make_offer_btn_text'] != '') ? $button_options_display['display_setting_custom_make_offer_btn_text'] : __('Make Offer', $this->plugin_slug);

        $custom_styles_override = '';
        if ($button_options_display) {
            if (isset($button_options_display['display_setting_custom_make_offer_btn_text_color']) && $button_options_display['display_setting_custom_make_offer_btn_text_color'] != '') {
                $custom_styles_override .= 'color:' . $button_options_display['display_setting_custom_make_offer_btn_text_color'] . '!important;';
            }
            if (isset($button_options_display['display_setting_custom_make_offer_btn_color']) && $button_options_display['display_setting_custom_make_offer_btn_color'] != '') {
                $custom_styles_override .= ' background:' . $button_options_display['display_setting_custom_make_offer_btn_color'] . '!important; border-color:' . $button_options_display['display_setting_custom_make_offer_btn_color'] . '!important;';
            }
        }

        // if post has offers button enabled
        if ( $custom_tab_options_offers['enabled'] == 'yes' && !$is_external_product && $is_instock && ($_product->get_price() > 0))
        {
            // get global on/off settings for offer button
            $button_global_onoff_frontpage = ($button_options_general && isset($button_options_general['general_setting_enable_make_offer_btn_frontpage']) && $button_options_general['general_setting_enable_make_offer_btn_frontpage'] != '') ? true : false;
            $button_global_onoff_catalog = ($button_options_general && isset($button_options_general['general_setting_enable_make_offer_btn_catalog']) && $button_options_general['general_setting_enable_make_offer_btn_catalog'] != '') ? true : false;

            if( (is_front_page() && !$button_global_onoff_frontpage) || (!is_front_page() && !is_product() && !$button_global_onoff_catalog) )
            {
                //
            }
            else
            {
                // adds hidden class if position is not default
                $hiddenclass = ( isset($button_options_display['display_setting_make_offer_button_position_single']) && $button_options_display['display_setting_make_offer_button_position_single'] != 'default' && $button_options_display['display_setting_make_offer_button_position_single'] != 'right_of_add') ? 'angelleye-ofwc-hidden' : '';
                $customclass = ( $hiddenclass == 'angelleye-ofwc-hidden' ) ? $button_options_display['display_setting_make_offer_button_position_single'] : '';

                $display_right_of_add = ( !empty($button_options_display['display_setting_make_offer_button_position_single']) && $button_options_display['display_setting_make_offer_button_position_single'] == 'right_of_add' ) ? TRUE : FALSE;
                $display_right_of_add_class = ($display_right_of_add) ? 'ofwc-button-right-of-add-to-cart' : '';

                if($display_right_of_add)
                {
                    echo '<div class="offers-for-woocommerce-make-offer-button-cleared '.$hiddenclass.'"></div>';
                }
                echo '<div id="offers-for-woocommerce-add-to-cart-wrap" class="offers-for-woocommerce-add-to-cart-wrap '.$display_right_of_add_class.'" data-ofwc-position="'.$customclass.'"><div>';
            }
		}
	}

    /**
     * Add Make Offer button after add to cart button
     *
     * @since	0.1.0
     */
    public function angelleye_ofwc_after_add_to_cart_button()
    {
        global $post;
        global $current_user;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

        // enable offers for only logged in users
        if($button_options_general && isset($button_options_general['general_setting_enable_offers_only_logged_in_users']) && $button_options_general['general_setting_enable_offers_only_logged_in_users'] != '') {
            if ( !is_user_logged_in() ) return;
        }

        // enable offers for only certain user roles
        if(!empty($button_options_general['general_setting_allowed_roles']))
        {
            if ( is_user_logged_in() ) {
                $user_data = get_userdata($current_user->ID);
                $user_roles = $user_data->roles;
                $role_match = array_intersect($user_roles, $button_options_general['general_setting_allowed_roles'] );
                if( empty($role_match) ) return;
            }
            else
            {
                return;
            }
        }

        $custom_tab_options_offers = array(
            'enabled' => get_post_meta( $post->ID, 'offers_for_woocommerce_enabled', true ),
        );

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
        $is_external_product = ( isset( $_product->product_type ) && $_product->product_type == 'external' ) ? TRUE : FALSE;
        $is_instock = ( $_product->is_in_stock() ) ? TRUE : FALSE;

        // if post has offers button enabled
        if ( $custom_tab_options_offers['enabled'] == 'yes' && !$is_external_product && $is_instock && ($_product->get_price() > 0))
        {
            $button_title = (isset($button_options_display['display_setting_custom_make_offer_btn_text']) && $button_options_display['display_setting_custom_make_offer_btn_text'] != '') ? $button_options_display['display_setting_custom_make_offer_btn_text'] : __('Make Offer', $this->plugin_slug);

            $custom_styles_override = '';
            if ($button_options_display) {
                if (isset($button_options_display['display_setting_custom_make_offer_btn_text_color']) && $button_options_display['display_setting_custom_make_offer_btn_text_color'] != '') {
                    $custom_styles_override .= 'color:' . $button_options_display['display_setting_custom_make_offer_btn_text_color'] . '!important;';
                }
                if (isset($button_options_display['display_setting_custom_make_offer_btn_color']) && $button_options_display['display_setting_custom_make_offer_btn_color'] != '') {
                    $custom_styles_override .= ' background:' . $button_options_display['display_setting_custom_make_offer_btn_color'] . '!important; border-color:' . $button_options_display['display_setting_custom_make_offer_btn_color'] . '!important;';
                }
            }

            if( is_front_page() || (!is_front_page() && !is_product()) )
            {
                //
            }
            else
            {
                // adds hidden class if position is not default
                $hiddenclass = ( isset($button_options_display['display_setting_make_offer_button_position_single']) && $button_options_display['display_setting_make_offer_button_position_single'] != 'default' && $button_options_display['display_setting_make_offer_button_position_single'] != 'right_of_add') ? 'angelleye-ofwc-hidden' : '';
                $lightbox_class = (isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') ? ' offers-for-woocommerce-make-offer-button-single-product-lightbox' : '';
                $display_right_of_add = ( !empty($button_options_display['display_setting_make_offer_button_position_single']) && $button_options_display['display_setting_make_offer_button_position_single'] == 'right_of_add' ) ? TRUE : FALSE;

                if(!$display_right_of_add)
                {
                    echo '<div class="angelleye-offers-clearfix '.$hiddenclass.'"></div></div>';
                }
                else
                {
                    echo '</div>';
                }
                echo '<div class="single_variation_wrap_angelleye ofwc_offer_tab_form_wrap '.$hiddenclass.'"><button type="button" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-single-product ' . $lightbox_class . ' button alt" style="' . $custom_styles_override . '">' . $button_title . '</button>';
                echo '<div class="angelleye-offers-clearfix"></div></div></div>';

            }
        }
    }

	/**
	 * Callback - Add Make Offer button after add to cart button on Catalog view
	 *
	 * @since	0.1.0
	 */
	public function angelleye_ofwc_after_show_loop_item($post)
	{
        global $post;
        global $current_user;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

        // enable offers for only logged in users
        if($button_options_general && isset($button_options_general['general_setting_enable_offers_only_logged_in_users']) && $button_options_general['general_setting_enable_offers_only_logged_in_users'] != '') {
            if ( !is_user_logged_in() ) return;
        }

        // enable offers for only certain user roles
        if(!empty($button_options_general['general_setting_allowed_roles']))
        {
            if ( is_user_logged_in() ) {
                $user_data = get_userdata($current_user->ID);
                $user_roles = $user_data->roles;
                $role_match = array_intersect($user_roles, $button_options_general['general_setting_allowed_roles'] );
                if( empty($role_match) ) return;
            }
            else
            {
                return;
            }
        }

		$custom_tab_options_offers = array(
			'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),
		);

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
        $is_external_product = ( isset( $_product->product_type ) && $_product->product_type == 'external' ) ? TRUE : FALSE;
        $is_instock = ( $_product->is_in_stock() ) ? TRUE : FALSE;

        // if post has offers button enabled
        if ( $custom_tab_options_offers['enabled'] == 'yes' && !$is_external_product && $is_instock && ($_product->get_price() > 0))
        {
            // get global on/off settings for offer button - frontpage and catalog
            $button_global_onoff_frontpage = ($button_options_general && isset($button_options_general['general_setting_enable_make_offer_btn_frontpage']) && $button_options_general['general_setting_enable_make_offer_btn_frontpage'] != '') ? true : false;
            $button_global_onoff_catalog = ($button_options_general && isset($button_options_general['general_setting_enable_make_offer_btn_catalog']) && $button_options_general['general_setting_enable_make_offer_btn_catalog'] != '') ? true : false;

            if( (is_front_page() && !$button_global_onoff_frontpage) || (!is_front_page() && !$button_global_onoff_catalog) )
            {
                return;
            }
            else
            {
                $button_title = (isset($button_options_display['display_setting_custom_make_offer_btn_text']) && $button_options_display['display_setting_custom_make_offer_btn_text'] != '') ? $button_options_display['display_setting_custom_make_offer_btn_text'] : __( 'Make Offer', $this->plugin_slug );

                $custom_styles_override = 'style="';
                if(isset($button_options_display['display_setting_custom_make_offer_btn_text_color']) && $button_options_display['display_setting_custom_make_offer_btn_text_color'] != '')
                {
                    $custom_styles_override.= 'color:'.$button_options_display['display_setting_custom_make_offer_btn_text_color'].'!important;';
                }
                if(isset($button_options_display['display_setting_custom_make_offer_btn_color']) && $button_options_display['display_setting_custom_make_offer_btn_color'] != '')
                {
                    $custom_styles_override.= ' background:'.$button_options_display['display_setting_custom_make_offer_btn_color'].'!important; border-color:'.$button_options_display['display_setting_custom_make_offer_btn_color'].'!important;';
                }
                $custom_styles_override.= '"';

                $permalink = get_permalink($post->ID);
                $permalink.= (strpos($permalink, '?') !== false) ? '&aewcobtn=1' : '?aewcobtn=1';
                echo '<a href="'.$permalink.'" id="offers-for-woocommerce-make-offer-button-id-'.$post->ID.'" class="offers-for-woocommerce-make-offer-button-catalog button alt" '.$custom_styles_override.'>'.$button_title.'</a>';
            }
		}
	}

    /**
     * Action - Add lightbox make offer form
     *
     * @since   0.1.0
     */
    public function angelleye_ofwc_lightbox_make_offer_form()
    {
        global $current_user;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

        // enable offers for only logged in users
        if($button_options_general && isset($button_options_general['general_setting_enable_offers_only_logged_in_users']) && $button_options_general['general_setting_enable_offers_only_logged_in_users'] != '') {
            if ( !is_user_logged_in() ) return;
        }

        // enable offers for only certain user roles
        if(!empty($button_options_general['general_setting_allowed_roles']))
        {
            if ( is_user_logged_in() ) {
                $user_data = get_userdata($current_user->ID);
                $user_roles = $user_data->roles;
                $role_match = array_intersect($user_roles, $button_options_general['general_setting_allowed_roles'] );
                if( empty($role_match) ) return;
            }
            else
            {
                return;
            }
        }

        $is_lightbox = ( isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') ? TRUE : FALSE;
        if($is_lightbox)
        {
            echo '<div id="lightbox_custom_ofwc_offer_form">';
            $this->angelleye_ofwc_display_custom_woocommerce_product_tab_content();
            echo '</div>';
            echo '<div id="lightbox_custom_ofwc_offer_form_close_btn"></div>';
        }
    }
	
	/**
	 * Filter - Add new tab on woocommerce product single view
	 *
	 * @since	0.1.0
	 */
	public function angelleye_ofwc_add_custom_woocommerce_product_tab($tabs)
	{
        global $post;
        global $current_user;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

        // enable offers for only logged in users
        if($button_options_general && isset($button_options_general['general_setting_enable_offers_only_logged_in_users']) && $button_options_general['general_setting_enable_offers_only_logged_in_users'] != '') {
            if ( !is_user_logged_in() ) return $tabs;
        }

        // enable offers for only certain user roles
        if(!empty($button_options_general['general_setting_allowed_roles']))
        {
            if ( is_user_logged_in() ) {
                $user_data = get_userdata($current_user->ID);
                $user_roles = $user_data->roles;
                $role_match = array_intersect($user_roles, $button_options_general['general_setting_allowed_roles'] );
                if( empty($role_match) ) return $tabs;
            }
            else
            {
                return $tabs;
            }
        }

        $custom_tab_options_offers = array(
            'enabled' => get_post_meta( $post->ID, 'offers_for_woocommerce_enabled', true ),
        );

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
        $is_external_product = ( isset( $_product->product_type ) && $_product->product_type == 'external' ) ? TRUE : FALSE;
        $is_instock = ( $_product->is_in_stock() ) ? TRUE : FALSE;

        // if post has offers button enabled
        if ( $custom_tab_options_offers['enabled'] == 'yes' && !$is_external_product && $is_instock && ($_product->get_price() > 0))
        {
            if( isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox')
            {
                return $tabs;
            }

            $tab_title = (isset($button_options_display['display_setting_custom_make_offer_btn_text']) && $button_options_display['display_setting_custom_make_offer_btn_text'] != '') ? $button_options_display['display_setting_custom_make_offer_btn_text'] : __( 'Make Offer', $this->plugin_slug );

            // Add new tab "Make Offer"
            $tabs['tab_custom_ofwc_offer'] = array(
                'title' => $tab_title,
                'priority' => 50,
                'callback' => array( $this, 'angelleye_ofwc_display_custom_woocommerce_product_tab_content' ) );

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
	public function angelleye_ofwc_display_custom_woocommerce_product_tab_content()
	{
        global $post;
        global $current_user;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // enable offers for only logged in users
        if($button_options_general && isset($button_options_general['general_setting_enable_offers_only_logged_in_users']) && $button_options_general['general_setting_enable_offers_only_logged_in_users'] != '') {
            if ( !is_user_logged_in() ) return;
        }

        // enable offers for only certain user roles
        if(!empty($button_options_general['general_setting_allowed_roles']))
        {
            if ( is_user_logged_in() ) {
                $user_data = get_userdata($current_user->ID);
                $user_roles = $user_data->roles;
                $role_match = array_intersect($user_roles, $button_options_general['general_setting_allowed_roles'] );
                if( empty($role_match) ) return;
            }
            else
            {
                return;
            }
        }

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
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
        if($parent_offer_id != '')
        {
            $parent_post_status = get_post_status($parent_offer_id);
            $post_parent_type = get_post_type($parent_offer_id);
            $parent_post_offer_uid = get_post_meta($parent_offer_id, 'offer_uid', true);

            $final_offer = get_post_meta($parent_offer_id, 'offer_final_offer', true );
            $expiration_date = get_post_meta($parent_offer_id, 'offer_expiration_date', true );
            $expiration_date_formatted = ($expiration_date) ? date("Y-m-d 23:59:59", strtotime($expiration_date)) : FALSE;

            // check for valid parent offer ( must be a offer post type and accepted/countered and uid must match
            if( (isset($parent_post_status) && $parent_post_status != 'countered-offer') || ($post_parent_type != 'woocommerce_offer') || (!$parent_post_offer_uid) || ($parent_offer_uid == '') || ($parent_post_offer_uid != $parent_offer_uid) )
            {
                // If buyer already submitted 'buyer counter'
                if( $parent_post_status == 'buyercountered-offer' )
                {
                    $parent_offer_id = '';
                    $parent_offer_error = true;
                    $parent_offer_error_message = __('You can not submit another counter offer at this time; Counter offer is currently being reviewed. You can submit a new offer using the form below.', $this->plugin_slug);
                }
                else
                {
                    $parent_offer_id = '';
                    $parent_offer_error = true;
                    $parent_offer_error_message = __('Invalid Parent Offer Id; See shop manager for assistance.', $this->plugin_slug);
                }
            }
            // If offer counter was set to 'final offer'
            elseif( $final_offer == '1' )
            {
                $parent_offer_id = '';
                $parent_offer_error = true;
                $parent_offer_error_message = __('You can not submit a counter offer at this time; Counter offer is a final offer. You can submit a new offer using the form below.', $this->plugin_slug);
            }

            // If offer counter 'offer_expiration_date' is past
            elseif( ($expiration_date_formatted) && ($expiration_date_formatted <= (date("Y-m-d H:i:s", current_time('timestamp', 0 ))) ) )
            {
                $parent_offer_id = '';
                $parent_offer_error = true;
                $parent_offer_error_message = __('Counter offer has expired; You can not submit a counter offer at this time. You can submit a new offer using the form below.', $this->plugin_slug);
            }
            else
            {
                // lookup original offer data to display buyer info
                $offer_name = get_post_meta($parent_offer_id, 'offer_name', true );
                $offer_company_name = get_post_meta($parent_offer_id, 'offer_company_name', true );
                $offer_phone = get_post_meta($parent_offer_id, 'offer_phone', true );
                $offer_email = get_post_meta($parent_offer_id, 'offer_email', true );
            }
        }

        // If name,phone,email not already specified, then try to pull logged in user data if user is logged in
        if(empty($offer_name))
        {
            if( is_user_logged_in() )
            {
                $current_user = wp_get_current_user();

                $offer_email = $current_user->user_email;
                $offer_company_name = (!empty($current_user->billing_company)) ? $current_user->billing_company : '';
                $offer_phone = (!empty($current_user->billing_phone)) ? $current_user->billing_phone : '';

                // use WP user first/last name if available
                if(!empty($current_user->user_firstname))
                {
                    $offer_name = $current_user->user_firstname;
                    $offer_name.= (!empty($current_user->user_lastname)) ? ' ' . $current_user->user_lastname : '';
                }
                else // use WP/WC billing first/last name
                {
                    if(!empty($current_user->billing_first_name))
                    {
                        $offer_name = $current_user->billing_first_name;
                        $offer_name.= (!empty($current_user->billing_last_name)) ? ' ' . $current_user->billing_last_name : '';
                    }
                }
            }
        }

        // get options for button display
        $button_display_options = get_option('offers_for_woocommerce_options_display');

        $currency_symbol = get_woocommerce_currency_symbol();

		// Set html content for output
		include_once( 'views/public.php' );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    0.1.0
	 *
	 * @return    Plugin slug variable
	 */
	public function get_plugin_slug()
	{
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class
	 *
	 * @since    0.1.0
	 *
	 * @return    object    A single instance of this class
	 */
	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now
		if ( null == self::$instance ) {
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
	public static function activate( $network_wide )
	{
		if ( function_exists( 'is_multisite' ) && is_multisite())
		{
			if ( $network_wide )
			{
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ($blog_ids as $blog_id)
				{
					switch_to_blog($blog_id);
					self::single_activate();
				}

				restore_current_blog();
			}
			else
			{
				self::single_activate();
			}
		}
		else
		{
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
	public static function deactivate($network_wide)
	{
		if ( function_exists( 'is_multisite' ) && is_multisite())
		{
			if ($network_wide)
			{
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ($blog_ids as $blog_id)
				{
					switch_to_blog($blog_id);
					self::single_deactivate();
				}

				restore_current_blog();
			}
			else
			{
				self::single_deactivate();
			}
		}
		else
		{
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
	public function activate_new_site($blog_id)
	{
		if (1 !== did_action('wpmu_new_blog'))
		{
			return;
		}

		switch_to_blog( $blog_id );
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
	private static function get_blog_ids()
	{
		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";
		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated
	 *
	 * @since    0.1.0
	 */
	private static function single_activate()
	{
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated
	 *
	 * @since    0.1.0
	 */
	private static function single_deactivate()
	{
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Register and enqueue public-facing style sheet
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_enqueue_script( $this->plugin_slug . '-plugin-script-jquery-auto-numeric-1-9-24', plugins_url( 'assets/js/autoNumeric-1-9-24.js', __FILE__ ), self::VERSION);
	}

	public function new_offer_form_submit()
	{
		if(!is_admin())
		{
            global $wpdb; // this is how you get access to the database

			// Check if form was posted and select task accordingly
            if(isset($_REQUEST['woocommerceoffer_post']) && isset($_POST["offer_product_id"]) && $_POST["offer_product_id"] != '')
            {
				// set postmeta original vars
                $formData['orig_offer_name'] = (isset($_POST['offer_name'])) ? $_POST['offer_name'] : '';
                $formData['orig_offer_company_name'] = (isset($_POST['offer_company_name'])) ? $_POST['offer_company_name'] : '';
                $formData['orig_offer_phone'] = (isset($_POST['offer_phone'])) ? $_POST['offer_phone'] : '';
                $formData['orig_offer_email'] = (isset($_POST['offer_email'])) ? $_POST['offer_email'] : '';
                $formData['orig_offer_product_id'] = (isset($_POST['offer_product_id'])) ? $_POST['offer_product_id'] : '';
                $formData['orig_offer_variation_id'] = (isset($_POST['offer_variation_id'])) ? $_POST['offer_variation_id'] : '';
				$formData['orig_offer_quantity'] = (isset($_POST['offer_quantity'])) ? $_POST['offer_quantity'] : '0';
                $formData['orig_offer_price_per'] = (isset($_POST['offer_price_each'])) ? $_POST['offer_price_each'] : '0';
				$formData['orig_offer_amount'] = number_format(round($formData['orig_offer_quantity'] * $formData['orig_offer_price_per']), 2, ".", "");
                $formData['orig_offer_uid'] = uniqid('aewco-');;
                $formData['parent_offer_uid'] = (isset($_POST['parent_offer_uid'])) ? $_POST['parent_offer_uid'] : '';

                /**
                 * Check minimum quantity and minimum price
                 */
                // check for valid offer quantity (not zero)
                if( ($formData['orig_offer_quantity'] == '' || $formData['orig_offer_quantity'] == 0) )
                {
                    echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __( 'Please enter a positive value for \'Offer Quantity\'', $this->plugin_slug ) ));
                    exit;
                }
                // check for valid offer price (not zero)
                if( ($formData['orig_offer_price_per'] == '' || $formData['orig_offer_price_per'] == 0 || $formData['orig_offer_price_per'] == "0.00") )
                {
                    echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __( 'Please enter a positive value for \'Offer Amount\'', $this->plugin_slug ) ));
                    exit;
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
                $author_data = ( !is_user_logged_in() ) ? get_user_by( 'email', $formData['offer_email'] ) : false;

                if($author_data)
                {
                    $newPostData['post_author'] = $author_data->ID;
                }

				// set post vars
                $newPostData['post_date'] = date("Y-m-d H:i:s", current_time('timestamp', 0 ) );
				$newPostData['post_date_gmt'] = gmdate("Y-m-d H:i:s", time());
                $newPostData['post_type'] = 'woocommerce_offer';
                $newPostData['post_status'] = 'publish';
                $newPostData['post_title'] = $formData['offer_email'];

                // set offer comments
                $comments = (isset($_POST['offer_notes']) && $_POST['offer_notes'] != '') ? strip_tags(nl2br($_POST['offer_notes']), '<br><p>') : '';

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
                if($this->aeofwc_akismet_comment_check( $akismet_api_key, $data ) && FALSE)
                {
                    // is spam
                    echo json_encode(array("statusmsg" => 'failed-spam', "statusmsgDetail" => __( 'Invalid Offer Submission; See shop manager for assistance', $this->plugin_slug ) ));
                    exit;
                }

                // check for parent post id
                $parent_post_id = (isset($_POST['parent_offer_id'])) ? $_POST['parent_offer_id'] : '';
                $parent_post_status = get_post_status($parent_post_id);
                $post_parent_type = get_post_type($parent_post_id);

                // If has valid parent offer id post
                $is_counter_offer = ( $parent_post_id != '' ) ? true : false;

                if($is_counter_offer)
                {
                    // check for parent offer unique id
                    $parent_post_offer_uid = get_post_meta($parent_post_id, 'offer_uid', true);

                    // check for valid parent offer ( must be a offer post type and accepted/countered and uid must match
                    if( (isset($parent_post_status) && $parent_post_status != 'countered-offer') || ($post_parent_type != 'woocommerce_offer') || ($parent_post_offer_uid != $formData['parent_offer_uid']) )
                    {
                        echo json_encode(array("statusmsg" => 'failed-custom', "statusmsgDetail" => __( 'Invalid Parent Offer Id; See shop manager for assistance', $this->plugin_slug ) ));
                        exit;
                    }

                    $parent_post = array(
                        'ID'           => $parent_post_id,
                        'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                        'post_modified_gmt' => gmdate("Y-m-d H:i:s", current_time('timestamp', 0 )),
                        'post_status' => 'buyercountered-offer'
                    );

                    if($author_data)
                    {
                        $parent_post['post_author'] = $newPostData['post_author'];
                    }

                    // Update the parent post into the database
                    wp_update_post( $parent_post);

                    $formDataUpdated = array();

                    $formDataUpdated['offer_buyer_counter_quantity'] = $formData['offer_quantity'];
                    $formDataUpdated['offer_buyer_counter_price_per'] = $formData['offer_price_per'];
                    $formDataUpdated['offer_buyer_counter_amount'] = $formData['offer_amount'];

                    // Insert new Post Meta Values
                    foreach($formDataUpdated as $k => $v)
                    {
                        $newPostMetaData = array();
                        $newPostMetaData['post_id'] = $parent_post_id;
                        $newPostMetaData['meta_key'] = $k;
                        $newPostMetaData['meta_value'] = $v;

                        update_post_meta( $parent_post_id, $newPostMetaData['meta_key'], $newPostMetaData['meta_value']);
                    }

                    // Insert WP comment
                    $comment_text = "<span>" . __('Buyer Submitted Counter Offer', $this->plugin_slug) . "</span>";

                    if($comments != '')
                    {
                        // Insert WP comment
                        $comment_text.= '<br />' . $comments;
                    }

                    $data = array(
                        'comment_post_ID' => '',
                        'comment_author' => 'admin',
                        'comment_author_email' => '',
                        'comment_author_url' => '',
                        'comment_content' => $comment_text,
                        'comment_type' => '',
                        'comment_parent' => 0,
                        'user_id' => '',
                        'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                        'comment_agent' => '',
                        'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                        'comment_approved' => 'post-trashed',
                    );
                    $new_comment_id = wp_insert_comment( $data );

                    // insert comment meta
                    if( $new_comment_id )
                    {
                        add_comment_meta( $new_comment_id, 'angelleye_woocommerce_offer_id', $parent_post_id, true );
                    }
                }
                else
                {
                    // Insert new Post
                    $parent_post_id = wp_insert_post( $newPostData );
                    if( $parent_post_id )
                    {
                        // Insert new Post Meta Values
                        foreach($formData as $k => $v)
                        {
                            $newPostMetaData = array();
                            $newPostMetaData['post_id'] = $parent_post_id;
                            $newPostMetaData['meta_key'] = $k;
                            $newPostMetaData['meta_value'] = $v;
                            add_post_meta($newPostMetaData['post_id'], $newPostMetaData['meta_key'], $newPostMetaData['meta_value']);
                        }

                        // Insert WP comment
                        $comment_text = "<span>" . __('Created New Offer', $this->plugin_slug) . "</span>";

                        if($comments != '')
                        {
                            // Insert WP comment
                            $comment_text.= '<br />' . $comments;
                        }

                        $data = array(
                            'comment_post_ID' => '',
                            'comment_author' => 'admin',
                            'comment_author_email' => '',
                            'comment_author_url' => '',
                            'comment_content' => $comment_text,
                            'comment_type' => '',
                            'comment_parent' => 0,
                            'user_id' => 1,
                            'comment_author_IP' => '127.0.0.1',
                            'comment_agent' => '',
                            'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                            'comment_approved' => 'post-trashed',
                        );
                        $new_comment_id = wp_insert_comment( $data );

                        // insert comment meta
                        if( $new_comment_id )
                        {
                            add_comment_meta( $new_comment_id, 'angelleye_woocommerce_offer_id', $parent_post_id, true );
                        }
                    }
                    else
                    {
                        // return error msg
                        echo json_encode(array("statusmsg" => 'failed', "statusmsgDetail" => 'database error'));
                        exit;
                    }
                }

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
                $_pf = new WC_Product_Factory;
                $product = ( $variant_id ) ? $_pf->get_product( $variant_id ) : $_pf->get_product( $product_id );

                $product_qty = $formData['offer_quantity'];
                $product_price_per = $formData['offer_price_per'];
                $product_total = $formData['offer_amount'];

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
                    'product_total' => $product_total,
                    'offer_notes' => $comments
                );

                if( $variant_id )
                {
                    if ( $product->get_sku() ) {
                        $identifier = $product->get_sku();
                    } else {
                        $identifier = '#' . $product->variation_id;
                    }

                    $attributes = $product->get_variation_attributes();
                    $extra_data = ' &ndash; ' . implode( ', ', $attributes );
                    $offer_args['product_title_formatted'] = sprintf( __( '%s &ndash; %s%s', 'woocommerce' ), $identifier, $product->get_title(), $extra_data );
                }
                else
                {
                    if ( !empty($product) ) {
                        $identifier = $product->get_sku();
                    } else {
                        $identifier = '#' . $product_id;
                    }

                    $offer_args['product_title_formatted'] = sprintf( __( '%s &ndash; %s', 'woocommerce' ), $identifier, $product->get_title() );
                }

                if($is_counter_offer)
                {
                    $offer_args['is_counter_offer'] = true;

                    /**
                     * send admin 'New counter offer' email template
                     */
                    // the email we want to send
                    $email_class = 'WC_New_Counter_Offer_Email';
                }
                else
                {
                    $offer_args['is_counter_offer'] = false;

                    /**
                     * send admin 'New offer' email template
                     */
                    // the email we want to send
                    $email_class = 'WC_New_Offer_Email';
                }

                // load the WooCommerce Emails
                $wc_emails = new WC_Emails();
                $emails = $wc_emails->get_emails();

                // select the email we want & trigger it to send
                $new_email = $emails[$email_class];

                // set plugin slug in email class
                $new_email->plugin_slug = $this->plugin_slug;

                if($is_counter_offer)
                {
                    // define email template/path (html)
                    $new_email->template_html = 'woocommerce-new-counter-offer.php';
                    $new_email->template_html_path = plugin_dir_path(__FILE__) . 'includes/emails/';

                    // define email template/path (plain)
                    $new_email->template_plain = 'woocommerce-new-counter-offer.php';
                    $new_email->template_plain_path = plugin_dir_path(__FILE__) . 'includes/emails/plain/';
                }
                else
                {
                    // define email template/path (html)
                    $new_email->template_html = 'woocommerce-new-offer.php';
                    $new_email->template_html_path = plugin_dir_path(__FILE__) . 'includes/emails/';

                    // define email template/path (plain)
                    $new_email->template_plain = 'woocommerce-new-offer.php';
                    $new_email->template_plain_path = plugin_dir_path(__FILE__) . 'includes/emails/plain/';
                }

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
                $new_email->plugin_slug = $this->plugin_slug;

                // define email template/path (html)
                $new_email->template_html  = 'woocommerce-offer-received.php';
                $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

                // define email template/path (plain)
                $new_email->template_plain  = 'woocommerce-offer-received.php';
                $new_email->template_plain_path = plugin_dir_path(__FILE__). 'includes/emails/plain/';

                $new_email->trigger($offer_args);
                         
                do_action('auto_accept_auto_decline_handler', $offer_id, $product_id, $variant_id, $emails);

                // Success
                echo json_encode(array("statusmsg" => 'success'));
                exit;
            }
		}
	}

    /**
     * Add public query vars for API requests
     * @param array $vars List of current public query vars
     * @return array $vars
     */
    public function add_query_vars($vars){
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
    public function sniff_api_requests(){
        global $wp;
        if(isset($wp->query_vars['__aewcoapi'])){
            $this->handle_request();
        }
    }

    /** Handle API Requests
     * @return void
     */
    protected function handle_request(){
        global $wp;
        $request_error = false;
        $pid = (isset($wp->query_vars['woocommerce-offer-id'])) ? $wp->query_vars['woocommerce-offer-id'] : '' ;
        if($pid == '' || !is_numeric($pid))
        {
            $this->send_api_response( __( 'Missing or Invalid Offer Id; See shop manager for assistance', $this->plugin_slug ) );
        }
        else
        {
            /**
             * Lookup Offer
             * - Make sure valid 'accepted-offer' or 'countered-offer' status
             */
            $offer = get_post($pid);

            // check for parent offer unique id
            $offer_uid = get_post_meta( $offer->ID, 'orig_offer_uid', true);

            // check offer expiration date
            $expiration_date = get_post_meta($offer->ID, 'offer_expiration_date', true );
            $expiration_date_formatted = ($expiration_date) ? date("Y-m-d 23:59:59", strtotime($expiration_date)) : FALSE;

            // Invalid Offer Id
            if($offer == '')
            {
                $this->send_api_response( __( 'Invalid or Expired Offer Id; See shop manager for assistance', $this->plugin_slug ) );
            }
            // check for valid uid match
            elseif( ( $offer_uid != $wp->query_vars['woocommerce-offer-uid']) )
            {
                $this->send_api_response( __( 'Invalid Offer Status or Expired Offer Id; See shop manager for assistance', $this->plugin_slug ) );
            }
            // If offer counter 'offer_expiration_date' is past
            elseif( ($expiration_date_formatted) && ($expiration_date_formatted <= (date("Y-m-d H:i:s", current_time('timestamp', 0 ))) ) )
            {
                $request_error = true;
                $this->send_api_response( __( 'Offer has expired; You can submit a new offer using the form below.', $this->plugin_slug ) );
            }
            else
            {
                // Get offer meta
                $offer_meta = get_post_meta( $offer->ID, '', true );

                // Error - Offer On Hold
                if($offer->post_status == 'on-hold-offer')
                {
                    $request_error = true;
                    $this->send_api_response( __( 'Offer is currently On Hold; We will notify you when offer status is updated.', $this->plugin_slug ) );
                }
                // Error - Offer Not Accepted/Countered
                elseif($offer->post_status != 'accepted-offer' && $offer->post_status != 'countered-offer' && $offer->post_status != 'buyercountered-offer')
                {
                    $request_error = true;
                    $this->send_api_response( __( 'Invalid Offer Status or Expired Offer Id; See shop manager for assistance', $this->plugin_slug ) );
                }

                // Define product id
                $product_id = (isset($offer_meta['orig_offer_product_id'][0]) && is_numeric( $offer_meta['orig_offer_product_id'][0] ) ) ? $offer_meta['orig_offer_product_id'][0] : '';

                // Error - Missing Product Id on the offer meta
                if($product_id == '' || !is_numeric( $product_id ))
                {
                    $request_error = true;
                    $this->send_api_response( __( 'Error - Product Not Found; See shop manager for assistance', $this->plugin_slug ) );
                }

                // Lookup Product
                $product = new WC_Product($product_id);

                // Error - Invalid Product
                if(!isset($product->post) || $product->post->ID == '' || !is_numeric( $product_id ))
                {
                    $request_error = true;
                    $this->send_api_response( __( 'Error - Product Not Found; See shop manager for assistance', $this->plugin_slug ) );
                }

                if(!$request_error)
                {
                    // Add offer to cart
                    if($this->add_offer_to_cart( $offer, $offer_meta ) )
                    {
                        $this->send_api_response( __( 'Successfully added Offer to cart', $this->plugin_slug ), json_decode($pid));
                    }
                }
            }
        }
    }

    /**
     * Add offer to cart
     * @since   0.1.0
     */
    protected function add_offer_to_cart($offer = array(), $offer_meta = array() )
    {
        if ( ! is_admin() )
        {
            global $woocommerce;

            $quantity = $offer_meta['offer_quantity'][0];
            $product_id = $offer_meta['orig_offer_product_id'][0];
            $product_variation_id = $offer_meta['orig_offer_variation_id'][0];

            $_pf = new WC_Product_Factory();

            $_pf = new WC_Product_Factory;
            $_product = ( $product_variation_id ) ? $_pf->get_product( $product_variation_id ) : $_pf->get_product( $product_id );
            $_product_stock = $_product->get_total_stock();

            // lookup product meta by id or variant id
            if( $product_variation_id )
            {
                $product_variation_data = $_product->get_variation_attributes();
            }

            $product_variation_data['Offer ID'] = $offer->ID;

            $product_meta['woocommerce_offer_id'] = $offer->ID;
            $product_meta['woocommerce_offer_quantity'] = $offer_meta['offer_quantity'][0];
            $product_meta['woocommerce_offer_price_per'] = $offer_meta['offer_price_per'][0];

            $found = false;

            foreach($woocommerce->cart->get_cart() as $cart_item)
            {
                // check if offer id already in cart
                if(isset($cart_item['woocommerce_offer_id']) && $cart_item['woocommerce_offer_id'] == $offer->ID)
                {
                    $found = true;
                    $message = sprintf(
                        '<a href="%s" class="button wc-forward">%s</a> %s',
                        $woocommerce->cart->get_cart_url(),
                        __( 'View Cart', 'woocommerce' ),
                        __( 'Offer already added to cart', $this->plugin_slug ) );
                    $this->send_api_response( $message );
                }
            }

            if(!$found)
            {
                $item_id = $woocommerce->cart->add_to_cart( $product_id, $quantity, $product_variation_id, $product_variation_data, $product_meta );
            }

            if(isset($item_id))
            {
                return true;
            }
        }
        return false;
    }

    /** API Response Handler
     */
    public function send_api_response($msg, $pid = '')
    {
        global $woocommerce;
        $response['message'] = $msg;
        $response['type'] = 'error';

        if($pid)
        {
            $response['pid'] = $pid;
            $response['type'] = 'success';
            wc_add_notice( $response['message'], $response['type'] );
            wp_safe_redirect($woocommerce->cart->get_cart_url() );
            exit;
        }

        wc_add_notice( $response['message'], $response['type'] );
    }

    /**
     * Sets qty and price on any offer items in cart
     * @param $cart_object
     * @since   0.1.0
     */
    public function my_woocommerce_before_calculate_totals( $cart_object )
    {
        global $woocommerce;

        // loop cart contents to find offers -- force price to offer price per
        foreach ($cart_object->cart_contents as $key => $value) {
            // if offer item found
            if (isset($value['woocommerce_offer_price_per']) && $value['woocommerce_offer_price_per'] != '') {
                $value['data']->set_price($value['woocommerce_offer_price_per']);
            }
        }

        $showerror = false;
        // updating cart with posted values
        if(isset($_POST['cart']))
        {
            // loop cart contents to find offers -- force quantity to offer quantity
            foreach ($cart_object->cart_contents as $key => $value)
            {
                // if offer item found
                if (isset($value['woocommerce_offer_price_per']) && $value['woocommerce_offer_price_per'] != '')
                {
                    if (array_key_exists($key, $_POST['cart']))
                    {
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
            if ($showerror)
            {
                $message_type = 'error';
                $message = __('Offer quantity cannot be modified', $this->plugin_slug);
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
    function get_cart_items_from_session( $item, $values, $key ) {
        if ( array_key_exists( 'woocommerce_offer_id', $values ) )
        {
            $item[ 'woocommerce_offer_id' ] = $values['woocommerce_offer_id'];
        }
        if ( array_key_exists( 'woocommerce_offer_quantity', $values ) )
        {
            $item[ 'woocommerce_offer_quantity' ] = $values['woocommerce_offer_quantity'];
        }
        if ( array_key_exists( 'woocommerce_offer_price_per', $values ) )
        {
            $item[ 'woocommerce_offer_price_per' ] = $values['woocommerce_offer_price_per'];
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
    public function add_woocommerce_email_classes( $email_classes ) {

        // include our custom email classes
        include_once( 'includes/class-wc-new-offer-email.php' );
        include_once( 'includes/class-wc-new-counter-offer-email.php' );
        include_once( 'includes/class-wc-offer-received-email.php' );

        // add the email class to the list of email classes that WooCommerce loads
        $email_classes['WC_New_Offer_Email'] = new WC_New_Offer_Email();
        $email_classes['WC_New_Counter_Offer_Email'] = new WC_New_Counter_Offer_Email();
        $email_classes['WC_Offer_Received_Email'] = new WC_Offer_Received_Email();
        
        // include our custom email classes
        include_once( untrailingslashit( OFW_PLUGIN_URL ) . '/admin/includes/class-wc-accepted-offer-email.php' );
        include_once( untrailingslashit( OFW_PLUGIN_URL ) . '/admin/includes/class-wc-declined-offer-email.php' );
        include_once( untrailingslashit( OFW_PLUGIN_URL ) . '/admin/includes/class-wc-countered-offer-email.php' );
        
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
    public function ae_ofwc_woocommerce_checkout_order_processed( $order_id )
    {
        global $woocommerce;

        // Get Order
        $order = new WC_Order( $order_id );
        // Get order items
        $order_items = $order->get_items();

        // Check for offer id
        foreach( $order_items as $key => $value )
        {
            $item_offer_id = $order->get_item_meta( $key, 'Offer ID', true );

            /**
             * Update offer
             * Add postmeta value 'offer_order_id' for this order id
             * Set offer post status to 'completed-offer'
             */
            if( $item_offer_id )
            {
                // Update offer post args
                $offer_data = array();
                $offer_data['ID'] = $item_offer_id;
                $offer_data['post_status'] = 'completed-offer';

                // Update offer
                $offer_id = wp_update_post( $offer_data );

                // Check for offer post id
                if( $offer_id != 0 )
                {
                    // Add 'offer_order_id' postmeta to offer post
                    add_post_meta( $item_offer_id, 'offer_order_id', $order_id, true );

                    // Insert WP comment on related 'offer'
                    $comment_text = "<span>" . __('Updated - Status:', $this->plugin_slug) . "</span> ". __('Completed', $this->plugin_slug);
                    $comment_text.= '<p>' . __('Related Order', $this->plugin_slug) . ': ' . '<a href="post.php?post=' . $order_id . '&action=edit">#' . $order_id . '</a></p>';

                    $comment_data = array(
                        'comment_post_ID' => '',
                        'comment_author' => 'admin',
                        'comment_author_email' => '',
                        'comment_author_url' => '',
                        'comment_content' => $comment_text,
                        'comment_type' => '',
                        'comment_parent' => 0,
                        'user_id' => 1,
                        'comment_author_IP' => '127.0.0.1',
                        'comment_agent' => '',
                        'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                        'comment_approved' => 'post-trashed',
                    );
                    $new_comment_id = wp_insert_comment( $comment_data );

                    // insert comment meta
                    if( $new_comment_id )
                    {
                        add_comment_meta( $new_comment_id, 'angelleye_woocommerce_offer_id', $item_offer_id, true );
                    }
                }
            }
        }
    }

    /**
     * Filter - ae_paypal_standard_additional_parameters
     * @since   0.1.0
     */
    public function ae_paypal_standard_additional_parameters($paypal_args)
    {
        $paypal_args['bn'] = 'AngellEYE_SP_WooCommerce';
        return $paypal_args;
    }

    /**
     * Action - woocommerce_before_checkout_process
     * Checks for valid offer before checkout process
     * @since   0.1.0
     */
    public function ae_ofwc_woocommerce_before_checkout_process()
    {
        global $woocommerce;
        foreach( $woocommerce->cart->get_cart() as $cart_item )
        {
            // check if offer id already in cart
            if(isset($cart_item['woocommerce_offer_id']))
            {
                $pid = $cart_item['woocommerce_offer_id'];

                /**
                 * Lookup Offer
                 * - Make sure valid 'accepted-offer' or 'countered-offer' status
                 */
                $offer = get_post($pid);

                // Invalid Offer Id
                if ($offer == '') {
                    $this->send_api_response(__('Invalid or Expired Offer Id; See shop manager for assistance', $this->plugin_slug), '1');
                } else {
                    // Get offer meta
                    $offer_meta = get_post_meta($offer->ID, '', true);

                    // Error - Offer Not Accepted/Countered
                    if ($offer->post_status != 'accepted-offer' && $offer->post_status != 'countered-offer' && $offer->post_status != 'buyercountered-offer') {
                        $request_error = true;
                        $this->send_api_response(__('Invalid Offer Status or Expired Offer Id; See shop manager for assistance', $this->plugin_slug), '0');
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
    public function aeofwc_akismet_comment_check( $key, $data ) {
        $request = 'blog='. urlencode($data['blog']) .
            '&user_ip='. urlencode($data['user_ip']) .
            '&user_agent='. urlencode($data['user_agent']) .
            '&referrer='. urlencode($data['referrer']) .
            '&permalink='. urlencode($data['permalink']) .
            '&comment_type='. urlencode($data['comment_type']) .
            '&comment_author='. urlencode($data['comment_author']) .
            '&comment_author_email='. urlencode($data['comment_author_email']) .
            '&comment_author_url='. urlencode($data['comment_author_url']) .
            '&comment_content='. urlencode($data['comment_content']);
        $host = $http_host = $key.'.rest.akismet.com';
        $path = '/1.1/comment-check';
        $port = 443;
        $akismet_ua = "WordPress/3.8.1 | Akismet/2.5.9";
        $content_length = strlen( $request );
        $http_request  = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $http_request .= "Content-Length: {$content_length}\r\n";
        $http_request .= "User-Agent: {$akismet_ua}\r\n";
        $http_request .= "\r\n";
        $http_request .= $request;
        $response = '';
        if( false != ( $fs = @fsockopen( 'ssl://' . $http_host, $port, $errno, $errstr, 10 ) ) ) {

            fwrite( $fs, $http_request );

            while ( !feof( $fs ) )
                $response .= fgets( $fs, 1160 ); // One TCP-IP packet
            fclose( $fs );

            $response = explode( "\r\n\r\n", $response, 2 );
        }

        if ( 'true' == $response[1] )
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
    public function ofw_auto_approve_offer($offer_id = null, $emails = null) {
        global $wpdb; 
        if( isset($_POST["targetID"]) && !empty($_POST["targetID"]) ) {
            $post_id = $_POST["targetID"];
        } else {
            $post_id = $offer_id;
        }
        if( isset($post_id) && !empty($post_id) ) {
            $post_data = get_post($post_id);
            $table = $wpdb->prefix . "posts";
            $data_array = array(
                'post_status' => 'accepted-offer',
                'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                'post_modified_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1 ))
            );
            $where = array('ID' => $post_id);
            $wpdb->update( $table, $data_array, $where );
            $post_status_text = __('Accepted', $this->plugin_slug);
            $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? $_POST['angelleye_woocommerce_offer_status_notes'] : '';
            $recipient = get_post_meta($post_id, 'offer_email', true);
            $offer_id = $post_id;
            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
            $offer_name = get_post_meta($post_id, 'offer_name', true);
            $offer_email = $recipient;
            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
            $_pf = new WC_Product_Factory;
            $product = ( $variant_id ) ? $_pf->get_product( $variant_id ) : $_pf->get_product( $product_id );
            $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;
            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_total = ($product_qty * $product_price_per);
            if( $is_offer_buyer_countered_status ) {
                update_post_meta( $post_id, 'offer_quantity', $product_qty );
                update_post_meta( $post_id, 'offer_price_per', $product_price_per );
                update_post_meta( $post_id, 'offer_amount', $product_total );
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
                'product_total' => $product_total,
                'offer_notes' => $offer_notes
            );
            if( $variant_id ) {
                if ( $product->get_sku() ) {
                    $identifier = $product->get_sku();
                } else {
                    $identifier = '#' . $product->variation_id;
                }
                $attributes = $product->get_variation_attributes();
                $extra_data = ' &ndash; ' . implode( ', ', $attributes );
                $offer_args['product_title_formatted'] = sprintf( __( '%s &ndash; %s%s', 'woocommerce' ), $identifier, $product->get_title(), $extra_data );
            }
            else {
                $offer_args['product_title_formatted'] = $product->get_formatted_name();
            }
            $email_class = 'WC_Accepted_Offer_Email';
            if( empty($emails) ) {
                $wc_emails = new WC_Emails();
                $emails = $wc_emails->get_emails();
            }
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;
            $new_email->plugin_slug = $this->plugin_slug;
            $new_email->template_html  = 'woocommerce-offer-accepted.php';
            $new_email->template_html_path = untrailingslashit( OFW_PLUGIN_URL ) . '/admin/includes/emails/';
            $new_email->template_plain  = 'woocommerce-offer-accepted.php';
            $new_email->template_plain_path = untrailingslashit( OFW_PLUGIN_URL ) . '/admin/includes/emails/plain/';
            
            $new_email->trigger($offer_args);
            $comment_text = "<span>". __('Updated - Status:', $this->plugin_slug)."&nbsp;</span>";
            $comment_text.= $post_status_text;
            if(isset($offer_notes) && $offer_notes != '') {
                $comment_text.= '</br>'. nl2br($offer_notes);
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
                'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                'comment_approved' => 'post-trashed',
            );
            $new_comment_id = wp_insert_comment( $data );
            if( $new_comment_id ) {
                add_comment_meta( $new_comment_id, 'angelleye_woocommerce_offer_id', $post_id, true );
            }
        }
    }

    /**
     * 
     * @global type $wpdb
     * @param type $offer_id
     * @param type $emails
     * @since   1.2.0
     */
    public function ofw_auto_decline_offer($offer_id = null, $emails = null) {
        global $wpdb; 
        if( isset($_POST["targetID"]) && !empty($_POST["targetID"]) ) {
            $post_id = $_POST["targetID"];
        } else {
            $post_id = $offer_id;
        }
        if ( isset($post_id ) && !empty($post_id) ) {
            $post_data = get_post($post_id);
            $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;
            $table = $wpdb->prefix . "posts";
            $data_array = array(
                'post_status' => 'declined-offer',
                'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                'post_modified_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1 ))
            );
            $where = array('ID' => $post_id);
            $wpdb->update( $table, $data_array, $where );
            $post_status_text = __('Declined', $this->plugin_slug);
            $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? $_POST['angelleye_woocommerce_offer_status_notes'] : '';
            $recipient = get_post_meta($post_id, 'offer_email', true);
            $offer_id = $post_id;
            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
            $offer_name = get_post_meta($post_id, 'offer_name', true);
            $offer_email = $recipient;
            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
            $_pf = new WC_Product_Factory;
            $product = ( $variant_id ) ? $_pf->get_product( $variant_id ) : $_pf->get_product( $product_id );
            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_total = ($product_qty * $product_price_per);
            if( $is_offer_buyer_countered_status ) {
                update_post_meta( $post_id, 'offer_quantity', $product_qty );
                update_post_meta( $post_id, 'offer_price_per', $product_price_per );
                update_post_meta( $post_id, 'offer_amount', $product_total );
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
                'product_total' => $product_total,
                'offer_notes' => $offer_notes
            );
            if( $variant_id ) {
                if ( $product->get_sku() ) {
                    $identifier = $product->get_sku();
                } else {
                    $identifier = '#' . $product->variation_id;
                }
                $attributes = $product->get_variation_attributes();
                $extra_data = ' &ndash; ' . implode( ', ', $attributes );
                $offer_args['product_title_formatted'] = sprintf( __( '%s &ndash; %s%s', 'woocommerce' ), $identifier, $product->get_title(), $extra_data );
            } else {
                $offer_args['product_title_formatted'] = $product->get_formatted_name();
            }
            $email_class = 'WC_Declined_Offer_Email';
            if( empty($emails) ) {
                $wc_emails = new WC_Emails();
                $emails = $wc_emails->get_emails();
            }
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;
            $new_email->plugin_slug = $this->plugin_slug;
            $new_email->template_html  = 'woocommerce-offer-declined.php';
            $new_email->template_html_path = untrailingslashit( OFW_PLUGIN_URL ) . '/admin/includes/emails/'; 
            $new_email->template_plain  = 'woocommerce-offer-declined.php';
            $new_email->template_plain_path = untrailingslashit( OFW_PLUGIN_URL ) . '/admin/includes/emails/plain/';
            $new_email->trigger($offer_args);
            $comment_text = "<span>Updated - Status: </span>";
            $comment_text.= $post_status_text;
            if(isset($offer_notes) && $offer_notes != '') {
                $comment_text.= '</br>'. nl2br($offer_notes);
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
                'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                'comment_approved' => 'post-trashed',
            );
            $new_comment_id = wp_insert_comment( $data );
            if( $new_comment_id ) {
                add_comment_meta( $new_comment_id, 'angelleye_woocommerce_offer_id', $post_id, true );
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
        if( isset($post_meta_auto_accept_enabled) && $post_meta_auto_accept_enabled == 'yes') {
            if( isset($variant_id) && !empty($variant_id) ) {
                 $variable_product = new WC_Product_Variation( $variant_id );
                 $atual_regular_price = (isset($variable_product->regular_price) && !empty($variable_product->regular_price) ) ? $variable_product->regular_price : 0;
                 $atual_sales_price = $variable_product->sale_price;
             } else {
                 $atual_regular_price = get_post_meta( $product_id, '_regular_price', true);
                 $atual_sales_price = get_post_meta( $product_id, '_sale_price', true);
                 $atual_regular_price = (isset($atual_regular_price) && !empty($atual_regular_price) ) ? $atual_regular_price : 0;

             }
             $product_price = (isset($atual_sales_price) && !empty($atual_sales_price)) ? $atual_sales_price : $atual_regular_price;
             $offer_price = get_post_meta($offer_id, 'offer_price_per', true);
             $auto_accept_percentage = get_post_meta($product_id, '_offers_for_woocommerce_auto_accept_percentage', true);
             if( isset($offer_price) && !empty($offer_price) && isset($auto_accept_percentage) && !empty($auto_accept_percentage) ) {
                 $user_offer_percentage = $this->ofwc_get_percentage($offer_price, $product_price);
                 if( (int) $auto_accept_percentage <= (int) $user_offer_percentage) {
                     $this->ofw_auto_approve_offer($offer_id, $emails);
                     return true;
                 }
             }
        }

        $post_meta_auto_decline_enabled = get_post_meta($product_id, '_offers_for_woocommerce_auto_decline_enabled', true);
        if( isset($post_meta_auto_decline_enabled) && $post_meta_auto_decline_enabled == 'yes') {
            if( isset($variant_id) && !empty($variant_id) ) {
                 $variable_product = new WC_Product_Variation( $variant_id );
                 $atual_regular_price = (isset($variable_product->regular_price) && !empty($variable_product->regular_price) ) ? $variable_product->regular_price : 0;
                 $atual_sales_price = $variable_product->sale_price;
             } else {
                 $atual_regular_price = get_post_meta( $product_id, '_regular_price', true);
                 $atual_sales_price = get_post_meta( $product_id, '_sale_price', true);
                 $atual_regular_price = (isset($atual_regular_price) && !empty($atual_regular_price) ) ? $atual_regular_price : 0;

             }
             $product_price = (isset($atual_sales_price) && !empty($atual_sales_price)) ? $atual_sales_price : $atual_regular_price;
             $offer_price = get_post_meta($offer_id, 'offer_price_per', true);
             $auto_decline_percentage = get_post_meta($product_id, '_offers_for_woocommerce_auto_decline_percentage', true);
             if( isset($offer_price) && !empty($offer_price) && isset($auto_decline_percentage) && !empty($auto_decline_percentage) ) {
                 $user_offer_percentage = $this->ofwc_get_percentage($offer_price, $product_price);
                 if( (int) $auto_decline_percentage >= (int) $user_offer_percentage) {
                     $this->ofw_auto_decline_offer($offer_id, $emails);
                     return true;
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
}