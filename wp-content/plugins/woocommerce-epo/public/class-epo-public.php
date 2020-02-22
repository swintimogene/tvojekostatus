<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/public
 */
if(!defined('WPINC')){	die; }

if(!class_exists('EPO_Public')):
 
class EPO_Public {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		//$this->define_public_hooks();
		add_action('after_setup_theme', array($this, 'define_public_hooks'));
	}

	public function enqueue_styles_and_scripts() {
		global $wp_scripts;
		$is_quick_view = EPO_Utils::is_quick_view_plugin_active();
		
		if(is_product() || ( $is_quick_view && (is_shop() || is_product_category()) ) || apply_filters('epo_enqueue_public_scripts', false)){
			$debug_mode = apply_filters('epo_debug_mode', false);
			$suffix = $debug_mode ? '' : '.min';
			$jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
			
			$this->enqueue_styles($suffix, $jquery_version);
			$this->enqueue_scripts($suffix, $jquery_version, $is_quick_view);
		}
	}
	
	private function enqueue_styles($suffix, $jquery_version) {
		wp_register_style('select2', EPO_WOO_ASSETS_URL.'/css/select2.css');
		
		wp_enqueue_style('select2');
		wp_enqueue_style('jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/'. $jquery_version .'/themes/smoothness/jquery-ui.css');
		wp_enqueue_style('epo-timepicker-style', EPO_ASSETS_URL_PUBLIC.'js/timepicker/jquery.timepicker.css');
		wp_enqueue_style('epo-public-style', EPO_ASSETS_URL_PUBLIC . 'css/epo-public'. $suffix .'.css', $this->version);
	}

	private function enqueue_scripts($suffix, $jquery_version, $is_quick_view) {
		$in_footer = apply_filters( 'epo_enqueue_script_in_footer', true );
		$deps = array();
		
		wp_register_script('epo-timepicker-script', EPO_ASSETS_URL_PUBLIC.'js/timepicker/jquery.timepicker.min.js', array('jquery'), '1.0.1');
			
		if(apply_filters('epo_include_jquery_ui_i18n', true)){
			wp_register_script('jquery-ui-i18n', '//ajax.googleapis.com/ajax/libs/jqueryui/'.$jquery_version.'/i18n/jquery-ui-i18n.min.js',
			array('jquery','jquery-ui-datepicker'), $in_footer);
			
			$deps[] = 'jquery-ui-i18n';
		}else{
			$deps[] = 'jquery';
			$deps[] = 'jquery-ui-datepicker';
		}
		
		if(EPO_Utils::get_settings('disable_select2_for_select_fields') != 'yes'){
			$deps[] = 'select2';
			
			$select2_languages = apply_filters( 'epo_select2_i18n_languages', false);
			if(is_array($select2_languages)){
				foreach($select2_languages as $lang){
					$handle = 'select2_i18n_'.$lang;
					wp_register_script($handle, '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/i18n/'.$lang.'.js', array('jquery','select2'));
					$deps[] = $handle;
				}
			}
		}

		wp_register_script('epo-public-script', EPO_ASSETS_URL_PUBLIC . 'js/epo-public'. $suffix .'.js', $deps, $this->version, true );
		
		wp_enqueue_script('epo-timepicker-script');						
		wp_enqueue_script('epo-public-script');
		
		$wepo_var = array(
			'lang' => array( 
						'am' => EPO_i18n::__t('am'), 
						'pm' => EPO_i18n::__t('pm'),  
						'AM' => EPO_i18n::__t('AM'), 
						'PM' => EPO_i18n::__t('PM'),
						'decimal' => EPO_i18n::__t('.'), 
						'mins' => EPO_i18n::__t('mins'), 
						'hr'   => EPO_i18n::__t('hr'), 
						'hrs'  => EPO_i18n::__t('hrs'),
					),
			'language' 	  => EPO_Utils::get_locale_code(),
			'date_format' => EPO_Utils::get_jquery_date_format(wc_date_format()),
			'readonly_date_field' => apply_filters('epo_date_picker_field_readonly', true),
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
			'price_ph_simple'	=> apply_filters('epo_product_price_placeholder', ''),
			'price_ph_variable'	=> apply_filters('epo_variable_product_price_placeholder', ''),
			'is_quick_view' => $is_quick_view,
		);
		wp_localize_script('epo-public-script', 'epo_public_var', $wepo_var);
	}
	
	public function define_public_hooks(){
		$hp_add_to_cart_link = apply_filters('epo_loop_add_to_cart_link_hook_priority', 10);
		$hp_display = apply_filters('epo_display_hooks_priority', 10);
		$hp_validation = apply_filters('epo_add_to_cart_validation_hook_priority', 99);
		$hp_bf_total = apply_filters('epo_before_calculate_totals_hook_priority', 1);
		$hp_new_order = apply_filters('epo_new_order_item_hook_priority', 10);
		$hp_bf_mini_cart = apply_filters('epo_before_mini_cart_hook_priority', 10);
		$hp_add_item_data = apply_filters('epo_add_cart_item_data_hook_priority', 10);

		$hook_name_before_single_product = apply_filters('hook_name_before_single_product', 'woocommerce_before_single_product');
		$hook_name_before_add_to_cart_button = apply_filters('hook_name_before_add_to_cart_button', 'woocommerce_before_add_to_cart_button');
		$hook_name_after_add_to_cart_button = apply_filters('hook_name_after_add_to_cart_button', 'woocommerce_after_add_to_cart_button');
		
		if(EPO_Utils::woo_version_check('3.3')){
			add_filter('woocommerce_loop_add_to_cart_link', array($this, 'filter_loop_add_to_cart_link'), $hp_add_to_cart_link, 3);
		}else{
			add_filter('woocommerce_loop_add_to_cart_link', array($this, 'filter_loop_add_to_cart_link'), $hp_add_to_cart_link, 2);
		}
		
		add_action( $hook_name_before_single_product, array($this, 'action_before_single_product') );
		if(EPO_Utils::is_yith_quick_view_enabled()){
			add_action('yith_wcqv_product_summary', array($this, 'action_before_single_product'), 1);
		}
		if(EPO_Utils::is_flatsome_quick_view_enabled()){
			add_action('woocommerce_single_product_lightbox_summary',array($this, 'action_before_single_product'), 1);
		}
		if(EPO_Utils::is_astra_quick_view_enabled()){
			add_action('astra_woo_quick_view_product_summary',array($this, 'action_before_single_product'), 1);
		}
		
		add_action( $hook_name_before_add_to_cart_button, array($this, 'action_before_add_to_cart_button'), $hp_display);	
		add_action( $hook_name_after_add_to_cart_button, array($this, 'action_after_add_to_cart_button'), $hp_display);
		add_action( 'woocommerce_single_variation', array($this, 'action_before_variation_data'), 5);
		//add_action( 'woocommerce_before_add_to_cart_quantity', array($this, 'action_before_add_to_cart_quantity'), $hp_display);
		//add_action( 'woocommerce_after_add_to_cart_quantity', array($this, 'action_after_add_to_cart_quantity'), $hp_display);
		//add_action( 'woocommerce_before_variations_form', array($this, 'action_before_variations_form'), $hp_display);
		//add_action( 'woocommerce_after_variations_form', array($this, 'action_after_variations_form'), $hp_display);
		//add_action( 'woocommerce_before_single_variation', array($this, 'action_before_single_variation'), $hp_display);
		//add_action( 'woocommerce_after_single_variation', array($this, 'action_after_single_variation'), $hp_display);
		//add_action( 'woocommerce_single_variation', array($this, 'action_single_variation_90'), 90);
		
		add_filter( 'woocommerce_add_to_cart_validation', array($this, 'filter_add_to_cart_validation'), $hp_validation, 6 );
		add_filter( 'woocommerce_add_cart_item_data', array($this, 'filter_add_cart_item_data'), $hp_add_item_data, 3 );
		add_filter( 'woocommerce_get_item_data', array($this, 'filter_get_item_data'), 10, 2 );
		
		if(EPO_Utils::is_rightpress_dynamic_pricing_plugin_active()){
			add_action('woocommerce_cart_loaded_from_session',array($this, 'action_before_calculate_totals'), $hp_bf_total, 1);
		}else{
			add_action('woocommerce_before_calculate_totals', array($this, 'action_before_calculate_totals'), $hp_bf_total, 1);
		}
		
		//add_action('woocommerce_before_calculate_totals', array($this, 'action_before_calculate_totals'), $hp_bf_total, 1);
		if(EPO_Utils::is_woo_dynamic_pricing_plugin_active()){
			add_action('wc_memberships_discounts_disable_price_adjustments', array($this, 'action_memberships_discounts_disable_price_adjustments'));
			add_filter('wc_dynamic_pricing_apply_cart_item_adjustment', array($this, 'filter_dynamic_pricing_apply_cart_item_adjustment'), 10, 4);
			add_filter('woocommerce_dynamic_pricing_get_price_to_discount', array($this, 'filter_dynamic_pricing_get_price_to_discount'), 10, 3);
			//add_filter('wc_dynamic_pricing_get_use_sale_price', array($this, 'filter_dynamic_pricing_get_use_sale_price'));
		}
		//add_action( 'woocommerce_new_order_item', array($this, 'woo_new_order_item'), $hp_new_order, 3);
		//add_action( 'woocommerce_add_order_item_meta', array($this, 'woo_add_order_item_meta'), 1, 3 ); //Older version WooCommerce support
		
		if(EPO_Utils::woo_version_check()){
			add_action( 'woocommerce_new_order_item', array($this, 'woo_new_order_item'), $hp_new_order, 3);
		}else{
			add_action( 'woocommerce_add_order_item_meta', array($this, 'woo_add_order_item_meta'), 1, 3 ); //Older version WooCommerce support
		}
		
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', array($this, 'filter_order_item_get_formatted_meta_data'), 10, 2);
		
		add_action( 'woocommerce_before_mini_cart', array($this, 'action_recalculate_total'), $hp_bf_mini_cart );
		
		add_action('wp_ajax_epo_calculate_extra_cost', array($this, 'wp_ajax_action_calculate_extra_cost_handler'), 10);
    	add_action('wp_ajax_nopriv_epo_calculate_extra_cost', array($this, 'wp_ajax_action_calculate_extra_cost_handler'), 10);

    	add_filter('woocommerce_order_again_cart_item_data', array($this, 'filter_order_again_cart_item_data'), 10, 3); 
	}
	
	public function filter_loop_add_to_cart_link($link, $product, $args=false){
		$modify = apply_filters('epo_modify_loop_add_to_cart_link', true);
		
		$product_type = false;
		if(EPO_Utils::woo_version_check()){
			$product_type = $product->get_type();
		}else{
			$product_type = $product->product_type;
		}
		
		if($modify && EPO_Utils::has_extra_options($product) && $product->is_in_stock() && ($product_type === 'simple' || $product_type === 'bundle')){
			$link_text = EPO_Utils::get_settings('add_to_cart_link_text');
			$link_text = $link_text ? $link_text : 'Select options';
			
			$product_id = false;
			$product_sku = false;
    		if(EPO_Utils::woo_version_check()){
    			$product_id = $product->get_id();
    			$product_sku = $product->get_sku();
    		}else{
    			$product_id = $product->id;
    			$product_sku = $product->sku;
    		}
			
			if(EPO_Utils::woo_version_check('3.3')){
				if(isset($args['class'])){
					$args['class'] = str_replace("ajax_add_to_cart", "", $args['class']);
				}

				$link = sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
					esc_url( $product->get_permalink() ),
					esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
					esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
					isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
					esc_html( EPO_i18n::__t($link_text) )
				);
			}else{
				$link = sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
					esc_url( $product->get_permalink() ),
					esc_attr( isset( $quantity ) ? $quantity : 1 ),
					esc_attr( $product_id ),
					esc_attr( $product_sku ),
					esc_attr( isset( $class ) ? $class : 'button' ),
					esc_html( EPO_i18n::__t($link_text) )
				);
			}
		}
		return $link;
	}
	
	public function action_before_single_product(){
		global $product;
		
		$product_id = false;
		if(EPO_Utils::woo_version_check()){
			$product_id = $product->get_id();
		}else{
			$product_id = $product->id;
		}
		
		$categories = EPO_Utils::get_product_categories($product);
		
		$sections = EPO_Utils::get_custom_sections();
		$section_hook_map = array();
		
		if($sections && is_array($sections) && !empty($sections)){
			foreach($sections as $section_name => $section){
				$section = EPO_Utils_Section::prepare_section_and_fields($section, $product_id, $categories);
				
				if($section){
					$hook_name = $section->get_property('position');
					if(array_key_exists($hook_name, $section_hook_map) && is_array($section_hook_map[$hook_name])) {
						$section_hook_map[$hook_name][$section_name] = $section;
					}else{
						$section_hook_map[$hook_name] = array();
						$section_hook_map[$hook_name][$section_name] = $section;
					}
				}
			}
		}
		
		$this->sections_extra = $section_hook_map;
	}
	
	public function action_before_add_to_cart_button(){
		$this->render_disabled_field_names_hidden_field();
		$this->render_sections('woo_before_add_to_cart_button');
	}
	public function action_after_add_to_cart_button(){
		$this->render_sections('woo_after_add_to_cart_button');
	}
	public function action_before_add_to_cart_quantity(){
		$this->render_sections('woo_before_add_to_cart_quantity');
	}
	public function action_after_add_to_cart_quantity(){
		$this->render_sections('woo_after_add_to_cart_quantity');
	}
	public function action_before_variations_form(){
		$this->render_sections('woo_before_variations_form');
	}
	public function action_after_variations_form(){
		$this->render_sections('woo_after_variations_form');
	}
	public function action_before_single_variation(){
		$this->render_sections('woo_before_single_variation');
	}
	public function action_after_single_variation(){
		$this->render_sections('woo_after_single_variation');
	}
	public function action_before_variation_data(){
		$this->render_sections('woo_single_variation_5');
	}
	public function action_single_variation_90(){
		$this->render_sections('woo_single_variation_90');
	}
	
	public function filter_add_to_cart_validation($passed, $product_id, $quantity, $variation_id=false, $variations=false, $cart_item_data=false){ 
		$extra_options = $this->prepare_product_options(false);
		$ignore_unposted = apply_filters( 'epo_ignore_unposted_fields', false );
		
		if($extra_options){
			//$upload_fields = array();
			
			foreach($extra_options as $field_name => $field){
				$type = $field->get_property('type');
				$is_posted = isset($_POST[$field_name]) || isset($_REQUEST[$field_name]) ? true : false;
				$posted_value = $this->get_posted_value($field_name, $type);
				
				if(($type === 'radio' || $type === 'multiselect' || $type === 'checkboxgroup') && (!$is_posted || !$posted_value) && !$ignore_unposted){
					$passed = $this->validate_field($passed, $field, $posted_value);
					
				}else if($type === 'file'){
					//$upload_fields[$field_name] = $field;
					$file = isset($_FILES[$field_name]) ? $_FILES[$field_name] : false;
					$passed = $this->validate_file($passed, $field, $file);
					
				}else if($is_posted){
					$passed = $this->validate_field($passed, $field, $posted_value);
				}
			}
			
			/*if($passed){
				foreach($upload_fields as $name => $field){
					$uploaded = $this->upload_file($_FILES, $name, $field);
					if(isset($uploaded['error'])){
						$this->wepo_add_error('<strong>'.$title.'</strong> '. $upload['error']);
						$passed = false;
					}
				}
			}*/
		}
		return $passed;
	}
	
	// Load cart item data - may be added by other plugins.
	public function filter_add_cart_item_data($cart_item_data, $product_id = 0, $variation_id = 0){
		$skip_bundled_items = (isset($cart_item_data['bundled_by']) && apply_filters('epo_skip_extra_options_for_bundled_items', true)) ? true : false;
		
		if(!$skip_bundled_items){
			$extra_cart_item_data = $this->prepare_extra_cart_item_data();
			
			if($extra_cart_item_data){
				if(apply_filters('epo_set_unique_key_for_cart_item', false, $cart_item_data, $product_id, $variation_id)){
					$cart_item_data['unique_key'] = md5( microtime().rand() );
				}
				$cart_item_data['epo_options'] = $extra_cart_item_data;
			}
		}
		return $cart_item_data;
	}
	
	private function calculate_cart_item_extra_cost($cart_item, $return_extra_cost=false){
		$result = 0;
		if($cart_item) { 		
			$extra_options = isset($cart_item['epo_options']) ? $cart_item['epo_options'] : false;
			if($extra_options) {				
				$quantity = floatval( $cart_item['quantity'] );
				$orgPrice = floatval( $cart_item['data']->get_price('') );
				$extra_price = 0;
				
				if(isset($cart_item['epo-original_price']) && is_numeric($cart_item['epo-original_price'])){
					$orgPrice = floatval( $cart_item['epo-original_price'] );
				}else{
					$cart_item['epo-original_price'] = $orgPrice;
				}

				$product_info = array();
				$product_info['id'] = $cart_item['product_id'];
				$product_info['price'] = $orgPrice;
				
				foreach($extra_options as $name => $data){
					if(isset($data['price_field']) && $data['price_field']){
						$extra_price = $extra_price + EPO_Utils_Price::get_extra_cost_final($product_info, $data);
					}
				}

				//$extra_price = apply_filters('epo_cart_item_extra_cost', $extra_price, $cart_item);
				
				if($return_extra_cost){
					$result = $extra_price;
				}else{
					$result = $orgPrice + $extra_price;
				}
			}           
		}
		return $result;
	}
	
	public static function is_woo_dynamic_price_applied($cart_item){
		$result = false;
		if(EPO_Utils::is_woo_dynamic_pricing_plugin_active() && $cart_item && isset($cart_item['discounts'])){
			$result = true;
		}
		return $result;
	}
	
	public function action_before_calculate_totals($cart_object){
		foreach($cart_object->cart_contents as $key => &$value) {
			if(!self::is_woo_dynamic_price_applied($value)){
				/*$new_price = $this->calculate_cart_item_extra_cost($value);
				if($new_price){
					$value['data']->set_price($new_price);
				}*/
		 		
				$extra_options = isset($value['epo_options']) ? $value['epo_options'] : false;
				if($extra_options) {
					$product_id = isset($value['product_id']) ? $value['product_id'] : false;				
					$quantity = floatval( $value['quantity'] );
					$orgPrice = floatval( $value['data']->get_price('') );
					$extra_price = 0;
					$has_price_fields = false;
					$exclude_base_price = apply_filters('epo_extra_cost_exclude_base_price', false, $product_id);
					
					if(isset($value['epo-original_price']) && is_numeric($value['epo-original_price'])){
						$orgPrice = floatval( $value['epo-original_price'] );
					}else{
						$value['epo-original_price'] = $orgPrice;
					}

					$product_info = array();
					$product_info['id'] = $product_id;
					$product_info['price'] = $orgPrice;
					
					foreach($extra_options as $name => $data){
						if(isset($data['price_field']) && $data['price_field']){
							$extra_price = $extra_price + EPO_Utils_Price::get_extra_cost_final($product_info, $data);
							$has_price_fields = true;
							if($data['price_type'] === "dynamic-excl-base-price") {
								$exclude_base_price = true;
							}
						}
					}
					if($has_price_fields){
						$new_price = $exclude_base_price && $extra_price ? $extra_price : $orgPrice + $extra_price;
						$value['data']->set_price($new_price);
					}
				}
			}     
		}
	}
	
	public function filter_dynamic_pricing_get_price_to_discount($result, $value, $key){
		$new_price = $this->calculate_cart_item_extra_cost($value);
		if($new_price){
			$result = $new_price;
		}
		return $result;	
		/*$extra_options = isset($value['epo_options']) ? $value['epo_options'] : false;
		if($extra_options) {
			$quantity = floatval( $value['quantity'] );
			$orgPrice = floatval( $value['data']->get_price('') );
			$extra_price = 0;
			
			if(isset($value['epo-original_price']) && is_numeric($value['epo-original_price'])){
				$orgPrice = floatval( $value['epo-original_price'] );
			}else{
				$value['epo-original_price'] = $orgPrice;
			}
		
			foreach($extra_options as $name => $data){
				if(isset($data['price_field']) && $data['price_field']){
					$extra_price = $extra_price + EPO_Utils_Price::get_extra_cost_final($orgPrice, $data);
				}
			}
			$result = $orgPrice + $extra_price;
		}
		return $result;*/
	}
	
	public function filter_dynamic_pricing_get_use_sale_price($value, $product){
		$value = apply_filters('epo_dynamic_pricing_display_price_excluding_extra_cost', false);
		return $value;
	}

	public function action_memberships_discounts_disable_price_adjustments(){
       remove_filter('wc_dynamic_pricing_get_use_sale_price', array($this, 'filter_dynamic_pricing_get_use_sale_price'), 10);
   	}

	public function filter_dynamic_pricing_apply_cart_item_adjustment($adjusted_price, $cart_item_key, $original_price, $module){
		if($cart_item_key && isset(WC()->cart->cart_contents[ $cart_item_key])){
			$cart_item = WC()->cart->cart_contents[$cart_item_key];
			$new_price = $this->calculate_cart_item_extra_cost($cart_item);
			if($new_price){
				add_filter('wc_dynamic_pricing_get_use_sale_price', array($this, 'filter_dynamic_pricing_get_use_sale_price'), 10, 2);
			}
		}		
		return $adjusted_price;
	}
	
	// Filter item data to allow 3rd parties to add more to the array.
	public function filter_get_item_data($item_data, $cart_item = null){
		if(apply_filters('epo_display_custom_cart_item_meta', true)){
			$item_data = is_array($item_data) ? $item_data : array();		
			$extra_options = $cart_item && isset($cart_item['epo_options']) ? $cart_item['epo_options'] : false;
			$product_price = $cart_item && isset($cart_item['epo-original_price']) ? $cart_item['epo-original_price'] : false;
			$display_option_text = apply_filters('epo_order_item_meta_display_option_text', true);
			
			if($extra_options){
				$product_info = array();
				$product_info['id'] = $cart_item['product_id'];
				$product_info['price'] = $product_price;

				foreach($extra_options as $name => $data){
					if(isset($data['value']) && isset($data['label'])) {
						$ftype = isset($data['field_type']) ? $data['field_type'] : false;
						$value = isset($data['value']) ? $data['value'] : '';
						
						if($ftype === 'file'){
							$value = EPO_Utils::get_file_display_name($value, apply_filters('epo_item_display_filename_as_link', false, $name));
							//$value = EPO_Utils::get_filename_from_path($value);
						}else{
							$value = is_array($value) ? implode(",", $value) : $value;
							$value = $display_option_text ? EPO_Utils::get_option_display_value($name, $value, $data) : $value;
						}
						
						$is_show_price = apply_filters('epo_show_price_for_item_meta', true, $name);
						if($is_show_price){
							$value .= EPO_Utils_Price::get_display_price_item_meta($data, $data['price_type'], $data['price'], $product_info);
						}
						
						$item_data[] = array("name" => EPO_i18n::__t($data['label']), "value" => trim(stripslashes($value)));
					}
				}
			}
		}
		return $item_data;
	}
	
	public function woo_new_order_item($item_id, $item, $order_id){
		$legacy_values = is_object($item) && isset($item->legacy_values) ? $item->legacy_values : false;
		if($legacy_values){
			$extra_options = isset($legacy_values['epo_options']) ? $legacy_values['epo_options'] : false;
			$product_price = isset($legacy_values['epo-original_price']) ? $legacy_values['epo-original_price'] : false;
			
			$this->add_order_item_meta($item_id, $item, $extra_options, $product_price);
			
			/*if($extra_options){		
				$product_info = array();
				$product_info['id'] = $item['product_id'];
				$product_info['price'] = $product_price;

				foreach($extra_options as $name => $data){
					$ftype = isset($data['field_type']) ? $data['field_type'] : false;
					$value = isset($data['value']) ? $data['value'] : '';
					
					if($ftype === 'file'){
						$value = json_encode($value);//EPO_Utils::get_file_display_name($value);
					}else{
						$value = is_array($value) ? implode(",", $value) : $value;
					}
					
					//$value = is_array($data['value']) ? implode(",", $data['value']) : $data['value'];
					$display_value = $value;

					$price_html = EPO_Utils_Price::get_display_price_item_meta($data, $data['price_type'], $data['price'], $product_info, true);
					if($price_html){
						$price_meta_key_prefix = $this->get_order_item_price_meta_key_prefix();
						wc_add_order_item_meta( $item_id, $price_meta_key_prefix.$name, trim(stripslashes(apply_filters('epo_add_order_item_meta_price_html', $price_html, $name, $data))) );
					}
					
					$is_show_price = apply_filters('epo_show_price_for_order_formatted_meta', true, $name);
					if($is_show_price){
						//$display_value .= EPO_Utils_Price::get_display_price_item_meta($data, $data['price_type'], $data['price'], $product_info, true);
						$display_value .= $price_html;
					}
					
					wc_add_order_item_meta( $item_id, $name, trim(stripslashes(apply_filters('epo_add_order_item_meta_value', $value, $name, $display_value))) );
				}
			}*/
		}
	}
	
	public function woo_add_order_item_meta( $item_id, $values, $cart_item_key ) {
		if($values && is_array($values)){
			$extra_options = isset($values['epo_options']) ? $values['epo_options'] : false;
			$product_price = isset($values['epo-original_price']) ? $values['epo-original_price'] : false;
			
			$this->add_order_item_meta($item_id, $values, $extra_options, $product_price);

			/*if($extra_options){
				$product_info = array();
				$product_info['id'] = $values['product_id'];
				$product_info['price'] = $product_price;

				foreach($extra_options as $name => $data){
					$ftype = isset($data['field_type']) ? $data['field_type'] : false;
					$value = isset($data['value']) ? $data['value'] : '';
					
					if($ftype === 'file'){
						$value = json_encode($value);//EPO_Utils::get_file_display_name($value);
					}else{
						$value = is_array($value) ? implode(",", $value) : $value;
					}
					
					//$value = is_array($data['value']) ? implode(",", $data['value']) : $data['value'];
					$display_value = $value;

					$price_html = EPO_Utils_Price::get_display_price_item_meta($data, $data['price_type'], $data['price'], $product_info, true);
					if($price_html){
						$price_meta_key_prefix = $this->get_order_item_price_meta_key_prefix();
						wc_add_order_item_meta( $item_id, $price_meta_key_prefix.$name, trim(stripslashes(apply_filters('epo_add_order_item_meta_price_html', $price_html, $name, $data))) );
					}
					
					$is_show_price = apply_filters('epo_show_price_for_order_formatted_meta', true, $name);
					if($is_show_price){
						//$display_value .= EPO_Utils_Price::get_display_price_item_meta($data, $data['price_type'], $data['price'], $product_info, true);
						$display_value .= $price_html;
					}
					
					wc_add_order_item_meta( $item_id, $name, trim(stripslashes(apply_filters('epo_add_order_item_meta_value', $value, $name, $display_value))) );
				}
			}*/
		}
	}

	public function add_order_item_meta($item_id, $item, $extra_options, $product_price) {
		if($extra_options){
			$product_info = array();
			$product_info['id'] = $item['product_id'];
			$product_info['price'] = $product_price;

			foreach($extra_options as $name => $data){
				$ftype = isset($data['field_type']) ? $data['field_type'] : false;
				$value = isset($data['value']) ? $data['value'] : '';
				
				if($ftype === 'file'){
					$value = json_encode($value);//EPO_Utils::get_file_display_name($value);
				}else{
					$value = is_array($value) ? implode(",", $value) : $value;
				}
				
				//$value = is_array($data['value']) ? implode(",", $data['value']) : $data['value'];
				$display_value = $value;

				$price_html = EPO_Utils_Price::get_display_price_item_meta($data, $data['price_type'], $data['price'], $product_info, true);
				if($price_html){
					$price_meta_key_prefix = $this->get_order_item_price_meta_key_prefix();
					wc_add_order_item_meta( $item_id, $price_meta_key_prefix.$name, trim(stripslashes(apply_filters('epo_add_order_item_meta_price_html', $price_html, $name, $data))) );
				}
				
				$is_show_price = apply_filters('epo_show_price_for_order_formatted_meta', true, $name);
				if($is_show_price){
					//$display_value .= EPO_Utils_Price::get_display_price_item_meta($data, $data['price_type'], $data['price'], $product_info, true);
					$display_value .= $price_html;
				}
				
				wc_add_order_item_meta( $item_id, $name, trim(stripslashes(apply_filters('epo_add_order_item_meta_value', $value, $name, $display_value))) );
			}
		}
	}
	
	public function upload_file($file, $name, $field){
		$upload = false;
		
		if(is_array($file)){
			if(!function_exists('wp_handle_upload')){
				require_once(ABSPATH. 'wp-admin/includes/file.php');
				require_once(ABSPATH. 'wp-admin/includes/media.php');
			}
			
			add_filter('upload_dir', array('EPO_Utils', 'upload_dir'));
			//add_filter('upload_mimes', array('EPO_Utils', 'upload_mimes'));
			$upload = wp_handle_upload($file, array('test_form' => false));
			remove_filter('upload_dir', array('EPO_Utils', 'upload_dir'));
			//remove_filter('upload_mimes', array('EPO_Utils', 'upload_mimes'));
			
			/*if($upload && !isset($upload['error'])){
				echo "File is valid, and was successfully uploaded.\n";
				var_dump( $upload );
			} else {
				echo $upload['error'];
			}*/
		}
		return $upload;
	}
		
	public function filter_order_item_get_formatted_meta_data($formatted_meta, $order_item){
		if(!empty($formatted_meta)){
			//$name_title_map = EPO_Utils::get_options_name_title_map();
			$custom_fields = EPO_Utils::get_custom_fields_full();
			$display_option_text = apply_filters('epo_order_item_meta_display_option_text', true);
			$price_meta_key_prefix = $this->get_order_item_price_meta_key_prefix();
			
			//if($name_title_map){
			if($custom_fields){
				foreach($formatted_meta as $key => $meta){
					//if(array_key_exists($meta->key, $name_title_map)) {
					if(array_key_exists($meta->key, $custom_fields)) {
						$field = $custom_fields[$meta->key];
						$type = $field->get_property('type');
						$display_key = EPO_Utils_Field::get_display_label($field);
						$value = $meta->value;
						$display_value = '';
						$price_meta_key = $price_meta_key_prefix.$meta->key;
						
						if($type === 'file'){
							$value = EPO_Utils::get_file_display_name_order($value, apply_filters('epo_order_display_filename_as_link', true, $meta->key));
							$display_value = $value;
						}else{
							$display_value = $display_option_text ? EPO_Utils::get_option_display_value($meta->key, $value, null) : $value;
							//$display_value = $display_option_text ? EPO_Utils::get_option_display_value($meta->key, $meta->value, null) : $meta->value;
						}

						$is_show_price = apply_filters('epo_show_price_for_order_formatted_meta', true, $meta->key);
						if($is_show_price){
							$price_html = $order_item->get_meta($price_meta_key);
							if($price_html){
								$display_value .= ' '.$price_html;
							}
						}

						$formatted_meta[$key] = (object) array(
							'key'           => $meta->key,
							'value'         => $value,
							//'display_key'   => apply_filters( 'woocommerce_order_item_display_meta_key', $name_title_map[$meta->key] ),
							'display_key'   => apply_filters( 'woocommerce_order_item_display_meta_key', $display_key, $meta, $order_item ),
							'display_value' => wpautop( make_clickable( apply_filters( 'woocommerce_order_item_display_meta_value', $display_value, $meta, $order_item ) ) ),
						);
					}else{
						if(EPO_Utils::startsWith($meta->key, $price_meta_key_prefix)){
							unset($formatted_meta[$key]);
						}
					}
				}
			}
		}
		return $formatted_meta;
	}

	public function get_order_item_price_meta_key_prefix(){
		return apply_filters('epo_add_order_item_price_meta_key_prefix', '_epoprice_');
	}
	
	public function action_recalculate_total(){
		WC()->cart->calculate_totals();
	}
	
	public function wp_ajax_action_calculate_extra_cost_handler() {
		$return = array(
			'code' => 'E001',
			'message' => ''
		);
		$request_data_json = isset($_POST['price_info']) ? stripslashes($_POST['price_info']) : '';
		
		if($request_data_json) {
			try{
				$valid_data = true;
				$request_data = json_decode($request_data_json, true);
				$product_id = isset($request_data['product_id']) ? $request_data['product_id'] : false;
				$result = EPO_Utils_Price::calculate_total_extra_cost($request_data);
				
				$is_variable_product = isset($request_data['is_variable_product']) ? $request_data['is_variable_product'] : false;
				$variation_id = isset($request_data['variation_id']) ? $request_data['variation_id'] : false;
				if($is_variable_product && !$variation_id){
					$valid_data = false;
				}
								
				if($result && $valid_data){
					$return = array(
						'code' => 'E000',
						'message' => '',
						'result' => $result
					);
				}else{
					$price_html = EPO_Utils_Price::get_product_price($request_data, true);
					$price_html = apply_filters('epo_product_price_html', $price_html, $product_id);
					if($price_html){
						$return = array(
							'code' => 'E002',
							'message' => '',
							'result' => $price_html
						);
					}else{
						$return = array(
							'code' => 'E003',
							'message' => ''
						);
					}
				}
			} catch (Exception $e) {
				$return = array(
					'code' => 'E004',
					'message' => $e->getMessage()
				);
			}
		}
		wp_send_json($return);
	}
	
	/***********************************************
	 **** DISPLAY CUSTOM PRODUCT FIELDS - START ****
	 ***********************************************/	
	public function render_disabled_field_names_hidden_field(){
		global $product;
		$prod_field_names = EPO_Utils_Section::get_product_fields($product, true);
		$prod_field_names = is_array($prod_field_names) ? implode(",", $prod_field_names) : '';
		
		echo '<input type="hidden" id="epo_product_fields" name="epo_product_fields" value="'.$prod_field_names.'"/>';
		echo '<input type="hidden" id="epo_disabled_fields" name="epo_disabled_fields" value=""/>';
		echo '<input type="hidden" id="epo_disabled_sections" name="epo_disabled_sections" value=""/>';
	}
	
	private function render_sections($hook_name){
		global $product;
		$product_type = false;
		if($product){
			if(EPO_Utils::woo_version_check()){
				$product_type = $product->get_type();
			}else{
				$product_type = $product->product_type;
			}
		}
		
		$sections = EPO_Utils::get_sections_by_hook($this->sections_extra, $hook_name);
		if($sections){						
			foreach($sections as $section_name => $section){
				$section_html = EPO_Utils_Section::prepare_section_html($section, $product_type);
				echo $section_html;
			}
		}
	}
	
	private function prepare_extra_cart_item_data(){
		$extra_data = array();
		$extra_options = $this->prepare_product_options(false);
		
		if($extra_options){
			foreach($extra_options as $name => $field){
				$type = $field->get_property('type');
				$posted_value = false;
				
				if($type === 'file'){
					if(isset($_FILES[$name])){
						$file = $_FILES[$name];

						if(!$field->get_property('required') && !EPO_Utils::is_valid_file($file)){
							continue;
						}

						$uploaded = $this->upload_file($file, $name, $field);
						
						if($uploaded && !isset($uploaded['error'])){
							$upload_info = array();
							$upload_info['name'] = $file['name'];
							$upload_info['url'] = $uploaded['url'];
							
							$posted_value = $upload_info;
							//$posted_value = $uploaded['url'] . '/' . $file['name']; 
						}else{
							$title = EPO_i18n::__t($field->get_property('title'));
							$this->wepo_add_error('<strong>'.$title.'</strong>: '. $uploaded['error']);
							continue;
						}
					}
				}else{
					$posted_value = $this->get_posted_value($name, $field->get_property('type'));
				}
				
				if($posted_value) {
					$price_type = $field->get_property('price_type');
					$price_unit = $field->get_property('price_unit');
					$quantity   = false;
					
					if($price_type && ($price_type === 'dynamic' || $price_type === 'dynamic-excl-base-price')){
						if($price_unit && !is_numeric($price_unit)){
							$qty_field = isset($extra_options['price_unit']) ? $extra_options['price_unit'] : false;
							$quantity = $qty_field && $this->get_posted_value($qty_field->get_property('name'), $qty_field->get_property('type'));
							$price_unit = 1;
						}
					}else{
						$price_unit = 0;
					}
				
					$data_arr = array();
					$data_arr['field_type']  	= $field->get_property('type');
					$data_arr['name']  			= $name;
					$data_arr['label'] 		 	= EPO_Utils_Field::get_display_label($field);
					$data_arr['value'] 		 	= $posted_value;
					$data_arr['price']       	= $field->get_property('price');
					$data_arr['price_type']  	= $price_type;
					$data_arr['price_unit']  	= $price_unit;
					$data_arr['price_min_unit'] = $field->get_property('price_min_unit');
					$data_arr['quantity'] 		= $quantity;
					$data_arr['price_field'] 	= $field->get_property('price_field');
					$data_arr['options']        = $field->get_property('options');
					
					$extra_data[$name] = $data_arr;
				}
			}
		}
		$extra_data = apply_filters('epo_extra_cart_item_data', $extra_data);
		return $extra_data;
	}
	
	/*private function remove_disabled_fields($extra_options){
		$disabled_fields = isset( $_POST['epo_disabled_fields'] ) ? wc_clean( $_POST['epo_disabled_fields'] ) : '';
		
		if(is_array($extra_options) && $disabled_fields){
			$dis_fields = explode(",", $disabled_fields);
			
			if(is_array($dis_fields) && !empty($dis_fields)){
				foreach($extra_options as $fname => $field) {
					if(in_array($fname, $dis_fields)){
						unset($extra_options[$fname]);
					}
				}
			}
		}
		return $extra_options;
	}*/
	
	private function prepare_product_options($names_only = true){
		$final_fields = array();
		$product_fields  = isset( $_POST['epo_product_fields'] ) ? wc_clean( $_POST['epo_product_fields'] ) : '';
		$disabled_fields = isset( $_POST['epo_disabled_fields'] ) ? wc_clean( $_POST['epo_disabled_fields'] ) : '';
		$disabled_sections = isset( $_POST['epo_disabled_sections'] ) ? wc_clean( $_POST['epo_disabled_sections'] ) : '';
		
		$prod_fields = $product_fields ? explode(",", $product_fields) : array();
		$dis_sections  = $disabled_sections ? explode(",", $disabled_sections) : array();
		$dis_fields  = $disabled_fields ? explode(",", $disabled_fields) : array();
		
		if(is_array($dis_sections)){
			$sections = EPO_Utils::get_custom_sections();
			if($sections && is_array($sections)){
				foreach($dis_sections as $sname) {
					$section = isset($sections[$sname]) ? $sections[$sname] : false;
					if(EPO_Utils_Section::is_valid_section($section)){
						$sfields = EPO_Utils_Section::get_fields($section);
						foreach($sfields as $name => $field) {
							if(($key = array_search($name, $prod_fields)) !== false){
								unset($prod_fields[$key]);
							}
							/*if(isset($prod_fields[$name])){
								unset($prod_fields[$name]);
							}*/
						}
					}
				}
			}
		}
		
		$result = array_diff($prod_fields, $dis_fields);
		if($names_only){
			$final_fields = $result;
		}else{
			$extra_options = EPO_Utils::get_custom_fields_full();
			foreach($result as $name) {
				if(isset($extra_options[$name])){
					$final_fields[$name] = $extra_options[$name];
				}
			}
		}
		
		return $final_fields;
	}
	
	private function validate_file($passed, $field, $file){
		if($field->get_property('required') && !$file) {
			$this->wepo_add_error( sprintf(EPO_i18n::__t('Please select a file for %s'), $field->get_property('title')) );
			$passed = false;
		}
		$title = EPO_Utils_Field::get_display_label($field);
		
		if($file){
			$file_type = EPO_Utils::get_posted_file_type($file);
			$file_size = isset($file['size']) ? $file['size'] : false;
			
			if($file_type && $file_size){
				$name = $field->get_property('name');
				$maxsize = apply_filters('epo_file_upload_maxsize', $field->get_property('maxsize'), $name);
				$maxsize_bytes = is_numeric($maxsize) ? $maxsize*1048576 : false;
				$accept = apply_filters('epo_file_upload_accepted_file_types', $field->get_property('accept'), $name);
				$accept = $accept && !is_array($accept) ? array_map('trim', explode(",", $accept)) : $accept;
				
				if(is_array($accept) && !empty($accept) && !in_array($file_type, $accept)){
					$this->wepo_add_error('<strong>'.$title.'</strong> '. sprintf(EPO_i18n::__t('Invalid file type.')));
					$passed = false;
					
				}else if($maxsize_bytes && is_numeric($maxsize_bytes) && $file_size >= $maxsize_bytes){
					$this->wepo_add_error('<strong>'.$title.'</strong> '. sprintf(EPO_i18n::__t('File too large. File must be less than %s megabytes.'), $maxsize));
					$passed = false;
				}
			}else if($field->get_property('required')) {
				$this->wepo_add_error('<strong>'.$title.'</strong> '. sprintf(EPO_i18n::__t('Please choose a file to upload')) );
				$passed = false;
			}
		}else if($field->get_property('required')) {
			$this->wepo_add_error('<strong>'.$title.'</strong> '. sprintf(EPO_i18n::__t('Please choose a file to upload')) );
			$passed = false;
		}
		
		return $passed;
	}
	
	private function validate_field($passed, $field, $posted_value){
		$name  = $field->get_property('name');
		$type  = $field->get_property('type');
		$value = $this->get_posted_value($name, $type);
		
		if(is_array($value)){
			foreach($value as $key => $val){
				if(EPO_Utils::is_blank($val)){
					unset($value[$key]);
				}
			}
		}
		
		if($field->get_property('required') && empty($value)) {
			$this->wepo_add_error( sprintf(EPO_i18n::__t('Please enter a value for %s'), $field->get_property('title')) );
			$passed = false;
		}else{
			$title = EPO_i18n::__t($field->get_property('title'));
			$validators = $field->get_property('validate');
			$validators = !empty($validators) ? explode("|", $validators) : false;

			if($validators && !empty($value)){
				foreach($validators as $validator){
					switch($validator) {
						case 'number' :
							if(!is_numeric($value)){
								$this->wepo_add_error('<strong>'.$title.'</strong> '. sprintf(EPO_i18n::__t('(%s) is not a valid number.'), $value));
								$passed = false;
							}
							break;

						case 'email' :
							if(!is_email($value)){
								$this->wepo_add_error('<strong>'.$title.'</strong> '. sprintf(EPO_i18n::__t('(%s) is not a valid email address.'), $value));
								$passed = false;
							}
							break;
						default:
							$custom_validators = EPO_Utils::get_settings('custom_validators');
							$custom_validator  = is_array($custom_validators) && isset($custom_validators[$validator]) ? $custom_validators[$validator] : false;
							
							if(is_array($custom_validator)){
								$pattern = $custom_validator['pattern'];
								
								if(preg_match($pattern, $value) === 0) {
									$this->wepo_add_error(sprintf(EPO_i18n::__t($custom_validator['message']), $title));
									$passed = false;
								}
							}else{
								$con_validators = EPO_Utils::get_settings('confirm_validators');
								$cnf_validator = is_array($con_validators) && isset($con_validators[$validator]) ? $con_validators[$validator] : false;
								if(is_array($cnf_validator)){
									$cfield = $cnf_validator['pattern'];
									$cvalue = $this->get_posted_value($cfield);
									
									if($value && $cvalue && $value != $cvalue) {
										$this->wepo_add_error(sprintf(EPO_i18n::__t($cnf_validator['message']), $title));
										$passed = false;
									}
								}
							}
							break;
					}
				}
			}
		}
		return $passed;
	}

	public function filter_order_again_cart_item_data($cart_item_data, $item, $order){
		$item_meta_data = $item->get_formatted_meta_data('');

		$product = $item->get_product();
		$prod_fields = EPO_Utils_Section::get_product_fields($product, true);
		$final_fields = array();
		
		$extra_options = EPO_Utils::get_custom_fields_full();
		foreach($prod_fields as $name) {
			if(isset($extra_options[$name])){
				$final_fields[$name] = $extra_options[$name];
			}
		}
		$extra_data = array();

		foreach ($item_meta_data as $meta_field) {
			$key = $meta_field->key;

			if(isset($extra_options[$key])){
				$field = $final_fields[$key];

				$posted_value = $meta_field->value;

				if($posted_value) {
					$price_type = $field->get_property('price_type');
					$price_unit = $field->get_property('price_unit');
					$quantity   = false;
					
					if($price_type && ($price_type === 'dynamic' || $price_type === 'dynamic-excl-base-price')){
						if($price_unit && !is_numeric($price_unit)){
							$qty_field = isset($extra_options['price_unit']) ? $extra_options['price_unit'] : false;
							$quantity = $qty_field && $this->get_posted_value($qty_field->get_property('name'), $qty_field->get_property('type'));
							$price_unit = 1;
						}
					}else{
						$price_unit = 0;
					}
				
					$data_arr = array();
					$data_arr['field_type']  	= $field->get_property('type');
					$data_arr['name']  			= $name;
					$data_arr['label'] 			= EPO_Utils_Field::get_display_label($field);
					$data_arr['value'] 			= $posted_value;
					$data_arr['price']       	= $field->get_property('price');
					$data_arr['price_type']  	= $price_type;
					$data_arr['price_unit']  	= $price_unit;
					$data_arr['price_min_unit'] = $field->get_property('price_min_unit');
					$data_arr['quantity'] 		= $quantity;
					$data_arr['price_field'] 	= $field->get_property('price_field');
					$data_arr['options']        = $field->get_property('options');
					
					$extra_data[$key] = $data_arr;
				}
			}
		}

		$cart_item_data['epo_options'] = $extra_data;
		return $cart_item_data;
	}
	
	public function get_posted_value($name, $type = false){
		$is_posted = isset($_POST[$name]) || isset($_REQUEST[$name]) ? true : false;
		$value = false;
		
		if($is_posted){
			$value = isset($_POST[$name]) && $_POST[$name] ? $_POST[$name] : false;
			$value = empty($value) && isset($_REQUEST[$name]) ? $_REQUEST[$name] : $value;
		}
		return $value;
	}
	
	public function wepo_add_error($msg){
		if(EPO_Utils::woo_version_check('2.3.0')){
			wc_add_notice($msg, 'error');
		} else {
			WC()->add_error($msg);
		}
	}
}

endif;