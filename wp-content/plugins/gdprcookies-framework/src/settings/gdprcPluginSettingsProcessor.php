<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcPluginSettingsProcessor Class
 *
 * Class for handling Plugin Settings
 *
 * @author $Author: NULL $
 * @version $Id: gdprcPluginSettingsProcessor.php 175 2018-03-07 15:21:02Z NULL $
 * @since 1.2
 */
final class gdprcPluginSettingsProcessor extends gdprcBaseSettingProcessor 
{		
	/**
	 * Flag if current request initiated a reset
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	public $resetting = false;		
	
	/**
	 * Settings file prefix
	 *
	 * Every settings file needs this prefix in the file name
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	private $prefix;	

	/**
	 * Plugin name
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	private $pluginName = '';
	
	/**
	 * Name of the option that saves all founded settings files
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	private $optionName;
	
	/**
	 * Flag if client requested a force to delete data
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	private $forceDeleteData = false;	
		
	/**
	 * All warning messages for form validation
	 *
	 * @since 1.2
	 *
	 * @var array
	 */
	private $warnings = array();	
	
	/**
	 * Flag if warnings are present
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	private $hasWarnings = false;		
	
	/**
	 * the current locale
	 * 
	 * @since 1.2
	 * 
	 * @var string
	 */
	private $currentLocale = null;	
	
	/**
	 * the available locales
	 * 
	 * @since 1.2
	 * 
	 * @var array
	 */
	private $locales = null;
	
	/**
	 * gdprcPluginGlobals instance
	 * 
	 * @since 1.4.0
	 * 
	 * @var gdprcPluginGlobals
	 */
	private $globals = false;
	
	const SETT_WARN_IS_NOT_NUM = 'is_not_numeric';
	
	const SETT_WARN_IS_NOT_COLOR = 'is_not_color';
	
	const SETT_WARN_IS_ZERO = 'is_zero';
	
	const SETT_WARN_IS_FLOAT = 'is_float';
	
	const SETT_WARN_NO_OPTION = 'no_option';	
	
	/**
	 * Constructor
	 *
	 * @param string $settings
	 * @param string $rootPath
	 * @param string $rootUri
	 * @param string $ext
	 * @param gdprcPluginGlobals $globals
	 *
	 * @access public
	 *
	 * @throws Exception if the $settings param is empty
	 *
	 * @since 1.2
	 */
	public function __construct( $settings = '', $rootPath = '', $rootUri = '', $ext = '.xml', $currentLocale, $locales, $globals )
	{
		if( '' === $settings ) {
			throw new Exception( 'input parameter $settings is empty.' );
		}		
		if( !is_a( $globals, 'gdprcPluginGlobals' ) ) {
			throw new Exception( 'Parameter globals is not valid.' );
		}
		
		$this->globals = $globals;
		$nameSpace = $this->globals->get( 'pluginNameSpace' );
		
		if( !gdprcMiscHelper::fontType( $nameSpace ) ) {
			throw new gdprcException( '' );
		}		

		parent::__construct( $rootPath, $rootUri, $ext, $nameSpace );
		
		$this->currentLocale = $currentLocale;
		$this->locales = $locales;
			
		$this->prefix = $this->getNamespace() . '-tab-';
		
		$this->resetting = ( isset( $_REQUEST['gdprc_do_reset'] ) && '1' === $_REQUEST['gdprc_do_reset'] ) ? true : false;
		//@todo: show notice confirming deleted 
		$this->forceDeleteData = ( isset( $_GET['force_delete_settings'] ) && '1' === $_GET['force_delete_settings'] && isset( $_GET['ns'] ) && $this->nameSpace === $_GET['ns'] ) ? true : false;
		$this->optionName = $settings;
	}
	
	
	/**
	 * Callback for the admin_init hook: register the setting
	 *
	 * @acces public
	 *
	 * @uses register_setting()
	 *
	 * @since 0.1
	 */
	public function register()
	{
		register_setting( $this->currentSetting , $this->currentSetting ,  array( &$this, 'validator' ) );
	}	
	
	
	/** 
	 * Create setting names and settings
	 * 
	 * The given (XML) file location are used as base
	 * 
	 * @access	public
	 * 
	 * @param	array $files (optional) pass an array with settings files
	 * 
	 * @uses	self::getFiles()
	 * @uses	self::hasSetting()
	 * @uses	self::setSettingName()
	 * @uses	self::create()
	 * 
	 * @since 1.2
	 */
	public function create( $files = array() ) 
	{		
		// first check if passed array with files is not empty
		if( is_array( $files ) && 0 === count( $files ) ) {
			$files = $this->getFiles();			
		}	

		// if still no valid file locations, then find the files in the directory
		if( is_array( $files ) && 0 < count( $files ) && !$this->_filesExist( $files ) ) {
			$this->find( true );
			$files = $this->getFiles();
		}		
		
		self::$settings[$this->nameSpace] = array();
		self::$settingNames[$this->nameSpace] = array();
				
		foreach ( $files as $idx => $file ) {	
			$name = $this->setSettingName( $idx );
			$this->_create( $idx, $name, $file );	
		}	
	}	
	
