<?php
/**
 * Custom product field Checkbox data object.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/model/fields
 */
if(!defined('WPINC')){	die; }

if(!class_exists('WEPO_Product_Field_Checkbox')):

class WEPO_Product_Field_Checkbox extends WEPO_Product_Field{
	public $checked = false;
	
	public function __construct() {
		$this->type = 'checkbox';
	}	
	
	/*public function get_html(){
		$html = '';
		if($this->enabled){
			$checked = $this->checked;
			$value   = $this->value ? $this->value : 1;
			$value 	 = apply_filters( 'epo_product_extra_option_value_'.$this->name, $value );
			$checked = isset($_POST[$this->name]) && $_POST[$this->name] == $value ? 'checked="checked"' : $checked;
			
			$price_data = $this->get_price_data();
			$input_class = $this->price_field ? 'epo-price-field' : '';
			
			$field_props  = 'value="'.$value.'"';
			$field_props .= ' class="epo-input-field input-checkbox '.$input_class.'"';
			$field_props .= ' '.$checked;
			$field_props .= $price_data;
			
			$price_html = $this->get_display_price();
			
			$cssclass = $this->cssclass_str;
			$ajax_conditions_data = $this->get_ajax_conditions_data_str();
			if($ajax_conditions_data){
				$cssclass .= empty($cssclass) ? 'epo-conditional-field' : ' epo-conditional-field';
			}
			
			if($this->title_position === 'left'){
				$html .= '<tr class="'. $cssclass .'" '. $ajax_conditions_data .'><td colspan="2">';
				$html .= '<input type="hidden" name="'. $this->name .'" value="">';
				$html .= '<label for="'. $this->name .'" class="label-tag checkbox '. $this->title_class_str .'">';
				$html .= '<input type="checkbox" id="'. $this->name .'" name="'. $this->name .'" '. $field_props .' /> ';
				$html .= $this->__wepo($this->title) . $this->get_required_html() .'</label>';
				$html .= !empty($price_html) ? ' (+'.$price_html.')' : '';
				$html .= '</td></tr>';		
			}else{
				$html .= '<tr class="'. $cssclass .'" '. $ajax_conditions_data .'><td colspan="2">';
				$html .= '<input type="hidden" name="'. $this->name .'" value="">';
				$html .= '<label for="'. $this->name .'" class="label-tag checkbox '. $this->title_class_str .'">';
				$html .= '<input type="checkbox" id="'. $this->name .'" name="'. $this->name .'" '. $field_props .' /> ';
				$html .= $this->__wepo($this->title) . $this->get_required_html() .'</label>';
				$html .= !empty($price_html) ? ' (+'.$price_html.')' : '';
				$html .= '</td></tr>';	
			}	
		}	
		return $html;
	}*/
}

endif;