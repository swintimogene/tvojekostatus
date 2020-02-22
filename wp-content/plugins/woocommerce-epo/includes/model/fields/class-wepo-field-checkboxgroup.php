<?php
/**
 * Custom product field Checkbox Group data object.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/model/fields
 */
if(!defined('WPINC')){	die; }

if(!class_exists('WEPO_Product_Field_CheckboxGroup')):

class WEPO_Product_Field_CheckboxGroup extends WEPO_Product_Field{
	public $options = array();
	
	public function __construct() {
		$this->type = 'checkboxgroup';
	}	
			
	/*public function get_html(){
		$html = '';
		if($this->enabled){
			$value = apply_filters( 'epo_product_extra_option_value_'.$this->name, $this->value );
			$value = isset($_POST[$this->name]) ? $_POST[$this->name] : $value;
			$input_class = $this->price_field ? 'epo-price-field' : '';
			
			if(!is_array($value) && !empty($value)){
				$value_arr = explode(',', $value);
				$value = array_map('trim', $value_arr);
			}
			
			$input_html = '';
			foreach($this->options as $option_key => $option){ 	
				$option_text = $this->esc_html__wepo($option['text']);
					
				if(is_array($value)){
					$checked = in_array($option_key, $value) ? 'checked' : '';
				}else{
					$checked = ($option_key === $value) ? 'checked' : '';
				}
				
				$price_html = $this->get_display_price_option($option);
				$price_data = $this->get_price_data_option($option);
				
				if(!empty($option_key) && !empty($option_text)){
					$option_text .= !empty($price_html) ? ' (+'.$price_html.')' : '';
				}
									
				$input_html .= '<label for="'.esc_attr($this->name).'_'.esc_attr($option_key).'" style="display:inline; margin-right: 10px;" ';
				$input_html .= 'class="label-tag checkbox '.$this->title_class_str.'">';  
				$input_html .= '<input type="checkbox" id="'.esc_attr($this->name).'_'.esc_attr($option_key).'" name="'. esc_attr( $this->name ) .'[]" '; 
				$input_html .= $price_data.' value="'. $option_key .'" '. $checked .' class="epo-input-field input-checkbox '.$input_class.'"/> '. $option_text .'</label>';
				
				if(!empty($this->cssclass)){
					$class_arr = explode(',', $this->cssclass);
					$class_arr = array_map('trim', $class_arr);
					if(in_array("valign", $class_arr)){
						$input_html .= '<br/>';
					}
				}
			}
			
			$html = $this->prepare_field_html($input_html, false);
		}
		return $html;
	}
	
	public function get_display_price_option($option){
		$is_price_field = is_numeric($option['price']);
		$price_type = $option['price_type'];
		$price = $option['price'];
		
		return $this->get_price_html($is_price_field, $price_type, $price);
	}
	
	public function get_price_final($product_price){
		$fprice = 0;
		$options = $this->options;
		
		if(is_array($options) && is_array($this->value)){
			foreach($this->value as $option_value){
				if(isset($options[$option_value])){
					$selected_option = $options[$option_value];
					
					if(isset($selected_option['price'])){
						$price = $selected_option['price'];
						
						if(isset($selected_option['price_type']) && $selected_option['price_type'] === 'percentage'){
							if(is_numeric($price) && is_numeric($product_price)){
								$fprice = $fprice + ($price/100)*$product_price;
							}	
						}else{
							if(is_numeric($price)){
								$fprice = $fprice + $price;
							}
						}
					}
				}
			}
		}
		return $fprice;
	}*/
}

endif;