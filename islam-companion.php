<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * this starts the plugin.
 *
 * @link:       http://nadirlatif.me/islam-companion
 * @since             1.0.0
 * @package           Islam_Companion
 *
 * @wordpress-plugin
 * Plugin Name:       Islam Companion
 * Plugin URI:        http://nadirlatif.me/islam-companion
 * Description:        The goal of this plugin is to make it easier to integrate Islam in your every day life
 * Version:           1.0.6
 * Author:            Nadir Latif
 * Author URI:        http://nadirlatif.me
 * License:            GPL-2.0+
 * License URI:        http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:            islam-companion
 * Domain Path:            /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-islam-companion-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-islam-companion-deactivator.php';

/** This action is documented in includes/class-islam-companion-activator.php */
register_activation_hook( __FILE__, array( 'Islam_Companion_Activator', 'activate' ) );

/** This action is documented in includes/class-islam-companion-deactivator.php */
register_activation_hook( __FILE__, array( 'Islam_Companion_Deactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-islam-companion.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_Islam_Companion() {

	$plugin = new Islam_Companion();
	$plugin->run();

}
run_Islam_Companion();
