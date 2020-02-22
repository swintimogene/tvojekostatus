<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('EPO_Admin')):
 
class EPO_Admin {
	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.3.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	
	public function enqueue_styles_and_scripts($hook) {
		if(strpos($hook, 'product_page_th_extra_product_options_pro') === false) {
			return;
		}
		$debug_mode = apply_filters('epo_debug_mode', false);
		$suffix = $debug_mode ? '' : '.min';
		
		$this->enqueue_styles($suffix);
		$this->enqueue_scripts($suffix);
	}
	
	private function enqueue_styles($suffix) {
		wp_enqueue_style('jquery-ui-style', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css?ver=1.11.4');
		wp_enqueue_style('woocommerce_admin_styles', EPO_WOO_ASSETS_URL.'css/admin.css');
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style('epo-admin-style', EPO_ASSETS_URL_ADMIN . 'css/epo-admin'. $suffix .'.css', $this->version);
		//wp_enqueue_style('epo-colorpicker-style', EPO_ASSETS_URL_ADMIN . 'colorpicker/spectrum.css');
	}

	private function enqueue_scripts($suffix) {
		$deps = array('jquery', 'jquery-ui-dialog', 'jquery-ui-sortable', 'jquery-tiptip', 'woocommerce_admin', 'wc-enhanced-select', 'select2', 'wp-color-picker');
		
		/*wp_enqueue_script('epo-admin-base', EPO_ASSETS_URL_ADMIN . 'js/inc/epo-admin-base.js', $deps, $this->version, false);
		wp_enqueue_script('epo-admin-conditions', EPO_ASSETS_URL_ADMIN . 'js/inc/epo-admin-conditions.js', array('epo-admin-base'), $this->version, false);
		wp_enqueue_script('epo-admin-script', EPO_ASSETS_URL_ADMIN . 'js/inc/epo-admin.js', array('epo-admin-base', 'epo-admin-conditions'), $this->version, false);
		wp_enqueue_script('epo-admin-conditions', EPO_ASSETS_URL_ADMIN . 'js/inc/epo-admin-advanced.js', array('epo-admin-base'), $this->version, false);
		*/		
		wp_enqueue_script( 'epo-admin-script', EPO_ASSETS_URL_ADMIN . 'js/epo-admin'. $suffix .'.js', $deps, $this->version, false );
		
		$wepo_var = array(
            'admin_url' => admin_url(),
            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        );
		wp_localize_script('epo-admin-script', 'wepo_var', $wepo_var);
	}
	
	public function admin_menu() {
		$this->screen_id = add_submenu_page('edit.php?post_type=product', EPO_i18n::__t('WooCommerce Extra Product Option'), 
		EPO_i18n::__t('Extra Product Option'), 'manage_woocommerce', 'th_extra_product_options_pro', array($this, 'output_settings'));

		//add_action('admin_print_scripts-'. $this->screen_id, array($this, 'enqueue_admin_scripts'));
	}
	
	public function add_screen_id($ids){
		$ids[] = 'woocommerce_page_th_extra_product_options_pro';
		$ids[] = strtolower( EPO_i18n::__t('WooCommerce') ) .'_page_th_extra_product_options_pro';

		return $ids;
	}
	
	public function plugin_action_links($links) {
		$settings_link = '<a href="'.admin_url('edit.php?post_type=product&page=th_extra_product_options_pro').'">'. __('Settings') .'</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	
	public function plugin_row_meta( $links, $file ) {
		if(EPO_BASE_NAME == $file) {
			$doc_link = esc_url('https://epo.localhost.localhost/help-guides/woocommerce-extra-product-options/');
			$support_link = esc_url('https://epo.localhost.localhost/help-guides/');
				
			$row_meta = array(
				'docs' => '<a href="'.$doc_link.'" target="_blank" aria-label="'.EPO_i18n::esc_attr__t('View plugin documentation').'">'.EPO_i18n::esc_html__t('Docs').'</a>',
				'support' => '<a href="'.$support_link.'" target="_blank" aria-label="'. EPO_i18n::esc_attr__t('Visit premium customer support' ) .'">'. EPO_i18n::esc_html__t('Premium support') .'</a>',
			);

			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}
	
	public function output_settings(){
		$tab  = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'general_settings';
		
		if($tab === 'advanced_settings'){			
			$advanced_settings = EPO_Admin_Settings_Advanced::instance();	
			$advanced_settings->render_page();			
		}else if($tab === 'license_settings'){			
			$license_settings = EPO_Admin_Settings_License::instance();	
			$license_settings->render_page();	
		}else{
			$general_settings = EPO_Admin_Settings_General::instance();	
			$general_settings->render_page();
		}
	}
}

endif;