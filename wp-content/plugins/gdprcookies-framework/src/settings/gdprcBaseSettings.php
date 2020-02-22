<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * Stack interface
 *
 * @author $Author: NULL $
 * @version $Id: gdprcBaseSettings.php 156 2017-06-15 17:11:16Z NULL $
 * @since 0.1
 */
interface iStack extends ArrayAccess {	
	
	public function getStack ();

	public function setStack( $data );
		
}

/**
 * igdprcSettings interface
 * 
 *
 *
 * @author $Author: NULL $
 * @version $Id: gdprcBaseSettings.php 156 2017-06-15 17:11:16Z NULL $
 * @since 0.1
 */
interface igdprcSettings extends iStack {
	
	/**
	 * Save the settings to the database
	 */
	public function setOption();	
	
	/**
	 * Set default settings to the stack
	 */
	public function setDefaults(); 
	
	/**
	 * Get the settings name
	 */
	public function getOptionName();
	
	/**
	 * Get the settings from the database
	 */
	public function getOption();	
	
	/**
	 * Delete the settings from the database 
	 * 
	 * @param string $name
	 */
	public function deleteOption( $name = '' );
	
}

/**
 * gdprcBaseSettings Class
 *
 * Base class for handeling WordPress Settings
 * 
 *  @todo: ArrayAccess add to impement..
 *
 * @author $Author: NULL $
 * @version $Id: gdprcBaseSettings.php 156 2017-06-15 17:11:16Z NULL $
 * @since 0.1
 */
abstract class gdprcBaseSettings implements igdprcSettings {

	/**
	 * The name of the setting
	 * 
	 * This is the name that will be added to the 'wp_options' table 
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected $optionName;
	
	/**
	 * @var string the active language
	 *
	 * @since 1.1.3
	 *
	 * Default is en_US
	 */
	protected $locale;	

	/**
	 * @var array all languages
	 *
	 * @since 1.1.3
	 *
	 */
	protected $locales = array();	
	
	/**
	 * Flag if a locale is present
	 * 
	 * @since 1.1.3
	 * 
	 * @var bool
	 */
	protected $hasLocale = false;
	
	
	/**
	 * Flag if current setting is indexed by locales
	 * 
	 * @since 1.1.8
	 * 
	 * @var bool
	 */
	protected $optionHasLocale = false;
	
	
	/**
	 * Flag if a multiple locales are present
	 * 
	 * @since 1.1.3
	 * 
	 * @var bool
	 */
	protected $hasMultipleLocales = false;
	
	
	/**
	 * Stack for holding all options 
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	protected $stack = array();
	
	/**
	 * Flag that indicates if settings are a first time or later
	 * 
	 * @since 0.1
	 * 
	 * @var bool
	 */	
	protected $isInit = false;
	
	

	/**
	 * Constructor
	 * 
	 * @param string $name
	 * 
	 * @since 0.1
	 */
	public function __construct( $name = '', $locale = '', $locales = array() )
	{	
		try {
						
			global $wpdb;
			$wpdb->suppress_errors();
			
			$this->_setOptionName( $name );
			
			// a locale alone makes no sense for the settings
			if( '' !== $locale &&  is_array( $locales ) && 0 === count( $locales )  ) {
				$locale = '';
			}
			
			$this->_setLocale( $locale );			
			$this->_setLocales( $locales );
			
			if( '' !== $this->locale )
				$this->hasLocale = true;
			
			// locales are only useful if more than 1
			if( is_array( $this->locales ) && !empty( $this->locales ) && 1 < count( $this->locales ) ) {
				$this->hasMultipleLocales = true;
			}
			
			$option = $this->getOption();
			
			if( false === $option )
			{
				$this->isInit = true;
				$this->setDefaults();				
				$this->mergeOptionTmp();				
				$this->setOption();
			}
			else {
				$this->isInit = false;				
				$this->_initStack( $option );						
			}		
			
		} catch( Exception $e ) {
		
			// only show Exception messages in the WP Dashboard
			if( is_admin() )
				throw $e;			
		}
	}

	
	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 * 
	 * @since 0.1
	 */
	public function offsetExists( $offset ) 
	{		
		return isset( $this->stack[$offset] );		
	}
	
