<?php
/**
 * The admin settings page common utility functionalities.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('EPO_Admin_Utils')):

class EPO_Admin_Utils {
	public static function get_sections(){				
		$sections = EPO_Utils::get_custom_sections();
		
		if($sections && is_array($sections) && !empty($sections)){
			return $sections;
		}else{
			$section = EPO_Utils_Section::prepare_default_section();
			
			$sections = array();
			$sections[$section->get_property('name')] = $section;
			return $sections;
		}		
	}
	
	public static function get_section($section_name){
	 	if($section_name){	
			$sections = self::get_sections();
			if(is_array($sections) && isset($sections[$section_name])){
				$section = $sections[$section_name];	
				if(EPO_Utils_Section::is_valid_section($section)){
					return $section;
				} 
			}
		}
		return false;
	}
	
	public static function load_products(){
		$args = array( 'post_type' => 'product', 'order' => 'ASC', 'posts_per_page' => -1, 'fields' => 'ids' );
		$products = get_posts( $args );
		$productsList = array();
		
		if(count($products) > 0){
			foreach($products as $pid){				
				//$productsList[] = array("id" => $product->ID, "title" => $product->post_title);
				$productsList[] = array("id" => $pid, "title" => get_the_title($pid));
			}
		}		
		return $productsList;
	}
	
	public static function load_products_cat(){
		$ignore_translation = apply_filters('epo_ignore_wpml_translation_for_product_category', false);
		$is_wpml_active = function_exists('icl_object_id');
		
		$product_cat = array();
		$pcat_terms = get_terms('product_cat', 'orderby=count&hide_empty=0');
		
		foreach($pcat_terms as $pterm){
			$pcat_slug = $pterm->slug;
			$pcat_slug = EPO_Utils::check_for_wpml_traslation($pcat_slug, $pterm, $is_wpml_active, $ignore_translation);
							
			$product_cat[] = array("id" => $pcat_slug, "title" => $pterm->name);
		}		
		return $product_cat;
	}
	
	public static function load_user_roles(){
		$user_roles = array();
		
		global $wp_roles;
    	$roles = $wp_roles->roles;
		//$roles = get_editable_roles();
		foreach($roles as $key => $role){
			$user_roles[] = array("id" => $key, "title" => $role['name']);
		}		
		
		return $user_roles;
	}
	
	public static function sort_sections_by_order($a, $b){
		if(is_array($a) && is_array($b)){
			$order_a = isset($a['order']) && is_numeric($a['order']) ? $a['order'] : 0;
			$order_b = isset($b['order']) && is_numeric($b['order']) ? $b['order'] : 0;
			
			if($order_a == $order_b){
				return 0;
			}
			return ($order_a < $order_b) ? -1 : 1;
		}else if(EPO_Utils_Section::is_valid_section($a) && EPO_Utils_Section::is_valid_section($b)){
			$order_a = is_numeric($a->get_property('order')) ? $a->get_property('order') : 0;
			$order_b = is_numeric($b->get_property('order')) ? $b->get_property('order') : 0;
			
			if($order_a == $order_b){
				return 0;
			}
			return ($order_a < $order_b) ? -1 : 1;
		}else{
			return 0;
		}
	}
	
	public static function stable_uasort(&$array, $cmp_function) {
		if(count($array) < 2) {
			return;
		}
		
		$halfway = count($array) / 2;
		$array1 = array_slice($array, 0, $halfway, TRUE);
		$array2 = array_slice($array, $halfway, NULL, TRUE);
	
		self::stable_uasort($array1, $cmp_function);
		self::stable_uasort($array2, $cmp_function);
		if(call_user_func_array($cmp_function, array(end($array1), reset($array2))) < 1) {
			$array = $array1 + $array2;
			return;
		}
		
		$array = array();
		reset($array1);
		reset($array2);
		while(current($array1) && current($array2)) {
			if(call_user_func_array($cmp_function, array(current($array1), current($array2))) < 1) {
				$array[key($array1)] = current($array1);
				next($array1);
			} else {
				$array[key($array2)] = current($array2);
				next($array2);
			}
		}
		while(current($array1)) {
			$array[key($array1)] = current($array1);
			next($array1);
		}
		while(current($array2)) {
			$array[key($array2)] = current($array2);
			next($array2);
		}
		return;
	}
}

endif;