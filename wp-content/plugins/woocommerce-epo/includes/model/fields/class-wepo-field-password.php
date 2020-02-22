<?php
/**
 * Custom product field Password data object.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/model/fields
 */
if(!defined('WPINC')){	die; }

if(!class_exists('WEPO_Product_Field_Password')):

class WEPO_Product_Field_Password extends WEPO_Product_Field{
	public function __construct() {
		$this->type = 'password';
	}	
		
	/*public function get_html(){
		$price_data = $this->get_price_data();
		$input_class = $this->price_field ? 'epo-price-field' : '';
		$value = apply_filters( 'epo_product_extra_option_value_'.$this->name, $this->value );
		$value = isset($_POST[$this->name]) ? $_POST[$this->name] : $value;
		
		$field_props  = 'placeholder="'. $this->__wepo($this->placeholder) .'"';
		$field_props .= ' value="'.$value.'"';
		$field_props .= ' class="epo-input-field '.$input_class.'"';
		$field_props .= $price_data;
		
		$input_html = '<input type="password" id="'.$this->name.'" name="'.$this->name.'" '.$field_props.' />';
		
		$html = $this->prepare_field_html($input_html);
		return $html;
	}*/
}

endif;