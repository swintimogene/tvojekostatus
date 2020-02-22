<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcFwDeactivationHandler Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcFwDeactivationHandler.php 156 2017-06-15 17:11:16Z NULL $
 * @since 1.4.6
 */
class gdprcFwDeactivationHandler
{
	/**
	 * The abolute gdprcookies Framework filepath
	 *
	 * @since 1.4.6
	 *
	 * @var string
	 */	
	private static $file = '';

	/**
	 * Add the hooks for deactivation
	 * 
	 * @param string $file
	 * 
	 * @uses register_deactivation_hook()
	 * 
	 * @since 1.4.6
	 */
	public static function addHooks( $file = '' )
	{
		self::$file = $file;
		register_deactivation_hook( self::$file, array( 'gdprcFwDeactivationHandler', 'deactivate' ) );
	}

	/**
	 * Callback for the activate_{$file} action hook
	 *
	 * Set/update the current gdprcookies Framework version in the wp_options table
	 *
	 * @uses delete_site_option()
	 * @uses delete_option()
	 *
	 * @since 1.1
	 * @since 1.4.6 renamed to deactivate
	 */
	public static function deactivate( $networkDeactivating )
	{
		if( $networkDeactivating ) {
			update_site_option( 'gdprcfw_active', '0' );
		} else {
			update_option( 'gdprcfw_active', '0' );
		}
	}
}