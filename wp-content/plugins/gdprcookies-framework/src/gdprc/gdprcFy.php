<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcFy class
 *
 * gdprcFy!
 *
 * @author $Author: NULL $
 * @version $Id: gdprcFy.php 182 2018-03-09 13:09:23Z NULL $
 *
 * @since 1.2.3
 */
final class gdprcFy
{
	private $ns;

	private $gdprcCore;
	
	private $globals;	
	
	private $optionName = '';
	
	private $optionNameSuffix = '_gdprcfied';
	
	private $pageSlugPrefix = 'page-gdprcfy-';
	
	private $slug = '';
	
	private $actiongdprcFy = 'gdprcfy';
	
	private $actionUngdprcFy = 'gdprcunfy';
	
	private $hookSuffix = '';
	
	private $gdprcfiedValue = null;
	
	private $siteUrl = '';
	
	private $url = 'aHR0cHM6Ly9zY3JpcHQuZ29vZ2xlLmNvbS9tYWNyb3Mvcy9BS2Z5Y2J5SkpfcmF1NHdOdGJmaU12SjIwMXhYck9TaWFQZGRFX1dlZ3NZMzVENEVBSnFKcWNoNi9leGVj';
		
	private $formErrorMsg = array();
	
	private $formErrorFields = array();
	
	private $formSuc6Msg = array();
	
	private $formHasErrors = false;
	
	private $submittedData = array();
	
	private $bypass = array( 'gdprcea' );
	
	private $bypassing = false;
	
	private $gdprcfied = false;	
	
	private $response = null;
	
	private $justgdprcfied = false;
	
	private $iframeRequested = false;
	
	private $action = null;
	
	public static $instancens = array();
	
	public static $likes = array();
	
	const TEMPL_NAME_gdprcFY_FORM = 'gdprc-tpl-gdprcfy.php';
	
	const TEMPL_NAME_gdprcUNFY_FORM = 'gdprc-tpl-gdprcunfy.php';
	
	const POPUP_TITLE = 'Validate plugin';
	
	const PLUGIN_ACTION_LINK_VALIDATE = 'Validate';
	
	const PLUGIN_ACTION_LINK_UNVALIDATE = 'Unvalidate';
	
	const FORM_FIELD_NAME_NS = 'wf_gdprcfy_ns';
	
	const FORM_FIELD_NAME_AC = 'wf_gdprcfy_ac';
	
	const FORM_FIELD_NAME_PC = 'wf_gdprcfy_pc';
	
	const FORM_FIELD_NAME_WC = 'wf_gdprcfy_wc';
	
	const ACTION_UPDATE_ACTIVE_STATUS = 'update_active_status';
	
	const URI_REGISTER = 'http://gdprcookies-plugins.com/register-envato-purchase-code/';
	
	/**
	 * Constructor
	 * 
	 * @access public
	 * 
	 * @param gdprcPluginGlobals $globals
	 * 
	 * @since 1.2.3
	 */
	public function __construct( $activating = false, $globals )
	{
		$this->ns = $globals->get( 'pluginNameSpace' );				
		$this->optionName = $this->ns.$this->optionNameSuffix;		
		$this->slug = $this->pageSlugPrefix.$this->ns;
		$this->globals = $globals;		
		$this->siteUrl = ( gdprcMultisiteHelper::isMs() ) ? network_site_url() : site_url();		
		self::$instancens[$this->ns] = true;
		
		if( $this->isBypass() ) {
			$this->gdprcfied = true;
			self::$likes[$this->ns] = true;
			return;
		}
				
		if( gdprcMiscHelper::isLocalhost() ) {
			$this->gdprcfied = true;
			return;			
		}
		
		$this->handleRequest();		
		$this->setgdprcfied();		

		if( $activating ) {
			return;	
		}
		
		$this->hook();
		
		if( !$this->isgdprcfied() || $this->isJustgdprcfied() ) {			
			$this->setNotices();
		}
	}	
	
