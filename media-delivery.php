<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://joshcorne.co.uk
 * @since             1.0.0
 * @package           Media_Delivery
 *
 * @wordpress-plugin
 * Plugin Name:       Media Delivery
 * Plugin URI:        https://joshcorne.co.uk/media-delivery/
 * Description:       This plugin can be used to upload images and videos to your WordPress instance and then deliver them securely to your customer.
 * Version:           1.0.0
 * Author:            Josh Corne
 * Author URI:        https://joshcorne.co.uk/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       media-delivery
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'MEDIA_DELIVERY_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-media-delivery-activator.php
 */
function activate_media_delivery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-media-delivery-activator.php';
	Media_Delivery_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-media-delivery-deactivator.php
 */
function deactivate_media_delivery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-media-delivery-deactivator.php';
	Media_Delivery_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_media_delivery' );
register_deactivation_hook( __FILE__, 'deactivate_media_delivery' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-media-delivery.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_media_delivery() {

	$plugin = new Media_Delivery();
	$plugin->run();

}
run_media_delivery();
