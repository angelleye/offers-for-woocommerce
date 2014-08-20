<?php
/**
 * Offers for WooCommerce - admin
 *
 * @package   Angelleye_Offers_For_Woocommerce_Admin
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 * @copyright 2014 AngellEYE
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
		 
		/* Add new post type */
		add_action('init', 'angelleye_ofwc_add_post_type_woocommerce_offer');
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
					'publicly_queryable' => false,
					'exclude_from_search' => true,            
					'hierarchical' => false,
					'show_in_menu' => 'woocommerce',
					'slug' => 'edit.php?post_status=publish&post_type=woocommerce_offer',
					'menu_position' => '',
					'show_in_admin_bar' => false,
					'supports' => array( 'comments', 'custom-fields' ),
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
		 * XXX
		 * @since	0.1.0
		 */
		add_filter('manage_woocommerce_offer_posts_columns' , 'set_woocommerce_offer_columns');

		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'manage_woocommerce_offer_posts_custom_column' , 'get_woocommerce_offer_column', 2, 10 );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_filter( 'manage_edit-woocommerce_offer_sortable_columns', 'woocommerce_offer_sortable_columns' );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_filter( 'wp_count_pages', 'toddsby_exclude_pages_from_counts', 10, 3); // hook that returns 3 parameters of wp_count_post function (wp-includes/post.php)
				
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'admin_init', 'remove_woocommerce_offer_meta_boxes' );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action('admin_menu', 'my_remove_submenus');
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action('admin_head', 'hide_that_stuff', 0);
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'init', 'wpse_72210_comments_exclude_lazy_hook', 0);
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		if (is_admin()) {
			add_filter('post_row_actions','remove_quick_edit',10,2);
		}
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'init', 'crt_my_custom_post_status_accepted', 10, 2 );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'init', 'crt_my_custom_post_status_completed', 10, 2 );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'init', 'crt_my_custom_post_status_declined', 10, 2 );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action('admin_footer-post.php', 'jc_append_post_status_list');
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_filter( 'display_post_states', 'jc_display_archive_state' );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'current_screen', 'change_post_to_article', 10, 2 );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_filter( 'bulk_actions-edit-woocommerce_offer', 'my_custom_bulk_actions' );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'add_meta_boxes', 'myplugin_add_meta_box' );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action( 'save_post', 'myplugin_save_meta_box_data' );
		
		/**
		 * XXX
		 * @since	0.1.0
		 */
		add_action('admin_init', 'angelleye_ofwc_intialize_options');
		
		
		
		
		
		/**
		 * Action - Admin Menu - Add the 'pending offer' count bubble
		 * @since	0.1.0
		 */
		add_action( 'admin_menu', 'add_user_menu_bubble' );
		
		/**
		 * Callback Action - Admin Menu - Add the 'pending offer' count bubble
		 * @since	0.1.0
		 */
		function add_user_menu_bubble() 
		{
	  		global $wpdb;
			$pend_count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'woocommerce_offer' AND post_status = 'publish' " ) );
			global $submenu;
			foreach($submenu[woocommerce] as $key => $value)
			{
				if ( $submenu[woocommerce][$key][2] == 'edit.php?post_type=woocommerce_offer' ) {
					$submenu[woocommerce][$key][0] = 'Offers';
					$submenu[woocommerce][$key][0] .= " <span id='woocommerce-offers-count' class='awaiting-mod update-plugins count-$pend_count'><span class='pending-count'>" . $pend_count . '</span></span>';
				}
			}
		}
		
		/**
		 * Action - Admin Menu - Add child submenu items for the woocommerce->offers submenu
		 * @since	0.1.0
		 */
		add_action( 'admin_menu', 'add_offers_submenu_children' );
		
		/**
		 * Callback Action - Admin Menu - Add child submenu items for the woocommerce->offers submenu
		 * @since	0.1.0
		 */
		function add_offers_submenu_children() 
		{
			$offers_manage_link_href = admin_url( 'edit.php?post_type=woocommerce_offer');
			$offers_settings_link_href = admin_url( 'options-general.php?page=offers-for-woocommerce');
			global $submenu;
			foreach($submenu[woocommerce] as $key => $value)
			{
				if ( $submenu[woocommerce][$key][2] == 'edit.php?post_type=woocommerce_offer' ) {
					// Add child submenu html
					//$submenu[woocommerce][$key][0] .= " <ul id='woocommerce-offer-admin-submenu' class=''><li class=''></li><li class=''> - Child Link Test</li><li class=''> - Child Link Test 2</li></ul>";
					$submenu[woocommerce][$key][0] .= "<script type='text/javascript'>
					jQuery(window).load(function($){
						//jQuery('#woocommerce-offer-admin-submenu').parent('li').addClass('wp-has-submenu').addClass('wp-not-current-submenu').addClass('menu-top');
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
		 */
		function process_product_meta_custom_tab( $post_id ) {
			update_post_meta( $post_id, 'offers_for_woocommerce_enabled', ( isset($_POST['offers_for_woocommerce_enabled']) && $_POST['offers_for_woocommerce_enabled'] ) ? 'yes' : 'no' );
		}
		add_action('woocommerce_process_product_meta', 'process_product_meta_custom_tab', 10, 2);  
  
		function custom_tab_options_tab_offers() {
			?>
					<li class="custom_tab_offers_for_woocommerce"><a href="#custom_tab_data_offers_for_woocommerce"><?php _e('Offers', 'angelleye_offers_for_woocommerce'); ?></a></li>
			<?php
		}
		add_action('woocommerce_product_write_panel_tabs', 'custom_tab_options_tab_offers');
  
		/**
		 * Callback Action - Add custom tab options in WooCommerce product tabs
		 * Provides the input fields and add/remove buttons for custom tabs on the single product page.
		 * @since	0.0.1
		 */
		function custom_tab_options_offers() 
		{
			global $post;
			$custom_tab_options_offers_checked = (get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true) == 'no') ? 'no' : 'yes';			
			?>
			<div id="custom_tab_data_offers_for_woocommerce" class="panel woocommerce_options_panel">
                <div class="options_group">
                    <p class="form-field">                    
                        <?php woocommerce_wp_checkbox( array('value' => $custom_tab_options_offers_checked, 'cbvalue' => 'yes', 'id' => 'offers_for_woocommerce_enabled', 'label' => __('Enable Offers?', 'angelleye_offers_for_woocommerce'), 'description' => __('Enable this option to enable the \'Make Offer\' buttons and form display in the shop.', 'angelleye_offers_for_woocommerce') ) ); ?>
                    </p>                    
                </div>                
			</div>
			<?php
		}
		
		/*
		 * Action - Add custom tab options in WooCommerce product tabs
		 * @since	0.1.0
		 */
		add_action('woocommerce_product_write_panels', 'custom_tab_options_offers');
		
		/*
		 * Action - jQuery load for Ajax 'approve offer' from manage list
		 * @since	0.1.0
		 */
		add_action( 'admin_footer', 'load_manage_actions_js' ); 
			
	
	/*
	 * Action - jQuery load for Ajax 'approve offer' from manage list
	 * @since	0.1.0
	 */
	function load_manage_actions_js() {
	?>
    <script type="text/javascript" >
	jQuery(document).ready(function($) {	
		$('.woocommerce-offer-post-action-link.woocommerce-offer-post-action-link-accept').click(function(){
			var targetID = $(this).attr('data-target');
			var data = {
				'action': 'approveOfferFromGrid',
				'targetID': targetID
			};
						
			$.post(ajaxurl, data, function(response) {
				
				$('tr.post-'+targetID+'.type-woocommerce_offer').addClass('status-accepted-offer');
				$('tr.post-'+targetID+'.type-woocommerce_offer').removeClass('status-publish');
				
				// modify post status icon css
				$('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').removeClass('pending').removeClass('trash').removeClass('declined');
				$('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').addClass('accepted');
				$('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').attr('title', 'Offer Status: Accepted');
				$('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').html('Accepted');
				
				// modify action links on post
				$('#woocommerce-offer-post-action-link-manage-id-'+targetID+'').html('Manage Offer');
				
				var previousPendingCountBubbleValue = $('#woocommerce-offers-count .pending-count').html();
				var newPendingCount = (previousPendingCountBubbleValue - 1);
				$('#woocommerce-offers-count .pending-count').html(newPendingCount);
				if(newPendingCount == 0)
				{
					$('#woocommerce-offers-count').fadeOut('slow');
				}
				
				// remove accept action link
				$('#woocommerce-offer-post-action-link-accept-id-'+targetID+'').parent('span').hide();
				return true;
			});
			return false;
		});
		
		$('.woocommerce-offer-post-action-link.woocommerce-offer-post-action-link-decline').click(function(){
			var targetID = $(this).attr('data-target');
			var data = {
				'action': 'declineOfferFromGrid',
				'targetID': targetID
			};
						
			$.post(ajaxurl, data, function(response) {
				
				
				if($('tr.post-'+targetID+'.type-woocommerce_offer').hasClass('status-publish'))
				{
					var previousPendingCountBubbleValue = $('#woocommerce-offers-count .pending-count').html();
					var newPendingCount = (previousPendingCountBubbleValue - 1);
					$('#woocommerce-offers-count .pending-count').html(newPendingCount);
					if(newPendingCount == 0)
					{
						$('#woocommerce-offers-count').fadeOut('slow');
					}
				}
				// remove the declined post
				$('tr.post-'+targetID+'.type-woocommerce_offer').slideToggle('slow');
				return true;
			});
			return false;
		});
	});
	</script>
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
	 * Filter - Remove meta boxes not needed on edit detail view
	 * @since	0.1.0	 
	 */
	function remove_woocommerce_offer_meta_boxes() 
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
	function my_remove_submenus() 
	{
		global $submenu;
		unset($submenu['edit.php?post_type=woocommerce_offer'][10]); // Removes 'Add New' submenu part from the submenu array
	}
	
	/**
	 * Filter - Add custom CSS in the admin head
	 * @since	0.1.0	 
	 */
	function hide_that_stuff() 
	{		
	  echo '<style type="text/css">';
	  echo '#adminmenu #menu-posts-woocommerce_offer .menu-icon-woocommerce_offer div.wp-menu-image:before { content: "\f110"; }';
	  echo '#woocommerce-product-data ul.wc-tabs li.custom_tab_offers_for_woocommerce a:before, .woocommerce ul.wc-tabs li.custom_tab_offers_for_woocommerce a:before { content: "\e03a";}';
	  //echo '#dashboard_right_now li.comment-count { display:none; }';
	  if('woocommerce_offer' == get_post_type())
	  echo '
	    #woocommerce-offers-settings-link { color:#BBBBBB; }
	    #favorite-actions {display:none;}
	  	.add-new-h2 { display:none; }
		.woocommerce-offer-post-status-grid-icon-div { 
		-moz-transform: translateX(0px) translateY(3px);
		-webkit-transform: translateX(0px) translateY(3px);
		-o-transform: translateX(0px) translateY(3px);
		-ms-transform: translateX(0px) translateY(3px);
		transform: translateX(0px) translateY(3px);
		}
		i.woocommerce-offer-post-status-grid-icon { border:2px solid #CCC; border-radius:8px; padding:0px 12px; font-size:13px; font-weight:normal; line-height:29px; }
		i.woocommerce-offer-post-status-grid-icon.pending { background-color:#D54E21; color:#FFFFFF; border:2px solid #B20000; }
		i.woocommerce-offer-post-status-grid-icon.accepted { background-color:#0074A2; color:#FFFFFF; border:2px solid #003448; }
		i.woocommerce-offer-post-status-grid-icon.completed { background-color:#52A200; color:#FFFFFF; border:2px solid #285100; }
		i.woocommerce-offer-post-status-grid-icon.declined { background-color:#777777; color:#FFFFFF; border:2px solid #333333; }
		i.woocommerce-offer-post-status-grid-icon.trash { background-color:#FCFCFC; color:#777; border:2px solid #777; }
		.type-woocommerce_offer.status-publish td, .type-woocommerce_offer.status-publish th { background-color: #FEF7F1; }
		.type-woocommerce_offer.status-publish th.check-column { border-left: 4px solid #D54E21; }
		.type-woocommerce_offer.status-accepted-offer th.check-column { border-left: 4px solid #0074A2; }	
		.type-woocommerce_offer.status-completed-offer th.check-column { border-left: 4px solid #52A200; }	
		.type-woocommerce_offer.status-declined-offer th.check-column { border-left: 4px solid #333333; }';		
	echo '</style>';
	}
	
	/**
	 * Filter - Modify the comments clause - to exclude "woocommerce_offer" post type
	 * @since	0.1.0
	 * @param  array  $clauses
	 * @param  object $wp_comment_query
	 * @return array
	 */
	function angelleye_ofwc_exclude_cpt_from_comments_clauses( $clauses, $wp_comment_query )
	{
		global $wpdb;
	
		//if ( ! $clauses['join'] )
			$clauses['join'] = "JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID";
	
		//if ( ! $wp_comment_query->query_vars['post_type' ] ) // only apply if post_type hasn't already been queried
			$clauses['where'] .= $wpdb->prepare( " AND {$wpdb->posts}.post_type != %s", 'woocommerce_offer' );
	
		return $clauses;
	}
	
	/**
	 * Filter - Modify the comments clause - to exclude "woocommerce_offer" post type
	 * @since	0.1.0
	 */
	function wpse_72210_comments_exclude_lazy_hook( $screen )
	{
		//if ( $screen->id == 'edit-comments' )
		add_filter( 'comments_clauses', 'angelleye_ofwc_exclude_cpt_from_comments_clauses' );
	}
	
	
	
	
	
	
	/**
	 * Add Meta Boxes
	 *
	 * @since	0.1.0
	 */
	/*function add_woocommerce_offer_metaboxes() {
		add_meta_box('woocommerce_offers_status', 'Offer Status', 'woocommerce_offers_status', 'woocommerce_offer', 'side', 'default');
	}*/
		
	
	/**
	 * Add custom taxonomy for the 'status' types
	 *
	 * @since	X.X.X
	 * NOTE: // using post status to classify our CPT instead of tax
	 */	 
	 /*function woocommerce_offer_status_tax() {
	
		$labels = array(
			'name'                       => _x( 'Offer Status', 'Offer Status', 'angelleye_offers_for_woocommerce' ),
			'singular_name'              => _x( 'Offer Status', 'Offer Status', 'angelleye_offers_for_woocommerce' ),
			'menu_name'                  => __( 'Offer Status', 'angelleye_offers_for_woocommerce' ),
			'all_items'                  => __( 'All Offer Status', 'angelleye_offers_for_woocommerce' ),
			'parent_item'                => __( 'Parent Offer Status', 'angelleye_offers_for_woocommerce' ),
			'parent_item_colon'          => __( 'Parent Offer Status:', 'angelleye_offers_for_woocommerce' ),
			'new_item_name'              => __( 'New Offer Status Name', 'angelleye_offers_for_woocommerce' ),
			'add_new_item'               => __( 'Add New Offer Status', 'angelleye_offers_for_woocommerce' ),
			'edit_item'                  => __( 'Edit Offer Status', 'angelleye_offers_for_woocommerce' ),
			'update_item'                => __( 'Update Offer Status', 'angelleye_offers_for_woocommerce' ),
			'separate_items_with_commas' => __( 'Separate Offer Status with commas', 'angelleye_offers_for_woocommerce' ),
			'search_items'               => __( 'Search Offer Status', 'angelleye_offers_for_woocommerce' ),
			'add_or_remove_items'        => __( 'Add or remove Offer Status', 'angelleye_offers_for_woocommerce' ),
			'choose_from_most_used'      => __( 'Choose from the most used Offer Status', 'angelleye_offers_for_woocommerce' ),
			'not_found'                  => __( 'Not Found', 'angelleye_offers_for_woocommerce' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => false,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'rewrite'                    => false,
			'update_count_callback'      => '_update_post_term_count',	);
		register_taxonomy( 'woocommerce_offer_status', array( 'woocommerce_offer' ), $args );
	
	}
	*/
	
	/**
	 * Set custom columns on CPT edit list view
	 * @since	0.1.0
	 */
	function set_woocommerce_offer_columns($columns) 
	{
		unset($columns['author']);
		unset($columns['comments']);
		unset($columns['date']);
		//unset($columns['woocommerce_offer_status']);
		$columns['woocommerce_original_offer_amount_column'] = __( 'Amount', 'angelleye_offers_for_woocommerce' );
		$columns['woocommerce_original_offer_price_per_column'] = __( 'Price Per', 'angelleye_offers_for_woocommerce' );
		$columns['woocommerce_original_offer_quantity_column'] = __( 'Quantity', 'angelleye_offers_for_woocommerce' );			
		$columns['woocommerce_original_offer_date_column'] = __( 'Date', 'angelleye_offers_for_woocommerce' );    
		return $columns;
	}
	
	/**
	 * Get custom columns data for CPT edit list view
	 * @since	0.1.0
	 */
	function get_woocommerce_offer_column( $column, $post_id ) 
	{
		switch ( $column ) {	
			case 'woocommerce_original_offer_quantity_column' :
				echo number_format(get_post_meta( $post_id , 'woocommerce_original_offer_quantity' , true ), 2); 
			break;
				
			case 'woocommerce_original_offer_price_per_column' :
				echo number_format(get_post_meta( $post_id , 'woocommerce_original_offer_price_per' , true ), 2); 
			break;
				
			case 'woocommerce_original_offer_amount_column' :
				echo number_format(get_post_meta( $post_id , 'woocommerce_original_offer_amount' , true ), 2); 
			break;
			
			case 'woocommerce_original_offer_date_column' :
				echo date('Y-n-j', strtotime(get_the_date())); 
			break;
		}
	}	
	
	/**
	 * Filter the custom columns for CPT edit list view to be sortable
	 * @since	0.1.0
	 */
	function woocommerce_offer_sortable_columns( $columns ) 
	{
		$columns['woocommerce_original_offer_price_per_column'] = 'woocommerce_original_offer_price_per_column';
		$columns['woocommerce_original_offer_quantity_column'] = 'woocommerce_original_offer_quantity_column'; 
		$columns['woocommerce_original_offer_amount_column'] = 'woocommerce_original_offer_amount_column';
		$columns['woocommerce_original_offer_date_column'] = 'woocommerce_original_offer_date_column'; 
		return $columns;
	}
	
	/**
	 * Filter the "quick edit" action links for CPT edit list view
	 * @since	0.1.0
	 */
	function remove_quick_edit( $actions ) 
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
				$actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage Offer') . '</a>';
				$actions['decline-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" id="woocommerce-offer-post-action-link-decline-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Decline') . '</a>';
			}
			elseif($post->post_status == 'declined-offer')
			{
				$actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Manage Offer') . '</a>';
			}
			elseif($post->post_status == 'completed-offer')
			{
				unset($actions['trash']);
			}
			elseif($post->post_status == 'trash')
			{				
			}
			elseif($post->post_status == 'publish')
			{
				$actions['counter-offer-link'] = '<a href="'.get_edit_post_link( $post->ID).'" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-manage" id="woocommerce-offer-post-action-link-manage-id-'.$post->ID.'">' . __('Make Counter Offer') . '</a>';				
				$actions['accept-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-accept" id="woocommerce-offer-post-action-link-accept-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Accept') . '</a>';
				$actions['decline-offer-link'] = '<a href="javascript:;" class="woocommerce-offer-post-action-link woocommerce-offer-post-action-link-decline" id="woocommerce-offer-post-action-link-decline-id-'.$post->ID.'" data-target="'.$post->ID.'">' . __('Decline') . '</a>';
			}
		}
		return $actions;
	}
	
	/**
	 * Register custom post status type -- Accepted Offer
	 * @since	0.1.0
	 */
	function crt_my_custom_post_status_accepted() 
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
	 * Register custom post status type -- Declined Offer
	 * @since	0.1.0
	 */
	function crt_my_custom_post_status_declined() 
	{
		$args = array(
			'label'                     => _x( 'declined-offer', 'Declined Offer', 'angelleye_offers_for_woocommerce' ),
			'label_count'               => _n_noop( 'Declined (%s)',  'Declined (%s)', 'angelleye_offers_for_woocommerce' ), 
			'public'                    => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'exclude_from_search'       => false,
		);
		register_post_status( 'declined-offer', $args );			
	}
	
	/**
	 * Register custom post status type -- Completed Offer
	 * @since	0.1.0
	 */
	function crt_my_custom_post_status_completed() 
	{
		$args = array(
			'label'                     => _x( 'completed-offer', 'Completed Offer', 'angelleye_offers_for_woocommerce' ),
			'label_count'               => _n_noop( 'Completed (%s)',  'Completed (%s)', 'angelleye_offers_for_woocommerce' ), 
			'public'                    => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'exclude_from_search'       => false,
		);
		register_post_status( 'completed-offer', $args );
	}
	
	/**
	 * Append custom post status types on edit detail view
	 * @since	0.1.0
	 * @NOTE:	This uses jQuery to push display of current post status and adds it to the select input on save form #select#post_status input
	 */	 
	function jc_append_post_status_list() 
	{
		global $post;
		$complete = '';
		$label = '';
		if($post->post_type == 'woocommerce_offer')
		{
			if($post->post_status == 'accepted-offer')
			{
				$complete = ' selected=selected';
				$label = "<span id='post-status-display'> Accepted</span>";
			} 
			elseif($post->post_status == 'completed-offer')
			{
				$complete = ' selected=selected';
				$label = "<span id='post-status-display'> Completed</span>";
			}
			elseif($post->post_status == 'declined-offer')
			{
				$complete = ' selected=selected';
				$label = "<span id='post-status-display'> Declined</span>";
			}
			
			if($post->post_status == 'accepted-offer' || $post->post_status == 'completed-offer' || $post->post_status == 'declined-offer')
			{
				echo '<script>jQuery(document).ready(function($){
				$("select#post_status").append("<option value='.$post->post_status.'-offer '.$complete.'>'.ucfirst($post->post_status).'</option>");
				$(".misc-pub-section label").append("'.$label.'");
				});</script>';
			  }
		 }
	}
	
	/**
	 * Filter - Display post status values on edit list view with customized html elements
	 * @since	0.1.0
	 */
	function jc_display_archive_state( $states ) 
	{
		global $post;
		$arg = get_query_var( 'post_status' );
		$screen = get_current_screen();
		if ( $screen->post_type == 'woocommerce_offer' ) 
		{
			if($post->post_status == 'accepted-offer'){
			   return array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon accepted" title="Offer Status: Accepted">Accepted</i></div>');
			}
			elseif($post->post_status == 'publish'){
			   return array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon pending" title="Offer Status: Pending">Pending</i></div>');
			}
			elseif($post->post_status == 'trash'){
			   return array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon trash" title="Offer Status: Trashed">Trashed</i></div>');
			}
			elseif($post->post_status == 'completed-offer'){
			   return array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon completed" title="Offer Status: Completed">Completed</i></div>');
			}
			elseif($post->post_status == 'declined-offer'){
			   return array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon declined" title="Offer Status: Declined">Declined</i></div>');
			}
			else
			{
			  return array('<br><div id="woocommerce-offer-post-status-grid-icon-id-'.$post->ID.'" class="woocommerce-offer-post-status-grid-icon-div"><i class="woocommerce-offer-post-status-grid-icon" title="Offer Status: '.ucwords($post->post_status).'">'.ucwords($post->post_status).'</i></div>');
			}
		}
		return $states;
	}
	
	/**
	 * Filter - Relabel display of post type "publish" for our CPT on edit list view
	 * @since	0.1.0
	 */
	function change_post_to_article($screen) 
	{
		if ( $screen->post_type == 'woocommerce_offer') 
		{
			add_filter('gettext',  'my_get_translated_text_publish');
			add_filter('ngettext',  'my_get_translated_text_publish');
		}
	}
	
	/**
	 * Translate "Published" language to "Pending"
	 * @since	0.1.0
	 */
	function my_get_translated_text_publish($translated)
	{
		$translated = str_ireplace('Published',  'Pending',  $translated);
		return $translated;
	}
	
	/**
	 * Filter - Unset the "edit" option for edit list view
	 * @since	0.1.0
	 */
	function my_custom_bulk_actions($actions)
	{
		unset($actions['edit']);
		return $actions;
	}
	
	/**
	 * Action - Add meta box - "Offer Summary"
	 * @since	0.1.0
	 * @NOTE:	Adds meta box to the side column on the edit detail view for our CPT
	 */
	function add_meta_box_offer_summary() 
	{
		$screens = array('woocommerce_offer');
		foreach($screens as $screen)
		{
			add_meta_box(
				'section_id_offer_summary',
				__( 'Offer Status', 'angelleye_offers_for_woocommerce' ),
				'add_meta_box_offer_summary_callback',
				$screen,
				'left','high'
			);
		}
	}
	
	/**
	 * Callback - Action - Add meta box - "Offer Summary"
	 * Output hmtl for "Offer Summary" meta box
	 * @since	0.1.0
	 * @param WP_Post $post The object for the current post/page
	 */
	function add_meta_box_offer_summary_callback( $post ) 
	{
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'meta_box_offer_summary', 'meta_box_offer_summary_nonce' );
		
		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		
		//$value = get_post_meta( $post->ID, 'woocommerce_offer_status', true );
		
		
		/*
		 * Output html form parts
		 */
		echo 'Offer Summary Meta Box content HERE';
	}
	
	/**
	 * Action - Add meta box - "Offer Status"
	 * @since	0.1.0
	 * @NOTE:	Adds meta box to the side column on the edit detail view for our CPT
	 */
	function myplugin_add_meta_box() 
	{
		$screens = array('woocommerce_offer');
		foreach($screens as $screen)
		{
			add_meta_box(
				'myplugin_sectionid',
				__( 'Offer Status', 'angelleye_offers_for_woocommerce' ),
				'myplugin_meta_box_callback',
				$screen,
				'side','high'
			);
		}
	}
	
	/**
	 * Callback - Action - Add meta box - "Offer Status"
	 * Output hmtl for "Offer Status" meta box
	 * @since	0.1.0
	 * @param WP_Post $post The object for the current post/page
	 */
	function myplugin_meta_box_callback( $post ) 
	{
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'myplugin_meta_box', 'myplugin_meta_box_nonce' );
		
		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$value = get_post_meta( $post->ID, 'woocommerce_offer_status', true );
		
		/*
		 * Set default
		 */
		if (!$value) $current_status_value = 'pending';
		
		/*
		 * Output html form parts
		 */
		echo '<input type="hidden" name="woocommerce_offer_status_noncename" id="woocommerce_offer_status_noncename" value="'; echo wp_create_nonce( 'woocommerce_offer'.$post->ID ); echo '" />';
		echo '<input type="radio" name="woocommerce_offer_status" value=""'; if ($current_status_value == 'pending' || $current_status_value == '') echo "checked=1"; echo '> Pending<br/>';
		echo '<input type="radio" name="woocommerce_offer_status" value="accepted"'; if ($current_status_value == 'accepted') echo "checked=1"; echo '> Accepted<br/>';
		echo '<input type="radio" name="woocommerce_offer_status" value="countered"'; if ($current_status_value == 'countered') echo "checked=1"; echo '> Countered<br/>';
		echo '<input type="radio" name="woocommerce_offer_status" value="rejected"'; if ($current_status_value == 'rejected') echo "checked=1"; echo '> Rejected<br/>';
	}
	
	/**
	 * When the post is saved, saves our custom data
	 * @since	0.1.0
	 * @param int $post_id The ID of the post being saved
	 */
	function myplugin_save_meta_box_data($post_id)
	{
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
	
		// Check if our nonce is set
		if(!isset($_POST['myplugin_meta_box_nonce']))
		{
			return;
		}
			
		// Verify that the nonce is valid
		if(!wp_verify_nonce($_POST['myplugin_meta_box_nonce'], 'myplugin_meta_box'))
		{
			return;
		}
		
		// If this is an autosave, our form has not been submitted, so we don't want to do anything
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
	
		// Check the user's permissions
		if(isset($_POST['post_type']) && 'page' == $_POST['post_type']) 
		{	
			if (!current_user_can('edit_page', $post_id))
			{
				return;
			}
		}
		else
		{	
			if (!current_user_can('edit_post', $post_id))
			{
				return;
			}
		}
	
		/*
		 * OK, its safe for us to save the data now
		 */
		 		
		// Make sure that it is set
		if (!isset($_POST['myplugin_new_field']))
		{
			return;
		}
	
		// Sanitize user input
		$my_data = sanitize_text_field($_POST['myplugin_new_field']);
	
		// Update the meta field in the database
		update_post_meta($post_id, '_my_meta_value_key', $my_data);
	}
	
	/**
	 * Add meta box goes into our admin_init function
	 * @since	0.1.0
	 */
	function render_woocommerce_offer_status_meta($post) 
	{
		$current_status_value = get_post_meta($post->ID, '_woocommerce_offer_status', TRUE);
		if(!$current_status_value) $current_status_value = 'pending';
		?>
		<input type="hidden" name="woocommerce_offer_status_noncename" id="woocommerce_offer_status_noncename" value="<?php echo wp_create_nonce( 'woocommerce_offer'.$post->ID );?>" />
		<input type="radio" name="woocommerce_offer_status" value="" <?php if ($current_status_value == 'pending' || $current_status_value == '') echo "checked=1";?>> Pending<br/>
		<input type="radio" name="woocommerce_offer_status" value="accepted" <?php if ($current_status_value == 'accepted') echo "checked=1";?>> Accepted<br/>
		<input type="radio" name="woocommerce_offer_status" value="countered" <?php if ($current_status_value == 'countered') echo "checked=1";?>> Countered<br/>
		<input type="radio" name="woocommerce_offer_status" value="rejected" <?php if ($current_status_value == 'rejected') echo "checked=1";?>> Rejected<br/>
		<?php
	}
	
	///////////////////////////// -- ??
	//add_meta_box(   'woocommerce-offer-status-meta-div', __('Offer Status'),  'render_woocommerce_offer_status_meta', 'woocommerce_offer', 'advanced', 'high');
	/////////////////////////////
	

	/**
	 * Initialize the plugin options setup 
	 * Adds Options, Sections and Fields
	 * Registers Settings
	 * @since	0.1.0
	 * @NOTE:	This function is registered with the "admin_init" hook
	 */
	function angelleye_ofwc_intialize_options() 
	{
		/**
		 * Add option - 'General Settings'
		 */ 
		if(false == get_option('offers_for_woocommerce_options_general'))	// If the plugin options don't exist, create them.
		{
			add_option('offers_for_woocommerce_options_general');
		}
		/**
		 * Add option - 'Email Settings'
		 */		 
		if(false == get_option('offers_for_woocommerce_options_email'))	// If the plugin options don't exist, create them.
		{
			add_option('offers_for_woocommerce_options_email');
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
		 * Register setting - 'Email Settings'
		 */
		register_setting(
			'offers_for_woocommerce_options_email', // Option group
			'offers_for_woocommerce_options_email', // Option name
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
			'offers_for_woocommerce_options_page_intro_text', // Callback page intro text
			'offers_for_woocommerce_general_settings' // Page
		);
		
		/**
		 * Add field - 'General Settings' - 'general_setting_enable_make_offer_btn_frontpage'
		 * Enable Make Offer button on home page
		 */
		add_settings_field(
			'general_setting_enable_make_offer_btn_frontpage', // ID
			'Enable Make Offer button on home page', // Title 
			'offers_for_woocommerce_options_page_output_input_checkbox', // Callback TEXT input
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
			'offers_for_woocommerce_options_page_output_input_checkbox', // Callback TEXT input
			'offers_for_woocommerce_general_settings', // Page
			'general_settings', // Section 
			array('option_name'=>'offers_for_woocommerce_options_general', 'input_label'=>'general_setting_enable_make_offer_btn_catalog', 'input_required'=>FALSE)         
		);
		
		/**
		 * Add field - 'General Settings' - 'general_setting_enable_make_offer_btn_product_detail'
		 * Enable Make Offer button on product detail
		 */
		add_settings_field(
			'general_setting_enable_make_offer_btn_product_detail', // ID
			'Enable Make Offer button on single product page', // Title 
			'offers_for_woocommerce_options_page_output_input_checkbox', // Callback TEXT input
			'offers_for_woocommerce_general_settings', // Page
			'general_settings', // Section 
			array('option_name'=>'offers_for_woocommerce_options_general', 'input_label'=>'general_setting_enable_make_offer_btn_product_detail', 'input_required'=>FALSE)         
		);
		
		/**
		 * Add section - 'Display Settings'
		 */
		add_settings_section(
			'display_settings', // ID
			'', // Title
			'offers_for_woocommerce_options_page_intro_text', // Callback page intro text
			'offers_for_woocommerce_display_settings' // Page
		);
		
		/**
		 * Add field - 'Display Settings' - 'display_setting_custom_make_offer_btn_text'
		 * Make Offer Button Text
		 */
		add_settings_field(
			'display_setting_custom_make_offer_btn_text', // ID
			'Make Offer button text', // Title 
			'offers_for_woocommerce_options_page_output_input_text', // Callback TEXT input
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
			'offers_for_woocommerce_options_page_output_input_colorpicker', // Callback TEXT input
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
			'offers_for_woocommerce_options_page_output_input_colorpicker', // Callback TEXT input
			'offers_for_woocommerce_display_settings', // Page
			'display_settings', // Section
			array('option_name'=>'offers_for_woocommerce_options_display', 'input_label'=>'display_setting_custom_make_offer_btn_color', 'input_required'=>FALSE)
		);
		
		
		/**
		 * Add section - 'Email Settings'
		 */
		add_settings_section(
			'email_settings', // ID
			'', // Title
			'offers_for_woocommerce_options_page_intro_text', // Callback page intro text
			'offers_for_woocommerce_email_settings' // Page
		);
		
		/**
		 * Add field - 'Email Settings' - 'email_setting_notifications_from_email'
		 * Notifications From Email
		 */
		add_settings_field(
			'email_setting_notifications_from_email', // ID
			'Notifications From Email', // Title 
			'offers_for_woocommerce_options_page_output_input_text', // Callback TEXT input
			'offers_for_woocommerce_email_settings', // Page
			'email_settings', // Section
			array('option_name'=>'offers_for_woocommerce_options_email', 'input_label'=>'email_setting_notifications_from_email', 'input_required'=>FALSE)
		);
		
		/**
		 * Add field - 'Email Settings' - 'email_setting_notifications_from_name'
		 * Notifications From Name
		 */
		add_settings_field(
			'email_setting_notifications_from_name', // ID
			'Notifications From Name', // Title 
			'offers_for_woocommerce_options_page_output_input_text', // Callback TEXT input
			'offers_for_woocommerce_email_settings', // Page
			'email_settings', // Section
			array('option_name'=>'offers_for_woocommerce_options_email', 'input_label'=>'email_setting_notifications_from_name', 'input_required'=>FALSE)
		);
		
		/**
		 * Add field - 'Email Settings' - 'email_setting_admin_notifications_email'
		 * Admin Notifications Email
		 */
		add_settings_field(
			'email_setting_admin_notifications_email', // ID
			'Admin Notifications Email', // Title 
			'offers_for_woocommerce_options_page_output_input_text', // Callback TEXT input
			'offers_for_woocommerce_email_settings', // Page
			'email_settings', // Section
			array('option_name'=>'offers_for_woocommerce_options_email', 'input_label'=>'email_setting_admin_notifications_email', 'input_required'=>FALSE)
		);
		
		
		/**
		 * Action - Enqueue the colour picker
		 * @since	0.1.0
		 */
		add_action( 'admin_enqueue_scripts', 'enqueue_colour_picker' );
		
		/**
		 * Callback - Action - Enqueue the colour picker
		 * @since	0.1.0
		 */
		function enqueue_colour_picker()
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
				 	 
	} // END - angelleye_ofwc_intialize_options
	
	/**
	 * Callback - Options Page intro text
	 * @since	0.1.0
	 */
	function offers_for_woocommerce_options_page_intro_text() 
	{
		print('<p>Complete the form below and click Save Changes button to update your settings.</p>');
	}
	
	/**
	 * Callback - Options Page - Output a 'text' input field for options page form
	 * @since	0.1.0
	 * @param	$args - Params to define 'option_name','input_label'
	 */
	function offers_for_woocommerce_options_page_output_input_text($args) 
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
	function offers_for_woocommerce_options_page_output_input_checkbox($args) 
	{
		$options = get_option($args['option_name']);
		$field_label = $args['input_label'];
		$field_required = ($args['input_required'] === true) ? ' required="required" ' : '';
		print(
			'<input '. $field_required. ' type="checkbox" id="'.$field_label.'" name="'.$args['option_name'].'['.$field_label.']" value="1" ' . checked(1, $options[$field_label], false) . '/>'
        );
	}
	
	/**
	 * Callback - Options Page - Output a 'colorpicker' input field for options page form
	 * @since	0.1.0
	 * @param	$args - Params to define 'option_name','input_label'
	 */
	function offers_for_woocommerce_options_page_output_input_colorpicker($args) 
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
	
	/////////////////////////////////////////////////////////////////
	///////////// NOT USING THESE -- THESE ARE EXAMPLES /////////////
	/////////////////////////////////////////////////////////////////			
	
		/**
		 * Callback FX - Options Page - Output plugin options page form html parts for "Display Options"
		 * @since	0.1.0
		 */
		function sandbox_toggle_header_callback($args) 
		{
			// First, we read the options collection
			$options = get_option('sandbox_theme_display_options');
			 
			// Next, we update the name attribute to access this element's ID in the context of the display options array
			// We also access the show_header element of the options collection in the call to the checked() helper function
			$html = '<input type="checkbox" id="show_header" name="sandbox_theme_display_options[show_header]" value="1" ' . checked(1, $options['show_header'], false) . '/>';
			 
			// Here, we'll take the first argument of the array and add it to a label next to the checkbox
			$html .= '<label for="show_header"> '  . $args[0] . '</label>';
			 
			echo $html;		 
		} // end sandbox_toggle_header_callback
		
		/**
		 * Callback FX - Options Page - Output plugin options page form html parts for "Display Options"
		 * @since	0.1.0
		 */
		function sandbox_toggle_content_callback($args) {
		 
			$options = get_option('sandbox_theme_display_options');
			 
			$html = '<input type="checkbox" id="show_content" name="sandbox_theme_display_options[show_content]" value="1" ' . checked(1, $options['show_content'], false) . '/>';
			$html .= '<label for="show_content"> '  . $args[0] . '</label>';
			 
			echo $html;		 
		} // end sandbox_toggle_content_callback
		
		/**
		 * Callback FX - Options Page - Output plugin options page form html parts for "Display Options"
		 * @since	0.1.0
		 */
		function sandbox_toggle_footer_callback($args)
		{
			$options = get_option('sandbox_theme_display_options');
			
			$html = '<input type="checkbox" id="show_footer" name="sandbox_theme_display_options[show_footer]" value="1" ' . checked(1, $options['show_footer'], false) . '/>';
			$html .= '<label for="show_footer"> '  . $args[0] . '</label>';
			
			echo $html;		 
		} // end sandbox_toggle_footer_callback

	/**
	 * END - custom funtions
	 */
	 
		
	} // END - construct
		
	
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
		
		/*if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );
		}*/
		if (is_admin())
		{
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Angelleye_Offers_For_Woocommerce::VERSION );
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
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Angelleye_Offers_For_Woocommerce::VERSION );
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
     */
	public function offers_for_woocommerce_options_validate_callback( )
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
	
	
	
	
}
