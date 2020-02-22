<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * igdprcMultilangPlugin interface
 *
 * @author $Author: NULL $
 * @version $Id: gdprcMultilangPlugin.php 132 2017-05-03 20:07:38Z NULL $
 * @since 1.2
 */
interface igdprcMultilangPlugin
{
	/**
	 * Determine if plugin is activated
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	bool
	 */
	public function isActive();

	/**
	 * Determine if plugin is ready (configured)
	 *
	 * This is optional
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	bool
	 */
	public function isReady();

	/**
	 * Get the default language code
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	string
	 */
	public function getDefaultCode();

	/**
	 * Get the default language locale
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	string
	 */
	public function getDefaultLocale();

	/**
	 * Get the active language code
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	string
	 */
	public function getActiveCode();

	/**
	 * Get the active language locale
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	string
	 */
	public function getActiveLocale();

	/**
	 * Get all language codes
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	string
	 */
	public function getAllCodes();

	/**
	 * Get all language locales
	 *
	 * This array should be indexed with the language codes
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	string
	 */
	public function getAllLocales();

	/**
	 * Get all locales indexed by language code
	 *
	 * @see		self::getAllLocales()
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	array
	 */
	public function getLangs();

	/**
	 * Get all language params (needed for the processor)
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	string
	 */
	public function getParams();

	/**
	 * Switch the language to the default
	 *
	 * @access	public
	 *
	 * @since 	1.2
	 *
	 * @return	string, the new language code
	 */
	public function switchToDefault();
}