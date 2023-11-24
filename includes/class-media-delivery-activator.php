<?php

/**
 * Fired during plugin activation
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Media_Delivery
 * @subpackage Media_Delivery/includes
 * @author     Josh Corne <josh@joshcorne.co.uk>
 */
class Media_Delivery_Activator {

	/**
	 * On activation, update .htaccess if using apache and 
	 * got mod_rewrite. Otherwise, notify user to do it manually.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// This will clear out any stale activation notices
		delete_option( 'media_delivery_activation_notice' );

		global $is_apache;
		global $is_IIS;
		global $is_nginx;

		if ( $is_apache ) {

			$added_htaccess = Media_Delivery_Activator::add_htaccess();

		 	if( !$added_htaccess ) {

				Media_Delivery_Activator::set_notice( 
					__( 'Could not write .htaccess. See here: ', 
						'media-delivery')
					.'<a href="'.get_admin_url().'edit.php?post_type=customer_media&page=security">'
						.__( 'Security', 'media-delivery' )
					.'</a>',
					'htaccess-notice',
					'notice-error'
				);	
			}

			if ( !got_mod_rewrite() ) {

					Media_Delivery_Activator::set_notice( 
						__( 'You do not have mod_rewrite enabled in Apache. See here: ', 
							'media-delivery')
						.'<a href="'.get_admin_url().'edit.php?post_type=customer_media&page=security">'
							.__( 'Security', 'media-delivery' )
						.'</a>',
						'rewrite-notice',
						'notice-error'
					);
			}
		} elseif ( $is_IIS ) {
			Media_Delivery_Activator::set_other_webserver_notice( 'IIS ');
		} elseif ( $is_nginx ) {
			Media_Delivery_Activator::set_other_webserver_notice( 'Nginx ');
		} else {
			Media_Delivery_Activator::set_other_webserver_notice();
		}
		
		Media_Delivery_Activator::create_plugin_role( );
		Media_Delivery_Activator::add_caps_to_role( 'administrator' );
		Media_Delivery_Activator::move_mac_files( );

		flush_rewrite_rules();
	}

	/**
	 * Add the capabilities to the administrator role
	 * 
	 * @param 	$role_name		The role to add the capabilities from
	 */
	private static function add_caps_to_role( $role_name ) {
		$role = get_role( $role_name );
		$capabilities = array(
			'edit_this_customer_media', 
			'read_this_customer_media', 
			'delete_this_customer_media',
			'edit_customer_media',
			'delete_customer_media',		
		);
		foreach ( $capabilities as $cap ) {
			$role->add_cap( $cap );
		}
	}

	/**
	 * Create the Customer Media Manager role.
	 */
	private static function create_plugin_role( ) {
		add_role(
			'customer_media_manager',
			'Customer Media Manager',
			array(
				'edit_this_customer_media'          => true, 
				'read_this_customer_media'          => true, 
				'delete_this_customer_media'        => true, 
				'edit_customer_media'      			=> true,
				'delete_customer_media'			 	=> true,
				'read'								=> true
			)
		);
	}

	/**
	 * Sets the option to show a message if they have a webserver
	 * other than Apache so they know they will need to set up 
	 * rewrite rules manually.
	 *
	 * @param	 string		The name of the webserver.
	 * @return	 string		The message to display in the notice.
	 * @since    1.0.0
	 */
	private static function set_other_webserver_notice( $webserver = 'unknown' ) {
		$message = __( 
			__( sprintf( 'Your webserver is %1$s so the rewrite rules must be 
				set up manually. See here: ', $webserver ), 'media-delivery')
			.'<a href="'.get_admin_url().'/edit.php?post_type=customer_media&page=security">'
				.__( 'Security', 'media-delivery')
			.'</a>'
		);
	
		return Media_Delivery_Activator::set_notice( $message, 'server-notice' ); 
	}

	/**
	 * Sets the option to show a message if they have a webserver
	 * other than Apache so they know they will need to set up 
	 * rewrite rules manually.
	 *
	 * @param	 string		The full message to display.
	 * @param	 string		The id for the message div
	 * @param	 string		The classes for the message div
	 * @return	 string		The HTML to display to the user.
	 * @since    1.0.0
	 */
	private static function set_notice( $message, $id, $classes = 'notice-info' ) {
		$notices = get_option( 'media_delivery_activation_notice', array() );

		$notices[$id] =
			'<div id="'.$id.'" class="activation-notice notice is-dismissible '.$classes.'">
				<p>'.$message.'</p>
			</div>';
		update_option( "media_delivery_activation_notice", $notices ); 
	}

	/**
	 * This will automatically edit .htaccess with the rewrite rules
	 * for access control to media files.
	 *
	 * @return	 bool	$success whether insert_with_markers succeeded
	 * @since    1.0.0
	 */
	private static function add_htaccess() {
		$success = false;
		// Edit our htaccess dynamically
		if( !is_multisite() ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
			$home_path = get_home_path();
			$htaccess_location = $home_path . '.htaccess';
		
			$content = array();

			if ( got_mod_rewrite() ) {
				$content[] = '<IfModule mod_rewrite.c>';
				$content[] = 'RewriteEngine On';
				$content[] = 'RewriteRule ^/wp-content/uploads/(customer_media/.+)$ /wp-content/plugins/media-delivery/includes/mac.php?file=$1 [QSD,L]';
				$content[] = '</IfModule>';
			}

			$success = insert_with_markers( $htaccess_location, 'WP-MAC', $content );
		}
		return $success;
	}

	/**
	 * This will put the mac files at the root.
	 *
	 * @since    1.0.0
	 */
	private static function move_mac_files( ) {
		$file = ABSPATH . 'mac.php';
		if ( is_writable( dirname( $file ) ) )
			copy( dirname(__FILE__) . '/mac.php', $file );

		$file = ABSPATH . 'mac-requires.php';
		if ( is_writable( dirname( $file ) ) )
			copy( dirname(__FILE__) . '/mac-requires.php', $file );
	}
}