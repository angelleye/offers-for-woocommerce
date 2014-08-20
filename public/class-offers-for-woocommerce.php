<?php
/**
 * Offers for WooCommerce - public
 *
 * @package   Angelleye_Offers_For_Woocommerce
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 * @copyright 2014 AngellEYE
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
	const VERSION = '0.0.2';

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
		 * Load plugin text domain
		 */
		add_action('init', array($this, 'load_plugin_textdomain'));
		
		/**
		 * Activate plugin when new blog is added
		 */
		add_action('wpmu_new_blog', array($this, 'activate_new_site'));
		
		/**
		 * Load public-facing style sheet and javascript
		 */
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

		/*******************************
		 * Define custom functionality *
		 *******************************
		 */							
		 
		 /**
		  * Init - Add Offer To Cart functions
		  * @since	0.1.0
		  */
		 add_action('init', array($this, 'add_offer_to_cart_init'));
		 
		 /**
		  * Init - New Offer Form Submit
		  * @since	0.1.0
		  */
		 add_action('init', array($this, 'new_offer_form_submit'));
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
	
	public function add_offer_to_cart_init()
	{
		if(!is_admin())
		{
			global $woocommerce;
			$cart_subtotal = $woocommerce->cart->get_cart_subtotal();
			$cart_items = $woocommerce->cart->get_cart();
			
			// Check if form was posted and select task accordingly
			if (isset($_REQUEST['woocommerceoffer_atc']) && $_REQUEST['woocommerceoffer_atc'] != '') 
			{
				$woocommerceoffer_atc = $_REQUEST['woocommerceoffer_atc'];
				switch ($woocommerceoffer_atc) {
					
					case 'test':
						$inc = 'test1';
						//include_once $inc;
						break;
						
					case 'test2':
						$inc = 'test2';
							//include_once $inc;
							
						echo 'Cart Contents: ';
						echo '<br>';
						echo '<pre>';
						print_r($cart_items);
						echo '</pre>';							
						break;
					default: $inc = '';
						break;
				}		
			}
		}
	}
	
	public function new_offer_form_submit()
	{
		if(!is_admin())
		{
			//global $woocommerce;
			
			
			
			// Check if form was posted and select task accordingly
			if (isset($_REQUEST['woocommerceoffer_post']))
			{
				sleep(1);				
				// return success
				if($_POST['offer_name'] == 'cole')
				{
					echo json_encode(array("statusmsg" => 'success'));
					exit;
				}
				else
				{
					// return error msg
					echo json_encode(array("statusmsg" => 'failed'));
					exit;
				}
			}
			
		}
	}

	
	/**
	 * END FILE!
	 */
}