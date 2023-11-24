<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Media_Delivery
 * @subpackage Media_Delivery/includes
 * @author     Josh Corne <josh@joshcorne.co.uk>
 */
class Media_Delivery {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Media_Delivery_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MEDIA_DELIVERY_VERSION' ) ) {
			$this->version = MEDIA_DELIVERY_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'media-delivery';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_global_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Media_Delivery_Loader. Orchestrates the hooks of the plugin.
	 * - Media_Delivery_i18n. Defines internationalization functionality.
	 * - Media_Delivery_Admin. Defines all hooks for the admin area.
	 * - Media_Delivery_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-media-delivery-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-media-delivery-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-media-delivery-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-media-delivery-public.php';

		$this->loader = new Media_Delivery_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Media_Delivery_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Media_Delivery_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Media_Delivery_Admin( $this->get_plugin_name(), $this->get_version() );

		// Actions
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'show_admin_notices' );
		$this->loader->add_action( 'wp_ajax_dismiss_activation_notice', $plugin_admin, 'hide_admin_notices' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_plugin_settings' );
		$this->loader->add_action( 'wp_ajax_fetch_video_shortcode', $plugin_admin, 'fetch_video_shortcode' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'additional_menus' ); 
		$this->loader->add_action( 'admin_head', $plugin_admin, 'setup_meta_boxes', 5 );
		$this->loader->add_action( 'save_post_customer_media', $plugin_admin, 'do_save_post_customer_media', 10, 2 );
		$this->loader->add_action( 'wp_insert_post_data', $plugin_admin, 'pre_save_post_media_delivery', 5, 2 );
		$this->loader->add_action( 'manage_customer_media_posts_custom_column', $plugin_admin, 'add_meta_column_data_to_admin_list', 10, 2 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'add_editor_stylesheet' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'block_autosave' );
		$this->loader->add_action( 'before_delete_post', $plugin_admin, 'delete_attachments_with_post', 99, 2 );

		//Filters
		$this->loader->add_filter( 'admin_init', $plugin_admin, 'custom_upload_dir', 999 );
		$this->loader->add_filter( 'manage_customer_media_posts_columns', $plugin_admin, 'add_meta_column_headers_to_admin_list');
		$this->loader->add_filter( 'got_rewrite', $plugin_admin, 'apache_rewrite_enabled' );
		$this->loader->add_filter( 'wp_sitemaps_post_types', $plugin_admin, 'remove_posts_from_sitemap' );
		$this->loader->add_filter( 'bulk_actions-edit-customer_media', $plugin_admin, 'remove_bulk_actions' );
		$this->loader->add_filter( 'post_row_actions', $plugin_admin, 'remove_quick_edit' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Media_Delivery_Public( $this->get_plugin_name(), $this->get_version() );

		// Actions
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'wp', $plugin_public, 'check_post_pass' );
		$this->loader->add_action( 'the_password_form', $plugin_public, 'dual_password_form', 9999 );
		$this->loader->add_action( 'the_content', $plugin_public, 'add_public_content' );
		$this->loader->add_action( 'protected_title_format', $plugin_public, 'change_protected_title_prefix' , 10, 2 );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'disable_attachment_pages', 1 );
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'exclude_media_delivery_posts');
		$this->loader->add_action( 'wp_ajax_download_images_zip', $plugin_public, 'generate_zip' );
		$this->loader->add_action( 'wp_ajax_nopriv_download_images_zip', $plugin_public, 'generate_zip' );

		// Filters
		$this->loader->add_filter( 'single_template', $plugin_public, 'get_customer_media_template' );
	}

	/**
	 * Register all of the hooks related to the global functionality of 
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_global_hooks() {
		// Actions
		$this->loader->add_action( 'init', $this, 'create_post_type' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Media_Delivery_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register post type for plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function create_post_type() {
		register_post_type( 'customer_media', array(
				'labels' 				=> array(
					'name' 						=> __( 'Customer Media', 'media-delivery' ),
					'singular_name' 			=> __( 'Customer Media', 'media-delivery' ),
					'menu_name'             	=> _x( 'Customer Media', 'Admin Menu text', 'media-delivery' ),
					'name_admin_bar'        	=> _x( 'Customer Media', 'Add New on Toolbar', 'media-delivery' ),
					'add_new'               	=> __( 'Add New', 'media-delivery' ),
					'add_new_item' 				=> __( 'Add New Customer Media', 'media-delivery' ),
					'new_item'              	=> __( 'New Customer Media', 'media-delivery' ),
					'edit_item'           		=> __( 'Edit Customer Media', 'media-delivery' ),
					'view_item'             	=> __( 'View Customer Media', 'media-delivery' ),
					'all_items'             	=> __( 'All Customer Media', 'media-delivery' ),
					'search_items'          	=> __( 'Search Customer Media', 'media-delivery' ),
					'parent_item_colon'     	=> __( 'Parent Customer Media', 'media-delivery' ),
					'not_found'             	=> __( 'No Customer Media found.', 'media-delivery' ),
					'not_found_in_trash'    	=> __( 'No Customer Media found in Bin.', 'media-delivery' ),
					'archives'              	=> _x( 'Customer Media posts', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'media-delivery' ),
					'insert_into_itefm'      	=> _x( 'Insert into Customer Media', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'media-delivery' ),
					'uploaded_to_this_item' 	=> _x( 'Uploaded to this Customer Media', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'media-delivery' ),
					'filter_items_list'     	=> _x( 'Filter Customer Media list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'media-delivery' ),
					'items_list_navigation' 	=> _x( 'Customer Media list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'media-delivery' ),
					'items_list'            	=> _x( 'Customer Media list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'media-delivery' ),
				),
				'menu_icon' 			=> 'dashicons-video-alt',
				'description'			=> __( 'Media which is to be delivered to customers.', 'media-delivery'),
				'public' 				=> true,
				'publicly_queryable'	=> true,
				'show_ui'            	=> true,
				'show_in_menu'       	=> true,
				'has_archive' 			=> false,
				'show_in_rest' 			=> true,
				'menu_position' 		=> 20,
				'supports' 				=> array(''),
				'rewrite'				=> array('slug' => 'customer_media'),
				'capabilities' 			=> array(
					'edit_post'          => 'edit_this_customer_media', 
					'read_post'          => 'read_this_customer_media', 
					'delete_post'        => 'delete_this_customer_media', 
					'edit_posts'         => 'edit_customer_media',
					'delete_posts'		 => 'delete_customer_media',
				),
				'map_meta_cap' 			=> true
		  	)
		);
	}
}
