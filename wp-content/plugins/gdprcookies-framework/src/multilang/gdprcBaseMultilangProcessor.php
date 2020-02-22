<?php
/**
 * Please see gdprcookies-framework.php for more details.
*/

/**
 * igdprcMultilangProcessor interface
*
* @author $Author: NULL $
* @version $Id: gdprcBaseMultilangProcessor.php 132 2017-05-03 20:07:38Z NULL $
* @since 1.2
*/
interface igdprcMultilangProcessor
{
	/**
	 *
	 */
	public function init();

	/**
	 * Get array with language codes (en, ..)
	 *
	 * @since 	1.2
	 *
	 * @return array with language codes
	 */
	public function getCodes();

	/**
	 * Get array with language locales (en_US, ..)
	 *
	 * @since 	1.2
	 *
	 * @return array with language codes
	 */
	public function getLocales();

	/**
	 * Get the langs option that is stored in the wp_options table
	 *
	 * @since 1.2
	 *
	 * @return bool false or empty array on failure, else an array
	 */
	public function getLangsOption();

	/**
	 * Get the active language locale
	 *
	 * @since 	1.2
	 */
	public function getActiveLocale();

	/**
	 * Get the active language code
	 *
	 * @since 	1.2
	 */
	public function getActiveCode();
}

/**
 * gdprcBaseMultilangProcessor class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcBaseMultilangProcessor.php 132 2017-05-03 20:07:38Z NULL $
 * @since 1.2
 */
abstract class gdprcBaseMultilangProcessor implements igdprcMultilangProcessor
{
	/**
	 * Flag if plugin is activated
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	public $isPluginActive = false;

	/**
	 * Name of the active multi lang plugin
	 *
	 * @var string
	 */
	public $pluginName = null;

	/**
	 * Flag if plugin setup is done
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	public $isPluginReady = false;

	/**
	 * Flag if settings are langueged
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	public $isLanguaged = false;

	/**
	 * Flag if current active lang is 'all'
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	public $isAll = false;

	/**
	 * The default language code
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	public $defaultCode = '';

	/**
	 * The default language locale
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	public $defaultLocale = '';

	/**
	 * The active language code
	 *
	 * i.e. en
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	public $activeCode = '';

	/**
	 * The active language locale
	 *
	 * // former activeLang
	 *
	 * i.e. en_US
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	public $activeLocale = '';

	/**
	 * All Available language codes
	 *
	 * NOTE: the code => locale pairs are stored in self::OPTION_NAME_LANGUAGES
	 *
	 * @since 1.2
	 *
	 * @var array
	 */
	public $codes = array();

	/**
	 * All Available locales
	 *
	 * @since 1.2
	 *
	 * @var array
	 */
	public $locales = array();

	/**
	 * All Available language codes stored in the gdprc_languages option
	 *
	 * Langs are stored as code => locale entries
	 *
	 * @since 1.2
	 *
	 * @var array
	 */
	public $gdprcLocales = array();

	public $removedCodes = array();

	public $newCodes = array();

	public $hasRemovedLang = false;

	public $hasNewLang = false;

	/**
	 * The current plugins namespace
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	protected $nameSpace = null;
		
	/**
	 * Name for the option in the wp_options that stores the languages
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	const OPTION_NAME_LANGUAGES = 'gdprc_languages';

	/**
	 * The singleton instance
	 *
	 * @access protected
	 *
	 * @since 1.2
	 *
	 * @var gdprcMultilangPlugin
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 *
	 * @param	string $nameSpace
	 * @param 	string $locale
	 * @param 	string $code
	 *
	 * @uses self::setActiveCode()
	 * @uses self::setActiveLocale()
	 *
	 * @since	1.2
	 *
	 * @throws 	Exception if namespace is not provided
	 */
	protected function __construct( $nameSpace = null, $locale = 'en_US', $code = 'en' )
	{
		if( null === $nameSpace || empty( $nameSpace ) ) {
			throw new Exception( __( 'Namespace parameter not provided', 'gdprcookies' ) );
		}

		// fall back in case no multi lang plugin is active
		$this->setActiveCode( $code );
		$this->setActiveLocale( $locale );
			
		$this->nameSpace = $nameSpace;
	}

	abstract public function reset();

	/* (non-PHPdoc)
	 * @see igdprcMlSettings::getCodes()
	 */
	public function getCodes()
	{
		return $this->codes;
	}

	/* (non-PHPdoc)
	 * @see igdprcMlSettings::getLocales()
	 */
	public function getLocales()
	{
		return $this->locales;
	}

	/**
	 * Get all locales indexed by language code
	 *
	 * @uses self::getLocales()
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public function getLangs()
	{
		return $this->getLocales();
	}

	/**
	 * @return string
	 */
	public function getActiveCode()
	{
		return $this->activeCode;
	}

