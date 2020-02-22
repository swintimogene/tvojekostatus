<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcMultilangWpml interface
 *
 * @author $Author: NULL $
 * @version $Id: gdprcMultilangWpml.php 132 2017-05-03 20:07:38Z NULL $
 * @since 1.2
 */
final class gdprcMultilangWpml implements igdprcMultilangPlugin
{
	/**
	 * Flag if WPML API is loaded
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	public $apiLoaded = false;

	/**
	 * The data for the current active language
	 *
	 * Data from gdprcWpmlHelper::getActiveLangData()
	 *
	 * @since 1.2
	 *
	 * @var array
	 */
	public $activeLanguageData = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->activeLanguageData = $this->getActiveLanguageData();

		$this->_includeApi();
	}


	public function isActive()
	{
		return gdprcWpmlHelper::isActive();
	}

	public function isReady()
	{
		return gdprcWpmlHelper::isReady();
	}

	public function getActiveLanguageData()
	{
		return gdprcWpmlHelper::getActiveLanguageData();
	}

	public function getDefaultCode()
	{
		return gdprcWpmlHelper::getDefaultCode();
	}

	public function getDefaultLocale()
	{
		$code = $this->getDefaultCode();
		$locale = gdprcWpmlHelper::mapCodeToLocale( $code, $this->getLangs() );

		return $locale;
	}

	public function getActiveCode()
	{
		return gdprcWpmlHelper::getActiveCode();
	}

	public function getActiveLocale()
	{
		return gdprcWpmlHelper::getActiveLocale();
	}

	public function getActiveLangData() {
		return gdprcWpmlHelper::getActiveLangData();
	}

	public function getAllCodes()
	{
		return gdprcWpmlHelper::getAllCodes();
	}

	public function getAllLocales()
	{
		return gdprcWpmlHelper::getAllLocales();
	}

	public function getLangs()
	{
		return $this->getAllLocales();
	}

	public function getParams()
	{
		$rtrn = new stdClass();
		$rtrn->isActive			= $this->isActive();
		$rtrn->isReady			= $this->isReady();
		$rtrn->defaultCode 		= $this->getDefaultCode();
		$rtrn->defaultLocale 	= $this->getDefaultLocale();
		$rtrn->activeCode		= $this->getActiveCode();
		$rtrn->activeLocale		= $this->getActiveLocale();
		$rtrn->allCodes			= $this->getAllCodes();
		$rtrn->allLocales		= $this->getAllLocales();

		return $rtrn;
	}

	public function switchToDefault()
	{
		return gdprcWpmlHelper::switchLanguageToDefault();
	}

	public function switchLanguage( $code, $cookie  )
	{
		return gdprcWpmlHelper::switchLanguage( $code, $cookie );
	}

	/**
	 * Inlude the WPML API code
	 *
	 * if succeeded, set flag self::apiLoaded to true
	 *
	 * @acces private
	 *
	 * @since 1.2
	 *
	 * @return boolean
	 */
	private function _includeApi()
	{
		if( defined( 'ICL_PLUGIN_PATH' ) ) {
			@require_once ICL_PLUGIN_PATH . '/inc/wpml-api.php';

			if( defined( 'WPML_API_SUCCESS' ) ) {
				$this->apiLoaded = true;
			}
			if( !defined( 'WPML_LOAD_API_SUPPORT' ) ) {
				define( 'WPML_LOAD_API_SUPPORT', true );
			}
		}
		else {
			return false;
		}
	}
}