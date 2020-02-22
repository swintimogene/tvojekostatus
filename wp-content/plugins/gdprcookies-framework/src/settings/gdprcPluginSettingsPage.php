<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcPluginSettingsPage Class
 *
 * Class for handling Plugin Settings page
 *
 * @author $Author: NULL $
 * @version $Id: gdprcPluginSettingsPage.php 171 2018-03-03 12:25:00Z NULL $
 * @since
 */
final class gdprcPluginSettingsPage 
{	
	/**
	 * The setting page hook_suffix values
	 * 
	 * @since 1.2
	 * 
	 * @var array
	 */
	public $hooknames = array();	
	
	/**
	 * Flag if current request is for one of the settings pages
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	public $onSettingsPage = false;	
	
	/**
	 * URI to the settings page
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	public $uri;
	
	/**
	 * URI to the default settings page
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	public $uriTabDefault = null;	
	
	/**
	 * The current tab name
	 * 
	 * @since 1.2
	 * 
	 * @var string
	 */
	public $currentTabIdx = null;	
	
	/**
	 * The current tab page slug
	 * 
	 * query var 'page' value
	 * 
	 * @since 1.2
	 * 
	 * @var string
	 */
	public $currentTabSlug = null;	
	
	/**
	 * Absolute path to the Templates folder
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	private $templatePath;
	
	/**
	 * Container for the tabs
	 *
	 * @since 1.2
	 *
	 * @var array
	 */
	private $tabs = array();
	
	/**
	 * The tab flagged as default
	 * 
	 * @since 1.2
	 * 
	 * @var array
	 */
	private $defaultTab = array();	
	
	/**
	 * The tab flagged as default index
	 *
	 * @since 1.2
	 *
	 * @var string
	 */	
	private $defaultTabIdx = null;	
	
	/**
	 * Flag if more than 1 tab is available
	 * 
	 * @var bool
	 */
	private $hasTabs = false;
	
	/**
	 * The plugins namespace
	 * 
	 * @since 1.2
	 * 
	 * @var string
	 */
	private $nameSpace;
	
	/**
	 * The page title
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	private $pageTitle;
	
	/**
	 * The menu title
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	private $menuTitle;		
	
	/**
	 * Prefix that all settings pages have in the slug
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	private $slug = 'gdprc-%s-settings';	
	
	/**
	 * Page slug of the parent page
	 * 
	 * In case of multiple tabs
	 * 
	 * @since 1.2
	 *
	 * @var string
	 */
	private $parentSlug = null;	
	
	/**
	 * Container for all gdprcPluginSettings instances for the current namespace
	 * 
	 * @since 1.2
	 * 
	 * @var array
	 */
	private $settings = array();
	
	/**
	 * gdprcPluginGlobals instance
	 * 
	 * @since 1.4.0
	 * 
	 * @var gdprcPluginGlobals
	 */
	private $globals = false;
	
	/**
	 * File name for the settings JavaScript
	 *
	 * @since 0.1
	 *
	 * @var string
	 */
	const FILE_NAME_SETTINGS_JS = 'gdprc-settings';
	
	/**
	 * File name for the settings lists JavaScript
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	const FILE_NAME_SETTINGS_LISTS_JS = 'gdprc-settings-lists';
	
	/**
	 * File name for the settings page css
	 *
	 * @since 1.4.7
	 *
	 * @var string
	 */
	const FILE_NAME_SETTINGS_CSS = 'admin-settings-page';
	
	/**
	 * Template file name for the settings page wrapper
	 *
	 * @since 0.1
	 *
	 * @var string
	 */
	const TEMPL_FILE_NAME_SETTINGS_PAGE = 'gdprc-tpl-settings-page.php';	
	
	/**
	 * Template file name for the settings page content table
	 *
	 * @since 0.1
	 *
	 * @var string
	 */
	const TEMPL_FILE_NAME_SETTINGS_PAGE_TABLE = 'gdprc-tpl-settings-page-table.php';
	
	/**
	 * Template file name for one Custom Post Type list item
	 *
	 * @since 1.2.1
	 *
	 * @var string
	 */
	const TEMPL_FILE_NAME_LIST_ITEMS = 'gdprc-tpl-list-items.php';
	
	/**
	 * Template file name for one list item
	 *
	 * @since 1.2.1
	 *
	 * @var string
	 */
	const TEMPL_FILE_NAME_LIST_ITEM = 'gdprc-tpl-list-item.php';	
	
