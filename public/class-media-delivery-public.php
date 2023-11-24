<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/public
 * @author     Josh Corne <josh@joshcorne.co.uk>
 */
class Media_Delivery_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Media_Delivery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Media_Delivery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/media-delivery-public.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-lightbox', plugin_dir_url( __FILE__ ) . 'css/lightbox.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Media_Delivery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Media_Delivery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$localised_strings = array(
			'download' => __( 'Download', 'media-delivery' ),
			'loading' => __( 'Loading...', 'media-delivery' )
		);

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/media-delivery-public.min.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'translatedStrings', $localised_strings );
		wp_enqueue_script( $this->plugin_name );
		wp_add_inline_script( $this->plugin_name, 'const ajaxurl = '. json_encode( admin_url( 'admin-ajax.php' ) ), 'before' );
		wp_enqueue_script( $this->plugin_name.'-lightbox', plugin_dir_url( __FILE__ ) . 'js/lightbox.min.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Generate the zip
	 *
	 * @see		 https://davidwalsh.name/create-zip-php
	 * @since    1.0.0
	 */
	public function generate_zip( ) {
		if( 
			isset( $_POST['post_id'] ) && 
			! post_password_required( $_POST['post_id'] ) 
		) {

			$zip = $this->create_zip_file( $_POST['post_id'] );
			// We could use the instantiated zipper to get this instead of array?
			update_post_meta( 
				$_POST['post_id'], 
				'_media_zip', 
				$zip['filename']
			); 

			wp_send_json_success( array( 
				'url' => $zip['url']
			));
		}
		wp_send_json_error( array( 
			'message' => __( 'Something went wrong.', 'media-delivery' )
		));
	}

	private function create_zip_file( $post_id ) {
		if ( ! class_exists( 'Zipper' ) ) {
			require_once plugin_dir_path( __FILE__ ) . '/class-media-delivery-zipper.php';
		}

		$title = sanitize_title_with_dashes( get_the_title( $post_id ) );
		$media = array();

		$img_ids = get_post_meta( $post_id, '_img_ids', true );
		foreach( $img_ids as $id ) {
			$media[] = get_attached_file( $id );
		}

		$video_id = get_post_meta( $post_id, '_vid_id', true );
		$media[] = get_attached_file( $video_id );

		// Zipper will provide a premade zip from cache if available
		$zipper = new Zipper( $this->plugin_name );
		return $zipper->zip_images( $title.'-'.$post_id, $media );
	}

	/**
	 * For 'customer_media' post types, alter the password form to have two boxes
	 * for booking ref and surname.
	 *
	 * @since    1.0.0
	 */
	public function dual_password_form( $form ) {
		global $post;

		if ( 'customer_media' === get_post_type($post) ) {
			$msg = '';

			if ( defined( 'INVALID_POST_PASS' ) ) {
				$msg = __( 'Sorry, those are not the correct details.', 'media-delivery' );
			}

			$ref_label = 'refbox-'.( empty( $post->ID ) ? rand() : $post->ID );
			$name_label = 'namebox-'.( empty( $post->ID ) ? rand() : $post->ID );

			ob_start();
			include_once 'partials/media-delivery-public-login.php';
			
			return str_replace(array("\r","\n"),'',trim(ob_get_clean()));
		}

		return $form;
	}

	/**
	 * This function is used to check if the password is valid and
	 * set a constant to display the error message if not.
	 *
	 * @since    1.0.0
	 */
	public function check_post_pass() {

		if ( ! is_single( ) || ! post_password_required() ) {
			return;
		}
	
		if ( isset( $_COOKIE['wp-postpass_' . COOKIEHASH ] ) ) {
			define( 'INVALID_POST_PASS', true );

			// Remove cookie from global too
			unset( $_COOKIE['wp-postpass_' . COOKIEHASH ] );
			// Tell the browser to remove the cookie so the message doesn't show up every time
			setcookie( 'wp-postpass_' . COOKIEHASH, '', -1, COOKIEPATH );
		}
	}

	/**
	 * This function is used to check if the password is valid and
	 * set a constant to display the error message if not.
	 *
	 * @since    1.0.0
	 */
	public function add_public_content( $content ) {
		global $post;

		if ( is_singular( 'customer_media' ) && in_the_loop() && !post_password_required() ) {
			$video = get_post_meta( $post->ID, '_vid_id', true );
			$images = get_post_meta( $post->ID, '_img_ids', true );
			$vid_url = wp_get_attachment_url( $video, 'full' );

			ob_start();
			include_once 'partials/media-delivery-public-display.php';
			$content = str_replace(array("\r","\n"),'',trim(ob_get_clean()));

		}
	
		return $content;
	}

	/**
	 * This function is used to override the theme's single.php.
	 * This ensures there are no back/prev or unnecessary features on
	 * a customer media page.
	 * 
	 * @since    1.0.0
	 */
	public function get_customer_media_template( $single_template ) {
		global $post;
	
		if ( 'customer_media' === $post->post_type && ! wp_is_block_theme() ) {
			$single_template = plugin_dir_path( __FILE__ ) . 'partials/single-customer_media.php';
		}
	
		return $single_template;
	}

	/**
	 * This will remove 'Protected: ' from the titles.
	 *
	 * @since    1.0.0
	 */
	public function change_protected_title_prefix( $format, $post ) {
		if ( 'customer_media' === get_post_type($post) ) {
			return '%s';
		}
		return $format;
	}

	/**
	 * This blocks attachment pages.
	 *
	 * @since    1.0.0
	 */
	public function disable_attachment_pages () {
		global $post;
		if ( ! is_attachment() || ! isset( $post->post_parent ) || ! is_numeric( $post->post_parent ) ) {
			return;
		}

		// Does the attachment have a parent post?
		// If the post is trashed, fallback to redirect to homepage.
		if ( 0 !== $post->post_parent && 'trash' !== get_post_status( $post->post_parent ) ) {
			// Redirect to the attachment parent.
			wp_safe_redirect( get_permalink( $post->post_parent ), 301 );
		} else {
			// For attachment without a parent redirect to homepage.
			wp_safe_redirect( get_bloginfo( 'wpurl' ), 302 );
		}
		exit;
	}

	/**
	 * This will modify all queries to exclude customer_media posts,
	 * except in single or admin.
	 *
	 * @see 	 https://wordpress.org/documentation/article/protect-posts-with-password/
	 * @since    1.0.0
	 */
	public function exclude_media_delivery_posts( $query ) {
		// Cannot use is_singluar this early on so alter query appropriately
		if( !$query->is_singular && 
			'customer_media' === $query->get('post_type') && 
			!is_admin() ) {
				add_filter( 'posts_where', array( $this, 'exclude_protected' ) );
		}
	}

	// Filter to hide protected posts
	public function exclude_protected( $where ) {
		global $wpdb;
		return $where .= " AND {$wpdb->posts}.post_password = '' ";
	}
}