	/**
	 * Update the settings based on given xml file locations
	 * 
	 * @acces public
	 * 
	 * @param	array	$locations
	 * @param 	bool	$overwite
	 * 
	 * @uses	self::setSettingName()
	 * @uses	self::create()
	 * @uses	self::updateOption()
	 * 
	 * @since 	1.2
	 * 
	 * @return	void or array with failed location
	 */
	public function update( $locations = array(), $overwite = false )
	{
		if( empty( $locations ) ) {
			return false;
		} else {			
			$files = $failures = array();
			$ext = parent::getExt();			
			foreach ( $locations as $location ) {
				$filesArr = $this->getFilesArray( $location, $ext );
				if( false === $filesArr ) {
					$failures[] = $location;
				} elseif( is_array( $filesArr ) && !empty( $filesArr ) ) {
					//ksort( $filesArr );
					$files += $filesArr;									
				}
			}			
		}
		
		$filesOld = $this->getOption();
			
		foreach ( $files as $idx => $file ) {
			if( is_numeric( $idx ) ) {
				$idx = $this->getIdxFromFilename( $file );
			}
						
			if( $this->hasSetting( $idx ) && !$overwite ) {
				continue;
			}
	
			$name = $this->setSettingName( $idx );
			$this->_create( $idx, $name, $file );
			$filesOld[$idx] = $file;
		}
		
		$this->updateOption( $filesOld );				
		$this->files = $filesOld;
	}	
		
	/**
	 * Initialize all settings
	 *
	 * Create class instances and set a WordPress Hook per setting
	 *
	 * @access public
	 * @param array $vars
	 * @param boolean $activating
	 *
	 * @since 1.2
	 */
	public function init( $vars = array(), $activating = false )
	{
		if( !$this->hasSettingsFiles ) {
			return;
		}
				
		// based on files in the {namespace}_settings option
		// create gdprcPluginSettings instances for each xml file 
		$this->create();
					
		$settingNames = $this->getSettingNames();
		foreach ( $settingNames as $idx => $name ) {					
			// only add hooks for current settings request
			if( isset( $_REQUEST['option_page'] ) && $_REQUEST['option_page'] === $name ) {
				
				// set the current settings name
				$this->currentSetting = $name;
				
				$this->setWarnings();
			
				add_action( 'admin_init' , array( &$this, 'register' ) );
				add_filter( 'pre_update_option_'.$name, array( &$this, 'beforeUpdate' ), 10, 2 );
			}
		}		
		
		if( $this->hasSettings( $this->nameSpace ) ) {
			$this->setHasSettings( true );
		}
	}	
	
