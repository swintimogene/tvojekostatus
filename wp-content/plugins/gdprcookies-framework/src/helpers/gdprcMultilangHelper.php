<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */


class gdprcMultilangHelper {
		
	
	/**
	 * Check if given option is indexed with locales
	 *
	 * @acces	public
	 *
	 * @param	array $option
	 *
	 * @since 	1.2
	 *
	 * @return 	bool
	 */
	public static function isLanguagedOption( $option = array() )
	{
		if( !is_array( $option ) || empty( $option ) )
			return false;
	
		$keys = array_keys( $option );
		$firstKey = $keys[0];
	
		return ( ( preg_match( '/^[a-z]{2}_[A-Z]{2}$/', $firstKey, $m ) ) ) ? true : false;
	}
	
	
	/**
	 * Map a locale format to a language code
	 *
	 * For example: from the 'locale' string "en_US", the first part "en" will be returned
	 *
	 * @acces public
	 *
	 * @param string $locale the locale
	 *
	 * @uses preg_match()
	 *
	 * @since 1.2
	 *
	 * @return string, empty on failure
	 */
	public static function mapLocaleToCode( $locale = '' )
	{
		if( '' === $locale )
			return '';
	
		$code = '';
	
		if( preg_match( '/^([a-z]{2})_([A-Z]{2})$/', $locale, $m ) ) {
	
			if( isset( $m[1] ) && 2 === strlen($m[1]) )
				$code = $m[1];
		}
	
		return $code;
	}
	

	/**
	 * Load the plugin text domain
	 *
	 * @param string $nameSpace
	 * @param string $pluginRelPath
	 *
	 * @since 1.4.0
	 *
	 * @uses load_plugin_textdomain()
	 */
	public static function loadPluginTextDomain( $nameSpace = '', $pluginRelPath = '' )
	{
		load_plugin_textdomain( $nameSpace, false,  $pluginRelPath );
	}	
}


/**
 * gdprcWpmlHelper class
 * 
 * Helper class that helps with WPML functions
 *  
 * @author $Author: NULL $
 * @version $Id: gdprcMultilangHelper.php 141 2017-05-08 16:02:54Z NULL $
 * 
 * @since 1.2
 */
