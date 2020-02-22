<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcHooksAdmin Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcHooksAdmin.php 171 2018-03-03 12:25:00Z NULL $
 * @since 1.4.0
 */
final class gdprcHooksAdmin extends gdprcHooks 
{
	/**
	 * The nonce
	 * 
	 * @var string
	 */
	private $nonce = '';
	
	/**
	 * @var string
	 */
	private $activatingOptionName = '';
		
	/**
	 * Flag if has settings
	 *
	 * @since 1.4.4
	 *
	 * @var boolean
	 */
	private $doSettings = false;	
	
	/**
	 * gdprcPluginSettingsProcessor instance
	 * 
	 * @since 1.4.0
	 * 
	 * @var gdprcPluginSettingsProcessor
	 */
	private $settingsProcessor = false;
	
	/**
	 * Constructor
	 * 
	 * @access public
	 * 
	 * @param string $nonce
	 * @param string $activatingOptionName
	 * @param bool $doSettings
	 * @param gdprcPluginSettingsProcessor $settingsProcessor
	 * @param gdprcPluginGlobals $globals
	 * 
	 * @since 1.4.0
	 */
	public function __construct( $nonce = '', $activatingOptionName = '', $doSettings = false, $settingsProcessor, $globals )
	{
		parent::__construct( $globals );
		
		$this->nonce = $nonce;
		$this->activatingOptionName = $activatingOptionName;
		$this->doSettings = $doSettings;
		$this->settingsProcessor = $settingsProcessor;		
	}	

	/**
	 * {@inheritDoc}
	 * @see gdprcHooks::add()
	 */
	public function add()
	{
		try {		
			// Network vs non network admin hooks
			if( is_network_admin() ) {
				add_action( 'wpmu_new_blog', array( &$this, 'activatePluginForNewBlog' ) );
				add_filter( 'network_admin_plugin_action_links', array( &$this, 'disableFrameworkDeactivation' ), 10, 2 );
			} elseif( !is_network_admin() && is_admin() ) {
				add_filter( 'plugin_action_links', array( &$this, 'disableFrameworkDeactivation' ), 10, 2 );
			}
		
			// Admin hooks
			if( is_admin() )
			{
				add_action( 'admin_menu', array( &$this, 'setAdminPages' ), 10 );
				add_action( 'admin_menu', array( &$this, 'removeAdminPages' ), 11 );
				add_action( 'admin_enqueue_scripts', array( &$this, 'setScriptsAdmin' ) );
				add_action( 'admin_enqueue_scripts', array( &$this, 'setStylesAdmin' ) );
				add_action( 'admin_print_styles', array( &$this, 'printStylesAdmin' ), 100 );
				add_action( 'admin_print_scripts', array( &$this, 'printScriptsAdminHeaderVars' ) );
				add_action( 'admin_print_scripts', array( &$this, 'printScriptsAdminHeader' ), 11 );
				add_action( 'admin_print_footer_scripts', array( &$this, 'printScriptsAdminFooter' ) );
				add_action( 'admin_footer-plugins.php', array( &$this, 'unsetActivating' ), 99999 );
		
				// frontend ajax requests
				add_action( 'wp_ajax_gdprc-action', array( &$this, 'processAjaxRequest' ) );
				add_action( 'wp_ajax_nopriv_gdprc-action', array( &$this, 'processAjaxRequest' ) );	
			} 	
		} catch ( Exception $e ) {
			throw $e;
		}		
	}

