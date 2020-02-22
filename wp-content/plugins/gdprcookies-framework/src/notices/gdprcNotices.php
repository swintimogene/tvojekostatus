<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcNotices Class
 *
 * Class for handling Plugin Settings page
 *
 * @author $Author: NULL $
 * @version $Id: gdprcNotices.php 132 2017-05-03 20:07:38Z NULL $
 * @since 1.1.8
 */
final class gdprcNotices 
{		
	/**
	 * The singleton instance
	 * 
	 * @access private
	 * 
	 * @since 1.1.8
	 * 
	 * @var gdprcNotices
	 */
	private static $_instance = null;	
	
	/**
	 * Notices array
	 * 
	 * @access private
	 *   
	 * @since 1.1.8
	 * 
	 * @var array
	 */
	private static $_notices = array();	
	
	private static $_isActivating = array();
		
	/**
	 * HTML class attribute format for notices
	 *
	 * @since 1.1.8
	 *
	 * @var string
	 */
	const NOTICE_CSS_CLASS = '%s-settings-error error';	
	
	/**
	 * Constructor
	 * 
	 * @access private
	 */
	private function __construct() {}	
	
	/**
	 * Singleton implementation
	 * 
	 * @acces public
	 * 
	 * @since 1.1.8
	 * 
	 * @return gdprcNotices
	 */
	public static function getInstance()
	{
		if ( null === self::$_instance ) {
			self::$_instance = new gdprcNotices();
		}
	
		return self::$_instance;
	}	
	
	/**
	 * Init the notice instance
	 * 
	 * @acces public
	 * 
	 * @param string $ns - the plugins namespace
	 * 
	 * @uses self::_init()
	 * 
	 * @since 1.1.8
	 */
	public function init( $ns = '' ) 
	{
		$this->_init( $ns );	
	}	
	
	/**
	 * Handles the init logic
	 * 
	 * Main task is:
	 * - init array entry for the namespace
	 * - adding the WordPress admin_notices hook
	 * 
	 * @access private
	 * 
	 * @param string $ns
	 * @throws Exception
	 */
	private function _init( $ns = '' ) 
	{
		static $count = 0;
		$count++;
				
		if( '' === $ns ) {		
			if( !isset( self::$_notices['none'] ) ) {
				self::$_notices['none'] = array();
			}
		} else {
			self::$_notices[$ns] = array();
		}		

		if( 1 === $count ) {
			add_action( 'all_admin_notices', array( &$this, 'show' ), 1 );
		}		
	}	
	
	/**
	 * Add a notice
	 * 
	 * With given namespace and type, the notice message is added to the static self::$_notices array
	 * If $setting is not false, a WordPress settings error will be added 
	 * 
	 * @access public
	 * 
	 * @param string $ns
	 * @param string $message
	 * @param string $type
	 * @param string $setting
	 * 
	 * @uses add_settings_error() if $setting is not false
	 * 
	 * @since 1.1.8
	 */
	public static function add( $ns = 'none', $message, $type = 'error', $setting = false ) 
	{				
		if( false == $ns || '' === $ns ) {
			$ns = 'none';
		}
		
		if( false !== $setting ) {			
			add_settings_error( $setting, $ns.'_'.$type, $message, $type );
		} else {			
			self::$_notices[$ns][$type][] = $message;
		}						
	}	
	
	/**
	 * Callback for the admin_notices hook
	 *
	 * Add settings errors if any
	 *
	 * @access public
	 *
	 * @uses settings_errors()
	 *
	 * @since 1.1.8
	 */
	public function show()
	{		
		if( !empty( self::$_notices ) ) {
			foreach ( self::$_notices as $ns => $notices ) {				
				if( empty( $notices ) ) { 
					continue; 
				}
				
				foreach ( $notices as $type => $messages )	{
					foreach ( $messages as $k => $msg ) {
						// print notices with at least CSS class "error". WP then takes the errer element and moves it to the WP header
						printf( "<div class='".self::NOTICE_CSS_CLASS."'><p><strong>%s</strong></p></div> \n", $type, $msg );						
					}
				}
			}
		}

		// echo settings errors only if not on WP settings pages. 
		// options-head.php does also call settings_errors()
		global $parent_file;
		if ( 'options-general.php' !== $parent_file ) {
			settings_errors();		
		}
	}	
}