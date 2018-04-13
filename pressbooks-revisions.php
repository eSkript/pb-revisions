<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/lukaiser
 * @since             1.0.0
 * @package           Pb_Revisions
 *
 * @wordpress-plugin
 * Plugin Name:       Press Books Revisions
 * Plugin URI:        https://github.com/eSkript/pb-revisions
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Lukas Kaiser
 * Author URI:        https://github.com/lukaiser
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pb-revisions
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
define( 'PB_REVISIONS_VERSION', '1.0.0' );

$role = get_role( 'administrator' );
$role->add_cap( 'activate_plugins' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pb-revisions-activator.php
 */
function activate_pb_revisions($networkwide) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pb-revisions-activator.php';
	Pb_Revisions_Activator::activate($networkwide);
}

/**
 * The code that runs during when a new blog is created
 * This action is documented in includes/class-pb-revisions-activator.php
 */
function activate_new_blog_pb_revisions($blog_id) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pb-revisions-activator.php';
	Pb_Revisions_Activator::activate_new_blog($blog_id);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pb-revisions-deactivator.php
 */
function deactivate_pb_revisions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pb-revisions-deactivator.php';
	Pb_Revisions_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pb_revisions' );
add_action('wpmu_new_blog', 'activate_new_blog_pb_revisions', 8, 1);
register_deactivation_hook( __FILE__, 'deactivate_pb_revisions' );

// -------------------------------------------------------------------------------------------------------------------
// Class autoloader
// -------------------------------------------------------------------------------------------------------------------

function _pb_revisions_autoload( $class_name ) {

	$prefix = 'PBRevisions\\';
	$len = strlen( $prefix );
	if ( strncasecmp( $prefix, $class_name, $len ) !== 0 ) {
		// Ignore classes not in our namespace
		return;
	}
	$parts = explode( '\\', strtolower( $class_name ) );
	array_shift( $parts );
	$class_file = 'class-pb-revisions-' . str_replace( '_', '-', array_pop( $parts ) ) . '.php';
	$path = count( $parts ) ? implode( '/', $parts ) . '/' : '';
	$path = str_replace( '_', '-', $path );
	@include( plugin_dir_path( __FILE__ ) . $path . $class_file );
}

spl_autoload_register( '_pb_revisions_autoload' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pb-revisions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pb_revisions() {

	$plugin = new Pb_Revisions();
	$plugin->run();

}
run_pb_revisions();