class gdprcWpmlHelper extends gdprcMultilangHelper {	
	
	
	/**
	 * Get the current active language data
	 *
	 * @acces public
	 *
	 * @uses self::getLangs()
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public static function getActiveLanguageData()
	{
		static $cache = null;
	
		if( null !== $cache )
			return $cache;
	
		$langs = self::getLangs();
		$data = array();
	
		foreach ( $langs as $code => $data ) {
	
			if(  '1' === $data['active'] ) break;
		}
	
		if( !empty( $data ) )
			$cache = $data;
			
		return $data;
	}
	
	
	
	/**
	 * Get the active language code
	 *
	 * @acces public
	 *
	 * @uses self::getLangs()
	 *
	 * @since 1.2
	 *
	 * @return string the language code or bool false on failure
	 */
	public static function getActiveCode()
	{
		static $cache = null;
	
		if( null !== $cache )
			return $cache;
		
		$langs = self::getLangs();
		
		$activeCode = false;
	
		foreach ( $langs as $code => $data ) {
	
			if(  '1' === $data['active'] )
			{
				if ( '' === $code && defined( 'ICL_LANGUAGE_CODE' ) ) { 
					$activeCode = ICL_LANGUAGE_CODE; 
				} elseif( '' !== $code ) { 
					$activeCode = $code; 
				} else {
					$activeCode = false;
				}
				
				break;
			}
		}
		
		if( !$activeCode && self::isAll() ) {
			$activeCode = 'all';
		}
	
		if( $activeCode )
			$cache = $activeCode;
			
		return $activeCode;
	}
	
	
	/**
	 * Get the active language locale
	 *
	 * @acces public
	 *
	 * @uses self::getLangs()
	 *
	 * @since 1.2
	 *
	 * @return string the locale or bool false on failure
	 */
	public static function getActiveLocale()
	{
		static $cache = null;
		
		if( null !== $cache )
			return $cache;
		
		$langs = self::getLangs();
		$activeLang = false;
	
		foreach ( $langs as $code => $data ) {
				
			if(  '1' === $data['active'] )
			{
				$activeLang = $data['default_locale'];
				break;
			}
		}
		
		if( $activeLang )
			$cache = $activeLang;
			
		return $activeLang;
	}
	
	
	/**
	 * Get all language codes
	 *
	 * @acces public
	 *
	 * @uses self::getLangs()
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public static function getAllCodes()
	{
		static $cache = null;
	
		if( null !== $cache )
			return $cache;
	
		$langs = self::getLangs();
		$codes = array();
	
		foreach ( $langs as $code => $data ) {
	
			$codes[] = $code;
		}
	
		if( !empty( $codes ) )
			$cache = $codes;
			
		return $codes;
	}
	
	
	/**
	 * Get all language locales
	 *
	 * @acces public
	 *
	 * @uses self::getLangs()
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public static function getAllLocales()
	{
		static $cache = null;
	
		if( null !== $cache )
			return $cache;
	
		$langs = self::getLangs();
		$locales = array();
	
		foreach ( $langs as $code => $data ) {
	
			$locales[$code] = $data['default_locale'];
		}
	
		if( !empty( $locales ) )
			$cache = $locales;
			
		return $locales;
	}
	
	
	
	/**
	 * Get the active language locale
	 * 
	 * For backword compatibility keep this method
	 * 
	 * @see self::getActiveLocale()
	 */
	public static function getActiveLang() 
	{
		return self::getActiveLocale();
	}
	
	
	/**
	 * Get WPML default language code
	 *
	 * @acces public
	 *
	 * @uses  icl_get_default_language()
	 * @uses  wpml_get_default_language()
	 * @uses  filter "wpml_default_language"
	 *
	 * @since 1.2
	 *
	 * @return string or bool false
	 */
	public static function getDefaultCode()
	{
		static $cache = null;
		
		if( null !== $cache )
			return $cache;
		
		if( function_exists( 'icl_get_default_language' ) )
			$code = icl_get_default_language();
		elseif( function_exists( 'wpml_get_default_language' ) )
			$code = wpml_get_default_language();
		elseif( has_filter( 'wpml_default_language' ) )
			$code = apply_filters( 'wpml_default_language', NULL );
		else 
			$code = false;

		if( false !== $code )
			$cache = $code;
		
		return $code;
	}
	
	
	/**
	 * Get all active language data
	 *
	 * @acces public
	 *
	 * @uses icl_get_languages()
	 *
	 * @since 1.2
	 *
	 * @return array or bool false on failure
	 */
	public static function getActiveLangsData()
	{
		static $cache = null;
		
		if( null !== $cache )
			return $cache;
		
		if( function_exists( 'icl_get_languages' ) )
			$langs = icl_get_languages();
		elseif( has_filter( 'wpml_default_language' ) )
			$langs = apply_filters( 'wpml_active_languages', '', array() );
		else 
			$langs = false;		
		
		if( is_array( $langs ) && !empty( $langs ) )
			$cache = $langs;
		
		return $langs;
	}	

	
	
	/**
	 * synonyme for self::getActiveLangsData()
	 *
	 * @see self::getActiveLangsData()
	 */
	public static function getLangs() 
	{
		return self::getActiveLangsData();
	}
	
	
	/**
	 * Get the current language code
	 * 
	 * Uses this function if 'all' is a possible option
	 * 
	 * @access 	public
	 * 
	 * @param	wpml_get_current_language()
	 * 
	 * @since 	1.2
	 * 
	 * @return	string
	 */
	public static function getCurrentCode()
	{
		static $cache = null;
		
		if( null !== $cache )
			return $cache;
		
		$current = wpml_get_current_language();
		
		if( '' !== $current )
			$cache = $current;
		
		return $current;		
	}	
	
	
	/**
	 * Get posst for current language only
	 * 
	 * @access	public
	 * 
	 * @param 	array $posts with WP_Post objects
	 * @param 	string $code (optional) the current language code
	 * 
	 * @uses	wpml_get_language_information()
	 * 
	 * @return bool false or array with posts
	 */
	public static function getPostsCurrentLanguage ( $posts = array(), $code = '' ) 
	{
		if( !is_array( $posts ) )
			return false;
		
		$postsCurrLang = array();		
		
		if( '' === $code )
			$code = self::getActiveCode();
		
		foreach ( $posts as $postId => $post ) {
			
			if( !is_a( $post, 'WP_Post') )
				continue;
				
			$info = wpml_get_language_information( null, $postId );						
			if( $code === $info['language_code'] )
				$postsCurrLang[] = $post;					
		}
		
		return ( !empty( $postsCurrLang ) ) ? $postsCurrLang : false;
	}
	
	
	/**
	 * Map a language code to language locale
	 *
	 * For example: for the 'code' "en", the locale "en_US" will be returned
	 *
	 * @param string $code
	 *
	 * @uses self::getAllLocales() if $langs is empty
	 *
	 * @since 1.2
	 *
	 * @return string, empty on failure
	 */
	public static function mapCodeToLocale( $code = '', $langs = array() )
	{
		if( '' == $code )
			return '';
			
		if( empty( $langs ) ) {
			$langs = self::getAllLocales();
		}
		
		if( !$langs )
			return '';
			
		$locale = '';
		if( array_key_exists( $code , $langs ) ) {
			$locale = $langs[$code];
		}
			
		return $locale;
	}
		
	
	/**
	 * @see parent::mapLocaleToCode()
	 */
	public static function mapLocaleToCode( $locale = '' ) 
	{
		return parent::mapLocaleToCode( $locale );
	}
	
	
	
