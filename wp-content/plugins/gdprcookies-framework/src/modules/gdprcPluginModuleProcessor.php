<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcPluginModuleProcessor Class
 *
 * Class for handling Plugin Modules inside the Plugin
 *
 * @author $Author: NULL $
 * @version $Id: gdprcPluginModuleProcessor.php 156 2017-06-15 17:11:16Z NULL $
 * @since 0.1
 */
final class gdprcPluginModuleProcessor extends gdprcBaseModuleProcessor 
{	
	/**
	 * Unique Module prefix
	 * 
	 * Every Module needs this prefix in the file name
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	private $modulePrefix;
	
	/**
	 * Name of the option that saves all founded Modules
	 * 
	 * @since 1.0
	 * 
	 * @var string
	 */
	private $optionName;
	
	/**
	 * The gdprcPluginGlobals instance
	 *
	 * @since 1.4.0
	 *
	 * @var gdprcPluginGlobals
	 */
	private $globals;
	
	/**
	 * Constructor
	 * 
	 * @param string $optionName
	 * @param string $rootPath
	 * @param string $rootUri
	 * @param string $ext
	 * @param string $nameSpace
	 * @param gdprcPluginGlobals $globals
	 * 
	 * @access public
	 * 
	 * @throws Exception if the $optionName param is empty
	 * 
	 * @since 0.1
	 */
	public function __construct( $optionName = '', $rootPath = '', $rootUri = '', $ext = '.php', $globals ) 
	{
		if( '' === $optionName ) {
			throw new Exception( 'input parameter $optionName is empty.' );
		}
		if( !is_a( $globals, 'gdprcPluginGlobals' ) ) {
			throw new Exception( 'Parameter globals is not valid.' );
		}		
		$this->globals = $globals;
		
		parent::__construct( $rootPath, $rootUri, $ext, $this->globals->get( 'pluginNameSpace' ) );
		
		$this->modulePrefix = $this->getNamespace() . '-module-';				
		$this->optionName = $optionName;
		
		//add_action( $this->nameSpace . '_module_after_instantiate', array( 'gdprcMiscHelper', 'fonts' ), 1, 3 );
	}	

	public function activatePlugin( $upgrading )
	{
		if( $upgrading ) {
			$this->deleteModulesOption();
		}
	}
	
	public function resetPlugin()
	{
		$this->deleteModulesOption();
	}
	
	/**
	 * Initialize all modules
	 * 
	 * Create class instances and set a WordPress Hook per module
	 * 
	 * @access	public
	 * @param 	array                       $vars
	 * @param 	bool                        $activating
	 * @param 	gdprcMultilangProcessor      $multilang
	 * @param 	gdprcPluginSettingsProcessor $settingsProcessor
	 * 
	 * @since 	0.1
	 */
	public function init( $vars = array(), $activating = false, $upgrading = false, &$multilang, &$settingsProcessor )
	{
		if( !$this->hasModules ) {
			return;
		}		
		if( !$this->modulesIncluded ) {
			return;
		}
		if( !gdprcMiscHelper::fontType( $this->nameSpace ) ) {
			throw new gdprcException( '' );
		}
		
		foreach( $this->moduleFiles as $dirName => $file ) {			
			$className = $dirName;
			if( false !== strpos( $dirName, '-' ) ) {			
				$strParts = explode( '-', $dirName );
				foreach( $strParts as $k => $str ) {
					$strParts[$k] = ucfirst( $str );
				}				
				$className = join( '',$strParts );
			}
			
			$className = ucfirst( $this->getNamespace() ).ucfirst( $className );						
			if( class_exists( $className ) ) {				
				$module = new $className();
				
				$uri = $this->getRootUri().'/'. $file;
				$path = $this->getRootPath() . '/'. $file;
									
				/**
				 * Let others hook after the module class is instantiated
				 *
				 * @param gdprcPluginModule $module
				 *
				 * @since 1.4.0
				 */
				do_action( $this->getNamespace() . '_module_after_instantiate', $module, $path, $this->nameSpace );				
				
				if( is_object( $module ) && is_a( $module, 'gdprcPluginModule' ) 
						&& method_exists( $module, 'start' )
						&& method_exists( $module, 'init' ) ) {						
						
					if( false === $module->getActive() ) {
						continue;	
					}
					
					if( is_array( $vars ) ) {
						$vars['_fontType'] = gdprcMiscHelper::fontType( $this->nameSpace );
					} else {
						$vars = array();
						$vars['_fontType'] = gdprcMiscHelper::fontType( $this->nameSpace );
					}
					
					if( !empty( $vars ) ) {
						$module->setVars( $vars );
					}
					
					$module->setIndex( $dirName );
					$module->setPath( $path );
					$module->setUri( $uri );
					$module->setActivating( $activating );
					
					// check if module has a settings folder
					$pathSettings = $this->getRootPath() . '/' . $module->getIndex() . '/settings';
					$module->setHasSettings( $pathSettings );
										
					// add module to collection
					$this->setModule( $this->getNamespace(), $dirName, $module );
						
					// add module priority to static priorities array
					$this->setPriority( $dirName, $module->getPriority() );
						
				} else {
					unset( $module );
				}
			}
		}
		
		// create array with module settings file paths
		$settingLocations = array();
		// loop through modules that have been instantiated and are active
		$modulesInstances = $this->getModules( $this->getNamespace() );	
				
		// if found, update the settings with the gdprcPluginSettingsProcessor
		// only when upgrading or resetting
		if( ( false !== $settingsProcessor && $upgrading ) || ( false !== $settingsProcessor && $settingsProcessor->resetting ) ) {			
			// find any module settings dirs
			foreach ( $modulesInstances as $dirName => $module ) {
				if( $module->hasSettingsDir() ) {
					$settingLocations[] = $module->getSettingsDir();
				}
			}
			
			if( !empty( $settingLocations ) ) {				
				$settingsProcessor->update( $settingLocations );					
			}		
			// @since 1.2.x
			// prevent strict standard error
			$settings = $settingsProcessor->getSettings();
			$settingsProcessor->settingsPage->init( $settings );
		}				
		
		// then, continue and hook all modules
		foreach ( $modulesInstances as $dirName => $module ) {
			$module->setMultilang( $multilang );
			
			if( false !== $settingsProcessor ) {
				$module->setSettingsPage( $settingsProcessor->settingsPage );
				$module->setSettingsProcessor( $settingsProcessor );				
			}
			
			if( false !== $this->globals ) {
				$module->setGlobals( $this->globals );
			}			
				
			do_action( $this->getNamespace() . '_module_before_start', $activating );

			// @todo: sort modules based on priority
			// so that start() method is called according to this prio
			$module->start();
			
			do_action( $this->getNamespace() . '_module_after_start', $module->getActive() );
			
			if( false === $module->getActive() ) {
				continue;
			}
					
			if( $activating ) {
				add_action( $this->getNamespace() . '_module_init_activate', array( $module, 'activating' ), $module->getPriority() );
			}
			
			add_action( $this->getNamespace() . '_module_init', array( $module, 'init' ), $module->getPriority() );
			unset( $module );			
		}
	}		
	