	/**
	 * Get a single setting from the stack
	 *
	 * @access public
	 * 
	 * @param string $offset the settings name
	 * 
	 * @since 0.1
	 * 
	 * @return mixed
	 */
	public function offsetGet( $offset )
	{
		try {
			if( $this->offsetExists( $offset ) )	
				return $this->stack[$offset];
			else
				throw new Exception( sprintf( 'Offset "%s" does not exist in stack.', $offset ) );
		} catch( Exception $e ) {
			
			// only show Exception messages in the WP Dashboard
			if( is_admin() ) {
				// @TODO: consider solution for logging
				//gdprcNotices::add( 'none', $e->getMessage() . sprintf( ' (File: %s:%s)', $e->getFile(), $e->getLine() ) );
			}
			
			return null;
		} 		
	}
		
	/**
	 * Wrapper for offsetGet
	 * 
	 * @param string $offset
	 * 
	 * @uses self::offsetGet()
	 * 
	 * @since 1.4.0
	 * 
	 * @return mixed
	 */
	public function get( $offset ) 
	{
		return $this->offsetGet( $offset );
	}
	
	/**
	 * Get the settings name
	 *
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function getOptionName()
	{
		return $this->optionName;
	}
	
	
	/**
	 * Get settings path
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}	
	
	
	/**
	 * Get the settings from the database
	 * 
	 * The settings option is a serialized array
	 *
	 * @access public
	 * 
	 * @uses get_option()
	 * 
	 * @since 0.1
	 * 
	 * @return object or bool false on failure
	 */
	public function getOption()
	{
		return get_option( $this->optionName );
	}
	
	
	/**
	 * Get the stack with all settings stored in it
	 *
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	public function getStack()
	{
		return $this->stack;
	}	
	
	
	/**
	 * Get the locale
	 *
	 * @access public
	 * 
	 * @since 1.2
	 * 
	 * @return string
	 */
	public function getLocale()
	{
		return $this->locale;
	}
	
	
	/**
	 * Get the locales array
	 *
	 * @access public
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public function getLocales()
	{
		return $this->locales;
	}
		
	
	/**
	 * Add data to the stack
	 *
	 * @access public
	 * 
	 * @param string $offset the settings name
	 * @param mixed $value
	 * 
	 * @since 0.1
	 */
	public function offsetSet ( $offset, $value ) 
	{
		$this->stack[$offset] = $value;
	}
	
	
	/**
	 * Wrapper for offsetSet
	 * 
	 * @param string $offset the settings name
	 * @param mixed $value
	 * 
	 * @uses self::offsetSet()
	 * 
	 * @since 1.4.0
	 */
	public function set( $offset, $value )
	{
		$this->offsetSet( $offset, $value );
	}
	
	
	/**
	 * Save the settings to the database
	 * 
	 * The current settings in the stack are saved
	 * 
	 * If the current settings has multiple languages, save the option
	 * with the locales as array key
	 * 
	 * @uses add_option()
	 * 
	 * @since 0.1
	 * 
	 * @return bool true on succes
	 */
	public function setOption()
	{
		$option = array();
		
		if( $this->hasMultipleLocales )
		{
			foreach( $this->locales as $locale )
			{
				$option[$locale] = $this->stack;			
			}
		} else {
			$option = $this->stack;
		}		

		return add_option( $this->optionName , $option );
	}	
	
	
	/**
	 * Create a backup of the current setting
	 *
	 * Duplicate the settings and rename with _tmp appended
	 *
	 * @uses gdprcBaseSettings::getOption()
	 * @uses add_option()
	 *
	 * @since 1.0.7
	 */
	public function setOptionTmp() {
	
		$current = $this->getOption();
	
		$option = $this->optionName . '_tmp';
	
		add_option( $option , $current );
	}
			
	
	/**
	 * Add data to the stack
	 *
	 * @access public
	 * 
	 * @param array $data
	 * 
	 * @since 0.1
	 */
	public function setStack( $data )
	{
		$this->stack = $data;
	}	
	
	
	/**
	 * Unset an entry in the stack
	 * 
	 * @access public
	 * 
	 * @param string offset
	 * 
	 * @since 0.1
	 */
	public function offsetUnset( $offset ) 
	{
		unset( $this->stack[$offset] );	
	}


	/**
	 * Delete the settings from the database 
	 * 
	 * @access public
	 * 
	 * @param string the settings name (optional)
	 * 
	 * @uses delete_option()
	 * 
	 * @since 0.1
	 * 
	 * @return bool true on succes or false on failure
	 */
	public function deleteOption( $name = '' )
	{
		$name = ( '' !== $name) ? $name : $this->optionName;
		
		return delete_option( $name );
	}


