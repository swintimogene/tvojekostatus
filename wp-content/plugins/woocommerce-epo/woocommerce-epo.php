<?php
/**
 * Plugin Name:       WooCommerce Extra Product Options Pro
 * Plugin URI:        https://epo.localhost/product/woocommerce-extra-product-options
 * Description:       Design woocommerce Product form in your own way, customize Product fields(Add, Edit, Delete and re arrange fields).
 * Version:           2.3.9
 * Author:            EPO
 * Author URI:        https://epo.localhost/
 *
 * Text Domain:       woocommerce-epo
 * Domain Path:       /languages
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 3.5.6
 */

if(!defined('WPINC')){	die; }

if (!function_exists('is_woocommerce_active')){
	function is_woocommerce_active(){
	    $active_plugins = (array) get_option('active_plugins', array());
	    if(is_multisite()){
		   $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	    }
	    return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins) || class_exists('WooCommerce');
	}
}

if(is_woocommerce_active()) {
	define('EPO_VERSION', '2.3.9');
	!defined('EPO_SOFTWARE_TITLE') && define('EPO_SOFTWARE_TITLE', 'WooCommerce Extra Product Options');
	!defined('EPO_FILE') && define('EPO_FILE', __FILE__);
	!defined('EPO_PATH') && define('EPO_PATH', plugin_dir_path( __FILE__ ));
	!defined('EPO_URL') && define('EPO_URL', plugins_url( '/', __FILE__ ));
	!defined('EPO_BASE_NAME') && define('EPO_BASE_NAME', plugin_basename( __FILE__ ));
	
	/**
	 * The code that runs during plugin activation.
	 */
	function activate_epo() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-epo-activator.php';
		EPO_Activator::activate();
	}
	
	/**
	 * The code that runs during plugin deactivation.
	 */
	function deactivate_epo() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-epo-deactivator.php';
		EPO_Deactivator::deactivate();
	}
	
	register_activation_hook( __FILE__, 'activate_epo' );
	register_deactivation_hook( __FILE__, 'deactivate_epo' );

	function epo_license_form_title_note($title_note){
		$help_doc_url = 'https://epo.localhost.localhost/help-guides/general-guides/download-purchased-plugin-file';

		$title_note .= ' Find out how to <a href="%s" target="_blank">get your license key</a>.';
		$title_note  = sprintf($title_note, $help_doc_url);
		return $title_note;
	}
	
	function epo_license_page_url($url, $prefix){
		$url = 'edit.php?post_type=product&page=th_extra_product_options_pro&tab=license_settings';
		return admin_url($url);
	}

	function init_auto_updater_epo(){
		/*
        if(!class_exists('EPO_License_Manager') ) {
			add_filter('thlm_license_form_title_note_woocommerce_extra_product_options', 'epo_license_form_title_note');
			add_filter('thlm_license_page_url_woocommerce_extra_product_options', 'epo_license_page_url', 10, 2);
			add_filter('thlm_enable_default_license_page', '__return_false');

			require_once( plugin_dir_path( __FILE__ ) . 'class-epo-license-manager.php' );
			$api_url = 'https://epo.localhost/';
			EPO_License_Manager::instance(__FILE__, $api_url, 'plugin', EPO_SOFTWARE_TITLE);
		}
        */
	}
	init_auto_updater_epo();
	
	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-epo.php';
	
	/**
	 * Begins execution of the plugin.
	 */
	function run_epo() {
		$plugin = new EPO();
		$plugin->run();
	}
	run_epo();
}