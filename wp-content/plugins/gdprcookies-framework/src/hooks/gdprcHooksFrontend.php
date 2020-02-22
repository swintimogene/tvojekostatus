<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcHooksFrontend Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcHooksFrontend.php 167 2018-02-24 23:09:06Z NULL $
 * @since 1.4.0
 */
final class gdprcHooksFrontend extends gdprcHooks
{
	/**
	 * gdprcAssets instance
	 * 
	 * @since 1.4.0
	 * 
	 * @var gdprcAssets
	 */
	private $assets = false;
	
	/**
	 * Constructor
	 * 
	 * @access public
	 * 
	 * @since 1.4.0
	 * 
	 * @param gdprcAssets $assets
	 * @param gdprcPluginGlobals $globals
	 */
	public function __construct( $assets, $globals )
	{
		if( !is_a( $assets, 'gdprcAssets' ) ) {
			throw new Exception( 'Parameter assets is not valid.' );
		}		
		
		parent::__construct( $globals );
		
		$this->assets = $assets;
	}
	
	/**
	 * {@inheritDoc}
	 * @see gdprcHooks::add()
	 */
	public function add()
	{
		try {		
			// Frontend hooks
			if( !is_admin() )
			{					
				if( $this->doFrontend ===  apply_filters( $this->nameSpace . '_do_frontend_hooks', true ) )
				{
					add_action( 'wp_enqueue_scripts', array(&$this, 'setStylesFrontend') );
					add_action( 'wp_enqueue_scripts', array(&$this, 'setScriptsFrontend') );
					add_action( 'wp_head', array(&$this, 'printScriptsFrontendVars'), 8 );
					add_action( 'wp_head', array(&$this, 'printScriptsFrontend') );
					add_action( 'template_redirect', array( &$this, 'renderTemplate' ) );
		
					if( isset( $_REQUEST['action'] ) && 'gdprc-footer-action' === $_REQUEST['action'] ) {
						add_action( 'wp_footer', array( &$this, 'processAjaxRequest' ), 99999 );
					}
				}	
			} 	
		} catch ( Exception $e ) {
				
		}		
	}	
	
	/**
	 * Callback for the wp_enqueue_scripts hook: Enqueue scripts for the front-end
	 *
	 * @acces public
	 *
	 * @uses wp_enqueue_script()
	 * @uses [YOUR_PLUGIN_NAMESPACE]_scripts_frontend (3 params are passed: $wp_scripts, $exclude, $isScriptDebug)
	 * @uses [YOUR_PLUGIN_NAMESPACE]_exclude_scripts_frontend to let gdprcookies Framework Plugins filter the $exclude parameter
	 *
	 * @since 0.1
	 */
	public function setScriptsFrontend()
	{
		try {
			global $wp_scripts;
		
			$exclude =  array();
			$pluginPath = $this->globals->get( 'pluginPath' );
			$jsUri = $this->globals->get( 'jsUri' );
			$isScriptDebug = gdprcMiscHelper::isScriptDebug();
			$ext = ( $isScriptDebug ) ? '.js' : '.min.js';
					
			// make sure jQuery is enqueued
			wp_enqueue_script( 'jquery' );
		
			if( file_exists( $pluginPath . '/assets/js/global' . $ext ) ) {					
				wp_enqueue_script( $this->nameSpace . '-global', $jsUri . '/global' . $ext, array( 'jquery' ) );
			}
		
			do_action( $this->nameSpace . '_scripts_frontend', $wp_scripts, apply_filters( $this->nameSpace . '_exclude_scripts_frontend', $exclude ), $isScriptDebug );
			
		} catch ( Exception $e ) {
			
		}
	}	
	
	/**
	 * Callback for the wp_head hook: setup global JavaScript parameters for the frontend
	 *
	 * @access public
	 *
	 * @uses [YOUR_PLUGIN_NAMESPACE]_script_frontent_vars to let gdprcookies Framework Plugins modify the $gdprcVars array
	 * @uses json_encode() to safely create a JavaScript array
	 *
	 * @since 1.0
	 */
	public function printScriptsFrontendVars()
	{
		try {
			// WordPress installation URI
			$wpuri = get_bloginfo( 'wpurl' );
			// is multisite or not		
			$isMs = gdprcMultisiteHelper::isMs();
		
			// build array with vars
			$gdprcVars = array();
			$gdprcVars['ns'] = $this->nameSpace;
			$gdprcVars['nonce'] = wp_create_nonce( $this->nameSpace . '-action' );
			$gdprcVars['wpurl'] = $wpuri;
			$gdprcVars['domain'] = gdprcMiscHelper::getHostWithoutSubdomain( $wpuri );
			$gdprcVars['ajaxurl'] = admin_url( 'admin-ajax.php' );
			$gdprcVars['referer'] = wp_get_referer();
			$gdprcVars['currenturl'] = ( $isMs ) ? gdprcMultisiteHelper::getCurrentUri() : home_url( add_query_arg( NULL, NULL ) );
			$gdprcVars['isms'] = ( $isMs ) ? true : false;
			$gdprcVars['mspath'] = ( $isMs ) ? gdprcMultisiteHelper::getBlogDetail( 'path' ) : '/' ;
		
			// fix for install with a sub folder setup
			// @since 1.1.8
			if( false !== strpos( $gdprcVars['currenturl'] , '/' ) && !$isMs )
				$gdprcVars['currenturl'] = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
				$gdprcVars = apply_filters( $this->nameSpace . '_script_frontend_vars' , $gdprcVars );
					
				?>
				<script type='text/javascript'>
				/* <![CDATA[ */
				var <?php echo $this->globals->get( 'jsNamespace' ) ?> = <?php echo json_encode($gdprcVars) ?>;
				/* ]]> */
				</script>
				<?php
		} catch ( Exception $e ) {
				
		}				
	}		
		
	/**
	 * Callback for the wp_head hook 
	 *
	 * @access public
	 * 
	 * @uses [YOUR_PLUGIN_NAMESPACE]_print_scripts_frontend to let gdprcookies Framework Plugins print styles for in the frontend head
	 * 
	 * @since 0.1 
	 */
	public function printScriptsFrontend()
	{			
		try { 
			do_action( $this->nameSpace . '_print_scripts_frontend' );
		} catch ( Exception $e ) {
				
		}		
	}			
			
			
	/**
	 * Callback for the wp_enqueue_scripts hook
	 * 
	 * @acces public
	 * 
	 * @uses [YOUR_PLUGIN_NAMESPACE]_print_scripts_frontend to let gdprcookies Framework Plugins enqueue styles for in the frontend head
	 * 
	 * @since 0.1
	 */
	public function setStylesFrontend()
	{
		try {
			global $wp_styles;
	
			$isModeDev = gdprcMiscHelper::isRunningModeDev();
			do_action( $this->nameSpace . '_styles_frontend', $wp_styles, apply_filters( $this->nameSpace . '_exclude_styles_frontend', array() ), $isModeDev );
		} catch ( Exception $e ) {
				
		}		
	}
	
	/**
	 * Callback for the template_redirect hook: render front-end templates
	 *
	 * @access public
	 *
	 * @since 0.1
	 */
	public function renderTemplate()
	{
		try {		
			do_action( $this->nameSpace . '_render_templates' );
		} catch ( Exception $e ) {
				
		}			
	}	
}