<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcCookieHelper class
 *
 * Helper class that helps with handing cookies
 *
 * @author $Author: NULL $
 * @version $Id: gdprcCookieHelper.php 167 2018-02-24 23:09:06Z NULL $
 *
 * @since 1.3
 */
class gdprcCookieHelper {

	/**
	 * Get an entry from the $_COOKIE super global
	 *
	 * @acces public
	 *
	 * @param string $name
	 *
	 * @since 1.3
	 *
	 * @return bool false if entry does not exist, the value otherwise
	 */
	public static function read( $name = '' )
	{
		if( isset( $_COOKIE[$name] ) )
			return $_COOKIE[$name];
		else
			return false;
	}
	
	
	/**
	 * Set a new Cookie header
	 *
	 * @acces public
	 *
	 * @param string 	$name
	 * @param string 	$value
	 * @param int 		$days
	 * @param bool 		$superGlobal wether to also store the Cookie in super global $_COOKIE
	 * @param string 	$path
	 * @param string 	$domain
	 * @param bool 		$secure
	 * @param bool 		$httponly
	 *
	 * @uses gdprcCookieHelper::setGlobal()
	 * @uses is_multisite()
	 * @uses gdprcMultisiteHelper::getBlogDetail()
	 * @uses setcookie()
	 *
	 * @link http://www.php.net/manual/en/function.setcookie.php
	 *
	 * @since 1.3
	 *
	 * @return bool
	 */
	public static function set( $name = '', $value = '', $days = 1, $superGlobal = false, $path = null, $domain = null, $secure = null, $httponly = null )
	{
		if( $superGlobal )
			self::setGlobal( $name, $value );
			
		$expire = ( -1 === $days ) ? time() - 3600 : time() + ( 86400 * $days );
	
		$hasPath = ( null !== $path );
		$hasDomain = ( null !== $domain );
		
		if( !$hasPath ) {
			$path = '/';
		}
		
		if( !$hasDomain ) {
			$path = '';
		}
		
		$path = apply_filters( 'gdprc_add_cookie_param_path' , $path );
			
		return setcookie( $name, $value, $expire, $path, $domain, $secure, $httponly );
	}	
	
	/**
	 * Set an entry in de $_COOKIE super global
	 *
	 * @acces public
	 *
	 * @param string $name Cookie name
	 * @param string $value Cookie value
	 *
	 * @since 1.3
	 *
	 * @return bool true is entry isset, false otherwise
	 */
	public static function setGlobal( $name = '', $value = '' )
	{
		if( '' === $name )
			return false;
	
		$_COOKIE[$name] = $value;
			
		return ( array_key_exists( $name, $_COOKIE ) );
	}	
	
	/**
	 * Unset an entry in the $_COOKIE array
	 *
	 * @acces public
	 *
	 * @param string $name the index in the $_COOKIE array
	 *
	 * @since 1.3
	 *
	 * @return bool false if $name is empty or true after unsetting the entry in the $_COOKIE array
	 */
	public static function deleteGlobal( $name = '' )
	{
		if( '' === $name )
			return false;
	
		$_COOKIE[$name] = '';
		$_COOKIE = array_filter( $_COOKIE );
			
		return ( !array_key_exists( $name, $_COOKIE ) );
	}
	
	
	
	/**
	 * Unset a Cookie header
	 *
	 * Unset a Cookie by settings the expiretime to -1 hour in the past with the setcookie() function
	 *
	 * @acces public
	 *
	 * @uses is_multisite()
	 * @uses gdprcMultisiteHelper::getBlogDetail()
	 * @uses wpcgCore::set()
	 *
	 * @param string $name
	 * @param bool $superGlobal
	 * @param string $path
	 * @param string $domain
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public static function delete( $name = '', $superGlobal = false, $path = '/', $domain = '' )
	{
		if( '' === $name )
			return false;
			
		return self::set( $name, '', -1, $superGlobal, $path, $domain );
	}
	
	
	/**
	 * Unset multiple cookies headers
	 *
	 * @acces public
	 *
	 * @param array $names
	 * @param bool $superGlobal
	 * @param string $path
	 *
	 * @uses wpcgCore::delete()
	 *
	 * @since 1.3
	 *
	 * @return bool false when $names is not an array, otherwise an array with info (bool true or false) about the deleted cookies
	 */
	public static function deleteMultiple( $names = array(), $superGlobal = false, $path = '/', $domain = '' )
	{
		if( !is_array( $names ) )
			return false;
			
		$deleted = array();
			
		foreach ( $names as $name ) {
	
			$deleted[$name] = self::delete( $name, $superGlobal, $path, $domain );
		}
	
		return $deleted;
	}
}