<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcCore Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcCore.php 175 2018-03-07 15:21:02Z NULL $
 * @since 1.4.0
 */
class gdprcCore 
{	
	/**
	 * The unique namespace of the Plugin that is extending gdprcookies Framework
	 *
	 * @var string
	 *
	 * @since 1.0
	 */
	private $nameSpace;
	
	/**
	 * The plugins absolute file path
	 * 
	 * @var string
	 * 
	 * @since 1.4.0
	 */
	private $file;
	
	/**
	 * The gdprcookies Framework plugins absolute file path
	 * 
	 * @var string
	 * 
	 * @since 1.4.0
	 */
	private $filegdprcFw;
	
	/**
	 * Flag if plugin settings should be initiated
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	private $doSettings;
	
	/**
	 * Current version of the Plugin that is extending gdprcookies Framework
	 *
	 * @var bool|string
	 *
	 * @since 1.1
	 */
	private $version;	

	/**
	 * Activating option name
	 *
	 * @var string
	 *
	 * @since 1.4.0
	 */
	private $activatingOptionName;	
	
	/**
	 * A created nonce
	 *
	 * The nonce is printed as a JavaScript variable inside {@link gdprcHooksAdmin::printScriptsAdminHeaderVars()}
	 * Also the nonce is passed to modules with {@link gdprcPluginModuleProcessor::init()}
	 *
	 * @var string
	 *
	 * @since 1.0
	 */
	private $nonce;	
	
	/**
	 * Singleton instance for gdprcNotices
	 *
	 * @since 1.1.8
	 *
	 * @var gdprcNotices
	 */
	private $notices;	
	
	/**
	 * gdprcPluginGlobals instance
	 * 
	 * @var gdprcPluginGlobals
	 */
	private $globals;
	
	/**
	 * Instance for gdprcSrc
	 *
	 * @since 1.4.0
	 *
	 * @var gdprcSrc
	 */
	private $gdprcSrc;	
	
	/**
	 * Settings page title
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	protected $pageTitle;
	
	/**
	 * Settings page menu title
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	protected $menuTitle;
	
	/**
	 * Flag if plugin is being activated
	 *
	 * @var bool
	 *
	 * @since 1.1
	 */
	protected $activating;
	
	/**
	 * Mode running
	 * 
	 * Can be dev or prod
	 * 
	 * @since 1.4.6
	 * 
	 * @var string
	 */
	protected $mode;
	
	/**
	 * Start the engine of gdprcookies Framework
	 * 
	 * @access protected
	 * 
	 * @param string $nameSpace
	 * @param string $file
	 * @param string $version
	 * @param string $doSettings
	 * 
	 * @uses gdprcCore::init()
	 * 
	 * @since 1.4.0
	 * 
	 * @throws Exception if mandatory method input params are missing
	 */
	protected function engine( $nameSpace = '', $file = '', $filegdprcFw = '', $version = '', $doSettings = false )
	{
		try {		
			if( '' === $nameSpace || '' === $file || '' === $filegdprcFw || '' === $version ) {
				throw new Exception( __( 'gdprcookies: mandatory plugin parameter missing', 'gdprcookies' ) );				
			}
						
			// Store constuctor params
			$this->nameSpace = $nameSpace;
			$this->file = $file;
			$this->filegdprcFw = $filegdprcFw;
			$this->version = $version;
			$this->doSettings = $doSettings;
			$this->activatingOptionName = sprintf( 'gdprc_activating_%s', strtolower( $this->nameSpace ) );			
			// Flag that client initiated activating process of current Plugin
			$this->activating = gdprcMiscHelper::isActivatingPlugin( $this->file );			
			// Set global var to true indicating plugin is being activated
			if( $this->activating ) {
				$GLOBALS[strtoupper( $this->activatingOptionName  )] = true;
			}
			
			// Continue with init process
			$this->init();
		
		} catch( Exception $e ) {		
			if( is_admin() ) {				
				// Init gdprcNotices, get singleton instance
				$this->notices = gdprcNotices::getInstance();
				$this->notices->init( $nameSpace );
			
				gdprcNotices::add( $nameSpace, $e->getMessage(), 'error' );
			}
		}		
	}
	
