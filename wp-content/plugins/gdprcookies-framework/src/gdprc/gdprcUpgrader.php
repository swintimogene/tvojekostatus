<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcUpgrader Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcUpgrader.php 141 2017-05-08 16:02:54Z NULL $
 * @since 1.4.0
 */
final class gdprcUpgrader
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
		$this->globals = $globals;
		$this->nameSpace = $this->globals->get( 'pluginNameSpace' );
	}
	
	/**
	 * Do upgrade dependend logic
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @param 	string	$currVersionPlugin
	 * @param 	string	$newVersionPlugin
	 * @param	string	$gdprcfwVersionOld
	 * @param 	string	$versiongdprcFw
	 * @param 	bool	$networkWide
	 */
	public function doUpgradeDependendLogic( $currVersionPlugin, $newVersionPlugin, $gdprcfwVersionOld, $versiongdprcFw, $networkWide )
	{
		$pluginPath = $this->globals->get( 'pluginPath' );
		$upgradeFile = $pluginPath . '/upgrade.php';
			
		if( 0 === $currVersionPlugin ) {
			return;
		} elseif( file_exists( $upgradeFile ) ) {
			@include_once $upgradeFile;
		} else {
			return;
		}
					
		$gdprcFwIs118orLower = version_compare( $gdprcfwVersionOld, '1.1.8', '<=' );
			
		if( $gdprcFwIs118orLower ) {
			$function = sprintf( '%s_gdprcfw_upgrade_%s', $this->nameSpace, '118_and_early' );
		} else {
			$function = sprintf( '%s_gdprcfw_upgrade_%s', $this->nameSpace, str_replace( '.', '', $versiongdprcFw ) );
		}

		if( function_exists( $function ) ) {
			call_user_func( $function, $networkWide );
		}

		do_action(  $this->nameSpace . '_gdprcfw_upgrade_logic', $currVersionPlugin, $newVersionPlugin, $gdprcfwVersionOld, $versiongdprcFw, $networkWide );
	}
}