	/**
	 * constructor
	 * 
	 * @access public
	 * 
	 * @param string $pageTitle
	 * @param string $menuTitle
	 * @param gdprcPluginGlobals $globals
	 * 
	 * @since 1.2
	 */
	public function __construct( $pageTitle  = '', $menuTitle = '', $globals ) 
	{	
		if( !is_a( $globals, 'gdprcPluginGlobals' ) ) {
			throw new Exception( 'Parameter globals is not valid.' );
		}		
		
		$this->globals = $globals;
		$this->nameSpace = $this->globals->get( 'pluginNameSpace' );
		
		if( !gdprcMiscHelper::fontType( $this->nameSpace ) ) {
			throw new gdprcException( '' );
		}
		
		$this->pageTitle = $pageTitle;
		$this->menuTitle = $menuTitle;		
		
		$this->templatePath = $this->globals->get( 'wfTemplPath' );		
		$this->slug = sprintf( $this->slug, $this->nameSpace );
		
		// if the current page request has a 'page' query var added, set some class members
		if ( isset( $_GET['page'] ) && false !== strpos(  $_GET['page'], $this->slug ) ) {			
			// allow tab names as words or seperated words like general, general-special
			if( preg_match( '/^'.$this->slug.'-([a-z-]+)$/', $_GET['page'], $m ) ) {		
				$this->currentTabIdx = $m[1];
				$this->currentTabSlug = $_GET['page'];
				$this->onSettingsPage = true;
			}
		}
	}	
	
	/**
	 * Callback for the {$nameSpace}_scripts_admin hook
	 *
	 * Enqueue jQuery scripts for the settings pages
	 *
	 * @access public
	 *
	 * @param string $hook_suffix
	 * @param WP_Scripts $wp_scripts
	 * @param bool $isScriptDebug
	 *
	 * @uses wp_enqueue_script()
	 * @uses wp_localize_script()
	 *
	 * @since 1.2
	 */
	public function setScripts( $hook_suffix, $wp_scripts, $isScriptDebug )
	{
		// make sure scripts are only enqueued on the settings pages
		if( in_array( $hook_suffix, $this->hooknames ) ) 
		{
			/**
			 * This hook let other Modules / Plugins enqueue scripts on the settings pages before {$nameSpace}-settings
			 *
			 * @param string $hook_suffix
			 * @param WP_Scripts $wp_scripts
			 * @param bool $isScriptDebug
			 */
			do_action( $this->nameSpace . '_settings_scripts_before', $hook_suffix, $wp_scripts, $isScriptDebug );
			
			$jsUri = $this->globals->get( 'jsUri' );
			$wfJsUri = $this->globals->get( 'wfJsUri' );	
			$pluginPath = $this->globals->get( 'pluginPath' );
			$isScriptDebug = gdprcMiscHelper::isScriptDebug();
			$ext = ( $isScriptDebug ) ? '.js' : '.min.js';
			
			$filename = self::FILE_NAME_SETTINGS_JS . $ext;
			$filenameList = self::FILE_NAME_SETTINGS_LISTS_JS . $ext;
						
			wp_enqueue_script( 'gdprc-settings', $wfJsUri . '/'. $filename, array( 'jquery' ), false, true );
			wp_enqueue_script( 'gdprc-settings-lists', $wfJsUri . '/'. $filenameList, array( 'gdprc-settings' ), false, true );
			
			// enqueue plugin settings JS file if exists
			if( file_exists( $pluginPath . '/assets/js/settings' . $ext ) ) {
				wp_enqueue_script( $this->nameSpace.'-settings', $jsUri . '/settings' . $ext, array( 'gdprc-settings' ), false, true );
			}
	
			/**
			 * This hook let other Modules / Plugins enqueue scripts on the settings pages after {$nameSpace}-settings
			 *
			 * @param string $hook_suffix
			 * @param WP_Scripts $wp_scripts
			 * @param bool $isModeDev
			 */
			do_action( $this->nameSpace . '_settings_scripts_after', $hook_suffix, $wp_scripts, $isScriptDebug );
	
			$l10nData = array();			
			$l10nData['del_confirm_post'] = __( 'Are you sure you want to DELETE the selected items?', 'gdprcookies' );
			$l10nData['del_confirm_cat'] = __( 'Are you sure you want to DELETE the selected item?', 'gdprcookies' );
			$l10nData['post_empty'] = __( 'Cannot save item, one or more fields are empty.', 'gdprcookies' );
			$l10nData['unknown_error'] = __( 'An Unknown error occurred. Please check your latest modification(s).', 'gdprcookies' );
			$l10nData['updating'] = __( 'updating...', 'gdprcookies' );
				
			// add global JavaScript vars for $l10nData array
			wp_localize_script( 'gdprc-settings', $this->nameSpace . 'Datal10n', apply_filters( $this->nameSpace . '_settings_scripts_l10n_data',  $l10nData ) );
		}
	}	
	