	/**
	 * Callback for the {nameSpace}_after_init_modules hook
	 * 
	 * @todo: 	detailed description
	 * 
	 * @acces 	public
	 * 
	 * @since 	1.2
	 * 
	 * @param 	string	$currentLocale
	 * @param 	array	$locales
	 */
	public function hook( $currentLocale, $locales )
	{		
		/**
		 * This hook let other Modules / Plugins add setting instances
		 *
		 * @param array 	self:$settings 		all setting instances (by reference)
		 * @param string 	self:$currentLocale the current locale
		 * @param array 	self:$locales		all locales
		 * @param string 	$namespace 			current namespace (gdprcclb)
		 */
		self::$settings[$this->nameSpace] = apply_filters_ref_array( $this->nameSpace . '_setting_groups_init', array( &self::$settings[$this->nameSpace], $currentLocale, $locales, $this->nameSpace ) );
		
		remove_all_filters( $this->nameSpace . '_setting_groups_init' );
			
		/**
		 * This hook let other Modules / Plugins alter the settings after they've been initialized
		 *
		 * @param array self:$settings all setting instances (by reference)
		 * @param gdprcPluginSettingsProcessor $this (by reference)
		 *
		 * @since 1.x
		 */
		do_action_ref_array( $this->nameSpace . '_settings_init_ready',  array( &self::$settings[$this->nameSpace], &$this ) );
		
		// if client requested, maybe delete Plugin data
		$this->maybeDeleteData();
			
		// include the I18n.php file if it exists
		// this file contains the strings from the XML setting files already wrapped in the __() WordPress function
		// and is created in gdprcPluginSettings::_writeTranslations() during the first init of the setting
		if( file_exists( $this->getRootPath() . '/I18n.php' ) )
		{
			include_once $this->getRootPath() . '/I18n.php';
		}
		
		// for testing only
		// @todo remove in production
		//add_action( 'gdprcclb_do_reset', array( &$this, 'resetModules' ) );

		remove_action( $this->nameSpace . '_after_init_modules', array( &$this, 'hook' ) );
	}	
	
