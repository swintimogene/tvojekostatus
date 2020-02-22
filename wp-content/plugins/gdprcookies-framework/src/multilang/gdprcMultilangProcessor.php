<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcMultilangProcessor class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcMultilangProcessor.php 141 2017-05-08 16:02:54Z NULL $
 * @since 1.2
 */
final class gdprcMultilangProcessor extends gdprcBaseMultilangProcessor 
{
	/**
	 * @var bool
	 */
	private $activating = false;
	
	/**
	 * gdprcPluginGlobals instance
	 * 
	 * @since 1.4.0
	 * 
	 * @var gdprcPluginGlobals
	 */
	private $globals = false;

	/**
	 * Constructor 
	 */
	public function __construct( $activating = false, $globals ) 
	{		
		$this->globals = $globals;
		$nameSpace = $this->globals->get( 'pluginNameSpace' );
		$locale =  $this->globals->get( 'locale' );
		
		$code = gdprcWpmlHelper::mapLocaleToCode( $locale );
		parent::__construct( $nameSpace, $locale, $code );
		
		if( false !== $this->createInstance() ) {
			$this->activating = $activating;			
			$this->init();
		}		
	}
	
	
	/**
	 * Remove the 'All' language switcher
	 *
	 * @acces 	public
	 *
	 * @uses 	WP_Admin_Bar::get_nodes()
	 * @uses 	WP_Admin_Bar::remove_node()
	 * @uses 	WP_Admin_Bar::add_node()
	 *
	 * @since 	1.2
	 */
	public function modifyAdminBar()
	{
		global $wp_admin_bar;
			
		$nodes = $wp_admin_bar->get_nodes();

		if( isset( $nodes['WPML_ALS_all'] ) ) {
			$wp_admin_bar->remove_node( 'WPML_ALS_all' );
		} elseif( isset( $nodes['WPML_ALS'] ) && 'all' === $this->activeCode ) {
			$wp_admin_bar->remove_node( 'WPML_ALS' );
				
			foreach( $nodes as $k => $node ) {					
				if( false !== strpos( $k, 'WPML_ALS' ) && 'WPML_ALS' === $node->parent ) {
					$newNode = array();
					$newNode['id'] 		= $node->id;
					$newNode['title'] 	= $node->title;
					$newNode['parent']	= null;
					$newNode['href'] 	= $node->href;
					$newNode['group'] 	= $node->group;
					$newNode['meta'] 	= $node->meta;
						
					$wp_admin_bar->remove_node( $k );
					$wp_admin_bar->add_node( $newNode );
				}
			}
		}
	}	
	
	/**
	 * Callback for the hook {nameSpace}_add_admin_pages
	 *
	 * Redirect the current settings tab to the default language if 'all' is requested
	 *
	 * @access 	public
	 *
	 * @uses 	SitePress::get_default_language()
	 * @uses 	add_query_arg()
	 * @uses 	wp_redirect()
	 *
	 * @since 	1.2
	 */
	public function redirectIfIsAll()
	{
		if( 'all' === ICL_LANGUAGE_CODE ) {
			if( '' === $this->defaultCode ) {
				$this->defaultCode = gdprcWpmlHelper::getDefaultCode();
			}
	
			$uri = add_query_arg( array( 'lang' => $this->defaultCode ) );			
	
			// use esc_url_raw()
			// @see https://make.wordpress.org/plugins/2015/04/20/fixing-add_query_arg-and-remove_query_arg-usage/
			$uri = esc_url_raw( $uri );
	
			//gdprcWpmlHelper::switchLanguageToDefault()
	
			wp_redirect( $uri );exit;
		}
	}	
	
	/**
	 * Callback for the {$nameSpace}_settings_init_ready hook
	 *
	 * If on one of the settings pages, check if a the languages need to be synced
	 *
	 * @access 	public
	 * 
	 * @param 	array $settings
	 * @param 	gdprcPluginSettingsProcessor $settingsProcessor
	 *
	 * @uses	self::sync()
	 *
	 * @since 	1.2
	 */
	public function maybeSync( $settings = array(), $settingsProcessor = false )
	{
		if( $this->hasRemovedLang || $this->hasNewLang ) {
			$this->sync( $settingsProcessor );
			remove_action( $this->nameSpace . '_settings_init_ready', array( &$this, 'maybeSync' ), 1 );
		}
	}	
	
	/**
	 * Callback for the {nameSpace}_do_reset hook
	 *
	 * User did a RESET action (tools tab)
	 *
	 * @access	public
	 *
	 * @uses 	parent::deleteLangsOption()
	 * @uses	self::updateLangsOption()
	 *
	 * @since	1.2
	 */
	public function reset()
	{
		$this->deleteLangsOption();
		$this->_updateLangsOption();
	}
	
	
	/**
	 * @param gdprcPluginSettingsProcessor $settingsProcessor
	 */
	public function afterInitSettings( $settingsProcessor = false ) 
	{
		if( is_a( $settingsProcessor, 'gdprcPluginSettingsProcessor' ) ) {
			if( $settingsProcessor->hasSettings ) {
				$settingNames = $settingsProcessor->getSettingNames();
				$keysNames = array_keys( $settingNames );
				$first = array_shift( $keysNames );
				$firstSetting = $settingsProcessor->getSetting( $first )->getOption();				
				$this->isLanguaged = gdprcMultilangHelper::isLanguagedOption( $firstSetting );

				$this->gdprcLocales = $this->getLangsOption();
				if( empty( $this->gdprcLocales ) && $this->isLanguaged ) {
					$keys = array_keys( $firstSetting ); 
					if( in_array( $this->getActiveLocale(), $keys ) ) {
						$this->gdprcLocales[$this->getActiveCode()] = $this->getActiveLocale();
						$this->updateLangsOption( $this->gdprcLocales );
					}					
				}
			}
		}
	}	
	