	/**
	 * Update the option in de database directly
	 * 
	 * Using this method instead of {@link gdprcBaseSettings::setOption()} can be used to bypass the hooks used within WordPress function {@link add_option()} 
	 * 
	 * if a locale is active, update the locale only instead of the whole setting
	 * 
	 * @access public
	 * 
	 * @uses gdprcBaseSettings::_updateOption()
	 * 
	 * @since 0.1
	 * 
	 * @return Ambigous <number, false, boolean, mixed>
	 */
	public function updateOption( $locale = false )
	{		
		try {
			$locale = ( $locale && '' !== $locale ) ? $locale : $this->locale;
			
			$optionOld = $this->getOption();
			
			if( $this->hasLocale && $this->hasMultipleLocales )
				$optionOld[$locale] = $this->stack;
			else 
				$optionOld = $this->stack;
	
			$result = $this->_updateOption( $optionOld );
			
		} catch ( Exception $e ) {
			
			// only show Exception messages in the WP Dashboard
			if( is_admin() )
				throw $e;
		}
		
		return $result;
	}	
	
	
	/**
	 * Update the current setting with given data array
	 * 
	 * @access	public
	 * 
	 * @since 	1.2	@todo verify
	 * 
	 * @param	array	$data
	 * 
	 * @uses	self::_updateOption()
	 * 
	 * @return 	boolean|Ambigous <Ambigous, number, false, boolean, mixed>
	 */
	public function updateOptionWithData( $data = null ) 
	{
		if( null === $data )
			return false;
		
		return $this->_updateOption( $data );
	}

	
	/**
	 * Check if current settings option exist
	 * 
	 * @access public
	 * 
	 * @uses gdprcBaseSettings::getOption() 
	 * 
	 * @since 1.1.7
	 * 
	 * @return bool
	 */
	public function hasOption() 
	{
		return ( $this->getOption() ) ? true : false;		
	}
	
	
	/**
	 * Check if current settings should has locales
	 * 
	 * Passed throug the constructor
	 *
	 * @access public
	 *
	 * @since 1.2
	 *
	 * @return bool
	 */
	public function hasLocale()
	{
		return $this->hasLocale;
	}
	
	
	