	/**
	 * Action callback for sanitize_option_{$option_name}
	 *
	 * The sanitize_option_{$option_name} hook is called threw 'register_setting()'
	 *
	 * @access public
	 *
	 * @param array $data
	 * 
	 * @todo: implent hook params: $value, $option, $original_value?
	 *
	 * @since 1.2
	 */	
	public function validator( $data ) 
	{		
		static $counter = 0;			
		$counter++;
		
		if( 2 === $counter ) {
			return $data;
		}
		
		try {						
			$page = $_REQUEST['option_page'];
			
			if( !isset( $data[$this->nameSpace.'_tab'] ) ) {
				return $data;
			}
			
			$path = $this->globals->get( 'pluginPathFile' );
			$this->pluginName = gdprcMiscHelper::getPluginData( $path, 'Name' );
						
			if( $this->resetting )  {
				gdprcNotices::add( $this->nameSpace,  __( sprintf( '%s settings resetted', $this->pluginName ) , 'gdprcookies' ), 'updated', $page );				
				return $data;
			}
			
			$settingNames = self::$settingNames[$this->nameSpace];			
			if( !in_array( $page , $settingNames ) ) {
				return $data;
			}
			
			$warnings = array();
				
			foreach ( $settingNames as $idx => $name ) {
				if( $page !== $name ) {
					continue;
				}
					
				$setting = $this->getSetting( $idx );
				$settingsXml = $setting->getSettings();				
				$fields = $settingsXml->xpath( '//field[@validate="y"]' );

				foreach ( $fields as $field ) {					
					$type = (string)$field['type'];
					$name = (string)$field['name'];
					$title = (string)$field->title;
					
					switch ( $type ) {						
						case 'numeric':							
							if( isset( $data[$name] ) && !is_numeric( $data[$name] ) ) {
								$warnings[$name] = sprintf( $this->getWarning( self::SETT_WARN_IS_NOT_NUM ), $title );
							}							
							break;							
						case 'numeric_abs':							
							if( isset( $data[$name] ) && !is_numeric( $data[$name] ) ) {
								$warnings[$name] = sprintf( $this->getWarning( self::SETT_WARN_IS_NOT_NUM ), $title );
							}							
							if( isset( $data[$name] ) && ( false !== strpos( $data[$name], '.' ) || false !== strpos( $data[$name], ',' ) ) ) {
								$warnings[$name] = sprintf( $this->getWarning( self::SETT_WARN_IS_FLOAT ), $title );
							} elseif( isset( $data[$name] ) && !is_numeric( $data[$name] ) ) {
								$warnings[$name] = sprintf( $this->getWarning( self::SETT_WARN_IS_NOT_NUM ), $title );
							} elseif( isset( $data[$name] ) && ( '0' === trim( $data[$name] ) || 0 > (int) $data[$name] ) ) {
								$warnings[$name] = sprintf( $this->getWarning( self::SETT_WARN_IS_ZERO ), $title );
							}									
							break;							
						case 'color':
							if( isset( $data[$name] ) ) {
								$color = $data[$name];							
								if ( gdprcMiscHelper::isValidHexColor( "#$color") && false === strpos( $color , '#') ) {
									$data[$name] = "#$color";
									continue;
								}								
								if( false === gdprcMiscHelper::isValidHexColor( $data[$name] ) ) {
									$warnings[$name] = sprintf( $this->getWarning( self::SETT_WARN_IS_NOT_COLOR ), $title );
								}
							}									
							break;							
						case 'force_option':							
							if( isset( $data[$name] ) && '-1' === $data[$name] ) {
								$warnings[$name] = sprintf( $this->getWarning( self::SETT_WARN_NO_OPTION ), $title );
							}
							break;							
						case 'allowed_tags':							
							if( function_exists( 'wp_kses_allowed_html' ) ) {
								$data[$name] = wp_kses( $data[$name], wp_kses_allowed_html( 'post' ), array( 'http', 'https', 'mailto', 'tel' ) );
							}							
							break;
					}
				}		
			}
			
			do_action( $this->nameSpace . '_validate_settings_'.$page, $data, $page );
		
			// let modules hook into the validate process
			$warnings = apply_filters( $this->nameSpace . '_settings_warnings_'.$page, $warnings, $data );
			$warnings = apply_filters( $this->nameSpace . '_settings_warnings', $warnings, $page, $data );
			
			$this->hasWarnings = ( empty( $warnings ) ) ? false : true;
		
			// add WP setting errors if errors occured
			if( true === $this->hasWarnings ) {
				foreach( $warnings as $name => $warning ) {
					if( '' !== $name && $setting->offsetExists( $name ) ) {
						$data[$name] = $setting->offsetGet( $name );
					}
					
					gdprcNotices::add( $this->nameSpace, $warning, 'updated', $page );
				}
			}
		
			// return form data
			$data = apply_filters( $this->nameSpace . '_validated_data', $data, $page, $this->hasWarnings );
			$data = apply_filters( $this->nameSpace . '_validated_data_'.$page, $data, $this->hasWarnings );
		
		} catch ( Exception $e ) {		
			gdprcNotices::add( $this->nameSpace, $e->getMessage(), 'error', $page );
		}
		
		unset( $setting, $settingsXml, $fields );
		
		return $data;		
	}	

	/**
	 * Do some setting related actions before updating the settings option
	 *
	 * This is a callback hooked to filter 'pre_update_option_{$option_name}'
	 *
	 * @access public
	 *
	 * @param array $data, the new values
	 * @param array $option the old values
	 *
	 * @since 1.2
	 *
	 * @return array
	 */	
	public function beforeUpdate( $data, $option )
	{
		try {	
			// data = new_value
			// options = old_value
				
			if( ! apply_filters( 'gdprc_do_update_settings', true ) ) {
				return $option;
			}
			
			if( $this->resetting ) {
				do_action( $this->nameSpace . '_do_reset' );
				return $option;
			}
	
			$page = $_POST['option_page'];

			$settingNames = $this->getSettingNames();
			if( !in_array( $page , $settingNames ) ) {
				return $option;	
			}

			$idx = array_search( $page , $settingNames );
			$setting = $this->getSetting( $idx );
						
			switch( $page ) {
				case $this->nameSpace . '_settings_tools':
					if( $this->resetting ) {							
						// $this->deleteOption();
						// $this->deleteOptionDefault();
						
 						// // @todo: use self::deleteData() here?	
 						// foreach ( $settingNames as $name ) 
 						// {						
 						//	 $setting->deleteOption( $name );	
 						//   $this->deleteOptionFields( $name );
 						// }
						
 						// do_action( $this->nameSpace . '_do_reset' );
							
						//return array();
						return $option;
					}
					break;
			}
			
			if( is_array( $data ) ) {
				// if no warnings occured, show update notice
				if( false === $this->hasWarnings ) {
					gdprcNotices::add( $this->nameSpace,  __( sprintf( '%s settings updated', $this->pluginName ) , 'gdprcookies' ), 'updated', $page );
				}
								
				if( !$setting->hasLocale() && $data === $option ) {
					return $option;
				} elseif( $setting->hasLocale() && $data === $option[$setting->getLocale()] ) {
					return $option;
				}
				
				$this->_update( $data, $page );
	
				if( !$setting->hasLocale() ) {
					return $setting->getstack();
				} else {
					$option[$setting->getLocale()] = $setting->getstack();
					return $option;
				}	
			} else {
				return $option;
			}				
		} catch ( Exception $e ) {				
			gdprcNotices::add( $this->nameSpace, $e->getMessage(), 'error', $page );
		}
	}
	
