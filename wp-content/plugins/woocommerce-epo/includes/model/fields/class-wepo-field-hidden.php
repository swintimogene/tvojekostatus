<?php
/**
 * Custom product field Hidden data object.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/model/fields
 */
if(!defined('WPINC')){	die; }

if(!class_exists('WEPO_Product_Field_Hidden')):

class WEPO_Product_Field_Hidden extends WEPO_Product_Field{
	public function __construct() {
		$this->type = 'hidden';
	}	
		
	/*public function get_html(){
		$price_data = $this->get_price_data();
		$input_class = $this->price_field ? 'epo-price-field' : '';
		$value = apply_filters( 'epo_product_extra_option_value_'.$this->name, $this->value );
		$value = isset($_POST[$this->name]) ? $_POST[$this->name] : $value;
		
		$field_props  = 'value="'.$value.'" class="epo-input-field '.$input_class.'"';
		$field_props .= $price_data;
				
		$input_html  = '<input type="hidden" id="'.$this->name.'" name="'.$this->name.'" '.$field_props.' />';
		
		return $input_html;
	}*/
}

endif;