	/**
	 * Callback for the admin_enqueue_scripts hook
	 *
	 * @acces public
	 *
	 * @uses wp_enqueue_script()
	 * @uses [YOUR_PLUGIN_NAMESPACE]_scripts_admin to let gdprcookies Framework Plugins enqueue scripts for admin pages
	 * @uses [YOUR_PLUGIN_NAMESPACE]_scripts_admin_before
	 * @uses [YOUR_PLUGIN_NAMESPACE]_scripts_admin_after
	 *
	 * @since 0.1
	 */
	public function setScriptsAdmin( $hook_suffix )
	{
		try {
			global $wp_scripts;

			$uriAssetsJs = $this->globals->get( 'wfJsUri' );
			$pluginPath = $this->globals->get( 'pluginPath' );
			$jsUri = $this->globals->get( 'jsUri' );
			$isScriptDebug = gdprcMiscHelper::isScriptDebug();
			$ext = ( $isScriptDebug ) ? '.js' : '.min.js';
			
			/**
			 * Let others enqueue scripts on the admin before
			 *
			 * @param string $hook_suffix
			 * @param WP_Scripts $wp_scripts
			 * @param boolean $isScriptDebug
			 * 
			 * @since 1.4.7
			 */			
			do_action( $this->nameSpace . '_scripts_admin_before', $hook_suffix, $wp_scripts, $isScriptDebug );
			
			if( null !== $uriAssetsJs ) {
				wp_enqueue_script( 'gdprc-notices', $uriAssetsJs . '/gdprc-notices.js', false, false, true );
			}
					
			if( file_exists( $pluginPath . '/assets/js/global.admin' . $ext ) ) {
				wp_enqueue_script( $this->nameSpace.'-global-admin', $jsUri . '/global.admin' . $ext, array( 'jquery' ), false, true );
			}
			
			/**
			 * Let others enqueue scripts on the admin after
			 *
			 * @param string $hook_suffix
			 * @param WP_Scripts $wp_scripts
			 * @param boolean $isScriptDebug
			 * 
			 * @since 1.4.6 added $wp_scripts and $isModeDev param
			 * @since 1.4.7 added $isScriptDebug param instead of $isModeDev
			 */
			do_action( $this->nameSpace . '_scripts_admin', $hook_suffix, $wp_scripts, $isScriptDebug );

			/**
			 * Let others enqueue scripts on the admin after
			 *
			 * @param string $hook_suffix
			 * @param WP_Scripts $wp_scripts
			 * @param boolean $isScriptDebug
			 * 
			 * @since 1.4.7
			 */
			do_action( $this->nameSpace . '_scripts_admin_after', $hook_suffix, $wp_scripts, $isScriptDebug );
		} catch ( Exception $e ) {
			
		}
	}	
	
	/**
	 * Callback for the admin_print_scripts hook: setup global JavaScript params
	 *
	 * @access public
	 *
	 * @uses [YOUR_PLUGIN_NAMESPACE]_script_admin_vars to let gdprcookies Framework Plugins modify the $gdprcVars array
	 * @uses json_encode() to safely create a JavaScript array
	 *
	 * @since 1.0
	 */
	public function printScriptsAdminHeaderVars()
	{
		try {
			static $did = false;
			
			if( !$did && $this->doSettings && $this->settingsProcessor->hasSettingsPage() && $this->settingsProcessor->getSettingsPage()->onSettingsPage ) {
				$gdprcVarsGlobal = array();
				$gdprcVarsGlobal['curr_sett_ns'] = $this->nameSpace;
				$did = true;
			}
				
			$gdprcVars = array();
			$gdprcVars['ns'] = $this->nameSpace;
			$gdprcVars['nonce'] = $this->nonce;
				
			$gdprcVars = apply_filters( $this->nameSpace . '_script_admin_vars' , $gdprcVars );
			?>
			<script type='text/javascript'>
			/* <![CDATA[ */
			<?php if( isset( $gdprcVarsGlobal['curr_sett_ns'] ) ): ?>var gdprcData = <?php echo json_encode( $gdprcVarsGlobal ) ?>;<?php echo "\n"; endif ?>
			var <?php echo $this->globals->get( 'jsNamespace' ) ?> = <?php echo json_encode( $gdprcVars ) ?>;
			/* ]]> */
			</script>
			<?php
		} catch ( Exception $e ) {

		}		
	}			
			
	/**
	 * Callback for the admin_print_scripts hook
	 * 
	 * @access public
	 * 
	 * @uses [YOUR_PLUGIN_NAMESPACE]_print_scripts_admin_header to let gdprcookies Framework Plugins print scripts in the admin head
	 * 
	 * @since 1.0
	 */
	public function printScriptsAdminHeader()
	{
		try {
			do_action( $this->nameSpace . '_print_scripts_admin_header' );
		} catch ( Exception $e ) {

		}		
	}	
	
	/**
	 * Callback for the admin_print_footer_scripts hook
	 * 
	 * @access public
	 * 
	 * @uses [YOUR_PLUGIN_NAMESPACE]_print_scripts_admin_footer to let gdprcookies Framework Plugins print scripts in the admin footer
	 * 
	 * @since 1.0
	 */
	public function printScriptsAdminFooter() 
	{	
		try {
			do_action( $this->nameSpace . '_print_scripts_admin_footer' );
		} catch ( Exception $e ) {

		}		
	}		
	
