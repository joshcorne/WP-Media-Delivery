<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$meta_keys = array(
	'_meta_fields_booking_ref',
	'_meta_fields_customer_surname',
	'_meta_fields_customer_email',
	'_img_ids',
	'_vid_id'
);

$options = array(
	'media_delivery_delete_data',
);

$post_type = 'customer_media';

// Delete all the posts
if ( get_option('media_delivery_delete_data') ) {
	$all_posts = get_posts( 
		array(
			'post_type'=>$post_type,
			'numberposts'=>'-1'
		)
	);

	// Delete all the post meta
	foreach( $meta_keys as $key ) {
		delete_metadata('post', 0, $key, '', true);
	}

	// Delete all the posts
	foreach ( $all_posts as $single ) {
		wp_delete_post( $single->ID, true );
	}

	// Delete all the options
	foreach ( $options as $option ) {
		if ( get_option($option) ) {
			delete_option($option);
			delete_site_option( $option );
		}
	}
}

delete_option( 'media_delivery_activation_notices' );

unregister_setting( 'media_delivery', 'media_delivery_delete_data' );

// Unregister the post type
unregister_post_type( $post_type );