	/**
	 * Callback for the init hook: initialize most of the Plugin
	 * 
	 * @uses	self::initgdprcfy()
	 * @uses 	self::initAssets()
	 * @uses 	self::initMultiLang()
	 * @uses	self::initSettings()
	 * @uses 	self::initSettingsPage()
	 * @uses 	self::initModules()
	 * @uses 	self::initHooks()
	 * @uses 	self::initShortcodes()
	 * 
	 * @since 1.4.0
	 */
	public function wpInit()
	{
		// create nonce for use in Ajax requests
		$this->nonce = wp_create_nonce( $this->nameSpace . '-action' );
		
		try {			
			$this->initgdprcfy();			
			
			if( !is_admin() ) {
				$this->initAssets();
			}

			$this->initMultiLang();		
			$this->initSettings();				
			$this->initSettingsPage();
			$this->initModules();		
			$this->initHooks();
			$this->initShortcodes();
						
		} catch ( Exception $e ) {
			if( is_admin() && ( '' !== ( $msg =  $e->getMessage() ) ) ) {
				gdprcNotices::add( $this->nameSpace, $msg, 'error' );
			}			
		}
	}
	
	/**
	 * Return plugin globals
	 * 
	 * @since 1.4.0
	 * 
	 * @return WpPluginGlobals
	 */
	public function getGlobals()
	{
		return $this->gdprcSrc->get( 'globals' );
	}

	/**
	 * Callback for the deactivate_{$file} action hook
	 *
	 * Main task is:
	 * - updating the 'active_gdprc_plugins' option
	 *
	 * @acces public
	 *
	 * @param bool $networkDeactivating Whether the plugin is deactivated for all sites in the network
	 *             or just the current site. Multisite only. Default is false.
	 *
	 * @since 1.1
	 */
	public function deactivatePlugin( $networkDeactivating )
	{
		$pluginFile = $this->getGlobals()->get( 'pluginFile' );
		$gdprcPlugins = gdprcMultisiteHelper::getOption( 'active_gdprc_plugins', false, $networkDeactivating );		
		
		// record active gdprcookies Plugins in the wp_options table
		if( false !== $gdprcPlugins && is_array( $gdprcPlugins ) )
		{
			$key = array_search( $pluginFile, $gdprcPlugins );
			if ( false !== $key ) {
				foreach ($gdprcPlugins as $k => $plugin) {
					if( $pluginFile === $plugin ) {
						unset($gdprcPlugins[$k]);
					}
				}
				gdprcMultisiteHelper::updateOption( 'active_gdprc_plugins', $gdprcPlugins, $networkDeactivating );
			}
		}
	}	
	