	/**
	 * Callback for the admin_enqueue_scripts hook
	 *  
	 * Enqueue styles for the admin Plugin page
	 *
	 * @acces public
	 * 
	 * @uses [YOUR_PLUGIN_NAMESPACE]_styles_admin to let gdprcookies Framework Plugins enqueue styles for in the admin head
	 * 
	 * @since 0.1
	 */
	public function setStylesAdmin( $hook_suffix )
	{
		try {
			global $wp_styles;
			
			$isModeDev = gdprcMiscHelper::isRunningModeDev();
			do_action( $this->nameSpace . '_styles_admin', $hook_suffix, $wp_styles, $isModeDev );
		} catch ( Exception $e ) {

		}				
	}	
	
	/**
	 * Callback for the admin_print_styles hook 
	 * 
	 * @access public
	 * 
	 * @uses [YOUR_PLUGIN_NAMESPACE]_print_styles_admin to let gdprcookies Framework Plugins print styles for in the admin head
	 * 
	 * @since 1.0
	 */
	public function printStylesAdmin() 
	{
		try {
			global $hook_suffix;
			
			do_action( $this->nameSpace . '_print_styles_admin', $hook_suffix );
		} catch ( Exception $e ) {

		}
	}
	
	/**
	 * Callback for the admin_menu hook
	 *
	 * @access public
	 *
	 * @uses [YOUR_PLUGIN_NAMESPACE]_add_admin_pages to let gdprcookies Framework Plugins add admin page(s)
	 *
	 * @since 0.1
	 */
	public function setAdminPages()
	{
		try {
			do_action( $this->nameSpace . '_add_admin_pages' );
		} catch ( Exception $e ) {

		}		
	}	
	
	/**
	 * Callback for the admin_menu hook
	 *
	 * @access public
	 *
	 * @uses [YOUR_PLUGIN_NAMESPACE]_add_admin_pages to let gdprcookies Framework Plugins remove admin page(s)
	 *
	 * @since 1.0
	 */
	public function removeAdminPages()
	{
		try {
			do_action( $this->nameSpace . '_remove_admin_pages' );
		} catch ( Exception $e ) {

		}
	}

	/**
	 * Callback for the plugin_action_links action hook
	 *
	 * Disable the gdprcookies Framework from being deactivated when gdprcookies Framework Plugins are still active
	 *
	 * @param array $actions
	 * @param string $plugin_file
	 *
	 * @since 1.1
	 *
	 * @return array
	 */
	public function disableFrameworkDeactivation( $actions, $pluginFile )
	{
		try {
			$gdprcPlugins = gdprcMiscHelper::getFrameworkActivePlugins( is_network_admin() );
			$gdprcFrameWorkFile = plugin_basename( $this->globals->get( 'wfPluginPath' ) . '/gdprcookies-framework.php' );
			
			if ( !empty( $gdprcPlugins ) && $pluginFile === $gdprcFrameWorkFile && array_key_exists( 'deactivate', $actions ) ) {
				unset( $actions['deactivate'] );
			}
			
			return $actions;
		} catch ( Exception $e ) {
			return $actions;
		}
	}

	/**
	 * Callback for the wpmu_new_blog hook
	 *
	 * Ensure that new blogs are configured automaticly
	 *
	 * @access public
	 *
	 * @param int $blogId
	 *
	 * @since 1.1
	 */
	public function activatePluginForNewBlog( $blogId )
	{
		try {
			switch_to_blog( $blogId );
			
			do_action( 'activate_'  . $this->globals->get( 'pluginFile' ), false );
			
			restore_current_blog();
		} catch ( Exception $e ) {
		
		}
	}

	/**
	 * Callback for admin_footer-plugins.php hook
	 *
	 * Unset the gdprcookiesFramework::activatingOptionName option
	 *
	 * @TODO: consider a WP Cronjob for removing this option in case it failed removing
	 *
	 * @uses	delete_option()
	 *
	 * @since	1.1.8
	 */
	public function unsetActivating()
	{
		delete_option( $this->activatingOptionName  );
	}	
}