	/**
	 * Perform hooks when on settings page
	 *
	 * @access public
	 *
	 * @since 1.2
	 */
	public function hook( $settingsProcessor = false )
	{
		if( $this->isPluginReady && is_a( $settingsProcessor, 'gdprcPluginSettingsProcessor' ) && $settingsProcessor->getSettingsPage()->onSettingsPage ) {	
			add_action( 'wp_before_admin_bar_render', array( &$this, 'modifyAdminBar' ) );
			add_action( $this->nameSpace . '_add_admin_pages', array( &$this, 'redirectIfIsAll' ) );
			add_action( $this->nameSpace . '_do_reset', array( &$this, 'reset' ) );
			add_action( $this->nameSpace . '_settings_init_ready', array( &$this, 'maybeSync' ), 1, 2 );
		}
	}
	
	
	/**
	 * Callback for {nameSpace}_force_delete_settings hook
	 * 
	 * @param	$blogId (for multisite only)	
	 * 
	 * @since	1.2
	 */
	public function forceDelete( $blogId = false )
	{
		parent::deleteLangsOption();
	}	
	

	public function init() 
	{			
		// retrieve language data from the plugin specific class
		$params = parent::getPluginInstance()->getParams();		
		
		$this->isPluginActive = $params->isActive;
		if( $this->isPluginActive ) {
			$this->isPluginReady = $params->isReady;
		}
		
		if( $this->isPluginActive && $this->isPluginReady ) {
			
			/*
			 
			 The $params parameter should be an object as follows: 
			 
				$params->isActive
				$params->isReady
				$params->defaultCode
				$params->defaultLocale
				$params->activeCode
				$params->activeLocale
				$params->allCodes
				$params->allLocales
				
			*/
			
			$this->setParamsDefault( $params->defaultCode, $params->defaultLocale );
			$this->setParamsActive( $params->activeCode, $params->activeLocale );
			$this->setParamsAll( $params->allCodes, $params->allLocales );

			// @todo: fix termonology codes vs langs
			$this->gdprcLocales = parent::getLangsOption();
			
			$this->isAll = ( 'all' === $params->activeCode ) ? true : false;
		
			if( !$this->isAll ) {
				if( !empty( $this->gdprcLocales ) ) {
					$exist = parent::getLangSyncStatusses( $this->gdprcLocales );
					
					$removed = array_keys( $exist, 2 );
					if( !empty( $removed ) ) {
						$this->removedCodes = $removed;
						$this->hasRemovedLang = true;
					}
					$new = array_keys( $exist, 0, true);
					if( !empty( $new ) ) {
						$this->newCodes = $new;
						$this->hasNewLang = true;
					}				
				}
			} elseif( $this->isAll ) {
				// if the current active language (code) is 'all',
				// set the params to the default language
				// if activating, switch the language also
				
				if( $this->activating || get_option( 'gdprc_activating_' . $this->nameSpace ) ) {
					$newCode = parent::getPluginInstance()->switchToDefault();					
				}
				
				$this->setParamsActive( $params->defaultCode, $params->defaultLocale );
			}			
		}	

		if( $this->activating ) {
			$this->activatePlugin();
		}		
		if( !$this->isPluginActive ) {
			parent::deleteLangsOption();
			return false;
		}
		
		return true;	
	}	
	
	/**
	 * @return boolean
	 */
	private function _updateLangsOption()
	{
		$locales = parent::getPluginInstance()->getLangs();			
			
		if( !is_array( $locales ) || empty( $locales ) ) {
			return false;				
		}
			
		return parent::updateLangsOption( $locales );
	}	
		
	/**
	 * Return a gdprcMultilangPlugin instance (singleton)
	 *
	 * if instance is null, create it first based on given classname
	 *
	 * @access private
	 *
	 * @since 1.2
	 *
	 * @return gdprcMultilangPlugin|boolean
	 */
	private function getInstance( $className = '' )
	{
		if ( null === self::$instance ) {
			self::$instance = new $className();
		}
	
		return self::$instance;
	}	
	
	/**
	 * Return the gdprcMultilangPlugin instance if ML plugin is active
	 *
	 * Currently the following WordPress Plugins are supported:
	 *
	 * - WPML
	 *
	 * @access private
	 *
	 * @uses self::getInstance()
	 *
	 * @since 1.2
	 *
	 * @return gdprcMultilangWpml|boolean false if ML plugin is not active
	 */
	private function createInstance()
	{
		if( gdprcWpmlHelper::isActive() ) {
			$this->pluginClassName = 'gdprcMultilangWpml';
			
			return $this->getInstance( $this->pluginClassName );			

		} else {				
			return false;
		}
	}
}