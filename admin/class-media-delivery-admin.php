<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/admin
 * @author     Josh Corne <josh@joshcorne.co.uk>
 */
class Media_Delivery_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/media-delivery-admin.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Register the JavaScript for the admin area.
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
			'videoFrameButton' => __( 'Use this video', 'media-delivery' ),
			'videoFrameTitle' => __( 'Select or Upload The Video', 'media-delivery' ),
			'deleteImg' => __( 'Delete image', 'media-delivery' ),
			'imgFrameButton' => __( 'Use these images', 'media-delivery' ),
			'imgFrameTitle' => __( 'Select or Upload Images', 'media-delivery' ),
			'mediaMissingError' => __( 'Video or images missing.', 'media-delivery' ),
			'passwordLengthError' => __( 'Name or reference too long.', 'media-delivery' )
		);

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/media-delivery-admin.min.js', array( 'jquery', 'jquery-ui-core', 'wp-color-picker' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'translatedStrings', $localised_strings );
		wp_enqueue_script( $this->plugin_name );
		
		global $post;
		if( $post ){
			wp_enqueue_media(array(
				'post'  => $post->ID,
			));
		}

	}

	/**
	 * Call me crazy, but I think there is a bug in WordPress which is showing
	 * mod_rewrite as enabled when it isn't. This overrides that.
	 *
	 * @since    1.0.0
	 */
	public function apache_rewrite_enabled() {
		return apache_mod_loaded( 'mod_rewrite' );
	}

	/**
	 * Show the admin notice.
	 *
	 * @since    1.0.0
	 */
	public function show_admin_notices() {
		$notices = get_option( 'media_delivery_activation_notice' );
		if ( $notices && !empty( $notices )) {
			foreach( $notices as $notice ) {
				echo wp_kses_post( $notice );
			}
		}
	}

	/**
	 * Remove the admin notice.
	 *
	 * @since    1.0.0
	 */
	public function hide_admin_notices( ) {
		$notices = get_option( 'media_delivery_activation_notice' );
		if ( isset( $notices ) && isset( $notices[$_POST['notice']] ) ) {
			unset( $notices[$_POST['notice']] );
		}

		if ( sizeof( $notices ) > 0) {
			update_option( 'media_delivery_activation_notice', $notices );
		} else {
			delete_option( 'media_delivery_activation_notice' );
		}
	}

	/**
	 * Do video shortcode for provided source.
	 *
	 * @since    1.0.0
	 */
	public function fetch_video_shortcode() {
		if ( isset( $_POST['src'] ) ) {	  
		  wp_send_json_success(do_shortcode('[video src="'.$_POST['src'].'"]'));
		}
	  }

	/**
	 * Add the settings button to sub-menu.
	 *
	 * @since    1.0.0
	 */
	public function additional_menus() {
		add_submenu_page(
			'edit.php?post_type=customer_media', 
			__( 'Customer Media Security', 'media-delivery' ), 
			__( 'Security', 'media-delivery' ), 
			'manage_options', 
			'security',
			array( $this, 'media_delivery_security_callback' )
		);

		add_submenu_page(
			'edit.php?post_type=customer_media', 
			__( 'Customer Media Settings', 'media-delivery' ), 
			__( 'Settings' ), 
			'manage_options', 
			'settings',
			array( $this, 'media_delivery_settings_callback' )
		);
	}

	/**
	 * Register the settings for the plugin.
	 *
	 * @since    1.0.0
	 */	
	public function register_plugin_settings() {	
		$this->add_settings_sections();
		$this->add_email_settings();
		$this->add_general_settings();	
	}

	/**
	 * Register the settings for the plugin.
	 *
	 * @since    1.0.0
	 */	
	public function add_general_settings() {	
		add_settings_field( 
			'media_delivery_delete_data', 
			__( 'Delete All Data On Uninstall', 'media-delivery' ),
			array( $this, 'add_delete_data_option' ), 
			'media_delivery', 
			'media_delivery_general',
			array( 'label_for' => 'media-delivery-delete-data' )
		);

		register_setting( 
			'media_delivery', 
			'media_delivery_delete_data'
		);

		add_settings_field( 
			'media_delivery_media_title', 
			__( 'Media Title', 'media-delivery' ),
			array( $this, 'add_media_title_option' ), 
			'media_delivery', 
			'media_delivery_general',
			array( 'label_for' => 'media-delivery-delete-data' )
		);

		register_setting( 
			'media_delivery', 
			'media_delivery_media_title'
		);
	}

	/**
	 * Register the settings for the plugin.
	 *
	 * @since    1.0.0
	 */	
	public function add_email_settings() {

		add_settings_field( 
			'media_delivery_email_bg_color', 
			__( 'Color', 'media-delivery' ),
			array( $this, 'add_email_color_option' ), 
			'media_delivery', 
			'media_delivery_email',
			array( 'label_for' => 'media-delivery-email-color' )
		);

		register_setting( 
			'media_delivery', 
			'media_delivery_email_bg_color'
		);

		add_settings_field( 
			'media_delivery_email_subject', 
			__( 'Subject', 'media-delivery' ),
			array( $this, 'add_email_subject_option' ), 
			'media_delivery', 
			'media_delivery_email',
			array( 'label_for' => 'media-delivery-email-subject' )
		);

		register_setting( 
			'media_delivery', 
			'media_delivery_email_subject'
		);
		
		add_settings_field( 
			'media_delivery_email_content', 
			__( 'Content', 'media-delivery' ),
			array( $this, 'add_email_content_option' ), 
			'media_delivery', 
			'media_delivery_email',
			array( 'label_for' => 'media-delivery-email-content' )
		);

		register_setting( 
			'media_delivery', 
			'media_delivery_email_content'
		);

	}

	/**
	 * Register the settings for the plugin.
	 *
	 * @since    1.0.0
	 */	
	public function add_settings_sections() {	
		add_settings_section( 
			'media_delivery_general', 
			__( 'General', 'media-delivery' ), 
			array( $this, 'add_general_settings_section_title' ), 
			'media_delivery' 
		);

		add_settings_section( 
			'media_delivery_email', 
			__( 'Email', 'media-delivery' ), 
			array( $this, 'add_email_settings_section_title' ), 
			'media_delivery' 
		);
	}

	/**
	 * General settings subtitle callback.
	 *
	 * @since    1.0.0
	 */
	public function add_general_settings_section_title() {
		echo '';
	}

	/**
	 * General settings subtitle callback.
	 *
	 * @since    1.0.0
	 */
	public function add_email_settings_section_title() {
		echo '<p>'
				.__('Available tags: ', 'media-delivery')
				.'<code>{site_url}</code>, <code>{site_title}</code>, <code>{post_url}</code>, <code>{first_name}</code>, <code>{surname}</code>.'
			.'</p>';
	}

	/**
	 * Delete data setting callback.
	 *
	 * @since    1.0.0
	 */
	public function add_delete_data_option() {
		echo '<input id="media-delivery-delete-data" 
				name="media_delivery_delete_data" 
				type="checkbox" 
				value="1" 
				class="media-delivery-general-option"'
				.checked( 1, esc_attr(get_option('media_delivery_delete_data')), false) .'/>';
	}
	
	/**
	 * Media title setting callback.
	 * 
	 * @since    1.0.0
	 */
	public function add_media_title_option() {
		echo '<input id="media-delivery-media-title" 
				name="media_delivery_media_title" 
				type="text" 
				value="'.esc_attr( 
					get_option(
						'media_delivery_media_title',
						__( '{first_name}\'s Media', 'media-delivery' )
					) 
				).'" 
				class="media-delivery-general-option" />
				<p><small>Available tag: <code>{first_name}</code>.</small></p>';
	}

	/**
	 * Email color setting callback.
	 *
	 * @since    1.0.0
	 */
	public function add_email_color_option() {
		echo '<span id="media-delivery-email-color-preview"
				class="color-picker-preview" 
				style="background-color:'
					.esc_attr( get_option('media_delivery_email_bg_color', '#515151' ) )
				.';">
			</span>
			<input id="media-delivery-email-color" 
				name="media_delivery_email_bg_color" 
				type="text" 
				placeholder="#515151" 
				value="'.esc_attr( get_option('media_delivery_email_bg_color', '#515151') ).'" 
				class="color-picker media-delivery-email-option" 
				required />';
	}

	/**
	 * Email content setting callback.
	 *
	 * @since    1.0.0
	 */
	public function add_email_content_option() {
		echo wp_editor( 
			wp_kses_post( get_option( 'media_delivery_email_content' ) ), 
			'media-delivery-email-content', 
			$settings = array(
				'textarea_name'=>'media_delivery_email_content',
				'media_buttons' => false,
				'textarea_rows' => 10,
				'teeny' => true,
				'quicktags' => false
			)
		);
	}

	/**
	 * Email subject setting callback.
	 *
	 * @since    1.0.0
	 */
	public function add_email_subject_option() {
		echo '<input id="media-delivery-email-subject" 
				name="media_delivery_email_subject" 
				type="text" 
				value="'.esc_attr( get_option( 'media_delivery_email_subject' ) ).'" 
				class="media-delivery-email-option" />';
	}

	/**
	 * A stylesheet for the iframe of the editor.
	 * Used in settings page. Scope further down to settings only,
	 * as opposed to type if editor starts being used elsewhere.
	 *
	 * @since    1.0.0
	 */
	public function add_editor_stylesheet() {
		global $typenow;
		if ( isset( $typenow ) && $typenow === 'customer_media' ) {
			add_editor_style( plugin_dir_url( __FILE__ ) . 'css/wpeditor.css' );
		}
	}

	/**
	 * Set up the security screen.
	 *
	 * @since    1.0.0
	 */
	public function media_delivery_security_callback() { 
		include_once 'partials/media-delivery-admin-security.php';
	}

	/**
	 * Set up the settings screen.
	 *
	 * @since    1.0.0
	 */
	public function media_delivery_settings_callback() { 
		include_once 'partials/media-delivery-admin-settings.php';
	}

	/**
	 * Add all the boxes to the new post screen.
	 *
	 * @since    1.0.0
	 */
	public function setup_meta_boxes() {
	
		global $wp_meta_boxes;
		global $post;
		$current_post_type = get_post_type($post);

		if ( 'customer_media' === $current_post_type ) {
			$wp_meta_boxes = array();
			$wp_meta_boxes['customer_media'] = array(
				'side' => array('core' => array( )),
				'normal' => array('core' => array( ))
			  );
		}
		
		add_meta_box(
			'publish-media-box', 
			__( 'Publish' ), 
			array( $this, 'publish_build_meta_box_callback' ), 
			'customer_media',
			'side',
			'core'
		 );
		
		add_meta_box(
			'meta-fields-meta-box', 
			__( 'Customer details', 'media-delivery' ), 
			array( $this, 'meta_fields_build_meta_box_callback' ), 
			'customer_media',
			'side'
		 );


		 add_meta_box(
			'video-meta-box',
			__( 'Video', 'media-delivery'),
			array( $this, 'video_build_meta_box_callback' ), 
			'customer_media',
			'normal',
			'core'
		);

		add_meta_box(
			'images-meta-box',
			__( 'Photos', 'media-delivery'),
			array( $this, 'imgs_build_meta_box_callback' ), 
			'customer_media',
			'normal',
			'core'
		);
	}

	/**
	 * Set up the video meta box.
	 *
	 * @since    1.0.0
	 */
	public function video_build_meta_box_callback( $post ) {

		// Get WordPress' media upload URL
		$upload_link = get_upload_iframe_src( 'video', $post->ID );
		
		// See if there's a media id already saved as post meta
		$your_vid_id = get_post_meta( $post->ID, '_vid_id', true );
		
		// Get the video src
		$your_vid_src = wp_get_attachment_url( $your_vid_id, 'full' );
		
		include_once 'partials/media-delivery-admin-video.php';
	}

	/**
	 * Set up the images meta box.
	 *
	 * @since    1.0.0
	 */
	public function imgs_build_meta_box_callback( $post ) {

		// Get WordPress' media upload URL
		$upload_link = get_upload_iframe_src( 'image', $post->ID );
		
		// See if there's a media id already saved as post meta
		$img_ids = get_post_meta( $post->ID, '_img_ids', true );
		
		// For convenience, see if the array is valid
		$have_imgs = is_array( $img_ids );
		
		include_once 'partials/media-delivery-admin-images.php';
	}

	/**
	 * Set up the publish meta box.
	 *
	 * @since    1.0.0
	 */
	public function publish_build_meta_box_callback( $post ) {
		
		$post_type = $post->post_type;
		$post_type_object = get_post_type_object($post_type);
		$can_publish = current_user_can($post_type_object->cap->publish_posts);

		include_once 'partials/media-delivery-admin-publish.php';
	}

	/**
	 * Set up the text fields meta box.
	 *
	 * @since    1.0.0
	 */	
	public function meta_fields_build_meta_box_callback( $post ) {

		$booking = get_post_meta( $post->ID, '_meta_fields_booking_ref', true );
		$surname = get_post_meta( $post->ID, '_meta_fields_customer_surname', true );
		$email = get_post_meta( $post->ID, '_meta_fields_customer_email', true );
		$first_name = get_post_meta( $post->ID, '_meta_fields_customer_first_name', true );

		$error_msg = '';

		if( strlen( $booking ) + strlen( $surname ) > 20 ) {
			$error_msg = 'Name or reference too long.';
		}

		include_once 'partials/media-delivery-admin-meta.php';
	}

	/**
	 * Sets the upload dir for customer_media.
	 *
	 * @since    1.0.0
	 */	
	public function set_upload_dir( $upload ) {
		$upload['subdir'] = '/customer_media' . $upload['subdir'];
		$upload['path'] = $upload['basedir'] . $upload['subdir'];
		$upload['url']  = $upload['baseurl'] . $upload['subdir'];
		return $upload;
	}

	/**
	 * For customer_media posts, adds filter to change upload dir.
	 * Required as access control will only work on subdir.
	 *
	 * @since    1.0.0
	 */
	public function custom_upload_dir( ) {
		global $pagenow;

		if ( ! empty( $_REQUEST['post_id'] ) && ( 'async-upload.php' == $pagenow || 'media-upload.php' == $pagenow ) ) {
			if ( 'customer_media' === get_post_type( $_REQUEST['post_id'] ) ) {
				add_filter( 'upload_dir', array( $this, 'set_upload_dir' ) );
			}
		}
	}

	/**
	 * Removes the quick edit button from the admin list
	 *
	 * @since    1.0.0
	 */
	public function remove_quick_edit( $actions ) {
		unset( $actions['inline hide-if-no-js'] );
        return $actions;
	}

	/**
	 * Remove bulk actions from admin list.
	 *
	 * @since	1.0.0
	 */
    public function remove_bulk_actions( $actions ) {
        unset( $actions['edit'] );
        return $actions;
    }

	/**
	 * Removes the autosave script from customer_media types.
	 *
	 * @since	1.0.0
	 */
	public function block_autosave() {
		if ( 'customer_media' === get_post_type() ) {
			//wp_dequeue_script( 'autosave' );
		}
	}

	/**
	 * Runs before the post is inputted into the database.
	 * This calls the functions to validate and set the 
	 * fields which are calculated.
	 *
	 * @param	 array	$data					sanitised, slashed and processed data
	 * @param	 array	$postarr				sanitised and slashed data
	 * @param	 array	$unsanitised_postarr	unprocessed, unsanitised data
	 * @param	 bool	$update					whether this is an existing post
	 * @return	 array	$data
	 * @since    1.0.0
	 */
	public function pre_save_post_media_delivery( $data, $postarr ) {
		
		if ( 
			'customer_media' === $data['post_type'] &&
			'trash' !== $data['post_status'] &&
			'auto-draft' !== $data['post_status'] &&
			! wp_is_post_revision( $postarr['ID'] ) &&
			! wp_is_post_autosave( $postarr['ID'] )
		) {

			if ( ! current_user_can( 'edit_customer_media', $postarr['ID'] ) ) {
				wp_die( __( 'You don\'t have permission.', 'media-delivery' ) );
			} 

			$errors = $this->validate_input( $data, $postarr );
			if ( ! empty( $errors ) ) wp_die( $errors );
			
			$data['post_title'] = $this->set_title( $postarr );
			$data['post_name'] = $this->set_name( $data, $postarr );
			$data['post_password'] = $this->set_password( $data, $postarr );
		}

		return $data;
	}

	/**
	 * Validate form input.
	 * 
 	 * @access   private
	 * @param	 array		$data
	 * @param	 array		$postarr
	 * @return	 string		$errors
	 * @since    1.0.0
	 */
	private function validate_input( $data , $postarr ) {
		// This works but would be better as an array
		$error = '';

		// Must have all meta fields
		$keys = array( 
			'meta_fields_booking_ref', 
			'meta_fields_customer_email', 
			'meta_fields_customer_surname',
			'meta_fields_customer_first_name'
		);
		foreach ($keys as $k) {
			if ( ! isset($postarr[$k] ) || empty( $postarr[$k] ) ) {
				$error = $error.__( 'Missing metadata. ', 'media-delivery' );
				break;
			}
		}

		// Must have either a video or img
		if ( ( ! isset( $postarr['vid_id'] ) || empty( $postarr['vid_id'] ) ) && 
			( ! isset( $postarr['img_ids'] ) || empty( $postarr['img_ids'] ) ) ) {
			$error = $error.__( 'No images or video. ', 'media-delivery' );
		}

		// Must not exceed 20 chars combined
		if( strlen( $postarr['meta_fields_booking_ref'] ) + strlen( $postarr['meta_fields_customer_surname'] ) > 20 ) {
			$error = $error.__( 'Name or reference too long. ', 'media-delivery' );
		}

		return $error;
	}

	/**
	 * Set the post password.
	 * Calculated from the booking ref and surname.
	 * 
	 * @access   private
	 * @param	 array		$data
	 * @param	 array		$postarr
	 * @return	 string		
	 * @since    1.0.0
	 */
	private function set_password( $data, $postarr ) {
		if ( isset( $postarr['meta_fields_booking_ref'] ) && !empty( $postarr['meta_fields_booking_ref'] ) &&  
		 isset( $postarr['meta_fields_customer_surname'] ) && !empty( $postarr['meta_fields_booking_ref'] ) ) {
			return $postarr['meta_fields_booking_ref'].strtoupper($postarr['meta_fields_customer_surname']);
		}
		return $data['post_password'];
	}
	
	/**
	 * Set the name of the field for the slug.
	 * Hash the value of the booking ref + time() to give
	 * pseudorandom value to prevent part of the password being 
	 * available in the URL.
	 * 
	 * @access   private
	 * @param	 array		$data
	 * @param	 array		$postarr
	 * @return	 string		
	 * @since    1.0.0
	 */
	private function set_name( $data , $postarr ) {
		// Once the slug is set, don't change when updating
		if ( str_contains( $data['post_name'], 'auto-draft' ) ) {
			// They're inevitably going to put random, duplicate booking refs in
			// so add time just to ensure unique (although WP will manage it too)
			return wp_hash($postarr['meta_fields_booking_ref'].time());
		}
		return $data['post_name'];
	}

	/**
	 * This set the post title to be the chosen title from
	 * settings supporting first_name replacement.
	 * 
 	 * @access   private
	 * @param	 array		$data
	 * @param	 array		$postarr
	 * @return	 string		
	 * @since    1.0.0
	 */
	private function set_title( $postarr ) {
		$media_title = get_option( 
			'media_delivery_media_title',
			__( '{first_name}\'s Media', 'media-delivery' )
		);

		return str_replace(
			'{first_name}', 
			esc_html( $postarr['meta_fields_customer_first_name'] ) ,
			$media_title
		);
	}

	/**
	 * This is hooked into the admin list to add the custom
	 * meta fields on the post list.
	 *
	 * @param	 array 		$columns
	 * @return	 array		$columns
	 * @since    1.0.0
	 */
	public function add_meta_column_headers_to_admin_list( $columns ) {
		$date = $columns['date'];
		unset( $columns['date'] ); 
		unset( $columns['author'] );

		$columns['_meta_fields_booking_ref'] = __( 'Booking Reference', 'media-delivery' );
		$columns['_meta_fields_customer_surname'] = __( 'Surname', 'media-delivery' );
		$columns['date'] = $date;

		return $columns;
	}

	/**
	 * This is hooked into the admin list to add the custom
	 * meta fields on the post list.
	 *
	 * @param	 			$column
	 * @param	 int		$post_id
	 * @since    1.0.0
	 */
	public function add_meta_column_data_to_admin_list( $column, $post_id ) {
		echo esc_html( get_post_meta( $post_id, $column , true ) );
	}
	
	/**
	 * Runs all the necessary saving functions, hooked into save_post_video.
	 *
	 * @param	 int|string		$post_id
	 * @param	 WP:Post		$post
	 * 
	 * @since    1.0.0
	 */
	public function do_save_post_customer_media( $post_id, $post ) {
		if ( 
			'trash' === $post->post_status || 
			'auto-draft' === $post->post_status ||
			wp_is_post_revision( $post_id ) ||
			wp_is_post_autosave( $post_id )
		) return;

		if ( current_user_can( 'edit_customer_media', $post_id ) ) {
			$this->meta_save( $post_id );
			$this->delete_cached_zip( $post_id );
			$this->imgs_save( $post_id );
			$this->video_save( $post_id );
			$this->maybe_send_email_on_save( $post_id, $post );
		} else {
			wp_die( __( 'You don\'t have permission.', 'media-delivery' ) );
		}
	}

	/**
	 * Delete a saved zip file. Runs when saving to invalidate 
	 * cached zip file and serve fresh when user downloads because
	 * contents may have changed.
	 * 
 	 * @access   private
	 * @param	 int		$post_id
	 * @since    1.0.0
	 */
	private function delete_cached_zip( $post_id ) {
		$file = get_post_meta( $post_id, '_media_zip', true );

		if ( file_exists( $file ) && is_writable( $file ) )
			unlink($file);
		
		delete_post_meta( $post_id, '_media_zip' ); 
	}

	/**
	 * Save the meta text fields.
	 * 
 	 * @access   private
	 * @return	 int		$post_id
	 * @since    1.0.0
	 */
	private function meta_save( $post_id ) {

		update_post_meta( $post_id, '_meta_fields_booking_ref', sanitize_text_field( $_POST['meta_fields_booking_ref'] ) ); 
		update_post_meta( $post_id, '_meta_fields_customer_first_name', sanitize_text_field( $_POST['meta_fields_customer_first_name'] ) );
		update_post_meta( $post_id, '_meta_fields_customer_surname', sanitize_text_field( $_POST['meta_fields_customer_surname'] ) );
		update_post_meta( $post_id, '_meta_fields_customer_email', sanitize_email( $_POST['meta_fields_customer_email'] ) );

	}

	/**
	 * If the admin chooses to send an email to the user, 
	 * this will use wp_mail to execute that.
	 *
 	 * @access   private
	 * @param	 int		$post_id	The post ID
	 * @param	 WP_Post	$post		The post
	 * @since    1.0.0
	 */
	private function maybe_send_email_on_save( $post_id, $post ) {

		if ( isset( $_POST['send_email']) && $_POST['send_email'] === 'true' ) {
			$subscribers	= array( get_post_meta( 
								$post_id, 
								'_meta_fields_customer_email', 
								true 
							) );
			$subject		= $this->tag_replacement( get_option( 'media_delivery_email_subject' ), $post );
			$headers		= array( 'Content-Type: text/html; charset=UTF-8' );
			$message 		= $this->tag_replacement( wpautop( get_option( 'media_delivery_email_content' ) ), $post );
			$bg_color 		= get_option( 'media_delivery_email_bg_color', '#515151' );

			ob_start();
			include_once 'partials/media-delivery-admin-email.php';
			$template = str_replace(array("\r","\n"),'',trim(ob_get_clean()));

			wp_mail( $subscribers, $subject, $template, $headers );
		}
	}

	/**
	 * Replaces the tags in the message content.
	 * Although $message is escaped in 
	 * partial/media-delivery-admin-email.php, this function 
	 * can be used elsewhere (such as subject) so it is escaped
	 * here too.
	 * 
	 * @access	private	
	 * @param	string		$message	message to have the tags replaced
	 * @param	WP_Post		$post		The post
	 * @return	string	 	the message with its tags replaced
	 */
	private function tag_replacement( $message, $post ) {
		$available_tags = [
			'site_title' => esc_html( get_bloginfo( 'name' ) ),
			'site_url' => esc_url( get_bloginfo( 'wpurl' ) ),
			'post_url' => esc_url( get_permalink( $post ) ),
			'first_name' => esc_html( 
				get_post_meta( $post->ID, '_meta_fields_customer_first_name', true ) 
			),
			'surname' => esc_html( 
				get_post_meta( $post->ID, '_meta_fields_customer_surname', true ) 
			)
		];

		foreach( $available_tags as $tag=>$content ) {
			$message = str_ireplace( '{'.$tag.'}', $content, $message );
		}
		return $message;
	}

	/**
	 * Saves the gallery images.
	 * 
 	 * @access   private
	 * @param	 int		$post_id
	 * @since    1.0.0
	 */
	private function imgs_save( $post_id ) {
		if ( 
			isset( $_POST['img_ids'] ) &&
			! empty( $numeric_ids = array_filter( $_POST['img_ids'], 'is_numeric' ) )
		) {
			update_post_meta( $post_id, '_img_ids', array_map( 'absint', $numeric_ids ) );
		} else {
			delete_post_meta( $post_id, '_img_ids' );
		}
	}

	/**
	 * Saves the video.
	 *
 	 * @access   private
	 * @param	 int		$post_id
	 * @since    1.0.0
	 */
	private function video_save( $post_id ) {
		if ( isset( $_POST['vid_id'] ) && is_numeric( $_POST['vid_id']  ) ) {
			update_post_meta( $post_id, '_vid_id', $_POST['vid_id'] ); 
		} else {
			delete_post_meta( $post_id, '_vid_id' );
		}
	}

	/**
 	 * Filter all posts of this post type from the wp-sitemap.xml.
	 *
	 * @param	array		$post_types		The list of post types
	 * @return	array		The list of post types
	 * @since	1.0.0
	 */
	public function remove_posts_from_sitemap( $post_types ) {
		unset( $post_types['customer_media'] );

		return $post_types;
	}

	/**
 	 * This will delete all the attachments linked to a customer_media post
	 * on delete of the post.
	 *
	 * @param	int			$id		The post id
	 * @param	WP_Post		$post	The post object
	 *
	 * @since	1.0.0
	 */
	public function delete_attachments_with_post( $id, $post ) {
		if ( 'customer_media' === $post->post_type ) {
			foreach( get_attached_media( '', $id ) as $attachment ) {
				wp_delete_attachment( $attachment->ID, 'true' );
			}
		}
	}
}
