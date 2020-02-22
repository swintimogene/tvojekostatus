<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcMultisiteHelper class
 * 
 * Helper class that helps with multisite operations
 *  
 * @author $Author: NULL $
 * @version $Id: gdprcMultisiteHelper.php 167 2018-02-24 23:09:06Z NULL $
 * 
 * @since 1.1
 */
class gdprcMultisiteHelper {		

	/**
	 * Get all sites in the network
	 * 
	 * @access public
	 * 
	 * @since 1.1
	 * 
	 * @return bool false on failure or if not multisite. An array with blog_id and path values otherwise
	 */
	public static function getSites()
	{
		static $sites = null;
		
		if( !self::isMs() )
			return false;
		
		if( null !== $sites )
			return $sites;		
		
		global $wpdb;
	
		$query = $wpdb->prepare( "SELECT blog_id,path FROM $wpdb->blogs
				WHERE site_id = %d
				AND spam = '0'
				AND deleted = '0'
				AND archived = '0'
				order by blog_id", $wpdb->siteid );
	
		$sites = $wpdb->get_results( $query );
	
		if(null !== $sites)
			return $sites;
		else
			return false;
	}	
	
	
	/**
	 * Get a detail for the current blog
	 * 
	 * @access public
	 * 
	 * @param string $detail
	 * 
	 * @uses get_site()
	 * @uses get_blog_details()
	 * 
	 * @since 1.1
	 * 
	 * @return bool false on failure/no multisite. Mixed the blog detail otherwise
	 */
	public static function getBlogDetail( $detail = 'blog_id' ) 
	{
		if( !self::isMs() )
			return false;
		
		// @since 1.3.2
		// Added support for new WP_Site implementation
		if( function_exists( 'get_site' ) ) {
			$details = get_site();
		} elseif( function_exists( 'get_blog_details' ) ) {
			$details = get_blog_details();
		}	else {
			return false;
		}	
		
		return ( isset( $details->{$detail} ) ) ? $details->{$detail} : false;		
	}
	
	
	/**
	 * Determine if the current blog is the network home
	 * 
	 * @access public
	 * 
	 * @uses network_home_url()
	 * @uses get_bloginfo( 'url' )
	 * 
	 * @since 1.1
	 * 
	 * @return bool true or false
	 */
	public static function isNetworkHome()
	{
		if( !self::isMs() )
			return false;
		
		if( network_home_url() === get_bloginfo( 'url' ) . '/' )
			return true;
		else
			return false;
	}
	
	
	/**
	 * Get the URI for the current page
	 *
	 * @access public
	 *
	 * @uses is_subdomain_install()
	 * @uses home_url()
	 * @uses network_home_url()
	 * @uses add_query_arg()
	 *
	 * @since 1.1.7
	 * 
	 * @return string
	 */
	public static function getCurrentUri()
	{
		if( !self::isMs() )
			return false;
	
		if( is_subdomain_install() )
			$uri = home_url( add_query_arg( NULL, NULL ) ); // subdomain
		else
			$uri = network_home_url( add_query_arg( NULL, NULL ) ); // subfolder
	
		return $uri;
	}	

	
	/**
	 * Get a network (wide) site option
	 *
	 * @access public
	 *
	 * @uses self::isMs()
	 * @uses get_option()
	 * @uses get_site_option()
	 *
	 * @since 1.2.5
	 *
	 * @return mixed
	 */	
	public static function getOption( $option, $default = null, $networkWide = false ) 
	{
		if( !self::isMs() ) {
			
			return get_option( $option, $default );
			
		} elseif( self::isMs() && !$networkWide ) {
			
			return get_option( $option, $default );
			
		} elseif( self::isMs() && $networkWide ) {
			
			return get_site_option( $option, $default );
			
		} else {
			
			return $default;
		}
	} 
	
	
	/**
	 * Update a network (wide) site option
	 *
	 * @access public
	 *
	 * @uses self::isMs()
	 * @uses update_option()
	 * @uses update_site_option()
	 *
	 * @since 1.2.5
	 *
	 * @return bool true on success or false on failure
	 */
	public static function updateOption( $option, $value = null, $networkWide = false )
	{
		if( !self::isMs() ) {
			
			return update_option( $option, $value );
			
		} elseif( self::isMs() && !$networkWide ) {
			
			return update_option( $option, $value );
			
		} elseif( self::isMs() && $networkWide ) {
			
			return update_site_option( $option, $value );
			
		} else {
			
			return false;			
		}	
	}	
	
	
	/**
	 * Determine if plugin is activated 'network wide'
	 *
	 * @access public
	 *
	 * @uses self::isMs()
	 * @uses is_plugin_active_for_network()
	 *
	 * @since 1.2.5
	 *
	 * @return bool
	 */	
	public static function isPluginNetworkWide( $pluginFile = '' )
	{
		static $nw = null;
		
		if( !self::isMs() || '' === $pluginFile )
			return false;		
		
		if( null !== $nw )
			return $nw;
				
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		
		$nw = is_plugin_active_for_network( $pluginFile );
		
		return $nw;		
	}
	
	
	/**
	 * Determine if is multisite
	 * 
	 * @access public
	 * 
	 * @uses is_multisite()
	 * 
	 * @since 1.2.5
	 * 
	 * @return bool
	 */
	public static function isMs()
	{
		static $is = null;
		
		if( null !== $is ) 
			return $is; 
		
		$is = is_multisite();
		
		return $is;
	}	
}