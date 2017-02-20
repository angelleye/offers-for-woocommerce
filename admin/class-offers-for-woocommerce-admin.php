<?php
/**
 * Offers for WooCommerce - admin
 *
 * @package   Angelleye_Offers_For_Woocommerce_Admin
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 */

/**
 * Plugin class - admin
 * Administrative side of the WordPress site.
 * 
 * @since	0.1.0
 * @package	Angelleye_Offers_For_Woocommerce_Admin
 * @author	AngellEYE <andrew@angelleye.com>
 */ 
class Angelleye_Offers_For_Woocommerce_Admin {
	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	/**
	 * Slug of the plugin screen
	 * @since    0.1.0
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	
	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a settings page and menu
	 * @since     0.1.0
	 */
	private function __construct()
	{
        /**
         * Define email templates path
         */
                if (!defined('OFWC_EMAIL_TEMPLATE_PATH')) {
                    define( 'OFWC_EMAIL_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/emails/' );
                }
                
		/**
		 * Call $plugin_slug from public plugin class
		 * @since	0.1.0
		 */
                
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		/**
         * Filter - Add links to plugin meta
         * @since   1.1.2
         */
        add_filter( 'plugin_row_meta', array( $this, 'ofwc_add_plugin_action_links' ), 10, 2 );
		
		/**
		 *******************************
		 * Define custom functionality *
		 *******************************
		 */
		 
		/**
		 * Action - Add post type "woocommerce_offer" 
		 *
		 * @since	0.1.0
		 */
		add_action('init', array( $this, 'angelleye_ofwc_add_post_type_woocommerce_offer' ) );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_filter('manage_woocommerce_offer_posts_columns' , array( $this, 'set_woocommerce_offer_columns' ), 1, 10  );

		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'manage_woocommerce_offer_posts_custom_column' , array( $this, 'get_woocommerce_offer_column' ), 2, 10 );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_filter( 'manage_edit-woocommerce_offer_sortable_columns', array( $this, 'woocommerce_offer_sortable_columns' ) );
				
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'admin_init', array( $this, 'remove_woocommerce_offer_meta_boxes' ) );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		//add_action('admin_menu', array( $this, 'my_remove_submenus' ) );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_filter( 'comments_clauses', array( $this, 'angelleye_ofwc_exclude_cpt_from_comments_clauses' ), 10, 1 );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */

		add_filter('post_row_actions', array( $this, 'remove_quick_edit' ), 10, 2 );


        /**
         * XXX
         * @since	0.1.0
         */
        add_action( 'init', array( $this, 'my_custom_post_status_accepted' ), 10, 2 );

        /**
         * XXX
         * @since	0.1.0
         */
        add_action( 'init', array( $this, 'my_custom_post_status_countered' ), 10, 2 );

        /**
         * Adds post_status 'on-hold-offer'
         * @since	1.0.1
         */
        add_action( 'init', array( $this, 'my_custom_post_status_on_hold' ), 10, 2 );

        /**
         * Adds post_status 'expired-offer'
         * @since	1.0.1
         */
        add_action( 'init', array( $this, 'my_custom_post_status_expired' ), 10, 2 );

        /**
         * XXX
         * @since	0.1.0
         */
        add_action( 'init', array( $this, 'my_custom_post_status_buyer_countered' ), 10, 2 );

        /**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'init', array( $this, 'my_custom_post_status_completed' ), 10, 2 );

        /**
         * XXX
         * @since	0.1.0
         */
        add_action( 'init', array($this, 'my_custom_post_status_declined' ), 10, 2 );

		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_filter( 'display_post_states', array( $this, 'jc_display_archive_state' ) );

		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'current_screen', array( $this, 'translate_published_post_label' ) , 10, 2 );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_filter( 'bulk_actions-edit-woocommerce_offer', array( $this, 'my_custom_bulk_actions' ) );

        /**
         * XXX
         * @since	0.1.0
         */
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box_offer_summary' ), 10, 2 );

       /**
         * XXX
         * @since	0.1.0
         */
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box_offer_comments' ), 10, 2 );

        /**
         * XXX
         * @since	0.1.0
         */
                if(!$this->ofw_is_anonymous_communication_enable()) {
                    add_action( 'add_meta_boxes', array( $this, 'add_meta_box_offer_addnote' ), 10, 2 );
                }
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'save_post', array( $this, 'myplugin_save_meta_box_data' ) );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action('admin_init', array( $this, 'angelleye_ofwc_intialize_options' ) );
		
		/**
		 * Action - Admin Menu - Add the 'pending offer' count bubble
		 * @since	0.1.0
		 */
		add_action( 'admin_menu', array( $this, 'add_user_menu_bubble' ) );
		
		/**
		 * Action - Add 'pending offer(s)' count to wp dashboard 'at a glance' widget
		 * @since	0.1.0
		 */
		add_action( 'dashboard_glance_items', array( $this, 'my_add_cpt_to_dashboard' ) );

		 /**
		 * Action - Admin Menu - Add child submenu items for the woocommerce->offers submenu
		 * @since	0.1.0
		 */
		add_action( 'admin_menu', array( $this, 'add_offers_submenu_children' ) );
		
		/**
		 * Process meta
		 *
		 * Processes the custom tab options when a post is saved
		 * @since	0.1.0
		 */
		add_action('woocommerce_process_product_meta', array( $this, 'process_product_meta_custom_tab' ), 10, 2 );
		
		/**
		 * Output WooCommerce Tab on product single
		 * @since	0.1.0
		 */
		add_action('woocommerce_product_write_panel_tabs', array( $this, 'custom_tab_options_tab_offers' ));
		
		/*
		 * Action - Add custom tab options in WooCommerce product tabs
		 * @since	0.1.0
		 */
		add_action('woocommerce_product_data_panels', array( $this, 'custom_tab_options_offers' ));
		
		/**
		 * Override updated message for custom post type
		 *
		 * @param array $messages Existing post update messages.
		 *
		 * @return array Amended post update messages with new CPT update messages.
		 * @since	0.1.0
		 */
		add_filter( 'post_updated_messages', array( $this, 'my_custom_updated_messages' ) );
		
		/*
		 * ADMIN COLUMN - SORTING - ORDERBY
		 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
		 */
		add_filter( 'request', array( $this, 'woocommerce_offers_list_orderby' ) );
		
		/*
		 * ADD TO QUERY - PULL IN all except 'trash' when viewing 'all' list
		 * @since	0.1.0
		 */
		add_action('pre_get_posts', array( $this, 'my_pre_get_posts' ) );

        /**
         * Join posts and postmeta tables
         * @since   1.0.1
         */
        add_filter('posts_join', array( $this, 'aeofwc_search_join' ) );

        /**
         * Modify the search query with posts_where
         * @since   1.0.1
         */
        add_filter( 'posts_where', array( $this, 'aeofwc_search_where' ) );

        /**
         * Prevent duplicates
         * @since   1.0.1
         */
        add_filter( 'posts_distinct', array( $this, 'aeofwc_search_distinct' ) );

        /*
         * Action - Ajax 'approve offer' from manage list
         * @since	0.1.0
         */
        add_action( 'wp_ajax_approveOfferFromGrid', array( $this, 'approveOfferFromGridCallback') );
        add_action( 'approveOfferFromGrid', array( $this, 'approveOfferFromGridCallback') );

        /*
         * Action - Ajax 'decline offer' from manage list
         * @since	0.1.0
         */
        add_action( 'wp_ajax_declineOfferFromGrid', array( $this, 'declineOfferFromGridCallback') );
        add_action( 'declineOfferFromGrid', array( $this, 'declineOfferFromGridCallback') );

        /*
         * Action - Ajax 'add offer note' from manage offer details
         * @since	0.1.0
         */
        add_action( 'wp_ajax_addOfferNote', array( $this, 'addOfferNoteCallback') );

        /*
         * Action - Ajax 'bulk enable/disable tool' from offers settings/tools
         * @since	0.1.0
         */
        add_action( 'wp_ajax_adminToolBulkEnableDisable', array( $this, 'adminToolBulkEnableDisableCallback') );
        
        /*
         * Filter - Add email class to WooCommerce for 'Accepted Offer'
         * @since   0.1.0
         */
        add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_email_classes' ) );

        /**
         * XXX
         * @since	0.1.0
         */
        add_action( 'admin_notices', array( $this, 'aeofwc_admin_notices' ) );

        /**
         * Adds help tab content for manage offer screen
         * @since   0.1.0
         */
        add_filter( 'contextual_help', array( $this, 'ae_ofwc_contextual_help'), 10, 3 );

        /**
         * Check for WooCommerce plugin
         * Adds nag message to admin notice
         * @since   1.0.1
         */
        add_action( 'admin_init', array( $this, 'ae_ofwc_check_woocommerce_nag_notice_ignore' ) );
        add_action('admin_init', array( $this, 'ae_ofwc_check_woocommerce_available' ) );

        /**
         * Action - Bulk action - Enable/Disable Offers on WooCommerce products
         * @since   1.0.1
         */
        add_action('admin_footer-edit.php', array( $this, 'custom_bulk_admin_footer' ) );

        /**
         * Action - Bulk action - Process Enable/Disable Offers on WooCommerce products
         * @since   1.0.1
         */
        add_action('load-edit.php', array( $this, 'custom_bulk_action' ) );

        /**
         * Action - Show admin notice for bulk action Enable/Disable Offers on WooCommerce products
         * @since   1.0.1
         */
        add_action('admin_notices', array( $this, 'custom_bulk_admin_notices' ) );
        
        /**
         * Action - Quick edit - Process Enable/Disable Offers on WooCommerce products display field
         * @since   1.2
         */
        add_action('woocommerce_product_quick_edit_end', array( $this, 'woocommerce_product_quick_edit_end_own' ), 10 );
        
        /**
         * Action - Quick edit - Process Enable/Disable Offers on WooCommerce products save field
         * @since   1.2
         */
        add_action('woocommerce_product_quick_edit_save', array( $this, 'woocommerce_product_quick_edit_save_own' ), 10, 1 );
        
        /**
         * Action - Bulk edit - Process Enable/Disable Offers on WooCommerce products display field
         * @since   1.2
         */
        add_action('woocommerce_product_bulk_edit_end', array( $this, 'woocommerce_product_quick_edit_end_own' ), 10 );
        
        /**
         * Action - Bulk edit - Process Enable/Disable Offers on WooCommerce products save data
         * @since   1.2
         */
        add_action('woocommerce_product_bulk_edit_save', array( $this, 'woocommerce_product_quick_edit_save_own' ), 10, 1 );
        
        /**
         * @since   1.2
         * Add jquery thinkbox in footer area
         */
        add_action('in_admin_footer', array($this, 'my_admin_footer_function'));
        add_action('offers_for_woocommerce_setting_tab', array( $this, 'offers_for_woocommerce_setting_tab_own' ) );
        add_action('offers_for_woocommerce_setting_tab_content', array( $this, 'offers_for_woocommerce_setting_tab_content_own' ) );
        add_action('offers_for_woocommerce_setting_tab_content_save', array( $this, 'offers_for_woocommerce_setting_tab_content_save_own' ) );
        add_action( 'admin_init', array( $this, 'ofw_auto_accept_decline_from_email' ) );
        add_filter( 'the_title', array($this, 'ofw_anonymous_title'), 10, 2);
        add_filter( 'woocommerce_cart_shipping_packages', array($this, 'ofw_woocommerce_cart_shipping_packages'), 10, 1);

        // Resolve conflict with PDF Invoice Packaging Slip plugin
        if ( class_exists( 'WooCommerce_PDF_Invoices' ) ) {
            remove_action( 'woocommerce_email_header', array( WC()->mailer(), 'email_header' ) );
            remove_action( 'woocommerce_email_footer', array( WC()->mailer(), 'email_footer' ) );
        }

        /**
         * Action - Manage offers if product is deleted
         */
        add_action( 'trashed_post', array($this, 'ofw_before_product_trash_action'), 10, 1);
        add_action( 'after_delete_post', array($this, 'ofw_before_product_trash_action'), 10, 1);
        add_action( 'untrashed_post', array($this, 'ofw_before_product_untrash_action'), 10, 1);
        /**
         * END - custom functions
         */

	} // END - construct
	
	/**
	 * Action - Add post type "woocommerce_offer" 
	 *
	 * @since	0.1.0
	 */
	function angelleye_ofwc_add_post_type_woocommerce_offer()
	{
                $show_in_menu = current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true;
		register_post_type( 'woocommerce_offer',
			array(
				'labels' => array(
					'name' => __('Manage Offers', 'offers-for-woocommerce'),
					'singular_name' => __('WooCommerce Offer', 'offers-for-woocommerce'),
					'add_new' => __('Add New', 'offers-for-woocommerce'),
					'add_new_item' => __('Add New WooCommerce Offer', 'offers-for-woocommerce'),
					'edit' => __('Manage', 'offers-for-woocommerce'),
					'edit_item' => __('Manage WooCommerce Offer', 'offers-for-woocommerce'),
					'new_item' => __('New WooCommerce Offer', 'offers-for-woocommerce'),
					'view' => __('View', 'offers-for-woocommerce'),
					'view_item' => __('View WooCommerce Offer', 'offers-for-woocommerce'),
					'search_items' => __('Search WooCommerce Offers', 'offers-for-woocommerce'),
					'not_found' => __('No WooCommerce Offers found', 'offers-for-woocommerce'),
					'not_found_in_trash' => __('No WooCommerce Offers found in Trash', 'offers-for-woocommerce'),
					'parent' => __('Parent WooCommerce Offer', 'offers-for-woocommerce')
				),
				'description' => 'Offers for WooCommerce - Custom Post Type',
				'public' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => true,            
				'hierarchical' => false,
				'show_in_menu' => $show_in_menu,
				'menu_position' => '',
				'show_in_admin_bar' => false,
				'supports' => array( 'section_id_offer_comments', 'section_id_offer_summary', 'section_id_offer_addnote' ),
				//'capability_type' => 'post',
				//'capabilities' => array( 'create_posts' => false,),	// Removes support for the "Add New" function
				'taxonomies' => array(''),
				//'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),	// No longer used; instead we use CSS for icon
				'menu_icon' => '',
				'has_archive' => false,
                                'capability_type' => 'woocommerce_offer',
                                'map_meta_cap' => true
			)
		);
                
                $this->create_woocommerce_offer_capabilities();
	}
		
	/**
	 * Callback Action - Admin Menu - Add the 'pending offer' count bubble
	 * @since	0.1.0
	 */
	function add_user_menu_bubble() 
	{
            global $wpdb;
            $args = array('woocommerce_offer','publish');
            $pend_count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '%s' AND post_status = '%s'", $args ) );
            global $submenu;

            if(isset($submenu['woocommerce']) && !empty($submenu['woocommerce'])){
                foreach($submenu['woocommerce'] as $key => $value)
                {
                    if ( $submenu['woocommerce'][$key][2] == 'edit.php?post_type=woocommerce_offer' ) {
                            $submenu['woocommerce'][$key][0] = 'Offers';
                            $submenu['woocommerce'][$key][0] .= " <span id='woocommerce-offers-count' class='awaiting-mod update-plugins count-$pend_count'><span class='pending-count'>" . $pend_count . '</span></span>';
                    }
                }
            }
	}
	
	/**
	 * Callback Action - Admin Menu - Add child submenu items for the woocommerce->offers submenu
	 * @since	0.1.0
	 */
	function add_offers_submenu_children() 
	{
            $offers_manage_link_href = admin_url( 'edit.php?post_type=woocommerce_offer');
            $offers_settings_link_href = admin_url( 'options-general.php?page=offers-for-woocommerce');
            global $submenu;
            if(isset($submenu['woocommerce']) && !empty($submenu['woocommerce'])){
                foreach($submenu['woocommerce'] as $key => $value)
                {
                    if ( $submenu['woocommerce'][$key][2] == 'edit.php?post_type=woocommerce_offer' ) {
                            // Add child submenu html
                            $submenu['woocommerce'][$key][0] .= "<script type='text/javascript'>
                            jQuery(window).load(function($){
                                    jQuery('#woocommerce-offers-count').parent('a').after('<ul id=\'woocommerce-offer-admin-submenu\' class=\'\'><li class=\'woocommerce-offer-admin-submenu-item\'><a href=\'".$offers_manage_link_href."\'>&nbsp;&#8211;&nbsp;". __('Manage Offers', 'offers-for-woocommerce'). "</a></li><li class=\'woocommerce-offer-admin-submenu-item\'><a id=\'woocommerce-offers-settings-link\' class=\'woocommerce-offer-submenu-link\' href=\'".$offers_settings_link_href."\'>&nbsp;&#8211;&nbsp;". __('Offers Settings', 'offers-for-woocommerce'). "</a></li></ul>');
                            });</script>";					
                    }
                }
            }
	}
	
	/**
	 * Filter - Add custom product data tab on woocommerce product edit page
	 * @since	0.1.0
	 */
	//add_filter( 'woocommerce_product_tabs', 'ofwc_filter_woocommerce_product_tabs');			
	
	
	/**
	 * Process meta
	 *
	 * Processes the custom tab options when a post is saved
	 * @since	0.1.0
	 */
	function process_product_meta_custom_tab( $post_id ) {
            update_post_meta( $post_id, 'offers_for_woocommerce_enabled', ( isset($_POST['offers_for_woocommerce_enabled']) && $_POST['offers_for_woocommerce_enabled'] ) ? 'yes' : 'no' );
            update_post_meta( $post_id, '_offers_for_woocommerce_auto_accept_enabled', ( isset($_POST['_offers_for_woocommerce_auto_accept_enabled']) && $_POST['_offers_for_woocommerce_auto_accept_enabled'] ) ? 'yes' : 'no' );
            update_post_meta( $post_id, '_offers_for_woocommerce_auto_decline_enabled', ( isset($_POST['_offers_for_woocommerce_auto_decline_enabled']) && $_POST['_offers_for_woocommerce_auto_decline_enabled'] ) ? 'yes' : 'no' );
            update_post_meta( $post_id, '_offers_for_woocommerce_auto_accept_percentage', ( isset($_POST['_offers_for_woocommerce_auto_accept_percentage']) && !empty($_POST['_offers_for_woocommerce_auto_accept_percentage']) ) ? $_POST['_offers_for_woocommerce_auto_accept_percentage'] : '' );
            update_post_meta( $post_id, '_offers_for_woocommerce_auto_decline_percentage', ( isset($_POST['_offers_for_woocommerce_auto_decline_percentage']) && !empty($_POST['_offers_for_woocommerce_auto_decline_percentage']) ) ? $_POST['_offers_for_woocommerce_auto_decline_percentage'] : '' );
	}
	
	/**
	 * Output WooCommerce Tab on product single
	 * @since	0.1.0
	 */
	function custom_tab_options_tab_offers() {
        global $post;
        $_product = wc_get_product($post->ID);
        $class_hidden = ( isset( $_product->product_type ) && $_product->product_type == 'external' ) ? ' custom_tab_offers_for_woocommerce_hidden' : '';
        print(
            '<li id="custom_tab_offers_for_woocommerce" class="custom_tab_offers_for_woocommerce '. $class_hidden . '"><a href="#custom_tab_data_offers_for_woocommerce">' . __('Offers', 'offers-for-woocommerce') . '</a></li>'
        );
	}
	
	/**
	 * Callback Action - Add custom tab options in WooCommerce product tabs
	 * Provides the input fields and add/remove buttons for custom tabs on the single product page.
	 * @since	0.0.1
	 */
	function custom_tab_options_offers() {
            global $post, $pagenow;
            
            /**
             * offers_for_woocommerce_enabled
             */
            
            $post_meta_offers_enabled = get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true);
            $field_value = 'yes';
            $field_callback = ($post_meta_offers_enabled) ? $post_meta_offers_enabled : 'no';
            $button_options_general = get_option('offers_for_woocommerce_options_general');
            if( $pagenow == 'post-new.php' && isset($button_options_general['general_setting_enable_offers_by_default']) ) {
                if( $button_options_general['general_setting_enable_offers_by_default'] == '1' ) {
                    $field_callback = 'yes';
                }
             }
             
            /**
             *  Auto Accept Offer
             */
            $post_meta_auto_accept_enabled = get_post_meta($post->ID, '_offers_for_woocommerce_auto_accept_enabled', true);
            $field_value_auto_accept_enabled = 'yes';
            $field_callback_auto_accept_enabled = ($post_meta_auto_accept_enabled) ? $post_meta_auto_accept_enabled : 'no';
            $post_meta_auto_accept_percentage = get_post_meta($post->ID, '_offers_for_woocommerce_auto_accept_percentage', true);
            $post_meta_auto_accept_percentage_value = ($post_meta_auto_accept_percentage) ? $post_meta_auto_accept_percentage : '';
            
            
            /**
             * Auto Decline Offer
             */
            $post_meta_auto_decline_enabled = get_post_meta($post->ID, '_offers_for_woocommerce_auto_decline_enabled', true);
            $field_value_auto_decline_enabled = 'yes';
            $field_callback_auto_decline_enabled = ($post_meta_auto_decline_enabled) ? $post_meta_auto_decline_enabled : 'no';
            $post_meta_auto_decline_percentage = get_post_meta($post->ID, '_offers_for_woocommerce_auto_decline_percentage', true);
            $post_meta_auto_decline_percentage_value = (!empty($post_meta_auto_decline_percentage)) ? $post_meta_auto_decline_percentage : '';
            
            ?>
            <div id="custom_tab_data_offers_for_woocommerce" class="panel woocommerce_options_panel">
                <div class="options_group">
                     <?php woocommerce_wp_checkbox( array('value' => $field_value, 'cbvalue' => $field_callback, 'id' => 'offers_for_woocommerce_enabled', 'label' => __('Enable Offers?', 'offers-for-woocommerce'), 'desc_tip' => 'true', 'description' => __('Enable this option to enable the \'Make Offer\' buttons and form display in the shop.', 'offers-for-woocommerce') ) ); ?>
                </div> 
                <div class="options_group">
                    <?php woocommerce_wp_checkbox( array('value' => $field_value_auto_accept_enabled, 'cbvalue' => $field_callback_auto_accept_enabled, 'id' => '_offers_for_woocommerce_auto_accept_enabled', 'label' => __('Enable Auto Accept Offers?', 'offers-for-woocommerce'), 'desc_tip' => 'true', 'description' => __('Enable this option to automatically accept offers based on the percentage set.', 'offers-for-woocommerce') ) ); ?>
                    <p class="form-field offers_for_woocommerce_auto_accept_percentage "><label for="offers_for_woocommerce_auto_accept_percentage"><?php echo __( 'Auto Accept Percentage', 'offers-for-woocommerce' ) ; ?></label><input type="number" placeholder="Enter Percentage" value="<?php echo $post_meta_auto_accept_percentage_value; ?>" min="1" max="100" id="_offers_for_woocommerce_auto_accept_percentage" name="_offers_for_woocommerce_auto_accept_percentage" style="" class="short"> <?php echo '<img class="help_tip" data-tip="' . esc_attr( 'Any offer above the percentage entered here will be automatically accepted.' ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />'; ?> </p>               
                </div>
                <div class="options_group">
                    <?php woocommerce_wp_checkbox( array('value' => $field_value_auto_decline_enabled, 'cbvalue' => $field_callback_auto_decline_enabled, 'id' => '_offers_for_woocommerce_auto_decline_enabled', 'label' => __('Enable Auto Decline Offers?', 'offers-for-woocommerce'), 'desc_tip' => 'true', 'description' => __('Enable this option to automatically decline offers based on the percentage set.', 'offers-for-woocommerce') ) ); ?>
                     <p class="form-field offers_for_woocommerce_auto_decline_percentage "><label for="_offers_for_woocommerce_auto_decline_percentage"><?php echo __( 'Auto Decline Percentage', 'offers-for-woocommerce' ) ; ?></label><input type="number" placeholder="Enter Percentage" value="<?php echo $post_meta_auto_decline_percentage_value; ?>" min="1" max="100" id="offers_for_woocommerce_auto_decline_percentage" name="_offers_for_woocommerce_auto_decline_percentage" style="" class="short"> <?php echo '<img class="help_tip" data-tip="' . esc_attr( 'Any offer below the percentage entered here will be automatically declined' ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />'; ?> </p>               
                </div>
            </div>
            <?php
	}
	
	/*************/
	/*************/
	
	/**
	 **************************
	 * Admin public functions start HERE *
	 **************************
	 */
	
	
	/**
	 * Override updated message for custom post type
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with new CPT update messages.
	 * @since	0.1.0
	 */
	public function my_custom_updated_messages( $messages ) {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );
	
		$messages['woocommerce_offer'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Offer updated.',  'offers-for-woocommerce'),
			2  => __( 'Offer Details updated.',  'offers-for-woocommerce'),
			3  => __( 'Offer Details deleted.',  'offers-for-woocommerce'),
			4  => __( 'Offer updated.',  'offers-for-woocommerce'),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Offer restored to revision from %s',  'offers-for-woocommerce'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Offer set as Pending Status.',  'offers-for-woocommerce'),
			7  => __( 'Offer saved.',  'offers-for-woocommerce'),
			8  => __( 'Offer submitted.',  'offers-for-woocommerce'),
			9  => sprintf(
				__( 'Offer scheduled for: <strong>%1$s</strong>.',  'offers-for-woocommerce'),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i',  'offers-for-woocommerce'), strtotime( $post->post_date ) )
			),
			10 => __( 'Offer draft updated.',  'offers-for-woocommerce'),
            11 => __( 'Offer note added.',  'offers-for-woocommerce')
		);
	
		return $messages;
	}
	
	/**
	 * Filter - Remove meta boxes not needed on edit detail view
	 * @since	0.1.0	 
	 */
	public function remove_woocommerce_offer_meta_boxes() 
	{
		$hidden = array(
			'posttitle', 
			'submitdiv', 
			'categorydiv', 
			'formatdiv', 
			'pageparentdiv', 
			'postimagediv', 
			'tagsdiv-post_tag', 
			'postexcerpt', 
			'slugdiv',
			'trackbacksdiv', 
			'commentstatusdiv', 
			'commentsdiv', 
			'authordiv', 
			'revisionsdiv');
			
		foreach($hidden as $item)
		{
			remove_meta_box( $item, 'woocommerce_offer', 'normal' );
		}
	}
	
	/**
	 * Filter - Remove submenu "Add New"
	 * @since	0.1.0	 
	 * @NOTE:	Removes 'Add New' submenu part from the submenu array
	 */
	public function my_remove_submenus() 
	{
		global $submenu;
		unset($submenu['edit.php?post_type=woocommerce_offer'][10]); // Removes 'Add New' submenu part from the submenu array
	}
	
	/**
	 * Filter - Modify the comments clause - to exclude "woocommerce_offer" post type
	 * @since	0.1.0
	 * @param  array  $clauses
	 * @param  object $wp_comment_query
	 * @return array
	 */
	public function angelleye_ofwc_exclude_cpt_from_comments_clauses( $clauses ) {
            $screen = get_current_screen();
            if ( $screen->id == 'edit-comments' ) {
                global $wpdb;
                $clauses['join'] = "JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID";
                $clauses['where'] .= $wpdb->prepare(" AND $wpdb->posts.post_type <> '%s'", 'woocommerce_offer');
                return $clauses;
            } else {
                return $clauses;
            }
	}
	
	/**
	 * Set custom columns on CPT edit list view
	 * @since	0.1.0
	 */
	public function set_woocommerce_offer_columns($columns) 
	{
            if(!$this->ofw_is_anonymous_communication_enable()) {
                $columns['offer_name'] = __( 'Name', 'offers-for-woocommerce' );
            }
            $columns['offer_product_title'] = __( 'Product', 'offers-for-woocommerce' );
            $columns['offer_amount'] = __( 'Amount', 'offers-for-woocommerce' );
            $columns['offer_price_per'] = __( 'Price Per', 'offers-for-woocommerce' );
            $columns['offer_quantity'] = __( 'Quantity', 'offers-for-woocommerce' );
            return $columns;
	}
	
	/**
	 * Get custom columns data for CPT edit list view
	 * @since	0.1.0
	 */
	public function get_woocommerce_offer_column( $column, $post_id ) 
	{
        $post_status = get_post_status( $post_id );

		switch ( $column ) {
            case 'offer_name' :
                $val = get_post_meta( $post_id , 'offer_name' , true );
                echo stripslashes($val);
                break;
            case 'offer_product_title' :
                $product_id = get_post_meta( $post_id , 'orig_offer_product_id' , true );
                $product_variant_id = get_post_meta( $post_id , 'orig_offer_product_id' , true );

                $product_title = get_the_title($product_id);

                if($product_title)
                {
                    $val = '<a href="post.php?post=' . $product_id . '&action=edit">' . $product_title . '</a>';
                }
                else
                {
                    $val = '<em>' . __('Not Found', 'offers-for-woocommerce' ) . '</em>';
                }

                echo stripslashes($val);
                break;

            case 'offer_quantity' :
                if( $post_status == 'buyercountered-offer' )
                {
                    $val = get_post_meta( $post_id , 'offer_buyer_counter_quantity' , true );
                }
                else
                {
                    $val = get_post_meta( $post_id , 'offer_quantity' , true );
                }
                $val = ($val != '') ? $val : '0';
                echo number_format($val, 2, '.', '');
			break;
				
			case 'offer_price_per' :
                if( $post_status == 'buyercountered-offer' )
                {
                    $val = get_post_meta( $post_id , 'offer_buyer_counter_price_per' , true );
                }
                else
                {
                    $val = get_post_meta( $post_id , 'offer_price_per' , true );
                }
                $val = ($val != '') ? $val : '0';
				echo get_woocommerce_currency_symbol().number_format($val, 2, '.', '');
			break;

			case 'offer_amount' :
                if( $post_status == 'buyercountered-offer' )
                {
                    $val = get_post_meta( $post_id , 'offer_buyer_counter_amount' , true );
                }
                else
                {
                    $val = get_post_meta( $post_id , 'offer_amount' , true );
                }
                $val = ($val != '') ? $val : '0';
                echo get_woocommerce_currency_symbol().number_format($val, 2, '.', '');
            break;
		}
	}	
	
	/**
	 * Filter the custom columns for CPT edit list view to be sortable
	 * @since	0.1.0
	 */
	public function woocommerce_offer_sortable_columns( $columns ) 
	{
        $columns['offer_email'] = 'offer_email';
        $columns['offer_name'] = 'offer_name';
        $columns['offer_product_title'] = 'orig_offer_product_id';
		$columns['offer_price_per'] = 'offer_price_per';
		$columns['offer_quantity'] = 'offer_quantity'; 
		$columns['offer_amount'] = 'offer_amount';
		return $columns;
	}
	
	/*
	 * ADMIN COLUMN - SORTING - ORDERBY
	 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
	 */
	public function woocommerce_offers_list_orderby( $vars )
    {
        // check for orderby var
        if ( !isset( $vars['orderby']) )
        {
            // order by date default
            $vars = array_merge( $vars, array(
                'orderby' => 'post_date',
                'order' => 'desc' ) );
        }
        else {
            if (isset($vars['orderby']) && (($vars['orderby'] == 'date') ))
            {
                $vars['orderby'] = 'post_date';
            }
            if (isset($vars['orderby']) && (($vars['orderby'] == 'offer_amount') || ($vars['orderby'] == 'offer_price_per') || ($vars['orderby'] == 'offer_quantity') || ($vars['orderby'] == 'offer_amount'))) {
                $vars = array_merge($vars, array(
                    'meta_key' => $vars['orderby'],
                    'orderby' => 'meta_value_num'));
            }
            if (isset($vars['orderby']) && (($vars['orderby'] == 'offer_name') || ($vars['orderby'] == 'offer_email'))) {
                $vars = array_merge($vars, array(
                    'meta_key' => $vars['orderby'],
                    'orderby' => 'meta_value'));
            }
        }
		return $vars;
	}
	
	/*
	 * ADD TO QUERY - PULL IN all except 'trash' when viewing 'all' list
	 * @since	0.1.0
	 */
	public function my_pre_get_posts($query) 
	{
		$arg_post_type = get_query_var( 'post_type' );		
		$arg_post_status = get_query_var( 'post_status' );
		$arg_orderby = get_query_var( 'orderby' );

		if ( !$arg_post_status && $arg_post_type == 'woocommerce_offer' ) 
		{
			if( is_admin() && $query->is_main_query() ) 
			{
				$query->set('post_status', array( 'publish','accepted-offer','countered-offer','buyercountered-offer','declined-offer','completed-offer','on-hold-offer' ) );
				if ( !$arg_orderby)
				{
					$query->set('orderby', 'post_date');
					$query->set('order', 'desc');
				}
			}						
		}		
	}

    /**
     * Join posts and postmeta tables
     * @since   1.0.1
     */
    function aeofwc_search_join( $join ) {
        global $wpdb, $screen, $wp;

        $screen = get_current_screen();

        if ( is_search() && $screen->post_type == 'woocommerce_offer' ) {

            $found_blank_s = (isset($_GET['s']) && isset($_GET['orderby'])) ? TRUE : FALSE;
            if($found_blank_s)
            {
                $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
                $current_url = esc_url_raw($current_url);
                $redirect_url = str_replace("&s=&", "&", $current_url);
                wp_redirect($redirect_url);
            }
            $join .='LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
        }

        return $join;
    }

    /**
     * Modify the search query with posts_where
     * @since   1.0.1
     */
    function aeofwc_search_where( $where ) {
        global $pagenow, $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/screen.php');
        $screen = get_current_screen();

        if ( is_search() && $screen->post_type == 'woocommerce_offer' ) {
            $where = preg_replace(
                "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
        }

        return $where;
    }

    /**
     * Prevent duplicates
     * @since   1.0.1
     */
    function aeofwc_search_distinct( $where ) {
        global $wpdb;

        $screen = get_current_screen();

        if ( is_search() && $screen->post_type == 'woocommerce_offer' ) {
            return "DISTINCT";
        }

        return $where;
    }
	
	/**
	 * Filter the "quick edit" action links for CPT edit list view
	 * @since	0.1.0
	 */
	public function remove_quick_edit( $actions ) 
	{
		global $post;
        if( $post && $post->post_type == 'woocommerce_offer' )
		{			
			unset($actions['inline hide-if-no-js']);
			unset($actions['edit']);
			unset($actions['view']);

            if($post->post_status == 'accepted-offer')
            {
                $actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage Offer', 'offers-for-woocommerce') . '</a>';
                $actions['decline-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" title="'. __('Set Offer Status to Declined', 'offers-for-woocommerce'). '" id="woocommerce-offer-post-action-link-decline-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Decline', 'offers-for-woocommerce') . '</a>';
            }
            if($post->post_status == 'countered-offer')
            {
                $actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage&nbsp;Offer') . '</a>';
                $actions['decline-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" title="'. __('Set Offer Status to Declined', 'offers-for-woocommerce'). '" id="woocommerce-offer-post-action-link-decline-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Decline', 'offers-for-woocommerce') . '</a>';
            }
            elseif($post->post_status == 'declined-offer')
            {
                $actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="'. __('Offer Details', 'offers-for-woocommerce'). '" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage Offer', 'offers-for-woocommerce') . '</a>';
            }
            elseif($post->post_status == 'on-hold-offer')
            {
                $actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="'. __('Offer Details', 'offers-for-woocommerce'). '" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage Offer', 'offers-for-woocommerce') . '</a>';
            }
            elseif($post->post_status == 'expired-offer')
            {
                $actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="'. __('Offer Details', 'offers-for-woocommerce'). '" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage Offer', 'offers-for-woocommerce') . '</a>';
            }
            elseif($post->post_status == 'completed-offer')
            {
                unset($actions['trash']);
            }
            elseif($post->post_status == 'trash')
            {
            }
            elseif($post->post_status == 'publish' || $post->post_status == 'buyercountered-offer')
            {
                $actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="'. __('Offer Details', 'offers-for-woocommerce'). '" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Make Counter Offer', 'offers-for-woocommerce') . '</a>';
                $actions['accept-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-accept" title="'. __('Set Offer Status to Accepted', 'offers-for-woocommerce'). '" id="woocommerce-offer-post-action-link-accept-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Accept', 'offers-for-woocommerce') . '</a>';
                $actions['decline-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" title="'. __('Set Offer Status to Declined', 'offers-for-woocommerce'). '" id="woocommerce-offer-post-action-link-decline-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Decline', 'offers-for-woocommerce') . '</a>';
            }
		}
		return $actions;
	}

    /**
     * Register custom post status type -- Accepted Offer
     * @since	0.1.0
     */
    public function my_custom_post_status_accepted()
    {
        $args = array(
            'label'                     => _x( 'accepted-offer', __('Accepted Offer', 'offers-for-woocommerce') ),
            'label_count'               => _n_noop( __('Accepted (%s)', 'offers-for-woocommerce'),  __('Accepted (%s)', 'offers-for-woocommerce') ),
            'public'                    => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'accepted-offer', $args );
    }

    /**
     * Register custom post status type -- Countered Offer
     * @since	0.1.0
     */
    public function my_custom_post_status_countered()
    {
        $args = array(
            'label'                     => _x( 'countered-offer', __('Countered Offer', 'offers-for-woocommerce') ),
            'label_count'               => _n_noop( __('Countered (%s)', 'offers-for-woocommerce'),  __('Countered (%s)', 'offers-for-woocommerce') ),
            'public'                    => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'countered-offer', $args );
    }

    /**
     * Register custom post status type -- Offer On Hold
     * @since	1.0.1
     */
    public function my_custom_post_status_on_hold()
    {
        $args = array(
            'label'                     => _x( 'on-hold-offer', __('On Hold', 'offers-for-woocommerce') ),
            'label_count'               => _n_noop( __('On Hold (%s)', 'offers-for-woocommerce'),  __('On Hold (%s)', 'offers-for-woocommerce') ),
            'public'                    => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'on-hold-offer', $args );
    }

    /**
     * Register custom post status type -- Offer Expired
     * @since	1.0.1
     */
    public function my_custom_post_status_expired()
    {
        $args = array(
            'label'                     => _x( 'expired-offer', __('Expired', 'offers-for-woocommerce') ),
            'label_count'               => _n_noop( __('Expired (%s)', 'offers-for-woocommerce'),  __('Expired(%s)', 'offers-for-woocommerce') ),
            'public'                    => true,
            'show_in_admin_all_list'    => false,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'expired-offer', $args );
    }

    /**
     * Register custom post status type -- Buyer Countered Offer
     * @since	0.1.0
     */
    public function my_custom_post_status_buyer_countered()
    {
        $args = array(
            'label'                     => _x( 'buyercountered-offer', __('Buyer Countered Offer', 'offers-for-woocommerce') ),
            'label_count'               => _n_noop( __('Buyer Countered (%s)', 'offers-for-woocommerce'),  __('Buyer Countered (%s)', 'offers-for-woocommerce') ),
            'public'                    => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'buyercountered-offer', $args );
    }
	
	/**
	 * Register custom post status type -- Declined Offer
	 * @since	0.1.0
	 */
	public function my_custom_post_status_declined() 
	{
		$args = array(
			'label'                     => _x( 'declined-offer', __('Declined Offer', 'offers-for-woocommerce') ),
			'label_count'               => _n_noop( __('Declined (%s)', 'offers-for-woocommerce'),  __('Declined (%s)', 'offers-for-woocommerce') ),
			'public'                    => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'exclude_from_search'       => false,
		);
		register_post_status( 'declined-offer', $args );			
	}
	
	/**
	 * Register custom post status type -- Completed Offer
	 * @since	0.1.0
	 */
	public function my_custom_post_status_completed() 
	{
		$args = array(
			'label'                     => _x( 'completed-offer', __('Completed Offer', 'offers-for-woocommerce') ),
			'label_count'               => _n_noop( __('Completed (%s)', 'offers-for-woocommerce'),  __('Completed (%s)', 'offers-for-woocommerce') ),
			'public'                    => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'exclude_from_search'       => false,
		);
		register_post_status( 'completed-offer', $args );
	}

	/**
	 * Filter - Display post status values on edit list view with customized html elements
	 * @since	0.1.0
	 */
	public function jc_display_archive_state( $states ) 
	{
		global $post;

		$screen = get_current_screen();

		if (!empty($screen) && $screen->post_type == 'woocommerce_offer' )
		{
            if($post->post_status == 'accepted-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon accepted" title="'. __('Offer Status: Accepted', 'offers-for-woocommerce'). '">'. __('Accepted', 'offers-for-woocommerce'). '</i></div>');
            }
            elseif($post->post_status == 'countered-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon countered" title="'. __('Offer Status: Countered', 'offers-for-woocommerce'). '">'. __('Countered', 'offers-for-woocommerce'). '</i></div>');
            }
            elseif($post->post_status == 'buyercountered-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon buyercountered" title="'. __('Offer Status: Buyer Countered', 'offers-for-woocommerce'). '">'. __('Buyer Countered', 'offers-for-woocommerce'). '</i></div>');
            }
			elseif($post->post_status == 'publish'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon pending" title="'. __('Offer Status: Pending', 'offers-for-woocommerce'). '">'. __('Pending', 'offers-for-woocommerce'). '</i></div>');
			}
			elseif($post->post_status == 'trash'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon trash" title="'. __('Offer Status: Trashed', 'offers-for-woocommerce'). '">'. __('Trashed', 'offers-for-woocommerce'). '</i></div>');
			}
			elseif($post->post_status == 'completed-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon completed" title="'. __('Offer Status: Completed', 'offers-for-woocommerce'). '">'. __('Completed', 'offers-for-woocommerce'). '</i></div>');
			}
            elseif($post->post_status == 'declined-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon declined" title="'. __('Offer Status: Declined', 'offers-for-woocommerce'). '">'. __('Declined', 'offers-for-woocommerce'). '</i></div>');
            }
            elseif($post->post_status == 'on-hold-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon on-hold" title="'. __('Offer Status: On Hold', 'offers-for-woocommerce'). '">'. __('On Hold', 'offers-for-woocommerce'). '</i></div>');
            }
            elseif($post->post_status == 'expired-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon expired" title="'. __('Offer Status: Expired', 'offers-for-woocommerce'). '">'. __('Expired', 'offers-for-woocommerce'). '</i></div>');
            }
			else
			{
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon" title="'. __('Offer Status:', 'offers-for-woocommerce'). '&nbsp;'.ucwords($post->post_status).'">'.ucwords($post->post_status).'</i></div>');
			}

            if ( ! empty( $states ) ) {
                $state_count = count( $states );
                $i = 0;
                echo '';
                foreach ( $states as $state ) {
                    ++$i;
                    ( $i == $state_count ) ? $sep = '' : $sep = ', ';
                    echo "<span class='post-state'>$state$sep</span>";
                }
            }
            return;
		} else {
                    return $states;
                }
	}
	
	/**
	 * Filter - Relabel display of post type "publish" for our CPT on edit list view
	 * @since	0.1.0
	 */
	public function translate_published_post_label($screen) 
	{
		if ( $screen->post_type == 'woocommerce_offer') 
		{
			add_filter('gettext',  array( $this, 'my_get_translated_text_publish' ) );
			add_filter('ngettext', array( $this, 'my_get_translated_text_publish' ) );
		}

        /**
         * Auto-Expire offers with expire date past
         * @since   1.0.1
         */
        if ( "edit-woocommerce_offer" == $screen->id )
        {
            global $wpdb;

            $target_now_date = date("Y-m-d H:i:s", current_time('timestamp', 0 ));

            $expired_offers = $wpdb->get_results($wpdb->prepare("SELECT post_id, meta_value FROM $wpdb->postmeta WHERE `meta_key` = '%s' AND `meta_value` <> ''", 'offer_expiration_date'), 'ARRAY_A');
            if (($expired_offers) && !empty($expired_offers))
            {
                foreach ($expired_offers as $v)
                {
                    $offer_expire_date_formatted = date("Y-m-d 23:59:59", strtotime($v['meta_value']));
                    if( $offer_expire_date_formatted <= $target_now_date )
                    {
                        $post_status = get_post_status( $v['post_id']);
                        if( $post_status && $post_status != 'trash' ) {
                            $target_post = array(
                                'ID' => $v['post_id'],
                                'post_status' => 'expired-offer'
                            );
                            wp_update_post($target_post);
                        }
                    }
                }
            }
        }
	}
	
	/**
	 * Translate "Published" language to "Pending"
	 * @since	0.1.0
	 */
	public function my_get_translated_text_publish($translated)
	{
		$translated = str_ireplace('Published',  'Pending',  $translated);
		return $translated;
	}
	
	/**
	 * Filter - Unset the "edit" option for edit list view
	 * @since	0.1.0
	 */
	public function my_custom_bulk_actions($actions)
	{
		unset($actions['edit']);
		return $actions;
	}

    /**
     * Action - Add meta box - "Offer Comments"
     * @since	0.1.0
     */
    public function add_meta_box_offer_comments()
    {
        $screens = array('woocommerce_offer');
        foreach($screens as $screen)
        {
            add_meta_box(
                'section_id_offer_comments',
                __( 'Offer Activity Log', 'offers-for-woocommerce' ),
                array( $this, 'add_meta_box_offer_comments_callback' ),
                $screen,
                'side','default'
            );
        }
    }

    /**
     * Callback - Action - Add meta box - "Offer Comments"
     * Output hmtl for "Offer Comments" meta box
     * @since	0.1.0
     * @param WP_Post $post The object for the current post/page
     */
    public function add_meta_box_offer_comments_callback( $post )
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM $wpdb->commentmeta INNER JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID WHERE $wpdb->commentmeta.meta_value = '%d' ORDER BY comment_date desc", $post->ID );
        $offer_comments = $wpdb->get_results($query);

        /*
		 * Output html for Offer Comments loop
		 */
        include_once('views/meta-panel-comments.php');
    }

    /**
     * Action - Add meta box - "Offer Summary"
     * @since	0.1.0
     */
    public function add_meta_box_offer_summary()
    {
        $screens = array('woocommerce_offer');
        foreach($screens as $screen)
        {
            add_meta_box(
                'section_id_offer_summary',
                __( 'Offer Details', 'offers-for-woocommerce' ),
                array( $this, 'add_meta_box_offer_summary_callback' ),
                $screen,
                'normal', 'high'
            );
        }
    }

    /**
     * Callback - Action - Add meta box - "Offer Summary"
     * Output hmtl for "Offer Summary" meta box
     * @since	0.1.0
     * @param WP_Post $post The object for the current post/page
     */
    public function add_meta_box_offer_summary_callback( $post )
    {
        global $post, $wpdb;

		do_action('before_offer_summary_meta_box', $post);

        if($post->ID)
        {
            $postmeta = get_post_meta($post->ID);
            $currency_symbol = get_woocommerce_currency_symbol();

            // Add an nonce field so we can check for it later.
            wp_nonce_field( 'woocommerce_offer_summary_metabox', 'woocommerce_offer_summary_metabox_noncename' );

            /*
             * Use get_post_meta() to retrieve an existing value
             * from the database and use the value for the form.
             */
            $current_status_value = get_post_status( $post->ID);

            /*
             * Set default
             */
            if (!isset($current_status_value))
            {
                $current_status_value = 'publish';
            }

            // Lookup product data
            $product_id = $postmeta['offer_product_id'][0];
            $product_variant_id = ( isset( $postmeta['offer_variation_id'][0] ) && $postmeta['offer_variation_id'][0] != '' ) ? $postmeta['offer_variation_id'][0] : '';

            $_product = wc_get_product($product_id);

            if($_product != false) {
            
                if( $product_variant_id )
                {
                    $_product_variant = wc_get_product($product_variant_id);
                    
                    $_product_variant_managing_stock = ( $_product_variant->managing_stock() == 'parent' ) ? true : false;

                    $_product_sku = ( $_product_variant->get_sku() ) ? $_product_variant->get_sku() : $_product->get_sku();
                    $_product_permalink = $_product_variant->get_permalink();
                    $_product_attributes_resulat = $_product_variant->get_variation_attributes();
                    foreach ( $_product_attributes_resulat as $name => $attribute ) {
                            $_product_attributes[] = wc_attribute_label( str_replace( 'attribute_', '', $name ) ) . ': <strong>' . $attribute . '</strong>';
                    }
                    $_product_regular_price = ( $_product_variant->get_regular_price() ) ? $_product_variant->get_regular_price() : $_product->get_regular_price();
                    $_product_sale_price = ( $_product_variant->get_sale_price() ) ? $_product_variant->get_sale_price() : $_product->get_sale_price();

                    $_product_managing_stock = ( $_product_variant->managing_stock() ) ? $_product_variant->managing_stock() : $_product->managing_stock();
                    $_product_stock = ( $_product_variant_managing_stock ) ? $_product_variant->get_total_stock() : $_product->get_total_stock();
                    $_product_in_stock = ( $_product_variant_managing_stock ) ? $_product_variant->has_enough_stock($postmeta['offer_quantity'][0]) : $_product->has_enough_stock($postmeta['offer_quantity'][0]);
                    $_product_backorders_allowed = ( $_product_variant_managing_stock ) ? $_product_variant->backorders_allowed() : $_product->backorders_allowed();
                    $_product_backorders_require_notification = ( $_product_variant_managing_stock ) ? $_product_variant->backorders_require_notification() : $_product->backorders_require_notification();
                    $_product_formatted_name = $_product_variant->get_formatted_name();
                    $_product_image = ( $_product_variant->get_image( 'shop_thumbnail') ) ? $_product_variant->get_image( 'shop_thumbnail') : $_product->get_image( 'shop_thumbnail');
                }
                else
                {
                    $_product_sku = $_product->get_sku();
                    $_product_attributes = array();
                    $_product_permalink = $_product->get_permalink();
                    $_product_regular_price = $_product->get_regular_price();
                    $_product_sale_price = $_product->get_sale_price();
                    $_product_managing_stock = $_product->managing_stock();
                    $_product_stock = $_product->get_total_stock();
                    $_product_in_stock = $_product->has_enough_stock($postmeta['offer_quantity'][0]);
                    $_product_backorders_allowed = $_product->backorders_allowed();
                    $_product_backorders_require_notification = $_product->backorders_require_notification();
                    $_product_formatted_name = $_product->get_formatted_name();
                    $_product_image = $_product->get_image( 'shop_thumbnail');

                    // set error message if product not found...
                }

                /**
                 * Set default expiration date on 'pending' offer expiration date input field
                 * @since   1.2.0
                 */
                if($current_status_value == 'publish')
                {
                    // get offers options - general
                    $default_expire_date = '';
                    $options_general = get_option('offers_for_woocommerce_options_general');
                    if(!empty($options_general['general_setting_default_expire_days']))
                    {
                        $current_time = date("Y-m-d H:i:s", current_time('timestamp', 0 ));
                        $default_expire_days = str_replace(",","", $options_general['general_setting_default_expire_days']);
                        $default_expire_date = ($default_expire_days != '') ? date("m/d/Y", strtotime( $current_time .' + '. $default_expire_days .' days') ) : '';
                    }
                    if($default_expire_date != '')
                    {
                        $postmeta['offer_expiration_date'] = array($default_expire_date);
                    }
                }
            $offer_inventory_msg = '<strong>Notice: </strong>' . __('Product stock is lower than offer quantity!', 'offers-for-woocommerce');
            $show_offer_inventory_msg = ( $_product_in_stock ) ? FALSE : TRUE;
                
                $offer_order_meta = array();
                // Check for 'offer_order_id'
                if( isset( $postmeta['offer_order_id'][0] ) && is_numeric( $postmeta['offer_order_id'][0] ) )
                {
                    $order_id = $postmeta['offer_order_id'][0];

                    // Set order meta data array
                    
                    $offer_order_meta['Order ID'] = '<a href="post.php?post='. $order_id . '&action=edit">' . '#' . $order_id . '</a>';

                    // Get Order
                    $order = new WC_Order( $order_id );
                    if($order->post)
                    {
                        $offer_order_meta['Order Date'] = $order->post->post_date;
                        $offer_order_meta['Order Status'] = ucwords($order->get_status() );
                    }
                    else
                    {
                        $offer_order_meta['Order ID'].= '<br /><small><strong>Notice: </strong>' . __('Order not found; may have been deleted', 'offers-for-woocommerce') . '</small>';
                    }
                    $offer_order_meta['Order Date'] = $order->post->post_date;
                    $offer_order_meta['Order Status'] = ucwords($order->get_status() );
                }
                else
                {
                    $offer_order_meta['Order ID'] = '<br /><small><strong>Notice: </strong>' . __('Order not found; may have been deleted', 'offers-for-woocommerce') . '</small>';
                }

                // set author_data
                $author_data = get_userdata($post->post_author);

                // set author offer counts
                $author_counts = array();
                if($author_data)
                {
                    // count offers by author id

                    $post_type = 'woocommerce_offer';

                    $args = array($post_type,'trash', $post->post_author);
                    $count_all = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '%s' AND post_status != '%s' AND post_author = '%s'", $args ) );

                    $args = array($post_type,'publish', $post->post_author);
                    $count_pending = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '%s' AND post_status = '%s' AND post_author = '%s'", $args ) );

                    $args = array($post_type,'accepted-offer', $post->post_author);
                    $count_accepted = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '%s' AND post_status = '%s' AND post_author = '%s'", $args ) );

                    $args = array($post_type,'countered-offer', $post->post_author);
                    $count_countered = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '%s' AND post_status = '%s' AND post_author = '%s'", $args ) );

                    $args = array($post_type,'buyercountered-offer', $post->post_author);
                    $count_buyer_countered = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '%s' AND post_status = '%s' AND post_author = '%s'", $args ) );

                    $args = array($post_type,'declined-offer', $post->post_author);
                    $count_declined = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '%s' AND post_status = '%s' AND post_author = '%s'", $args ) );

                    $args = array($post_type,'completed-offer', $post->post_author);
                    $count_completed = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '%s' AND post_status = '%s' AND post_author = '%s'", $args ) );

                    $args = array($post_type,'on-hold-offer', $post->post_author);
                    $count_on_hold = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '%s' AND post_status = '%s' AND post_author = '%s'", $args ) );

                    $args = array($post_type,'expired-offer', $post->post_author);
                    $count_expired = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '%s' AND post_status = '%s' AND post_author = '%s'", $args ) );

                    $author_counts['all'] = apply_filters( 'get_usernumposts', $count_all, $post->post_author );
                    $author_counts['pending'] = apply_filters( 'get_usernumposts', $count_pending, $post->post_author );
                    $author_counts['accepted'] = apply_filters( 'get_usernumposts', $count_accepted, $post->post_author );
                    $author_counts['countered'] = apply_filters( 'get_usernumposts', $count_countered, $post->post_author );
                    $author_counts['buyercountered'] = apply_filters( 'get_usernumposts', $count_buyer_countered, $post->post_author );
                    $author_counts['declined'] = apply_filters( 'get_usernumposts', $count_declined, $post->post_author );
                    $author_counts['completed'] = apply_filters( 'get_usernumposts', $count_completed, $post->post_author );
                    $author_counts['on_hold'] = apply_filters( 'get_usernumposts', $count_on_hold, $post->post_author );
                    $author_counts['expired'] = apply_filters( 'get_usernumposts', $count_expired, $post->post_author );

                    $author_data->offer_counts = $author_counts;
                }
            
                $query_counter_offers_history = $wpdb->prepare("SELECT * FROM $wpdb->commentmeta INNER JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID WHERE $wpdb->commentmeta.meta_value = '%d' AND $wpdb->comments.comment_type = 'offers-history' ORDER BY comment_date ASC", $post->ID );
                $query_counter_offers_history_result = $wpdb->get_results($query_counter_offers_history);
               
            } else {
                wp_die( '<strong>ERROR</strong>: ' . __( 'Product not found for this offer.', 'offers-for-woocommerce' ) );
            }
            /**
             * Output html for Offer Comments loop
             */
            $is_anonymous_communication_enable = $this->ofw_is_anonymous_communication_enable();
            include_once('views/meta-panel-summary.php');
        }

		do_action('after_offer_summary_meta_box', $post);
    }

    /**
     * Action - Add meta box - "Add Offer Note"
     * @since	0.1.0
     */
    public function add_meta_box_offer_addnote()
    {
        $screens = array('woocommerce_offer');
        foreach($screens as $screen)
        {
            add_meta_box(
                'section_id_offer_addnote',
                __( 'Add Offer Note', 'offers-for-woocommerce' ),
                array( $this, 'add_meta_box_offer_addnote_callback' ),
                $screen,
                'side','low'
            );
        }
    }

    /**
     * Callback - Action - Add meta box - "Add Offer Note"
     * Output hmtl for "Add Offer Note" meta box
     * @since	0.1.0
     * @param WP_Post $post The object for the current post/page
     */
    public function add_meta_box_offer_addnote_callback( $post )
    {
        /*
		 * Output html for Offer Add Note form
		 */
        include_once('views/meta-panel-add-note.php');
    }
	
	/**
	 * When the post is saved, saves our custom data
	 * @since	0.1.0
	 * @param int $post_id The ID of the post being saved
	 */
	public function myplugin_save_meta_box_data($post_id)
	{
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
	
		// If this is an autosave, our form has not been submitted, so we don't want to do anything
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check if our nonce is set
		if(!isset($_POST['woocommerce_offer_summary_metabox_noncename']))
		{
			return;
		}

        // Verify that the nonce is valid
        if(!wp_verify_nonce($_POST['woocommerce_offer_summary_metabox_noncename'], 'woocommerce_offer'.$post_id))
        {
            return;
        }
        
        $user = wp_get_current_user();
        $allowed_roles = array('vendor', 'administrator', 'shop_manager', 'seller');

        // Check the user's permissions
        if(isset($_POST['post_type']) && 'woocommerce_offer' == $_POST['post_type'])
        {
            if (!current_user_can('edit_page', $post_id) || !array_intersect($allowed_roles, $user->roles ))
            {
                return;
            }
        }

        /*
         * OK, its safe for us to save the data now
         */

        // Save 'final offer' post meta
        $offer_final_offer = (isset($_POST['offer_final_offer']) && $_POST['offer_final_offer'] == '1') ? '1' : '0';
        update_post_meta( $post_id, 'offer_final_offer', $offer_final_offer );

        // Save 'offer_expiration_date' post meta
        $offer_expiration_date = (isset($_POST['offer_expiration_date']) && $_POST['offer_expiration_date'] != '') ? $_POST['offer_expiration_date'] : '';
        update_post_meta( $post_id, 'offer_expiration_date', $offer_expiration_date );

        // Get current data for Offer after saved
        $post_data = get_post($post_id);
        // Filter Post Status Label
        $post_status_text = (strtolower($post_data->post_status) == 'publish') ? 'Pending' : $post_data->post_status;
        $post_status_text = ucwords(str_replace("-", " ", str_replace("offer", " ", strtolower($post_status_text))));

        // set update notes
        $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? $_POST['angelleye_woocommerce_offer_status_notes'] : '';

        // set offer expiration date
        $offer_expire_date = get_post_meta($post_id, 'offer_expiration_date', true);
        
        $offer_shipping_cost = (isset($_POST['offer_shipping_cost']) && $_POST['offer_shipping_cost'] != '0.00') ? $_POST['offer_shipping_cost'] : 0.00;
        update_post_meta( $post_id, 'offer_shipping_cost', $offer_shipping_cost );

        // Accept Offer
        if($post_data->post_status == 'accepted-offer' && isset($_POST['post_previous_status']) && $_POST['post_previous_status'] != 'accepted-offer')
        {
            /**
             * Email customer accepted email template
             * @since   0.1.0
             */
            // set recipient email
            $recipient = get_post_meta($post_id, 'offer_email', true);
            $offer_id = $post_id;
            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
            $offer_name = get_post_meta($post_id, 'offer_name', true);
            $offer_email = $recipient;

            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
            
            $product = ( $variant_id ) ? wc_get_product($variant_id) : wc_get_product( $product_id );

            // if buyercountered-offer previous then use buyer counter values
            $is_offer_buyer_countered_status = ( $_POST['post_previous_status'] == 'buyercountered-offer' ) ? true : false;

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'orig_offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'orig_offer_price_per', true);
            $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
            
            $product_total = number_format(round($product_qty * $product_price_per, 2), 2, '.', '');

            // Update qty/price/total meta values
            update_post_meta( $post_id, 'offer_quantity', $product_qty );
            update_post_meta( $post_id, 'offer_price_per', $product_price_per );
            update_post_meta( $post_id, 'offer_shipping_cost', $product_shipping_cost );
            update_post_meta( $post_id, 'offer_amount', $product_total );

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

            if( $variant_id )
            {
                if ( $product->get_sku() ) {
                    $identifier = $product->get_sku();
                } else {
                    $identifier = '#' . $product->variation_id;
                }

                $attributes = $product->get_variation_attributes();
                $extra_data = ' &ndash; ' . implode( ', ', $attributes );
                $offer_args['product_title_formatted'] = sprintf( __( '%s &ndash; %s%s', 'offers-for-woocommerce' ), $identifier, $product->get_title(), $extra_data );
            }
            else
            {
                $offer_args['product_title_formatted'] = $product->get_formatted_name();
            }

            $offer_args['offer_expiration_date'] = ($offer_expire_date) ? $offer_expire_date : FALSE;

            // the email we want to send
            $email_class = 'WC_Accepted_Offer_Email';

            // load the WooCommerce Emails
            $wc_emails = new WC_Emails();
            $emails = $wc_emails->get_emails();

            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

            // set plugin slug in email class
            $new_email->plugin_slug = 'offers-for-woocommerce';

            // define email template/path (html)
            $new_email->template_html  = 'woocommerce-offer-accepted.php';
            $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

            // define email template/path (plain)
            $new_email->template_plain  = 'woocommerce-offer-accepted.php';
            $new_email->template_plain_path = plugin_dir_path(__FILE__). 'includes/emails/plain/';

            $new_email->trigger($offer_args);
        }

        // Offer On Hold
        if($post_data->post_status == 'on-hold-offer' && isset($_POST['post_previous_status']) && $_POST['post_previous_status'] != 'on-hold-offer')
        {
            /**
             * Email customer offer on hold email template
             * @since   1.0.1
             */
            // set recipient email
            $recipient = get_post_meta($post_id, 'offer_email', true);
            $offer_id = $post_id;
            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
            $offer_name = get_post_meta($post_id, 'offer_name', true);
            $offer_email = $recipient;

            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
            
            $product = ( $variant_id ) ? wc_get_product( $variant_id ) : wc_get_product( $product_id );

            // if buyercountered-offer previous then use buyer counter values
            $is_offer_buyer_countered_status = ( $_POST['post_previous_status'] == 'buyercountered-offer' ) ? true : false;

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
            $product_total = number_format(round($product_qty * $product_price_per, 2), 2, '.', '');
            
            // if buyercountered-offer status, update postmeta values for quantity,price,amount
            if( $is_offer_buyer_countered_status )
            {
                update_post_meta( $post_id, 'offer_quantity', $product_qty );
                update_post_meta( $post_id, 'offer_price_per', $product_price_per );
                update_post_meta( $post_id, 'offer_shipping_cost', $product_shipping_cost );
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
                'product_shipping' => $product_price_per,
                'product_total' => $product_total,
                'offer_notes' => $offer_notes
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
                $offer_args['product_title_formatted'] = sprintf( __( '%s &ndash; %s%s', 'offers-for-woocommerce' ), $identifier, $product->get_title(), $extra_data );
            }
            else
            {
                $offer_args['product_title_formatted'] = $product->get_formatted_name();
            }

            // the email we want to send
            $email_class = 'WC_Offer_On_Hold_Email';

            // load the WooCommerce Emails
            $wc_emails = new WC_Emails();
            $emails = $wc_emails->get_emails();

            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

            // set plugin slug in email class
            $new_email->plugin_slug = 'offers-for-woocommerce';

            // define email template/path (html)
            $new_email->template_html  = 'woocommerce-offer-on-hold.php';
            $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

            // define email template/path (plain)
            $new_email->template_plain  = 'woocommerce-offer-on-hold.php';
            $new_email->template_plain_path = plugin_dir_path(__FILE__). 'includes/emails/plain/';

            $new_email->trigger($offer_args);
        }

        // Counter Offer
        if($post_data->post_status == 'countered-offer')
        {
            // set updated offer values
            $offer_quantity = (isset($_POST['offer_quantity']) && $_POST['offer_quantity'] != '') ? str_replace(",","", $_POST['offer_quantity']) : '';
            $offer_price_per = (isset($_POST['offer_price_per']) && $_POST['offer_price_per'] != '') ? str_replace(",","", $_POST['offer_price_per']) : '';
            $offer_shipping_cost = (isset($_POST['offer_shipping_cost']) && $_POST['offer_shipping_cost'] != '0.00') ? str_replace(",","", $_POST['offer_shipping_cost']) : 0.00;
            $offer_total = number_format(round($offer_quantity * $offer_price_per, 2), 2, '.', '');

            /**
             * Update Counter Offer post meta values
             */
            update_post_meta( $post_id, 'offer_quantity', $offer_quantity );
            update_post_meta( $post_id, 'offer_price_per', $offer_price_per );
            update_post_meta( $post_id, 'offer_shipping_cost', $offer_shipping_cost );
            update_post_meta( $post_id, 'offer_amount', $offer_total );

            /**
             * Email customer countered email template
             * @since   0.1.0
             */
            // set recipient email
            $recipient = get_post_meta($post_id, 'offer_email', true);
            $offer_id = $post_id;
            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
            $offer_name = get_post_meta($post_id, 'offer_name', true);
            $offer_email = $recipient;

            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
            
            $product = ( $variant_id ) ? wc_get_product( $variant_id ) : wc_get_product( $product_id );

            $product_qty = get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = get_post_meta($post_id, 'offer_price_per', true);
            $offer_shipping_cost = get_post_meta( $post_id, 'offer_shipping_cost', true );
            $product_total = get_post_meta($post_id, 'offer_amount', true);

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
                'product_shipping_cost' => $offer_shipping_cost,
                'product_total' => $product_total,
                'offer_notes' => $offer_notes,
                'final_offer' => $offer_final_offer
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
                $offer_args['product_title_formatted'] = sprintf( __( '%s &ndash; %s%s', 'offers-for-woocommerce' ), $identifier, $product->get_title(), $extra_data );
            }
            else
            {
                $offer_args['product_title_formatted'] = $product->get_formatted_name();
            }

            $offer_args['offer_expiration_date'] = ($offer_expire_date) ? $offer_expire_date : FALSE;

            // the email we want to send
            $email_class = 'WC_Countered_Offer_Email';

            // load the WooCommerce Emails
            $wc_emails = new WC_Emails();
            $emails = $wc_emails->get_emails();

            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

            // set plugin slug in email class
            $new_email->plugin_slug = 'offers-for-woocommerce';

            // define email template/path (html)
            $new_email->template_html  = 'woocommerce-offer-countered.php';
            $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

            // define email template/path (plain)
            $new_email->template_plain  = 'woocommerce-offer-countered.php';
            $new_email->template_plain_path = plugin_dir_path(__FILE__). 'includes/emails/plain/';

            $new_email->trigger($offer_args);
        }

        // Decline Offer
        if($post_data->post_status == 'declined-offer' && isset($_POST['post_previous_status']) && $_POST['post_previous_status'] != 'declined-offer')
        {
            /**
             * Email customer declined email template
             * @since   0.1.0
             */
            // set recipient email
            $recipient = get_post_meta($post_id, 'offer_email', true);
            $offer_id = $post_id;
            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
            $offer_name = get_post_meta($post_id, 'offer_name', true);
            $offer_email = $recipient;
            $coupon_code = ( isset($_POST["ofw_coupon_list"]) && !empty($_POST["ofw_coupon_list"]) ) ? $_POST["ofw_coupon_list"] : '';
            
            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
            
            $product = ( $variant_id ) ? wc_get_product( $variant_id ) : wc_get_product( $product_id );

            // if buyercountered-offer previous then use buyer counter values
            $is_offer_buyer_countered_status = ( $_POST['post_previous_status'] == 'buyercountered-offer' ) ? true : false;

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
            $product_total = number_format(round($product_qty * $product_price_per, 2), 2, '.', '');
           
            // if buyercountered-offer status, update postmeta values for quantity,price,amount
            if( $is_offer_buyer_countered_status )
            {
                update_post_meta( $post_id, 'offer_quantity', $product_qty );
                update_post_meta( $post_id, 'offer_price_per', $product_price_per );
                update_post_meta( $post_id, 'offer_shipping_cost', $product_shipping_cost );
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
                'product_shipping_cost' => $product_shipping_cost,
                'product_total' => $product_total,
                'offer_notes' => $offer_notes,
                'coupon_code' => $coupon_code
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
                $offer_args['product_title_formatted'] = sprintf( __( '%s &ndash; %s%s', 'offers-for-woocommerce' ), $identifier, $product->get_title(), $extra_data );
            }
            else
            {
                $offer_args['product_title_formatted'] = $product->get_formatted_name();
            }

            // the email we want to send
            $email_class = 'WC_Declined_Offer_Email';

            // load the WooCommerce Emails
            $wc_emails = new WC_Emails();
            $emails = $wc_emails->get_emails();

            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

            // set plugin slug in email class
            $new_email->plugin_slug = 'offers-for-woocommerce';

            // define email template/path (html)
            $new_email->template_html  = 'woocommerce-offer-declined.php';
            $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

            // define email template/path (plain)
            $new_email->template_plain  = 'woocommerce-offer-declined.php';
            $new_email->template_plain_path = plugin_dir_path(__FILE__). 'includes/emails/plain/';

            $new_email->trigger($offer_args);
        }

        // Insert WP comment
        $comment_text = "<span>". __('Updated - Status:', 'offers-for-woocommerce'). "&nbsp;</span>";
        $comment_text.= $post_status_text;

        // include update notes
        if(isset($offer_notes) && $offer_notes != '')
        {
            $comment_text.= '</br>'. nl2br($offer_notes);
        }

        $data = array(
            'comment_post_ID' => '',
            'comment_author' => 'admin',
            'comment_author_email' => '',
            'comment_author_url' => '',
            'comment_content' => $comment_text,
            'comment_type' => 'offers-history',
            'comment_parent' => 0,
            'user_id' => get_current_user_id(),
            'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
            'comment_agent' => '',
            'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
            'comment_approved' => 'post-trashed',
        );
        $new_comment_id = wp_insert_comment( $data );

        // insert comment meta
        if( $new_comment_id )
        {
            add_comment_meta( $new_comment_id, 'angelleye_woocommerce_offer_id', $post_id, true );
            if($post_data->post_status == 'countered-offer') {
                add_comment_meta( $new_comment_id, 'offer_quantity', $offer_quantity, true );
                add_comment_meta( $new_comment_id, 'offer_amount', $offer_total, true );
                add_comment_meta( $new_comment_id, 'offer_price_per', $offer_price_per, true );
                add_comment_meta( $new_comment_id, 'offer_status', '3', true );
            }
        }
	}

	/**
	 * Initialize the plugin options setup 
	 * Adds Options, Sections and Fields
	 * Registers Settings
	 * @since	0.1.0
	 * @NOTE:	This function is registered with the "admin_init" hook
	 */
	public function angelleye_ofwc_intialize_options() 
	{
		/**
		 * Add option - 'General Settings'
		 */
        //the default options
        $offers_for_woocommerce_options_general = array(
            'general_setting_enable_make_offer_btn_frontpage' => '1',
            'general_setting_enable_make_offer_btn_catalog' => '1'
        );

        //check to see if present already
        if(!get_option('offers_for_woocommerce_options_general')) {
            //option not found, add new
            add_option('offers_for_woocommerce_options_general', $offers_for_woocommerce_options_general);
        }

		/**
		 * Add option - 'Display Settings'
		 */
        //the default options
        $offers_for_woocommerce_options_display = array(
            'display_setting_make_offer_form_field_offer_total' => '1',
            'display_setting_make_offer_form_field_offer_company_name' => '1',
            'display_setting_make_offer_form_field_offer_phone' => '1',
            'display_setting_make_offer_form_field_offer_notes' => '1'
        );

        //check to see if present already
        if(!get_option('offers_for_woocommerce_options_display')) {
            //option not found, add new
            add_option('offers_for_woocommerce_options_display', $offers_for_woocommerce_options_display);
        }

		/**
		 * Register setting - 'General Settings'
		 */	
		register_setting(
			'offers_for_woocommerce_options_general', // Option group
			'offers_for_woocommerce_options_general', // Option name
			'' // Validate
		);

		/**
		 * Register setting - 'Display Settings'
		 */
		register_setting(
			'offers_for_woocommerce_options_display', // Option group
			'offers_for_woocommerce_options_display', // Option name
			'' // Validate
		);
		
		/**
		 * Add section - 'General Settings'
		 */
		add_settings_section(
			'general_settings', // ID
			'', // Title
			array( $this, 'offers_for_woocommerce_options_page_intro_text' ), // Callback page intro text
			'offers_for_woocommerce_general_settings' // Page
		);
		
		/**
		 * Add field - 'General Settings' - 'general_setting_enable_make_offer_btn_frontpage'
		 * Enable Make Offer button on home page
		 */
		add_settings_field(
			'general_setting_enable_make_offer_btn_frontpage', // ID
			__('Show on Home Page', 'offers-for-woocommerce'), // Title
			array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback
			'offers_for_woocommerce_general_settings', // Page
			'general_settings', // Section 
			array(
                'option_name'=>'offers_for_woocommerce_options_general',
                'input_label'=>'general_setting_enable_make_offer_btn_frontpage',
                'input_required'=>FALSE,
                'description' => __('Check this option to display offer buttons for products on your home page.', 'offers-for-woocommerce'),
            )
		);

        /**
         * Add field - 'General Settings' - 'general_setting_enable_make_offer_btn_catalog'
         * Enable Make Offer button on shop page
         */
        add_settings_field(
            'general_setting_enable_make_offer_btn_catalog', // ID
            __('Show on Shop Page', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback
            'offers_for_woocommerce_general_settings', // Page
            'general_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_general',
                'input_label'=>'general_setting_enable_make_offer_btn_catalog',
                'input_required'=>FALSE,
                'description' => __('Check this option to display offer buttons for products on your shop page.', 'offers-for-woocommerce'),
            )
        );

        /**
         * Add field - 'General Settings' - 'general_setting_enable_offers_by_default'
         * Enable Make Offer button on new products by default
         */
        add_settings_field(
            'general_setting_enable_offers_by_default', // ID
            __('Enable Offers by Default', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback
            'offers_for_woocommerce_general_settings', // Page
            'general_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_general',
                'input_label'=>'general_setting_enable_offers_by_default',
                'input_required'=>FALSE,
                'description' => __('Check this option to automatically enable offers on all new products by default.', 'offers-for-woocommerce'),
            )
        );

        /**
         * Add field - 'General Settings' - 'general_setting_limit_offer_quantity_by_stock'
         * Limit Offer Quantity on products with limited stock and no backorders
         */
        add_settings_field(
            'general_setting_limit_offer_quantity_by_stock', // ID
            __('Limit Offer Quantity at Product Stock Quantity', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback
            'offers_for_woocommerce_general_settings', // Page
            'general_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_general',
                'input_label'=>'general_setting_limit_offer_quantity_by_stock',
                'input_required'=>FALSE,
                'description' => __('Check this option to limit offer quantity at stock quantity on products not allowing backorders.', 'offers-for-woocommerce'),
            )
        );

        /**
         * Add field - 'General Settings' - 'general_setting_default_expire_days'
         * Default amount of days out to set expire date
         */
        add_settings_field(
            'general_setting_default_expire_days', // ID
            __('Default Offer Expiration Days', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_text' ), // Callback TEXT input
            'offers_for_woocommerce_general_settings', // Page
            'general_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_general',
                'input_label'=>'general_setting_default_expire_days',
                'input_required'=>FALSE,
                'description' => __('Enter the amount of days from accepting/countering an offer that you would like the expiration date to automatically set.', 'offers-for-woocommerce'),
            )
        );
        
        /**
         * Add field - 'General Settings' - 'general_setting_enable_offers_only_logged_in_users'
         * Enable Offers For Only Logged-in Users
         */
        add_settings_field(
            'general_setting_enable_offers_required_logged_in_users', // ID
            __('Require Log In', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback Checkbox input
            'offers_for_woocommerce_general_settings', // Page
            'general_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_general',
                'input_label'=>'general_setting_enable_offers_only_logged_in_users',
                'input_required'=>FALSE,
                'description' => __('Check this option to require users to login in order to submit an offer.', 'offers-for-woocommerce'),
            )
        );
        
        /**
         * Add field - 'General Settings' - 'general_setting_enable_offers_only_logged_in_users'
         * Enable Offers For Only Logged-in Users
         */
        add_settings_field(
            'general_setting_enable_offers_only_logged_in_users', // ID
            __('Hide Offers Until Logged In', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback Checkbox input
            'offers_for_woocommerce_general_settings', // Page
            'general_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_general',
                'input_label'=>'general_setting_enable_offers_hide_untill_logged_in_users',
                'input_required'=>FALSE,
                'description' => __('Check this option to completely hide the offer buttons unless the user is logged in.', 'offers-for-woocommerce'),
            )
        );

        /**
         * Add field - 'General Settings' - 'general_setting_allowed_roles'
         * Enable Offers For Only Specific User Roles
         */
        $editable_roles = get_editable_roles();
        $editable_roles_inputs = array();
        foreach($editable_roles as $role)
        {
            array_push($editable_roles_inputs,
                array('option_label' => $role['name'], 'option_value' => strtolower($role['name']) )
            );
        }
        add_settings_field(
            'general_setting_allowed_roles', // ID
            __('Enable Offers for Only Specific User Roles', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_select' ), // Callback SELECT input
            'offers_for_woocommerce_general_settings', // Page
            'general_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_general',
                'input_label'=>'general_setting_allowed_roles',
                'input_required'=>FALSE,
                'description' => __('Select the roles you want offers enabled for. Leave blank for all roles enabled.', 'offers-for-woocommerce'),
                'options'=> $editable_roles_inputs,
                'multiple' => TRUE,
                'extra_classes' => 'chosen-select'
            )
        );
        

        
        /**
          * Add field - 'General Settings' - 'general_setting_disable_coupon'
          * Disable coupons when checking out with accepted offer
          */
         add_settings_field(
            'general_setting_disable_coupon', // ID
             __('Disable Coupons', 'offers-for-woocommerce'), // Title
             array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback
             'offers_for_woocommerce_general_settings', // Page
             'general_settings', // Section
             array(
                 'option_name'=>'offers_for_woocommerce_options_general',
                 'input_label'=>'general_setting_disable_coupon',
                 'input_required'=>FALSE,
                 'description' => __('Check this option to disable coupons when checking out with an accepted offer included in the shopping cart.', 'offers-for-woocommerce'),
             )
         );

                add_settings_field(
            'general_setting_enable_anonymous_communication', // ID
            __('Enable Anonymous Communication', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback
            'offers_for_woocommerce_general_settings', // Page
            'general_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_general',
                'input_required'=>FALSE,
                'input_label'=>'general_setting_enable_anonymous_communication',
                'description' => __('Check this option to hide the contact information for potential buyers while negotiation is going on.  This can be useful when working with vendors who would be paying you a commission.', 'offers-for-woocommerce'),
            )
        );
                
            add_settings_field(
                'general_setting_show_pending_offer', // ID
                __('Show Pending Offer Details to Buyers', 'offers-for-woocommerce'), // Title
                array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback
                    'offers_for_woocommerce_general_settings', // Page
                    'general_settings', // Section
                array(
                    'option_name'=>'offers_for_woocommerce_options_general',
                    'input_required'=>FALSE,
                    'input_label'=>'general_setting_show_pending_offer',
                    'description' => __('Check this option to display pending offer(s) on front end.', 'offers-for-woocommerce'),
                )
            );
            
            
            add_settings_field(
                'general_setting_show_highest_current_bid', // ID
                __('Display Highest Offer', 'offers-for-woocommerce'), // Title
                array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback
                    'offers_for_woocommerce_general_settings', // Page
                    'general_settings', // Section
                array(
                    'option_name'=>'offers_for_woocommerce_options_general',
                    'input_required'=>FALSE,
                    'input_label'=>'general_setting_show_highest_current_bid',
                    'description' => __('Check this option to display the highest current offer on product page for potential buyers to see.', 'offers-for-woocommerce'),
                )
            );
            
            /**
             * Add section - 'Display Settings'
             */
            add_settings_section(
                    'display_settings', // ID
                    '', // Title
                    array( $this, 'offers_for_woocommerce_options_page_intro_text' ), // Callback page intro text
                    'offers_for_woocommerce_display_settings' // Page
            );

        /**
         * Add field - 'Display Settings' - 'display_setting_enable_make_offer_form_lightbox'
         * Enable Make Offer button on home page
         */
        add_settings_field(
            'display_setting_make_offer_form_display_type', // ID
            __('Form Display Type', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_select' ), // Callback SELECT input
            'offers_for_woocommerce_display_settings', // Page
            'display_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_display',
                'input_label'=>'display_setting_make_offer_form_display_type',
                'input_required'=>FALSE,
                'description' => __('Depending on your theme, you may wish to display the offer form on a tab within the product page or in a lightbox window on top of the product page.', 'offers-for-woocommerce'),
                'options'=> array(
                    array('option_label' => __('Product Tabs (default display)', 'offers-for-woocommerce'), 'option_value' => 'tabs'),
                    array('option_label' => __('Lightbox', 'offers-for-woocommerce'), 'option_value' => 'lightbox')
                ))
        );

        /**
         * Add field - 'Display Settings' - 'display_setting_make_offer_form_fields'
         * Enable optional form fields on make offer form
         */
        add_settings_field(
            'display_setting_make_offer_form_fields', // ID
            __('Form Fields', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_checkbox_group' ), // Callback checkbox group
            'offers_for_woocommerce_display_settings', // Page
            'display_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_display',
                'input_label'=>'display_setting_make_offer_form_field',
                'input_required'=>FALSE,
                'description' => __('Tick the checkbox of the form fields you want to display on the offer form. Quantity, Price Each, Your Name, Your Email Address are required fields by default.', 'offers-for-woocommerce'),
                'options'=> array(
                    array('option_label' => __('Quantity', 'offers-for-woocommerce'), 'option_name' => 'offer_quantity', 'option_disabled' => TRUE ),
                    array('option_label' => __('Price Each', 'offers-for-woocommerce'), 'option_name' => 'offer_price_each', 'option_disabled' => TRUE ),
                    array('option_label' => __('Your Name', 'offers-for-woocommerce'), 'option_name' => 'offer_name', 'option_disabled' => TRUE ),
                    array('option_label' => __('Your Email Address', 'offers-for-woocommerce'), 'option_name' => 'offer_email', 'option_disabled' => TRUE ),
                    array('option_label' => __('Total Offer Amount', 'offers-for-woocommerce'), 'option_name' => 'offer_total', 'option_disabled' => FALSE ),
                    array('option_label' => __('Company Name', 'offers-for-woocommerce'), 'option_name' => 'offer_company_name', 'option_disabled' => FALSE ),
                    array('option_label' => __('Phone Number', 'offers-for-woocommerce'), 'option_name' => 'offer_phone', 'option_disabled' => FALSE ),
                    array('option_label' => __('Offer Notes', 'offers-for-woocommerce'), 'option_name' => 'offer_notes', 'option_disabled' => FALSE )
                )
            )
        );

        /**
         * Add field - 'Display Settings' - 'display_setting_make_offer_button_position_single'
         * Make Offer Button position
         */
        add_settings_field(
            'display_setting_make_offer_button_position_single', // ID
            __('Button Position', 'offers-for-woocommerce'), // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_select' ), // Callback SELECT input
            'offers_for_woocommerce_display_settings', // Page
            'display_settings', // Section
            array(
                'option_name'=>'offers_for_woocommerce_options_display',
                'input_label'=>'display_setting_make_offer_button_position_single',
                'input_required'=>FALSE,
                'description' => __('Use this setting to adjust the location of the \'Make Offer\' button on your product detail page.', 'offers-for-woocommerce'),
                'options'=> array(
                    array('option_label' => __('After add to cart button (default display)', 'offers-for-woocommerce'), 'option_value' => 'default'),
                    array('option_label' => __('Before add to cart button', 'offers-for-woocommerce'), 'option_value' => 'before_add'),
                    array('option_label' => __('To the right of add to cart button', 'offers-for-woocommerce'), 'option_value' => 'right_of_add'),
                    array('option_label' => __('After product price', 'offers-for-woocommerce'), 'option_value' => 'after_price'),
                    array('option_label' => __('After product tabs', 'offers-for-woocommerce'), 'option_value' => 'after_tabs')
                ))
        );
		
		/**
		 * Add field - 'Display Settings' - 'display_setting_custom_make_offer_btn_text'
		 * Make Offer Button Text
		 */
		add_settings_field(
			'display_setting_custom_make_offer_btn_text', // ID
			__('Button Text', 'offers-for-woocommerce'), // Title
			array( $this, 'offers_for_woocommerce_options_page_output_input_text' ), // Callback TEXT input
			'offers_for_woocommerce_display_settings', // Page
			'display_settings', // Section
			array(
                'option_name'=>'offers_for_woocommerce_options_display',
                'input_label'=>'display_setting_custom_make_offer_btn_text',
                'input_required'=>FALSE,
                'description' => __('Set the text you would like to be displayed in the offer button.', 'offers-for-woocommerce'),
            )
		);
		
		/**
		 * Add field - 'Display Settings' - 'display_setting_custom_make_offer_btn_text_color'
		 * Make Offer Button Text Color
		 */
		add_settings_field(
			'display_setting_custom_make_offer_btn_text_color', // ID
            __('Button Text Color', 'offers-for-woocommerce'), // Title
			array( $this, 'offers_for_woocommerce_options_page_output_input_colorpicker' ), // Callback TEXT input
			'offers_for_woocommerce_display_settings', // Page
			'display_settings', // Section
			array(
                'option_name'=>'offers_for_woocommerce_options_display',
                'input_label'=>'display_setting_custom_make_offer_btn_text_color',
                'input_required'=>FALSE,
                'description' => __('Use the color-picker to choose the font color for the text on your offer buttons.', 'offers-for-woocommerce'),
            )
		);

		/**
		 * Add field - 'Display Settings' - 'display_setting_custom_make_offer_btn_color'
		 * Make Offer Button Text Color
		 */
		add_settings_field(
			'display_setting_custom_make_offer_btn_color', // ID
            __('Button Color', 'offers-for-woocommerce'), // Title
			array( $this, 'offers_for_woocommerce_options_page_output_input_colorpicker' ), // Callback TEXT input
			'offers_for_woocommerce_display_settings', // Page
			'display_settings', // Section
			array(
                'option_name'=>'offers_for_woocommerce_options_display',
                'input_label'=>'display_setting_custom_make_offer_btn_color',
                'input_required'=>FALSE,
                'description' => __('Use the color-picker to choose the background color for your offer buttons.', 'offers-for-woocommerce'),
            )
		);

	} // END - angelleye_ofwc_intialize_options
	
	/**
	 * Enqueue the colour picker
	 * This is called by function 'enqueue_admin_scripts' 
	 * @since	0.1.0
	 */
	public function my_enqueue_colour_picker()
	{
		wp_enqueue_script(
		'artus-field-color-js', 
		'ofwc_field_colorpicker.js', 
		array('jquery', 'farbtastic'),
		time(),
		true
		);	

		wp_enqueue_style( 'farbtastic' );
	}
	
	/**
	 * Callback - Options Page intro text
	 * @since	0.1.0
	 */
	public function offers_for_woocommerce_options_page_intro_text() 
	{
		print('<p>'. __('Complete the form below and click Save Changes button to update your settings.', 'offers-for-woocommerce'). '</p>');
	}
	
	/**
	 * Callback - Options Page - Output a 'text' input field for options page form
	 * @since	0.1.0
	 * @param	$args - Params to define 'option_name','input_label'
	 */
	public function offers_for_woocommerce_options_page_output_input_text($args) 
	{
		$options = get_option($args['option_name']);
        $description = isset($args['description']) ? $args['description'] : '';
		$field_label = $args['input_label'];
		$field_required = ($args['input_required']) ? ' required="required" ' : '';
		printf(
            '<input ' .$field_required. ' type="text" id="'.$field_label.'" name="'.$args['option_name'].'['.$field_label.']" value="%s" />',
            isset( $options[$field_label] ) ? esc_attr( $options[$field_label]) : ''
        );

        echo '<div class="angelleye-settings-description">' . $description . '</div>';
	}

    /**
     * Callback - Options Page - Output a 'Checkbox' input field for options page form
     * @since	0.1.0
     * @param	$args - Params to define 'option_name','input_label'
     */
    public static function offers_for_woocommerce_options_page_output_input_checkbox($args)
    {
        $options = get_option($args['option_name']);
        $description = isset($args['description']) ? $args['description'] : '';
        $field_label = $args['input_label'];
        $field_required = ($args['input_required'] === true) ? ' required="required" ' : '';
        $is_checked = (isset($options[$field_label])) ? $options[$field_label] : '0';
        print(
            '<input '. $field_required. ' type="checkbox" id="'.$field_label.'" name="'.$args['option_name'].'['.$field_label.']" value="1" ' . checked(1, $is_checked, false) . '/>&nbsp;' . $description
        );
    }

    /**
     * Callback - Options Page - Output a 'Select' input field for options page form
     * @since	0.1.0
     * @param	$args - Params to define 'option_name','input_label','input_required,'options'
     */
    public function offers_for_woocommerce_options_page_output_input_select($args)
    {
        $options = get_option($args['option_name']);
        $description = isset($args['description']) ? $args['description'] : '';
        $field_label = $args['input_label'];
        $field_required = ($args['input_required'] === true) ? ' required="required" ' : '';
        $multiple = (isset($args['multiple']) && $args['multiple'] === true) ? ' multiple="multiple" ' : '';
        $field_label_multiple = (isset($args['multiple']) && $args['multiple'] === true) ? '[]' : '';
        $extra_classes = (!empty($args['extra_classes'])) ? ' class="'. $args['extra_classes'] . '" ' : '';

        print(
            '<select '. $extra_classes . $field_required. ' id="'.$field_label.'" name="'.$args['option_name'].'['.$field_label.']'. $field_label_multiple . '" ' . $multiple . '/>'
        );
        foreach( $args['options'] as $option )
        {
            if(isset($args['multiple']) && $args['multiple'] === true)
            {
                $is_selected = (isset($options[$field_label]) && in_array($option['option_value'], $options[$field_label])) ? 'selected="selected"' : '';
            }
            else
            {
                $is_selected = (isset($options[$field_label]) && $options[$field_label] == $option['option_value']) ? 'selected="selected"' : '';
            }
            print(
                '<option value="'. $option['option_value'] . '" '. $is_selected .'>'. $option['option_label'] . '</option>'
            );
        }

        print(
        '</select>'
        );

        echo '<div class="angelleye-settings-description">' . $description . '</div>';

    }

    /**
     * Callback - Options Page - Output a grouping of checkboxes for options page form
     * @since	1.1.3
     * @param	$args - Params to define 'option_name','option_label','option_disabled'
     */
    public function offers_for_woocommerce_options_page_output_checkbox_group($args)
    {
        $options = get_option($args['option_name']);
        $description = isset($args['description']) ? $args['description'] : '';
        $field_label = $args['input_label'];

        echo '<div class="angelleye-settings-description"><p>' . $description . '</p></div>';
        echo '<ul class="angelleye-settings-ul-checkboxes">';
        foreach( $args['options'] as $option )
        {
            $is_checked = (isset($options[$field_label.'_'.$option['option_name']])) ? $options[$field_label.'_'.$option['option_name']] : '0';
            $is_disabled = (!empty($option['option_disabled'])) ? 'disabled="disabled" checked="checked"' : '';
            print(
                '<li><input name="'.$args['option_name'].'['.$field_label.'_'.$option['option_name'].']" type="checkbox" value="1" ' . checked(1, $is_checked, false) . $is_disabled . '/>&nbsp;'.$option['option_label'].'</li>'
            );
        }
        echo '</ul>';
    }
	
	/**
	 * Callback - Options Page - Output a 'colorpicker' input field for options page form
	 * @since	0.1.0
	 * @param	$args - Params to define 'option_name','input_label'
	 */
	public function offers_for_woocommerce_options_page_output_input_colorpicker($args) 
	{
		$options = get_option($args['option_name']);
        $description = isset($args['description']) ? $args['description'] : '';
		$field_label = $args['input_label'];
		$field_required = ($args['input_required']) ? ' required="required" ' : '';
		
		echo '<div class="farb-popup-wrapper">';
		
		printf(
            '<input ' .$field_required. ' type="text" id="'.$field_label.'" name="'.$args['option_name'].'['.$field_label.']" value="%s" class="popup-colorpicker" />',
            (isset( $options[$field_label]) && $options[$field_label] != '') ? esc_attr( $options[$field_label]) : ''
        );
		
		print('<div id="'.$field_label.'picker" class="color-picker"></div></div>');
		
		echo '<script type="text/javascript">';	
		
		echo 'jQuery(document).ready(function(){
			var $input = jQuery("#'.$field_label.'");
			var $pickerId = "#" + jQuery("#'.$field_label.'").attr("id") + "picker";
	
			jQuery($pickerId).hide();
			jQuery($pickerId).farbtastic($input);
			jQuery($input).click(function(){
				jQuery($pickerId).slideToggle();
				});
			jQuery($input).focus(function(){
				if(jQuery("#'.$field_label.'").val() == "")
				{
					jQuery("#'.$field_label.'").val("#");
				}
				});
			jQuery("#woocommerce_offers_options_form").submit(function(){
				if(jQuery($input).val() == "#")
				{
					jQuery($input).val("");
				}
				return true;
				});
			});
		';
		echo '</script>';

        echo '<div class="angelleye-settings-description">' . $description . '</div>';
	}
	
	/**
	 * Return an instance of this class
	 * @since     0.1.0
	 * @return    object    A single instance of this class
	 */
	public static function get_instance() 
	{
		/**
		 * If not super admin or shop manager, return
		 */
		if ( !(current_user_can('vendor') || current_user_can('administrator') || current_user_can('seller')) ) {
			return;
		}		





		/*
		 * If the single instance hasn't been set, set it now
		 */
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet
	 * @since     0.1.0
	 * @return    null    Return early if no settings page is registered
	 */
	public function enqueue_admin_styles() 
	{      
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		$screen = get_current_screen();

        if ( ("edit-woocommerce_offer" == $screen->id || "woocommerce_offer" == $screen->id || $this->plugin_screen_hook_suffix == $screen->id) )
        {
            // Bootstrap styles for modal
            wp_enqueue_style( 'offers-for-woocommerce-angelleye-offers-admin-styles-boostrap-custom', plugins_url( 'assets/css/bootstrap-custom.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );

            // jQuery styles
            wp_enqueue_style( 'offers-for-woocommerce-angelleye-offers-admin-styles-jquery-ui', plugins_url( 'assets/css/jquery-ui.min.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );
            wp_enqueue_style( 'offers-for-woocommerce-angelleye-offers-admin-styles-jquery-ui-structure', plugins_url( 'assets/css/jquery-ui.structure.min.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );
            wp_enqueue_style( 'offers-for-woocommerce-angelleye-offers-admin-styles-jquery-ui-theme', plugins_url( 'assets/css/jquery-ui.theme.min.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );

            // admin styles
            wp_enqueue_style( 'offers-for-woocommerce-angelleye-offers-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );

            // chosen js styles
            wp_enqueue_style( 'offers-for-woocommerce-angelleye-offers-admin-styles-jquery-chosen', plugins_url( 'assets/css/chosen/chosen.min.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );
        }

        if ( "product" == $screen->id && is_admin() )
        {
            // admin styles - edit product
            wp_enqueue_style( 'offers-for-woocommerce-angelleye-offers-edit-product-styles', plugins_url( 'assets/css/edit-product.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );
        }
	}

	/**
	 * Register and enqueue admin-specific JavaScript
	 * @since     0.1.0
	 * @return    null    Return early if no settings page is registered
	 */
	public function enqueue_admin_scripts() 
	{	
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		$screen = get_current_screen();
						
		if ( $this->plugin_screen_hook_suffix == $screen->id && is_admin() ) 
		{
			// load color picker			
			$this->my_enqueue_colour_picker();

			// Admin footer scripts
			wp_enqueue_script( 'offers-for-woocommerce-angelleye-offers-admin-footer-scripts', plugins_url( 'assets/js/admin-footer-scripts.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // autoNumeric js
            wp_enqueue_script( 'offers-for-woocommerce-angelleye-offers-jquery-auto-numeric-1-9-24', plugins_url( '../public/assets/js/autoNumeric-1-9-24.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // Admin settings scripts
            wp_enqueue_script( 'offers-for-woocommerce-angelleye-offers-admin-settings-scripts', plugins_url( 'assets/js/admin-settings-scripts.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // Chosen js
            wp_enqueue_script( 'offers-for-woocommerce-angelleye-offers-jquery-chosen', plugins_url( 'assets/js/chosen.jquery.min.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );
		}
        if ( "edit-woocommerce_offer" == $screen->id && is_admin() )
        {
            // Admin actions
            wp_enqueue_script( 'offers-for-woocommerce-angelleye-offers-admin-actions', plugins_url( 'assets/js/admin-actions.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // Bootstrap modal.js
            wp_enqueue_script( 'offers-for-woocommerce-angelleye-offers-bootstrap-modal', plugins_url( 'assets/js/bootstrap-modal.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // jQuery.confirm.js
            wp_enqueue_script( 'offers-for-woocommerce-angelleye-offers-jquery-confirm-min', plugins_url( 'assets/js/jquery.confirm.min.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );
        }
        if ( "woocommerce_offer" == $screen->id && is_admin() )
        {
            // Jquery datepicker.js
            wp_enqueue_script( 'offers-for-woocommerce-angelleye-offers-jquery-datepicker', plugins_url( 'assets/js/jquery-ui.min.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // autoNumeric js
            wp_enqueue_script( 'offers-for-woocommerce-angelleye-offers-jquery-auto-numeric-1-9-24', plugins_url( '../public/assets/js/autoNumeric-1-9-24.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // admin scripts
            wp_enqueue_script( 'offers-for-woocommerce-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );
            global $post, $wpdb;
            $ofw_offer_expiration_date_show = 'false';
            $expiration_date = get_post_meta($post->ID, 'offer_expiration_date', true );
            $today_date = date("m/d/Y");  
            if((isset($expiration_date) && !empty($expiration_date)) && ( strtotime($expiration_date) < strtotime($today_date) )){ 
                $ofw_offer_expiration_date_show = 'true';
            }
            wp_localize_script('offers-for-woocommerce-admin-script', 'ofw_param', array(
                'ofw_offer_expiration_date_show' => $ofw_offer_expiration_date_show
            ));
        }

        if ( "product" == $screen->id && is_admin() )
        {
            // admin scripts - edit product
            wp_enqueue_script( 'offers-for-woocommerce-angelleye-offers-admin-script-edit-product', plugins_url( 'assets/js/edit-product.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );
        }
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() 
	{
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'options-general.php', 
			__( 'Offers for WooCommerce - Settings', 'offers-for-woocommerce' ),
			__( 'Offers for WooCommerce', 'offers-for-woocommerce' ),
			'manage_options',
			'offers-for-woocommerce',
			array( $this, 'display_plugin_admin_page'));			
	}

	/**
	 * Callback - Render the settings page for this plugin.
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() 
	{

        // WooCommerce product categories
        $taxonomy     = 'product_cat';
        $orderby      = 'name';
        $show_count   = 0;      // 1 for yes, 0 for no
        $pad_counts   = 0;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no
        $title        = '';
        $empty        = 0;

        $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty
        );

        $product_cats = get_categories( $args );

		include_once( 'views/admin.php' );
	}
	
	/**
	 * Add Plugin Page Action links
	 * @since    0.1.0
	 */
	public function ofwc_add_plugin_action_links( $links, $file )
	{
        $plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . 'offers-for-woocommerce.php' );

        if($file == $plugin_basename)
        {
            $new_links = array(
                sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=offers-for-woocommerce' ), __( 'Configure', 'offers-for-woocommerce' ) ),
                sprintf( '<a href="%s" target="_blank">%s</a>', 'http://www.angelleye.com/category/docs/offers-for-woocommerce/?utm_source=offers_for_woocommerce&utm_medium=docs_link&utm_campaign=offers_for_woocommerce', __( 'Docs', 'offers-for-woocommerce' ) ),
                sprintf( '<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/plugin/offers-for-woocommerce/', __( 'Support', 'offers-for-woocommerce' ) ),
                sprintf( '<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/view/plugin-reviews/offers-for-woocommerce', __( 'Write a Review', 'offers-for-woocommerce' ) ),
            );

            $links = array_merge( $links, $new_links );
        }
        return $links;
	}
	
	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
	 * @since    0.1.0
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['general_setting_1'] ) )
            $new_input['general_setting_1'] = absint( $input['general_setting_1'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }
	
	/**
     * Validate each setting field as needed
     *
     * @param	array	$input
	 * @since    0.1.0
     */
	public function offers_for_woocommerce_options_validate_callback($input)
	{
		return $input;
		
		// Create our array for storing the validated options
		$output = array();
		 
		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			 
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
			 
				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
				 
			} // end if
			 
		} // end foreach
		 
		// Return the array processing any additional functions filtered by this action
		echo apply_filters( 'offers_for_woocommerce_options_validate_callback', $output, $input );
	}

	/**
	 * Callback - Action - Add 'pending offer(s)' count to wp dashboard 'at a glance' widget
	 * @since	0.1.0
	 */
	public function my_add_cpt_to_dashboard( $glances )
	{
		$post_types = get_post_types( array( '_builtin' => false ), 'objects' );
		foreach ( $post_types as $post_type ) {
			if($post_type->name == 'woocommerce_offer')
			{
				$num_posts = wp_count_posts( $post_type->name );
				$num = number_format_i18n( $num_posts->publish );
				$text = _n( 'Pending Offer', 'Pending Offers', $num_posts->publish );
				if( (is_super_admin()) || (current_user_can( 'manage_woocommerce')) ) {
					$output = '<a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a>';
					echo '<li class="page-count ' . $post_type->name . '-count">' . $output . '</td>';
				}
			}
		}
	}

    /*
 * Action - Ajax 'approve offer' from manage list
 * @since	0.1.0
 */
    public function approveOfferFromGridCallback()
    {
        if(is_admin() && ( is_ajax() || (isset($_GET['ofw_from_email']) && $_GET['ofw_from_email'] == true)))
        {
            

            global $post, $wpdb; // this is how you get access to the database
            if( isset($_GET['targetID']) && !empty($_GET['targetID'])) {
                $post_id = $_GET["targetID"];
            } else {
                $post_id = $_POST['targetID'];
            }
            do_action('ofw_before_auto_approve_offer_admin', $post_id);
            // Get current data for Offer prior to save
            $post_data = get_post($post_id);

            $table = $wpdb->prefix . "posts";
            $data_array = array(
                'post_status' => 'accepted-offer',
                'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                'post_modified_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1 ))
            );
            $where = array('ID' => $post_id);
            $wpdb->update( $table, $data_array, $where );

            // Filter Post Status Label
            $post_status_text = __('Accepted', 'offers-for-woocommerce');

            // set update notes
            $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? $_POST['angelleye_woocommerce_offer_status_notes'] : '';

            /**
             * Email customer accepted email template
             * @since   0.1.0
             */
            // set recipient email
            $recipient = get_post_meta($post_id, 'offer_email', true);
            $offer_id = $post_id;
            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
            $offer_name = get_post_meta($post_id, 'offer_name', true);
            $offer_email = $recipient;

            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
            
            $product = ( $variant_id ) ? wc_get_product( $variant_id ) : wc_get_product( $product_id );

            // if buyercountered-offer previous then use buyer counter values
            $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
            $product_total = number_format(round($product_qty * $product_price_per, 2), 2, '.', '');

            // if buyercountered-offer status, update postmeta values for quantity,price,amount
            if( $is_offer_buyer_countered_status )
            {
                update_post_meta( $post_id, 'offer_quantity', $product_qty );
                update_post_meta( $post_id, 'offer_price_per', $product_price_per );
                update_post_meta( $post_id, 'offer_shipping_cost', $product_shipping_cost );
                update_post_meta( $post_id, 'offer_amount', $product_total );
            }
            
            if ( get_post_status ( $post_id ) != 'accepted-offer' ) {
            
                do_action('ofw_before_auto_approve_offer_admin', $post_id);
                // Get current data for Offer prior to save
                $post_data = get_post($post_id);

                $table = $wpdb->prefix . "posts";
                $data_array = array(
                    'post_status' => 'accepted-offer',
                    'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                    'post_modified_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1 ))
                );
                $where = array('ID' => $post_id);
                $wpdb->update( $table, $data_array, $where );

                // Filter Post Status Label
                $post_status_text = __('Accepted', 'offers-for-woocommerce');

                // set update notes
                $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? $_POST['angelleye_woocommerce_offer_status_notes'] : '';

                /**
                 * Email customer accepted email template
                 * @since   0.1.0
                 */
                // set recipient email
                $recipient = get_post_meta($post_id, 'offer_email', true);
                $offer_id = $post_id;
                $offer_uid = get_post_meta($post_id, 'offer_uid', true);
                $offer_name = get_post_meta($post_id, 'offer_name', true);
                $offer_email = $recipient;

                $product_id = get_post_meta($post_id, 'offer_product_id', true);
                $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
                
                $product = ( $variant_id ) ? wc_get_product( $variant_id ) : wc_get_product( $product_id );

                // if buyercountered-offer previous then use buyer counter values
                $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;

                $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
                $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
                $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
                $product_total = number_format(round($product_qty * $product_price_per, 2), 2, '.', '');

                // if buyercountered-offer status, update postmeta values for quantity,price,amount
                if( $is_offer_buyer_countered_status )
                {
                    update_post_meta( $post_id, 'offer_quantity', $product_qty );
                    update_post_meta( $post_id, 'offer_price_per', $product_price_per );
                    update_post_meta( $post_id, 'offer_shipping_cost', $product_shipping_cost );
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
                    'product_shipping_cost' => $product_shipping_cost,
                    'product_total' => $product_total,
                    'offer_notes' => $offer_notes
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

                
            }
            else
            {
                $offer_args['product_title_formatted'] = $product->get_formatted_name();
            }

                // the email we want to send
                $email_class = 'WC_Accepted_Offer_Email';
                // load the WooCommerce Emails
                $wc_emails = new WC_Emails();
                $emails = $wc_emails->get_emails();

                // select the email we want & trigger it to send
                $new_email = $emails[$email_class];
                $new_email->recipient = $recipient;

               
            $new_email->plugin_slug = 'offers-for-woocommerce';

            // define email template/path (html)
            $new_email->template_html  = 'woocommerce-offer-accepted.php';
            $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

                // define email template/path (plain)
                $new_email->template_plain  = 'woocommerce-offer-accepted.php';
                $new_email->template_plain_path = plugin_dir_path(__FILE__). 'includes/emails/plain/';

                $new_email->trigger($offer_args);

            // Insert WP comment
            $comment_text = "<span>". __('Updated - Status:', 'offers-for-woocommerce')."&nbsp;</span>";
            $comment_text.= $post_status_text;

                // include update notes
                if(isset($offer_notes) && $offer_notes != '')
                {
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

                // insert comment meta
                if( $new_comment_id )
                {
                    add_comment_meta( $new_comment_id, 'angelleye_woocommerce_offer_id', $post_id, true );
                }

                do_action('ofw_after_auto_approve_offer_admin', $post_id);

                if(is_ajax()) {
                    die(); // this is required to return a proper result
                }
            }
        }

    /*
     * Action - Ajax 'decline offer' from manage list
     * @since	0.1.0
     */
    public function declineOfferFromGridCallback()
    {
        if(is_admin() && ( is_ajax() || (isset($_GET['ofw_from_email']) && $_GET['ofw_from_email'] == true)))
        {
            global $wpdb; // this is how you get access to the database
            if( isset($_GET['targetID']) && !empty($_GET['targetID'])) {
                $post_id = $_GET["targetID"];
            } else {
                $post_id = $_POST["targetID"];
            }
            do_action('ofw_before_auto_decline_offer_admin', $post_id);
            // Get current data for Offer prior to save
            $post_data = get_post($post_id);
            $coupon_code = ( isset($_POST["coupon_code"]) && !empty($_POST["coupon_code"]) ) ? $_POST["coupon_code"] : '';

            // if buyercountered-offer previous then use buyer counter values
            $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;

            $table = $wpdb->prefix . "posts";
            $data_array = array(
                'post_status' => 'declined-offer',
                'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                'post_modified_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1 ))
            );
            $where = array('ID' => $post_id);
            $wpdb->update( $table, $data_array, $where );

            // Filter Post Status Label
            $post_status_text = __('Declined', 'offers-for-woocommerce');

            // set update notes
            $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? $_POST['angelleye_woocommerce_offer_status_notes'] : '';

            /**
             * Email customer declined email template
             * @since   0.1.0
             */
            // set recipient email
            $recipient = get_post_meta($post_id, 'offer_email', true);
            $offer_id = $post_id;
            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
            $offer_name = get_post_meta($post_id, 'offer_name', true);
            $offer_email = $recipient;

            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
            
            $product = ( $variant_id ) ? wc_get_product( $variant_id ) : wc_get_product( $product_id );

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
            $product_total = number_format(round($product_qty * $product_price_per, 2), 2, '.', '');

            // if buyercountered-offer status, update postmeta values for quantity,price,amount
            if( $is_offer_buyer_countered_status )
            {
                update_post_meta( $post_id, 'offer_quantity', $product_qty );
                update_post_meta( $post_id, 'offer_price_per', $product_price_per );
                update_post_meta( $post_id, 'offer_shipping_cost', $product_shipping_cost );
                update_post_meta( $post_id, 'offer_amount', $product_total );
            }
            if ( get_post_status ( $post_id ) != 'declined-offer' ) {
            
                do_action('ofw_before_auto_decline_offer_admin', $post_id);
                // Get current data for Offer prior to save
                $post_data = get_post($post_id);
                $coupon_code = ( isset($_POST["coupon_code"]) && !empty($_POST["coupon_code"]) ) ? $_POST["coupon_code"] : '';

                // if buyercountered-offer previous then use buyer counter values
                $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;

                $table = $wpdb->prefix . "posts";
                $data_array = array(
                    'post_status' => 'declined-offer',
                    'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                    'post_modified_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1 ))
                );
                $where = array('ID' => $post_id);
                $wpdb->update( $table, $data_array, $where );

                // Filter Post Status Label
                $post_status_text = __('Declined', 'offers-for-woocommerce');

                // set update notes
                $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? $_POST['angelleye_woocommerce_offer_status_notes'] : '';

                /**
                 * Email customer declined email template
                 * @since   0.1.0
                 */
                // set recipient email
                $recipient = get_post_meta($post_id, 'offer_email', true);
                $offer_id = $post_id;
                $offer_uid = get_post_meta($post_id, 'offer_uid', true);
                $offer_name = get_post_meta($post_id, 'offer_name', true);
                $offer_email = $recipient;

                $product_id = get_post_meta($post_id, 'offer_product_id', true);
                $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
                
                $product = ( $variant_id ) ? wc_get_product( $variant_id ) : wc_get_product( $product_id );

                $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
                $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
                $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
                $product_total = number_format(round($product_qty * $product_price_per, 2), 2, '.', '');

                // if buyercountered-offer status, update postmeta values for quantity,price,amount
                if( $is_offer_buyer_countered_status )
                {
                    update_post_meta( $post_id, 'offer_quantity', $product_qty );
                    update_post_meta( $post_id, 'offer_price_per', $product_price_per );
                    update_post_meta( $post_id, 'offer_shipping_cost', $product_shipping_cost );
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
                    'offer_notes' => $offer_notes,
                    'coupon_code' => $coupon_code
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
            }
            else
            {
                $offer_args['product_title_formatted'] = $product->get_formatted_name();
            }

                // the email we want to send
                $email_class = 'WC_Declined_Offer_Email';

                // load the WooCommerce Emails
                $wc_emails = new WC_Emails();
                $emails = $wc_emails->get_emails();

                // select the email we want & trigger it to send
                $new_email = $emails[$email_class];
                $new_email->recipient = $recipient;

                $new_email->plugin_slug = 'offers-for-woocommerce';

                // define email template/path (html)
                $new_email->template_html  = 'woocommerce-offer-declined.php';
                $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

                // define email template/path (plain)
                $new_email->template_plain  = 'woocommerce-offer-declined.php';
                $new_email->template_plain_path = plugin_dir_path(__FILE__). 'includes/emails/plain/';

                $new_email->trigger($offer_args);

                // Insert WP comment
                $comment_text = "<span>Updated - Status: </span>";
                $comment_text.= $post_status_text;

                // include update notes
                if(isset($offer_notes) && $offer_notes != '')
                {
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

                // insert comment meta
                if( $new_comment_id )
                {
                    add_comment_meta( $new_comment_id, 'angelleye_woocommerce_offer_id', $post_id, true );
                }

                do_action('ofw_after_auto_decline_offer_admin', $post_id);

                if(is_ajax()) {
                    die(); // this is required to return a proper result
                }
            }
        }
    

    /*
     * Action - Ajax 'add offer note' from manage offer details
     * @since	0.1.0
     */
    public function addOfferNoteCallback()
    {
        if(is_admin() && (defined('DOING_AJAX') || DOING_AJAX))
        {
            $post_id = $_POST["targetID"];
            // Get current data for Offer
            $post_data = get_post($post_id);
            // Filter Post Status Label
            $post_status_text = (strtolower($post_data->post_status) == 'publish') ? 'Pending' : $post_data->post_status;
            $post_status_text = ucwords(str_replace("-", " ", str_replace("offer", " ", strtolower($post_status_text))));

            $noteSendToBuyer = (isset($_POST["noteSendToBuyer"]) && $_POST["noteSendToBuyer"] != '') ? '1' : '';
            $offer_notes = $_POST['noteContent'];

            $current_user = wp_get_current_user();

            // Insert WP comment
            $comment_text = "<span>". __('Offer Note:', 'offers-for-woocommerce'). "</span>";
            if($noteSendToBuyer != '1')
            {
                $comment_text.= "&nbsp;". __('(admin only)', 'offers-for-woocommerce');
            }
            else
            {
                $comment_text.= "&nbsp;". __('(sent to buyer)', 'offers-for-woocommerce');
            }
            $comment_text.= "<br />" .$offer_notes;

            $data = array(
                'comment_post_ID' => '',
                'comment_author' => $current_user->user_login,
                'comment_author_email' => $current_user->user_email,
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

            // insert comment meta
            if( $new_comment_id )
            {
                add_comment_meta( $new_comment_id, 'angelleye_woocommerce_offer_id', $post_id, true );
            }

            if( $new_comment_id )
            {

                if($noteSendToBuyer == '1')
                {
                    // Email buyer the offer note (not private admin note)
                    /**
                     * Offer note email template
                     * @since   0.1.0
                     */
                    // set recipient email
                    $recipient = get_post_meta($post_id, 'offer_email', true);
                    $offer_id = $post_id;
                    $offer_uid = get_post_meta($post_id, 'offer_uid', true);
                    $offer_name = get_post_meta($post_id, 'offer_name', true);
                    $offer_email = $recipient;

                    $product_id = get_post_meta($post_id, 'offer_product_id', true);
                    $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
                    
                    $product = ( $variant_id ) ? wc_get_product( $variant_id ) : wc_get_product( $product_id );

                    // if buyercountered-offer previous then use buyer counter values
                    $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;

                    $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
                    $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
                    $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
                    $product_total = ($product_qty * $product_price_per);

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

                    if( $variant_id )
                    {
                        if ( $product->get_sku() ) {
                            $identifier = $product->get_sku();
                        } else {
                            $identifier = '#' . $product->variation_id;
                        }

                        $attributes = $product->get_variation_attributes();
                        $extra_data = ' &ndash; ' . implode( ', ', $attributes );
                        $offer_args['product_title_formatted'] = sprintf( __( '%s &ndash; %s%s', 'offers-for-woocommerce' ), $identifier, $product->get_title(), $extra_data );
                    }
                    else
                    {
                        $offer_args['product_title_formatted'] = $product->get_formatted_name();
                    }

                    // the email we want to send
                    $email_class = 'WC_Offer_Note_Email';

                    // load the WooCommerce Emails
                    $wc_emails = new WC_Emails();
                    $emails = $wc_emails->get_emails();

                    // select the email we want & trigger it to send
                    $new_email = $emails[$email_class];
                    $new_email->recipient = $recipient;

                    // set plugin slug in email class
                    $new_email->plugin_slug = 'offers-for-woocommerce';

                    // define email template/path (html)
                    $new_email->template_html  = 'woocommerce-offer-note.php';
                    $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

                    // define email template/path (plain)
                    $new_email->template_plain  = 'woocommerce-offer-note.php';
                    $new_email->template_plain_path = plugin_dir_path(__FILE__). 'includes/emails/plain/';

                    $new_email->trigger($offer_args);
                }

                $redirect_url = admin_url('post.php?post='.$post_id.'&action=edit&noheader=true&message=11');
                echo $redirect_url;
            }
            else
            {
                echo 'failed';
            }
            die(); // this is required to return a proper result
        }
    }

    /*
     * Action - Ajax 'bulk enable/disable tool' from offers settings/tools
     * @since	0.1.0
     */
    public function adminToolBulkEnableDisableCallback()
    {
        if(is_admin() && (defined('DOING_AJAX') || DOING_AJAX))
        {
            global $wpdb;
            $processed_product_id = array();
            $errors = FALSE;
            $products = FALSE;
            $product_ids = FALSE;
            $update_count = 0;
            $where_args = array(
                'post_type' => array( 'product', 'product_variation' ),
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'suppress_filters' => 1,
                'fields' => 'id=>parent',
                );
            $where_args['meta_query'] = array();

            $ofwc_bulk_action_type = ( isset( $_POST["actionType"] ) ) ? $_POST['actionType'] : FALSE;
            $ofwc_bulk_action_target_type = ( isset( $_POST["actionTargetType"] ) ) ? $_POST['actionTargetType'] : FALSE;
            $ofwc_bulk_action_target_where_type = ( isset( $_POST["actionTargetWhereType"] ) ) ? $_POST['actionTargetWhereType'] : FALSE;
            $ofwc_bulk_action_target_where_category = ( isset( $_POST["actionTargetWhereCategory"] ) ) ? $_POST['actionTargetWhereCategory'] : FALSE;
            $ofwc_bulk_action_target_where_product_type = ( isset( $_POST["actionTargetWhereProductType"] ) ) ? $_POST['actionTargetWhereProductType'] : FALSE;
            $ofwc_bulk_action_target_where_price_value = ( isset( $_POST["actionTargetWherePriceValue"] ) ) ? $_POST['actionTargetWherePriceValue'] : FALSE;
            $ofwc_bulk_action_target_where_stock_value = ( isset( $_POST["actionTargetWhereStockValue"] ) ) ? $_POST['actionTargetWhereStockValue'] : FALSE;
            $ofw_meta_key_value = ( isset($_POST['ofw_meta_key_value']) && !empty($_POST["ofw_meta_key_value"]) ) ?  $_POST['ofw_meta_key_value'] : FALSE;
            $autoAcceptDeclinePercentage = ( isset($_POST['autoAcceptDeclinePercentage']) && !empty($_POST['autoAcceptDeclinePercentage']) ) ? $_POST['autoAcceptDeclinePercentage'] : FALSE;

            if (!$ofwc_bulk_action_type || !$ofwc_bulk_action_target_type){
                $errors = TRUE;
            }
            if (!$ofw_meta_key_value){
                $errors = TRUE;
            }

            $ofwc_bulk_action_type = ($ofwc_bulk_action_type == 'enable' || $ofwc_bulk_action_type == 'accept_enable' || $ofwc_bulk_action_type == 'decline_enable') ? 'yes' : 'no';

            // All Products
            if ($ofwc_bulk_action_target_type == 'all'){
                $products = new WP_Query($where_args);
            }
            // Featured products
            elseif ($ofwc_bulk_action_target_type == 'featured') {
                array_push($where_args['meta_query'],
                    array(
                        'key' => '_featured',
                        'value' => 'yes'
                    )
                );
                $products = new WP_Query($where_args);
            }
            // Where
            elseif( $ofwc_bulk_action_target_type == 'where' && $ofwc_bulk_action_target_where_type)
            {
                // Where - By Category
                if ($ofwc_bulk_action_target_where_type == 'category' && $ofwc_bulk_action_target_where_category) {
                    $where_args['product_cat'] = $ofwc_bulk_action_target_where_category;
                    $products = new WP_Query($where_args);

                } // Where - By Product type
                elseif ($ofwc_bulk_action_target_where_type == 'product_type' && $ofwc_bulk_action_target_where_product_type) {
                    $where_args['product_type'] = $ofwc_bulk_action_target_where_product_type;
                    $products = new WP_Query($where_args);

                } // Where - By Price - greater than
                elseif ($ofwc_bulk_action_target_where_type == 'price_greater') {
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_price',
                            'value' => str_replace(",", "", number_format($ofwc_bulk_action_target_where_price_value, 2, '.', '') ),
                            'compare' => '>',
                            'type' => 'DECIMAL(10,2)'
                        )
                    );
                    $products = new WP_Query($where_args);

                } // Where - By Price - less than
                elseif ($ofwc_bulk_action_target_where_type == 'price_less') {
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_price',
                            'value' => str_replace(",", "", number_format($ofwc_bulk_action_target_where_price_value, 2, '.', '') ),
                            'compare' => '<',
                            'type' => 'DECIMAL(10,2)'
                        )
                    );
                    $products = new WP_Query($where_args);

                } // Where - By Stock - greater than
                elseif ($ofwc_bulk_action_target_where_type == 'stock_greater') {
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_manage_stock',
                            'value' => 'yes'
                        )
                    );
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_stock',
                            'value' => str_replace(",", "", number_format($ofwc_bulk_action_target_where_stock_value, 0) ),
                            'compare' => '>',
                            'type' => 'NUMERIC'
                        )
                    );
                    $products = new WP_Query($where_args);

                } // Where - By Stock - less than
                elseif ($ofwc_bulk_action_target_where_type == 'stock_less') {
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_manage_stock',
                            'value' => 'yes'
                        )
                    );
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_stock',
                            'value' => str_replace(",", "", number_format($ofwc_bulk_action_target_where_stock_value, 0) ),
                            'compare' => '<',
                            'type' => 'NUMERIC'
                        )
                    );
                    $products = new WP_Query($where_args);

                } // Where - Stock status 'instock'
                elseif ($ofwc_bulk_action_target_where_type == 'instock') {
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_stock_status',
                            'value' => 'instock'
                        )
                    );
                    $products = new WP_Query($where_args);

                } // Where - Stock status 'outofstock'
                elseif ($ofwc_bulk_action_target_where_type == 'outofstock') {
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_stock_status',
                            'value' => 'outofstock'
                        )
                    );
                    $products = new WP_Query($where_args);

                } // Where - Sold Individually
                elseif ($ofwc_bulk_action_target_where_type == 'sold_individually') {
                    array_push($where_args['meta_query'],
                        array(
                            'key' => '_sold_individually',
                            'value' => 'yes'
                        )
                    );
                    $products = new WP_Query($where_args);
                }

            }
            else
            {
                $errors = TRUE;
            }

            // Update posts
            if(!$errors && $products)
            {
                if(count($products->posts) < 1)
                {
                    $errors = TRUE;
                    $update_count = 'zero';
                    $redirect_url = admin_url('options-general.php?page=offers-for-woocommerce&tab=tools&processed='.$update_count);
                    echo $redirect_url;
                }
                else
                {
                    foreach($products->posts as $target)
                    {
                        $target_product_id = ( $target->post_parent != '0' ) ? $target->post_parent : $target->ID;
                        if( get_post_type( $target_product_id ) == 'product' && !in_array($target_product_id, $processed_product_id) ) {
                            if(!update_post_meta($target_product_id, $ofw_meta_key_value , $ofwc_bulk_action_type )) {
                            } else {
                                $processed_product_id[$target_product_id] = $target_product_id;
                            }
                            if( $autoAcceptDeclinePercentage ) {
                                $ofw_auto_accept_or_decline_percentage_key = str_replace("enabled", "percentage", $ofw_meta_key_value);
                                update_post_meta($target_product_id, $ofw_auto_accept_or_decline_percentage_key , $autoAcceptDeclinePercentage );
                                $processed_product_id[$target_product_id] = $target_product_id;
                            }
                        }
                    }
                    $update_count = count($processed_product_id);
                }
            }

            // return
            if( !$errors )
            {
                if($update_count == 0)
                {
                   $update_count = 'zero';
                }

                $redirect_url = admin_url('options-general.php?page=offers-for-woocommerce&tab=tools&processed='.$update_count);
                echo $redirect_url;
            }
            else
            {
                //echo 'failed';
            }
            die(); // this is required to return a proper result
        }
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
        require( 'includes/class-wc-accepted-offer-email.php' );
        require( 'includes/class-wc-declined-offer-email.php' );
        require( 'includes/class-wc-countered-offer-email.php' );
        require( 'includes/class-wc-offer-on-hold-email.php' );
        require( 'includes/class-wc-offer-note-email.php' );

        // add the email class to the list of email classes that WooCommerce loads
        $email_classes['WC_Accepted_Offer_Email'] = new WC_Accepted_Offer_Email();
        $email_classes['WC_Declined_Offer_Email'] = new WC_Declined_Offer_Email();
        $email_classes['WC_Countered_Offer_Email'] = new WC_Countered_Offer_Email();
        $email_classes['WC_Offer_On_Hold_Email'] = new WC_Offer_On_Hold_Email();
        $email_classes['WC_Offer_Note_Email'] = new WC_Offer_Note_Email();

        return $email_classes;
    }

    /**
     * Add WP Notices
     * @since   0.1.0
     */
    public function aeofwc_admin_notices()
    {
        global $current_user ;
        $user_id = $current_user->ID;

        $screen = get_current_screen();

        // if filtering Offers edit page by 'author'
        if ( "edit-woocommerce_offer" == $screen->id && is_admin() ) {
            $author_id = (isset($_GET['author']) && is_numeric($_GET['author'])) ? $_GET['author'] : '';
            if($author_id)
            {
                $author_data = get_userdata($author_id);
                // not valid user id
                if(!$author_data) return;

                echo '<div class="notice error angelleye-admin-notice-filterby-author">';
                echo '<p>'. __('Currently filtered by user', 'offers-for-woocommerce'). '&nbsp;<strong>"' . $author_data->user_login . '"</strong> <a href="edit.php?post_type=woocommerce_offer">'. __('Click here to reset filter', 'offers-for-woocommerce'). '</a></p>';
                echo '</div>';
            }
        }

        if ( $this->plugin_screen_hook_suffix == $screen->id && is_admin() ) {

            // Tools - Bulk enable/disable offers
            $processed = (isset($_GET['processed']) ) ? $_GET['processed'] : FALSE;
            if($processed)
            {
                if($processed == 'zero')
                {
                    echo '<div class="updated">';
                    echo '<p>'. sprintf( __('Action completed; %s records processed.', 'offers-for-woocommerce'), '0');
                    echo '</div>';
                }
                else
                {
                    echo '<div class="updated">';
                    echo '<p>'. sprintf( __('Action completed; %s records processed. ', 'offers-for-woocommerce'), $processed);
                    echo '</div>';
                }
            }
        }

        /**
         * Detect other known plugins that might conflict; show warnings
         */
        if ( is_plugin_active( 'social-networks-auto-poster-facebook-twitter-g/NextScripts_SNAP.php' ) )
        {
            // Check that the user hasn't already clicked to ignore the message
            if ( ! get_user_meta($user_id, 'angelleye_offers_for_woocommerce_ignore_next_scripts_snap') ) {
                $get_symbol = (strpos($_SERVER['REQUEST_URI'], "?")) ? "&" : "?";
                echo '<div class="updated"> <p><strong>'. __('We notice you are running the NextScripts Social Networks Auto-Poster plugin.', 'offers-for-woocommerce') .'</strong><br />'. __('Please make sure to exclude the custom post type "woocommerce_offer" in the {SNAP} Social Networks Auto-Poster settings in order to avoid conflicts with new offers.', 'offers-for-woocommerce') .' | <a href="'. $_SERVER['REQUEST_URI'] . $get_symbol . 'angelleye_offers_for_woocommerce_ignore_next_scripts_snap=0">Hide Notice</a></p> </div>';
            }
        }

        return;
    }

    /**
     * Adds help tab content for manage offer screen
     * @param $contextual_help
     * @param $screen_id
     * @param $screen
     * @return mixed
     */
    function ae_ofwc_contextual_help( $contextual_help, $screen_id, $screen ) {

        // Only add to certain screen(s). The add_help_tab function for screen was introduced in WordPress 3.3.
        if ( "edit-woocommerce_offer" != $screen->id || ! method_exists( $screen, 'add_help_tab' ) )
            return $contextual_help;

        $screen->add_help_tab( array(
            'id'      => 'angelleye-offers-for-woocommerce-overview-tab_01',
            'title'   => __( 'Overview', 'offers-for-woocommerce' ),
            'content' => '<p>' . __( 'This plugin is currently in development. Please send any feedback or bug reports to andrew@angelleye.com. Thank you.', 'offers-for-woocommerce' ) . '</p>',
        ));

        $screen->add_help_tab( array(
            'id'      => 'angelleye-offers-for-woocommerce-overview-tab_02',
            'title'   => __( 'Help Tab', 'offers-for-woocommerce' ),
            'content' => '<p>' . __( 'This plugin is currently in development. Please send any feedback or bug reports to andrew@angelleye.com. Thank you.', 'offers-for-woocommerce' ) . '</p>',
        ));

        $screen->add_help_tab( array(
            'id'      => 'angelleye-offers-for-woocommerce-overview-tab_03',
            'title'   => __( 'Help Tab', 'offers-for-woocommerce' ),
            'content' => '<p>' . __( 'This plugin is currently in development. Please send any feedback or bug reports to andrew@angelleye.com. Thank you.', 'offers-for-woocommerce' ) . '</p>',
        ));

        return $contextual_help;
    }

    /*
     * Check WooCommerce is available
     * @since   0.1.0
     */
    public function ae_ofwc_check_woocommerce_available()
    {
        if (is_admin()) {

            global $current_user;
            $user_id = $current_user->ID;

            if (!function_exists('is_plugin_active_for_network')) {
                require_once(ABSPATH . '/wp-admin/includes/plugin.php');
            }

            if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !is_plugin_active_for_network('woocommerce/woocommerce.php'))
            {
                if ( in_array('offers-for-woocommerce/offers-for-woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) || is_plugin_active_for_network('offers-for-woocommerce/offers-for-woocommerce.php')) {

                    // deactivate offers for woocommerce plugin
                    deactivate_plugins(plugin_dir_path(realpath(dirname(__FILE__))) . 'offers-for-woocommerce.php');

                    // remove hide nag msg
                    //delete_user_meta($user_id, 'angelleye_offers_for_woocommerce_ignore_01');

                    // redirect
                    //wp_redirect('plugins.php');
                }
                add_action( 'admin_notices', array( $this, 'ae_ofwc_admin_notice_woocommerce_mia' ) );
            }
            else
            {
                // remove hide nag msg
                delete_user_meta($user_id, 'angelleye_offers_for_woocommerce_ignore_01');
            }
        }
    }

    public function ae_ofwc_admin_notice_woocommerce_mia()
    {
        global $current_user ;
        $user_id = $current_user->ID;

        // Check that the user hasn't already clicked to ignore the message
        if ( ! get_user_meta($user_id, 'angelleye_offers_for_woocommerce_ignore_01') ) {
            printf('<div class="updated"> <p> %s  | <a href="%2$s">Hide Notice</a></p> </div>', __('<strong>Offers for WooCommerce has been deactivated; WooCommerce is required.</strong><br />Please make sure that WooCommerce is installed and activated before activating Offers for WooCommerce.', 'offers-for-woocommerce'), '?angelleye_offers_for_woocommerce_ignore_01=0');
        }
    }

    /**
     * Add ignore nag message for admin notices
     * @since   0.1.0
     */
    public function ae_ofwc_check_woocommerce_nag_notice_ignore()
    {
        global $current_user;
        $user_id = $current_user->ID;

        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['angelleye_offers_for_woocommerce_ignore_01']) && '0' == $_GET['angelleye_offers_for_woocommerce_ignore_01'] ) {
            add_user_meta($user_id, 'angelleye_offers_for_woocommerce_ignore_01', 'true');
        }

        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['angelleye_offers_for_woocommerce_ignore_next_scripts_snap']) && '0' == $_GET['angelleye_offers_for_woocommerce_ignore_next_scripts_snap'] ) {
            add_user_meta($user_id, 'angelleye_offers_for_woocommerce_ignore_next_scripts_snap', 'true');
        }
    }

    /**
     * Action - Bulk action - Enable/Disable Offers on WooCommerce products
     * @since   1.0.1
     */
    public function custom_bulk_admin_footer() {

        global $post_type;

        if($post_type == 'product') {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('<option>').val('enable_offers').text('<?php _e('Enable Offers', 'offers-for-woocommerce');?>').appendTo("select[name='action']");
                    jQuery('<option>').val('enable_offers').text('<?php _e('Enable Offers', 'offers-for-woocommerce');?>').appendTo("select[name='action2']");
                    jQuery('<option>').val('disable_offers').text('<?php _e('Disable Offers', 'offers-for-woocommerce');?>').appendTo("select[name='action']");
                    jQuery('<option>').val('disable_offers').text('<?php _e('Disable Offers', 'offers-for-woocommerce');?>').appendTo("select[name='action2']");
                });
            </script>
        <?php
        }
    }

    /**
     * Action - Bulk action - Process Enable/Disable Offers on WooCommerce products
     * @since   1.0.1
     */
    public function custom_bulk_action() {

        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();

        $post_ids = (isset($_REQUEST['post']) ) ? $_REQUEST['post'] : FALSE;

        if($post_ids) {
            switch ($action) {
                case 'enable_offers':
                    $updated_count = 0;

                    foreach ($post_ids as $post_id) {
                        // update post
                        update_post_meta( $post_id, 'offers_for_woocommerce_enabled', 'yes');
                        $updated_count++;
                    }
                    // build the redirect url
                    $sendback = add_query_arg(array('enabled_offers' => $updated_count, 'ids' => join(',', $post_ids)), 'edit.php?post_type=product');
                    $sendback = esc_url_raw($sendback);

                    break;
                case 'disable_offers':
                    $updated_count = 0;

                    foreach ($post_ids as $post_id) {
                        // update post
                        update_post_meta( $post_id, 'offers_for_woocommerce_enabled', 'no');
                        $updated_count++;
                    }
                    // build the redirect url
                    $sendback = add_query_arg(array('disabled_offers' => $updated_count, 'ids' => join(',', $post_ids)), 'edit.php?post_type=product');
                    $sendback = esc_url_raw($sendback);

                    break;
                default:
                    return;
            }

            wp_redirect($sendback);
            exit();
        }
    }

    /**
     * Action - Show admin notice for bulk action Enable/Disable Offers on WooCommerce products
     * @since   1.0.1
     */
    public function custom_bulk_admin_notices()
    {
        global $post_type, $pagenow;

        if($pagenow == 'edit.php' && $post_type == 'product' && isset($_REQUEST['enabled_offers']) && (int) $_REQUEST['enabled_offers'] && ($_REQUEST['enabled_offers'] > 0)) {
            $message = sprintf( __( 'Offers enabled for %s products.', 'offers-for-woocommerce' ), number_format_i18n( $_REQUEST['enabled_offers'] ) );
            echo '<div class="updated"><p>'.$message.'</p></div>';
        }

        if($pagenow == 'edit.php' && $post_type == 'product' && isset($_REQUEST['disabled_offers']) && (int) $_REQUEST['disabled_offers'] && ($_REQUEST['disabled_offers'] > 0)) {
            $message = sprintf( __( 'Offers disabled for %s products.', 'offers-for-woocommerce' ), number_format_i18n( $_REQUEST['disabled_offers'] ) );
            echo '<div class="updated"><p>'.$message.'</p></div>';
        }
    }
    
    /**
     * Action - Quick edit - Process Enable/Disable Offers on WooCommerce products display field
     * @since   1.2
     */
    public function woocommerce_product_quick_edit_end_own() {
        global $post;
        $field_value = 'yes';
        $button_options_general = get_option('offers_for_woocommerce_options_general');
        $is_default_enable = 'no';
        if( isset($button_options_general['general_setting_enable_offers_by_default']) && $button_options_general['general_setting_enable_offers_by_default'] == '1') {
            $is_enable = 'yes';
        } else {
            $is_enable = 'no';
        }
        ?>
        <br class="clear" />
        <label class="alignleft">
            <span class="checkbox-title"><?php _e( 'Enable Offers?', 'offers-for-woocommerce' ); ?></span>
            <input type="checkbox" name="offers_for_woocommerce_enabled" value="yes" <?php echo checked( $field_value, $is_enable, false ); ?>>
        </label>
        <br class="clear" />
        
        <label class="alignleft">
            <span class="checkbox-title"><?php _e( 'Enable Auto Accept Offers?', 'offers-for-woocommerce' ); ?></span>
            <input type="checkbox" name="_offers_for_woocommerce_auto_accept_enabled" value="yes" <?php echo checked( $field_value, $is_default_enable, false ); ?>>
        </label>                  
        <label class="alignleft" for="_offers_for_woocommerce_auto_accept_percentage"><?php echo __( 'Auto Accept Percentage', 'offers-for-woocommerce' ) ; ?>
            <input type="number" placeholder="Enter Percentage" value="" min="1" max="100" id="_offers_for_woocommerce_auto_accept_percentage" name="_offers_for_woocommerce_auto_accept_percentage" style="" class="short">                   
        </label>
        
        
        <label class="alignleft">
            <span class="checkbox-title"><?php _e( 'Enable Auto Decline Offers?', 'offers-for-woocommerce' ); ?></span>
            <input type="checkbox" name="_offers_for_woocommerce_auto_decline_enabled" value="yes" <?php echo checked( $field_value, $is_default_enable, false ); ?>>
        </label>                  
           
        <label class="alignleft" for="_offers_for_woocommerce_auto_decline_percentage"><?php echo __( 'Auto Decline Percentage', 'offers-for-woocommerce' ) ; ?>
            <input type="number" placeholder="Enter Percentage" value="" min="1" max="100" id="_offers_for_woocommerce_auto_decline_percentage" name="_offers_for_woocommerce_auto_decline_percentage" style="" class="short">                   
        </label>

        <?php
    }
    
    /**
     * Action - Quick edit - Process Enable/Disable Offers on WooCommerce products save field
     * @since   1.2
     */
    public function woocommerce_product_quick_edit_save_own($product) {
        $post_id = $product->id;
        update_post_meta( $post_id, 'offers_for_woocommerce_enabled', ( isset($_REQUEST['offers_for_woocommerce_enabled']) && $_REQUEST['offers_for_woocommerce_enabled'] ) ? 'yes' : 'no' );
        update_post_meta( $post_id, '_offers_for_woocommerce_auto_accept_enabled', ( isset($_REQUEST['_offers_for_woocommerce_auto_accept_enabled']) && $_REQUEST['_offers_for_woocommerce_auto_accept_enabled'] ) ? 'yes' : 'no' );
        update_post_meta( $post_id, '_offers_for_woocommerce_auto_decline_enabled', ( isset($_REQUEST['_offers_for_woocommerce_auto_decline_enabled']) && $_REQUEST['_offers_for_woocommerce_auto_decline_enabled'] ) ? 'yes' : 'no' );
        if( isset($_REQUEST['_offers_for_woocommerce_auto_accept_percentage']) && !empty($_REQUEST['_offers_for_woocommerce_auto_accept_percentage']) ) {
            update_post_meta( $post_id, '_offers_for_woocommerce_auto_accept_percentage', $_REQUEST['_offers_for_woocommerce_auto_accept_percentage']);
        }
        if( isset($_REQUEST['_offers_for_woocommerce_auto_decline_percentage']) && !empty($_REQUEST['_offers_for_woocommerce_auto_decline_percentage']) ) {
            update_post_meta( $post_id, '_offers_for_woocommerce_auto_decline_percentage', $_REQUEST['_offers_for_woocommerce_auto_decline_percentage']);
        }
    } 
    
    public function my_admin_footer_function() {
        $screen = get_current_screen();
        if($screen->post_type == 'woocommerce_offer') {
            add_thickbox();
            $coupon_list = get_posts('post_type=shop_coupon');
            ?>
        <div id="ofw_send_coupon_declineOfferFromGrid" style="display: none;" class="wrap">
                <form action="" id="declineOfferFromGrid">
                    <?php if($coupon_list) { ?>
                    <div><p>You may be declining this particular offer, but including a coupon code in the email notification to the buyer can often entice them to go ahead with a purchase. Select a coupon code here if you would like to include it in the declined offer email.</p></div>
                    <label for="ofw_coupon_list"><?php _e( 'Coupon List', 'offers-for-woocommerce' ); ?></label>
                    <select id="ofw_coupon_list" name="ofw_coupon_list">
                        <option value="" ><?php _e( 'Select Coupon', 'offers-for-woocommerce' ); ?></option>
                        <?php foreach ( $coupon_list as $coupon  ) : ?>
                            <option value="<?php echo $coupon->post_name; ?>"><?php echo $coupon->post_title; ?></option>
                        <?php endforeach; ?>
                    </select>
                      </select>
                    <?php } else { 
                        echo __('No Coupons found.', 'offers-for-woocommerce');
                    }
                     ?>
                    <br><br>
                    <input type="hidden" name="offer-id" id="offer-id" value="">
                    <?php if($coupon_list) { ?>
                    <input type="button" value="<?php _e( 'Send coupon & Decline', 'offers-for-woocommerce' ); ?>" class="button ofw-decline-popup" id="send_coupon_decline_offer" name="send_coupon_decline_offer">
                    <?php } ?>
                    <input type="button" value="<?php _e( 'Decline', 'offers-for-woocommerce' ); ?>" class="button ofw-decline-popup" id="decline_offer" name="decline_offer">
                </form>
             </div>
            <a style="display: none" href="#TB_inline?height=150&amp;width=260&amp;&inlineId=ofw_send_coupon_declineOfferFromGrid" class="thickbox ofw_send_coupon_declineOfferFromGrid">View my inline content!</a>	
            <?php 
        }
    }
    public function offers_for_woocommerce_setting_tab_own() {
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_settings';
        ?>
        <a href="?page=<?php echo 'offers-for-woocommerce'; ?>&tab=recaptcha" class="nav-tab <?php echo $active_tab == 'recaptcha' ? 'nav-tab-active' : ''; ?>"><?php echo __('Google reCAPTCHA', 'offers-for-woocommerce'); ?></a>
        <a href="?page=<?php echo 'offers-for-woocommerce'; ?>&tab=mailchimp" class="nav-tab <?php echo $active_tab == 'mailchimp' ? 'nav-tab-active' : ''; ?>"><?php echo __('MailChimp', 'offers-for-woocommerce'); ?></a>
        <a href="?page=<?php echo 'offers-for-woocommerce'; ?>&tab=constant_contact" class="nav-tab <?php echo $active_tab == 'constant_contact' ? 'nav-tab-active' : ''; ?>"><?php echo __('Constant Contact', 'offers-for-woocommerce'); ?></a>
        <a href="?page=<?php echo 'offers-for-woocommerce'; ?>&tab=mailpoet" class="nav-tab <?php echo $active_tab == 'mailpoet' ? 'nav-tab-active' : ''; ?>"><?php echo __('MailPoet', 'offers-for-woocommerce'); ?></a>
        <?php 
        
    }   
       
    public function offers_for_woocommerce_setting_tab_content_own() {
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_settings';
        if( $active_tab == 'mailchimp' ) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-offers-for-woocommerce-html-output.php';
            include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-mailchimp-helper.php';
            $OFW_Woocommerce_MailChimp_Helper = new AngellEYE_Offers_for_Woocommerce_MailChimp_Helper();
            $mcapi_setting_fields = $OFW_Woocommerce_MailChimp_Helper->offers_for_woocommerce_mcapi_setting_fields();
            $Html_output = new AngellEYE_Offers_for_Woocommerce_Html_output();
            ?>
            <form id="ofw_mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
                <?php $Html_output->init($mcapi_setting_fields); ?>
                <p class="submit">
                    <input type="submit" name="ofw_mailChimp_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
                </p>
            </form>
            <?php
        }
        
        if( $active_tab == 'constant_contact' ) {
            include_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-offers-for-woocommerce-html-output.php';
            include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-constant-contact-helper.php';
            $OFW_Woocommerce_ConstantContact_Helper = new AngellEYE_Offers_for_Woocommerce_ConstantContact_Helper();
            $ccapi_setting_fields = $OFW_Woocommerce_ConstantContact_Helper->ofw_ccapi_setting_field();
            $Html_output = new AngellEYE_Offers_for_Woocommerce_Html_output();
            ?>
            <form id="constantContact_integration_form" enctype="multipart/form-data" action="" method="post">
                <?php $Html_output->init($ccapi_setting_fields); ?>
                <p class="submit">
                    <input type="submit" name="ofw_constantContact_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
                </p>
            </form>
            <?php
        }
        
        if( $active_tab == 'mailpoet' ) {
            if (( class_exists('WYSIJA') )) {
                include_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-offers-for-woocommerce-html-output.php';
                include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-mailpoet-helper.php';
                $OFW_Woocommerce_MailPoet_Helper = new AngellEYE_Offers_for_Woocommerce_MailPoet_Helper();
                $mailpoet_setting_fields = $OFW_Woocommerce_MailPoet_Helper->offers_for_woocommerce_mailpoet_setting_fields();
                $Html_output = new AngellEYE_Offers_for_Woocommerce_Html_output();
                ?>
                <form id="constantContact_integration_form" enctype="multipart/form-data" action="" method="post">
                    <?php $Html_output->init($mailpoet_setting_fields); ?>
                    <p class="submit">
                        <input type="submit" name="ofw_mailpoet_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
                    </p>
                </form>
                <?php
            } else {
                echo "<br><br><strong>MailPoet subscribe</strong> requires <strong><a target='_blank' href='http://wordpress.org/plugins/wysija-newsletters/' rel='nofollow'>MailPoet plugin</a></strong> plugin to work normally. Please activate it or install it.<br /><br />Back to the WordPress <a href='" . admin_url('plugin-install.php?tab=search&s=MailPoet') . "'>Plugins page</a>.";
            }
        }
        
        if( $active_tab == 'recaptcha' ) {
            include_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-offers-for-woocommerce-html-output.php';
            include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-recaptcha-helper.php';
            $OFW_Woocommerce_Recaptcha_Helper = new AngellEYE_Offers_for_Woocommerce_Recaptcha_Helper();
            $recaptcha_setting_field = $OFW_Woocommerce_Recaptcha_Helper->ofw_recaptcha_setting_field();
            $Html_output = new AngellEYE_Offers_for_Woocommerce_Html_output();
            ?>
            <form id="recaptcha_integration_form" enctype="multipart/form-data" action="" method="post">
                <?php $Html_output->init($recaptcha_setting_field); ?>
                <p class="submit">
                    <input type="submit" name="ofw_recaptcha_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
                </p>
            </form>
            <?php
        }

    }    

    public static function offers_for_woocommerce_setting_tab_content_save_own() {
        if( isset($_POST['ofw_mailChimp_integration']) ) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-offers-for-woocommerce-html-output.php';
            include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-mailchimp-helper.php';
            $OFW_Woocommerce_MailChimp_Helper = new AngellEYE_Offers_for_Woocommerce_MailChimp_Helper();
            $mcapi_setting_fields = $OFW_Woocommerce_MailChimp_Helper->offers_for_woocommerce_mcapi_setting_fields();
            $Html_output = new AngellEYE_Offers_for_Woocommerce_Html_output();
            $Html_output->save_fields($mcapi_setting_fields);
        }
        if( isset($_POST['ofw_constantContact_integration']) ) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-offers-for-woocommerce-html-output.php';
            include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-constant-contact-helper.php';
            $OFW_Woocommerce_ConstantContact_Helper = new AngellEYE_Offers_for_Woocommerce_ConstantContact_Helper();
            $ccapi_setting_fields = $OFW_Woocommerce_ConstantContact_Helper->ofw_ccapi_setting_field();
            $Html_output = new AngellEYE_Offers_for_Woocommerce_Html_output();
            $Html_output->save_fields($ccapi_setting_fields);
        }
        if( isset($_POST['ofw_mailpoet_integration']) ) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-offers-for-woocommerce-html-output.php';
            include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-mailpoet-helper.php';
            $OFW_Woocommerce_MailPoet_Helper = new AngellEYE_Offers_for_Woocommerce_MailPoet_Helper();
            $mailpoet_setting_fields = $OFW_Woocommerce_MailPoet_Helper->offers_for_woocommerce_mailpoet_setting_fields();
            $Html_output = new AngellEYE_Offers_for_Woocommerce_Html_output();
            $Html_output->save_fields($mailpoet_setting_fields);
        }
        if( isset($_POST['ofw_recaptcha_integration']) ) {
            
            include_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-offers-for-woocommerce-html-output.php';
            include_once OFFERS_FOR_WOOCOMMERCE_PLUGIN_DIR . '/includes/class-offers-for-woocommerce-recaptcha-helper.php';
            $OFW_Woocommerce_Recaptcha_Helper = new AngellEYE_Offers_for_Woocommerce_Recaptcha_Helper();
            $recaptcha_setting_field = $OFW_Woocommerce_Recaptcha_Helper->ofw_recaptcha_setting_field();
            $Html_output = new AngellEYE_Offers_for_Woocommerce_Html_output();
            $Html_output->save_fields($recaptcha_setting_field);
        }
        
    }
    
    public function ofw_auto_accept_decline_from_email() {
        if( isset($_GET['from_email_action']) && !empty($_GET['from_email_action']) && $_GET['ofw_from_email'] == true && ( isset($_GET['targetID']) && !empty($_GET['targetID']))) {
            if($_GET['from_email_action'] == 'declineOfferFromGrid') {
                do_action('declineOfferFromGrid');
            }
            if($_GET['from_email_action'] == 'approveOfferFromGrid') {
                do_action('approveOfferFromGrid');
            }
            wp_redirect( admin_url( 'post.php?post=' . $_GET['targetID'] . '&action=edit' ) );
            exit();
        }
    }
    
    /**
    * Create capabilities.
    */
    public function create_woocommerce_offer_capabilities() {
        global $wp_roles;

        $woocommerce_offer_capabilities = get_option('woocommerce_offer_capabilities');

        if (empty($woocommerce_offer_capabilities)) {

            if (!class_exists('WP_Roles')) {
                return;
            }

            if (!isset($wp_roles)) {
                $wp_roles = new WP_Roles();
            }

            $capabilities = $this->get_core_capabilities();

            foreach ($capabilities as $cap_group) {
                foreach ($cap_group as $cap) {
                    $wp_roles->add_cap('shop_manager', $cap);
                    $wp_roles->add_cap('administrator', $cap);
                }
            }
        } else {
            update_option('woocommerce_offer_capabilities', true);
        }
    }

    /**
     *
     * @return array
     */
    public static function get_core_capabilities() {
        $capabilities = array();

        $capability_types = array('woocommerce_offer');

        foreach ($capability_types as $capability_type) {

            $capabilities[$capability_type] = array(
                // Post type
                "edit_{$capability_type}",
                "read_{$capability_type}",
                "delete_{$capability_type}",
                "edit_{$capability_type}s",
                "edit_others_{$capability_type}s",
                "publish_{$capability_type}s",
                "read_private_{$capability_type}s",
                "delete_{$capability_type}s",
                "delete_private_{$capability_type}s",
                "delete_published_{$capability_type}s",
                "delete_others_{$capability_type}s",
                "edit_private_{$capability_type}s",
                "edit_published_{$capability_type}s",
            );
        }

        return $capabilities;
    }
    
    public function ofw_is_anonymous_communication_enable() {
        $offers_for_woocommerce_options_general = get_option('offers_for_woocommerce_options_general');
        if( isset($offers_for_woocommerce_options_general['general_setting_enable_anonymous_communication']) && $offers_for_woocommerce_options_general['general_setting_enable_anonymous_communication'] == 1 ) {
            return true;
        } 
        return false;
    }
    
    public function ofw_anonymous_title($title, $id) {
        if( get_post_type( $id ) == 'woocommerce_offer'  && $this->ofw_is_anonymous_communication_enable() ) {
            return 'Potential Buyer';
        } else {
            return $title;
        }
    }
    
    public function ofw_woocommerce_cart_shipping_packages($packages) {
        WC()->session->__unset( 'shipping_for_package' );
        return $packages;
    }
    
    public function ofw_is_show_pending_offer_enable() {
        $offers_for_woocommerce_options_general = get_option('offers_for_woocommerce_options_general');
        if (isset($offers_for_woocommerce_options_general['general_setting_show_pending_offer']) && $offers_for_woocommerce_options_general['general_setting_show_pending_offer'] == 1) {
            return true;
        }
        return false;
    }
    
    public function ofwc_decline_all_offers() {
        global $wpdb; // this is how you get access to the database
        $product_id = $_POST["targetID"];
        $post_count = $this->ofw_get_pending_offer_by_product_id($_POST['id']);
        
        do_action('ofw_before_auto_decline_offer_admin', $post_id);
        // Get current data for Offer prior to save
        $post_data = get_post($post_id);
        $coupon_code = ( isset($_POST["coupon_code"]) && !empty($_POST["coupon_code"]) ) ? $_POST["coupon_code"] : '';

        // if buyercountered-offer previous then use buyer counter values
        $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;

        $table = $wpdb->prefix . "posts";
        $data_array = array(
            'post_status' => 'declined-offer',
            'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
            'post_modified_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1 ))
        );
        $where = array('ID' => $post_id);
        $wpdb->update( $table, $data_array, $where );

        // Filter Post Status Label
        $post_status_text = __('Declined', 'offers-for-woocommerce');

        // set update notes
        $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? $_POST['angelleye_woocommerce_offer_status_notes'] : '';

        /**
         * Email customer declined email template
         * @since   0.1.0
         */
        // set recipient email
        $recipient = get_post_meta($post_id, 'offer_email', true);
        $offer_id = $post_id;
        $offer_uid = get_post_meta($post_id, 'offer_uid', true);
        $offer_name = get_post_meta($post_id, 'offer_name', true);
        $offer_email = $recipient;

        $product_id = get_post_meta($post_id, 'offer_product_id', true);
        $variant_id = get_post_meta($post_id, 'offer_variation_id', true);

        $product = ( $variant_id ) ? wc_get_product( $variant_id ) : wc_get_product( $product_id );

        $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
        $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
        $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
        $product_total = number_format(round($product_qty * $product_price_per, 2), 2, '.', '');

        // if buyercountered-offer status, update postmeta values for quantity,price,amount
        if( $is_offer_buyer_countered_status )
        {
            update_post_meta( $post_id, 'offer_quantity', $product_qty );
            update_post_meta( $post_id, 'offer_price_per', $product_price_per );
            update_post_meta( $post_id, 'offer_shipping_cost', $product_shipping_cost );
            update_post_meta( $post_id, 'offer_amount', $product_total );
        }
        if ( get_post_status ( $post_id ) != 'declined-offer' ) {

            do_action('ofw_before_auto_decline_offer_admin', $post_id);
            // Get current data for Offer prior to save
            $post_data = get_post($post_id);
            $coupon_code = ( isset($_POST["coupon_code"]) && !empty($_POST["coupon_code"]) ) ? $_POST["coupon_code"] : '';

            // if buyercountered-offer previous then use buyer counter values
            $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;

            $table = $wpdb->prefix . "posts";
            $data_array = array(
                'post_status' => 'declined-offer',
                'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                'post_modified_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1 ))
            );
            $where = array('ID' => $post_id);
            $wpdb->update( $table, $data_array, $where );

            // Filter Post Status Label
            $post_status_text = __('Declined', 'offers-for-woocommerce');

            // set update notes
            $offer_notes = (isset($_POST['angelleye_woocommerce_offer_status_notes']) && $_POST['angelleye_woocommerce_offer_status_notes'] != '') ? $_POST['angelleye_woocommerce_offer_status_notes'] : '';

            /**
             * Email customer declined email template
             * @since   0.1.0
             */
            // set recipient email
            $recipient = get_post_meta($post_id, 'offer_email', true);
            $offer_id = $post_id;
            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
            $offer_name = get_post_meta($post_id, 'offer_name', true);
            $offer_email = $recipient;

            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);

            $product = ( $variant_id ) ? wc_get_product( $variant_id ) : wc_get_product( $product_id );

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_shipping_cost = get_post_meta($post_id, 'offer_shipping_cost', true);
            $product_total = number_format(round($product_qty * $product_price_per, 2), 2, '.', '');

            // if buyercountered-offer status, update postmeta values for quantity,price,amount
            if( $is_offer_buyer_countered_status )
            {
                update_post_meta( $post_id, 'offer_quantity', $product_qty );
                update_post_meta( $post_id, 'offer_price_per', $product_price_per );
                update_post_meta( $post_id, 'offer_shipping_cost', $product_shipping_cost );
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
                'offer_notes' => $offer_notes,
                'coupon_code' => $coupon_code
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
        }
        else
        {
            $offer_args['product_title_formatted'] = $product->get_formatted_name();
        }

        // the email we want to send
        $email_class = 'WC_Declined_Offer_Email';

        // load the WooCommerce Emails
        $wc_emails = new WC_Emails();
        $emails = $wc_emails->get_emails();

        // select the email we want & trigger it to send
        $new_email = $emails[$email_class];
        $new_email->recipient = $recipient;

        $new_email->plugin_slug = 'offers-for-woocommerce';

        // define email template/path (html)
        $new_email->template_html  = 'woocommerce-offer-declined.php';
        $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

        // define email template/path (plain)
        $new_email->template_plain  = 'woocommerce-offer-declined.php';
        $new_email->template_plain_path = plugin_dir_path(__FILE__). 'includes/emails/plain/';

        $new_email->trigger($offer_args);

        // Insert WP comment
        $comment_text = "<span>Updated - Status: </span>";
        $comment_text.= $post_status_text;

        // include update notes
        if(isset($offer_notes) && $offer_notes != '')
        {
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

        // insert comment meta
        if( $new_comment_id )
        {
            add_comment_meta( $new_comment_id, 'angelleye_woocommerce_offer_id', $post_id, true );
        }

        do_action('ofw_after_auto_decline_offer_admin', $post_id);

        if(is_ajax()) {
            die(); // this is required to return a proper result
        }
    }
    
    public function ofw_display_pending_offer_lable_product_details_page($product_id) {
        if ($this->ofw_is_show_pending_offer_enable()) {
            global $wpdb;
            $total_result = $wpdb->get_results($wpdb->prepare("
                    SELECT SUM( postmeta.meta_value ) AS total_qty, COUNT(posts.ID) as total_offer
                    FROM $wpdb->postmeta AS postmeta
                    JOIN $wpdb->postmeta pm2 ON pm2.post_id = postmeta.post_id
                    INNER JOIN $wpdb->posts AS posts ON ( posts.post_type = 'woocommerce_offer' AND posts.post_status NOT LIKE 'completed-offer')
                    WHERE postmeta.meta_key LIKE 'offer_quantity' AND pm2.meta_key LIKE 'offer_product_id' AND pm2.meta_value LIKE %d
                    AND postmeta.post_id = posts.ID LIMIT 0, 99
            ", $product_id), ARRAY_A);
            $total_qty = (isset($total_result[0]['total_qty']) && !empty($total_result[0]['total_qty'])) ? $total_result[0]['total_qty'] : 0;
            $total_offer = (isset($total_result[0]['total_offer']) && !empty($total_result[0]['total_offer'])) ? $total_result[0]['total_offer'] : 0;
            if ($total_qty > 0 && $total_offer > 0) {
                    return array('qty'=>$total_qty,'total'=>$total_offer);
            }
        }
    }
    
    /*
     * Decline offers if product is trashed.
     */
    public function decline_offers_for_trashed_products($offer_id = null)
    {
        global $wpdb;
        $post_id = $offer_id;        
        if (isset($post_id) && !empty($post_id)) {
            $post_data = get_post($post_id);
            if(in_array($post_data->post_status, array( 'expired-offer', 'declined-offer' ))){
                $deleted_post = wp_trash_post($post_id);
            } else {
                $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;
                $table = $wpdb->prefix . "posts";
                $data_array = array(
                    'post_status' => 'declined-offer',
                    'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0)),
                    'post_modified_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1))
                );
                $where = array('ID' => $post_id);
                $wpdb->update($table, $data_array, $where);
                $post_status_text = __('Declined', 'offers-for-woocommerce');
                $offer_notes = '';
                $recipient = get_post_meta($post_id, 'offer_email', true);
                $offer_uid = get_post_meta($post_id, 'offer_uid', true);
                $offer_name = get_post_meta($post_id, 'offer_name', true);
                $offer_email = $recipient;
                $product_id = get_post_meta($post_id, 'offer_product_id', true);
                $variant_id = get_post_meta($post_id, 'offer_variation_id', true);

                $product = ( $variant_id ) ? wc_get_product($variant_id) : wc_get_product($product_id);
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
                $email_class = 'WC_Declined_Offer_Email';
                $new_email = WC()->mailer()->emails[$email_class];
                $new_email->recipient = $recipient;
                $new_email->plugin_slug = 'offers-for-woocommerce';
                $new_email->template_html = 'woocommerce-offer-declined.php';
                $new_email->template_html_path = untrailingslashit(OFW_PLUGIN_URL) . '/admin/includes/emails/';
                $new_email->template_plain = 'woocommerce-offer-declined.php';
                $new_email->template_plain_path = untrailingslashit(OFW_PLUGIN_URL) . '/admin/includes/emails/plain/';
                $new_email->trigger($offer_args);
                $deleted_post = wp_trash_post($post_id);
            }
        }
    }
    
    /*
     * Decline and delete offers if product is trashed.
     */
    public function ofw_before_product_trash_action($product_id) {
        global $wpdb;
        $_product = wc_get_product( $product_id );
        if($_product == false){
            return;
        }
        $args = array(
            'post_type' => 'woocommerce_offer',
            'post_status' => array( 'publish','accepted-offer','countered-offer','buyercountered-offer','on-hold-offer', 'expired-offer', 'declined-offer' ),
            'posts_per_page' => -1,
            'meta_key' => 'offer_product_id',
            'meta_value' => $product_id,
            'meta_compare' => '==',
            'fields' => 'ids',
        );
        $query = new WP_Query( $args );
        if ($query->have_posts()):
            foreach( $query->posts as $offer_id ):
                $this->decline_offers_for_trashed_products($offer_id);
            endforeach;
        endif;
    }
    
    /*
     * Restore offers if product is untrashed.
     */
    public function restore_offers_for_untrashed_products($offer_id = null)
    {
        global $wpdb;
        $post_id = $offer_id;        
        if (isset($post_id) && !empty($post_id)) {
            
            if ( !$post = get_post($post_id, ARRAY_A) )
		return $post;

            if ( $post['post_status'] != 'trash' )
                    return false;

            do_action( 'untrash_post', $post_id );

            $post_status = get_post_meta($post_id, '_wp_trash_meta_status', true);

            $post['post_status'] = $post_status;

            delete_post_meta($post_id, '_wp_trash_meta_status');
            delete_post_meta($post_id, '_wp_trash_meta_time');

            wp_insert_post( wp_slash( $post ) );

            wp_untrash_post_comments($post_id);

            do_action( 'untrashed_post', $post_id );

            return $post;
        }
    }
    
    public function ofw_before_product_untrash_action($product_id) {
        global $wpdb;
        $_product = wc_get_product( $product_id );
        if($_product == false){
            return;
        }
        $args = array(
            'post_type' => 'woocommerce_offer',
            'post_status' => array( 'trash' ),
            'posts_per_page' => -1,
            'meta_key' => 'offer_product_id',
            'meta_value' => $product_id,
            'meta_compare' => '==',
            'fields' => 'ids',
        );
        $query = new WP_Query( $args );
        if ($query->have_posts()):
            foreach( $query->posts as $offer_id ):
                $this->restore_offers_for_untrashed_products($offer_id);
            endforeach;
        endif;
    }
}