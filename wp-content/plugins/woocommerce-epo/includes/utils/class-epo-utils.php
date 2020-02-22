<?php
/**
 * The common utility functionalities for the plugin.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/utils
 */
if(!defined('WPINC')){	die; }

if(!class_exists('EPO_Utils')):

class EPO_Utils {
	const OPTION_KEY_CUSTOM_SECTIONS   = 'epo_custom_sections';
	const OPTION_KEY_SECTION_HOOK_MAP  = 'epo_section_hook_map';
	const OPTION_KEY_NAME_TITLE_MAP    = 'epo_options_name_title_map';
	const OPTION_KEY_ADVANCED_SETTINGS = 'epo_advanced_settings';
	
	static $PATTERN = array(			
			'/d/', '/j/', '/l/', '/z/', '/S/', //day (day of the month, 3 letter name of the day, full name of the day, day of the year, )			
			'/F/', '/M/', '/n/', '/m/', //month (Month name full, Month name short, numeric month no leading zeros, numeric month leading zeros)			
			'/Y/', '/y/' //year (full numeric year, numeric year: 2 digit)
		);
		
	static $REPLACE = array(
			'dd','d','DD','o','',
			'MM','M','m','mm',
			'yy','y'
		);
	
	public static function get_advanced_settings(){
		$settings = get_option(EPO_Utils::OPTION_KEY_ADVANCED_SETTINGS);
		$settings = apply_filters('epo_advanced_settings', $settings);
		return empty($settings) ? false : $settings;
	}
	
	public static function get_setting_value($settings, $key){
		if(is_array($settings) && isset($settings[$key])){
			return $settings[$key];
		}
		return '';
	}
	
	public static function get_settings($key){
		$settings = self::get_advanced_settings();
		if(is_array($settings) && isset($settings[$key])){
			return $settings[$key];
		}
		return '';
	}
	
	public static function get_section_hook_map(){
		$section_hook_map = get_option(self::OPTION_KEY_SECTION_HOOK_MAP);	
		$section_hook_map = is_array($section_hook_map) ? $section_hook_map : array();
		return $section_hook_map;
	}
	
	public static function get_custom_sections(){
		$sections = get_option(self::OPTION_KEY_CUSTOM_SECTIONS);
		return empty($sections) ? false : $sections;
	}
		
	public static function has_extra_options($product){
		$options_extra = EPO_Utils_Section::get_product_sections_and_fields($product);
		return empty($options_extra) ? false : true;		
	}
	
	public static function get_sections_by_hook($section_hook_map, $hook_name){
		if(is_array($section_hook_map) && array_key_exists($hook_name, $section_hook_map)) {
			$hooked_sections = $section_hook_map[$hook_name];
			return (is_array($hooked_sections) && !empty($hooked_sections)) ? $hooked_sections : false;
		}
		return false;
	}
	
	public static function get_custom_fields_full(){
		$fields_full = array();
		$sections = self::get_custom_sections();
		if($sections && is_array($sections)){
			foreach($sections as $section_name => $section){
				$fields = EPO_Utils_Section::get_fields($section);
				if($fields){
					$fields_full = array_merge($fields_full, $fields);
				}
			}
		}
		return empty($fields_full) ? false : $fields_full;
	}
	
	public static function get_options_name_title_map(){
		$name_title_map = array();
		$sections = self::get_custom_sections();
		if($sections && is_array($sections)){
			foreach($sections as $section_name => $section){
				$fields = EPO_Utils_Section::get_fields($section);
				if($fields){
					foreach($fields as $name => $field){
						$name_title_map[$name] = EPO_Utils_Field::get_display_label($field);
					}
				}
			}
		}
		return empty($name_title_map) ? false : $name_title_map;
	}
	
	public static function get_option_display_value($name, $value, $data){
		$type = false;
		$options = false;
		
		if(is_array($data)){
			$type =  isset($data['field_type']) ? $data['field_type'] : '';
			if(EPO_Utils_Field::is_option_field($type)){
				$options = isset($data['options']) ? $data['options'] : false;
			}
		}else{
			$fields_all = self::get_custom_fields_full();
			if(is_array($fields_all) && isset($fields_all[$name])){
				$field = $fields_all[$name];
				if(EPO_Utils_Field::is_valid_field($field)){
					$type = $field->get_property('type');
					if(EPO_Utils_Field::is_option_field($type)){
						$options = $field->get_property('options');
					}
				}
			}
		}
		
		if($value && is_array($options)){
			$value_arr = array_map('trim', explode(',', $value));
			$value = '';

			foreach($value_arr as $val){
				if(isset($options[$val])){
					$option = $options[$val];
					if(is_array($option) && isset($option['text'])){
						$value .= $value ? ', ' : '';
						$value .= EPO_i18n::__t($option['text']);
					}
				}
			}
		}

		/*if($value && is_array($options) && isset($options[$value])){
			$option = $options[$value];
			if(is_array($option) && isset($option['text'])){
				$value = EPO_i18n::__t($option['text']);
			}
		}*/
		
		return $value;
	}
		
