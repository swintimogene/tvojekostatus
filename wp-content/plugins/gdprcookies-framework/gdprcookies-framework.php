<?php
/*
Main plugin file for gdprcookies Framework
 
@author $Author: NULL $
@version $Id: gdprcookies-framework.php 183 2018-03-09 13:11:24Z NULL $
 
Plugin Name: gdprcookies Framework
Plugin URI:
Description: Framework to help WordPress developers building Plugins
Author: gdprcookies Plugins
Version: 1.4.8
Author URI: http://www.gdprcookies-plugins.com/about-gdprcookies-plugins/
License: GPL v3

gdprcookies Framework - A WordPress Plugin Framework to help WordPress developers building Plugins.

Copyright (C) 2013 - 2018, Vincent Weber webRtistik

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


// Define paths
$pathWfSrc = dirname( __FILE__ ) . '/src';
$pathWfinc = dirname( __FILE__ ) . '/inc';

// Load source classes

// Interfaces
if( !interface_exists( 'igdprcMultilangPlugin' )          ) { require_once $pathWfSrc . '/multilang/gdprcMultilangPlugin.php'; }

// Classes
if( !class_exists( 'gdprcSrc' )                           ) { require_once $pathWfSrc . '/gdprcSrc.php'; }
if( !class_exists( 'gdprcException' )                     ) { require_once $pathWfSrc . '/notices/gdprcException.php'; }
if( !class_exists( 'gdprcNotices' )                       ) { require_once $pathWfSrc . '/notices/gdprcNotices.php'; }
if( !class_exists( 'gdprcAjaxHelper' )                    ) { require_once $pathWfSrc . '/helpers/gdprcAjaxHelper.php'; }	
if( !class_exists( 'gdprcXmlSettingsHelper' )             ) { require_once $pathWfSrc . '/helpers/gdprcXmlSettingsHelper.php'; }
if( !class_exists( 'gdprcFormHelper' )                    ) { require_once $pathWfSrc . '/helpers/gdprcFormHelper.php'; }
if( !class_exists( 'gdprcMiscHelper' )                    ) { require_once $pathWfSrc . '/helpers/gdprcMiscHelper.php'; }
if( !class_exists( 'gdprcMultisiteHelper' )               ) { require_once $pathWfSrc . '/helpers/gdprcMultisiteHelper.php'; }
if( !class_exists( 'gdprcMultilangHelper' )               ) { require_once $pathWfSrc . '/helpers/gdprcMultilangHelper.php'; }
if( !class_exists( 'gdprcRemoteHelper' )                  ) { require_once $pathWfSrc . '/helpers/gdprcRemoteHelper.php'; }
if( !class_exists( 'gdprcCookieHelper' )                  ) { require_once $pathWfSrc . '/helpers/gdprcCookieHelper.php'; }
if( !class_exists( 'gdprcBaseSettings' )                  ) { require_once $pathWfSrc . '/settings/gdprcBaseSettings.php'; }
if( !class_exists( 'gdprcPluginSettings' )                ) { require_once $pathWfSrc . '/settings/gdprcPluginSettings.php'; }
if( !class_exists( 'gdprcPluginGlobals' )                 ) { require_once $pathWfSrc . '/settings/gdprcPluginGlobals.php'; }
if( !class_exists( 'gdprcBaseSettingProcessor' )          ) { require_once $pathWfSrc . '/settings/gdprcBaseSettingProcessor.php'; }
if( !class_exists( 'gdprcPluginSettingsProcessor' )       ) { require_once $pathWfSrc . '/settings/gdprcPluginSettingsProcessor.php'; }
if( !class_exists( 'gdprcPluginSettingsPage' )            ) { require_once $pathWfSrc . '/settings/gdprcPluginSettingsPage.php'; }
if( !class_exists( 'gdprcBaseMultilangProcessor' )        ) { require_once $pathWfSrc . '/multilang/gdprcBaseMultilangProcessor.php'; }
if( !class_exists( 'gdprcMultilangProcessor' )            ) { require_once $pathWfSrc . '/multilang/gdprcMultilangProcessor.php'; }
if( !class_exists( 'gdprcMultilangWpml' )                 ) { require_once $pathWfSrc . '/multilang/gdprcMultilangWpml.php'; }
if( !class_exists( 'gdprcTemplate' )                      ) { require_once $pathWfSrc . '/template/gdprcTemplate.php'; }
if( !class_exists( 'gdprcPluginModuleTemplateProcessor' ) ) { require_once $pathWfSrc . '/template/gdprcPluginModuleTemplateProcessor.php'; }
if( !class_exists( 'gdprcPluginModule' )	 					       ) { require_once $pathWfSrc . '/modules/gdprcPluginModule.php'; }
if( !class_exists( 'gdprcBaseModuleProcessor' )           ) { require_once $pathWfSrc . '/modules/gdprcBaseModuleProcessor.php'; }
if( !class_exists( 'gdprcPluginModuleProcessor' )         ) { require_once $pathWfSrc . '/modules/gdprcPluginModuleProcessor.php'; }
if( !class_exists( 'gdprcTaxonomy' )                      ) { require_once $pathWfSrc . '/posttype/gdprcTaxonomy.php'; }
if( !class_exists( 'gdprcPostType' )                      ) { require_once $pathWfSrc . '/posttype/gdprcPostType.php'; }
if( !class_exists( 'gdprcAssets' )                        ) { require_once $pathWfSrc . '/assets/gdprcAssets.php'; }
if( !class_exists( 'gdprcHooks' )                         ) { require_once $pathWfSrc . '/hooks/gdprcHooks.php'; }
if( !class_exists( 'gdprcHooksAdmin' )                    ) { require_once $pathWfSrc . '/hooks/gdprcHooksAdmin.php'; }
if( !class_exists( 'gdprcHooksFrontend' )                 ) { require_once $pathWfSrc . '/hooks/gdprcHooksFrontend.php'; }
if( !class_exists( 'gdprcShortcodes' )                    ) { require_once $pathWfSrc . '/shortcodes/gdprcShortcodes.php'; }
if( !class_exists( 'gdprcFy' )                            ) { require_once $pathWfSrc . '/gdprc/gdprcFy.php'; }
if( !class_exists( 'gdprcCore' )                          ) { require_once $pathWfSrc . '/gdprc/gdprcCore.php'; }
if( !class_exists( 'gdprcUpgrader' )                      ) { require_once $pathWfSrc . '/gdprc/gdprcUpgrader.php'; }

// Framework core includes
if( !class_exists( $pathWfinc . '/core/gdprcFwActivationHandler.php' )   ) { require_once $pathWfinc . '/core/gdprcFwActivationHandler.php'; }
if( !class_exists( $pathWfinc . '/core/gdprcFwDeactivationHandler.php' ) ) { require_once $pathWfinc . '/core/gdprcFwDeactivationHandler.php'; }
// Handle framework activation and deactivation
gdprcFwActivationHandler::addHooks( __FILE__ );
gdprcFwDeactivationHandler::addHooks( __FILE__ );	

if( !class_exists( 'gdprcookiesFramework' ) ) {
		
	/**
	 * gdprcookiesFramework Class
	 * 
	 * This is the main Plugin class for gdprcookiesFramework
	 * 
	 * @author $Author: NULL $
	 * @version $Id: gdprcookies-framework.php 183 2018-03-09 13:11:24Z NULL $
	 * @since 1.0
	 */
	class gdprcookiesFramework extends gdprcCore {
		
		/**
		 * Current version of the gdprcookies Framework
		 *
		 * @var string
		 * 
		 * @since 1.0.4
		 */
		const VERSION = '1.4.8';		
		
		/**
		 * Start a Plugin
		 * 
		 * @access protected
		 * 
		 * @param  string 				   $nameSpace   the plugins namespace
		 * @param  string 				   $file        the __FILE__ path of the Plugin that is extending gdprcookies Framework
		 * @param  (boolean|string)  $version     the Plugins current version
		 * @param  boolean           $doSettings  flag if settings should be initiated
		 * 
		 * @uses   gdprcCore::engine()
		 * 
		 * @since  1.0 
		 */
		protected function start( $nameSpace = '', $file = '', $version = '', $doSettings = false ) 
		{	
			parent::engine( $nameSpace, $file, __FILE__, $version, $doSettings );
		}	
	}
}