	/**
	 * Callback for the admin_enqueue_scripts hook
	 * 
	 * @since	public
	 * 
	 * @param 	string $hook_suffix
	 * 
	 * @since 	1.2.3
	 * 
	 * @uses	wp_enqueue_style()
	 */
	public function setStylesAdmin( $hook_suffix )
	{
		global $wp_styles;
		
		if( $hook_suffix === $this->hookSuffix ) {
			wp_enqueue_style( 'gdprc-forms',  $this->globals->get( 'wfCssUri' ) .'/form.css' );
		}		
	}	
	
	/**
	 * Callback for the plugin_action_links_{$pluginFile} hook
	 *
	 * @access 	public
	 *
	 * @param 	array $actions
	 *
	 * @since 	1.2.3
	 *
	 * @return	array
	 */
	public function setPluginActionLink( $actions )
	{
		if( !$this->isgdprcfied() ) {		
			$actions[$this->actiongdprcFy] = $this->getValidateLink();
		} else {
			$actions[$this->actionUngdprcFy] = $this->getUnValidateLink();
		}
			
		return $actions;
	}	
	
	/**
	 * Callback for the network_admin_plugin_action_links_{$pluginFile} hook
	 *
	 * @access 	public
	 *
	 * @param 	array $actions
	 * @param 	string $pluginFile
	 * @param 	array $pluginData
	 * @param 	string $context
	 *
	 * @since 	1.2.5
	 *
	 * @return	array
	 */
	public function setPluginActionLinkNetwork( $actions, $pluginFile, $pluginData, $context )
	{
		if( !$this->isgdprcfied() ) {		
			$actions[$this->actiongdprcFy] = $this->getValidateLink();
		} else {
			$actions[$this->actionUngdprcFy] = $this->getUnValidateLink();
		}
			
		return $actions;
	}	
	
	/**
	 * Callback for the admin_menu hook
	 *
	 * create an WP page without admin menu (parent is null)
	 *
	 * @access	public
	 * 
	 * @uses 	add_submenu_page	
	 *
	 * @since 	1.2.3
	 */
	public function addPage()
	{
		$this->hookSuffix = add_submenu_page( null, self::POPUP_TITLE, self::POPUP_TITLE, 'manage_options', $this->slug, array( &$this, 'renderPage' ) );			
	}	
	
	/**
	 * Callback for the admin_head hook
	 * 
	 * Hide all admin notices in the iframe popup
	 * 
	 * @access public
	 * 
	 * @uses remove_all_actions
	 * 
	 * @since 1.2.3
	 * @since 1.4.7 renamed to adminHead
	 */
	public function adminHead()
	{	
		static $did = false;
		
		remove_all_actions( 'admin_notices' );	
		
		if( gdprcMultisiteHelper::isMs() ) {
			remove_all_actions( 'network_admin_notices' );
		}
		
		if( !$did ): 
		
		?>
		<script type="text/javascript">		
		function onClickgdprcookiesButton(e) {
			e.preventDefault();			
			if(confirm('<?php echo __( 'Are you sure you want to '. (( $this->getActionFromRequest() === $this->actiongdprcFy ) ? 'validate' : 'un-validate') .'?', 'gdprcookies' ); ?>')) {				
				var form = document.getElementById('<?php echo (( $this->getActionFromRequest() === $this->actiongdprcFy ) ? 'gdprcfy-form' : 'gdprcunfy-form' ) ?>');
				if(form) {
					form.submit();
				}
			}	else {
				return false;				
			}			
		}
		</script>
		<?php endif;
		$did = true;
	}	
	
