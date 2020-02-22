<?php
/**
 * Custom product field Select data object.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/model/fields
 */
if(!defined('WPINC')){	die; }

if(!class_exists('WEPO_Product_Field_Select')):

class WEPO_Product_Field_Select extends WEPO_Product_Field{
	public function __construct() {
		$this->type = 'select';
	}	
	
	/*public function get_html(){
		$value = apply_filters( 'epo_product_extra_option_value_'.$this->name, $this->value );
		$value = isset($_POST[$this->name]) ? $_POST[$this->name] : $value;
		
		$options_html = '';
		foreach($this->options as $option_key => $option){		
			$selected 	= ($option_key === $value) ? 'selected' : '';
			$price_html = $this->get_display_price_option($option);
			$price_data = $this->get_price_data_option($option);
			
			$option_text  = $this->esc_html__wepo($option['text']);
			if(!empty($option_key) && !empty($option_text)){
				$option_text .= !empty($price_html) ? ' (+'.$price_html.')' : '';
			}
			//$option_text .= is_numeric($option['price']) ? ' (+'.$option['price'].$price_suffix.')' : '';
					
			$options_html .= '<option value="'.$option_key.'" '.$selected.' '.$price_data.'>'.$option_text.'</option>';
		}
		
		$input_class = $this->price_field ? 'epo-price-field epo-price-option-field' : '';
		
		$field_props  = 'value="'.$value.'"';
		$field_props .= ' class="epo-input-field '.$input_class.'"';
		
		$select_html  = '<select id="'.$this->name.'" name="'.$this->name.'" '.$field_props.'" >';
		$select_html .= $this->placeholder ? '<option value="" selected="selected">'.$this->esc_html__wepo($this->placeholder).'</option>' : '';
		$select_html .= $options_html;
		$select_html .= '</select>';
		
		$html = $this->prepare_field_html($select_html, false);
		return $html;
	}
	
	public function get_display_price_option($option){
		$display_price = '';
		if($this->price_field){
			$price_type = $option['price_type'];
			$price = $option['price'];
			
			$display_price = $this->get_price_html($this->price_field, $price_type, $price);
		}
		
		return $display_price;
	}
	
	public function get_price_final($product_price){
		$fprice = 0;
		$options = $this->options;
		if(is_array($options) && isset($options[$this->value])){
			$selected_option = $options[$this->value];
			if(isset($selected_option['price'])){
				$fprice = $selected_option['price'];
				
				if(isset($selected_option['price_type']) && $selected_option['price_type'] === 'percentage'){
					if(is_numeric($fprice) && is_numeric($product_price)){
						$fprice = ($fprice/100)*$product_price;
					}	
				}
			}
		}
		return $fprice;
	}*/
}

endif;