<?php
/**
 * The core utility functionality for the plugin.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/utils
 */
if(!defined('WPINC')){	die; }

if(!class_exists('EPO_Utils_Core')):

class EPO_Utils_Core {
	public static function log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}

endif;