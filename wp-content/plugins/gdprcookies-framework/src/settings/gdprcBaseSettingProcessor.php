<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * igdprcSettingProcessor interface
 *
 * @author $Author: NULL $
 * @version $Id: gdprcBaseSettingProcessor.php 175 2018-03-07 15:21:02Z NULL $
 * @since 1.2
 */
interface igdprcSettingProcessor
{
	public function setSetting( $key = null, $setting, $nameSpace= null );
	public function setSettingName( $key = null, $nameSpace = null );
	public function setHasSettings( $hasSettings = false );
	public function setHasSettingsPage( $hasSettingsPage = false );
	public function setSettingsPage( $settingsPage );
	public function getSetting( $key = null, $nameSpace = null );
	public function getSettings( $nameSpace = null );
	public function getSettingNames( $nameSpace = null );
	public function getSettingName( $key = null, $nameSpace = null );
	public function getSettingsPage();
	public function getHasSettings();
	public function getFiles();
	public function hasSetting( $key = null, $nameSpace = null );
	public function hasSettings( $nameSpace = null );
	public function hasSettingName( $key = null, $nameSpace = null );
	public function hasSettingNames( $nameSpace = null );
	public function hasSettingsPage();
	public function find( $force = false );
}

/**
 * gdprcBaseSettingProcessor Class
 *
 * Base class for handling Plugin Settings
 *
 * @author $Author: NULL $
 * @version $Id: gdprcBaseSettingProcessor.php 175 2018-03-07 15:21:02Z NULL $
 * @since 1.2
 */
abstract class gdprcBaseSettingProcessor implements igdprcSettingProcessor
{
	/**
	 * The root path for all Settings
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	protected $rootPath;

	/**
	 * The root URI for all Settings
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	protected $rootUri;

	/**
	 * The extension that all Settings should have
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	protected $ext;

	/**
	 * The namespace for all Settings
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	protected $nameSpace;

	/**
	 * All founded Settings
	 *
	 * @since 1.2
	 *
	 * @var array
	 */
	protected $files = array();

	/**
	 * Flag if Settings files are found
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	protected $hasSettingsFiles = false;

	/**
	 * Flag if the $settings array is set for namespace
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	public $hasSettings = false;

	/**
	 * Flag if current namespace has a settings page
	 *
	 * @since 1.4.0
	 *
	 * @var bool
	 */
	public $hasSettingsPage = false;

	/**
	 * Container for all setting names as stored in the wp_options table
	 *
	 * @since 1.2
	 *
	 * @var array
	 */
	public static $settingNames = array();

	/**
	 * All Setting instances
	 *
	 * @since 1.2
	 *
	 * @var array
	 */
	public static $settings = array();

	/**
	 * Instance of gdprcPluginSettingsPage class
	 *
	 * @since 1.2
	 *
	 * @var gdprcPluginSettingsPage
	 */
	public $settingsPage = null;

	/**
	 * Name of the current setting
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	protected $currentSetting = null;

	/**
	 * Constructor
	 *
	 * @access	public
	 *
	 * @param 	string $rootPath
	 * @param 	string $rootUri
	 * @param 	string $ext
	 * @param 	string $nameSpace
	 *
	 * @since	1.2
	 */
	public function __construct( $rootPath = '', $rootUri = '', $ext = '', $nameSpace = '' )
	{
		$this->_setRootPath( $rootPath );
		$this->_setRootUri( $rootUri );
		$this->_setExt( $ext );
		$this->_setNamespace( $nameSpace );

		// init the static arrays
		self::$settings[$this->nameSpace] = array();
		self::$settingNames[$this->nameSpace] = array();
	}


	/**
	 * Check if a Setting exists for given namespace and key
	 *
	 * @access	public
	 *
	 * @param	string $key
	 * @param 	string $nameSpace
	 *
	 * @since 	1.2
	 *
	 * @return	bool
	 */
	public function hasSetting( $key = null, $nameSpace = null )
	{
		if( null === $nameSpace ) {
			$nameSpace = $this->nameSpace;
		}

		return ( isset( self::$settings[$nameSpace][$key] ) ) ? true : false;
	}

	/**
	 * Check if a Settings exists for given namespace
	 *
	 * @access public
	 *
	 * @param string $nameSpace
	 *
	 * @since 1.2
	 *
	 * @return bool
	 */
	public function hasSettings( $nameSpace = null )
	{
		if( null === $nameSpace ) {
			$nameSpace = $this->nameSpace;
		}

		return ( is_array( self::$settings[$nameSpace] ) ) ? true : false;
	}

