<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcShortcodes Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcShortcodes.php 141 2017-05-08 16:02:54Z NULL $
 * @since 1.4.0
 */
final class gdprcShortcodes
{
	/**
	 * The plugins namespace
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	private $nameSpace;

	/**
	 * The gdprcCore instance
	 *
	 * @since 1.4.0
	 *
	 * @var gdprcCore
	 */
	private $gdprcCore;	
	
	/**
	 * Global plugin settings
	 *
	 * @since 1.4.0
	 *
	 * @var gdprcPluginGlobals
	 */
	private $globals;
	
	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * @param gdprcPluginGlobals $globals
	 *
	 * @since 1.4.0
	 */
	public function __construct( $globals )
	{
		if( !is_a( $globals, 'gdprcPluginGlobals' ) ) {
			throw new Exception( 'Parameter globals is not valid.' );
		}

		$this->nameSpace = $globals->get( 'pluginNameSpace' );
	}	
	
	/**
	 * Callback for the registered shortcodes
	 *
	 * @access	public
	 *
	 * @param	array	$atts
	 * @param 	string 	$content
	 * @param 	string 	$tag
	 *
	 * @uses 	filter {nameSpace}_shortcode_{$tag} to perform the shortcode logic
	 *
	 * @since	2.3.x
	 *
	 * @return	string
	 */
	public function doshortcode( $atts = array(), $content = null, $tag = '' )
	{
		do_action( $this->nameSpace . '_before_shortcode_' . $tag, $atts, $content );
	
		return apply_filters( $this->nameSpace . '_shortcode_' . $tag, $atts, $content, $tag );
	}	

	/**
	 * Add schortcodes
	 * 
	 * Plugins can addd shortcodes with the hook {nameSpace}_registered_shortcodes
	 * 
	 * @access public
	 * 
	 * @uses add_shortcode()
	 */
	public function add()
	{
		/**
		 * Let plugin modules add shortcodes
		 *
		 * @param array $shortcodes
		 *
		 * @since 1.2.5
		 *
		 * @return array
		 */
		$shortcodes = apply_filters( $this->nameSpace . '_registered_shortcodes', array() );
			
		if( is_array( $shortcodes ) && 0 < count( $shortcodes ) ) {
			foreach ( $shortcodes as $sc ) {
				add_shortcode( $sc, array( &$this, 'doshortcode' ) );
			}
		}
	}	
}