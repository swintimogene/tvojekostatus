<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * Final class gdprcPluginGlobals 
 *
 * @author $Author: NULL $
 * @version $Id: gdprcPluginGlobals.php 170 2018-02-28 22:56:29Z NULL $
 * 
 * @since 0.1
 */
final class gdprcPluginGlobals extends gdprcBaseSettings {
	
	/**
	 * The Plugins namespace
	 * 
	 * @since 1.0
	 * 
	 * @var string
	 */
	private $nameSpace;
		
	/**
	 * The Plugins file
	 * 
	 * @since 1.0
	 * 
	 * @var string
	 */
	private $pluginFile;
	
	/**
	 * The gdprcookies Framework Plugins file
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	private $gdprcFwFile;	

	/**
	 * Constructor
	 * 
	 * Calls parent class gdprcBaseSettings
	 * 
	 * @access public
	 * 
	 * @param string $name
	 * @param string $nameSpace
	 * @param string $pluginFile
	 * @param string $gdprcFwFile
	 * 
	 * @since 0.1
	 */
	public function __construct( $name = '', $nameSpace = '', $pluginFile = '', $gdprcFwFile = '' )
	{
		try {		
			$this->nameSpace = $nameSpace;
			$this->pluginFile = $pluginFile;
			$this->gdprcFwFile = $gdprcFwFile;
			
			if( gdprcMiscHelper::isRunningModeDev() ) {
				$this->reset();
				// this should solve the issue users copy the db to a new domain				
			} elseif( 0 < count( $this->stack ) && false === strpos( $this->get( 'pluginUri' ) , $_SERVER['HTTP_HOST'] ) ) {
				$this->reset();
			} elseif( isset( $_GET['force_reset_globals'] ) && '1' === $_GET['force_reset_globals'] && isset( $_GET['ns'] ) && $this->nameSpace === $_GET['ns'] ) {
				$this->reset();				
			}
			
			parent::__construct( $name );
			
		} catch( Exception $e ) {
			throw $e;
		}
	}
	
	
	/* (non-PHPdoc)
	 * @see WpSettings::setDefaults()
	 * 
	 * @since 0.1
	 */
	public function setDefaults()
	{
		// gdprcookies Framework URL's and Paths
		$wfPluginFolder = trim( plugin_basename( dirname( $this->gdprcFwFile ) ), '\/' );
		$wfPluginUri = plugins_url( '/'.$wfPluginFolder );
		
		$this->set( 'wfPluginPath', WP_PLUGIN_DIR . '/'.$wfPluginFolder );
		$this->set( 'wfAssetsUri', $wfPluginUri . '/assets' );	
		$this->set( 'wfImgUri',  $this->get( 'wfAssetsUri' ) . '/img' );
		$this->set( 'wfJsUri',  $this->get( 'wfAssetsUri' ) . '/js' );
		$this->set( 'wfCssUri',  $this->get( 'wfAssetsUri' ) . '/css' );
		$this->set( 'wfTemplUri',  $wfPluginUri . '/templates' );
		$this->set( 'wfTemplPath',  $this->get( 'wfPluginPath' ) . '/templates' );
		
		$pluginBase = plugin_basename( $this->pluginFile );		
		$pluginDirName = ( preg_match( '/[^\/]+/', $pluginBase, $m ) ) ? $m[0] : '';		
		
		$this->set( 'pluginNameSpace', $this->nameSpace ); 
		$this->set( 'pluginDirName', $pluginDirName );
		$this->set( 'pluginFile', $pluginBase );
		$this->set( 'pluginFileBase', basename( $this->get( 'pluginFile' ), '.php' ) );
		
		$this->set( 'pluginUri', plugins_url( '/' . $pluginDirName ) );
		$this->set( 'pluginPath', WP_PLUGIN_DIR . '/' . $pluginDirName );
		$this->set( 'pluginPathFile', $this->pluginFile );
		$this->set( 'settingsPath',  $this->get( 'pluginPath' ) . '/settings' );
		$this->set( 'settingsUri',  $this->get( 'pluginUri' ) . '/settings' );
		$this->set( 'templatePath',  $this->get( 'pluginPath' ) . '/templates' );
		$this->set( 'assetsPath',  $this->get( 'pluginPath' ) . '/assets' );
		$this->set( 'templatePathTheme', array( get_stylesheet_directory(), get_stylesheet_directory().'/'.$this->nameSpace ) );		
		$this->set( 'modulePath',  $this->get( 'pluginPath' ) . '/modules' );
		$this->set( 'moduleUri',  $this->get( 'pluginUri' ) . '/modules' );
		$this->set( 'assetsUri',  $this->get( 'pluginUri' ) . '/assets' );
		$this->set( 'cssUri',  $this->get( 'assetsUri' ) . '/css' );
		$this->set( 'jsUri',  $this->get( 'assetsUri' ) . '/js' );
		$this->set( 'imgUri',  $this->get( 'assetsUri' ) . '/img' );
		$this->set( 'jsNamespace', $this->nameSpace.'Data' );
		$this->set( 'optionModules', $this->nameSpace . '_modules' );
		$this->set( 'optionSettings', $this->nameSpace . '_settings' );
		$this->set( 'locale', ( '' !== ( $locale = get_locale() ) ) ? $locale : 'en_US' );	
	}

	
	/**
	 * Reset the stack and db to defaults
	 * 
	 * @access public
	 * 
	 * @since 1.1.8
	 */
	public function reset() 
	{
		$this->setDefaults();
		$this->updateOption();
	}
}