<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcFwActivation Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcFwActivationHandler.php 156 2017-06-15 17:11:16Z NULL $
 * @since 1.4.6
 */
class gdprcFwActivationHandler 
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
	 * Add the hooks for activation
	 * 
	 * @param string $file
	 * 
	 * @uses register_activation_hook()
	 * 
	 * @since 1.4.6
	 */
	public static function addHooks( $file = '' )
	{
		self::$file = $file;
		add_action( 'activated_plugin', array( 'gdprcFwActivationHandler', 'loadFrameworkFirst' ), 10, 2 );
		register_activation_hook( self::$file, array( 'gdprcFwActivationHandler', 'activate' ) );
	}	

	/**
	 * Callback for the activated_plugin hook
	 *
	 * Make sure the gdprcookies Framework is loaded first.
	 * Therefor the wp_options.active_plugins database field is re-ordered.
	 *
	 * @uses update_site_option() or update_option()
	 *
	 * @since 1.0
	 */
	public static function loadFrameworkFirst( $plugin, $network_wide )
	{
		// ensure path to this file is via main wp plugin path	
		$wp_path_to_this_file = preg_replace( '/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", self::$file );
		$this_plugin = plugin_basename( trim( $wp_path_to_this_file ) );
	
		if( $network_wide ) {
			$active_sidewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
	
			if( array_key_exists( $this_plugin, $active_sidewide_plugins ) ) {
				$this_plugin_key = $this_plugin;
				$this_plugin_value = $active_sidewide_plugins[$this_plugin_key];
					
				unset( $active_sidewide_plugins[$this_plugin_key] );
					
				$active_sidewide_plugins = array_merge( array( $this_plugin_key => $this_plugin_value ), $active_sidewide_plugins );
					
				update_site_option( 'active_sitewide_plugins', $active_sidewide_plugins );
			}
		} else {
			$active_plugins = get_option( 'active_plugins' );
			$this_plugin_key = array_search( $this_plugin, $active_plugins );
	
			if ( false !== $this_plugin_key ) {
				array_splice( $active_plugins, $this_plugin_key, 1 );
				array_unshift( $active_plugins, $this_plugin );				
				update_option( 'active_plugins', $active_plugins );
			}
		}
	}
	
	
	/**
	 * Callback for the activate_{$file} action hook
	 *
	 * Set/update the current gdprcookies Framework version in the wp_options table
	 *
	 * @uses update_site_option() or update_option()
	 *
	 * @since 1.0.4
	 * @since 1.4.6 renamed to activate
	 */
	public static function activate( $networkWide )
	{
		$upgrading = false;
	
		if( $networkWide ) {
			if( false != ( $currentVersion = get_site_option( 'gdprcfw_version', 0 ) ) ) {
				$upgrading = version_compare( $currentVersion, gdprcookiesFramework::VERSION, '<' );
			}
			if( $upgrading ) {
				update_site_option( 'gdprcfw_version_old', $currentVersion );
			}
	
			update_site_option( 'gdprcfw_version', gdprcookiesFramework::VERSION );
			update_site_option( 'gdprcfw_active', '1' );
			
		}	else {
			if( false != ( $currentVersion = get_option( 'gdprcfw_version', 0 ) ) ) {
				$upgrading = version_compare( $currentVersion, gdprcookiesFramework::VERSION, '<' );
			}
	
			if( $upgrading ) {
				update_option( 'gdprcfw_version_old', $currentVersion );
			}
	
			update_option( 'gdprcfw_version', gdprcookiesFramework::VERSION );
			update_option( 'gdprcfw_active', '1' );
		}
	}
}