	/**
	 * Store the settings (files) in the database as a option
	 *
	 * @access public
	 * 
	 * @param array $files
	 * 
	 * @uses add_option()
	 *
	 * @since 1.2
	 */
	public function setOption( $files )
	{
		add_option( $this->optionName, $files );
	}	
	
	/**
	 * Update the settings (files) option
	 *
	 * @access public
	 * 
	 * @param array $files
	 * 
	 * @uses update_option()
	 *
	 * @since 1.2
	 */
	public function updateOption( $files )
	{		
		update_option( $this->optionName, $files );
	}	
	
	/**
	 * Get the option with setting files
	 *
	 * @access public
	 * 
	 * @uses get_option()
	 *
	 * @since 1.2
	 *
	 * @return array with setting files
	 */
	public function getOption()
	{
		return get_option( $this->optionName, array() );
	}	
	
	/**
	 * Delete option in database
	 *
	 * @access public
	 * 
	 * @uses delete_option()
	 *
	 * @since 1.2
	 */
	public function deleteOption()
	{
		delete_option( $this->optionName );
	}	
	
	/**
	 * Delete the default setting option in the database
	 *
	 * @access public
	 *
	 * @uses delete_option()
	 *
	 * @since 1.4.0
	 */	
	public function deleteOptionDefault() 
	{
		delete_option( $this->nameSpace. '_setting_default' );
	}
	
	/**
	 * Delete the database option that holds the settings XML fields
	 *
	 * @access public
	 *
	 * @param string $optionName
	 *
	 * @uses delete_option()
	 *
	 * @since 1.4.0
	 */	
	public function deleteOptionFields( $optionName = '' ) 
	{
		delete_option( $optionName . '_fields' );	
	}
	
	/**
	 * Delete all settings data
	 * 
	 * @access public
	 * 
	 * @param boolean $deleteTmp
	 * @param boolean $backup
	 * 
	 * @uses self::deleteData()
	 */
	public function deleteAll( $deleteTmp = false, $backup = false ) 
	{
		$this->deleteData( $deleteTmp, $backup );
	}	
	
	/* (non-PHPdoc)
	 * @see igdprcSettingProcessor::find()
	 */
	public function find( $force = false )
	{		
		$files = $this->getFiles();
		if( !$force && $this->_hasFiles( $files ) && $this->_filesExist() ) {
			$this->files = $files;
			$this->hasSettingsFiles = true;
		} else {			
			$path = parent::getRootPath();
			$ext = parent::getExt();
			$files = $this->getFilesArray( $path, $ext );			
		
			if( !empty( $files ) ) {
				// To make sure the directory listing is ASC, sort by the array key 
				ksort( $files );
				
				$this->updateOption( $files );
				$this->files = $files;
				$this->hasSettingsFiles = true;
			}
		}		
	}	
	
	/**
	 * Handle the reset steps
	 * 
	 * @access public
	 * 
	 * @uses self::getOption()
	 * @uses self::create()
	 * @uses self::deleteOption()
	 * @uses self::deleteAll()
	 * 
	 * @since 1.4.6
	 */
	public function reset()
	{
		$files = $this->getOption();
		$filesExist = $this->_filesExist();
		if( !$filesExist ) {
			$this->deleteOption();
			$files = array();
		}		
		
		$this->create( $files );
		$this->deleteOption();
		$this->deleteAll( true );		
	}
	
