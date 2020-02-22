<?php
/**
 * The extra price specific functionality for the plugin.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/utils
 */
if(!defined('WPINC')){	die; }

if(!class_exists('EPO_Utils_Price')):

class EPO_Utils_Price {
	public static function get_extra_cost_final($product_info, $data){
		$fprice = 0;
		$name = isset($data['name']) ? $data['name'] : false;
		$field_type = isset($data['field_type']) ? $data['field_type'] : false;
		
		if($name){
			if(self::is_price_field_type_option($field_type)){
				$fprice = self::get_extra_cost_option_field($data, $field_type, $product_info);
			}else{
				$price_type = $data['price_type'];
				$price = $data['price'];
				$fprice = self::calculate_extra_cost($data, $product_info, $price_type, $price);
			}
		}
		return $fprice;
	}
		
	public static function calculate_total_extra_cost($request_data){
		$result = false;
		$product_id = isset($request_data['product_id']) ? $request_data['product_id'] : false;
		$price_info_list = isset($request_data['price_info']) ? $request_data['price_info'] : false;
		
		if($product_id && $price_info_list){
			$product = $product_id ? wc_get_product( $product_id ) : false;
			$product_price = self::get_product_price($request_data);
			$fprice = 0;
			$exclude_base_price = apply_filters('epo_extra_cost_exclude_base_price', false, $product_id);

			$product_info = array();
			$product_info['id'] = $product_id;
			$product_info['price'] = $product_price;

			foreach($price_info_list as $fname => $price_info){
				$price_type = isset($price_info['price_type']) ? $price_info['price_type'] : '';
				$price 		= isset($price_info['price']) ? $price_info['price'] : 0;
				$multiple   = isset($price_info['multiple']) ? $price_info['multiple'] : 0;

				if($price_type === 'dynamic-excl-base-price'){
					$exclude_base_price = true;
				}
				$exclude_base_price = apply_filters('epo_extra_cost_exclude_base_price_single', $exclude_base_price, $product_id, $fname);
				
				if($multiple == 1){
					$price_arr = explode(",", $price);
					$price_type_arr = explode(",", $price_type);
					
					foreach($price_arr as $index => $oprice){
						$oprice_type = isset($price_type_arr[$index]) ? $price_type_arr[$index] : 'normal';
						$fprice += self::calculate_extra_cost($price_info, $product_info, $oprice_type, $oprice, $index);
					}
				}else{
					$fprice += self::calculate_extra_cost($price_info, $product_info, $price_type, $price);
				}
			}
			
			$final_price = $exclude_base_price && $fprice ? $fprice : $product_price + $fprice;
			$display_price = wc_price($final_price);
			$display_price = $display_price.self::get_price_suffix( $product );
			
			$result = array();
			$result['product_price'] = $product_price;
			$result['extra_cost'] = $fprice;
			$result['final_price'] = $final_price;
			$result['display_price'] = apply_filters('epo_product_price_html', $display_price, $product_id);
		}
		
		return $result;
	}

	public static function get_price_suffix( $product ) {
		$price_suffix = '';
		if($product && apply_filters('epo_show_product_price_suffix', true, $product)){
			$price_suffix = $product->get_price_suffix();
		}
		return $price_suffix;
	}
	
	public static function get_product_price($request_data, $is_default = false){
		$price = false;
		$product = false;
		$product_id = isset($request_data['product_id']) ? $request_data['product_id'] : false;
		$variation_id = isset($request_data['variation_id']) ? $request_data['variation_id'] : false;
		
		if($variation_id){
			$product = new WC_Product_Variation( $variation_id );
		}else if($product_id){
			$pf = new WC_Product_Factory();  
			$product = $pf->get_product($product_id);
		}
		
		if($product){
			$price = $is_default ? $product->get_price_html() : $product->get_price('');
		}

		$price = apply_filters('epo_product_price', $price, $product, $is_default);
		return $price;
	}
	