	/**
	 * Callback for the {$nameSpace}_styles_admin hook
	 *
	 * Enqueue styles for the settings pages
	 *
	 * @access public
	 *
	 * @param string $hook_suffix
	 * @param WP_Styles $wp_styles
	 * @param bool $isModeDev
	 *
	 * @uses wp_enqueue_style()
	 * @uses wp_localize_script()
	 *
	 * @since 0.1
	 */
	public function setStyles( $hook_suffix, $wp_styles, $isModeDev )
	{
		// make sure styles are only enqueued on the settings pages
		if( in_array( $hook_suffix, $this->hooknames ) ) {			
			$ext = ( $isModeDev ) ? '.css' : '.min.css';
			
			wp_enqueue_style( 'gdprc-settings', $this->globals->get( 'wfCssUri' ) .'/' . self::FILE_NAME_SETTINGS_CSS . $ext );
			
			// load plugin admin settings page if exist
			if( file_exists( $this->globals->get( 'assetsPath' ) . '/css/' . self::FILE_NAME_SETTINGS_CSS . $ext ) ) {
				wp_enqueue_style( $this->nameSpace.'-settings', $this->globals->get( 'cssUri' ) . '/' . self::FILE_NAME_SETTINGS_CSS . $ext, array( 'gdprc-settings' ) );
			}
	
			/**
			 * This hook let other Modules / Plugins enqueue styles on the settings pages after {$nameSpace}-settings
			 *
			 * @param WP_Styles $wp_styles
			 * @param bool $isModeDev
			 */
			do_action( $this->nameSpace . '_settings_styles', $wp_styles, $isModeDev );
		}
	}	
	
	/**
	 * @param string $hook_suffix
	 */
	public function printStyles( $hook_suffix ) 
	{
		// make sure styles are only enqueued on the settings pages
		if( in_array( $hook_suffix, $this->hooknames ) ) {						
			$logoPath = $this->globals->get( 'pluginPath' ) . '/assets/img/logo.svg';
						
			if( !file_exists( $logoPath ) ) {
				$logoUri = $this->globals->get( 'wfImgUri' ) . '/logo-settings.svg'; 
			} else {
				$logoUri = $this->globals->get( 'imgUri' ) . '/logo.svg';
			}			
		?>			
		<style type="text/css">
		#gdprc-plugin-header .logo { background-image:url("<?php echo $logoUri ?>"); }
		<?php do_action( $this->nameSpace . '_print_settings_styles' ); ?>		
		</style>	
		<?php			
		}	
	}
	
	/**
	 * Get a tab from the tabs array
	 *
	 * @access	public
	 *
	 * @param 	$idx the tab index key
	 *
	 * @uses 	self::getTabs()
	 *
	 * @since 	1.2
	 *
	 * @return 	array the tab or empty array if tab is not found
	 */
	public function getTab( $idx = null )
	{
		if( null == $idx ) {
			return array();
		}
		
		$tabs = $this->getTabs();
	
		if( isset( $tabs[$idx] ) ) {
			return $tabs[$idx];
		} else {
			return array();
		}
	}
	
	/**
	 * Get the first tab from the tabs array
	 *
	 * @access public
	 *
	 * @uses self::getTabs()
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public function getFirstTab()
	{
		$tabs = $this->getTabs();
	
		return array_shift( $tabs );
	}	

	/**
	 * Get the tabs
	 *
	 * @access public
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public function getTabs()
	{
		return $this->tabs;
	}	
	
	/**
	 * Get the tab that is flagged as default
	 *
	 * @access public
	 *
	 * @uses self::getTabs()
	 * @uses self::getFirstTab() if default tab is not found with self::getTabs()
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public function getDefaultTab()
	{
		if( !empty( $this->defaultTab ) ) {
			return $this->defaultTab;
		} else {
			$tabs = $this->getTabs();
			$default = array();
		
			foreach ( $tabs as $k => $tab ) {
				if( $tab['default'] ) {
					$default = $tab;
					break;
				}
			}
		
			if( empty( $default ) ) {
				$default = $this->getFirstTab();
			}
		
			return $default;
		}
	}	
	
	/**
	 * Get the index for the default tab
	 *
	 * @access public
	 *
	 * @uses self::getDefaultTab() OR
	 * @uses self::getFirstTab() if tab is not found with self::getDefaultTab()
	 *
	 * @since 1.2
	 *
	 * @return string
	 */
	public function getDefaultTabIdx()
	{
		$tab = $this->getDefaultTab();
	
		if( !isset( $tab['idx'] ) ) {
			$tab = $this->getFirstTab();
		}
	
		return $tab['idx'];
	}