	/**
	 * Handle the upgrade steps
	 *
	 * @access public
	 * 
	 * @uses self::getOption()
	 * @uses self::create()
	 * @uses self::deleteAll()
	 * @uses self::deleteOption()
	 *
	 * @since 1.4.6
	 */	
	public function upgrade()
	{
		$files = $this->getOption();		
		$filesExist = $this->_filesExist( $files );
		if( !$filesExist ) {
			$this->deleteOption();
			$files = array();			
		}
		
		// only upgrade if files already exist
		if( !empty( $files ) || !$filesExist ) {
			$this->create( $files );
			$this->deleteAll( false, true );
			$this->deleteOption();
		}		
	}
	
	/**
	 * Determine if files in passed $files or option still exist
	 * 
	 * @uses self::getFiles()
	 * 
	 * @since 1.4.7
	 * 
	 * @return boolean
	 */
	private function _filesExist( $files = null ) 
	{
		$exist = false;
		$files =  ( is_array( $files ) ) ? $files : $this->getFiles();		

		foreach ( $files as $idx => $file ) {
			if( file_exists( $file ) ) {
				$exist = true;
				break;
			}
		}

		return $exist;
	}
	
	/**
	 * Determine if having files
	 * 
	 * @param array $files
	 * 
	 * @uses self::getFiles()
	 * 
	 * @since 1.4.7
	 * 
	 * @return boolean
	 */
	private function _hasFiles( $files = array() ) 
	{
		return ( is_array( $files ) && 0 < count( $files ) );
	}
	
	/**
	 * Create a setting based on given
	 *
	 * index, name and file path
	 *
	 * @access	private
	 *
	 * @param	string $idx
	 * @param	string $name
	 * @param	string $file - the file path to the XML file
	 *
	 * @uses	gdprcPluginSettings class
	 * @uses	self::setSetting()
	 *
	 * @since 1.2
	 */
	private function _create( $idx, $name, $file )
	{
		$instance = new gdprcPluginSettings( $name, $file, $this->currentLocale, $this->locales, $this->nameSpace );
		$this->setSetting( $idx, $instance );
	}	
	
	/**
	 * Satinize fields in given array
	 *
	 * @access private
	 *
	 * @param array $data
	 * @param array $skip Skip fieds to skip
	 *
	 * @since 1.2
	 *
	 * @return $data
	 */
	private function satinize( $data = array(), $skip = array() ) {	
		foreach ( (array) $data as $field => $value ) {	
			if( empty( $skip ) ) {	
				$data[$field] = sanitize_text_field( $value );
			} elseif( !empty( $skip ) && !in_array( $field, $skip ) ) {					
				$data[$field] = sanitize_text_field( $value );
			}
		}
	
		return $data;
	}	
	