	/**
	 * Check if current settings option has locales
	 * 
	 * @access public
	 * 
	 * @since 1.1.8
	 * 
	 * @return bool
	 */
	public function hasOptionLocale() 
	{
		return $this->optionHasLocale;
	}
	
	
	/**
	 * Merge temp settings option with current settings
	 * 
	 * This ensures to maintain previous settings when updating the Plugin
	 * 
	 * @access 	protected
	 * 
	 * @uses	self::_mergeOptionTmp() @since 1.2
	 * @uses 	get_option()
	 * @uses 	self::setStack()
	 * @uses 	self::deleteOption()
	 * 
	 * @since 1.0.7
	 */
	protected function mergeOptionTmp() 
	{	
		$optionTmp = $this->optionName . '_tmp';
		
		if( false !== ( $backup = get_option( $optionTmp ) ) ) {
			
			$also = array();
			if( isset( $backup['_gdprc_bckp_also'] ) )
			{
				$also = $backup['_gdprc_bckp_also'];
				unset( $backup['_gdprc_bckp_also'] );
			}
			
			// for multi lang installs, correct the $backup
			if( $this->hasLocale && $this->hasMultipleLocales ) 
			{				
				$mergedStack = array();
				foreach( $this->locales as $locale ) {
	
					$backupLocale = $backup[$locale];					
					$mergedStack[$locale] = $this->_mergeOptionTmp( $backupLocale, $also );
				}
				
				$this->setStack( $mergedStack[$this->locale] );
				add_option( $this->optionName , $mergedStack );
				$this->deleteOption( $optionTmp );
			
			} else {
				
				$mergedStack = $this->_mergeOptionTmp( $backup, $also );

				$this->setStack( $mergedStack );
				$this->deleteOption( $optionTmp );				
			}
		}		
	}	
	
	
	/**
	 * @Todo descr
	 * 
	 * @param unknown_type $optionOld
	 * @param unknown_type $name
	 * 
	 * @uses wpdb::update()
	 * 
	 * @return Ambigous <number, false, boolean, mixed>
	 */
	protected function _updateOption( $optionOld, $name = '' ) 
	{
		global $wpdb;
		
		$option = maybe_serialize( $optionOld );		
		$name = ( '' !== $name ) ? $name : $this->optionName;
		
		// from wp-includes/option.php update_option() around line 299
		if ( ! defined( 'WP_INSTALLING' ) ) {
			$alloptions = wp_load_alloptions();
			if ( isset( $alloptions[$name] ) ) {
				$alloptions[$name] = $option;
				wp_cache_set( 'alloptions', $alloptions, 'options' );
			} else {
				wp_cache_set( $name, $option, 'options' );
			}
			unset( $alloptions );
		}
		
		unset( $optionOld );
		
		return $wpdb->update( $wpdb->options, array( 'option_value' => $option ), array( 'option_name' => $name ) );
		
		/*
		if( false === $result ) {
			throw new Exception(  sprintf( 'Database error during updating setting %s with error <strong>%s</strong>', $name, $wpdb->last_error ) );
		}
		elseif( 0 === $result ) {
			return update_option( $name , $option );
		} else {
			return $result;
		}
		*/		
	}
	
	
	/**
	 * Set the stack for the current request and handles locales
	 * 
	 * @access private
	 * 
	 * @param array $option
	 * 
	 * @uses gdprcBaseSettings::setStack()
	 * @uses gdprcBaseSettings::updateOptionWithData()
	 */
	private function _initStack( $option ) 
	{
		if( !is_array( $option ) ) {
			throw new Exception( sprintf( 'input parameter $option is not an array for %s.', $this->optionName ) );
		}
		
		$keys = array_keys( $option );		
		$firstKey = $keys[0];
				
		$this->optionHasLocale = gdprcMultilangHelper::isLanguagedOption( $option );		
		
		// check if current settings option array is with multi language keys
		if( $this->hasLocale && $this->optionHasLocale && in_array( $this->locale, $keys ) ) 
		{
			$this->setStack( $option[$this->locale] );
		}
		elseif( $this->hasLocale && $this->optionHasLocale && !in_array( $this->locale, $keys ) ) 
		{	
			$this->setStack( $option[$firstKey] );
			$this->updateOptionWithData( $option + array( $this->locale => $option[$firstKey] ) );
		}
		elseif( $this->hasLocale && !$this->optionHasLocale && $this->hasMultipleLocales ) 
		{				
			$this->setStack( $option );
			$this->updateOptionWithData( array( $this->locale => $option ) );
		}
		elseif( !$this->hasLocale && $this->optionHasLocale ) 
		{			
			$wpLocale = get_locale(); 
			$key = ( in_array( $wpLocale , $keys ) ) ? $wpLocale : $firstKey;			
			$this->setStack( $option[$key] );
			$this->updateOptionWithData( $option[$key] );
		}
		else {			
			$this->setStack( $option );
		}		
	}
	
	
	/**
	 * Merge the current stack with a given backup array
	 * 
	 * @access	private
	 * 
	 * @param 	array	$backup
	 * @param 	array	$also entries that needs to be merged even if not present in current stack
	 * 
	 * @since 	1.2
	 * 
	 * @return array 
	 */
	private function _mergeOptionTmp( $backup = array(), $also = array() ) {
		
		$mergedStack = array();
		$stack = $this->getStack();
		foreach( $stack as $k => $v ) {
		
			if( isset( $backup[$k] ) )
				$mergedStack[$k] = $backup[$k];
			else
				$mergedStack[$k] = $v;
		}
			
		if( !empty( $also ) )
		{
			foreach ( $also as $k ) {
				if( !isset( $mergedStack[$k] ) && isset( $backup[$k] ) )
					$mergedStack[$k] = $backup[$k];
			}
		}
		
		return $mergedStack;		
	}
	
	
	/**
	 * Set the option name
	 * 
	 * @access private
	 * 
	 * @param string $name
	 * @throws Exception if $name is empty
	 * 
	 * @since 0.1
	 */
	private function _setOptionName( $name = '' )
	{
		if( '' === $name )
			throw new Exception( 'input parameter $name is empty.' );
		else
			$this->optionName = $name;
	}
	
	
	/**
	 * Set the locale
	 *
	 * @access private
	 *
	 * @param string $locale
	 * @throws Exception if $locale is not a string
	 *
	 * @since 1.1.3
	 */
	private function _setLocale( $locale )
	{
		if( !is_string( $locale ) )
			throw new Exception( 'input parameter $locale is not a string for setting: ' . $this->optionName );
		else 
			$this->locale = $locale;
	}

	/**
	 * Set the locales
	 *
	 * @access private
	 *
	 * @param string $locales
	 * @throws Exception if $locales is not an array
	 *
	 * @since 1.1.3
	 */
	private function _setLocales( $locales )
	{
		if( !is_array( $locales ) )
			throw new Exception( 'input parameter $locales is not an array for setting: ' . $this->optionName );
		else
			$this->locales = $locales;
	}	
}