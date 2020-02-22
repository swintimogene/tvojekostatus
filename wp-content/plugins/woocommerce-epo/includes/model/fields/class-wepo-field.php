<?php
/**
 * Custom product field data object.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/model/fields
 */
if(!defined('WPINC')){	die; }

if(!class_exists('WEPO_Product_Field')):

class WEPO_Product_Field{
	public $order = '';
	public $type = '';
	public $id   = '';
	public $name = '';	
	public $name_old = '';
	
	public $minlength = '';
	public $maxlength = '';
	
	public $value = '';
	public $placeholder = '';
	public $options_json = '';
	public $options = array();
	public $validate = '';
	//public $validator = '';
	public $input_class = '';
	//public $input_class_str = '';
	public $cssclass = '';
	public $cssclass_str = '';
		
	public $title = '';
	public $title_type  = '';
	public $title_color = '';
	public $title_position = '';
	public $title_class = '';
	public $title_class_str = '';
	
	public $subtitle = '';
	public $subtitle_type  = '';
	public $subtitle_color = '';
	public $subtitle_position = '';
	public $subtitle_class = '';
	public $subtitle_class_str = '';
	
	public $price_field = false;
	public $price = 0;
	public $price_unit = 0;
	public $price_type = '';
	public $price_min_unit = 0;
	public $price_prefix = '';
	public $price_suffix = '';
	
	public $required = false;
	public $enabled  = true;
	
	public $rules_action = '';
	public $rules_action_ajax = '';
	
	public $conditional_rules_json = '';
	public $conditional_rules = array();
	
	public $conditional_rules_ajax_json = '';
	public $conditional_rules_ajax = array();
	
	public $separator_type  = '';
	public $separator_hight = '';
		
	public function __construct() {
			
	}
	
	public function set_property($name, $value){
		$this->$name = $value;
	}
	
	public function get_property($name){
		if(property_exists($this, $name)){
			return $this->$name;
		}else{
			return '';
		}
	}
}

endif;