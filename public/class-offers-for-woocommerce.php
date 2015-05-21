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
	const VERSION = '1.1.2';

	/**
	 *
	 * Unique pluginidentifier
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'angelleye-offers-for-woocommerce';

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
		 * Load plugin text domain
		 */
		add_action('init', array( $this, 'load_plugin_textdomain' ) );
		
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

        /* Add Offer Button if price blank */
        add_action( 'woocommerce_single_product_summary', array( $this, 'angelleye_ofwc_blank_price_display') );
		
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
        add_action( 'woocommerce_checkout_order_processed', array( $this, 'ae_ofwc_woocommerce_checkout_order_processed' ), 1, 2 );

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
    }

	/**
	 * Add extra div wrap before add to cart button
	 *
	 * @since	0.1.0
	 */
	public function angelleye_ofwc_before_add_to_cart_button()
	{
		global $post;
		$custom_tab_options_offers = array(
			'enabled' => get_post_meta( $post->ID, 'offers_for_woocommerce_enabled', true ),
		);

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
        $is_external_product = ( isset( $_product->product_type ) && $_product->product_type == 'external' ) ? TRUE : FALSE;
        $is_instock = ( $_product->is_in_stock() ) ? TRUE : FALSE;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

        echo $_product->get_price();

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
                $hiddenclass = ( isset($button_options_display['display_setting_make_offer_button_position_single']) && $button_options_display['display_setting_make_offer_button_position_single'] != 'default') ? 'angelleye-ofwc-hidden' : '';
                $customclass = ( $hiddenclass == 'angelleye-ofwc-hidden' ) ? $button_options_display['display_setting_make_offer_button_position_single'] : '';

                echo '<div class="offers-for-woocommerce-make-offer-button-cleared '.$hiddenclass.'"></div>
                <div id="offers-for-woocommerce-add-to-cart-wrap" class="offers-for-woocommerce-add-to-cart-wrap" data-ofwc-position="'.$customclass.'"><div>';
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
            // get offers options - display
            $button_options_display = get_option('offers_for_woocommerce_options_display');

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

            if( (is_front_page() && !$button_global_onoff_frontpage) || (!is_front_page() && !is_product() && !$button_global_onoff_catalog) )
            {
                //
            }
            else
            {
                // adds hidden class if position is not default
                $hiddenclass = ( isset($button_options_display['display_setting_make_offer_button_position_single']) && $button_options_display['display_setting_make_offer_button_position_single'] != 'default') ? 'angelleye-ofwc-hidden' : '';
                $customclass = ( $hiddenclass == 'angelleye-ofwc-hidden' ) ? $button_options_display['display_setting_make_offer_button_position_single'] : '';

                $is_lightbox = (isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') ? TRUE : FALSE;
                $lightbox_class = (isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') ? ' offers-for-woocommerce-make-offer-button-single-product-lightbox' : '';

                echo '<div class="angelleye-offers-clearfix '.$hiddenclass.'"></div></div><div class="single_variation_wrap_angelleye ofwc_offer_tab_form_wrap '.$hiddenclass.'"><button type="button" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-single-product ' . $lightbox_class . ' button alt" style="' . $custom_styles_override . '">' . $button_title . '</button></div>';
                echo '</div>';
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
		$custom_tab_options_offers = array(
			'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),
		);

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
        $is_external_product = ( isset( $_product->product_type ) && $_product->product_type == 'external' ) ? TRUE : FALSE;
        $is_instock = ( $_product->is_in_stock() ) ? TRUE : FALSE;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

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
                // get offers options - display
                $button_options_display = get_option('offers_for_woocommerce_options_display');

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

                echo '<a href="'.get_permalink($post->ID).'?aewcobtn=1" id="offers-for-woocommerce-make-offer-button-id-'.$post->ID.'" class="offers-for-woocommerce-make-offer-button-catalog button alt" '.$custom_styles_override.'>'.$button_title.'</a>';
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
        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

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
     * Action - Add Offer Button if price blank
     */
    public function angelleye_ofwc_blank_price_display()
    {
        global $post;
        $custom_tab_options_offers = array(
            'enabled' => get_post_meta( $post->ID, 'offers_for_woocommerce_enabled', true ),
        );

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
        $is_external_product = ( isset( $_product->product_type ) && $_product->product_type == 'external' ) ? TRUE : FALSE;
        $is_instock = ( $_product->is_in_stock() ) ? TRUE : FALSE;

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');

        // get offers options - display
        $button_options_display = get_option('offers_for_woocommerce_options_display');

        echo $_product->get_price();

        // if post has offers button enabled
        if ( $custom_tab_options_offers['enabled'] == 'yes' && !$is_external_product && $is_instock && ($_product->get_price() == ''))
        {
            // get global on/off settings for offer button
            $button_global_onoff_frontpage = ($button_options_general && isset($button_options_general['general_setting_enable_make_offer_btn_frontpage']) && $button_options_general['general_setting_enable_make_offer_btn_frontpage'] != '') ? true : false;
            $button_global_onoff_catalog = ($button_options_general && isset($button_options_general['general_setting_enable_make_offer_btn_catalog']) && $button_options_general['general_setting_enable_make_offer_btn_catalog'] != '') ? true : false;

            // get offers options - display
            $button_options_display = get_option('offers_for_woocommerce_options_display');

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

            if( (is_front_page() && !$button_global_onoff_frontpage) || (!is_front_page() && !is_product() && !$button_global_onoff_catalog) )
            {
                //
            }
            else
            {
                // adds hidden class if position is not default
                $hiddenclass = ( isset($button_options_display['display_setting_make_offer_button_position_single']) && $button_options_display['display_setting_make_offer_button_position_single'] != 'default') ? 'angelleye-ofwc-hidden' : '';
                $customclass = ( $hiddenclass == 'angelleye-ofwc-hidden' ) ? $button_options_display['display_setting_make_offer_button_position_single'] : '';

                $is_lightbox = (isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') ? TRUE : FALSE;
                $lightbox_class = (isset($button_options_display['display_setting_make_offer_form_display_type']) && $button_options_display['display_setting_make_offer_form_display_type'] == 'lightbox') ? ' offers-for-woocommerce-make-offer-button-single-product-lightbox' : '';

                echo '<div class="single_variation_wrap_angelleye ofwc_offer_tab_form_wrap '.$hiddenclass.'"><button type="button" id="offers-for-woocommerce-make-offer-button-id-' . $post->ID . '" class="offers-for-woocommerce-make-offer-button-single-product ofwc-no-float-button ' . $lightbox_class . ' button alt" style="' . $custom_styles_override . '">' . $button_title . '</button></div>';
                echo '<div id="ofwc-blank-price-product-id" data-product-id="'.$post->ID.'"></div>';



            }
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
        $custom_tab_options_offers = array(
            'enabled' => get_post_meta( $post->ID, 'offers_for_woocommerce_enabled', true ),
        );

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
        $is_external_product = ( isset( $_product->product_type ) && $_product->product_type == 'external' ) ? TRUE : FALSE;
        $is_instock = ( $_product->is_in_stock() ) ? TRUE : FALSE;

        // if post has offers button enabled
        if ( (!$_product->get_price()) || $custom_tab_options_offers['enabled'] == 'yes' && !$is_external_product && $is_instock && ($_product->get_price() != 0))
        {
            // get offers options - display
            $button_options_display = get_option('offers_for_woocommerce_options_display');

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

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
        $is_sold_individually = $_product->is_sold_individually();
        $is_backorders_allowed = $_product->backorders_allowed();
        $stock_quantity = $_product->get_stock_quantity();

        // get offers options - general
        $button_options_general = get_option('offers_for_woocommerce_options_general');
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
         */
        $log_url = $_SERVER['HTTP_HOST'];
        $log_plugin_id = 3;
        $log_activation_status = 1;
        wp_remote_request('http://www.angelleye.com/web-services/wordpress/update-plugin-status.php?url=' . $log_url . '&plugin_id=' . $log_plugin_id . '&activation_status=' . $log_activation_status);
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
         */
        $log_url = $_SERVER['HTTP_HOST'];
        $log_plugin_id = 3;
        $log_activation_status = 0;
        wp_remote_request('http://www.angelleye.com/web-services/wordpress/update-plugin-status.php?url='.$log_url.'&plugin_id='.$log_plugin_id.'&activation_status='.$log_activation_status);
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
	 * Load the plugin text domain for translation
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain()
	{
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
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

                            if(!$wpdb->query( $wpdb->prepare(
                                "INSERT INTO $wpdb->postmeta
                                    ( post_id, meta_key, meta_value )
                                    VALUES ( %d, %s, %s )
                                ",
                                $parent_post_id,
                                $newPostMetaData['meta_key'],
                                $newPostMetaData['meta_value']
                                ) ) )
                            {
                                ////echo json_encode($wpdb->last_query);
                                // return error msg
                                echo json_encode(array("statusmsg" => 'failed', "statusmsgDetail" => 'database error'));
                                exit;
                            }
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
                    $offer_args['product_title_formatted'] = $product->get_formatted_name();
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

                // Check product stock availability
                $_pf = new WC_Product_Factory();
                $_product = $_pf->get_product($product_id);
                $_product_stock = $_product->get_total_stock();
                $_product_in_stock = $_product->has_enough_stock($offer_meta['offer_quantity'][0]);

                if(!$_product_in_stock)
                {
                    $request_error = true;

                    if($_product_stock != '' && $_product_stock != '0' && $_product_stock < $offer_meta['offer_quantity'][0])
                    {
                        $_product_in_stock_formatted = number_format($_product_stock, 0);
                        $this->send_api_response( sprintf( __( 'Error - Product does not have enough in stock to fulfill your order at this time.', $this->plugin_slug ). '<br />Current stock available: %s', $_product_in_stock_formatted ) );
                    }
                    else
                    {
                        $this->send_api_response( __( 'Error - Product is out of stock; See shop manager for assistance', $this->plugin_slug ) );
                    }
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
        require( 'includes/class-wc-new-offer-email.php' );
        require( 'includes/class-wc-new-counter-offer-email.php' );
        require( 'includes/class-wc-offer-received-email.php' );

        // add the email class to the list of email classes that WooCommerce loads
        $email_classes['WC_New_Offer_Email'] = new WC_New_Offer_Email();
        $email_classes['WC_New_Counter_Offer_Email'] = new WC_New_Counter_Offer_Email();
        $email_classes['WC_Offer_Received_Email'] = new WC_Offer_Received_Email();

        return $email_classes;
    }

    /**
     * Action - woocommerce_checkout_order_processed
     * Adds offer postmeta  'offer_order_id'
     * @since   0.1.0
     */
    public function ae_ofwc_woocommerce_checkout_order_processed( $order_id, $posted )
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

}