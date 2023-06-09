<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wisdmlabs.com
 * @since             1.0.0
 * @package           Store
 *
 * @wordpress-plugin
 * Plugin Name:       Store Plugin
 * Plugin URI:        https://wisdmlabs.com
 * Description:       Creates Stores For Local Pickup in Woocommerce
 * Version:           1.0.0
 * Author:            Subhajit Bera
 * Author URI:        https://wisdmlabs.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       store
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'STORE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-store-activator.php
 */
function activate_store() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-store-activator.php';
	Store_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-store-deactivator.php
 */
function deactivate_store() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-store-deactivator.php';
	Store_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_store' );
register_deactivation_hook( __FILE__, 'deactivate_store' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-store.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_store() {

	$plugin = new Store();
	$plugin->run();

}
run_store();
