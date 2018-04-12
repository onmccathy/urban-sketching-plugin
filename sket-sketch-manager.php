<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://huttartsites.co.nz
    * @since          1.2.1
 * @package           Sket_Sketch_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Sketch Manager
 * Plugin URI:        sketch-manager
 * Description:       This plugin contains the customisations for the Hutt Art Society Website
 * Version:           1.2.1
 * Author:            Owen McCarthy
 * Author URI:        http://huttartsites.co.nz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sket-sketch-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('TEXTDOMAIN','sket-sketch-manager');
define('PLUGIN_URL', plugin_dir_url(__FILE__));
//define('PLUGIN_UNIT_TEST', false);

 /**
         * The framework responsible for metabox funuctionality
         */
        require_once plugin_dir_path( __FILE__ ) . 'includes/cmb2/init.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sket-sketch-manager-activator.php
 */
function activate_sket_sketch_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sket-sketch-manager-activator.php';
	Sket_Sketch_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sket-sketch-manager-deactivator.php
 */
function deactivate_sket_sketch_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sket-sketch-manager-deactivator.php';
	Sket_Sketch_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sket_sketch_manager' );
register_deactivation_hook( __FILE__, 'deactivate_sket_sketch_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sket-sketch-manager.php';

/**
 * The common functions file
 * contains common functions used throughout the plugin.
 */
require plugin_dir_path( __FILE__ ) . 'functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sket_sketch_manager() {

	$plugin = new Sket_Sketch_Manager();
        
	$plugin->run();

}
run_sket_sketch_manager();