	private static function calculate_extra_cost($price_info, $product_info, $price_type, $price, $index=false){
		$fprice = 0;
		$name  = isset($price_info['name']) ? $price_info['name'] : '';
		$value = isset($price_info['value']) ? $price_info['value'] : false;
		$product_price = is_array($product_info) && isset($product_info['price']) ? $product_info['price'] : false;

		$price = apply_filters('epo_product_field_price', $price, $price_type, $name, $price_info, $index);

		if($price_type === 'percentage'){
			if(is_numeric($price) && is_numeric($product_price)){
				$fprice = ($price/100)*$product_price;
			}
		}else if($price_type === 'dynamic' || $price_type === 'dynamic-excl-base-price' || $price_type === 'char-count'){
			$price_unit = isset($price_info['price_unit']) ? $price_info['price_unit'] : false;
			$quantity = isset($price_info['quantity']) ? $price_info['quantity'] : false;

			if($price_type === 'char-count' && !empty($value)){
				$quantity = strlen($value);
			}

			$quantity = apply_filters('epo_extra_cost_quantity_'.$name, $quantity, $value); //Deprecated
			$quantity = apply_filters('epo_extra_cost_quantity', $quantity, $name, $value);
			$value = $quantity && is_numeric($quantity) ? $quantity : $value;
			
			if(is_numeric($price) && is_numeric($value) && is_numeric($price_unit) && $price_unit > 0){
				$price_min_unit = isset($price_info['price_min_unit']) && is_numeric($price_info['price_min_unit']) ? $price_info['price_min_unit'] : 0;
				$value = $value && ($value > $price_min_unit) ? $value - $price_min_unit : 0;
				
				$price = apply_filters('epo_extra_cost_unit_price_'.$name, $price, $product_price, $price_type);
				$price = apply_filters('epo_extra_cost_unit_price', $price, $name, $product_price, $price_type);
				$is_unit_type_range = apply_filters('epo_extra_cost_unit_price_type_range_'.$name, false);

				$total_units = $value/$price_unit;
				$total_units = $is_unit_type_range ? ceil($total_units) : $total_units;
							
				$fprice = $price*$total_units;
				//$fprice = $price*($value/$price_unit);
				
				if($price_type === 'dynamic-excl-base-price' && is_numeric($product_price) && $value >= $price_unit){
					//$fprice = $fprice - $product_price;
				}
			}
		}else if($price_type === 'custom'){
			if($value && is_numeric($value)){
				$fprice = $value;
			}
		}else{
			if(is_numeric($price)){
				$fprice = $price;
			}
		}
		
		if($name){
			$fprice = apply_filters('epo_product_field_extra_cost_'.$name, $fprice, $product_price, $price_info); //Deprecated
			$fprice = apply_filters('epo_product_field_extra_cost', $fprice, $name, $price_info, $product_info);
		}

		//return $fprice;
		return is_numeric($fprice) ? $fprice : 0;
	}
	
