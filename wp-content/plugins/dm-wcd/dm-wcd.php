<?php
/**
 * Plugin Name: DM WooCommerce Discounts
 * Plugin URI: http://www.datacom.eu
 * Description: DM WooCommerce Discounts 
 * Author: Datacom
 * Author URI: http://www.datacom.sk
 * Version: 1.0.0
 * Text Domain: dm-wcd
 *
 * Copyright: (c) 2017 Datacom (info@datacom.eu)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author    DATACOM
 * @copyright (c) 2017 Datacom (info@datacom.eu)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require plugin_dir_path( __FILE__ ) . 'includes/class-dm-wcd.php';
require plugin_dir_path(__FILE__) . 'includes/class-dm-wcd-discount.php';

function run_dm_wcd() {

    add_action( 'admin_menu', 'dc_wcd_menu' );

	$plugin = new DM_wcd();
	$plugin->run();

}
run_dm_wcd();

function dc_wcd_menu() {
    add_menu_page( 'Zľavy', 'Zľavy', 'edit_posts', 'zlavy', 'zlavy_nastavenie', 'http://apl.sk.localhost/wp-content/plugins/dm-wcd/admin/images/dm-wcd-dashicon.png' );
}