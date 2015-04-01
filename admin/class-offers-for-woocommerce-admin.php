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
		 * @NOTE: Uncomment following lines if the admin class should only be available for super admins
		 */
		//if( ! is_super_admin() ) {
			//return;
		//} 
		/**
		 * Call $plugin_slug from public plugin class
		 * @since	0.1.0
		 */
		$plugin = Angelleye_Offers_For_Woocommerce::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'ofwc_add_plugin_action_links' ) );
		
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
		add_filter('manage_woocommerce_offer_posts_columns' , array( $this, 'set_woocommerce_offer_columns' ) );

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
		add_action('admin_menu', array( $this, 'my_remove_submenus' ) );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'init', array( $this, 'comments_exclude_lazy_hook' ), 0 );
		
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
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box_offer_addnote' ), 10, 2 );
		
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
		add_action('woocommerce_product_write_panels', array( $this, 'custom_tab_options_offers' ));
		
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

        /*
         * Action - Ajax 'decline offer' from manage list
         * @since	0.1.0
         */
        add_action( 'wp_ajax_declineOfferFromGrid', array( $this, 'declineOfferFromGridCallback') );

        /*
         * Action - Ajax 'add offer note' from manage offer details
         * @since	0.1.0
         */
        add_action( 'wp_ajax_addOfferNote', array( $this, 'addOfferNoteCallback') );

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
         * @since   0.1.0
         */
        add_action( 'admin_init', array( $this, 'ae_ofwc_check_woocommerce_nag_notice_ignore' ) );
        add_action('admin_init', array( $this, 'ae_ofwc_check_woocommerce_available' ) );

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
		register_post_type( 'woocommerce_offer',
			array(
				'labels' => array(
					'name' => 'Manage Offers',
					'singular_name' => 'WooCommerce Offer',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New WooCommerce Offer',
					'edit' => 'Manage',
					'edit_item' => 'Manage WooCommerce Offer',
					'new_item' => 'New WooCommerce Offer',
					'view' => 'View',
					'view_item' => 'View WooCommerce Offer',
					'search_items' => 'Search WooCommerce Offers',
					'not_found' => 'No WooCommerce Offers found',
					'not_found_in_trash' => 'No WooCommerce Offers found in Trash',
					'parent' => 'Parent WooCommerce Offer'
				),
				'description' => 'Offers for WooCommerce - Custom Post Type', 
				'public' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => true,            
				'hierarchical' => false,
				'show_in_menu' => 'woocommerce',
				'menu_position' => '',
				'show_in_admin_bar' => false,
				'supports' => array( 'section_id_offer_comments', 'section_id_offer_summary', 'section_id_offer_addnote' ),
				//'capability_type' => 'post',
				//'capabilities' => array( 'create_posts' => false,),	// Removes support for the "Add New" function
				'taxonomies' => array(''),
				//'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),	// No longer used; instead we use CSS for icon
				'menu_icon' => '',
				'has_archive' => false,
			)
		);
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
		foreach($submenu['woocommerce'] as $key => $value)
		{
			if ( $submenu['woocommerce'][$key][2] == 'edit.php?post_type=woocommerce_offer' ) {
				$submenu['woocommerce'][$key][0] = 'Offers';
				$submenu['woocommerce'][$key][0] .= " <span id='woocommerce-offers-count' class='awaiting-mod update-plugins count-$pend_count'><span class='pending-count'>" . $pend_count . '</span></span>';
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
		foreach($submenu['woocommerce'] as $key => $value)
		{
			if ( $submenu['woocommerce'][$key][2] == 'edit.php?post_type=woocommerce_offer' ) {
				// Add child submenu html
				$submenu['woocommerce'][$key][0] .= "<script type='text/javascript'>
				jQuery(window).load(function($){
					jQuery('#woocommerce-offers-count').parent('a').after('<ul id=\'woocommerce-offer-admin-submenu\' class=\'\'><li class=\'woocommerce-offer-admin-submenu-item\'><a href=\'".$offers_manage_link_href."\'>&nbsp;&#8211;&nbsp;Manage Offers</a></li><li class=\'woocommerce-offer-admin-submenu-item\'><a id=\'woocommerce-offers-settings-link\' class=\'woocommerce-offer-submenu-link\' href=\'".$offers_settings_link_href."\'>&nbsp;&#8211;&nbsp;Offers Settings</a></li></ul>');
				});</script>";					
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
	}
	
	/**
	 * Output WooCommerce Tab on product single
	 * @since	0.1.0
	 */
	function custom_tab_options_tab_offers() {
        global $post;

        $_pf = new WC_Product_Factory();
        $_product = $_pf->get_product( $post->ID );
        $class_hidden = ( isset( $_product->product_type ) && $_product->product_type == 'external' ) ? ' custom_tab_offers_for_woocommerce_hidden' : '';
        print(
            '<li id="custom_tab_offers_for_woocommerce" class="custom_tab_offers_for_woocommerce '. $class_hidden . '"><a href="#custom_tab_data_offers_for_woocommerce">' . __('Offers', 'angelleye_offers_for_woocommerce') . '</a></li>'
        );
	}
	
	/**
	 * Callback Action - Add custom tab options in WooCommerce product tabs
	 * Provides the input fields and add/remove buttons for custom tabs on the single product page.
	 * @since	0.0.1
	 */
	function custom_tab_options_offers() 
	{
		global $post;
        $post_meta_offers_enabled = get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true);

        $field_value = 'yes';
        $field_callback = ($post_meta_offers_enabled) ? $post_meta_offers_enabled : 'no';

		?>
		<div id="custom_tab_data_offers_for_woocommerce" class="panel woocommerce_options_panel">
			<div class="options_group">
				<p class="form-field">                    
					<?php woocommerce_wp_checkbox( array('value' => $field_value, 'cbvalue' => $field_callback, 'id' => 'offers_for_woocommerce_enabled', 'label' => __('Enable Offers?', 'angelleye_offers_for_woocommerce'), 'description' => __('Enable this option to enable the \'Make Offer\' buttons and form display in the shop.', 'angelleye_offers_for_woocommerce') ) ); ?>
				</p>                    
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
			1  => __( 'Offer updated.', 'angelleye_offers_for_woocommerce' ),
			2  => __( 'Offer Details updated.', 'angelleye_offers_for_woocommerce' ),
			3  => __( 'Offer Details deleted.', 'angelleye_offers_for_woocommerce' ),
			4  => __( 'Offer updated.', 'angelleye_offers_for_woocommerce' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Offer restored to revision from %s', 'angelleye_offers_for_woocommerce' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Offer set as Pending Status.', 'angelleye_offers_for_woocommerce' ),
			7  => __( 'Offer saved.', 'angelleye_offers_for_woocommerce' ),
			8  => __( 'Offer submitted.', 'angelleye_offers_for_woocommerce' ),
			9  => sprintf(
				__( 'Offer scheduled for: <strong>%1$s</strong>.', 'angelleye_offers_for_woocommerce' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'angelleye_offers_for_woocommerce' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Offer draft updated.', 'angelleye_offers_for_woocommerce' ),
            11 => __( 'Offer note added.', 'angelleye_offers_for_woocommerce' )
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
			/*'postcustom', */
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
	 * Filter - Add custom CSS in the admin head
	 * @since	0.1.0	 
	 */
	public function hide_that_stuff() 
	{		
		// NOT BEING USED
	}
	
	/**
	 * Filter - Modify the comments clause - to exclude "woocommerce_offer" post type
	 * @since	0.1.0
	 * @param  array  $clauses
	 * @param  object $wp_comment_query
	 * @return array
	 */
	public function angelleye_ofwc_exclude_cpt_from_comments_clauses( $clauses )
	{
		global $wpdb;

		$clauses['join'] = "JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID";
		
		$clauses['where'] .=
        $wpdb->prepare(" AND $wpdb->posts.post_type <> '%s'", 'woocommerce_offer');

		return $clauses;
	}
	
	/**
	 * Filter - Modify the comments clause - to exclude "woocommerce_offer" post type
	 * @since	0.1.0
	 */
	public function comments_exclude_lazy_hook( $screen )
	{
		//if ( $screen->id == 'edit-comments' )
		add_filter( 'comments_clauses', array( $this, 'angelleye_ofwc_exclude_cpt_from_comments_clauses' ) );
	}

	/**
	 * Set custom columns on CPT edit list view
	 * @since	0.1.0
	 */
	public function set_woocommerce_offer_columns($columns) 
	{
        $columns['offer_name'] = __( 'Name', 'angelleye_offers_for_woocommerce' );
		$columns['offer_amount'] = __( 'Amount', 'angelleye_offers_for_woocommerce' );
		$columns['offer_price_per'] = __( 'Price Per', 'angelleye_offers_for_woocommerce' );
		$columns['offer_quantity'] = __( 'Quantity', 'angelleye_offers_for_woocommerce' );			
		$columns['offer_actions'] = __( 'Action', 'angelleye_offers_for_woocommerce' );    
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
                echo number_format($val, 0);
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
				echo get_woocommerce_currency_symbol().number_format($val, 2);
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
                echo get_woocommerce_currency_symbol().number_format($val, 2);
            break;
			
			case 'offer_actions' :
				$view_detail_link = get_edit_post_link( $post_id );
				include('includes/list-actions-html.php'); 
			break;
		}
	}	
	
	/**
	 * Filter the custom columns for CPT edit list view to be sortable
	 * @since	0.1.0
	 */
	public function woocommerce_offer_sortable_columns( $columns ) 
	{
        $columns['offer_name'] = 'offer_name';
        $columns['offer_email'] = 'offer_email';
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
        global $wpdb;

        if ( is_search() ) {
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

        if ( is_search() ) {
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

        if ( is_search() ) {
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
		if( $post->post_type == 'woocommerce_offer' ) 
		{			
			unset($actions['inline hide-if-no-js']);
			unset($actions['edit']);
			unset($actions['view']);
			//unset($actions['trash']);

            if($post->post_status == 'accepted-offer')
            {
                $actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage&nbsp;Offer') . '</a>';
                $actions['decline-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" title="Set Offer Status to Declined" id="woocommerce-offer-post-action-link-decline-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Decline') . '</a>';
            }
            if($post->post_status == 'countered-offer')
            {
                $actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage&nbsp;Offer') . '</a>';
                $actions['decline-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" title="Set Offer Status to Declined" id="woocommerce-offer-post-action-link-decline-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Decline') . '</a>';
            }
            elseif($post->post_status == 'declined-offer')
            {
                $actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage&nbsp;Offer') . '</a>';
            }
            elseif($post->post_status == 'on-hold-offer')
            {
                $actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage&nbsp;Offer') . '</a>';
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
				$actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" title="Offer Details" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Make&nbsp;Counter&nbsp;Offer') . '</a>';				
				$actions['accept-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-accept" title="Set Offer Status to Accepted" id="woocommerce-offer-post-action-link-accept-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Accept') . '</a>';
				$actions['decline-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" title="Set Offer Status to Declined" id="woocommerce-offer-post-action-link-decline-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Decline') . '</a>';
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
            'label'                     => _x( 'accepted-offer', 'Accepted Offer', 'angelleye_offers_for_woocommerce' ),
            'label_count'               => _n_noop( 'Accepted (%s)',  'Accepted (%s)', 'angelleye_offers_for_woocommerce' ),
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
            'label'                     => _x( 'countered-offer', 'Countered Offer', 'angelleye_offers_for_woocommerce' ),
            'label_count'               => _n_noop( 'Countered (%s)',  'Countered (%s)', 'angelleye_offers_for_woocommerce' ),
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
            'label'                     => _x( 'on-hold-offer', 'On Hold', 'angelleye_offers_for_woocommerce' ),
            'label_count'               => _n_noop( 'On Hold (%s)',  'On Hold (%s)', 'angelleye_offers_for_woocommerce' ),
            'public'                    => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'on-hold-offer', $args );
    }

    /**
     * Register custom post status type -- Buyer Countered Offer
     * @since	0.1.0
     */
    public function my_custom_post_status_buyer_countered()
    {
        $args = array(
            'label'                     => _x( 'buyercountered-offer', 'Buyer Countered Offer', 'angelleye_offers_for_woocommerce' ),
            'label_count'               => _n_noop( 'Buyer Countered (%s)',  'Buyer Countered (%s)', 'angelleye_offers_for_woocommerce' ),
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
			'label'                     => _x( 'declined-offer', 'Declined Offer', 'angelleye_offers_for_woocommerce' ),
			'label_count'               => _n_noop( 'Declined (%s)',  'Declined (%s)', 'angelleye_offers_for_woocommerce' ), 
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
			'label'                     => _x( 'completed-offer', 'Completed Offer', 'angelleye_offers_for_woocommerce' ),
			'label_count'               => _n_noop( 'Completed (%s)',  'Completed (%s)', 'angelleye_offers_for_woocommerce' ), 
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
		$arg = get_query_var( 'post_status' );
		$screen = get_current_screen();

		if ( $screen->post_type == 'woocommerce_offer' )
		{
            if($post->post_status == 'accepted-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon accepted" title="Offer Status: Accepted">Accepted</i></div>');
            }
            elseif($post->post_status == 'countered-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon countered" title="Offer Status: Countered">Countered</i></div>');
            }
            elseif($post->post_status == 'buyercountered-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon buyercountered" title="Offer Status: Buyer Countered">Buyer Countered</i></div>');
            }
			elseif($post->post_status == 'publish'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon pending" title="Offer Status: Pending">Pending</i></div>');
			}
			elseif($post->post_status == 'trash'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon trash" title="Offer Status: Trashed">Trashed</i></div>');
			}
			elseif($post->post_status == 'completed-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon completed" title="Offer Status: Completed">Completed</i></div>');
			}
            elseif($post->post_status == 'declined-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon declined" title="Offer Status: Declined">Declined</i></div>');
            }
            elseif($post->post_status == 'on-hold-offer'){
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon on-hold" title="Offer Status: On Hold">On Hold</i></div>');
            }
			else
			{
                $states = array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon" title="Offer Status: '.ucwords($post->post_status).'">'.ucwords($post->post_status).'</i></div>');
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
                __( 'Offer Activity Log', 'angelleye_offers_for_woocommerce' ),
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
                __( 'Offer Details', 'angelleye_offers_for_woocommerce' ),
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
        global $post;

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

            $_pf = new WC_Product_Factory();
            $_product = $_pf->get_product($product_id);

            if( $product_variant_id )
            {
                $_pf_variant = new WC_Product_Factory();
                $_product_variant = $_pf_variant->get_product($product_variant_id);
                $_product_variant_managing_stock = ( $_product_variant->managing_stock() == 'parent' ) ? true : false;

                $_product_sku = ( $_product_variant->get_sku() ) ? $_product_variant->get_sku() : $_product->get_sku();
                $_product_permalink = $_product_variant->get_permalink();
                $_product_attributes = $_product_variant->get_variation_attributes();
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
                $_product_attributes = $_product->get_attributes();
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
             * Check to 'consider inventory' of product stock compared to offer quantities
             * @since   0.1.0
             */

            if( ( !$_product_in_stock)  )
            {
                $offer_inventory_msg = '<strong>Notice: </strong>' . __('Product stock is lower than offer quantity!', 'angelleye_offers_for_woocommerce');
            }

            // Check for 'offer_order_id'
            if( isset( $postmeta['offer_order_id'][0] ) && is_numeric( $postmeta['offer_order_id'][0] ) )
            {
                $order_id = $postmeta['offer_order_id'][0];

                // Set order meta data array
                $offer_order_meta = array();
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
                    $offer_order_meta['Order ID'].= '<br /><small><strong>Notice: </strong>' . __('Order not found; may have been deleted', 'angelleye_offers_for_woocommerce') . '</small>';
                }
            }

            // set author_data
            $author_data = get_userdata($post->post_author);

            // set author offer counts
            $author_counts = array();
            if($author_data)
            {
                // count offers by author id
                global $wpdb;

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

                $author_counts['all'] = apply_filters( 'get_usernumposts', $count_all, $post->post_author );
                $author_counts['pending'] = apply_filters( 'get_usernumposts', $count_pending, $post->post_author );
                $author_counts['accepted'] = apply_filters( 'get_usernumposts', $count_accepted, $post->post_author );
                $author_counts['countered'] = apply_filters( 'get_usernumposts', $count_countered, $post->post_author );
                $author_counts['buyercountered'] = apply_filters( 'get_usernumposts', $count_buyer_countered, $post->post_author );
                $author_counts['declined'] = apply_filters( 'get_usernumposts', $count_declined, $post->post_author );
                $author_counts['completed'] = apply_filters( 'get_usernumposts', $count_completed, $post->post_author );

                $author_data->offer_counts = $author_counts;
            }

            /*
             * Output html for Offer Comments loop
             */
            include_once('views/meta-panel-summary.php');
        }
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
                __( 'Add Offer Note', 'angelleye_offers_for_woocommerce' ),
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

        // Check the user's permissions
        if(isset($_POST['post_type']) && 'woocommerce_offer' == $_POST['post_type'])
        {
            if (!current_user_can('edit_page', $post_id) || !current_user_can( 'manage_woocommerce'))
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
            $_pf = new WC_Product_Factory;
            $product = ( $variant_id ) ? $_pf->get_product( $variant_id ) : $_pf->get_product( $product_id );

            // if buyercountered-offer previous then use buyer counter values
            $is_offer_buyer_countered_status = ( $_POST['post_previous_status'] == 'buyercountered-offer' ) ? true : false;

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_total = ($product_qty * $product_price_per);

            // if buyercountered-offer status, update postmeta values for quantity,price,amount
            if( $is_offer_buyer_countered_status )
            {
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

            // the email we want to send
            $email_class = 'WC_Accepted_Offer_Email';

            // load the WooCommerce Emails
            $wc_emails = new WC_Emails();
            $emails = $wc_emails->get_emails();

            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

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
            $_pf = new WC_Product_Factory;
            $product = ( $variant_id ) ? $_pf->get_product( $variant_id ) : $_pf->get_product( $product_id );

            // if buyercountered-offer previous then use buyer counter values
            $is_offer_buyer_countered_status = ( $_POST['post_previous_status'] == 'buyercountered-offer' ) ? true : false;

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_total = ($product_qty * $product_price_per);

            // if buyercountered-offer status, update postmeta values for quantity,price,amount
            if( $is_offer_buyer_countered_status )
            {
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

            // the email we want to send
            $email_class = 'WC_Offer_On_Hold_Email';

            // load the WooCommerce Emails
            $wc_emails = new WC_Emails();
            $emails = $wc_emails->get_emails();

            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

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
            $offer_total = number_format(round($offer_quantity * $offer_price_per), 2, ".", "");

            /**
             * Update Counter Offer post meta values
             */
            update_post_meta( $post_id, 'offer_quantity', $offer_quantity );
            update_post_meta( $post_id, 'offer_price_per', $offer_price_per );
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
            $_pf = new WC_Product_Factory;
            $product = ( $variant_id ) ? $_pf->get_product( $variant_id ) : $_pf->get_product( $product_id );

            $product_qty = get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = get_post_meta($post_id, 'offer_price_per', true);
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
                $offer_args['product_title_formatted'] = sprintf( __( '%s &ndash; %s%s', 'woocommerce' ), $identifier, $product->get_title(), $extra_data );
            }
            else
            {
                $offer_args['product_title_formatted'] = $product->get_formatted_name();
            }

            // the email we want to send
            $email_class = 'WC_Countered_Offer_Email';

            // load the WooCommerce Emails
            $wc_emails = new WC_Emails();
            $emails = $wc_emails->get_emails();

            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

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

            $product_id = get_post_meta($post_id, 'offer_product_id', true);
            $variant_id = get_post_meta($post_id, 'offer_variation_id', true);
            $_pf = new WC_Product_Factory;
            $product = ( $variant_id ) ? $_pf->get_product( $variant_id ) : $_pf->get_product( $product_id );

            // if buyercountered-offer previous then use buyer counter values
            $is_offer_buyer_countered_status = ( $_POST['post_previous_status'] == 'buyercountered-offer' ) ? true : false;

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_total = ($product_qty * $product_price_per);

            // if buyercountered-offer status, update postmeta values for quantity,price,amount
            if( $is_offer_buyer_countered_status )
            {
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

            // the email we want to send
            $email_class = 'WC_Declined_Offer_Email';

            // load the WooCommerce Emails
            $wc_emails = new WC_Emails();
            $emails = $wc_emails->get_emails();

            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

            // define email template/path (html)
            $new_email->template_html  = 'woocommerce-offer-declined.php';
            $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

            // define email template/path (plain)
            $new_email->template_plain  = 'woocommerce-offer-declined.php';
            $new_email->template_plain_path = plugin_dir_path(__FILE__). 'includes/emails/plain/';

            $new_email->trigger($offer_args);
        }

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
		if(false == get_option('offers_for_woocommerce_options_general'))	// If the plugin options don't exist, create them.
		{
			add_option('offers_for_woocommerce_options_general');
		}

		/**
		 * Add option - 'Display Settings'
		 */
		if(false == get_option('offers_for_woocommerce_options_display'))	// If the plugin options don't exist, create them.
		{
			add_option('offers_for_woocommerce_options_display');
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
			'Enable Make Offer button on home page', // Title 
			array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback TEXT input
			'offers_for_woocommerce_general_settings', // Page
			'general_settings', // Section 
			array('option_name'=>'offers_for_woocommerce_options_general', 'input_label'=>'general_setting_enable_make_offer_btn_frontpage', 'input_required'=>FALSE)         
		);
		
		/**
		 * Add field - 'General Settings' - 'general_setting_enable_make_offer_btn_catalog'
		 * Enable Make Offer button on shop page
		 */
		add_settings_field(
			'general_setting_enable_make_offer_btn_catalog', // ID
			'Enable Make Offer button on shop page', // Title 
			array( $this, 'offers_for_woocommerce_options_page_output_input_checkbox' ), // Callback TEXT input
			'offers_for_woocommerce_general_settings', // Page
			'general_settings', // Section 
			array('option_name'=>'offers_for_woocommerce_options_general', 'input_label'=>'general_setting_enable_make_offer_btn_catalog', 'input_required'=>FALSE)         
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
            'Make Offer form display type', // Title
            array( $this, 'offers_for_woocommerce_options_page_output_input_select' ), // Callback SELECT input
            'offers_for_woocommerce_display_settings', // Page
            'display_settings', // Section
            array('option_name'=>'offers_for_woocommerce_options_display', 'input_label'=>'display_setting_make_offer_form_display_type', 'input_required'=>FALSE,
                'options'=> array(
                    array('option_label' => 'Product Tabs (default display)', 'option_value' => 'tabs'),
                    array('option_label' => 'Lightbox', 'option_value' => 'lightbox')
                ))
        );
		
		/**
		 * Add field - 'Display Settings' - 'display_setting_custom_make_offer_btn_text'
		 * Make Offer Button Text
		 */
		add_settings_field(
			'display_setting_custom_make_offer_btn_text', // ID
			'Make Offer button text', // Title 
			array( $this, 'offers_for_woocommerce_options_page_output_input_text' ), // Callback TEXT input
			'offers_for_woocommerce_display_settings', // Page
			'display_settings', // Section
			array('option_name'=>'offers_for_woocommerce_options_display', 'input_label'=>'display_setting_custom_make_offer_btn_text', 'input_required'=>FALSE)
		);
		
		/**
		 * Add field - 'Display Settings' - 'display_setting_custom_make_offer_btn_text_color'
		 * Make Offer Button Text Color
		 */
		add_settings_field(
			'display_setting_custom_make_offer_btn_text_color', // ID
			'Make Offer button text color', // Title 
			array( $this, 'offers_for_woocommerce_options_page_output_input_colorpicker' ), // Callback TEXT input
			'offers_for_woocommerce_display_settings', // Page
			'display_settings', // Section
			array('option_name'=>'offers_for_woocommerce_options_display', 'input_label'=>'display_setting_custom_make_offer_btn_text_color', 'input_required'=>FALSE)
		);
		
		/**
		 * Add field - 'Display Settings' - 'display_setting_custom_make_offer_btn_color'
		 * Make Offer Button Text Color
		 */
		add_settings_field(
			'display_setting_custom_make_offer_btn_color', // ID
			'Make Offer button color', // Title 
			array( $this, 'offers_for_woocommerce_options_page_output_input_colorpicker' ), // Callback TEXT input
			'offers_for_woocommerce_display_settings', // Page
			'display_settings', // Section
			array('option_name'=>'offers_for_woocommerce_options_display', 'input_label'=>'display_setting_custom_make_offer_btn_color', 'input_required'=>FALSE)
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
		print('<p>Complete the form below and click Save Changes button to update your settings.</p>');
	}
	
	/**
	 * Callback - Options Page - Output a 'text' input field for options page form
	 * @since	0.1.0
	 * @param	$args - Params to define 'option_name','input_label'
	 */
	public function offers_for_woocommerce_options_page_output_input_text($args) 
	{
		$options = get_option($args['option_name']);
		$field_label = $args['input_label'];
		$field_required = ($args['input_required']) ? ' required="required" ' : '';
		printf(
            '<input ' .$field_required. ' type="text" id="'.$field_label.'" name="'.$args['option_name'].'['.$field_label.']" value="%s" />',
            isset( $options[$field_label] ) ? esc_attr( $options[$field_label]) : ''
        );
	}

    /**
     * Callback - Options Page - Output a 'Checkbox' input field for options page form
     * @since	0.1.0
     * @param	$args - Params to define 'option_name','input_label'
     */
    public function offers_for_woocommerce_options_page_output_input_checkbox($args)
    {
        $options = get_option($args['option_name']);
        $field_label = $args['input_label'];
        $field_required = ($args['input_required'] === true) ? ' required="required" ' : '';
        $is_checked = (isset($options[$field_label])) ? $options[$field_label] : '0';
        print(
            '<input '. $field_required. ' type="checkbox" id="'.$field_label.'" name="'.$args['option_name'].'['.$field_label.']" value="1" ' . checked(1, $is_checked, false) . '/>'
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
        $field_label = $args['input_label'];
        $field_required = ($args['input_required'] === true) ? ' required="required" ' : '';

        print(
            '<select '. $field_required. ' id="'.$field_label.'" name="'.$args['option_name'].'['.$field_label.']"/>'
        );
        foreach( $args['options'] as $option )
        {
            $is_selected = (isset($options[$field_label]) && $options[$field_label] == $option['option_value']) ? 'selected="selected"' : '';
            print(
                '<option value="'. $option['option_value'] . '" '. $is_selected .'>'. $option['option_label'] . '</option>'
            );
        }

        print(
            '</select>'
        );

    }
	
	/**
	 * Callback - Options Page - Output a 'colorpicker' input field for options page form
	 * @since	0.1.0
	 * @param	$args - Params to define 'option_name','input_label'
	 */
	public function offers_for_woocommerce_options_page_output_input_colorpicker($args) 
	{
		$options = get_option($args['option_name']);
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
		if( (! is_super_admin()) && (! current_user_can( 'manage_woocommerce')) ) {
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
            wp_enqueue_style( $this->plugin_slug .'-angelleye-offers-admin-styles-boostrap-custom', plugins_url( 'assets/css/bootstrap-custom.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );

            // jQuery styles
            wp_enqueue_style( $this->plugin_slug .'-angelleye-offers-admin-styles-jquery-ui', plugins_url( 'assets/css/jquery-ui.min.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );
            wp_enqueue_style( $this->plugin_slug .'-angelleye-offers-admin-styles-jquery-ui-structure', plugins_url( 'assets/css/jquery-ui.structure.min.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );
            wp_enqueue_style( $this->plugin_slug .'-angelleye-offers-admin-styles-jquery-ui-theme', plugins_url( 'assets/css/jquery-ui.theme.min.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );

            // admin styles
            wp_enqueue_style( $this->plugin_slug .'-angelleye-offers-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );
        }

        if ( "product" == $screen->id && is_admin() )
        {
            // admin styles - edit product
            wp_enqueue_style( $this->plugin_slug .'-angelleye-offers-edit-product-styles', plugins_url( 'assets/css/edit-product.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );
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

			// admin scripts
			wp_enqueue_script( $this->plugin_slug . '-angelleye-offers-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

			// Admin footer scripts
			wp_enqueue_script( $this->plugin_slug . '-angelleye-offers-admin-footer-scripts', plugins_url( 'assets/js/admin-footer-scripts.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );			
		}
        if ( "edit-woocommerce_offer" == $screen->id && is_admin() )
        {
            // Admin actions
            wp_enqueue_script( $this->plugin_slug . '-angelleye-offers-admin-actions', plugins_url( 'assets/js/admin-actions.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // Bootstrap modal.js
            wp_enqueue_script( $this->plugin_slug . '-angelleye-offers-bootstrap-modal', plugins_url( 'assets/js/bootstrap-modal.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // jQuery.confirm.js
            wp_enqueue_script( $this->plugin_slug . '-angelleye-offers-jquery-confirm-min', plugins_url( 'assets/js/jquery.confirm.min.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );
        }
        if ( "woocommerce_offer" == $screen->id && is_admin() )
        {
            // Jquery datepicker.js
            wp_enqueue_script( $this->plugin_slug . '-angelleye-offers-jquery-datepicker', plugins_url( 'assets/js/jquery-ui.min.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // autoNumeric js
            wp_enqueue_script( $this->plugin_slug . '-angelleye-offers-jquery-auto-numeric-1-9-24', plugins_url( '../public/assets/js/autoNumeric-1-9-24.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );

            // admin scripts
            wp_enqueue_script( $this->plugin_slug . '-angelleye-offers-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );
        }

        if ( "product" == $screen->id && is_admin() )
        {
            // admin scripts - edit product
            wp_enqueue_script( $this->plugin_slug . '-angelleye-offers-admin-script-edit-product', plugins_url( 'assets/js/edit-product.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );
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
			__( 'Offers for WooCommerce - Settings', $this->plugin_slug ),
			__( 'Offers for WooCommerce', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page'));			
	}

	/**
	 * Callback - Render the settings page for this plugin.
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() 
	{
		include_once( 'views/admin.php' );
	}
	
	/**
	 * Add Plugin Page Action links
	 * @since    0.1.0
	 */
	public function ofwc_add_plugin_action_links( $links ) 
	{
		return array_merge(
			array(
				'configure' => sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=offers-for-woocommerce' ), __( 'Configure', 'offers-for-woocommerce' ) ),
				'docs'      => sprintf( '<a href="%s" target="_blank">%s</a>', 'http://www.angelleye.com/category/docs/offers-for-woocommerce/', __( 'Docs', 'offers-for-woocommerce' ) ),
				'support'   => sprintf( '<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/plugin/offers-for-woocommerce/', __( 'Support', 'offers-for-woocommerce' ) ),
				'review'    => sprintf( '<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/view/plugin-reviews/offers-for-woocommerce', __( 'Write a Review', 'offers-for-woocommerce' ) ),
			),
			$links
		);
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
        if(is_admin() && (defined('DOING_AJAX') || DOING_AJAX))
        {
            global $wpdb; // this is how you get access to the database
            $post_id = $_POST["targetID"];

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
            $post_status_text = 'Accepted';

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
            $_pf = new WC_Product_Factory;
            $product = ( $variant_id ) ? $_pf->get_product( $variant_id ) : $_pf->get_product( $product_id );

            // if buyercountered-offer previous then use buyer counter values
            $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_total = ($product_qty * $product_price_per);

            // if buyercountered-offer status, update postmeta values for quantity,price,amount
            if( $is_offer_buyer_countered_status )
            {
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

            // the email we want to send
            $email_class = 'WC_Accepted_Offer_Email';

            // load the WooCommerce Emails
            $wc_emails = new WC_Emails();
            $emails = $wc_emails->get_emails();

            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

            // define email template/path (html)
            $new_email->template_html  = 'woocommerce-offer-accepted.php';
            $new_email->template_html_path = plugin_dir_path(__FILE__). 'includes/emails/';

            // define email template/path (plain)
            $new_email->template_plain  = 'woocommerce-offer-accepted.php';
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


            die(); // this is required to return a proper result
        }
    }

    /*
     * Action - Ajax 'decline offer' from manage list
     * @since	0.1.0
     */
    public function declineOfferFromGridCallback()
    {
        if(is_admin() && (defined('DOING_AJAX') || DOING_AJAX))
        {
            global $wpdb; // this is how you get access to the database
            $post_id = $_POST["targetID"];

            // Get current data for Offer prior to save
            $post_data = get_post($post_id);

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
            $post_status_text = 'Declined';

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
            $_pf = new WC_Product_Factory;
            $product = ( $variant_id ) ? $_pf->get_product( $variant_id ) : $_pf->get_product( $product_id );

            $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
            $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
            $product_total = ($product_qty * $product_price_per);

            // if buyercountered-offer status, update postmeta values for quantity,price,amount
            if( $is_offer_buyer_countered_status )
            {
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

            // the email we want to send
            $email_class = 'WC_Declined_Offer_Email';

            // load the WooCommerce Emails
            $wc_emails = new WC_Emails();
            $emails = $wc_emails->get_emails();

            // select the email we want & trigger it to send
            $new_email = $emails[$email_class];
            $new_email->recipient = $recipient;

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


            die(); // this is required to return a proper result
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
            $comment_text = "<span>Offer Note:</span>";
            if($noteSendToBuyer != '1')
            {
                $comment_text.= " (admin only)";
            }
            else
            {
                $comment_text.= " (sent to buyer)";
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
                    $_pf = new WC_Product_Factory;
                    $product = ( $variant_id ) ? $_pf->get_product( $variant_id ) : $_pf->get_product( $product_id );

                    // if buyercountered-offer previous then use buyer counter values
                    $is_offer_buyer_countered_status = ( $post_data->post_status == 'buyercountered-offer' ) ? true : false;

                    $product_qty = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_quantity', true) : get_post_meta($post_id, 'offer_quantity', true);
                    $product_price_per = ( $is_offer_buyer_countered_status ) ? get_post_meta($post_id, 'offer_buyer_counter_price_per', true) : get_post_meta($post_id, 'offer_price_per', true);
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

                    // the email we want to send
                    $email_class = 'WC_Offer_Note_Email';

                    // load the WooCommerce Emails
                    $wc_emails = new WC_Emails();
                    $emails = $wc_emails->get_emails();

                    // select the email we want & trigger it to send
                    $new_email = $emails[$email_class];
                    $new_email->recipient = $recipient;

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
    public function aeofwc_admin_notices(){

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
                echo '<p>'. __('Currently filtered by user', 'angelleye_offers_for_woocommerce'). ' <strong>"' . $author_data->user_login . '"</strong> <a href="edit.php?post_type=woocommerce_offer">Click here to reset filter</a></p>';
                echo '</div>';
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
            'title'   => __( 'Overview', 'angelleye_offers_for_woocommerce' ),
            'content' => '<p>' . __( 'This plugin is currently in development. Please send any feedback or bug reports to andrew@angelleye.com. Thank you.', 'angelleye_offers_for_woocommerce' ) . '</p>',
        ));

        $screen->add_help_tab( array(
            'id'      => 'angelleye-offers-for-woocommerce-overview-tab_02',
            'title'   => __( 'Help Tab', 'angelleye_offers_for_woocommerce' ),
            'content' => '<p>' . __( 'This plugin is currently in development. Please send any feedback or bug reports to andrew@angelleye.com. Thank you.', 'angelleye_offers_for_woocommerce' ) . '</p>',
        ));

        $screen->add_help_tab( array(
            'id'      => 'angelleye-offers-for-woocommerce-overview-tab_03',
            'title'   => __( 'Help Tab', 'angelleye_offers_for_woocommerce' ),
            'content' => '<p>' . __( 'This plugin is currently in development. Please send any feedback or bug reports to andrew@angelleye.com. Thank you.', 'angelleye_offers_for_woocommerce' ) . '</p>',
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
                    deactivate_plugins(plugin_dir_path(realpath(dirname(__FILE__))) . $this->plugin_slug . '.php');

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
            printf('<div class="updated"> <p> %s  | <a href="%2$s">Hide Notice</a></p> </div>', __('<strong>Offers for WooCommerce has been deactivated; WooCommerce is required.</strong><br />Please make sure that WooCommerce is installed and activated before activating Offers for WooCommerce.', 'angelleye_offers_for_woocommerce'), '?angelleye_offers_for_woocommerce_ignore_01=0');
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
    }

}