	/**
	 * Update the stack
	 *
	 * NOTE:checkboxes cant use the isset check!
	 *
	 * @access 	private
	 *
	 * @uses 	gdprcPluginSettings::satinize()
	 * @uses 	apply_filters() to let modules hook into this process
	 *
	 * @param 	array $data
	 * @param 	string $setting
	 *
	 * @since 	1.2
	 */
	private function _update( $data, $page = '' )
	{
		// update the options object and options table with given values from form in admin settings
		if( '' !== $page ) {
			$settingNames = $this->getSettingNames();
			if( !in_array( $page , $settingNames ) ) {
				return;	
			}
			
			$currentIdx = '';
			$skip = array();			
			foreach ( $settingNames as $idx => $name ) {
				if( $page !== $name ) {
					continue;
				} else { 
					$currentIdx = $idx;
				}
					
				$setting = $this->getSetting( $idx );
				$settingsXml = $setting->getSettings();				
				$fields = $settingsXml->xpath( '//field[@validate="y"]' );

				foreach ( $fields as $field ) {
					$name = (string)$field['name'];
					if( !gdprcXmlSettingsHelper::isFieldToSanitize( $field ) ) {
						$skip[] = $name;
					}
				}
			}
	
			/**
			 * Filter the $skip array
			 *
			 * @param array 	$skip
			 * @param string	$setting
			 *
			 * @since 1.x
			 */
			$skip = apply_filters( $this->nameSpace . '_settings_skip_fields', $skip, $page );				
			$data = $this->satinize( $data, $skip );
			
			foreach ( $settingNames as $idx => $name ) {
				if( $page !== $name ) {
					continue;
				}
				
				$setting = $this->getSetting( $idx );
				$settingsXml = $setting->getSettings();
				$fields = $settingsXml->xpath( '//field' );
				
				foreach ( $fields as $field ) {
					$name = (string)$field['name'];
					
					if( gdprcXmlSettingsHelper::isCheckbox( $field ) ) {
						$setting->offsetSet( $name, ( isset( $data[$name] ) ? true : false ) );
					} elseif( gdprcXmlSettingsHelper::isWpPostTypeList( $field ) ) {
						$isClickSelect = gdprcXmlSettingsHelper::isClickSelect( $field );						
						if( $isClickSelect ) {
							$attributes = gdprcXmlSettingsHelper::getAttr( $field );
							$context = ( isset( $attributes['_context'] ) && '' != $attributes['_context'] ) ? $attributes['_context'] : false;
							if( false !== $context ) {								
								$context = str_replace( '-', '_', $context );
								$name = $context . '_selected';
								if( isset( $data[$name] ) )	$setting->offsetSet( $name,	(int)$data[$name] );
							}
						}						
					} else {
						if( isset( $data[$name] ) )	{ 
							$setting->offsetSet( $name,	$data[$name] ); 
						}
					} 
				}
			}
			
			$moduleUpdateSettings = apply_filters( $this->nameSpace . '_update_settings', array(), $data, $page );				
			if( !empty( $moduleUpdateSettings ) ) {
				foreach ( $moduleUpdateSettings as $k => $v ) {
					$setting->offsetSet( $k, $v );
				}
			}	
		} else {
			// return false or msg
		}
	}	
	
	/**
	 * Get settings filename index
	 * 
	 * For example find "general" from {namespace}-tab-general.xml
	 *
	 * @access private
	 *
	 * @uses preg_match()
	 *
	 * @since 1.2
	 *
	 * @return string the index or bool false if filename does not fit the format
	 */	
	private function getIdxFromFilename( $filename = '', $ext = '.xml' ) 
	{
		if( preg_match( "/^{$this->prefix}([^.]+)\\{$ext}$/", $filename, $m ) ) {
			$idx = trim( $m[1] );
			return $idx;	
		}
		
		return false;	
	}	
	
	/**
	 * Get directory files array
	 *
	 * @access 	private
	 *
	 * @param	string $path
	 * @param 	string $ext
	 *
	 * @uses 	gdprcMiscHelper::findFiles() to read the files
	 * @uses 	self::getIdxFromFilename() to strip the unique index from the filename
	 *
	 * @since 	1.2
	 *
	 * @return 	array
	 */
	private function getFilesArray( $path, $ext )
	{
		$files = array();
	
		// look into the directory and search for settings files
		$found = gdprcMiscHelper::findFiles( $path, $ext, $this->prefix );	
		if( false === $found ) {
			throw new Exception( sprintf( __( 'gdprcookies Framework: Could not open path settings location on: %s', 'gdprcookies' ), $path ) );
		} elseif ( is_array( $found ) && !empty( $found ) ) {
			// init modules
			foreach ( $found as $file ) {
				$fullPath = "$path/$file";
				if ( !is_readable( $fullPath ) ) {
					continue;
				}
					
				// find i.e. "general" from {namespace}-settings-general.xml
				if( $idx = $this->getIdxFromFilename( $file, $ext ) ) {
					// @todo: consider storing relative path
					$files[$idx] = $fullPath;
				} else {
					continue;
				}
			}
		}
	
		return $files;
	}	
		
