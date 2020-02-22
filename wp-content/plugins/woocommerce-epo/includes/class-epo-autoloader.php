<?php
/**
 * Auto-loads the required dependencies for this plugin.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes
 */
if(!defined('WPINC')){	die; }

if(!class_exists('EPO_Autoloader')):

class EPO_Autoloader {
	private $include_path = '';
	
	private $class_path = array(
				'wepo_condition' => 'includes/model/rules/class-wepo-condition.php',
				'wepo_condition_set' => 'includes/model/rules/class-wepo-condition-set.php',
				'wepo_condition_rule' => 'includes/model/rules/class-wepo-rule.php',
				'wepo_condition_rule_set' => 'includes/model/rules/class-wepo-rule-set.php',
				
				'wepo_product_page_section' => 'includes/model/class-wepo-section.php',
				'wepo_product_field' => 'includes/model/fields/class-wepo-field.php',
				'wepo_product_field_inputtext' => 'includes/model/fields/class-wepo-field-inputtext.php',
				'wepo_product_field_hidden' => 'includes/model/fields/class-wepo-field-hidden.php',
				'wepo_product_field_password' => 'includes/model/fields/class-wepo-field-password.php',
				'wepo_product_field_number' => 'includes/model/fields/class-wepo-field-number.php',
				'wepo_product_field_tel' => 'includes/model/fields/class-wepo-field-tel.php',
				'wepo_product_field_textarea' => 'includes/model/fields/class-wepo-field-textarea.php',				
				'wepo_product_field_select' => 'includes/model/fields/class-wepo-field-select.php',
				'wepo_product_field_multiselect' => 'includes/model/fields/class-wepo-field-multiselect.php',
				'wepo_product_field_radio' => 'includes/model/fields/class-wepo-field-radio.php',
				'wepo_product_field_checkbox' => 'includes/model/fields/class-wepo-field-checkbox.php',
				'wepo_product_field_checkboxgroup' => 'includes/model/fields/class-wepo-field-checkboxgroup.php',
				'wepo_product_field_datepicker' => 'includes/model/fields/class-wepo-field-datepicker.php',
				'wepo_product_field_timepicker' => 'includes/model/fields/class-wepo-field-timepicker.php',
				'wepo_product_field_file' => 'includes/model/fields/class-wepo-field-file.php',
				'wepo_product_field_heading' => 'includes/model/fields/class-wepo-field-heading.php',
				'wepo_product_field_label' => 'includes/model/fields/class-wepo-field-label.php',
		);
	
	/*private $class_path = array(
				'WEPO_Form_Builder' => 'classes/admin/class-wepo-admin-form-builder.php',
				
				'WEPO_Extra_Product_Options_Utils' => 'classes/fe/class-wepo-extra-product-options-utils.php',
				
				'WEPO_Condition' => 'classes/fe/rules/class-wepo-condition.php',
				'WEPO_Condition_Set' => 'classes/fe/rules/class-wepo-condition-set.php',
				'WEPO_Condition_Rule' => 'classes/fe/rules/class-wepo-rule.php',
				'WEPO_Condition_Rule_Set' => 'classes/fe/rules/class-wepo-rule-set.php',
				
				'WEPO_Product_Page_Section' => 'classes/fe/class-wepo-section.php',
				'WEPO_Product_Field' => 'classes/fe/fields/class-wepo-field.php',
				'WEPO_Product_Field_InputText' => 'classes/fe/fields/class-wepo-field-inputtext.php',
				'WEPO_Product_Field_Hidden' => 'classes/fe/fields/class-wepo-field-hidden.php',
				'WEPO_Product_Field_Password' => 'classes/fe/fields/class-wepo-field-password.php',
				'WEPO_Product_Field_Textarea' => 'classes/fe/fields/class-wepo-field-textarea.php',				
				'WEPO_Product_Field_Select' => 'classes/fe/fields/class-wepo-field-select.php',
				'WEPO_Product_Field_Multiselect' => 'classes/fe/fields/class-wepo-field-multiselect.php',
				'WEPO_Product_Field_Radio' => 'classes/fe/fields/class-wepo-field-radio.php',
				'WEPO_Product_Field_Checkbox' => 'classes/fe/fields/class-wepo-field-checkbox.php',
				'WEPO_Product_Field_CheckboxGroup' => 'classes/fe/fields/class-wepo-field-checkboxgroup.php',
				'WEPO_Product_Field_DatePicker' => 'classes/fe/fields/class-wepo-field-datepicker.php',
				'WEPO_Product_Field_TimePicker' => 'classes/fe/fields/class-wepo-field-timepicker.php',
				'WEPO_Product_Field_Heading' => 'classes/fe/fields/class-wepo-field-heading.php',
				'WEPO_Product_Field_Label' => 'classes/fe/fields/class-wepo-field-label.php',
				'WEPO_Product_Field_Factory' => 'classes/fe/fields/class-wepo-field-factory.php',
			
				'WEPO_Settings' 	 => 'classes/class-wepo-settings.php',
				'WEPO_Settings_Page' => 'classes/class-wepo-settings-page.php',
				'WEPO_License_Settings' => 'classes/class-wepo-license-settings.php',
				'WEPO_Extra_Product_Options_Settings' => 'classes/fe/class-wepo-extra-product-options-settings.php',
				'WEPO_Extra_Product_Options_Advanced_Settings' => 'classes/fe/class-wepo-extra-product-options-settings-advanced.php',
			
				'WEPO_Extra_Product_Options_Frontend' => 'classes/fe/class-wepo-extra-product-options-frontend.php',
		);*/

	public function __construct() {
		$this->include_path = untrailingslashit(EPO_PATH);
		
		if(function_exists("__autoload")){
			spl_autoload_register("__autoload");
		}
		spl_autoload_register(array($this, 'autoload'));
	}

	/** Include a class file. */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			require_once( $path );
			return true;
		}
		return false;
	}
	
	public function autoload_( $class ) {
		if(isset($this->class_path[$class])){
			$file = $this->class_path[$class];
			$this->load_file( TH_WEPO_PATH.$file );
		}
	}
	
	/** Class name to file name. */
	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}
	
	public function autoload( $class ) {
		$class = strtolower( $class );
		$file  = $this->get_file_name_from_class( $class );
		$path  = '';
		$file_path  = '';

		if (isset($this->class_path[$class])){
			$file_path = $this->include_path . '/' . $this->class_path[$class];
		} else {
			if (strpos($class, 'epo_admin') === 0){
				$path = $this->include_path . '/admin/';
			} elseif (strpos($class, 'epo_public') === 0){
				$path = $this->include_path . '/public/';
			} elseif (strpos($class, 'epo_utils') === 0){
				$path = $this->include_path . '/includes/utils/';
			} else{
				$path = $this->include_path . '/includes/';
			}
			$file_path = $path . $file;
		}
		
		if( empty($file_path) || (!$this->load_file($file_path) && strpos($class, 'epo_') === 0) ) {
			$this->load_file( $this->include_path . $file );
		}
	}
}

endif;

new EPO_Autoloader();