	/**
	 * Callback for the activate_{$file} action hook
	 *
	 * If the current installation is multisite, all blogs are being called with the switch_to_blog() function.
	 *
	 * @param bool $networkWide, indicates if the Plugin is being activated 'network wide'
	 *
	 * @uses self::doUpgradeDependendLogic()
	 * @uses self::initMultiLang()
	 * @uses self::initSettings()
	 * @uses self::initSettingsPage()
	 * @uses self::initModules()
	 *
	 * @since 0.1
	 */
	public function activatePlugin( $networkWide )
	{
		try {			
			$upgrading = false;
			$upgradingWf = false;
			$gdprcPlugins = array();
			$isMultisite = gdprcMultisiteHelper::isMs();
				
			if( false === gdprcMiscHelper::isFrameworkActive( $networkWide, $isMultisite ) ) {	
				throw new Exception( __( 'gdprcookies Framework is not activated, please activate the gdprcookies Framework Plugin.', 'gdprcookies' ) );	
			} else {				
				$gdprcfwVersionOld = gdprcMultisiteHelper::getOption( 'gdprcfw_version_old', false, $networkWide );
	
				// set/update the current gdprcookies Framework version number in the wp_options table
				gdprcMultisiteHelper::updateOption( 'gdprcfw_version', gdprcookiesFramework::VERSION, $networkWide );
	
				// flag if WF is being upgraded
				$upgradingWf = version_compare( $gdprcfwVersionOld, gdprcookiesFramework::VERSION, '<' );
			}

			// set plugin version info. for Multisite, store the version in the wp_sitemeta table
			if( false !== $this->version && '' !== $this->version ) {				
				$currentVersion = gdprcMultisiteHelper::getOption( $this->nameSpace . '_version', 0, $networkWide );
					
				if( false !== $currentVersion ) {
					$upgrading = version_compare( $currentVersion, $this->version, '<' );
				}
					
				gdprcMultisiteHelper::updateOption( $this->nameSpace . '_version', $this->version, $networkWide );
			}
	
			// When upgrading reset plugin globals
			if( $upgrading || $upgradingWf ) {				
				// Create gdprcUpgrader instance				
				$this->initUpgrader();
				
				if( $isMultisite ) {
					$currentBlog = get_current_blog_id();
					$sites = gdprcMultisiteHelper::getSites();
					foreach ( $sites as $site ) {
						switch_to_blog( $site->blog_id );
						$this->getGlobals()->reset();
					}
	
					// switch back to the current blog
					switch_to_blog( $currentBlog );
						
				} else {
					$this->getGlobals()->reset();
				}
					
			} elseif( false === strpos( $this->getGlobals()->get( 'pluginUri' ) , $_SERVER['HTTP_HOST'] ) ) {
				// when current globals dont match the HTTP_HOST,
				// most likely the plugin has been moved to another host
				// @todo: does this works also for MS?
				$this->getGlobals()->reset();
			}
				
			// When upgrading, do depend logic
			if( $upgrading ) {	

			}
			
			// record active gdprcookies Plugins in the wp_options table			
			$gdprcPlugins = gdprcMultisiteHelper::getOption( 'active_gdprc_plugins', array(), $networkWide );
			$gdprcPlugins[] = $this->getGlobals()->get( 'pluginFile' );
			// prevent duplicate entries
			$gdprcPlugins = array_unique( $gdprcPlugins );
				
			// update the option 'active_gdprc_plugins'			
			gdprcMultisiteHelper::updateOption( 'active_gdprc_plugins', $gdprcPlugins, $networkWide );
				
			$this->initgdprcfy( true );
			
			if( $networkWide ) {
				$currentBlog = get_current_blog_id();
				$sites = gdprcMultisiteHelper::getSites();
	
				foreach ( $sites as $site ) {
					switch_to_blog( $site->blog_id );
						
					// When upgrading, do depend logic
					if( $upgrading ) {
						$this->gdprcSrc->get( 'upgrader' )->doUpgradeDependendLogic( $currentVersion, $this->version, $gdprcfwVersionOld, gdprcookiesFramework::VERSION, $networkWide );
					}
						
					$this->initMultiLang( true );						
					$this->initSettings( true, $upgrading );						
					$this->initSettingsPage();						
					// init Plugin modules and reset the wp_options option
					$this->initModules( true, true, $upgrading );
						
					/**
					 * Let Plugin modules hook into this process
					 *
					 * @param bool $upgrading, true if plugin is being upgraded, false otherwise
					 *
					 * @since 1.0
					 */
					do_action( $this->nameSpace . '_activate_plugin', $upgrading );
	
					// remove all attached action to ensure the callbacks are only called ones
					remove_all_actions( $this->nameSpace . '_activate_plugin' );
				}
	
				// switch back to the current blog
				switch_to_blog( $currentBlog );
	
			} else {
	
				// When upgrading, do depend logic
				if( $upgrading ) {
					$this->gdprcSrc->get( 'upgrader' )->doUpgradeDependendLogic( $currentVersion, $this->version, $gdprcfwVersionOld, gdprcookiesFramework::VERSION, $networkWide );
				}
	
				$this->initMultiLang( true );	
				$this->initSettings( true, $upgrading );					
				$this->initSettingsPage();	
				// init Plugin modules and reset the wp_options option
				$this->initModules( true, true, $upgrading );
					
				// see documentation above
				do_action( $this->nameSpace . '_activate_plugin', $upgrading );
	
				add_option( $this->activatingOptionName, true );
			}
				
			// set global var to false indicating plugin is being activated
			$GLOBALS[sprintf( 'gdprc_ACTIVATING_%s', strtoupper( $this->nameSpace ) )] = false;
				
		} catch( gdprcException $e ) {
			
		} catch ( Exception $e ) {
	
			$msg = sprintf(
					__( 'gdprcookies Framework could not activate the plugin:%sReturn to the <a href="%s">plugins page</a>.', 'gdprcookies' ),
					'<br/><br/><strong>' . $e->getMessage() . '</strong><br/><br/>',
					admin_url( 'plugins.php' ) );
				
			wp_die( $msg, 'gdprcookies Framework error' );
		}
	}
	