	/**
	 * Get a tab parameter
	 *
	 * @access public
	 *
	 * @uses self::getTab()
	 *
	 * @since 1.2
	 *
	 * @return mixed the tab param value or bool false on failure
	 */
	public function getTabParam( $idx = null, $needle = null)
	{
		if( null == $idx && null == $needle ) {
			return false;
		}
		
		$tab = $this->getTab( $idx );
		if( !empty( $tab ) && isset( $tab[$needle] ) ) {
			return $tab[$needle];
		} else {
			return false;
		}			
	}	
	
	/**
	 * Check if has tabs
	 * 
	 * @access public
	 * 
	 * @since 1.4.0
	 * 
	 * @return boolean
	 */
	public function hasTabs() 
	{
		$tabs = $this->getTabs();
		if( !empty( $tabs ) ) {
			return true;
		} else {
			return false;			
		} 
	}
	
	/**
	 * @param unknown_type $activating
	 */
	public function activating( $activating ) 
	{
		if( $activating ) {
			$this->setTabs();
		}	
	}	
	
	/**
	 * Callback for the {nameSpace}_ajax_json_return hook
	 *
	 * Filter the AJAX return array based on given $action, actions are:
	 *
	 *  - add-prop			: Add a new Property
	 *  - del-prop			: Delete a Property
	 *  - update-prop		: Update a Property or Property Meta
	 *
	 * @access	public
	 *
	 * @param 	mixed	$return the value that is returned to the client
	 * @param 	string 	$action the 'gdprc-action' that is defined at the client-side (JavaScript)
	 * @param 	array 	$data (optional) extra data to pass from client-side to here
	 *
	 * @since 	1.2.1
	 *
	 * @return mixed
	 */
	public function process( $return, $action, $data )
	{
		// if action does not match one of the following, return directly
		if( 'gdprc-add-post' !== $action && 'gdprc-del-post' !== $action && 'gdprc-update-post' !== $action ) {
			return $return;
		}
		
		// To continue, a Post ID is needed
		if( ( 'gdprc-del-post' === $action || 'gdprc-update-post' === $action ) && !isset( $data['post_id'] ) ) {
			return $return;
		} elseif( ( 'gdprc-del-post' === $action || 'gdprc-update-post' === $action ) && isset( $data['post_id'] ) ) {
			$postId = $data['post_id'];
		} else {
			$postId = 0;
		}
		
		if( 'gdprc-add-post' === $action && ( !isset( $data['post_type'] ) || '' === $data['post_type'] ) ) {
			return $return;
		} elseif( 'gdprc-add-post' === $action && isset( $data['post_type'] ) ) { 
			$postType = $data['post_type'];
		} else {
			$postType = null;
		}		
		
		if( 'gdprc-add-post' !== $action && false !== ( $postType = get_post_type( is_array( $postId ) ? $postId[0] : $postId ) ) ) {
			$postTypeObj = new gdprcPostType( $postType );
		} elseif( 'gdprc-add-post' === $action && null !== $postType ) {
			$postTypeObj = new gdprcPostType( $postType );
		} else {
			$postTypeObj = null;
		}
		
		if( null === $postTypeObj ) {
			return $return;
		}
		
		$context = ( isset( $data['context'] ) ) ? $data['context'] : '';
		
		switch( $action ) {
			case 'gdprc-add-post':			
				if( !isset( $data['val'] ) ) {
					return $return;
				} else {
					$title = $data['val'];
				}
				
				$canDel = ( isset( $data['can_del'] ) ) ? $data['can_del'] : false;
				$canSave = ( isset( $data['can_save'] ) ) ? $data['can_save'] : false;				
				$hasTitle = ( isset( $data['has_title'] ) ) ? $data['has_title'] : false;
				$hasMedia = ( isset( $data['has_media'] ) ) ? $data['has_media'] : false;
							
				if( $postId = $postTypeObj->create( $title ) ) {
					$title = get_the_title( $postId );
					
					/**
					 * Let modules hook into the ajax add post process
					 * 
					 * @param	int		$postId
					 * @param	string	$context
					 * @param	array	$data
					 * 
					 * @since	1.2.2beta
					 */
					do_action( 'gdprc_'. $this->nameSpace .'_ajax_post_action_add', $postId, $context, $data );
					
					/**
					 * Let modules hook into the ajax add post process
					 *
					 * @param	int		$postId
					 * @param	string	$context
					 * @param	array	$data
					 * @param	string	$action
					 *
					 * @since	1.2.2beta
					 */					
					do_action( 'gdprc_'. $this->nameSpace .'_ajax_post_action', $postId, $context, $data, $action );
															
					$templ = new gdprcTemplate( self::TEMPL_FILE_NAME_LIST_ITEM, $this->templatePath, self::TEMPL_FILE_NAME_LIST_ITEM );
			
					$templ->setVar( 'post_id', $postId );
					$templ->setVar( 'post_type', $postType );
					$templ->setVar( 'title', $title );
					$templ->setVar( 'can_del', $canDel );
					$templ->setVar( 'can_save', $canSave );
					$templ->setVar( 'has_title', $hasTitle );
					$templ->setVar( 'context', $context );
			
					$return['id'] = $postId;
					$return['title'] = $title;
					$return['template'] = $templ->render( false, true );
			
				} else {
					$return = false;
				}
				break;
	
			case 'gdprc-del-post':					
				$notdeleted = array();
				foreach( (array)$postId as $id ) {
					if( $postTypeObj->delete( $id ) ) {
						$return['deleted'][] = $id;						
						
						/**
						 * Let modules hook into the ajax delete post process
						 *
						 * @param	int		$id
						 * @param	string	$context
						 * @param	array	$data
						 *
						 * @since	1.2.2beta
						 */						
						do_action( 'gdprc_'. $this->nameSpace .'_ajax_post_action_del', $id, $context, $data );
						
						/**
						 * Let modules hook into the ajax delete post process
						 *
						 * @param	int		$id
						 * @param	string	$context
						 * @param	array	$data
						 * @param	string	$action
						 *
						 * @since	1.2.2beta
						 */						
						do_action( 'gdprc_'. $this->nameSpace .'_ajax_post_action', $id, $context, $data, $action );	
					} else {
						$notdeleted[] = $id;
					}
				}				
				if( !empty( $notdeleted ) ) {
					$return = false;
				}	
				break;	
			case 'gdprc-update-post':	
				if( !isset( $data['val'] ) ) {
					return $return;
				} else {					
					$titleNew	= $data['val'];
					$hasGroupedMeta = false;
				}
				
				// determine if current action must handle post meta as one group (array)
				if( isset( $data['group_meta'] ) && ( 'true' === $data['group_meta'] || true === $data['group_meta'] ) ) {
					$hasGroupedMeta = true;
					
					if( !isset( $data['group_meta_key'] ) || '' === $data['group_meta_key'] ) {
						return false;
					}
					
					$groupedMetaKey = $data['group_meta_key'];
				}
				
				$post =  $postTypeObj->fetch( $postTypeObj->retrieve( $postId ) );
				$title = array_shift( $post )->post_title;
				$args = $postTypeObj->getArgsNew( $titleNew );
	
				// if the title has been modified, update the Post slug
				if( $title !== $titleNew ) {
					$args['post_name'] = sanitize_title( $titleNew );
				}
	
				$return['changed'] = false;
				$return['post_id'] = $postTypeObj->update( $postId, $args, true );
	
				if( gdprcAjaxHelper::isValidData( $return['post_id'] ) ) {					
					/**
					 * Let modules hook into the ajax update post process
					 *
					 * @param	int		$id
					 * @param	string	$context
					 * @param	array	$data
					 *
					 * @since	1.2.2beta
					 */					
					do_action( 'gdprc_'. $this->nameSpace .'_ajax_post_action_update', $postId, $context, $data );
					
					/**
					 * Let modules hook into the ajax update post process
					 *
					 * @param	int		$id
					 * @param	string	$context
					 * @param	array	$data
					 * @param	string	$action
					 *
					 * @since	1.2.2beta
					 */					
					do_action( 'gdprc_'. $this->nameSpace .'_ajax_post_action', $postId, $context, $data, $action );
					
					/**
					 * Let modules hook and alter the the ajax update post data
					 *
					 * @param	array	$data
					 * @param	int		$postId
					 * @param	string	$context
					 *
					 * @since	1.2.2beta
					 * 
					 * @return	array
					 */					
					$data_filtered = apply_filters( 'gdprc_'. $this->nameSpace .'_ajax_post_action_update_data', $data, $postId, $context );
					
					// be sure the filtered $data has the 'post_meta' entry
					if( is_array( $data_filtered ) && isset( $data_filtered['post_meta']) ) {
						$data = $data_filtered;
					}
					
					// return 0 instead of false to prevent an invalid ajax return
					$return['changed'] = ( $title !== $titleNew ) ? 1 : 0;
	
					if( isset( $data['post_meta'] ) ) {
						$metaAdded = false;	
						$postMeta = $data['post_meta'];
						
						if( $hasGroupedMeta ) {														
							$current = get_post_meta( $postId, $groupedMetaKey, true );
							if( is_array( $current ) && !empty( $current ) ) {
								$postMeta = array_merge( $current, $postMeta );
							}
							$metaAdded = update_post_meta( $postId, $groupedMetaKey, $postMeta );
							if( $metaAdded ) {
								foreach ( $postMeta as $k => $v ) {
									$return['meta_added'][$k] = $v;
								}
							}
						} else {							
							foreach ( $postMeta as $k => $v ) {							
								switch( $k ) {
									default:
										$metaAdded = update_post_meta( $postId, $k, $v );
										break;
								}
							
								if( false === $metaAdded ) {
									// meta not added
									$return['meta_not_added'][$k] = $v;
							
								} else {
									// meta succesfully added
									$return['meta_added'][$k] = $v;
								}
							}							
						}
					}
				}	
				break;
		}
	
		return $return;
	}	
	