	/**
	 * Render the TB content page
	 * 
	 * @access	public
	 * 
	 * @uses 	gdprcTemplate
	 * 
	 * @since 	1.2.3
	 */
	public function renderPage()
	{	
		$pluginFilePath = $this->globals->get( 'pluginPathFile' );
		$templPath = $this->globals->get( 'wfTemplPath' );
		$contentId = 'gdprcfy-wrap-'.$this->ns;
		$logoUri = $this->globals->get( 'wfImgUri' ) . '/logo.svg';
		$action = $this->getActionFromRequest();
		
		if( $action ) {		
			$template = ( $action === $this->actiongdprcFy ) ? self::TEMPL_NAME_gdprcFY_FORM : self::TEMPL_NAME_gdprcUNFY_FORM;			
			
			// set button attributes			
			$btnAttributes = array( 'class' => 'btn' );
			
			//if( $this->isformSubmitted() && 
			//		!$this->formHasErrors && 
			//		( ( $action === $this->actiongdprcFy && $this->gdprcfied ) || ( $action === $this->actionUngdprcFy && !$this->gdprcfied ) ) ) {
			//	$btnAttributes['disabled'] = 'disabled';
			//}	
			
			if( $action === $this->actiongdprcFy && $this->gdprcfied ) {
				$btnAttributes['onclick'] = 'self.parent.tb_remove(); parent.location.reload(1); return false;';
			} elseif( $action === $this->actionUngdprcFy && !$this->gdprcfied ) {
				$btnAttributes['onclick'] = 'self.parent.tb_remove(); parent.location.reload(1); return false;';
			} else {
				$btnAttributes['onclick'] = 'onClickgdprcookiesButton(event);';
			}
			
			$form = new gdprcTemplate( $template, $templPath, $template, false );			
			$form->setVar( 'all_has_error' , false );			
			
			if( $this->formHasErrors ) {
				if( in_array( '*', $this->formErrorFields ) ) {
					$form->setVar( 'all_has_error' , true );
				}			
				
				$form->setVar( 'has_err' , true );
				$form->setVar( 'msg_err' , __( 'Whoeps somethings wrong', 'gdprcookies' ) . ':<br/>' . (join( '<br/>', $this->formErrorMsg )) );
				$form->setVar( 'msg_suc6' , '' );					
				$form->setVar( self::FORM_FIELD_NAME_PC , $this->submittedData[self::FORM_FIELD_NAME_PC] );
				$form->setVar( self::FORM_FIELD_NAME_WC , $this->submittedData[self::FORM_FIELD_NAME_WC] );
			
			} else {			
				$form->setVar( 'has_err' , false );
				$form->setVar( 'msg_err' , '' );
				$form->setVar( 'msg_suc6', join( '<br/>', $this->formSuc6Msg ) );			
				$form->setVar( self::FORM_FIELD_NAME_PC , '' );
				$form->setVar( self::FORM_FIELD_NAME_WC , '' );
			}
			
			$form->setVar( 'action', $action ); 
			$form->setVar( 'tb_content_id', $contentId );
			$form->setVar( 'logo_uri', $logoUri );
			$form->setVar( 'ns', $this->ns );
			$form->setVar( 'field_name_ns' , self::FORM_FIELD_NAME_NS );
			$form->setVar( 'field_name_ac' , self::FORM_FIELD_NAME_AC );
			$form->setVar( 'field_name_pc' , self::FORM_FIELD_NAME_PC );
			$form->setVar( 'field_name_wc' , self::FORM_FIELD_NAME_WC );
			$form->setVar( 'error_fields', $this->formErrorFields );
			$form->setVar( self::FORM_FIELD_NAME_NS , $this->ns );
			$form->setVar( 'gdprcfied' , $this->gdprcfied );
			$form->setVar( 'plugin_name', gdprcMiscHelper::getPluginData( $pluginFilePath, 'Name' ) );
			$form->setVar( 'uri_register', self::URI_REGISTER );
			$form->setVar( 'btn_attributes', $btnAttributes );
		
			$form->render();
			
		} else {
			echo __( 'Could not complete form. Unknown action', 'gdprcookies' );
		}
	}	
	
	/**
	 * Find out if gdprcfied
	 * 
	 * @access	public
	 * 
	 * @since 	1.2.3
	 * 
	 * @return boolean
	 */
	public function isgdprcfied() 
	{
		return $this->gdprcfied;
	}	
	
	/**
	 * Find out if just gdprcfied
	 * 
	 * @access	public
	 * 
	 * @since 	1.2.3
	 * 
	 * @return boolean
	 */
	public function isJustgdprcfied()
	{
		return $this->justgdprcfied;
	}	
		
	/**
	 * Find out if need bypass
	 * 
	 * @access	public
	 * 
	 * @since 1.4.3
	 * 
	 * @return boolean
	 */
	public function isBypass()
	{
		return ( in_array( $this->ns , $this->bypass ) );
	}
	
	/**
	 * Find out if current ns is liked
	 * 
	 * @param string $ns
	 * 
	 * @since 1.4.3
	 * 
	 * @return boolean
	 */
	public static function isLiked( $ns = '' )
	{
		return ( isset( self::$likes[$ns] ) );
	}
	
