<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Media_Delivery
 * @subpackage Media_Delivery/includes
 * @author     Josh Corne <josh@joshcorne.co.uk>
 */
class Media_Delivery_Deactivator {

	/**
	 * Upon deactivation, remove the .htaccess file is applied.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $is_apache;

		if ( $is_apache ) {
			Media_Delivery_Deactivator::remove_htaccess();
		}

		Media_Delivery_Deactivator::delete_plugin_role( );
		Media_Delivery_Deactivator::remove_caps_from_role( 'administrator' );
		Media_Delivery_Deactivator::remove_mac_files( );

		flush_rewrite_rules();
	}

	/**
	 * This will remove the custom role for the media delivery plugin.
	 */
	private static function delete_plugin_role() {
		remove_role( 'customer_media_manager' );
	}

	/**
	 * This removes the capabilities from the provided role.
	 * 
	 * @param	$role_name		the role to remove the capabilities from
	 */
	private static function remove_caps_from_role( $role_name ) {
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
	 * This file will remove the additions made to .htaccess
	 */
	private static function remove_htaccess(){
		// Edit our htaccess dynamically
		if( !is_multisite() ){
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
			$home_path = get_home_path();
			$htaccess_location = $home_path . '.htaccess';

			$marker = 'WP-MAC';
	
			// rewrite file using WP Filesystem
			ob_start(); 
			$creds = request_filesystem_credentials('', false, false, dirname($htaccess_location), null, true);
			ob_end_clean();
			if ( $creds ) {
				$filesystem = WP_Filesystem($creds, dirname($htaccess_location), true);
				if ( $filesystem ) {
					global $wp_filesystem;
					$contents = $wp_filesystem->get_contents($htaccess_location);

					$newcontents = Media_Delivery_Deactivator::remove_marker($contents, $marker);
					$writeresult = $wp_filesystem->put_contents($htaccess_location, $newcontents, FS_CHMOD_FILE);
				}
			}
		}
	}

	/**
	 * This file find the WP-MAC additions, remove it and return the new content.
	 */
	private static function remove_marker($contents, $marker) {
		$posa = strpos($contents, '# BEGIN '.$marker);
		$posb = strpos($contents, '# END '.$marker) + strlen('# END '.$marker);
		if( $posa && $posb ) {
			$newcontent = substr($contents, 0, $posa);
			$newcontent .= substr($contents, $posb, strlen($contents));

			return $newcontent;
		}
	}

	/**
	 * This will remove the mac.php and mac-requires.php files.
	 */
	private static function remove_mac_files( ) {
		$file = ABSPATH . 'mac.php';
		if ( file_exists( $file ) && is_writable( dirname( $file ) ) )
			unlink( $file );

		$file = ABSPATH . 'mac-requires.php';
		if ( file_exists( $file ) && is_writable( dirname( $file ) ) )
			unlink( $file );
	}
}
