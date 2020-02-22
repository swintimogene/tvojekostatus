<?php
/**
 * The admin general settings page functionality of the plugin.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('EPO_Admin_Settings_General')):

class EPO_Admin_Settings_General extends EPO_Admin_Settings {
	protected static $_instance = null;
	
	private $cell_props_L = array();
	private $cell_props_R = array();
	private $cell_props_CB = array();
	private $cell_props_CBS = array();
	private $cell_props_CBL = array();
	private $cell_props_CP = array();
	
	private $section_props = array();
	private $field_props = array();
	private $field_props_display = array();
	
	public function __construct() {
		parent::__construct('general_settings', '');
		
		add_filter('epo_load_products', array('EPO_Admin_Utils', 'load_products'));
		add_filter('epo_load_products_cat', array('EPO_Admin_Utils', 'load_products_cat'));
		add_filter('epo_load_user_roles', array('EPO_Admin_Utils', 'load_user_roles'));
		
		$this->init_constants();
	}
	
	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function init_constants(){
		$this->cell_props_L = array( 
			'label_cell_props' => 'width="13%"', 
			'input_cell_props' => 'width="34%"', 
			'input_width' => '250px',  
		);
		
		$this->cell_props_R = array( 
			'label_cell_props' => 'width="13%"', 
			'input_cell_props' => 'width="34%"', 
			'input_width' => '250px', 
		);
		
		$this->cell_props_CB = array( 
			'label_props' => 'style="margin-right: 40px;"', 
		);
		$this->cell_props_CBS = array( 
			'label_props' => 'style="margin-right: 15px;"', 
		);
		$this->cell_props_CBL = array( 
			'label_props' => 'style="margin-right: 52px;"', 
		);
		
		$this->cell_props_CP = array(
			'label_cell_props' => 'width="13%"', 
			'input_cell_props' => 'width="34%"', 
			'input_width' => '225px',
		);
		
		//$this->section_props = $this->get_section_form_props();
		$this->field_props = $this->get_field_form_props();
		$this->field_props_display = $this->get_field_form_props_display();
	} 
	
	public function get_html_text_tags(){
		return array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'p'  => 'p', 'div' => 'div', 'span' => 'span', 'label' => 'label');
	}
	
	public function get_field_types(){
		return array('inputtext' => 'Text', 'hidden' => 'Hidden', 'password' => 'Password', 'number' => 'Number', 'tel' => 'Tel', 'textarea' => 'Textarea', 'select' => 'Select', 'multiselect' => 'Multiselect', 
			'radio' => 'Radio', 'checkbox' => 'Checkbox', 'checkboxgroup' => 'Checkbox Group', 'datepicker' => 'Date Picker', 'timepicker' => 'Time Picker', 
			'file' => 'File Upload', 'heading' => 'Heading', 'label' => 'Label');
		/*return array('inputtext' => 'Text', 'hidden' => 'Hidden', 'password' => 'Password', 'textarea' => 'Textarea', 'select' => 'Select', 'multiselect' => 'Multiselect', 
			'radio' => 'Radio', 'checkbox' => 'Checkbox', 'checkboxgroup' => 'Checkbox Group', 'datepicker' => 'Date Picker', 'timepicker' => 'Time Picker', 
			'heading' => 'Heading', 'label' => 'Label');*/
	}
		
	public function get_available_positions(){
		$positions = array(
			'woo_before_add_to_cart_button'		=> 'Before Add To Cart Button',
			'woo_after_add_to_cart_button'		=> 'After Add To Cart Button',
			'woo_single_variation_5' 			=> 'Before Variation Data (for variable products)',
			/*'woo_before_add_to_cart_quantity' 	=> 'Before Add To Cart Quantity',
			'woo_after_add_to_cart_quantity'  	=> 'After Add To Cart Quantity',
			'woo_before_variations_form' 		=> 'Before Variations Form',
			'woo_after_variations_form'  		=> 'After Variations Form',
			'woo_before_single_variation' 		=> 'Before Single Variation',
			'woo_after_single_variation'  		=> 'After Single Variation',
			'woo_single_variation_90' 			=> 'woo_single_variation_90',
			
			'woo_single_product_before_title' 		=> 'Before Title',
			'woo_single_product_after_title' 		=> 'After Title',
			'woo_single_product_before_rating' 		=> 'Before Rating',
			'woo_single_product_after_rating' 		=> 'After Rating',
			'woo_single_product_before_price' 		=> 'Before Price',
			'woo_single_product_after_price' 		=> 'After Price',
			'woo_single_product_before_excerpt' 	=> 'Before Excerpt',
			'woo_single_product_after_excerpt' 		=> 'After Excerpt',
			'woo_single_product_before_add_to_cart' => 'Before Add To Cart',
			'woo_single_product_after_add_to_cart'  => 'After Add To Cart',			
			'woo_single_product_before_meta' 		=> 'Before Meta',
			'woo_single_product_after_meta' 		=> 'After Meta',
			'woo_single_product_before_sharing' 	=> 'Before Sharing',
			'woo_single_product_after_sharing' 		=> 'After Sharing',*/
		);

		return apply_filters('epo_extra_fields_display_position', $positions);
	}
	
	public function get_section_form_props(){
		$positions = $this->get_available_positions();
		$html_text_tags = $this->get_html_text_tags();
		
		/*$box_types = array(
			'' 				 => 'Normal (clear)',
			'box' 			 => 'Box',
			'collapse' 		 => 'Expand and Collapse (start opened)',
			'collapseclosed' => 'Expand and Collapse (start closed)',
			'accordion' 	 => 'Accordion',
		);*/
		//$title_positions = array( '' => 'Above field', 'left' => 'Left of the field', 'right' => 'Right of the field', 'disable' => 'Disable' );
		
		return array(
			'name' 		 => array('name'=>'name', 'label'=>'Name/ID', 'type'=>'text', 'required'=>1),
			'position' 	 => array('name'=>'position', 'label'=>'Display Position', 'type'=>'select', 'options'=>$positions, 'required'=>1),
			//'box_type' 	 => array('name'=>'box_type', 'label'=>'Box Type', 'type'=>'select', 'options'=>$box_types),
			'order' 	 => array('name'=>'order', 'label'=>'Display Order', 'type'=>'text'),
			'cssclass' 	 => array('name'=>'cssclass', 'label'=>'CSS Class', 'type'=>'text'),
			'show_title' => array('name'=>'show_title', 'label'=>'Show section title in product page.', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>1),
			
			'title_cell_with' => array('name'=>'title_cell_with', 'label'=>'Col-1 Width', 'type'=>'text', 'value'=>''),
			'field_cell_with' => array('name'=>'field_cell_with', 'label'=>'Col-2 Width', 'type'=>'text', 'value'=>''),
			
			'title' 		   => array('name'=>'title', 'label'=>'Title', 'type'=>'text', 'required'=>1),
			//'title_position' => array('name'=>'title_position', 'label'=>'Title Position', 'type'=>'select', 'options'=>$title_positions),
			'title_type' 	   => array('name'=>'title_type', 'label'=>'Title Type', 'type'=>'select', 'value'=>'h3', 'options'=>$html_text_tags),
			'title_color' 	   => array('name'=>'title_color', 'label'=>'Title Color', 'type'=>'colorpicker'),
			'title_class' 	   => array('name'=>'title_class', 'label'=>'Title Class', 'type'=>'text'),
			
			'subtitle' 			  => array('name'=>'subtitle', 'label'=>'Subtitle', 'type'=>'text'),
			//'subtitle_position' => array('name'=>'subtitle_position', 'label'=>'Subtitle Position', 'type'=>'select', 'options'=>$title_positions),
			'subtitle_type' 	  => array('name'=>'subtitle_type', 'label'=>'Subtitle Type', 'type'=>'select', 'value'=>'h3', 'options'=>$html_text_tags),
			'subtitle_color' 	  => array('name'=>'subtitle_color', 'label'=>'Subtitle Color', 'type'=>'colorpicker'),
			'subtitle_class' 	  => array('name'=>'subtitle_class', 'label'=>'Subtitle Class', 'type'=>'text'),
		);
	}
	
	public function get_field_form_props(){
		$html_text_tags = $this->get_html_text_tags();
		$field_types = $this->get_field_types();
		
		$validators = array(
			'email' => 'Email',
			'number' => 'Number',
		);
		$custom_validators = EPO_Utils::get_settings('custom_validators');
		if(is_array($custom_validators)){
			foreach( $custom_validators as $vname => $validator ) {
				$validators[$vname] = $validator['label'];
			}
		}
		
		$confirm_validators = EPO_Utils::get_settings('confirm_validators');
		if(is_array($confirm_validators)){
			foreach( $confirm_validators as $vname => $validator ) {
				$validators[$vname] = $validator['label'];
			}
		}
		
		$price_types = array(
			'normal' => 'Fixed',
			'custom' => 'Custom',
			'percentage' => 'Percentage of Product Price',
			'dynamic' => 'Dynamic',
			'dynamic-excl-base-price' => 'Dynamic - Exclude base price ',
		);
		
		$title_positions = array(
			'left' => 'Left of the field',
			'above' => 'Above field',
		);
		
		$time_formats = array(
			'h:i A' => '12-hour format',
			'H:i' => '24-hour format',
		);
		
		$week_days = array(
			'sun' => 'Sunday',
			'mon' => 'Monday',
			'tue' => 'Tuesday',
			'wed' => 'Wednesday',
			'thu' => 'Thursday',
			'fri' => 'Friday',
			'sat' => 'Saturday',
		);
		
		$upload_file_types = array(
			'png'  => 'PNG',
			'jpg'  => 'JPG',
			'gif'  => 'GIF',
			'pdf'  => 'PDF',
			'docx' => 'DOCX',
		);
		
		$hint_name = "Used to save values in database. Name must begin with a lowercase letter.";
		$hint_title = "Display name for the input field which will be shown on the product page. A link can be set by using the relevant HTML tags. For example: <a href='URL that you want to link to' target='_blank'>I agree to the terms and conditions</a>. Please use single quotes instead of double quotes";
		$hint_value = "Default value to be shown when the checkout form is loaded.";
		$hint_placeholder = "Short hint that describes the expected value/format of the input field.";
		$hint_input_class = "Define CSS class here to make the input field styled differently.";
		$hint_title_class = "Define CSS class name here to style Label.";
		
		$hint_accept = "Specify allowed file types separated by comma (e.g. png,jpg,docx,pdf).";
		
		$hint_default_date = "Specify a date in the current dateFormat, or number of days from today (e.g. +7) or a string of values and periods ('y' for years, 'm' for months, 'w' for weeks, 'd' for days, e.g. '+1m +7d'), or leave empty for today.";
		$hint_date_format = "The format for parsed and displayed dates.";
		$hint_min_date = "The minimum selectable date. Specify a date in yyyy-mm-dd format, or number of days from today (e.g. -7) or a string of values and periods ('y' for years, 'm' for months, 'w' for weeks, 'd' for days, e.g. '-1m -7d'), or leave empty for no minimum limit.";
		$hint_max_date = "The maximum selectable date. Specify a date in yyyy-mm-dd format, or number of days from today (e.g. +7) or a string of values and periods ('y' for years, 'm' for months, 'w' for weeks, 'd' for days, e.g. '+1m +7d'), or leave empty for no maximum limit.";
		$hint_year_range = "The range of years displayed in the year drop-down: either relative to today's year ('-nn:+nn' e.g. -5:+3), relative to the currently selected year ('c-nn:c+nn' e.g. c-10:c+10), absolute ('nnnn:nnnn' e.g. 2002:2012), or combinations of these formats ('nnnn:+nn' e.g. 2002:+3). Note that this option only affects what appears in the drop-down, to restrict which dates may be selected use the minDate and/or maxDate options.";
		$hint_number_of_months = "The number of months to show at once.";
		$hint_disabled_dates = "Specify dates in yyyy-mm-dd format separated by comma.";
		
		return array(
			'name' 		  => array('type'=>'text', 'name'=>'name', 'label'=>'Name', 'required'=>1),
			'type' 		  => array('type'=>'select', 'name'=>'type', 'label'=>'Field Type', 'required'=>1, 'options'=>$field_types, 
								'onchange'=>'epoFieldTypeChangeListner(this)'),
			'value' 	  => array('type'=>'text', 'name'=>'value', 'label'=>'Default Value'),
			'placeholder' => array('type'=>'text', 'name'=>'placeholder', 'label'=>'Placeholder'),
			'validate' 	  => array('type'=>'multiselect', 'name'=>'validate', 'label'=>'Validations', 'placeholder'=>'Select validations', 'options'=>$validators),
			'cssclass'    => array('type'=>'text', 'name'=>'cssclass', 'label'=>'Wrapper Class', 'placeholder'=>'Seperate classes with comma'),
			'input_class'    => array('type'=>'text', 'name'=>'input_class', 'label'=>'Input Class', 'placeholder'=>'Seperate classes with comma'),
			
			'price'        => array('type'=>'text', 'name'=>'price', 'label'=>'Price', 'placeholder'=>'Price'),
			'price_unit'   => array('type'=>'text', 'name'=>'price_unit', 'label'=>'Unit', 'placeholder'=>'Unit'),
			'price_type'   => array('type'=>'select', 'name'=>'price_type', 'label'=>'Price Type', 'options'=>$price_types, 'onchange'=>'epoPriceTypeChangeListener(this)'),
			'price_min_unit' => array('type'=>'text', 'name'=>'price_min_unit', 'label'=>'Min. Unit', 'placeholder'=>'Min. Unit'),
			//'price_prefix' => array('type'=>'text', 'name'=>'price_prefix', 'label'=>'Price Prefix'),
			//'price_suffix' => array('type'=>'text', 'name'=>'price_suffix', 'label'=>'Price Suffix'),
			
			'minlength'   => array('type'=>'text', 'name'=>'minlength', 'label'=>'Min. Length', 'hint_text'=>'The minimum number of characters allowed'),
			'maxlength'   => array('type'=>'text', 'name'=>'maxlength', 'label'=>'Max. Length', 'hint_text'=>'The maximum number of characters allowed'),
			
			'checked'  => array('type'=>'checkbox', 'name'=>'checked', 'label'=>'Checked by default', 'value'=>'yes', 'checked'=>0),
			'required' => array('type'=>'checkbox', 'name'=>'required', 'label'=>'Required', 'value'=>'yes', 'checked'=>0, 'status'=>1),
			'enabled'  => array('type'=>'checkbox', 'name'=>'enabled', 'label'=>'Enabled', 'value'=>'yes', 'checked'=>1, 'status'=>1),
			
			'title'          => array('type'=>'text', 'name'=>'title', 'label'=>'Title'),
			'title_position' => array('type'=>'select', 'name'=>'title_position', 'label'=>'Title Position', 'options'=>$title_positions),
			'title_type'     => array('type'=>'select', 'name'=>'title_type', 'label'=>'Title Type', 'value'=>'label', 'options'=>$html_text_tags),
			'title_color'    => array('type'=>'colorpicker', 'name'=>'title_color', 'label'=>'Title Color'),
			'title_class'    => array('type'=>'text', 'name'=>'title_class', 'label'=>'Title Class', 'placeholder'=>'Seperate classes with comma'),
			
			'subtitle'       => array('type'=>'text', 'name'=>'subtitle', 'label'=>'Subtitle'),
			'subtitle_type'  => array('type'=>'select', 'name'=>'subtitle_type', 'label'=>'Subtitle Type', 'value'=>'label', 'options'=>$html_text_tags),
			'subtitle_color' => array('type'=>'colorpicker', 'name'=>'subtitle_color', 'label'=>'Subtitle Color'),
			'subtitle_class' => array('type'=>'text', 'name'=>'subtitle_class', 'label'=>'Subtitle Class', 'placeholder'=>'Seperate classes with comma'),
			
			'maxsize' => array('type'=>'text', 'name'=>'maxsize', 'label'=>'Maxsize(in MB)'),
			'accept'  => array('type'=>'text', 'name'=>'accept', 'label'=>'Accepted File Types', 'placeholder'=>'eg: png,jpg,docx,pdf', 'hint_text'=>$hint_accept),

			'cols' => array('type'=>'text', 'name'=>'cols', 'label'=>'Cols'),
			'rows' => array('type'=>'text', 'name'=>'rows', 'label'=>'Rows'),
						
			'default_date' => array('type'=>'text','name'=>'default_date', 'label'=>'Default Date','placeholder'=>"Leave empty for today's date",'hint_text'=>$hint_default_date),
			'date_format'  => array('type'=>'text', 'name'=>'date_format', 'label'=>'Date Format', 'value'=>'dd/mm/yy', 'hint_text'=>$hint_date_format),
			'min_date'     => array('type'=>'text', 'name'=>'min_date', 'label'=>'Min. Date', 'placeholder'=>'The minimum selectable date', 'hint_text'=>$hint_min_date),
			'max_date'     => array('type'=>'text', 'name'=>'max_date', 'label'=>'Max. Date', 'placeholder'=>'The maximum selectable date', 'hint_text'=>$hint_max_date),
			'year_range'   => array('type'=>'text', 'name'=>'year_range', 'label'=>'Year Range', 'value'=>'-100:+1', 'hint_text'=>$hint_year_range),
			'number_of_months' => array('type'=>'text', 'name'=>'number_of_months', 'label'=>'Number Of Months', 'value'=>'1', 'hint_text'=>$hint_number_of_months),
			'disabled_days'  => array('type'=>'multiselect', 'name'=>'disabled_days', 'label'=>'Disabled Days', 'placeholder'=>'Select days to disable', 'options'=>$week_days),
			'disabled_dates' => array('type'=>'text', 'name'=>'disabled_dates', 'label'=>'Disabled Dates', 'placeholder'=>'Seperate dates with comma', 
			'hint_text'=>$hint_disabled_dates),
			
			'min_time'    => array('type'=>'text', 'name'=>'min_time', 'label'=>'Min. Time', 'value'=>'12:00am', 'sub_label'=>'ex: 12:30am'),
			'max_time'    => array('type'=>'text', 'name'=>'max_time', 'label'=>'Max. Time', 'value'=>'11:30pm', 'sub_label'=>'ex: 11:30pm'),
			'start_time'  => array('type'=>'text', 'name'=>'start_time', 'label'=>'Start Time', 'value'=>'', 'sub_label'=>'ex: 2h 30m'),
			'time_step'   => array('type'=>'text', 'name'=>'time_step', 'label'=>'Time Step', 'value'=>'30', 'sub_label'=>'In minutes, ex: 30'),
			'time_format' => array('type'=>'select', 'name'=>'time_format', 'label'=>'Time Format', 'value'=>'h:i A', 'options'=>$time_formats),
			'linked_date' => array('type'=>'text', 'name'=>'linked_date', 'label'=>'Linked Date'),
		);
	}
	
	public function get_field_form_props_display(){
		return array(
			'name'  => array('name'=>'name', 'type'=>'text'),
			'type'  => array('name'=>'type', 'type'=>'select'),
			'title' => array('name'=>'title', 'type'=>'text'),
			'placeholder' => array('name'=>'placeholder', 'type'=>'text'),
			'validate' => array('name'=>'validate', 'type'=>'text'),
			'required' => array('name'=>'required', 'type'=>'checkbox', 'status'=>1),
			'enabled'  => array('name'=>'enabled', 'type'=>'checkbox', 'status'=>1),
		);
	}
	
	public function render_page(){
		$this->render_tabs();
		$this->render_sections();
		$this->render_content();
	}
	
	public function reset_to_default() {
		delete_option(EPO_Utils::OPTION_KEY_CUSTOM_SECTIONS);
		delete_option(EPO_Utils::OPTION_KEY_SECTION_HOOK_MAP);
		delete_option(EPO_Utils::OPTION_KEY_NAME_TITLE_MAP);
		
		return '<div class="updated"><p>'. EPO_i18n::__t('Product fields successfully reset') .'</p></div>';
	}
	
	/*------------------------------------*
	*----- SECTION FUNCTIONS - START ----*
	*------------------------------------*/
	/* Override */
	public function render_sections() {
		$result = false;
		if(isset($_POST['reset_fields']))
			$result = $this->reset_to_default();
			
		if(isset($_POST['s_action']) && $_POST['s_action'] == 'new')
			$result = $this->create_section();	
			
		if(isset($_POST['s_action']) && $_POST['s_action'] == 'edit')
			$result = $this->edit_section();
			
		if(isset($_POST['s_action']) && $_POST['s_action'] == 'remove')
			$result = $this->remove_section();
			
		$sections = array();
		$sections = EPO_Admin_Utils::get_sections();
		if(empty($sections)){
			return;
		}
		
		$this->sort_sections($sections);
		
		$array_keys = array_keys( $sections );
		$current_section = $this->get_current_section();
				
		echo '<ul class="thpladmin-sections">';
		$i=0; 
		foreach( $sections as $name => $section ){
			$url = $this->get_admin_url($this->page_id, sanitize_title($name));
			$rules_json = htmlspecialchars($section->get_property('conditional_rules_json'));
			$rules_json_ajax = htmlspecialchars($section->get_property('conditional_rules_ajax_json'));
			
			echo '<li><a href="'.$url.'" class="'.($current_section == $name ? 'current' : '').'">'.EPO_i18n::__t($section->get_property('title')).'</a></li>';
			
			if(EPO_Utils_Section::is_custom_section($section)){
				?>
                <li>
                	<form id="section_prop_form_<?php echo $name; ?>" method="post" action="">
						<?php /*?><input type="hidden" name="f_props[<?php echo $i; ?>]" class="f_props" value='<?php echo $props_json; ?>' /><?php */?>
                        <input type="hidden" name="f_rules[<?php echo $i; ?>]" class="f_rules" value="<?php echo $rules_json; ?>" />
                        <input type="hidden" name="f_rules_ajax[<?php echo $i; ?>]" class="f_rules_ajax" value="<?php echo $rules_json_ajax; ?>" />
                    </form>
					<span class='s_edit_btn dashicons dashicons-edit tips' data-tip='<?php EPO_i18n::_et('Edit Section'); ?>'  
					onclick='epoOpenEditSectionForm(<?php echo EPO_Utils_Section::get_property_json($section); ?>)'></span>
                </li>
				<li>
					<span class="s_copy_btn dashicons dashicons-admin-page tips" data-tip="<?php EPO_i18n::_et('Duplicate Section'); ?>"  
					onclick='epoOpenCopySectionForm(<?php echo EPO_Utils_Section::get_property_json($section); ?>)'></span>
				</li>
				<li>
                    <form method="post" action="">
                        <input type="hidden" name="s_action" value="remove" />
                        <input type="hidden" name="i_name" value="<?php echo $name; ?>" />
						<span class='s_delete_btn dashicons dashicons-no tips' data-tip='<?php EPO_i18n::_et('Delete Section'); ?>'  
						onclick='epoRemoveSection(this)'></span>
					</form>
                </li>
                <?php
			}
			echo '<li>';
			echo(end( $array_keys ) == $name ? '' : '<li style="margin-right: 5px;">|</li>');
			echo '</li>';
			
			$i++;
		}
		echo '<li><a href="javascript:void(0)" onclick="epoOpenNewSectionForm()" class="add_link">+ '. EPO_i18n::__t( 'Add new section' ) .'</a></li>';
		echo '</ul>';		
		
		if($result){
			echo $result;
		}
	}
	
	public function prepare_copy_section($section, $posted){
		$s_name_copy = isset($posted['s_name_copy']) ? $posted['s_name_copy'] : '';
		if($s_name_copy){
			$section_copy = EPO_Admin_Utils::get_section($s_name_copy);
			if(EPO_Utils_Section::is_valid_section($section_copy)){
				$field_set = $section_copy->get_property('fields');
				if(is_array($field_set) && !empty($field_set)){
					$section->set_property('fields', $field_set);
				}
			}
		}
		return $section;
	}
	
	public function create_section(){
		$section = EPO_Utils_Section::prepare_section_from_posted_data($_POST);
		$section = $this->prepare_copy_section($section, $_POST);
		$result1 = $this->update_section($section);
		$result2 = $this->update_options_name_title_map();
		
		if($result1 == true){
			return '<div class="updated"><p>'. EPO_i18n::__t('New section added successfully.') .'</p></div>';
		}else{
			return '<div class="error"><p> '. EPO_i18n::__t('New section not added due to an error.') .'</p></div>';
		}		
	}
	
	public function edit_section(){
		$section  = EPO_Utils_Section::prepare_section_from_posted_data($_POST, 'edit');
		$name 	  = $section->get_property('name');
		$position = $section->get_property('position');
		$old_position = !empty($_POST['i_position_old']) ? $_POST['i_position_old'] : '';
		
		if($old_position && $position && ($old_position != $position)){			
			$this->remove_section_from_hook($position_old, $name);
		}
		
		$result = $this->update_section($section);
		
		if($result == true){
			return '<div class="updated"><p>'. EPO_i18n::__t('Section details updated successfully.') .'</p></div>';
		}else{
			return '<div class="error"><p> '. EPO_i18n::__t('Section details not updated due to an error.') .'</p></div>';
		}		
	}

	public function remove_section(){
		$section_name = !empty($_POST['i_name']) ? $_POST['i_name'] : false;		
		if($section_name){	
			$result = $this->delete_section($section_name);			
										
			if ($result == true) {
				return '<div class="updated"><p>'. EPO_i18n::__t('Section removed successfully.') .'</p></div>';
			} else {
				return '<div class="error"><p> '. EPO_i18n::__t('Section not removed due to an error.') .'</p></div>';
			}
		}
	}
	
	public function update_section($section){
	 	if(EPO_Utils_Section::is_valid_section($section)){	
			$sections = EPO_Admin_Utils::get_sections();
			$sections = (isset($sections) && is_array($sections)) ? $sections : array();
			
			$sections[$section->name] = $section;
			$this->sort_sections($sections);
			
			$result1 = $this->save_sections($sections);
			$result2 = $this->update_section_hook_map($section);
	
			return $result1;
		}
		return false;
	}
	
	private function update_section_hook_map($section){
		$section_name  = $section->name;
		$display_order = $section->get_property('order');
		$hook_name 	   = $section->position;
				
	 	if(isset($hook_name) && isset($section_name) && !empty($hook_name) && !empty($section_name)){	
			$hook_map = EPO_Utils::get_section_hook_map();
			
			//Remove from hook if already hooked
			if($hook_map && is_array($hook_map)){
				foreach($hook_map as $hname => $hsections){
					if($hsections && is_array($hsections)){
						if(($key = array_search($section_name, $hsections)) !== false) {
							unset($hsections[$key]);
							$hook_map[$hname] = $hsections;
						}
					}
					
					if(empty($hsections)){
						unset($hook_map[$hname]);
					}
				}
			}
			
			if(isset($hook_map[$hook_name])){
				$hooked_sections = $hook_map[$hook_name];
				if(!in_array($section_name, $hooked_sections)){
					$hooked_sections[] = $section_name;
					$hooked_sections = $this->sort_hooked_sections($hooked_sections);
					
					$hook_map[$hook_name] = $hooked_sections;
					$this->save_section_hook_map($hook_map);
				}
			}else{
				$hooked_sections = array();
				$hooked_sections[] = $section_name;
				$hooked_sections = $this->sort_hooked_sections($hooked_sections);
				
				$hook_map[$hook_name] = $hooked_sections;
				$this->save_section_hook_map($hook_map);
			}					
		}
	}
	
	public function update_options_name_title_map(){
	 	$name_title_map = array();
	 	$sections = $this->get_sections();
		if($sections && is_array($sections)){
			foreach($sections as $section_name => $section){
				if(EPO_Utils_Section::is_valid_section($section)){					
					$fields = $section->get_property('fields');					
					if($fields && is_array($fields)){
						foreach($fields as $field_name => $field){
							if(EPO_Utils_Field::is_valid_field($field) && EPO_Utils_Field::is_enabled($field)){
								$name_title_map[$field_name] = $field->get_display_label();
							}
						}
					}
				}
			}
		}
	 
		$result = $this->save_name_title_map($name_title_map);
		return $result;
	 }
	
	public function delete_section($section_name){
		if($section_name){	
			$sections = EPO_Admin_Utils::get_sections();
			if(is_array($sections) && isset($sections[$section_name])){
				$section = $sections[$section_name];
				
				if(EPO_Utils_Section::is_valid_section($section)){
					$hook_name = $section->get_property('position');
					
					$this->remove_section_from_hook($hook_name, $section_name);
					unset($sections[$section_name]);
								
					$result = $this->save_sections($sections);		
					return $result;
				}
			}
		}
		return false;
	}
	
	private function remove_section_from_hook($hook_name, $section_name){
		if(isset($hook_name) && isset($section_name) && !empty($hook_name) && !empty($section_name)){	
			$hook_map = EPO_Utils::get_section_hook_map();
			
			if(is_array($hook_map) && isset($hook_map[$hook_name])){
				$hooked_sections = $hook_map[$hook_name];
				if(is_array($hooked_sections) && !in_array($section_name, $hooked_sections)){
					unset($hooked_sections[$section_name]);				
					$hook_map[$hook_name] = $hooked_sections;
					$this->save_section_hook_map($hook_map);
				}
			}				
		}
	}
	
	private function save_sections($sections){
		$result = update_option(EPO_Utils::OPTION_KEY_CUSTOM_SECTIONS, $sections);
		return $result;
	}
	
	private function save_section_hook_map($section_hook_map){
		$result = update_option(EPO_Utils::OPTION_KEY_SECTION_HOOK_MAP, $section_hook_map);		
		return $result;
	}
	
	private function save_name_title_map($name_title_map){
		$result = update_option(EPO_Utils::OPTION_KEY_NAME_TITLE_MAP, $name_title_map);		
		return $result;
	}
	
	private function sort_sections(&$sections){
		if(is_array($sections) && !empty($sections)){
			EPO_Admin_Utils::stable_uasort($sections, array('EPO_Admin_Utils', 'sort_sections_by_order'));
		}
	}
	
	private function sort_hooked_sections(&$sections){
		if(is_array($sections) && !empty($sections)){
			EPO_Admin_Utils::stable_uasort($sections, array('EPO_Admin_Utils', 'sort_sections_by_order'));
		}
	}
	
	/*-----------------------------------
	*------ SECTION FORMS - START ------
	*-----------------------------------*/
	private function output_add_section_form_pp(){
		?>
        <div id="epo_new_section_form_pp" title="Create New Section" class="epo_popup_wrapper">
          <?php $this->output_popup_form_section('new'); ?>
        </div>
        <?php	
		/*$fields = $this->get_section_form_props();
		?>
        <div id="epo_new_section_form_pp" title="Create New Section" class="epo_popup_wrapper">
          	<form method="post" id="epo_new_section_form" action="">
          		<input type="hidden" name="s_action" value="new" />
				<input type="hidden" name="s_name_copy" value="" />            
                <table width="100%" border="0">
                    <?php
                    $this->output_section_info_form($fields);
                    $this->render_form_fragment_h_separator();
                    $this->output_title_form($fields, true);
                    $this->render_form_fragment_h_separator();
                    $this->output_rule_form($fields, true);
                    ?>    
                </table>
          	</form>
        </div>
        <?php
		*/
	}
	
	private function output_edit_section_form_pp(){
		?>
        <div id="epo_edit_section_form_pp" title="Edit Section" class="epo_popup_wrapper">
          <?php $this->output_popup_form_section('edit'); ?>
        </div>
        <?php
		/*$fields = $this->get_section_form_props();	
		?>
        <div id="epo_edit_section_form_pp" title="Edit Section" class="epo_popup_wrapper">
          	<form method="post" id="epo_edit_section_form" action="">
          		<input type="hidden" name="s_action" value="edit" />
            	<input type="hidden" name="s_name" value="" />
            	<input type="hidden" name="i_position_old" value="" />                
          		<table width="100%" border="0">
                	<?php
                    $this->output_section_info_form($fields);
                    $this->render_form_fragment_h_separator();
                    $this->output_title_form($fields, true);
                    $this->render_form_fragment_h_separator();
                    $this->output_rule_form($fields, true);
                    ?> 
            	</table>
          	</form>
        </div>
        <?php*/
	}
	
	private function output_popup_form_section($form_type){
		$fields = $this->get_section_form_props();	
		?>
        <form method="post" id="epo_<?php echo $form_type ?>_section_form" action="">
            <input type="hidden" name="s_action" value="<?php echo $form_type ?>" />
            <div id="epo-tabs-container_<?php echo $form_type ?>">
                <ul class="thpladmin-tabs-menu">
                    <li class="first current"><a class="epo_tab_general_link" href="javascript:void(0)" 
                    onclick="epoOpenFormTab(this, 'epo-section-tab-general', '<?php echo $form_type ?>')">General Properties</a></li>
                    <li><a class="epo_tab_rules_link" href="javascript:void(0)" 
                    onclick="epoOpenFormTab(this, 'epo-section-tab-rules', '<?php echo $form_type ?>')">Display Rules</a></li>
                </ul>
                <div id="epo_section_editor_form_<?php echo $form_type ?>" class="thpladmin-tab epo_popup_wrapper">
                    <div id="epo-section-tab-general_<?php echo $form_type ?>" class="thpladmin-tab-content">
                        <?php if($form_type === 'edit'){ ?>
                            <input type="hidden" name="s_name" value="" />
                            <input type="hidden" name="i_position_old" value="" />
                        <?php }else{ ?>
                            <input type="hidden" name="s_name_copy" value="" />
                        <?php } ?>
                        <input type="hidden" name="i_rules" value="" />
						<input type="hidden" name="i_rules_ajax" value="" />
                         
                        <table width="100%" border="0">
                            <?php
                            $this->output_section_info_form($fields);
                            $this->render_form_fragment_h_separator();
                            $this->output_title_form($fields, true);
                            $this->render_form_fragment_h_separator();
                            $this->output_rule_form($fields, true);
                            ?> 
                        </table>
                    </div>
                    <div id="epo-section-tab-rules_<?php echo $form_type ?>" class="thpladmin-tab-content">
                        <table class="epo_section_form_tab_rules_placeholder" width="100%" style="margin-top: 10px;">
                        <?php 
                        $this->render_field_form_fragment_rules(); 
                        $this->render_field_form_fragment_rules_ajax();
                        ?>
                        </table>
                    </div>
                </div>
            </div>    
        </form>
        <?php
	}
	
	private function output_section_info_form($fields){
		$available_positions = $this->get_available_positions();
		
		$args_L = $this->cell_props_L; //array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		$args_R = $this->cell_props_R; //array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		
		?>
        <tr>                
            <td colspan="6" class="err_msgs"></td>
        </tr>            	
        <tr>                
            <?php
			$this->render_form_field_element($fields['name'], $args_L);
			$this->render_form_field_element($fields['position'], $args_R);
			?>
        </tr>  
        <tr>                
            <?php 
			$this->render_form_field_element($fields['cssclass'], $args_L);
			$this->render_form_field_element($fields['order'], $args_R);
			?>
        </tr> 
		<tr>                
            <?php 
			$this->render_form_field_element($fields['title_cell_with'], $args_L);
			$this->render_form_field_element($fields['field_cell_with'], $args_R);
			?>
        </tr>
        <?php
	}
	
	private function output_title_form($fields, $show_subtitle = false){
		?>
        <tr>  
        	<?php $this->render_form_field_element($fields['show_title'], array( 'input_cell_props' => 'colspan="4"' )); ?>
        </tr>
        <?php $this->render_field_form_fragment_h_spacing(); ?>
        <tr>                
        	<?php
			$this->render_form_field_element($fields['title'], $this->cell_props_L);
			$this->render_form_field_element($fields['title_type'], $this->cell_props_R);
			//$this->render_form_field_element($fields['title_position'], $args);
			?>
        </tr>
        <tr>                
        	<?php
			$this->render_form_field_element($fields['title_color'], $this->cell_props_CP);
			$this->render_form_field_element($fields['title_class'], $this->cell_props_R);
			?>
        </tr>
        
        <?php
		if($show_subtitle){
			$this->render_field_form_fragment_h_spacing();
		?>
        <tr> 
        	<?php
			$this->render_form_field_element($fields['subtitle'], $this->cell_props_L);
			$this->render_form_field_element($fields['subtitle_type'], $this->cell_props_R);
			//$this->render_form_field_element($fields['subtitle_position'], $args);
			?>
        </tr>  
        <tr>  
        	<?php
			$this->render_form_field_element($fields['subtitle_color'], $this->cell_props_CP);
			$this->render_form_field_element($fields['subtitle_class'], $this->cell_props_R);
			?>
        </tr>
        <?php
		}
	}
	
	private function output_rule_form(){
	
	}
   /*------ SECTION FORMS - END --------*/

   /*-----------------------------------*
	*----- SECTION FUNCTIONS - END -----*
	*-----------------------------------*/
	
	private function render_fields_table_heading(){
		?>
		<th class="sort"></th>
		<th class="check-column"><input type="checkbox" style="margin:0px 4px -1px -1px;" onclick="epoSelectAllProductFields(this)"/></th>
		<th class="name"><?php EPO_i18n::_et('Name'); ?></th>
		<th class="type"><?php EPO_i18n::_et('Type'); ?></th>
		<th class="label"><?php EPO_i18n::_et('Label'); ?></th>
		<th class="placeholder"><?php EPO_i18n::_et('Placeholder'); ?></th>
		<th class="validate"><?php EPO_i18n::_et('Validation Rules'); ?></th>
        <th class="status"><?php EPO_i18n::_et('Required'); ?></th>
		<th class="status"><?php EPO_i18n::_et('Enabled'); ?></th>
		<th class="actions align-center"><?php EPO_i18n::_et('Actions'); ?></th>
        <?php
	}
	
	private function render_actions_row($section){
		if(EPO_Utils_Section::is_valid_section($section)){
		?>
			<th colspan="5">
				<button type="button" class="button button-primary" onclick="epoOpenNewFieldForm('<?php echo $section->get_property('name'); ?>')">
					<?php EPO_i18n::_et('+ Add field'); ?>
				</button>
				<button type="button" class="button" onclick="epoRemoveSelectedFields()"><?php  EPO_i18n::_et('Remove'); ?></button>
				<button type="button" class="button" onclick="epoEnableSelectedFields()"><?php  EPO_i18n::_et('Enable'); ?></button>
				<button type="button" class="button" onclick="epoDisableSelectedFields()"><?php EPO_i18n::_et('Disable'); ?></button>
			</th>
			<th colspan="5">
				<input type="submit" name="save_fields" class="button-primary" value="<?php EPO_i18n::_et('Save changes') ?>" style="float:right" />
				<input type="submit" name="reset_fields" class="button" value="<?php EPO_i18n::_et('Reset to default fields') ?>" style="float:right; margin-right: 5px;" 
				onclick="return confirm('Are you sure you want to reset to default fields? all your changes will be deleted.');"/>
			</th>  
    	<?php 
		}
	}
	
	private function render_content(){
		$action = isset($_POST['f_action']) ? $_POST['f_action'] : false;
		$section_name = $this->get_current_section();
		$section = EPO_Admin_Utils::get_section($section_name);
		if(!EPO_Utils_Section::is_valid_section($section)){
			$section = EPO_Utils_Section::prepare_default_section();
		}
		
		if($action === 'new')
			echo $this->save_or_update_field($section, $action);	
			
		if($action === 'edit')
			echo $this->save_or_update_field($section, $action);
		
		if(isset($_POST['save_fields']))
			echo $this->save_fields($section);
			
		$section = EPO_Admin_Utils::get_section($section_name);
		if(!EPO_Utils_Section::is_valid_section($section)){
			$section = EPO_Utils_Section::prepare_default_section();
		}
		?>            
        <div class="wrap woocommerce"><div class="icon32 icon32-attributes" id="icon-woocommerce"><br /></div>                
			<form method="post" id="epo_product_fields_form" action="">
            <table id="epo_product_fields" class="wc_gateways widefat thpladmin_fields_table" cellspacing="0">
                <thead>
                    <tr><?php $this->render_actions_row($section); ?></tr>
                    <tr><?php $this->render_fields_table_heading(); ?></tr>						
                </thead>
                <tfoot>
                    <tr><?php $this->render_fields_table_heading(); ?></tr>
                    <tr><?php $this->render_actions_row($section); ?></tr>
                </tfoot>
                <tbody class="ui-sortable">
                <?php 
				if(EPO_Utils_Section::is_valid_section($section) && EPO_Utils_Section::has_fields($section)){
					$i=0;												
					foreach( $section->get_property('fields') as $field ) {	
						$name = $field->get_property('name');
						$type = $field->get_property('type');
						$is_enabled = $field->get_property('enabled') ? 1 : 0;
						$props_json = htmlspecialchars($this->get_property_set_json($field));
						
						$options_json = htmlspecialchars($field->get_property('options_json'));
						$rules_json = htmlspecialchars($field->get_property('conditional_rules_json'));
						$rules_json_ajax = htmlspecialchars($field->get_property('conditional_rules_ajax_json'));
						
						$disabled_actions = !$is_enabled;
				?>
						<tr class="row_<?php echo $i; echo($is_enabled === 1 ? '' : ' thpladmin-disabled') ?>">
							<td width="1%" class="sort ui-sortable-handle">
								<input type="hidden" name="f_name[<?php echo $i; ?>]" class="f_name" value="<?php echo $name; ?>" />
								<input type="hidden" name="f_order[<?php echo $i; ?>]" class="f_order" value="<?php echo $i; ?>" />
								<input type="hidden" name="f_deleted[<?php echo $i; ?>]" class="f_deleted" value="0" />
								<input type="hidden" name="f_enabled[<?php echo $i; ?>]" class="f_enabled" value="<?php echo $is_enabled; ?>" />
								
								<input type="hidden" name="f_props[<?php echo $i; ?>]" class="f_props" value='<?php echo $props_json; ?>' />
								<input type="hidden" name="f_options[<?php echo $i; ?>]" class="f_options" value="<?php echo $options_json; ?>" />
								<input type="hidden" name="f_rules[<?php echo $i; ?>]" class="f_rules" value="<?php echo $rules_json; ?>" />
								<input type="hidden" name="f_rules_ajax[<?php echo $i; ?>]" class="f_rules_ajax" value="<?php echo $rules_json_ajax; ?>" />
							</td>
							<td class="td_select"><input type="checkbox" name="select_field"/></td>
							
							<?php
							foreach( $this->field_props_display as $pname => $property ){
								//$property = $this->field_props[$pname];
							
								$pvalue = $field->get_property($pname);
								$pvalue = is_array($pvalue) ? implode(',', $pvalue) : $pvalue;
								$pvalue = esc_attr($pvalue);
								
								if($property['type'] == 'checkbox'){
									$pvalue = $pvalue ? 1 : 0;
								}
								
								if(isset($property['status']) && $property['status'] == 1){
									$statusHtml = $pvalue == 1 ? '<span class="dashicons dashicons-yes tips" data-tip="'.EPO_i18n::__t('Yes').'"></span>' : '-';
									?>
									<td class="td_<?php echo $pname; ?> status"><?php echo $statusHtml; ?></td>
									<?php
								}else{
									?>
									<td class="td_<?php echo $pname; ?>"><?php echo stripslashes($pvalue); ?></td>
									<?php
								}
							}
							?>
							
							<td class="td_actions" align="center">
								<?php if($is_enabled){ ?>
									<span class="f_edit_btn dashicons dashicons-edit tips" data-tip="<?php EPO_i18n::_et('Edit Field'); ?>"  
									onclick="epoOpenEditFieldForm(this, <?php echo $i; ?>)"></span>
								<?php }else{ ?>
									<span class="f_edit_btn dashicons dashicons-edit disabled"></span>
								<?php } ?>
	
								<span class="f_copy_btn dashicons dashicons-admin-page tips" data-tip="<?php EPO_i18n::_et('Duplicate Field'); ?>"  
								onclick="epoOpenCopyFieldForm(this, <?php echo $i; ?>)"></span>
							</td>
						</tr>						
                <?php 
						$i++;
					} 
				}else{
					echo '<tr><td colspan="10" class="empty-msg-row">'.EPO_i18n::__t('No custom fields found. Click on Add Field button to create new fields.').'</td></tr>';
				} 
				?>
                </tbody>
            </table> 
            </form>
            <?php
            $this->output_add_field_form_pp();
			$this->output_edit_field_form_pp();
			$this->output_add_section_form_pp();
			$this->output_edit_section_form_pp();
			$this->output_popup_form_field_fragments();
			?>
    	</div>
    	<?php
    }
	
	public function get_property_set_json($field){
		if(EPO_Utils_Field::is_valid_field($field)){
			$props_set = array();
			
			foreach( $this->field_props as $pname => $property ){
				$pvalue = $field->get_property($pname);
				$pvalue = is_array($pvalue) ? implode(',', $pvalue) : $pvalue;
				$pvalue = esc_attr($pvalue);
				
				if($property['type'] == 'checkbox'){
					$pvalue = $pvalue ? 1 : 0;
				}
				$props_set[$pname] = $pvalue;
			}
						
			$props_set['custom'] = EPO_Utils_Field::is_custom_field($field) ? 1 : 0;
			$props_set['price_field'] = $field->get_property('price_field') ? 1 : 0;
			$props_set['rules_action'] = $field->get_property('rules_action');
			$props_set['rules_action_ajax'] = $field->get_property('rules_action_ajax');
						
			return json_encode($props_set);
		}else{
			return '';
		}
	}
	
	private function save_or_update_field($section, $action) {
		try {
			$field = EPO_Utils_Field::prepare_field_from_posted_data($_POST, $this->field_props);
			
			if($action === 'edit'){
				$section = EPO_Utils_Section::update_field($section, $field);
			}else{
				$section = EPO_Utils_Section::add_field($section, $field);
			}
			
			$result1 = $this->update_section($section);
			$result2 = $this->update_options_name_title_map();
			
			if($result1 == true) {
				echo '<div class="updated"><p>'. EPO_i18n::__t('Your changes were saved.') .'</p></div>';
			}else {
				echo '<div class="error"><p>'. EPO_i18n::__t('Your changes were not saved due to an error (or you made none!).') .'</p></div>';
			}
		} catch (Exception $e) {
			echo '<div class="error"><p>'. EPO_i18n::__t('Your changes were not saved due to an error.') .'</p></div>';
		}
	}
	
	private function save_fields($section) {
		try {
			$f_names = !empty( $_POST['f_name'] ) ? $_POST['f_name'] : array();	
			if(empty($f_names)){
				echo '<div class="error"><p> '. EPO_i18n::__t('Your changes were not saved due to no fields found.') .'</p></div>';
				return;
			}
			
			$f_order   = !empty( $_POST['f_order'] ) ? $_POST['f_order'] : array();	
			$f_deleted = !empty( $_POST['f_deleted'] ) ? $_POST['f_deleted'] : array();
			$f_enabled = !empty( $_POST['f_enabled'] ) ? $_POST['f_enabled'] : array();
						
			$sname = $section->get_property('name');
			$field_set = EPO_Utils_Section::get_fields($section);
						
			$max = max( array_map( 'absint', array_keys( $f_names ) ) );
			for($i = 0; $i <= $max; $i++) {
				$name = $f_names[$i];
				
				if(isset($field_set[$name])){
					if(isset($f_deleted[$i]) && $f_deleted[$i] == 1){
						unset($field_set[$name]);
						continue;
					}
					
					$field = $field_set[$name];
					$field->set_property('order', isset($f_order[$i]) ? trim(stripslashes($f_order[$i])) : 0);
					$field->set_property('enabled', isset($f_enabled[$i]) ? trim(stripslashes($f_enabled[$i])) : 0);
					
					$field_set[$name] = $field;
				}
			}
			$section->set_property('fields', $field_set);
			$section = EPO_Utils_Section::sort_fields($section);
			
			$result = $this->update_section($section);
			
			if ($result == true) {
				echo '<div class="updated"><p>'. EPO_i18n::__t('Your changes were saved.') .'</p></div>';
			} else {
				echo '<div class="error"><p>'. EPO_i18n::__t('Your changes were not saved due to an error (or you made none!).') .'</p></div>';
			}
		} catch (Exception $e) {
			echo '<div class="error"><p>'. EPO_i18n::__t('Your changes were not saved due to an error.') .'</p></div>';
		}
	}
	
	private function output_add_field_form_pp(){
		?>
        <div id="epo_new_field_form_pp" title="New Product Field" class="epo_popup_wrapper">
          <?php $this->output_popup_form_fields('new'); ?>
        </div>
        <?php
	}
		
	private function output_edit_field_form_pp(){		
		?>
        <div id="epo_edit_field_form_pp" title="Edit Product Field" class="epo_popup_wrapper">
          <?php $this->output_popup_form_fields('edit'); ?>
        </div>
        <?php
	}
	
	/*******************************************
	*-------- HTML FORM FRAGMENTS - START -----
	*******************************************/
	
	private function output_popup_form_fields($form_type){
		?>
		<form method="post" id="epo_<?php echo $form_type ?>_field_form" action="">
			<input type="hidden" name="f_action" value="<?php echo $form_type ?>" />
        	<div id="epo-tabs-container_<?php echo $form_type ?>">
                <ul class="thpladmin-tabs-menu">
                    <li class="first current"><a class="epo_tab_general_link" href="javascript:void(0)" 
                    onclick="epoOpenFormTab(this, 'epo-tab-general', '<?php echo $form_type ?>')">General Properties</a></li>
					<li><a class="epo_tab_styles_link" href="javascript:void(0)" 
                    onclick="epoOpenFormTab(this, 'epo-tab-styles', '<?php echo $form_type ?>')">Display Styles</a></li>
                    <li><a class="epo_tab_rules_link" href="javascript:void(0)" 
                    onclick="epoOpenFormTab(this, 'epo-tab-rules', '<?php echo $form_type ?>')">Display Rules</a></li>
                </ul>
                <div id="epo_field_editor_form_<?php echo $form_type ?>" class="thpladmin-tab epo_popup_wrapper">
                    <div id="epo-tab-general_<?php echo $form_type ?>" class="thpladmin-tab-content">
                    	<input type="hidden" name="i_name_old" value="" />
						<input type="hidden" name="i_options" value="" />
						<input type="hidden" name="i_rules" value="" />
						<input type="hidden" name="i_rules_ajax" value="" />
						
						<?php $this->render_field_form_fragment_general($form_type); ?>
                        <table class="epo_field_form_tab_general_placeholder" width="100%"></table>
                    </div>
					<div id="epo-tab-styles_<?php echo $form_type ?>" class="thpladmin-tab-content thpladmin-tab-content-styles">
                    	<table class="epo_field_form_tab_styles_placeholder" width="100%" style="margin-top: 10px;">
                    	<?php 
						$this->render_field_form_fragment_styles(); 
						?>
                        </table>
                    </div>
                    <div id="epo-tab-rules_<?php echo $form_type ?>" class="thpladmin-tab-content">
                    	<table class="epo_field_form_tab_rules_placeholder" width="100%" style="margin-top: 10px;">
                    	<?php 
						$this->render_field_form_fragment_rules(); 
						$this->render_field_form_fragment_rules_ajax();
						?>
                        </table>
                    </div>
                </div>
        	</div>
        </form>
        <?php
	}	
	
	private function output_popup_form_field_fragments(){
		$this->render_form_field_inputtext();
		$this->render_form_field_hidden();
		$this->render_form_field_password();
		$this->render_form_field_number();
		$this->render_form_field_tel();	
		$this->render_form_field_textarea();
		$this->render_form_field_select();
		$this->render_form_field_multiselect();		
		$this->render_form_field_radio();
		$this->render_form_field_checkbox();
		$this->render_form_field_checkboxgroup();
		$this->render_form_field_datepicker();
		$this->render_form_field_timepicker();
		$this->render_form_field_file();		
		$this->render_form_field_heading();
		$this->render_form_field_label();
		$this->render_form_field_default();
		
		$this->render_field_form_fragment_product_list();
		$this->render_field_form_fragment_category_list();
		$this->render_field_form_fragment_user_role_list();
		$this->render_field_form_fragment_fields_wrapper();
	}
	
	private function render_form_field_inputtext(){
		?>
        <table id="epo_field_form_id_inputtext" class="epo_field_form_table" width="100%" style="display:none;">
			<tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['validate'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['maxlength'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
        </table>
        <?php   
	}
	
	private function render_form_field_hidden(){
		?>
        <table id="epo_field_form_id_hidden" class="epo_field_form_table" width="100%" style="display:none;">
			<tr>
            	<?php
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_R);
				?>
            </tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
        </table>
        <?php   
	}
	
	private function render_form_field_password(){
		?>
        <table id="epo_field_form_id_password" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['placeholder'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
			<tr>
            	<?php
				$this->render_form_field_element($this->field_props['maxlength'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['validate'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
        </table>
        <?php   
	}

	private function render_form_field_number(){
		?>
        <table id="epo_field_form_id_number" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
        </table>
        <?php   
	}

	private function render_form_field_tel(){
		?>
        <table id="epo_field_form_id_tel" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['validate'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['maxlength'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
        </table>
        <?php   
	}
	
	private function render_form_field_textarea(){
		?>
        <table id="epo_field_form_id_textarea" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['maxlength'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['cols'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['rows'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
        </table>
        <?php   
	}
	
	private function render_form_field_select(){
		?>
        <table id="epo_field_form_id_select" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_R);
				?>
            </tr>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <?php $this->render_field_form_fragment_options(); ?>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}
	
	private function render_form_field_multiselect(){
		$field_props_maxlength = $this->field_props['maxlength'];
		$field_props_maxlength['label'] = 'Max. Selections';
		$field_props_maxlength['hint_text'] = 'The maximum number of options that can be selected';
		?>
        <table id="epo_field_form_id_multiselect" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($field_props_maxlength, $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <?php $this->render_field_form_fragment_options(); ?>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}
	
	private function render_form_field_radio(){
		?>
        <table id="epo_field_form_id_radio" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php $this->render_field_form_fragment_options(); ?>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}
	
	private function render_form_field_checkbox(){
		?>
        <table id="epo_field_form_id_checkbox" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<tr>
            	<?php
				$value_props = $this->field_props['value'];
				$value_props['label'] = 'Value';
		
				$this->render_form_field_element($value_props, $this->cell_props_L);
				$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('checkbox');
				$this->render_field_form_fragment_h_spacing(); 
			?>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['checked'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}
	
	private function render_form_field_checkboxgroup(){
		?>
        <table id="epo_field_form_id_checkboxgroup" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php $this->render_field_form_fragment_options(); ?>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}
	
	private function render_form_field_datepicker(){
		?>
        <table id="epo_field_form_id_datepicker" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['placeholder'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('datepicker');
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
            <?php 
				//$this->render_field_form_fragment_h_spacing(); 
				$this->render_form_fragment_h_separator();
				$this->render_field_form_fragment_datepicker();
				$this->render_field_form_fragment_h_spacing(); 
			?>   
        </table>
        <?php   
	}
	
	private function render_form_field_timepicker(){
		?>
        <table id="epo_field_form_id_timepicker" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				 $this->render_field_form_fragment_price('timepicker');
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
            <?php 
				$this->render_field_form_fragment_h_spacing(); 
				$this->render_form_fragment_h_separator();
				$this->render_field_form_fragment_timepicker();
				$this->render_field_form_fragment_h_spacing();
			?>   
        </table>
        <?php   
	}
	
	private function render_form_field_file(){
		?>
        <table id="epo_field_form_id_file" class="epo_field_form_table" width="100%" style="display:none;">
			<tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_props['maxsize'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_props['accept'], $this->cell_props_R);
				?>
            </tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
        </table>
        <?php   
	}
	
	private function render_form_field_heading(){
		$title_props = $this->field_props['title'];
		$title_props['required'] = true;
		?>
        <table id="epo_field_form_id_heading" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($title_props, $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<?php 
				$this->render_field_form_fragment_title(false);
			?>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
        </table>
        <?php   
	}
	
	private function render_form_field_label(){
		$title_props = $this->field_props['title'];
		$title_props['required'] = true;
		?>
        <table id="epo_field_form_id_label" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($title_props, $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<?php 
				$this->render_field_form_fragment_title(false);
			?>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
        </table>
        <?php   
	}
	
	private function render_form_field_default(){
		?>
        <table id="epo_field_form_id_default" class="epo_field_form_table" width="100%" style="display:none;">
            <tr>      
				<?php          
				$this->render_form_field_element($this->field_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['subtitle'], $this->cell_props_R);
				?>
			</tr>
			<tr>
            	<?php
            	$this->render_form_field_element($this->field_props['value'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['validate'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['input_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
        </table>
        <?php   
	}
	
	private function render_field_form_fragment_general($form_type, $input_field = true){
		$field_types = $this->get_field_types();
		
		$field_name_label = $input_field ? EPO_i18n::__t('Name') : EPO_i18n::__t('ID');
		?>
        <table width="100%">
            <tr>                
                <td colspan="6" class="err_msgs"></td>
            </tr>
			<?php if($form_type === 'edit'){ ?> 
            <tr>
            	<td colspan="6">
                    <input type="hidden" name="i_rowid" value="" />
                    <input type="hidden" name="i_original_type" value="" />
                </td>
            </tr>    
            <?php } ?>  
                	         
            <tr>  
            <?php 
				$this->render_form_field_element($this->field_props['name'], $this->cell_props_L);
				$this->render_form_field_element($this->field_props['type'], $this->cell_props_R); 
			?>         
            </tr>
        </table>  
        <?php
	}
	
	private function render_field_form_fragment_styles(){
		$this->render_field_form_fragment_title(true);
	}
	
    private function render_field_form_fragment_options(){
		?>
		<tr>
			<td width="13%" valign="top"><?php EPO_i18n::_et('Options'); ?></td>
			<?php $this->render_form_fragment_tooltip(); ?>
			<td colspan="4">
				<table border="0" cellpadding="0" cellspacing="0" class="epo-option-list thpladmin-dynamic-row-table"><tbody>
					<tr>
						<td style="width:190px;"><input type="text" name="i_options_key[]" placeholder="Option Value" style="width:180px;"/></td>
						<td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="Option Text" style="width:180px;"/></td>
						<td style="width:80px;"><input type="text" name="i_options_price[]" placeholder="Price" style="width:70px;"/></td>
						<td style="width:130px;">    
							<select name="i_options_price_type[]" style="width:120px;">
								<option selected="selected" value="">Normal</option>
								<option value="percentage">Percentage</option>
							</select>
						</td>
						<td class="action-cell"><a href="javascript:void(0)" onclick="epoAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>
						<td class="action-cell"><a href="javascript:void(0)" onclick="epoRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a></td>
						<td class="action-cell sort ui-sortable-handle"></td>
					</tr>
				</tbody></table>            	
			</td>
		</tr>
        <?php
	}
			
	private function render_field_form_fragment_title($show_position = true){
		?>
        <tr>   
        	<?php          
        	$this->render_form_field_element($this->field_props['title_type'], $this->cell_props_L);
			$this->render_form_field_element($this->field_props['title_color'], $this->cell_props_CP);
			?>
        </tr>
        <tr>  
        	<?php          
        	$this->render_form_field_element($this->field_props['title_class'], $this->cell_props_L);
			if($show_position){
				$this->render_form_field_element($this->field_props['title_position'], $this->cell_props_R);
			}else{
				$this->render_form_field_blank();
			}
			?>            
        </tr>
        <?php
		$this->render_field_form_fragment_h_spacing(10);
		?>
        <tr class="epo_subtitle_row">    
        	<?php          
			$this->render_form_field_element($this->field_props['subtitle_type'], $this->cell_props_R);
			$this->render_form_field_element($this->field_props['subtitle_color'], $this->cell_props_CP);
			?>
        </tr>  
        <tr class="epo_subtitle_row"> 
        	<?php          
			$this->render_form_field_element($this->field_props['subtitle_class'], $this->cell_props_R);
			$this->render_form_field_blank();
			?>
        </tr>
        <?php
	}
	
	private function render_field_form_fragment_show_subtitle_checkbox(){
		?>                              
        <td colspan="3">
        <input type="checkbox" id="a_fshowsubtitle" name="i_showsubtitle" checked="checked" onchange="epo_show_subtitle_options(this)"/>
        <label for="a_fshowsubtitle" style="margin-right: 40px;" ><?php EPO_i18n::_et('Add subtitle'); ?></label>
        </td>
        <?php
	}
	
	private function render_field_form_fragment_datepicker(){
		?>
        <tr>     
        	<?php          
			$this->render_form_field_element($this->field_props['date_format'], $this->cell_props_L);
			$this->render_form_field_element($this->field_props['default_date'], $this->cell_props_R);
			?> 
        </tr>  
        <tr>     
        	<?php          
        	$this->render_form_field_element($this->field_props['min_date'], $this->cell_props_L);
			$this->render_form_field_element($this->field_props['max_date'], $this->cell_props_R);
			?> 
        </tr>  
        <tr> 
        	<?php          
        	$this->render_form_field_element($this->field_props['year_range'], $this->cell_props_L);
			$this->render_form_field_element($this->field_props['number_of_months'], $this->cell_props_R);
			?> 
        </tr>
		<tr>  
		<?php
            $this->render_form_field_element($this->field_props['disabled_days'], $this->cell_props_L);
            $this->render_form_field_element($this->field_props['disabled_dates'], $this->cell_props_R);
        ?>
        </tr>
        <?php
    }
	
	private function render_field_form_fragment_timepicker(){
		?>
        <tr>       
        	<?php          
        	$this->render_form_field_element($this->field_props['min_time'], $this->cell_props_L);
			$this->render_form_field_element($this->field_props['max_time'], $this->cell_props_R);
			?> 
        </tr>  
        <tr>       
        	<?php          
        	$this->render_form_field_element($this->field_props['time_step'], $this->cell_props_L);
			$this->render_form_field_element($this->field_props['time_format'], $this->cell_props_R);
			?> 
        </tr>
        <?php
    }
	
	private function render_field_form_fragment_is_price_field(){
		?>
        <input type="checkbox" id="a_is_price_field" name="i_is_price_field" value="1" onchange="epo_show_price_fields(this)"/>
        <label for="a_is_price_field"><?php EPO_i18n::_et('Is Price Field'); ?></label>
        <?php
	}
	
	private function render_field_form_fragment_price($type = false){
		?>
        <tr>
            <td width="13%"><?php EPO_i18n::_et('Price'); ?></td>
            <?php $this->render_form_fragment_tooltip(false); ?>
            <td width="34%">
            	<input type="text" name="i_price" placeholder="Price" style="width:250px;" class="thpladmin-price-field"/>
                <label class="thpladmin-dynamic-price-field" style="display:none">per</label>
                <input type="text" name="i_price_unit" placeholder="Unit" style="width:80px; display:none" class="thpladmin-dynamic-price-field"/>
                <label class="thpladmin-dynamic-price-field" style="display:none">unit</label>
            </td>
			
		<?php 
			$field_props = $this->field_props['price_type'];
			$options = isset($field_props['options']) ? $field_props['options'] : array();
			
			if($type === 'datepicker' || $type === 'timepicker' || $type === 'checkbox'){
				unset($options['custom']);
				unset($options['dynamic']);
				unset($options['dynamic-excl-base-price']);
			}
			
			$field_props['options'] = $options;
			$this->render_form_field_element($field_props, $this->cell_props_R); 
		?>
        </tr>  
		<tr style="display:none" class="thpladmin-dynamic-price-field">        
            <?php          
        	$this->render_form_field_element($this->field_props['price_min_unit'], $this->cell_props_L);
			$this->render_form_field_blank();
			?> 
		</tr>
        <?php
	}
	
	private function render_field_form_fragment_rules(){
		?>
        <tr>
        	<td style="padding-left: 12px;">
                <select name="i_rules_action" style="width:80px;">
                    <option value="show">Show</option>
                    <option value="hide">Hide</option>
                </select>
                field if all below conditions are met.
            </td>
        </tr>
        <tr>                
            <td colspan="6">
            	<table class="epo_conditional_rules" width="100%"><tbody>
                    <tr class="epo_rule_set_row">                
                        <td>
                            <table class="epo_rule_set" width="100%"><tbody>
                                <tr class="epo_rule_row">
                                    <td>
                                        <table class="epo_rule" width="100%" style=""><tbody>
                                            <tr class="epo_condition_set_row">
                                                <td>
                                                    <table class="epo_condition_set" width="100%" style=""><tbody>
                                                        <tr class="epo_condition">
                                                            <td width="25%">
                                                                <select name="i_rule_operand_type" style="width:200px;" onchange="epoRuleOperandTypeChangeListner(this)">
                                                                    <option value=""></option>
                                                                    <option value="product">Product</option>
                                                                    <option value="category">Category</option>
																	<option value="user_role">User role</option>
                                                                </select>
                                                            </td>
                                                            <td width="25%">
                                                                <select name="i_rule_operator" style="width:200px;">
                                                                    <option value=""></option>
                                                                    <option value="equals">Equals to/ In</option>
                                                                    <option value="not_equals">Not Equals to/ Not in</option>
                                                                </select>
                                                            </td>
                                                            <td width="25%" class="thpladmin_rule_operand"><input type="text" name="i_rule_operand" style="width:200px;"/></td>
                                                            <td>
                                                                <a href="javascript:void(0)" class="thpl_logic_link" onclick="epoAddNewConditionRow(this, 1)" title="">AND</a>
                                                                <a href="javascript:void(0)" class="thpl_logic_link" onclick="epoAddNewConditionRow(this, 2)" title="">OR</a>
                                                                <a href="javascript:void(0)" class="thpl_delete_icon dashicons dashicons-no" onclick="epoRemoveRuleRow(this)" title="Remove"></a>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>            	
                        </td>            
                    </tr> 
        		</tbody></table>
        	</td>
        </tr>
        <?php
	}
	
	private function render_field_form_fragment_rules_ajax(){
		?>
        <tr><td style="border-top: 1px dashed #e6e6e6;">&nbsp;</td></tr>
        <tr>
        	<td style="padding-left: 12px;">
                <select name="i_rules_action_ajax" style="width:80px;">
                    <option value="show">Show</option>
                    <option value="hide">Hide</option>
                </select>
                field if all below conditions are met.
            </td>
        </tr>
        <tr>                
            <td>
            	<table class="epo_conditional_rules_ajax" width="100%"><tbody>
                    <tr class="epo_rule_set_row">                
                        <td>
                            <table class="epo_rule_set" width="100%"><tbody>
                                <tr class="epo_rule_row">
                                    <td>
                                        <table class="epo_rule" width="100%" style=""><tbody>
                                            <tr class="epo_condition_set_row">
                                                <td>
                                                    <table class="epo_condition_set" width="100%" style=""><tbody>
                                                        <tr class="epo_condition">
                                                        	<td width="25%" class="thpladmin_rule_operand">
                                                            	<input type="hidden" name="i_rule_operand_type" value="field" />
                                                            	<?php $this->render_field_form_fragment_fields_select(); ?>
                                                            </td>
                                                            <td width="25%">
                                                                <select name="i_rule_operator" style="width:200px;" onchange="epoRuleOperatorChangeListnerAjax(this)">
                                                                    <option value="">Please select an operator</option>
                                                                    <option value="empty">Is empty</option>
                                                                    <option value="not_empty">Is not empty</option>
                                                                    <option value="value_eq">Value equals to</option>
                                                                    <option value="value_ne">Value not equals to</option>
                                                                    <option value="value_gt">Value greater than</option>
                                                                    <option value="value_le">Value less than</option>
																	<option value="date_eq">Date equals to</option>
                                                                    <option value="date_ne">Date not equals to</option>
                                                                    <option value="date_gt">Date after</option>
                                                                    <option value="date_lt">Date before</option>
																	<option value="day_eq">Day equals to</option>
                                                                    <option value="day_ne">Day not equals to</option>
                                                                    <option value="checked">Is checked</option>
                                                                    <option value="not_checked">Is not checked</option>
                                                                </select>
                                                            </td>
                                                            <td width="25%"><input type="text" name="i_rule_value" style="width:200px;"/></td>
                                                            <td>
                                                              <a href="javascript:void(0)" class="thpl_logic_link" onclick="epoAddNewConditionRowAjax(this, 1)" title="">AND</a>
                                                              <a href="javascript:void(0)" class="thpl_logic_link" onclick="epoAddNewConditionRowAjax(this, 2)" title="">OR</a>
                                                              <a href="javascript:void(0)" class="thpl_delete_icon dashicons dashicons-no" onclick="epoRemoveRuleRowAjax(this)" title="Remove"></a>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>            	
                        </td>            
                    </tr> 
        		</tbody></table>
        	</td>
        </tr>
        <?php
	}
	
	private function render_field_form_fragment_product_list(){
		$products = apply_filters( "epo_load_products", array() );
		array_unshift( $products , array( "id" => "-1", "title" => "All Products" ));
		?>
        <div id="epo_product_select" style="display:none;">
        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select products" class="epo-enhanced-multi-select" style="width:200px;" value="">
			<?php 	
                foreach($products as $product){
                    echo '<option value="'. $product["id"] .'" >'. $product["title"] .'</option>';
                }
            ?>
        </select>
        </div>
        <?php
	}
	
	private function render_field_form_fragment_category_list(){		
		$categories = apply_filters( "epo_load_products_cat", array() );
		array_unshift( $categories , array( "id" => "-1", "title" => "All Categories" ));
		?>
        <div id="epo_product_cat_select" style="display:none;">
        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select categories" class="epo-enhanced-multi-select" style="width:200px;" value="">
			<?php 	
                foreach($categories as $category){
                    echo '<option value="'. $category["id"] .'" >'. $category["title"] .'</option>';
                }
            ?>
        </select>
        </div>
        <?php
	}
	
	private function render_field_form_fragment_user_role_list(){		
		$user_roles = apply_filters( "epo_load_user_roles", array() );
		//array_unshift( $user_roles , array( "id" => "-1", "title" => "All User Roles" ));
		?>
        <div id="epo_user_role_select" style="display:none;">
        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select user roles" class="epo-enhanced-multi-select" style="width:200px;" value="">
			<?php 	
                foreach($user_roles as $role){
                    echo '<option value="'. $role["id"] .'" >'. $role["title"] .'</option>';
                }
            ?>
        </select>
        </div>
        <?php
	}
	
	private function render_field_form_fragment_fields_wrapper(){		
		?>
        <div id="epo_product_fields_select" style="display:none;">
			<?php $this->render_field_form_fragment_fields_select(); ?>
        </div>
        <?php
	}
	
	private function render_field_form_fragment_fields_select(){		
		$sections = EPO_Admin_Utils::get_sections();
		$other_fields = apply_filters('epo_extra_fields_for_diaplay_rules', array('quantity' => 'Product Quantity'));
		$show_name = apply_filters('epo_show_filed_name_for_field_list_in_conditions_tab', true);	
		?>
        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select field(s)" class="epo-enhanced-multi-select" style="width:200px;" value="">
			<option value="product_variation"><b>  Product variation</b></option>
			<?php 
			if($sections && is_array($sections)){	
				foreach($sections as $sname => $section){	
					if($section && EPO_Utils_Section::is_valid_section($section)){
						$fields = $section->get_property('fields');
						if($fields && is_array($fields)){	
							echo '<optgroup label="'. $section->get_property('title') .'">';
							foreach($fields as $name => $field){
								if($field && EPO_Utils_Field::is_enabled($field)){
									$label = $field->get_property('title');
									$label = empty($label) ? $name : $label;
									if($show_name){
										$label .= ' ('. $name .')';
									}
									echo '<option value="'. $name .'" >'. $label .'</option>';
								}
							}
							echo '</optgroup>';
						}
					}
				}
				echo '<optgroup label="Other Fields">';
				foreach($other_fields as $name => $label){
					if($name && $label){
						echo '<option value="'. $name .'" >'. EPO_i18n::__t($label) .'</option>';
					}
				}
				echo '</optgroup>';
			}
            ?>
        </select>
        <?php
	}
	/*******************************************
 	*-------- HTML FORM FRAGMENTS - END -------
 	*******************************************/
}

endif;