	/**
	 * gdprcookiesFy this install
	 *
	 * @access private
	 *
	 * @param bool $activating
	 *
	 * @uses gdprcFy::isgdprcfied()
	 *
	 * @since 1.2.3
	 */
	private function initgdprcfy( $activating = false )
	{
		try {
			$this->gdprcSrc->create( 
					'gdprcFy',
					array( $activating ),
					'',
					false
					);
			
			if( false === $this->gdprcSrc->get( 'fy' )->isgdprcfied() ) {
				if( $activating ) {
					throw new gdprcException( '' );
				} else {
					throw new Exception( '' );
				}				
			}							
		} catch( Exception $e ) {				
			throw $e;
		}
	}	
	
	/**
	 * Initialize multi language setups
	 *
	 * @access private
	 *
	 * @uses gdprcMultiLangProcessor class
	 *
	 * @todo add support for more multi language plugins
	 *
	 * Support now:
	 *
	 *  - WPML
	 *
	 * @since 1.2
	 */
	private function initMultiLang( $activating = false )
	{
		try {	
			$multilang = $this->gdprcSrc->create(
					'gdprcMultiLangProcessor',
					array( $activating )				
					);			
	
			add_action( $this->nameSpace . '_after_init_settings', array( &$multilang, 'afterInitSettings' ) );
			add_action( $this->nameSpace . '_before_init_modules', array( &$multilang, 'hook' ) );
			add_action( $this->nameSpace . '_force_delete_settings', array( &$multilang, 'forceDelete' ) );
				
		} catch( Exception $e ) {				
			throw $e;
		}
	}	
	
	/**
	 * Initialize the gdprcookies Plugin settings
	 *
	 * The gdprcPluginSettingsProcessor instance search for xml settings included in the plugin 'settings' folder
	 *
	 * @access private
	 * 
	 * @param bool $activating
	 * @param bool $upgrading
	 *
	 * @uses gdprcPluginSettingsProcessor class
	 *
	 * @since 1.2
	 */
	private function initSettings( $activating = false, $upgrading = false )
	{
		if( !$this->doSettings ) {
			return;
		}
	
		try {	
			$path = $this->getGlobals()->get( 'settingsPath' );
			$uri = $this->getGlobals()->get( 'settingsUri' );
			$settings = $this->getGlobals()->get( 'optionSettings' );
			$multilang = $this->gdprcSrc->get( 'multilangprocessor' );
		
			$settingsProcessor = $this->gdprcSrc->create(
					'gdprcPluginSettingsProcessor',
					array( $settings, $path, $uri, '.xml', $multilang->getActiveLocale(), $multilang->getLocales() )
					);
				
			// @todo review this step regards optimization for running dev mode
			if( $settingsProcessor->resetting ) {	
				$settingsProcessor->reset();
			} elseif( $upgrading ) {
				$settingsProcessor->upgrade();
			}

			$settingsProcessor->find();	
			$settingsProcessor->init( 
					array( '_nonce' => $this->nonce ), 
					$activating
					);

			/**
			 * @since
			 */
			do_action( $this->nameSpace . '_after_init_settings', $settingsProcessor );

			add_action( $this->nameSpace . '_after_init_modules', array( &$settingsProcessor, 'hook' ), 1, 2 );
				
		} catch ( Exception $e ) {	
			$this->doSettings = false;	
			throw $e;
		}
	}	
	
	/**
	 * Initialize the gdprcookies Plugin settings page
	 *
	 * @access private
	 *
	 * @uses gdprcPluginSettingsPage class
	 *
	 * @since 1.2
	 */
	private function initSettingsPage()
	{
		try {			
			$settingsProcessor = $this->gdprcSrc->get( 'settingsprocessor', false );
			
			if( $this->doSettings && false !== $settingsProcessor && $settingsProcessor->hasSettings ) {
				
				$settingsPage = $this->gdprcSrc->create(
						'gdprcPluginSettingsPage',
						array( $this->pageTitle, $this->menuTitle )
						);				
				
				$settingsProcessor->setSettingsPage( $settingsPage );
				$settingsProcessor = $this->gdprcSrc->set( 'settingsProcessor', $settingsProcessor, true );
					
				// pass $settings not directly to prevent PHP ERROR:
				// Strict Standards: Only variables should be passed by reference in
				$settings = $settingsProcessor->getSettings( $this->nameSpace );				
				$settingsPage->init( $settings );
					
				add_action( $this->nameSpace . '_before_init_modules', array( &$settingsPage, 'hook' ), 2, 0 );
	
				// @todo check tabs else set false
				$settingsProcessor->setHasSettingsPage( true );
			}	
		} catch( Exception $e ) {	
			throw $e;				
		}
	}	
	