	/**
	 * Handle requests
	 * 
	 * @access	private
	 * 
	 * @since 	1.2.3
	 */
	private function handleRequest() 
	{		
		if( isset( $_REQUEST['page'] ) && 0 === strpos( $_REQUEST['page'], $this->pageSlugPrefix ) ) {
			$this->iframeRequested = true;
		}		
		if( $this->iframeRequested && $this->slug === $_REQUEST['page'] ) {						 
			if( !defined( 'IFRAME_REQUEST' ) ) {
				define( 'IFRAME_REQUEST', true );
			}

			if( $this->isformSubmitted() ) {				
				$this->action = ( isset( $_REQUEST[self::FORM_FIELD_NAME_AC] ) && '' !== $_REQUEST[self::FORM_FIELD_NAME_AC] ) ? trim( $_REQUEST[self::FORM_FIELD_NAME_AC] ) : null;
				$pc = ( isset( $_REQUEST[self::FORM_FIELD_NAME_PC] ) && '' !== $_REQUEST[self::FORM_FIELD_NAME_PC] ) ? trim( $_REQUEST[self::FORM_FIELD_NAME_PC] ) : null; 
				$wc = ( isset( $_REQUEST[self::FORM_FIELD_NAME_WC] ) && '' !== $_REQUEST[self::FORM_FIELD_NAME_WC] ) ? trim( $_REQUEST[self::FORM_FIELD_NAME_WC] ) : null;
				$ns = ( isset( $_REQUEST[self::FORM_FIELD_NAME_NS] ) && '' !== $_REQUEST[self::FORM_FIELD_NAME_NS] ) ? trim( $_REQUEST[self::FORM_FIELD_NAME_NS] ) : null;
				$hrAction = ( $this->action === $this->actiongdprcFy ) ? 'validated' : 'un-validated';
				
				if( null === $this->action ) {					
					$this->formErrorMsg[] = __( 'Could not complete the request.', 'gdprcookies' );					
				} elseif( null === $ns ) {										
					$this->formErrorMsg[] = sprintf( __( 'Your Purchase code could not be %s. Form data not valid.', 'gdprcookies' ), $hrAction );					
				} elseif( null === $pc && null === $wc) { 				
					$this->formErrorMsg[] = __( 'The Purchase code and gdprcookies code fields are empty.', 'gdprcookies' );
					$this->formErrorFields[] = '*';					
				} 
				else {					
					if( null === $pc ) {							
						$this->formErrorMsg[] = __( 'The Purchase code field is empty.', 'gdprcookies' );
						$this->formErrorFields[] = self::FORM_FIELD_NAME_PC;
					}						
					if( null === $wc ) {					
						$this->formErrorMsg[] = __( 'The gdprcookies code field is empty.', 'gdprcookies' );
						$this->formErrorFields[] = self::FORM_FIELD_NAME_WC;
					}
				}
				
				$this->formHasErrors = ( !empty( $this->formErrorMsg ) );
					
				// supply the form with submitted data in case needed
				$this->submittedData[self::FORM_FIELD_NAME_PC] = $pc;
				$this->submittedData[self::FORM_FIELD_NAME_WC] = $wc;
					
				// if no errors occured, continue
				if( false === $this->formHasErrors ) {					
					$url = base64_decode( $this->url );
					
					switch( $this->action ) {							
						case $this->actiongdprcFy:
							$this->handleRequestForgdprcFy( $url, $pc, $wc, $ns );
							break;
						case $this->actionUngdprcFy:
							$this->handleRequestForgdprcunFy( $url, $pc, $wc, $ns );
							break;
						default:				
							break;
					}						
				}
			}			
		}
	}		

