<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcAjaxHelper class
 *
 * Helper class for AJAX processes
 *
 * @author $Author: NULL $
 * @version $Id: gdprcAjaxHelper.php 141 2017-05-08 16:02:54Z NULL $
 * @since 1.0
 */
class gdprcAjaxHelper 
{	
	/**
	 * String for the global param
	 * 
	 * @var string
	 */
	const gdprc_DOING_AJAX_STR = 'gdprc_DOING_AJAX';
	
	/**
	 * Validate ajax return data
	 * 
	 * Data is valid to return when no errors, false values, empty values etc are found  
	 * 
	 * @access public 
	 * 
	 * @param mixed $data
	 * @param bool 	$allowFalse, if set to true, $data can be bool FALSE
	 * 
	 * @since 1.0
	 * 
	 * @return string '1' if valid else '0'
	 */
	public static function isValidData( $data = false, $allowFalse = false )
	{
		if( $allowFalse )
			return ( self::hasWpError( $data ) || empty( $data ) ) ? '0' : '1';
		else 
			return ( !$data || self::hasWpError( $data ) || empty( $data ) || ( is_array( $data ) && in_array( false, $data, true ) ) ) ? '0' : '1';
	}
	
	/**
	 * Determine if ajax return data contains a WP_Error object
	 * 
	 * @access public
	 * 
	 * @param mixed $data
	 * 
	 * @uses WP_Error::is_wp_error()
	 * 
	 * @since 1.0.8
	 * 
	 * @return bool true or false
	 */
	public static function hasWpError( $data )
	{
		if( is_wp_error( $data ) ) {
			return true;
		}
		elseif( is_array( $data ) ) {
			
			foreach ( $data as $entry ) {

				if( is_wp_error( $entry ) )
					return true;				
			}			
		}		
	}	
	
	/**
	 * Retrieve WP_Error messages found in ajax return data
	 * 
	 * @access public
	 * 
	 * @param mixed $data
	 * 
	 * @uses WP_Error::is_wp_error()
	 * @uses WP_Error::get_error_messages()
	 * 
	 * @since 1.0.8
	 * 
	 * @return array empty if no errors are found, otherwise array
	 */
	public static function getWpErrors( $data ) 
	{		
		$msg = array();
		
		if( is_wp_error( $data ) ) {
			$msg = $data->get_error_messages();
		}
		elseif( is_array( $data ) ) {
				
			foreach ( $data as $entry ) {
		
				if( is_wp_error( $entry ) ) {
					$msg = $entry->get_error_messages();
					break;
				}
			}
		}		

		return $msg;
	}	
	
	/**
	 * Check if gdprc_BYPASS_AJAX_CHECK is defined
	 * 
	 * @since 1.4.0
	 * 
	 * @return boolean
	 */
	public static function needBypass() 
	{
		return ( defined( 'gdprc_BYPASS_AJAX_CHECK' ) && true === gdprc_BYPASS_AJAX_CHECK );	
	}
	
	/**
	 * Check if DOING_AJAX is defined
	 * 
	 * @since 1.4.0
	 * 
	 * @return boolean
	 */
	public static function doingAjax()
	{
		return ( defined( 'DOING_AJAX' ) && true === DOING_AJAX );		
	}
	
	/**
	 * Check if doing Heartbeat
	 * 
	 * @since 1.4.0
	 * 
	 * @return boolean
	 */
	public static function doingHeartbeat()
	{
		if( self::doingAjax() && 'heartbeat' === $_REQUEST['action'] ) {
			return true;
		} else {
			return false;
		}		
	}
	
	/**
	 * Check if doing AJAX but no gdprcookies Ajax Action
	 *
	 * @since 1.4.0
	 *
	 * @return boolean
	 */
	public static function doingWithoutgdprcAction()
	{
		if( self::doingAjax() && 'gdprc-action' !== $_REQUEST['action'] ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Check if doing gdprcookies Ajax Action
	 *
	 * @since 1.4.0
	 *
	 * @return boolean
	 */
	public static function doinggdprcAction()
	{
		if( self::doingAjax() && 'gdprc-action' === $_REQUEST['action'] ) {
			return true;
		} else {
			return false;
		}
	}	
	
	/**
	 * If not yet done, define doing gdprcookies Ajax
	 * 
	 * @since 1.4.0
	 */
	public static function maybeSetgdprcAjaxGlobal()
	{
		if( self::doinggdprcAction() && !defined( self::gdprc_DOING_AJAX_STR ) ) {
			define( self::gdprc_DOING_AJAX_STR, true );
		}		
	}	

	/**
	 * Determine if gdprcookies Framework should not continue in the current AJAX request
	 *
	 * @access public
	 *
	 * @uses self::needBypass()
	 * @uses self::doingHeartbeat()
	 * @uses self::doingWithoutgdprcAction()
	 * @uses self::doinggdprcAction()
	 * @uses self::maybeSetgdprcAjaxGlobal()
	 *
	 * @since 1.4.0
	 */
	public static function maybeQuitForAjax()
	{
		if( !self::needBypass() ) {
			// Prevent plugins from executing during WP heartbeat AJAX calls
			if( self::doingHeartbeat() ) {
				return true;
			} elseif( self::doingWithoutgdprcAction() ) {
				// only allow gdprcookies Framework AJAX calls
				return true;
			}
		}
	
		return false;
	}
}