	/**
	 * @return string
	 */
	public function getActiveLocale()
	{
		return $this->activeLocale;
	}

	/**
	 * @return string
	 */
	public function getDefaultCode()
	{
		return $this->defaultCode;
	}

	/**
	 * @return string
	 */
	public function getDefaultLocale()
	{
		return $this->defaultLocale;
	}

	/* (non-PHPdoc)
	 * @see igdprcMlSettings::getLangsOption()
	 */
	public function getLangsOption()
	{
		return get_option( self::OPTION_NAME_LANGUAGES, array() );
	}

	/**
	 * When activating, update the gdprc languages
	 *
	 * Save the current languages to the wp_options table
	 *
	 * @access public
	 *
	 * @uses self::deleteLangsOption() to delete the gdprc languages if the plugin is not ready
	 * @uses self::getLocales()
	 * @uses self::updateLangsOption()
	 *
	 * @since 1.2
	 */
	public function activatePlugin()
	{
		if( !$this->isPluginReady ) {
			$this->deleteLangsOption();
		} else {
			$locales = $this->getLocales();
			$this->updateLangsOption( $locales );
		}
	}

	/**
	 * Set the default language data parameters
	 *
	 * @acces protected
	 *
	 * @param	string	$code
	 * @param	string	$locale
	 *
	 * @uses	self::setDefaultCode()
	 * @uses	self::setDefaultLocale()
	 *
	 * @since 1.2
	 */
	protected function setParamsDefault( $code = '', $locale = '' )
	{
		$this->setDefaultCode( $code );
		$this->setDefaultLocale( $locale );
	}

	/**
	 * Set the active language data parameters
	 *
	 * @access protected
	 *
	 * @param	string	$code
	 * @param	string	$locale
	 *
	 * @uses	self::setActiveCode()
	 * @uses	self::setActiveLocale()
	 *
	 * @since 1.2
	 */
	protected function setParamsActive( $code = '', $locale = '' )
	{
		$this->setActiveCode( $code );
		$this->setActiveLocale( $locale );
	}

	/**
	 * Set the code and locales available in the current install
	 *
	 * @access protected
	 *
	 * @param	array	$codes
	 * @param	array	$locales indexed by the languag codes
	 *
	 * @uses	self::setCodes()
	 * @uses	self::setLocales()
	 *
	 * @since 1.2
	 */
	protected function setParamsAll( $codes = array(), $locales = array() )
	{
		$this->setCodes( $codes );
		$this->setLocales( $locales );
	}

	/**
	 * Set the codes
	 *
	 * @access protected
	 *
	 * @since 1.2
	 *
	 * @param array $codes
	 */
	protected function setCodes( $codes = array() )
	{
		$this->codes = $codes;
	}

	/**
	 * Set the locales
	 *
	 * @access protected
	 *
	 * @since 1.2
	 *
	 * @param array $locales
	 */
	protected function setLocales( $locales = array() )
	{
		$this->locales = $locales;
	}

	/**
	 * Get sync statuses for each language
	 *
	 * The languages stored in the wp_options table (self::OPTION_NAME_LANGUAGES)
	 * are compared to against the available languages from the current plugin.
	 *
	 * The following cases are asigned to each plugin language:
	 *
	 * 		0: lang does not exist (new)
	 * 		1: lang already exist
	 * 		2: lang has been removed
	 *
	 * @acces 	protected
	 *
	 * @param	array $gdprcLocales
	 *
	 * @since 	1.2
	 *
	 * @return 	array
	 */
	protected function getLangSyncStatusses( $gdprcLocales )
	{
		$locales = $this->getLocales();
		if( empty( $locales ) ) {
			return array();
		}

		$exist = array();
		foreach ( $locales as $code => $lang ) {
			$exist[$code] = 0;
		}

		foreach ( $exist as $code => $status ) {
			if( array_key_exists( $code, $gdprcLocales ) ) {
				// lang exists already
				$exist[$code] = 1;
			} else {
				// lang does not exist
			}
		}

		// languages that exist in the old array but not in the current are labled with 2,
		// they can be removed.
		foreach ( $gdprcLocales as $gdprcCode => $gdprcLocale ) {
			if( !array_key_exists( $gdprcCode, $exist ) ) {
				$exist[$gdprcCode] = 2;
			}
		}

		return $exist;
	}

	/**
	 * Get plugin instance
	 *
	 * @since 1.2
	 *
	 * @return gdprcMultilangPlugin
	 */
	public function getPluginInstance()
	{
		return self::$instance;
	}