	/**
	 * Initialize gdprcookies Framework Plugin modules
	 *
	 * An instance of class gdprcPluginModuleProcessor is created. 
	 * This instance search, inits and hooks the modules to the Plugin
	 *
	 * Plugins that extend the gdprcookies Framework can place Modules in folder '[YOUR_PLUGIN_ROOT]/modules'.
	 *
	 * @access private
	 *
	 * @param bool $reset flag to delete the database option first
	 *
	 * @uses gdprcPluginModuleProcessor::deleteModulesOption()
	 * @uses gdprcPluginModuleProcessor::findModules()
	 * @uses gdprcPluginModuleProcessor::includeModule()
	 * @uses gdprcPluginModuleProcessor::init()
	 * @uses gdprcPluginModuleProcessor::hook()
	 *
	 * @since 0.1
	 */
	private function initModules( $reset = false, $activating = false, $upgrading = false )
	{
		try {				
			$path = $this->getGlobals()->get( 'modulePath' );
			$uri = $this->getGlobals()->get( 'moduleUri' );
			$modules = $this->getGlobals()->get( 'optionModules' );
			
			$settingsProcessor = $this->gdprcSrc->get( 'settingsprocessor', false );
			$multilang = $this->gdprcSrc->get( 'multilangprocessor' );
			
			$moduleProcessor = $this->gdprcSrc->create(
					'gdprcPluginModuleProcessor',
					array( $modules, $path, $uri, '.php' )		
					);			
	
			if( $reset || $activating || $upgrading ) {
				$moduleProcessor->deleteModulesOption();
			}
	
			$moduleProcessor->findModules();
			$moduleProcessor->includeModules();
			$moduleProcessor->init( array( '_nonce' => $this->nonce ), $activating, $upgrading, $multilang, $settingsProcessor );
	
		} catch ( Exception $e ) {	
			throw $e;
		}
			
		do_action( $this->nameSpace . '_before_init_modules', $settingsProcessor, $multilang->getActiveLocale(), $multilang->getLocales() );
	
		$moduleProcessor->hook( $activating );
	
		do_action( $this->nameSpace . '_after_init_modules', $multilang->getActiveLocale(), $multilang->getLocales() );
	}	
	
	/**
	 * Init gdprcSrc instance 
	 * 
	 * @throws Exception
	 * 
	 * @since 1.4.0
	 */
	private function initgdprcSrc()
	{
		try {
			$this->gdprcSrc = new gdprcSrc( $this->nameSpace, $this->globals );
		} catch ( Exception $e ) {
			throw $e;
		}
	}
	
	/**
	 * Init gdprcNotices instance
	 * 
	 * @access private
	 * 
	 * @uses gdprcNotices
	 * @uses gdprcNotices::init()
	 * 
	 * @since 1.4.0
	 */
	private function initNotices()
	{
		try {
			$this->notices = gdprcNotices::getInstance();
			$this->notices->init( $this->nameSpace );	
		} catch ( Exception $e ) {
			throw $e;
		}
	}	
	
	/**
	 * Init Plugin global settings
	 * 
	 * @access private
	 * 
	 * @uses gdprcPluginGlobals
	 * 
	 * @since 1.4.0
	 */
	private function initPluginGlobals() 
	{
		try {
			$name = $this->nameSpace.'_globals';			
			$this->globals = new gdprcPluginGlobals( $name, $this->nameSpace, $this->file, $this->filegdprcFw );
		} catch ( Exception $e ) {
			throw $e;
		}
	}
	