	/**
	 * Set Warning messages
	 * 
	 * @access 	private
	 *
	 * @since 	1.2
	 */
	private function setWarnings()
	{
		$this->warnings[self::SETT_WARN_IS_NOT_NUM] 	= __( 'Please enter a numeric value for "%s"', 'gdprcookies' );
		$this->warnings[self::SETT_WARN_IS_NOT_COLOR] 	= __( 'Please enter a valid color for "%s" in the format #XXX or #XXXXXX', 'gdprcookies' );		
		$this->warnings[self::SETT_WARN_IS_ZERO] 		= __( 'Please enter a number greater then zero for "%s"', 'gdprcookies' );
		$this->warnings[self::SETT_WARN_IS_FLOAT] 		= __( 'Please enter an integer number like 5,  20 or 100 for "%s"', 'gdprcookies' );
		$this->warnings[self::SETT_WARN_NO_OPTION] 	= __( 'Please select an option for "%s"', 'gdprcookies' );
	
		$this->warnings = apply_filters( $this->nameSpace . '_settings_validation_warnings', $this->warnings, $this->currentSetting );
	}	
	
	/**
	 * Get Warning messages
	 * 
	 * @access 	private
	 *
	 * @since 	1.2
	 * 
	 * @return	array
	 */
	private function getWarnings() 
	{		
		return $this->warnings;		
	}	
	
	/**
	 * Get Warning message
	 *
	 * @access 	private
	 * 
	 * @param	string $key
	 *
	 * @since 	1.2
	 *
	 * @return	string
	 */	
	private function getWarning( $key = '' )
	{	
		if( isset( $this->warnings[$key] ) ) {
			return $this->warnings[$key];
		} else {
			return '';
		}
	}		
	
	/**
	 * Delete all settings
	 * 
	 * @access 	private
	 *
	 * @param 	bool	$deleteTmp
	 * @param 	bool	$backup
	 * 
	 * @uses	gdprcPluginSettings::setOptionTmp() if $backup is true
	 * @uses	gdprcPluginSettings::deleteOption()
	 * @uses	delete_option()	to delete te tmp option
	 * @uses	self::deleteOption() to delete the {nameSpace}_settings option
	 *
	 * @since 	1.2
	 */
	private function deleteData( $deleteTmp = false, $backup = false )
	{
		$settings = $this->getSettings();

		foreach ( $settings as $setting ) {
			if( $backup ) {
				$setting->setOptionTmp();
			}
	
			$setting->deleteOption();
				
			if( $deleteTmp ) {
				$setting->deleteOption( $setting->getOptionName() . '_tmp' );
			}
							
			// delete the option that holds the fields xml for the current setting
			$this->deleteOptionFields( $setting->getOptionName() );
		}
		
		// delete the option that holds the default setting
		$this->deleteOptionDefault();
		
		// delete the settings files option ({nameSpace}_settings)
		$this->deleteOption();
	}	
	
	/**
	 * Delete all Plugin data if query contains param
	 *
	 * If client requested an URL with quert param "force_delete_settings=1", data is deleted:
	 * 	- db entries in wp_options
	 *
	 * Conditions are that:
	 * 	- user has cap 'manage_options'
	 *
	 * @access private
	 *
	 * @since 1.2
	 */
	private function maybeDeleteData()
	{
		if( true === $this->forceDeleteData && current_user_can( 'manage_options' ) ) {	
			try {	
				$isMultisite = is_multisite();				
				if( $isMultisite && is_network_admin() ) {	
					$currentBlog = get_current_blog_id();
					$sites = gdprcMultisiteHelper::getSites();
						
					foreach ( $sites as $site ) {
						$id = $site->blog_id;
						switch_to_blog( $id );	
						$this->deleteData( true, false );
						
						do_action( $this->nameSpace. '_force_delete_settings', $id );
					}
						
					switch_to_blog( $currentBlog );
					
				} elseif ( !$isMultisite && is_admin() ) {	
					$this->deleteData( true, false );					
					do_action( $this->nameSpace. '_force_delete_settings', false );
				}	
			} catch ( Exception $e ) {	
				// only show Exception messages in the Network admin pages
				if( is_network_admin() || is_admin() ) {
					gdprcNotices::add( $this->nameSpace, $e->getMessage() );
				}
			}
		}
	}	
}