	//TODO check for any better approach.
	public static function get_product_categories($product){
		$ignore_translation = apply_filters('epo_ignore_wpml_translation_for_product_category', false);
		$is_wpml_active = function_exists('icl_object_id');
		
		$product_id = false;
		if(self::woo_version_check()){
			$product_id = $product->get_id();
		}else{
			$product_id = $product->id;
		}
		
		$categories = array();
		if($product_id){
			$product_cat = wp_get_post_terms($product_id, 'product_cat');
			if(is_array($product_cat)){
				foreach($product_cat as $category){
					$parent_cat = get_ancestors( $category->term_id, 'product_cat' ); 
					if(is_array($parent_cat)){
						foreach($parent_cat as $pcat_id){
							$pcat = get_term( $pcat_id, 'product_cat' );
							$pcat_slug = $pcat->slug;
							$pcat_slug = self::check_for_wpml_traslation($pcat_slug, $pcat, $is_wpml_active, $ignore_translation);
							$categories[] = $pcat_slug;
						}
					}
					$cat_slug = $category->slug;
					$cat_slug = self::check_for_wpml_traslation($cat_slug, $category, $is_wpml_active, $ignore_translation);
					$categories[] = $cat_slug;
				}
			}
		}
		return $categories;
	}

	public static function is_valid_file($file){
		$valid = false;
		if(is_array($file) && isset($file['name']) && !empty($file['name'])){
			$valid = true;
		}
		return $valid;
	}
	
	public static function get_file_upload_path(){
		$upload_dir = wp_upload_dir();
		$baseurl = isset($upload_dir['baseurl']) ? $upload_dir['baseurl'] : false;
		$path = $baseurl ? $baseurl.'/extra_product_options/' : false;
		return apply_filters('epo_file_upload_path', $path);
	}
	
	public static function upload_dir($upload_dir){
		$subdir = '';
		if(apply_filters('epo_uploads_use_unique_folders', true)){
			global $woocommerce;
			$subdir = '/' . md5($woocommerce->session->get_customer_id());
			
		}else if(apply_filters('epo_uploads_use_yearmonth_folders', false)){
			$time = current_time('mysql');
			$y = substr( $time, 0, 4 );
			$m = substr( $time, 5, 2 );
			$subdir = "/$y/$m";
		}
	 	
		$upload_path = rtrim(apply_filters('epo_upload_path', '/epo_uploads/'), '/');
		$subdir = $upload_path . $subdir;
		
		if(empty($upload_dir['subdir'])){
			$upload_dir['path'] = $upload_dir['path'] . $subdir;
			$upload_dir['url'] = $upload_dir['url'] . $subdir;
		} else {
			$upload_dir['path'] = str_replace( $upload_dir['subdir'], $subdir, $upload_dir['path'] );
			$upload_dir['url'] = str_replace( $upload_dir['subdir'], $subdir, $upload_dir['url'] );
		}
		$upload_dir['subdir'] = $subdir;
	 	
		return $upload_dir;
	}
	
	public static function upload_mimes(){
	
	}
	
	public static function get_posted_file_type($file){
		$file_type = false;
		if($file && isset($file['name'])){
			//$file_type = isset($file['type']) ? $file['type'] : false;
			$file_type = pathinfo($file['name'], PATHINFO_EXTENSION);
			//var_dump($file_type);
		}
		return $file_type;
	}
	
	public static function get_file_display_name($upload_info, $downloadable=true){
		$dname = '';
		if(is_array($upload_info)){
			$dname = isset($upload_info['name']) ? $upload_info['name'] : '';
			$url = isset($upload_info['url']) ? $upload_info['url'] : '';
			$price_info = isset($upload_info['price_info']) ? $upload_info['price_info'] : '';
			
			if($dname && $downloadable && $url){
				$dname  = '<a href="'.$url.'" target="_blank">'.$dname.'</a>';
				$dname .= $price_info ? $price_info : '';
			}
		}else{
			$dname = $upload_info ? $upload_info : '';
		}
		return $dname;
	}
	
	public static function get_file_display_name_order($upload_info_json, $downloadable=true){
		$dname = '';
		if($upload_info_json){
			$upload_info = json_decode($upload_info_json, true);
			if(!$upload_info){
				$last_index = strrpos( $upload_info_json, '}');
				$_upload_info_json = substr($upload_info_json, 0, $last_index+1);
				$upload_info = json_decode($_upload_info_json, true);
				$upload_info['price_info'] = substr($upload_info_json, $last_index+1);
			}
			$dname = self::get_file_display_name($upload_info, $downloadable);
		}
		return $dname;
	}
	