	private static function get_extra_cost_option_field($data, $field_type, $product_info){
		$fprice = 0;
		$name  = isset($data['name']) ? $data['name'] : '';
		$value = isset($data['value']) ? $data['value'] : false;
		$product_price = is_array($product_info) && isset($product_info['price']) ? $product_info['price'] : false;

		if($field_type === 'select' || $field_type === 'radio'){
			$options = $data['options'];
			//$value = $data['value'];
			
			if(is_array($options) && isset($options[$value])){
				$selected_option = $options[$value];
				
				if(isset($selected_option['price']) && isset($selected_option['price_type'])){
					$fprice = $selected_option['price'];
					$fprice_type = $selected_option['price_type'];
					
					if($fprice_type === 'percentage'){
						if(is_numeric($fprice) && is_numeric($product_price)){
							$fprice = ($fprice/100)*$product_price;
						}	
					}
				}
			}
		}else if($field_type === 'multiselect' || $field_type === 'checkboxgroup'){
			$options = $data['options'];
			//$value = $data['value'];
		
			if(is_array($options) && is_array($value)){
				foreach($value as $option_value){
					if(isset($options[$option_value])){
						$selected_option = $options[$option_value];
						
						if(isset($selected_option['price']) && isset($selected_option['price_type'])){
							$price = $selected_option['price'];
							$fprice_type = $selected_option['price_type'];
							
							if($fprice_type === 'percentage'){
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
		}

		if($name){
			$fprice = apply_filters('epo_product_field_extra_cost', $fprice, $name, $data, $product_info);
		}

		//return $fprice;
		return is_numeric($fprice) ? $fprice : 0;
	}
	
	public static function get_display_price_item_meta($data, $price_type, $price, $product_info, $plain = false){
		$price_html = '';
		$price = self::get_extra_cost_final($product_info, $data);
		$name = isset($data['name']) ? $data['name'] : false;
		
		if(is_numeric($price) && $price != 0){
			$price_html = self::display_price($price, $data, array(), $plain);
			$price_prefix = apply_filters('epo_item_meta_price_prefix', ' (', $name, $price, $data);
			$price_suffix = apply_filters('epo_item_meta_price_suffix', ')', $name, $price, $data);
			
			$price_html = $price_prefix.$price_html.$price_suffix;
		}
		return apply_filters('epo_item_meta_display_price', $price_html, $name, $data);
				
		/*
		$html = '';
		
		$field_type = isset($data['field_type']) ? $data['field_type'] : false;
		if(self::is_price_field_type_option($field_type)){
			$price = self::get_extra_cost_option_field($data, $field_type, $product_price);
		}
		
		if($price_type != 'custom' && is_numeric($price) && $price != 0){
			$html = $price > 0 ? ' (+'.$price.')' : ' ('.$price.')';
		}
		return $html;
		*/
	}
	
	public static function display_price($price, $field, $args = array(), $plain = false){
		extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
			'currency'           => '',
			'decimal_separator'  => wc_get_price_decimal_separator(),
			'thousand_separator' => wc_get_price_thousand_separator(),
			'decimals'           => wc_get_price_decimals(),
			'price_format'       => get_woocommerce_price_format(),
		) ) ) );
	
		$unformatted_price = $price;
		$negative = $price < 0;
		$price = apply_filters('raw_woocommerce_price', floatval($negative ? $price * -1 : $price));
		$price = apply_filters('formatted_woocommerce_price', number_format($price, $decimals, $decimal_separator, $thousand_separator), $price, $decimals, $decimal_separator, $thousand_separator);
	
		if(apply_filters('woocommerce_price_trim_zeros', false) && $decimals > 0){
			$price = wc_trim_zeros($price);
		}
		
		$price_sign = $negative ? '-' : ($price > 0 ? '+' : '');
		$price_sign = apply_filters('epo_extra_option_display_price_sign', $price_sign, $unformatted_price, $field);
		
		$return = '';
		if($plain){
			$return = self::display_price_plain($price_sign, $price_format, $currency, $price, $unformatted_price, $field);
		}else{
			$return = self::display_price_formatted($price_sign, $price_format, $currency, $price, $unformatted_price, $field);
		}
		//$formatted_price = $price_sign . sprintf($price_format, '<span class="epo-currency-symbol">'.get_woocommerce_currency_symbol($currency).'</span>', $price);
		//$return = '<span class="epo-price-amount">'. $formatted_price .'</span>';

		return apply_filters('epo_extra_option_display_price', $return, $price, $unformatted_price, $field);
	}
	
	private static function display_price_formatted($price_sign, $price_format, $currency, $price, $unformatted_price, $field){
		$formatted_price = $price_sign . sprintf($price_format, '<span class="epo-currency-symbol">'.get_woocommerce_currency_symbol($currency).'</span>', $price);
		$return = '<span class="epo-price-amount">'. $formatted_price .'</span>';
		return apply_filters('epo_extra_option_display_price_formatted', $return, $price, $unformatted_price, $field);
	}
	
	private static function display_price_plain($price_sign, $price_format, $currency, $price, $unformatted_price, $field){
		$return = $price_sign . sprintf($price_format, get_woocommerce_currency_symbol($currency), $price);
		return apply_filters('epo_extra_option_display_price_plain', $return, $price, $unformatted_price, $field);
	}
	
	public static function is_price_field_type_option($type){
		if($type && ($type === 'select' || $type === 'multiselect' || $type === 'radio' | $type === 'checkboxgroup')){
			return true;
		}
		return false;
	}
}

endif;