	/**
	 * Handle requests for gdprcFying
	 *
	 * @access	private
	 * 
	 * @uses	gdprcFy::_doRequest()
	 *
	 * @since 	1.2.5
	 */	
	private function handleRequestForgdprcFy( $url, $pc, $wc, $ns ) 
	{	
		$this->response = $this->doRequest( true, $url, $pc, $wc, $ns );
		
		if( gdprcRemoteHelper::responseHasErrors( $this->response ) ) {				
			// indicate the response has errors and set error params
			$this->formHasErrors = true;
			$this->formErrorMsg[] = gdprcRemoteHelper::responseGetErrorMsg( $this->response );				
		} else {		
			$this->formSuc6Msg[] = __( 'Your purchase code has been validated succesfully.', 'gdprcookies' );		
			$data = $this->getOption();
			
			if( false !== $data ) {
				if( $this->setOption( 8 ) ) {
					$this->gdprcfied = true;
					$this->justgdprcfied = true;		
				} else {		
					$this->formHasErrors = true;
					$this->formErrorMsg[] =  sprintf( __( 'Unknown error during updating your settings. Please <a href="%s">contact</a> our plugin support.', 'gdprcookies' ), 'mailto:gdprcookies-plugins@outlook.com') ;
				}
			}
		}		
	}	

	/**
	 * Handle requests for gdprc-un-Fying
	 *
	 * @access	private
	 * 
	 * @uses	gdprcFy::_doRequest()
	 *
	 * @since 	1.2.5
	 */
	private function handleRequestForgdprcunFy( $url, $pc, $wc, $ns )
	{
		$this->response = $this->doRequest( false, $url, $pc, $wc, $ns );
		
		if( gdprcRemoteHelper::responseHasErrors( $this->response ) ) {		
			// indicate the response has errors and set error params
			$this->formHasErrors = true;
			$this->formErrorMsg[] = gdprcRemoteHelper::responseGetErrorMsg( $this->response );
		
		} else {		
			$this->formSuc6Msg[] = __( 'Your purchase code has been un-validated succesfully.', 'gdprcookies' );			
			$data = $this->getOption();
			
			if( false !== $data ) {
				if( $this->setOption( 0 ) ) {
					$this->gdprcfied = false;
					$this->justgdprcfied = true;		
				} else {		
					$this->formHasErrors = true;
					$this->formErrorMsg[] =  sprintf( __( 'Unknown error during updating your settings. Please <a href="%s">contact</a> our plugin support.', 'gdprcookies' ), 'mailto:gdprcookies-plugins@outlook.com') ;
				}
			}
		}	
	}	
	
	/**
	 * Do requests for gdprc-(un)-Fying
	 *
	 * @access	private
	 * 
	 * @uses	gdprcRemoteHelper::request()
	 *
	 * @since 	1.2.5
	 */
	private function doRequest( $flag = false, $url, $pc, $wc, $ns ) 
	{
		$isMs = gdprcMultisiteHelper::isMs();
				
		$postFields = array(
				'action' => self::ACTION_UPDATE_ACTIVE_STATUS,
				'code' => $pc,
				'gdprc_code' => $wc,
				'url' => urlencode( $this->siteUrl ),
				'active' => $flag,
				'ns' => $ns,
				'type' => 'web'
		);
			
		$headers = array();
		$headers['Content-Type'] = 'application/json';
		$headers['Accept'] = 'application/json';
			
		
		$args = array(
				'method' => 'POST',
				'headers' => $headers,
				'body' => $postFields
		);
		
		return gdprcRemoteHelper::request( $url, $args );		
	}		
	
	/**
	 * Set the gdprcfied param based on the db option value
	 *
	 * @access	private
	 *
	 * @uses 	get_option
	 * @uses 	add_option
	 *
	 * @since 	1.2.3
	 */
	private function setgdprcfied()
	{
		$data = $this->getOption();		
		if( 8 === $data || 0 === $data ) {
			$this->gdprcfiedValue = $data;			
		} elseif( is_object( $data ) && isset( $data->gdprc1 ) && isset( $data->gdprc2 ) ) {			
			$this->gdprcfiedValue = (int) $data->gdprc1;
			if( 8 === $this->gdprcfiedValue && !$this->urlMatch( $data->gdprc2, false ) ) {
				$this->gdprcfiedValue = 0;
			}			
		} elseif( is_object( $data ) && ( !isset( $data->gdprc1 ) || !isset( $data->gdprc2 ) ) ) {
			$this->gdprcfiedValue = 0;
		}
		
		switch ( $this->gdprcfiedValue ) {
			case null:	
				$this->setOption( 0 );			
				$this->gdprcfied = false;
				break;
			case 0:
				$this->gdprcfied = false;
				if( 0 === $data ) {
					$this->setOption( 0 );
				}				
				break;
			case 8:
				$this->gdprcfied = true;				
				if( 8 === $data ) {
					$this->setOption( 8 );					
				}
				break;
			default:
				$this->gdprcfied = false;
				$this->setOption( 0 );	
				break;					
		}
	}		
	
