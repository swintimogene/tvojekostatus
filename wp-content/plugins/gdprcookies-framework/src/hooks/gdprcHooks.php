<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * igdprcHooks interface
 *
 * @author $Author: NULL $
 * @version $Id: gdprcHooks.php 156 2017-06-15 17:11:16Z NULL $
 * @since 1.4.0
 */
interface igdprcHooks
{
	/**
	 * Add all hooks
	 *
	 * @acces public
	 *
	 * @since 1.4.0
	 */	
	public function add();
}

/**
 * gdprcHooks Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcHooks.php 156 2017-06-15 17:11:16Z NULL $
 * @since 1.4.0
 */
abstract class gdprcHooks implements igdprcHooks
{
	/**
	 * Flag if frontend hooks should be added
	 * 
	 * @since 1.4.0
	 * 
	 * @var boolean
	 */
	public $doFrontend = true;
	
	/**
	 * gdprcPluginGlobals instance
	 * 
	 * @since 1.4.0
	 * 
	 * @var gdprcPluginGlobals
	 */
	protected $globals;
	
	/**
	 * The plugins namespace
	 * 
	 * @since 1.4.0
	 * 
	 * @var string
	 */
	protected $nameSpace = '';
	
	/**
	 * Constructor
	 *
	 * @access public
	 * 
	 * @param gdprcPluginGlobals $globals
	 * 
	 * @since 1.4.0
	 */
	public function __construct( $globals ) 
	{		
		if( !is_a( $globals, 'gdprcPluginGlobals' ) ) {
			throw new Exception( 'Parameter globals is not valid.' );
		}

		$this->globals = $globals;
		$this->nameSpace = $globals->get( 'pluginNameSpace' );
	}
	
	/**
	 * Callback for (WordPress) AJAX hook system 
	 * 
	 * This callback is also hooked to the 'wp_footer' action for AJAX requests made in the WordPress footer.
	 *  
	 * gdprcookies Framework Plugins should always provide the following REQUEST parameters in their JavaScript AJAX CALL:
	 * 
	 * 	- action: gdprc-action
	 * 	- YOUR_PLUGIN_NAMESPACE_action: [UNIQUE_ACTION] to add an unique action for the AJAX CALL
	 * 	- nonce: [YOUR_PLUGIN_NAMESPACE]Data.nonce (gdprcookies Framework automaticly adds in some JavaScript parameters inlcuded a nonce
	 * 	- data  (optional): extra data to pass from client-side to here
	 * 
	 * Example for jQuery with Plugin namespace "myplugin" : $.post(mypluginData.ajaxurl, {action:'gdprc-action', myplugin_action:'do-some-logic', nonce: mypluginData.nonce, data:{'foo':bar, 'food':bars}}, function(r) { ... });
	 * 
	 * If [YOUR_PLUGIN_NAMESPACE]_ajax_json_return returns 'content' as array index, the reponse is retured directly to the client without validating and json_encode
	 *  
	 * @uses filter [YOUR_PLUGIN_NAMESPACE]_ajax_json_return to let Modules interact with the gdprcookies Framework AJAX process
	 * @uses action [YOUR_PLUGIN_NAMESPACE]_after_process_ajax_request to let other Modules intertact with the AJAX process
	 * @uses gdprcAjaxHelper::isValidData to validate the $return parameter. 
	 * 
	 * @link gdprcookiesFramework::printScriptsFrontendVars()
	 * @link gdprcookiesFramework::printScriptsAdminHeaderVars()
	 * 
	 * @since 1.0
	 * 
	 * @returns void on missing YOUR_PLUGIN_NAMESPACE_action, a raw output or an json encoded array 
	 */
	public function processAjaxRequest()
	{
		if( !isset( $_REQUEST[$this->nameSpace . '_action'] ) )
			return;
		
		//@TODO test additional if condition with gdprc_DOING_AJAX (set in gdprcookiesFramework::start())			 
		//@TODO if has group meta, data is inside data[post_meta]
		
		$data = ( isset($_REQUEST['data']) ) ? $_REQUEST['data'] : null;
		$context = ( isset( $data['context'] ) ) ? $data['context'] : null;
		
		$r = array();
		$r['out'] = '';
		$hasError = false;
		
		$action = $_REQUEST[$this->nameSpace . '_action'];		
	
		if( false === check_ajax_referer( $this->nameSpace . '-action', 'nonce', false ) )
		{
			$r['out'] =  'ERROR: failed verifying nonce';
			$hasError = true;
		}
		
		if( false === $hasError )
		{	
			/**
			 * @todo add php doc
			 * @todo place this filter after {nameSpace}_validate_ajax_data ?
			 */
			do_action( $this->nameSpace . '_before_process_ajax_request', $action, $data );
			
			$validateReturn = array( 
					'is_valid' => true, 
					'fields' => array(), 
					'msg' => null );

			/**
			 * Let modules validate the ajax data for specific context
			 *
			 * @param	array $validateReturn
			 * @param	string the current AJAX action
			 * @param	array	the AJAX data
			 *
			 * @since	1.4.6
			 *
			 * @return	array
			 */
			$validateReturn = apply_filters( $this->nameSpace . '_validate_ajax_data_' . $context, $validateReturn, $action, $data );			
			
			/**
			 * Let modules validate the ajax data before processing it
			 * 
			 * @param	array	$validateReturn
			 * @param	string	the current AJAX action
			 * @param	array	the AJAX data
			 * @param	string	the context	- optional
			 * 
			 * @since	1.2.1
			 * 
			 * @return	array
			 */
			$validateReturn = apply_filters( $this->nameSpace . '_validate_ajax_data', $validateReturn, $action, $data, $context );
			
			if( isset( $validateReturn['is_valid'] ) && true == $validateReturn['is_valid'] )
			{				
				/**
				 * @todo add php doc
				 */
				$return = apply_filters( $this->nameSpace . '_ajax_json_return', null, $action, $data );
				
				// @todo: add $data param
				do_action( $this->nameSpace . '_after_process_ajax_request', $action, $return );
	
				if( is_array( $return ) && isset( $return['content'] ) ) {
					echo $return['content'];
					exit;
				}
				
				// allow data to be validated with bool false values
				// @since 1.1.8
				$allowFalse = ( is_array( $return ) && isset( $return['allow_false'] ) ) ? true : false;						
								
				$r['state'] = gdprcAjaxHelper::isValidData( $return, $allowFalse );// 1 or 0
				$r['out'] = $return;
				if( gdprcAjaxHelper::hasWpError( $return ) ) {				
					$r['wperrors'] = gdprcAjaxHelper::getWpErrors( $return );					
				}
			} else {
				$r['state'] = '0';
				$r['out'] = $validateReturn;
			}
		} else {
				$r['state'] = '-1';
		}			
		
		echo json_encode( $r );
		exit;	
	}	
}