	/**
	 * Init the gdprcHooks instance.
	 * 
	 * The add method will setup all hooks
	 * 
	 * @acces private
	 * 
	 * @uses gdprcHooks
	 * @uses gdprcHooks::add()
	 * 
	 * @since 1.4.0  
	 */
	private function initHooks()
	{
		try {		
			if( is_admin() ) {
				$hooks = $this->gdprcSrc->create(
						'gdprcHooksAdmin',
						array( $this->nonce, $this->activatingOptionName, $this->doSettings, $this->gdprcSrc->get( 'settingsprocessor', false ) )						
						);				
			} else {
				$hooks = $this->gdprcSrc->create(
						'gdprcHooksFrontend',
						array( $this->gdprcSrc->get( 'assets' ) )
						);			
			}
			
			// Add the hooks for admin or frontend
			$hooks->add();	
			
			if( is_admin() ) {
				/**
				 * Let others hook after admin hooks are added
				 *
				 * @since
				 */
				do_action( $this->nameSpace . '_init_admin_only_hooks' );
				
			} elseif( !is_admin() && $hooks->doFrontend ) {
				/**
				 * Let others hook after frontend hooks are added
				 *
				 * @since
				 */
				do_action( $this->nameSpace . '_init_frontend_only_hooks' );
			}
			
			/**
			 * Let others hook after hooks are added
			 *
			 * @since
			 */			
			do_action( $this->nameSpace . '_init_hooks' );
			
		} catch ( Exception $e ) {
			throw $e;
		}		
	}	

	/**
	 * Init shortcodes for modules
	 *
	 * @acces private
	 *
	 * @uses add_shortcode()
	 *
	 * @since 1.2.5
	 */
	private function initShortcodes()
	{
		try {
			$this->gdprcSrc->create(
					'gdprcShortcodes'
					)
				->add();
		} catch ( Exception $e ) {
			throw $e;
		}
	}
	
	/**
	 * Init asset logic
	 * 
	 * @acces private
	 * 
	 * @since 1.4.0
	 */
	private function initAssets() 
	{
		try {
			$this->gdprcSrc->create(
					'gdprcAssets',
					array(),
					'',
					false
					);			
		} catch ( Exception $e ) {
			throw $e;
		}		
	}	
	
	/**
	 * Init upgrader logic
	 * 
	 * @acces private
	 * 
	 * @throws Exception
	 * 
	 * @since 1.4.0
	 * 
	 * @return object
	 */
	private function initUpgrader()
	{
		try {
			$this->gdprcSrc->create(
					'gdprcUpgrader',
					array(),
					'',
					false
					);
		} catch ( Exception $e ) {
			throw $e;
		}		
	}	

	/**
	 * Register activation and deactivation hooks
	 *
	 * @acces private
	 *
	 * @uses register_deactivation_hook()
	 *
	 * @since 1.4.0
	 */
	private function registerActivationHooks()
	{
		register_deactivation_hook( $this->file, array( &$this, 'deactivatePlugin' ) );
	
		// To allow prio 11, use add_action instead of register_activation_hook()
		add_action( 'activate_' . $this->getGlobals()->get( 'pluginFile' ), array( &$this, 'activatePlugin' ) , 11 );
	}	

	/**
	 * Init the plugin starting logic
	 *
	 * Main tasks:
	 *
	 * * Creating a nonce with {@link wp_create_nonce()}
	 * * Loading Plugin translated texts with {@link load_plugin_textdomain()}
	 *
	 * @access private
	 */
	private function init()
	{
		try {				
			// Save globals with Plugin namespace to prevent overwriting
			$this->initPluginGlobals();
			
			// Init most of the gdprcookies Source (/src) classes
			$this->initgdprcSrc();
			
			// Init notices
			$this->initNotices();
				
			/**
			 * Allow others to apply logic at the beginning of this method
			 *
			 * Pay attention with code hooking into this action!
			 *
			 * @since 1.2.3
			 */
			do_action( $this->nameSpace . '_before_start' );		
	
			// Register the activation and deactivation hooks
			$this->registerActivationHooks();
				
			// Prevent plugins from executing during deactivating
			if( gdprcMiscHelper::isDeactivating() ) {
				return;
			}
				
			// Stop here for some Ajax requests
			if( gdprcAjaxHelper::maybeQuitForAjax() ) {
				return;
			}
				
			// Maybe set the gdprcookies Ajax global
			gdprcAjaxHelper::maybeSetgdprcAjaxGlobal();
				
			// Load the plugin text domain
			gdprcMultilangHelper::loadPluginTextDomain( $this->nameSpace, $this->getGlobals()->get( 'pluginDirName' ) . '/lang' );
				
			// Only init the Plugin if not activating
			if( false === $this->activating ) {
				add_action( 'init', array( &$this, 'wpInit' ), 2 );				
				define( sprintf( 'gdprc_RUNNING_%s', strtoupper( $this->nameSpace ) ), true );
			}
		} catch( Exception $e ) {
			throw $e;
		}
	}	
}