	/**
	 * Add all hooks
	 *
	 * @access private
	 *
	 * @since 	1.2.3
	 */
	private function hook()
	{
		if( gdprcMultisiteHelper::isMs() && current_user_can( 'manage_network_plugins' ) ) {				
			add_action( 'network_admin_menu', array( &$this, 'addPage' ) );
			add_filter( 'network_admin_plugin_action_links_' . $this->globals->get( 'pluginFile' ), array( &$this, 'setPluginActionLinkNetwork' ), 9999, 4 );				
		} elseif( !gdprcMultisiteHelper::isMs() && current_user_can( 'manage_options' ) ) {				
			add_action( 'admin_menu', array( &$this, 'addPage' ) );
			add_filter( 'plugin_action_links_' .$this->globals->get( 'pluginFile' ), array( &$this, 'setPluginActionLink' ), 9999 );
		}
		
		add_action( 'admin_enqueue_scripts', array( &$this, 'setStylesAdmin' ) );
		if( $this->iframeRequested ) {
			add_action( 'admin_head', array( &$this, 'adminHead' ), 9999 );
		}
	}	
	
	/**
	 * Get the HTML for the validate link
	 *
	 * @access	private
	 *
	 * @since 1.2.3
	 *
	 * @return string
	 */	
	private function setNotices() 
	{
		if( false === $this->iframeRequested ) {					
			$pluginFilePath = $this->globals->get( 'pluginPathFile' );
			$plugin = gdprcMiscHelper::getPluginData( $pluginFilePath, 'Name' );
			$validateLink = $this->getValidateLink();
			$msg = '';
			
			if( !gdprcMultisiteHelper::isMs() ) {								
				$msg = sprintf( __( 'Whoeps, the <u>%s</u> plugin is <u>not validated</u>. To use the plugin, click on the %s link.', 'gdprcookies' ), $plugin, $validateLink );								
			} elseif( gdprcMultisiteHelper::isMs() && is_network_admin() && current_user_can( 'manage_network_plugins' ) ) {				
				$msg = sprintf( __( 'Whoeps, the <u>%s</u> plugin is <u>not validated</u>. To use the plugin, click on the %s link.', 'gdprcookies' ), $plugin, $validateLink );								
			} elseif( gdprcMultisiteHelper::isMs() && !is_network_admin() && current_user_can( 'manage_network_plugins' ) ) {				
				$networkPluginsUrl = network_admin_url( 'plugins.php' );
				$msg = sprintf( __( 'Whoeps, the <u>%s</u> plugin is <u>not validated</u>. To use the plugin, Please go to the <a href="%s">Network plugins page</a> and click on the "<strong>Validate<strong>" link.', 'gdprcookies' ), $plugin, $networkPluginsUrl );				
			} else {
				// @todo: message that plugins needs validation but user is not allowed to
			}
			
			if( '' !== $msg ) {
				gdprcNotices::add( $this->ns, $msg, 'error' );
			}			
		}		
	}	
	
	/**
	 * Get the HTML for the validate link
	 *
	 * @access	private
	 *
	 * @since 1.2.3
	 *
	 * @return string
	 */
	private function getValidateLink()
	{
		static $links = array();
	
		if( !isset( $links[$this->ns] ) ) {			
			$arialabel = '';
			$title = '';
			
			if( gdprcMultisiteHelper::isMs() ) {
				$url = network_admin_url( sprintf( 'index.php?page=%s&action=%s&TB_iframe=true&width=%d&height=%d', $this->slug, $this->actiongdprcFy, 600, 300 ) );
			} else {
				$url = admin_url( sprintf( 'admin.php?page=%s&action=%s&TB_iframe=true&width=%d&height=%d', $this->slug, $this->actiongdprcFy, 600, 300 ) );
			}
				
			$links[$this->ns] = sprintf( '<a style="color:#D54E21;" class="thickbox" aria-label="%s" data-title="%s" href="%s">%s</a>', $arialabel, $title, $url, __( self::PLUGIN_ACTION_LINK_VALIDATE, 'gdprcookies' ) );
		}
	
		return $links[$this->ns];
	}	
	