	/**
	 * Callback for the {nameSpace}_after_init_modules hook
	 *
	 * @todo: 	detailed description
	 *
	 * @acces 	public
	 *
	 * @since 	1.2
	 */
	public function hook()
	{
		add_action( $this->nameSpace . '_scripts_admin', array( &$this, 'setScripts' ), 10, 3 );
		add_action( $this->nameSpace . '_styles_admin', array( &$this, 'setStyles' ), 10, 3 );
		add_action( $this->nameSpace . '_print_styles_admin', array( &$this, 'printStyles' ) );
		
		add_action( $this->nameSpace . '_add_admin_pages', array( &$this, 'addPage' ) );
		
		$pluginFile = $this->globals->get( 'pluginFile' );
		add_filter( 'plugin_action_links_' .$pluginFile, array( &$this, 'addPluginActionLink' ), 10 );

		add_action( $this->nameSpace . '_module_before_start', array( &$this, 'activating' ), 1 );
		
		add_filter( $this->nameSpace . '_ajax_json_return', array( &$this,'process' ), 10, 3 );
		
		// add settings name (group) for each tab
		foreach ( $this->tabs as $k => $data ) {
			$this->tabs[$k]['setting'] = $this->settings[$k]->getGroup();
		}
		
		/**
		 * This hook let other Modules / Plugins add a setting group
		 *
		 * @param array all tabs that represent a setting
		 */		
		$this->tabs = apply_filters( $this->nameSpace . '_setting_groups', $this->tabs );
			
		foreach( $this->tabs as $key => $tab ) {				
			if( !isset( $tab['slug'] ) ) {
				$this->addTabData( $key );
			}
		}		
	}	
	