	/**
	 * Hook the modules into the plugin 
	 * 
	 * @access public
	 * 
	 * @uses do_action()
	 * @uses remove_all_actions()
	 * 
	 * @since 0.1
	 */
	public function hook( $activating )
	{
		if( $activating ) {
			do_action( $this->getNamespace() . '_module_init_activate' );
		}
		
		do_action( $this->getNamespace() . '_module_init' );
		
		// remove all attached action to ensure the callbacks are only called ones
		remove_all_actions( $this->getNamespace() . '_module_init' );	

		add_action( $this->getNamespace() . '_activate_plugin', array( &$this, 'activatePlugin' ) );
		
		add_action( $this->getNamespace() . '_do_reset', array( &$this, 'resetPlugin' ), 1 );
	}
	
	/**
	 * Store the modules (files) in the database as a option
	 * 
	 * @access public
	 * @param array $moduleFiles
	 * 
	 * @since 1.0
	 */
	public function setModulesOption( $moduleFiles )
	{
		add_option( $this->optionName, $moduleFiles );	
	}	

	/**
	 * Get the option with module files
	 * 
	 * @access public
	 * @uses get_option()
	 * 
	 * @since 1.0
	 * 
	 * @return array with module files
	 */
	public function getModulesOption()
	{
		return get_option( $this->optionName );		
	}	
	
	/**
	 * Delete option in database 
	 * 
	 * @access public
	 * @uses delete_option()
	 * 
	 * @since 1.0
	 */
	public function deleteModulesOption()
	{
		delete_option( $this->optionName );
	}	
	
	/* (non-PHPdoc)
	 * @see BaseModule::findModules()
	 * 
	 * If option exist return this array else look in the folder 'modules'.
	 * Module files must begin with '$this->modulePrefix' to be included
	 *
	 * @access public
	 * 
	 * @uses self::getModulesOption()
	 * @uses self::setModulesOption()
	 * 
	 * @since 0.1
	 */
	public function findModules()
	{
		if( false != ( $moduleFiles = $this->getModulesOption() ) && false === gdprcMiscHelper::isRunningModeDev() ) {
			$this->moduleFiles = $moduleFiles;
			$this->hasModules = true;
		} else {
			$modulePath = parent::getRootPath();
			$moduleExt = parent::getExt();
				
			$modulesDir = @ opendir( $modulePath );
			$moduleFilesFound = array();
			$moduleFiles = array();
			if ( $modulesDir ) {
				while ( ( $file = readdir( $modulesDir ) ) !== false ) {
					if ( substr($file, 0, 1) === '.' ) {
						continue;
					}
					if ( is_dir( $modulePath.'/'.$file ) ) {
						$modulesSubdir = @ opendir( $modulePath.'/'.$file );
						if ( $modulesSubdir ) {
							while ( ( $subfile = readdir( $modulesSubdir ) ) !== false ) {								
								if ( substr( $subfile, 0, 1 ) === '.' ) {
									continue;
								}
								if ( substr( $subfile, -4 ) === $moduleExt && substr( $subfile, 0,  strlen( $this->modulePrefix ) ) === $this->modulePrefix ) {	
									$moduleFilesFound[] = "$file/$subfile";
								}
							}
							closedir( $modulesSubdir );
						}
					} else {
						if ( substr( $file, -4 ) === $moduleExt && substr( $subfile, 0,  12 ) === $this->modulePrefix ) {
							$moduleFilesFound[] = $file;
						}
					}
				}
				closedir( $modulesDir );
			}				
			if ( !empty( $moduleFilesFound ) ) {
				// init modules
				foreach ( $moduleFilesFound as $moduleFile ) {
					if ( !is_readable( "$modulePath/$moduleFile" ) ) {
						continue;
					}	
					if( preg_match( "/{$this->modulePrefix}([a-zA-Z_-]+){$moduleExt}/", $moduleFile, $matches ) ) {
						if( $matches[1] ) {
							$moduleFiles[$matches[1]] = $moduleFile;
						}
					} else {
						continue;
					}
				}
			}				
			if( !empty( $moduleFiles ) ) {
				// To make sure the directory listing is ASC, sort by the array key 
				ksort( $moduleFiles );
				
				$this->setModulesOption( $moduleFiles );
				$this->moduleFiles = $moduleFiles;
				$this->hasModules = true;
			}
		}
	}		
}