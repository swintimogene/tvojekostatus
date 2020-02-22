<?php

/**
 * Plugin Name:       WooCommerce Instagram Product Photos
 * Plugin URI:        https://www.thedotstore.com/
 * Description:       WooCommerce Instagram Product Photos displays Instagram photographs of your products, based on a hashtag.
 * Version:           2.5.7
 * Author:            Thedotstore
 * Author URI:        https://www.thedotstore.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-instagram
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-instagram-activator.php
 */
function activate_woo_instagram() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-instagram-activator.php';
	Woo_Instagram_Activator::activate();
}

if (!defined('WI_TEXT_DOMAIN')) {
    define('WI_TEXT_DOMAIN', 'woo-instagram');
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-instagram-deactivator.php
 */
function deactivate_woo_instagram() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-instagram-deactivator.php';
	Woo_Instagram_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_instagram' );
register_deactivation_hook( __FILE__, 'deactivate_woo_instagram' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-instagram.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_instagram() {

	$plugin = new Woo_Instagram();
	$plugin->run();

}
run_woo_instagram();
