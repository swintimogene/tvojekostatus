<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Instagram
 * @subpackage Woo_Instagram/includes
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Instagram_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		set_transient( '_woocommerce_instagram_product_photos_welcome_screen', true, 30 );
	}

}