	/**
	 * Render the admin settings page
	 *
	 * @acces public
	 *
	 * @uses gdprcTemplate to render the output
	 *
	 * @since 1.2
	 */
	public function render()
	{
		$tabidx = ( $this->hasTabs ) ? $this->currentTabIdx : $this->defaultTabIdx;
		$setting = $this->tabs[$tabidx]['setting'];
		
		// init the parent template
		$template = new gdprcTemplate( 'settings', $this->templatePath, self::TEMPL_FILE_NAME_SETTINGS_PAGE );
			
		// set vars for the template		
		$template->setVar( 'current_tab', $tabidx );
		$template->setVar( 'setting', $setting );
		$template->setVar( 'has_tabs', $this->hasTabs );
		$template->setVar( 'namespace', $this->nameSpace );		
		$template->setVar( 'title', $this->pageTitle );
				
		// construct the intructions URI
		$pluginDirName = $this->globals->get( 'pluginDirName' );
		$pluginDirName = str_replace( 'wp-' , 'gdprcookies-', $pluginDirName );		
		$instructionsUri = esc_url( sprintf( "http://www.gdprcookies-plugins.com/instruction-guide-%s-plugin/?utm_source=%s_plugin&utm_medium=wp_admin&utm_campaign=menu_%s", $pluginDirName, $this->nameSpace, $tabidx ) );
				
		if( $this->hasTabs ) {			
			$template->setVar( 'tabs', $this->tabs );			
		}	

		$template->setVar( 'instructions_uri', $instructionsUri );
			
		// create new gdprcTemplate instance
		$templateTable = new gdprcTemplate( 'table', $this->templatePath, self::TEMPL_FILE_NAME_SETTINGS_PAGE_TABLE );
				
		// setup Template vars
		$templateTable->setVars( $this->settings[$tabidx]->getstack() );
		$templateTable->setVar( 'current_templ', $tabidx );
	
		$templateTable->setVar( 'setting', $setting );
		$templateTable->setVar( 'locale', $this->globals->get('locale') );
		$templateTable->setVar( 'module_path', $this->globals->get( 'modulePath' ) );
		$templateTable->setVar( 'form_fields', $this->settings[$tabidx]->getSettings() );
		$templateTable->setVar( 'do_submit_btn', true );		
		$templateTable->setVar( 'namespace', $this->nameSpace );
					
		/**
		 * This hook let other Modules / Plugins filter the Template vars
		 *
		 * @param array $vars Template vars
		 */
		$vars = apply_filters( $this->nameSpace . '_templ_vars', $templateTable->getVars(), $tabidx );
	
		// add vars again in case $vars is modified
		$templateTable->setVars( $vars );
			
		/**
		 * This hook let other Modules / Plugins alter the $templateTable object
		 *
		 * @param gdprcTemplate $templateTable table Template object, passed by reference
		 */
		do_action_ref_array( $this->nameSpace . '_template_vars', array( &$templateTable ) );
	
		// render
		$table = $templateTable->render( false, true );
			
		$template->setVar( 'table', $table );
			
		// render
		echo $template->render();
	}	
	