	/**
	 * Check if a setting name exists for given namespace and key
	 *
	 * @access	public
	 *
	 * @param	string $key
	 * @param 	string $nameSpace
	 *
	 * @since 	1.2
	 *
	 * @return 	bool
	 */
	public function hasSettingName( $key = null, $nameSpace = null )
	{
		if( null === $nameSpace ) {
			$nameSpace = $this->nameSpace;
		}

		return ( isset( self::$settingNames[$nameSpace][$key] ) ) ? true : false;
	}

	/**
	 * Check if settings names exists for given namespace
	 *
	 * @access	public
	 *
	 * @param 	string $nameSpace
	 *
	 * @since 	1.2
	 *
	 * @return 	bool
	 */
	public function hasSettingNames( $nameSpace = null )
	{
		if( null === $nameSpace ) {
			$nameSpace = $this->nameSpace;
		}

		return ( isset( self::$settingNames[$nameSpace] ) ) ? true : false;
	}

	/**
	 * Get property $hasSettingsPage
	 *
	 * @access public
	 *
	 * @since 1.4.0
	 * 
	 * @return bool
	 */
	public function hasSettingsPage()
	{
		return $this->hasSettingsPage;
	}
	
	/**
	 * Set a setting
	 *
	 * @access 	public
	 *
	 * @todo:	add type casting 'gdprcPluginSettings' to $setting param?
	 *
	 * @param 	string				$key
	 * @param 	gdprcPluginSettings 	$setting
	 * @param 	string 				$nameSpace
	 *
	 * @since 	1.2
	 */
	public function setSetting( $key = null, $setting, $nameSpace = null )
	{
		if( null === $nameSpace ) {
			$nameSpace = $this->nameSpace;
		}

		if( self::hasSettings( $nameSpace ) ) {
			self::$settings[$nameSpace][$key] = $setting;
		} else {
			self::$settings[$nameSpace] = array();
			self::$settings[$nameSpace][$key] = $setting;
		}
	}

	/**
	 * Set a setting name
	 *
	 * @access 	public
	 *
	 * @param 	string $key
	 * @param 	string $nameSpace
	 *
	 * @since	1.2
	 *
	 * @return	string
	 */
	public function setSettingName( $key = null, $nameSpace = null )
	{
		if( null === $nameSpace ) {
			$nameSpace = $this->nameSpace;
		}

		$idx = strtolower( str_replace('-', '_', $key) );
		$name = sprintf( '%s_settings_%s', $nameSpace, $idx );

		if( self::hasSettingNames( $nameSpace ) ) {
			self::$settingNames[$nameSpace][$key] = $name;
		} else {
			self::$settingNames[$nameSpace] = array();
			self::$settingNames[$nameSpace][$key] = $name;
		}

		return $name;
	}

	/**
	 * Set property $hasSettings
	 * 
	 * @access public
	 *
	 * @param boolean $hasSettings
	 *
	 * @since 1.4.x
	 */
	public function setHasSettings( $hasSettings = false )
	{
		$this->hasSettings = $hasSettings;
	}

	/**
	 * Set property $hasSettingsPage
	 * 
	 * @access public
	 *
	 * @param boolean $hasSettingsPage
	 *
	 * @since 1.4.x
	 */
	public function setHasSettingsPage( $hasSettingsPage = false )
	{
		$this->hasSettingsPage = $hasSettingsPage;
	}

	/**
	 * Set settingsPage
	 * 
	 * @access public
	 *
	 * @param gdprcPluginSettingsPage $settingsPage
	 *
	 * @since 1.4.0
	 */
	public function setSettingsPage( $settingsPage )
	{
		$this->settingsPage = $settingsPage;
	}

	/**
	 * Get all Setting files
	 * 
	 * @access public
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * Get a setting from the stack
	 *
	 * @access public
	 *
	 * @param string $key
	 * @param string $nameSpace
	 *
	 * @since 1.2
	 *
	 * @return gdprcPluginSettings or bool false on failure
	 */
	public function getSetting( $key = null, $nameSpace = null )
	{
		if( null === $nameSpace ) {
			$nameSpace = $this->nameSpace;
		}

		if( isset( self::$settings[$nameSpace] ) && isset( self::$settings[$nameSpace][$key] ) ) {
			return self::$settings[$nameSpace][$key];
		} else {
			return false;
		}
	}