	/**
	 * Switch the language
	 *
	 * If the given $code equals the active lang, return this language code
	 *
	 * @access public
	 *
	 * @param string $code
	 *
	 * @uses SitePress::icl_get_default_language()
	 * @uses SitePress::switch_lang()
	 * @uses SitePress::get_current_language()
	 *
	 * @since 1.2
	 *
	 * @return string the (switched) language code
	 */
	public static function switchLanguage( $code = '', $cookie = false )
	{
		global $sitepress;
			
		if( '' === $code ) {
			//@todo: use self::getDefaultCode();
			$code = icl_get_default_language();
		}
			
		//@TODO: use WPML API for switch_lang() and get_current_language()	

		//do_action( 'wpml_switch_language', $code );
		$sitepress->switch_lang( $code, $cookie );
	
		// return apply_filters( 'wpml_current_language', NULL );
		// or
		// return self::getCurrentCode()
		return $sitepress->get_current_language();
	}
	
	
	/**
	 * Switch the language to the default
	 *
	 * @access public
	 *
	 * @uses self::switchLanguage()
	 *
	 * @since 1.2
	 *
	 * @return string the (switched) language code
	 */
	public static function switchLanguageToDefault()
	{
		return self::switchLanguage();
	}	
	
	
	/**
	 * @see parent::isLanguagedOption()
	 */
	public static function isLanguagedOption( $option = array() ) 
	{
		return parent::isLanguagedOption( $option );	
	}
	
	
	/**
	 * Determine if current request is for 'all'
	 * 
	 * @access	public
	 * 
	 * @uses	self::getCurrentCode()
	 * 
	 * @since	1.2
	 * 
	 * @return	bool
	 */
	public static function isAll()
	{
		static $cache = null;
		
		if( null !== $cache )
			return $cache;		
		
		$current = self::getCurrentCode();
		
		$isAll = ( 'all' === $current ) ? true : false;
		
		$cache = $isAll;
		
		return $isAll;
	}
	
	
	/**
	 * Is the WPML Plugin active
	 * 
	 * @access public
	 * 
	 * @since 1.2
	 * 
	 * @return bool
	 */
	public static function isActive() 
	{
		return ( defined( 'ICL_LANGUAGE_CODE' ) && class_exists( 'SitePress' ) ) ? true : false;			
	}
	
	
	/**
	 * Is the WPML Plugin ready, i.e. is the setup complete
	 *
	 * @access public
	 *
	 * @uses SitePress::get_setting( 'setup_complete' )
	 *
	 * @since 1.2
	 *
	 * @return bool
	 */	
	public static function isReady() 
	{
		if( !self::isActive() ) 
		{
			return false;
		}
		
		global $sitepress;
		
		if( is_object( $sitepress ) && !method_exists( $sitepress, 'get_setting' ) )
			return false;
			
		//@TODO: use WPML API for get_setting() (if possible)
		return ( is_object( $sitepress ) && $sitepress->get_setting( 'setup_complete' ) ) ? true : false;
	}
}	