	/**
	 * Callback for the add_menu_page and add_submenu_page functions
	 *
	 * @access public
	 * 
	 * @uses gdprcPluginSettingsPage::render()
	 *
	 * @since 1.2
	 */
	public function showPage() {
		
		$this->render();
	}	
	
	/**
	 * Callback for the {namespace}_add_admin_pages hook
	 *
	 * Main tasks are:
	 * 	- finish populating the '_tabs' array
	 * 	- add the settings pages
	 * 	- set some class members
	 *
	 * @access public
	 *
	 * @uses self::addPageSingle() OR
	 * @uses self::addPageTabs()
	 *
	 * @since 1.2
	 */
	public function addPage()
	{
		$this->defaultTab = $this->getDefaultTab();
		$this->defaultTabIdx = $this->getDefaultTabIdx();
				
		if( 1 === count( $this->tabs ) ) {						
			$this->addPageSingle();			
		} else {			
			$this->hasTabs = true;
			$this->addPageTabs();
		}
	}
	
	/**
	 * Callback for the plugin_action_links_{$pluginFile} hook
	 *
	 * Add a link to the settings page to the plugin actions
	 *
	 * @access public
	 *
	 * @param array $actions
	 *
	 * @since 1.2
	 *
	 * @return array
	 */
	public function addPluginActionLink( $actions )
	{
		$actions['settings'] = sprintf( '<a href="%s" title="%s">%s</a>', $this->uri, __( $this->pageTitle, $this->nameSpace ), __( 'Settings', $this->nameSpace ) );
		return $actions;
	}

	/**
	 * Handles the init logic
	 *
	 * @param array $settings all setting objects for the current namespace
	 *
	 * @uses self::setSettings()
	 *
	 * @uses self::setTabs()
	 *
	 * @access public
	 */
	public function init( &$settings = array() )
	{
		if( empty( $settings ) ) {
			throw new Exception( 'input parameter $settings is empty.' );
		}
	
		$this->setSettings( $settings );
		$this->setTabs();
	}
	
	/**
	 * Set the settings
	 *
	 * @acces private
	 *
	 * @param array $settings
	 *
	 * @since 1.2
	 */
	private function setSettings( &$settings = array() )
	{
		$this->settings = $settings;
	}	
	
	/**
	 * Add data to the tabs array for given tab
	 *
	 * @access private
	 *
	 * @param string $key the index of the tab in the array
	 *
	 * @since 1.2
	 */
	private function addTabData( $key )
	{
		$slug = sprintf( $this->slug, $this->nameSpace );		
		$slug = sprintf( '%s-%s', $slug, $key );

		$this->tabs[$key]['idx'] = $key;
		$this->tabs[$key]['slug'] = $slug;
		$this->tabs[$key]['class'] = ( $this->currentTabSlug === $slug ) ? 'tab-active' : 'tab-in-active';
		$this->tabs[$key]['uri'] = admin_url( sprintf('admin.php?page=%s', $slug ) );
	}	
	