	/**
	 * Get all Settings
	 *
	 * @access public
	 *
	 * @param string $nameSpace
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public function getSettings( $nameSpace = null )
	{
		if( null === $nameSpace ) {
			$nameSpace = $this->nameSpace;
		}

		if( isset( self::$settings[$nameSpace] ) ) {
			return self::$settings[$nameSpace];
		} else {
			return array();
		}
	}

	/**
	 * Get all Setting names
	 *
	 * @access public
	 *
	 * @param string $nameSpace
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public function getSettingNames( $nameSpace = null )
	{
		if( null === $nameSpace ) {
			$nameSpace = $this->nameSpace;
		}

		if( isset( self::$settingNames[$nameSpace] ) ) {
			return self::$settingNames[$nameSpace];
		} else {
			return array();
		}
	}


	/**
	 * Get a setting name
	 *
	 * @access public
	 *
	 * @param string $nameSpace
	 *
	 * @since 1.2
	 *
	 * @return string
	 */
	public function getSettingName( $key = null, $nameSpace = null )
	{
		if( null === $nameSpace ) {
			$nameSpace = $this->nameSpace;
		}

		if( isset( self::$settingNames[$nameSpace] ) && isset( self::$settingNames[$nameSpace][$key] ) ) {
			return self::$settingNames[$nameSpace][$key];
		} else {
			return '';
		}
	}

	/**
	 * Get property $hasSettings
	 *
	 * @access public
	 *
	 * @since 1.4.0
	 */
	public function getHasSettings()
	{
		return $this->hasSettings;
	}

	/**
	 * Get settingsPage
	 *
	 * @access public
	 *
	 * @since 1.4.0
	 */
	public function getSettingsPage()
	{
		return $this->settingsPage;
	}

	/**
	 * Get root Path
	 *
	 * @access protected
	 *
	 * @since 1.2
	 *
	 * @return string
	 */
	protected function getRootPath()
	{
		return $this->rootPath;
	}

	/**
	 * Get root URI
	 *
	 * @access protected
	 *
	 * @since 1.2
	 *
	 * @return string
	 */
	protected function getRootUri()
	{
		return $this->rootUri;
	}

	/**
	 * Get setting generic ext (like .php)
	 *
	 * @access protected
	 *
	 * @since 1.2
	 *
	 * @return string
	 */
	protected function getExt()
	{
		return $this->ext;
	}

	/**
	 * Get setting generic namespace
	 *
	 * @access protected
	 *
	 * @since 1.2
	 *
	 * @return string
	 */
	protected function getNamespace()
	{
		return $this->nameSpace;
	}

	/**
	 * Set the root path to the settings folder
	 *
	 * @access private
	 *
	 * @param string $rootPath
	 * @throws Exception if path is empty
	 *
	 * @since 1.2
	 */
	private function _setRootPath( $path )
	{
		if( '' === $path ) {
			throw new Exception( 'input parameter $rootPath is empty.' );
		} else {
			$this->rootPath = $path;
		}
	}

	/**
	 * Set the URI to the settings folder
	 *
	 * @access private
	 * 
	 * @param string $rootPath
	 * 
	 * @throws Exception if uri is empty
	 *
	 * @since 1.2
	 */
	private function _setRootUri( $uri )
	{
		if( '' === $uri ) {
			throw new Exception( 'input parameter $rootUri is empty.' );
		} else {
			$this->rootUri = $uri;
		}
	}

	/**
	 * Set the file extension of the setting
	 *
	 * @access private
	 * 
	 * @param string $ext
	 * 
	 * @throws Exception if ext is empty else string extension
	 *
	 * @since 1.2
	 */
	private function _setExt( $ext )
	{
		if( '' === $ext ) {
			throw new Exception( 'input parameter $ext is empty.' );
		} else {
			$this->ext = $ext;
		}
	}

	/**
	 * Set the namespace of the setting
	 *
	 * All settings should have the same prefix
	 *
	 * @access private
	 * 
	 * @param string $nameSpace
	 * 
	 * @throws Exception if namespace is empty else string prefix
	 *
	 * @since 1.2
	 */
	private function _setNamespace( $nameSpace )
	{
		if( '' === $nameSpace ) {
			throw new Exception( 'input parameter $nameSpace is empty.' );
		} else {
			$this->nameSpace = $nameSpace;
		}
	}
}