	/**
	 * Update the langs option in the wp_options table
	 *
	 * @see 	self::OPTION_NAME_LANGUAGES
	 *
	 * @access 	protected
	 *
	 * @param 	array $locales
	 *
	 * @uses 	update_option()
	 *
	 * @return	bool
	 */
	protected function updateLangsOption( $locales = array() )
	{
		return update_option( self::OPTION_NAME_LANGUAGES, $locales );
	}

	/**
	 * Delete the langs option in the wp_options table
	 *
	 * @see self::OPTION_NAME_LANGUAGES
	 *
	 * @access protected
	 *
	 * @uses delete_option()
	 *
	 * @since 1.2
	 */
	protected function deleteLangsOption()
	{
		delete_option( self::OPTION_NAME_LANGUAGES );
	}

	/**
	 * Sync the settings languages with active WPML languages
	 *
	 * @access	protected
	 *
	 * @param 	gdprcPluginSettingsProcessor $settingsProcessor
	 *
	 * @throws	Exception if unknown code is found
	 *
	 * @since 	1.2
	 */
	protected function sync( $settingsProcessor = false )
	{
		try {
			if( !$this->isLanguaged ) {
				return;
			}

			$defaultLocale = null;
			$newCodes = array();
			$settings = array();

			// get the languages as stored in the wp_options table
			$gdprcLocales = $this->getLangsOption();

			$exist = $this->getLangSyncStatusses( $gdprcLocales );
			if( isset( $exist['all'] ) ) {
				unset($exist['all']);
			}

			if( !in_array( 0 , $exist, true ) && !in_array( 2 , $exist ) ) {
				gdprcNotices::add( $this->nameSpace, __( 'All langugaes are in sync. Nothing to sync.', 'gdprcookies' ), 'updated', false );
				return;
			}

			foreach ( $this->locales as $code => $locale ) {
				$newCodes[$code] = $locale;
			}

			if( $this->hasNewLang || $this->hasRemovedLang ) {
				// update the language entry in the wp_options table
				$this->updateLangsOption( $newCodes );
			}

			// loop through all settings
			if( is_a( $settingsProcessor, 'gdprcPluginSettingsProcessor' ) ) {
				$settings = $settingsProcessor->getSettings( $this->nameSpace );
				
				foreach ( $settings as $index => $setting ) {
					$option = $setting->getOption();
					$optionNew = $option;
					$defaultLocaleExist = array_key_exists( $this->defaultLocale, $option );
						
					if( $defaultLocaleExist ) {
						$defaultStack = $option[$this->defaultLocale];
					} else {
						$firstLocale = array_shift( array_keys( $option ) );
						$defaultStack = $option[$firstLocale];
					}
						
					foreach( $exist as $code => $action ) {
						if( 0 === $action ) {
							$locale = gdprcWpmlHelper::mapCodeToLocale( $code, $this->locales );
							$optionNew[$locale] = $defaultStack;
							//remove_action( 'set_object_terms', array( 'WPML_Terms_Translations', 'set_object_terms_action' ), 10 );
						} elseif( 1 === $action ) {
							continue;
						} elseif( 2 === $action ) {
							$locale = $gdprcLocales[$code];
							unset( $optionNew[$locale] );
						} else {
							throw new Exception( sprintf( 'Parameter $action with value "%d" is not valid.', $action ) );
						}
				
						//self::switchLanguage( $code );
						gdprcWpmlHelper::switchLanguage( $code );
				
						/**
						 * Filter the options array before updating
						 *
						 * Let other modules hook into the process of syncing
						 *
						 * @param array 	$optionNew
						 * @param string 	$index
						 * @param string 	$code
						 * @param string 	$defaultCode
						 * @param string 	$locale
						 * @param int 		$action
						 *
						 * @since 2.2.7
						 */
						$optionNew = apply_filters( $this->nameSpace . '_wpml_sync_option_for_locale', $optionNew, $index, $code, $this->defaultCode, $locale, $action );
					}
						
					$setting->updateOptionWithData( $optionNew );
					unset( $optionNew, $option );
				}				
			}
				
			//self::switchLanguage( $this->activeCode, true );
			gdprcWpmlHelper::switchLanguage( $this->activeCode, true );

		} catch ( Exception $e ) {
			gdprcNotices::add( $this->nameSpace, $e->getMessage(), 'error', false );
		}
	}

	/**
	 * @param unknown_type $code
	 */
	private function setActiveCode( $code = '' )
	{
		$this->activeCode = $code;
	}

	/**
	 * @param unknown_type $Locale
	 */
	private function setActiveLocale( $Locale = '' )
	{
		$this->activeLocale = $Locale;
	}

	/**
	 * @param unknown_type $code
	 */
	private function setDefaultCode( $code = '' )
	{
		$this->defaultCode = $code;
	}

	/**
	 * @param unknown_type $Locale
	 */
	private function setDefaultLocale( $Locale = '' )
	{
		$this->defaultLocale = $Locale;
	}
}