	/**
	 * 
	 */
	private function setTabs() 
	{
		$settingKeys = array_keys( $this->settings );
		
		foreach ( $settingKeys as $key ) {				
			if( isset( $this->tabs[$key] ) ) {
				continue;
			}
			
			$prio = (int) $this->settings[$key]->getPriority();				
			$default = $this->settings[$key]->isDefaultSetting();
			$title = __( ucwords( preg_replace( '/(-)/', ' ', $key ) ), $this->nameSpace );
			$this->tabs[$key] = array( 'setting' => '', 'tab' => $title, 'default' => $default, 'prio' => $prio );
		}
		
		// sort the array based on the priority
		uasort( $this->tabs, array( &$this, 'sortTabs' ) );
		
		foreach ( $this->tabs as $key => $tab ) {
			$this->addTabData( $key );
		}		
	}	
	
	/**
	 * Callback for the uasort function
	 * 
	 * Sort the array based on the "prio" entry in the array
	 * 
	 * @acces	private
	 * 
	 * @param 	array	$gdprc1
	 * @param 	array	$gdprc2
	 * 
	 * @since 	1.2
	 * 
	 * @return 	number
	 */
	private function sortTabs( $gdprc1, $gdprc2 ) 
	{
		if ($gdprc1['prio'] == $gdprc2['prio']) {
			return 0;
		}
		
		return ($gdprc1['prio'] < $gdprc2['prio']) ? -1 : 1;
	}	

	/**
	 * Unset tabs
	 *
	 * @access private
	 *
	 * @since 1.4.0
	 */
	private function unsetTabs()
	{
		$this->tabs = array();
		$this->hasTabs = false;
	}
	
	/**
	 * Add the settings page for one tab
	 *
	 * @access private
	 *
	 * @uses add_options_page()
	 * @uses admin_url()
	 *
	 * @since 1.2
	 */
	private function addPageSingle()
	{
		$slug = sprintf( '%s-%s', $this->slug, $this->defaultTabIdx );		
		$this->hooknames[] = add_options_page( $this->pageTitle,  $this->menuTitle, 'manage_options', $slug, array( &$this, 'showPage' ) );
		$this->uri = admin_url( sprintf( 'options-general.php?page=%s', $slug ) );		
	}	
	
	/**
	 * Add the settings page for multiple tabs
	 *
	 * @access private
	 *
	 * @uses add_menu_page()
	 * @uses add_submenu_page()
	 *
	 * @since 1.2
	 */	
	private function addPageTabs()
	{
		$this->parentSlug = sprintf( '%s-%s', $this->slug, $this->defaultTabIdx );
		
		foreach ($this->tabs as $key => $tab ) {
			if( $this->defaultTabIdx === $key ) {
				$tabDefault = $this->tabs[$key];
				unset( $this->tabs[$key] );
				$this->tabs = array( $this->defaultTabIdx => $tabDefault ) + $this->tabs;
				break;
			}
		}
		
		foreach( $this->tabs as $key => $tab ) {		
			$slug = $tab['slug'];
			$title = ( $this->defaultTabIdx === $key ) ? $this->pageTitle : $tab['tab'];
			$menuTitle = ( $this->defaultTabIdx === $key ) ? $this->menuTitle : $tab['tab'];
		
			if( $this->defaultTabIdx === $key ) {
				$this->tabs[$key]['hookname'] = add_menu_page( $title, $menuTitle, 'manage_options', $slug, array( &$this, 'showPage' ), $this->globals->get( 'imgUri' ) . '/icon.svg' );
			} else {
				$this->tabs[$key]['hookname'] = add_submenu_page( $this->parentSlug, $title,  $menuTitle, 'manage_options', $slug, array( &$this, 'showPage' ) );
			}
		
			if( $key === $this->defaultTabIdx ) {
				$this->uriTabDefault = $this->uri = $this->tabs[$key]['uri'];
			}
		
			$this->hooknames[$key] = $this->tabs[$key]['hookname'];
		}

		// Rename menu-item general to general instead of self::menuTitle
		global $submenu;
		if ( isset( $submenu[$this->tabs[$this->defaultTabIdx]['slug']] ) ) {
			$submenu[$this->tabs[$this->defaultTabIdx]['slug']][0][0] = $this->tabs[$this->defaultTabIdx]['tab'];
		}
	}	
}