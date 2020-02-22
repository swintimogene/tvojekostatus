<?php
/**
 * Custom product field Input Text data object.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/model/fields
 */
if(!defined('WPINC')){	die; }

if(!class_exists('WEPO_Product_Field_InputText')):

class WEPO_Product_Field_InputText extends WEPO_Product_Field{
	public function __construct() {
		$this->type = 'inputtext';
	}	
		
	/*public function get_html(){
		$price_data = $this->get_price_data();
		$input_class = $this->price_field ? 'epo-price-field' : '';
		$value = apply_filters( 'epo_product_extra_option_value_'.$this->name, $this->value );
		$value = isset($_POST[$this->name]) ? $_POST[$this->name] : $value;
		
		$field_props  = 'placeholder="'. $this->esc_html__wepo($this->placeholder) .'"';
		$field_props .= ' value="'.$value.'"';
		$field_props .= ' class="epo-input-field '.$input_class.'"';
		$field_props .= $price_data;
		
		if($this->maxlength && is_numeric($this->maxlength)){
			$field_props .= ' maxlength="'.absint( $this->maxlength ).'"';
		}
		
		$input_html  = '<input type="text" id="'.$this->name.'" name="'.$this->name.'" '.$field_props.' />';
		$input_html .= $this->get_char_counter_html();
		
		$html = $this->prepare_field_html($input_html);
		return $html;
	}*/
}

endif;