	/**
	 * Get the HTML for the un-validate link
	 *
	 * @access	private
	 *
	 * @since 1.2.5
	 *
	 * @return string
	 */
	private function getUnValidateLink()
	{
		static $links = array();
	
		if( !isset( $links[$this->ns] ) ) {
			$arialabel = '';
			$title = '';
			
			if( gdprcMultisiteHelper::isMs() ) {
				$url = network_admin_url( sprintf( 'index.php?page=%s&action=%s&TB_iframe=true&width=%d&height=%d', $this->slug, $this->actionUngdprcFy, 600, 300 ) );
			} else {
				$url = admin_url( sprintf( 'admin.php?page=%s&action=%s&TB_iframe=true&width=%d&height=%d', $this->slug, $this->actionUngdprcFy, 600, 300 ) );
			}
	
			$links[$this->ns] = sprintf( '<a style="color:#D54E21;" class="thickbox" aria-label="%s" data-title="%s" href="%s">%s</a>', $arialabel, $title, $url, __( self::PLUGIN_ACTION_LINK_UNVALIDATE, 'gdprcookies' ) );
		}
	
		return $links[$this->ns];
	}
	
	
	/**
	 * Sets the option, gdprcookiesFy
	 *
	 * @access	private
	 *
	 * @since 1.2.5
	 * 
	 * @return bool true on success or false on failure
	 */	
	private function setOption( $w ) 
	{
		static $p = null;
		
		$isMs = gdprcMultisiteHelper::isMs();
		
		if( null === $p ) {
			$p = $this->siteUrl;
		}
				
		$o = new stdClass();
		$o->gdprc1 = $w;
		$o->gdprc2 = $p;
		$fy = base64_encode( serialize( $o ) );
		if( 0 === $w ) {
			//unset(self::$instancens[$this->ns]);
		}
		return gdprcMultisiteHelper::updateOption( $this->optionName, $fy, $isMs );
	}
	
	
	/**
	 * Gets the option, gdprcookiesFy
	 *
	 * @access	private
	 *
	 * @since 1.2.5
	 * 
	 * @return object, int or bool false on failure
	 */
	private function getOption()
	{
		$isMs = gdprcMultisiteHelper::isMs();
		$fy = gdprcMultisiteHelper::getOption( $this->optionName, null, $isMs );
		if( null !== $fy ) {			
			// backward compat
			if( is_numeric( $fy ) && 0 === (int)$fy || 8 === (int)$fy ) {
				return (int)$fy;
			}
			$o = @unserialize( @base64_decode( $fy ) );
			if( is_object( $o ) && isset( $o->gdprc1 ) ) {
				return $o;
			}
		}
				
		return false;		
	}
	
	/**
	 * Check if current install site URL matches the gdprcFy URL
	 *
	 * @access	private
	 *
	 * @since 1.2.5
	 * 
	 * @return object or bool false on failure
	 */
	private function urlMatch( $p = '', $fullMatch = true )
	{
		if( !is_string( $p ) ) {
			return false;
		} elseif( '' === trim( $p ) ) {
			return false;
		}
		
		$p = gdprcMiscHelper::getCleanUri( $p );
		$p2 = gdprcMiscHelper::getCleanUri( $this->siteUrl );
		
		if( !$fullMatch ) {
			$p = gdprcMiscHelper::getHostWithoutSubdomain( $p );
			$p2 = gdprcMiscHelper::getHostWithoutSubdomain( $p2 );
		}
		
		return ( $p === $p2 ) ? true : false;		
	}
	
	/**
	 * Determin if form has been submitted
	 * 
	 * @since 1.4.7
	 */
	private function isformSubmitted()
	{
		return ( isset( $_REQUEST[self::FORM_FIELD_NAME_NS] ) );
	}
	
	private function getActionFromRequest()
	{
		return ( isset( $_GET['action'] ) ) ? $_GET['action'] : false;
	}
}