	public static function get_filename_from_path($path){
		if($path){
			$parts = explode('/', $path);
    		return array_pop($parts);
		}
		return $path;
	}
	
	public static function check_for_wpml_traslation($cat_slug, $cat, $is_wpml_active, $ignore_translation){
		if($is_wpml_active && $ignore_translation){
			global $sitepress;
			global $icl_adjust_id_url_filter_off;
			
			$orig_flag_value = $icl_adjust_id_url_filter_off;
			$icl_adjust_id_url_filter_off = true;
			$default_lang = $sitepress->get_default_language();
		
			$ocat_id = icl_object_id($cat->term_id, 'product_cat', true, $default_lang);
			$ocat = get_term($ocat_id, 'product_cat');
			$cat_slug = $ocat->slug;
		
			$icl_adjust_id_url_filter_off = $orig_flag_value;
		}
		return $cat_slug;
	}
	
	public static function get_user_roles($user = false) {
		$user = $user ? new WP_User( $user ) : wp_get_current_user();
		
		if(!($user instanceof WP_User))
		   return false;
		   
		$roles = $user->roles;
		return $roles;
	}
	
	public static function get_locale_code(){
		$locale_code = '';
		$locale = get_locale();
		if(!empty($locale)){
			$locale_arr = explode("_", $locale);
			if(!empty($locale_arr) && is_array($locale_arr)){
				$locale_code = $locale_arr[0];
			}
		}		
		return empty($locale_code) ? 'en' : $locale_code;
	}
	
	public static function get_jquery_date_format($woo_date_format){				
		$woo_date_format = !empty($woo_date_format) ? $woo_date_format : wc_date_format();
		return preg_replace(self::$PATTERN, self::$REPLACE, $woo_date_format);	
	}
	
	public static function convert_cssclass_string($cssclass){
		if(!is_array($cssclass)){
			$cssclass = array_map('trim', explode(',', $cssclass));
		}
		
		if(is_array($cssclass)){
			$cssclass = implode(" ",$cssclass);
		}
		return $cssclass;
	}
	
	public static function convert_cssclass_array($cssclass){
		if(!is_array($cssclass)){
			$cssclass = array_map('trim', explode(',', $cssclass));
		}
		return $cssclass;
	}
	
	public static function is_subset_of($arr1, $arr2){
		if(is_array($arr1) && is_array($arr2)){
			foreach($arr2 as $value){
				if(!in_array($value, $arr1)){
					return false;
				}
			}
		}
		return true;
	}

	public static function remove_by_value($value, $arr){
		if(is_array($arr)){
			foreach (array_keys($arr, $value, true) as $key) {
			    unset($arr[$key]);
			}
		}
		return $arr;
	}
	
	public static function is_blank($value) {
		return empty($value) && !is_numeric($value);
	}

	public static function startsWith($haystack, $needle){
     	$length = strlen($needle);
     	return (substr($haystack, 0, $length) === $needle);
	}
	
	public static function woo_version_check( $version = '3.0' ) {
	  	if(function_exists( 'is_woocommerce_active' ) && is_woocommerce_active() ) {
			global $woocommerce;
			if( version_compare( $woocommerce->version, $version, ">=" ) ) {
		  		return true;
			}
	  	}
	  	return false;
	}
	
	public static function is_woo_dynamic_pricing_plugin_active(){
		$active = is_plugin_active('woocommerce-dynamic-pricing/woocommerce-dynamic-pricing.php');
		return apply_filters('epo_woo_dynamic_pricing_plugin_enabled', $active);
	}
	
	public static function is_rightpress_dynamic_pricing_plugin_active(){
		$active = is_plugin_active('wc-dynamic-pricing-and-discounts/wc-dynamic-pricing-and-discounts.php');
		return apply_filters('epo_rightpress_dynamic_pricing_plugin_enabled', $active);
	}
	
	public static function is_quick_view_plugin_active(){
		$quick_view = false;
		if(self::is_flatsome_quick_view_enabled()){
			$quick_view = 'flatsome';
		}else if(self::is_yith_quick_view_enabled()){
			$quick_view = 'yith';
		}else if(self::is_astra_quick_view_enabled()){
			$quick_view = 'astra';
		}
		return apply_filters('epo_is_quick_view_plugin_active', $quick_view);
	}
	
	public static function is_yith_quick_view_enabled(){
		return is_plugin_active('yith-woocommerce-quick-view/init.php');
	}
	
	public static function is_flatsome_quick_view_enabled(){
		return (get_option('template') === 'flatsome');
	}

	public static function is_astra_quick_view_enabled(){
		return is_plugin_active('astra-addon/astra-addon.php');
	}
}

endif;