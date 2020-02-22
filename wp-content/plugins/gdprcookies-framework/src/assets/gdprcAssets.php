<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcAssets Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcAssets.php 167 2018-02-24 23:09:06Z NULL $
 * @since 1.4.0
 */
class gdprcAssets
{
	/**
	 * Global plugin settings
	 *
	 * @since 1.4.0
	 *
	 * @var gdprcPluginGlobals
	 */
	private $globals;

	/**
	 * The plugins namespace
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	private $nameSpace;	
	
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
	
		$this->globals = $globals;
		$this->nameSpace = $globals->get( 'pluginNameSpace' );			
	}	
	
}