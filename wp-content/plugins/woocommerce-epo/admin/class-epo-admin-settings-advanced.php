<?php
/**
 * The admin advanced settings page functionality of the plugin.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('EPO_Admin_Settings_Advanced')):

class EPO_Admin_Settings_Advanced extends EPO_Admin_Settings{
	protected static $_instance = null;
	
	private $settings_fields = NULL;
	private $cell_props_L = array();
	private $cell_props_R = array();
	private $cell_props_CB = array();
	private $cell_props_TA = array();
	
	public function __construct() {
		parent::__construct('advanced_settings');
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
			'label_cell_props' => 'class="titledesc" scope="row" style="width: 20%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '250px', 
			'label_cell_th' => true 
		);
		$this->cell_props_R = array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		$this->cell_props_CB = array( 'cell_props' => 'colspan="3"', 'render_input_cell' => true );
		$this->cell_props_TA = array( 
			'label_cell_props' => 'class="titledesc" scope="row" style="width: 20%; vertical-align:top"', 
			'rows' => 10, 
		);
		
		$this->settings_fields = $this->get_advanced_settings_fields();
	}
	
	public function get_advanced_settings_fields(){
		return array(
			'section_custom_validators' => array('title'=>'Custom validators', 'type'=>'separator', 'colspan'=>'3'),
			'custom_validators' => array(
				'name'=>'custom_validators', 'label'=>'Custom validators', 'type'=>'dynamic_options'
			),
			'confirm_validators' => array(
				'name'=>'confirm_validators', 'label'=>'Confirm field validators', 'type'=>'dynamic_options', 'prefix'=>'cnf'
			),
			'section_other_settings' => array('title'=>'Other Settings', 'type'=>'separator', 'colspan'=>'3'),
			'disable_select2_for_select_fields' => array(
				'name'=>'disable_select2_for_select_fields', 'label'=>'Disable "Enhanced Select(Select2)" for select fields.', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>0
			)
		);
	}
	
	public function render_page(){
		$this->render_tabs();
		$this->render_content();
		$this->render_import_export_settings();
	}
		
	public function save_advanced_settings($settings){
		$result = update_option(EPO_Utils::OPTION_KEY_ADVANCED_SETTINGS, $settings);
		return $result;
	}
	
	private function reset_settings(){
		delete_option(EPO_Utils::OPTION_KEY_ADVANCED_SETTINGS);
		echo '<div class="updated"><p>'. EPO_i18n::__t('Settings successfully reset') .'</p></div>';	
	}
	
	private function save_settings(){
		$settings = array();
		
		foreach( $this->settings_fields as $name => $field ) {
			if($field['type'] === 'dynamic_options'){
				$prefix = isset($field['prefix']) ? 'i_'.$field['prefix'].'_' : 'i_';
				
				$vnames = !empty( $_POST[$prefix.'validator_name'] ) ? $_POST[$prefix.'validator_name'] : array();
				$vlabels = !empty( $_POST[$prefix.'validator_label'] ) ? $_POST[$prefix.'validator_label'] : array();
				$vpatterns = !empty( $_POST[$prefix.'validator_pattern'] ) ? $_POST[$prefix.'validator_pattern'] : array();
				$vmessages = !empty( $_POST[$prefix.'validator_message'] ) ? $_POST[$prefix.'validator_message'] : array();
				
				$validators = array();
				$max = max( array_map( 'absint', array_keys( $vnames ) ) );
				for($i = 0; $i <= $max; $i++) {
					$vname = isset($vnames[$i]) ? stripslashes(trim($vnames[$i])) : '';
					$vlabel = isset($vlabels[$i]) ? stripslashes(trim($vlabels[$i])) : '';
					$vpattern = isset($vpatterns[$i]) ? stripslashes(trim($vpatterns[$i])) : '';
					$vmessage = isset($vmessages[$i]) ? stripslashes(trim($vmessages[$i])) : '';
					
					if(!empty($vname) && !empty($vpattern)){
						$vlabel = empty($vlabel) ? $vname : $vlabel;
						
						$validator = array();
						$validator['name'] = $vname;
						$validator['label'] = $vlabel;
						$validator['pattern'] = $vpattern;
						$validator['message'] = $vmessage;
						
						$validators[$vname] = $validator;
					}
				}
				$settings[$name] = $validators;
			}else{
				$value = '';
				
				if($field['type'] === 'checkbox'){
					$value = !empty( $_POST['i_'.$name] ) ? $_POST['i_'.$name] : '';
				}else if($field['type'] === 'multiselect_grouped'){
					$value = !empty( $_POST['i_'.$name] ) ? $_POST['i_'.$name] : '';
					$value = is_array($value) ? implode(',', $value) : $value;
				}else if($field['type'] === 'text' || $field['type'] === 'textarea'){
					$value = !empty( $_POST['i_'.$name] ) ? $_POST['i_'.$name] : '';
					$value = !empty($value) ? stripslashes(trim($value)) : '';
				}else{
					$value = !empty( $_POST['i_'.$name] ) ? $_POST['i_'.$name] : '';
				}
				
				$settings[$name] = $value;
			}
		}
				
		$result = $this->save_advanced_settings($settings);
		if ($result == true) {
			echo '<div class="updated"><p>'. EPO_i18n::__t('Your changes were saved.') .'</p></div>';
		} else {
			echo '<div class="error"><p>'. EPO_i18n::__t('Your changes were not saved due to an error (or you made none!).') .'</p></div>';
		}	
	}
	
	private function render_content(){
		if(isset($_POST['reset_settings']))
			$this->reset_settings();	
			
		if(isset($_POST['save_settings']))
			$this->save_settings();
			
		$settings = EPO_Utils::get_advanced_settings();
		?>            
        <div style="padding-left: 30px;">               
		    <form id="advanced_settings_form" method="post" action="">
                <table class="form-table thpladmin-form-table">
                    <tbody>
                    <?php 
					foreach( $this->settings_fields as $name => $field ) { 
						if($field['type'] === 'separator'){
							$this->render_form_section_separator($field);
						}else {
					?>
                        <tr valign="top">
                            <?php 
								if($field['type'] === 'dynamic_options'){
									$this->render_validator_settings($settings, $field);
									
								}else{
									if(is_array($settings) && isset($settings[$name])){
										if($field['type'] === 'checkbox'){
											if($field['value'] === $settings[$name]){
												$field['checked'] = 1;
											}
										}else{
											$field['value'] = $settings[$name];
										}
									}
									
									if($field['type'] === 'checkbox'){
										$this->render_form_field_element($field, $this->cell_props_CB, false);
									}else if($field['type'] === 'multiselect' || $field['type'] === 'textarea'){
										$this->render_form_field_element($field, $this->cell_props_L);
									}else{
										$this->render_form_field_element($field, $this->cell_props_L);
									} 
								}
							?>
                        </tr>
                    <?php 
						}
					} 
					?>
                    </tbody>
                </table> 
                <p class="submit">
					<input type="submit" name="save_settings" class="button-primary" value="Save changes">
                    <input type="submit" name="reset_settings" class="button" value="Reset to default" 
					onclick="return confirm('Are you sure you want to reset to default settings? all your changes will be deleted.');">
            	</p>
            </form>
    	</div>       
    	<?php
	}
	
	public function render_validator_settings($settings, $field){
		$name = is_array($field) && isset($field['name']) ? $field['name'] : false;
		if($name){
			$custom_validators = is_array($settings) && isset($settings[$name]) ? $settings[$name] : array();
		
			?>
			<td><?php echo $field['label']; ?></td>
			<?php $this->render_form_fragment_tooltip(false); ?>
			<td>
				<table border="0" cellpadding="0" cellspacing="0" class="epo-validations-list thpladmin-dynamic-row-table"><tbody>
					<?php
					if(is_array($custom_validators) && !empty($custom_validators)){
						foreach( $custom_validators as $vname => $validator ) {
							$this->render_validator_row($settings, $field, $validator);
						}
					}else{
						$this->render_validator_row($settings, $field, false);
					}
					?>
				</tbody></table>            	
			</td>
			<?php
		}
	}
	
	public function render_validator_row($settings, $field, $validator = false){
		$vname = ''; $vlabel = ''; $vpattern = ''; $vmessage = '';
		$prefix = isset($field['prefix']) ? 'i_'.$field['prefix'].'_' : 'i_';
		$prefix_index = 0;
		
		$pattern_ph = 'Validator Pattern';
		
		if(isset($field['prefix']) && $field['prefix'] === 'cnf'){
			$prefix_index = 1;
			$pattern_ph = 'Field Name';
		}
		
		if($validator && is_array($validator)){
			$vname = isset($validator['name']) ? $validator['name'] : '';
			$vlabel = isset($validator['label']) ? $validator['label'] : '';
			$vpattern = isset($validator['pattern']) ? $validator['pattern'] : '';
			$vmessage = isset($validator['message']) ? $validator['message'] : '';
		}
		
		?>
		<tr>
			<td style="width:190px;">
				<input type="text" name="<?php echo $prefix ?>validator_name[]" value="<?php echo $vname; ?>" placeholder="Validator Name" style="width:180px;"/>
			</td>
			<td style="width:190px;">
				<input type="text" name="<?php echo $prefix ?>validator_label[]" value="<?php echo $vlabel; ?>" placeholder="Validator Label" style="width:180px;"/>
			</td>
			<td style="width:190px;">
				<input type="text" name="<?php echo $prefix ?>validator_pattern[]" value="<?php echo $vpattern; ?>" placeholder="<?php echo $pattern_ph; ?>" style="width:180px;"/>
			</td>
			<td style="width:190px;">
				<input type="text" name="<?php echo $prefix ?>validator_message[]" value="<?php echo $vmessage; ?>" placeholder="Validator Message" style="width:180px;"/>
			</td>
			<td class="action-cell">
				<a href="javascript:void(0)" onclick="epoAddNewValidatorRow(this, <?php echo $prefix_index; ?>)" class="dashicons dashicons-plus" title="Add new validator"></a>
			</td>
			<td class="action-cell">
				<a href="javascript:void(0)" onclick="epoRemoveValidatorRow(this, <?php echo $prefix_index; ?>)" class="dashicons dashicons-no-alt" title="Remove validator"></a>
			</td>
		</tr>
		<?php
	}
	
    /************************************************
	 *-------- IMPORT & EXPORT SETTINGS - START -----
	 ************************************************/
	public function prepare_plugin_settings(){
		$settings_sections = get_option(EPO_Utils::OPTION_KEY_CUSTOM_SECTIONS);
		$settings_hook_map = get_option(EPO_Utils::OPTION_KEY_SECTION_HOOK_MAP);
		$settings_name_title_map = get_option(EPO_Utils::OPTION_KEY_NAME_TITLE_MAP);
		$settings_advanced = get_option(EPO_Utils::OPTION_KEY_ADVANCED_SETTINGS);

		$plugin_settings = array(
			'OPTION_KEY_CUSTOM_SECTIONS' => $settings_sections,
			'OPTION_KEY_SECTION_HOOK_MAP' => $settings_hook_map,
			'OPTION_KEY_NAME_TITLE_MAP' => $settings_name_title_map,
			'OPTION_KEY_ADVANCED_SETTINGS' => $settings_advanced,
		);

		return base64_encode(serialize($plugin_settings));
	}
	
	public function render_import_export_settings(){
		if(isset($_POST['save_plugin_settings'])) 
			$result = $this->save_plugin_settings(); 
		
		if(isset($_POST['import_settings'])){			   
		} 
		
		$plugin_settings = $this->prepare_plugin_settings();
		if(isset($_POST['export_settings']))
			echo $this->export_settings($plugin_settings);   
		
		$imp_exp_fields = array(
			'section_import_export' => array('title'=>'Backup and Import Settings', 'type'=>'separator', 'colspan'=>'3'),
			'settings_data' => array(
				'name'=>'settings_data', 'label'=>'Plugin Settings Data', 'type'=>'textarea', 'value' => $plugin_settings,
				'sub_label'=>'You can tranfer the saved settings data between different installs by copying the text inside the text box. To import data from another install, replace the data in the text box with the one from another install and click "Import Settings".',
				//'sub_label'=>'You can insert the settings data to the textarea field to import the settings from one site to another website.'
			),
		);
		?>
		<div style="padding-left: 30px;">               
		    <form id="import_export_settings_form" method="post" action="" class="clear">
                <table class="form-table thpladmin-form-table">
                    <tbody>
                    <?php 
					foreach( $imp_exp_fields as $name => $field ) { 
						if($field['type'] === 'separator'){
							$this->render_form_section_separator($field);
						}else {
							?>
							<tr valign="top">
								<?php  
								if($field['type'] === 'checkbox'){
									$this->render_form_field_element($field, $this->cell_props_CB, false);
								}else if($field['type'] === 'multiselect'){
									$this->render_form_field_element($field, $cell_props);
								}else if($field['type'] === 'textarea'){
									$this->render_form_field_element($field, $this->cell_props_TA);
								}else{
									$this->render_form_field_element($field, $cell_props);
								}
								?>
							</tr>
                    		<?php 
						}
					} 
					?>
                    </tbody>
					<tfoot>
						<tr valign="top">
							<td colspan="2">&nbsp;</td>
							<td class="submit">
								<input type="submit" name="save_plugin_settings" class="button-primary" value="Import Settings">
								<!--<input type="submit" name="import_settings" class="button" value="Import Settings(CSV)">-->
								<!--<input type="submit" name="export_settings" class="button" value="Export Settings(CSV)">-->
							</td>
						</tr>
					</tfoot>
                </table> 
            </form>
    	</div> 
		<?php
	}
		
	public function save_plugin_settings(){		
		if(isset($_POST['i_settings_data']) && !empty($_POST['i_settings_data'])) {
			$settings_data_encoded = $_POST['i_settings_data'];   
			$settings = unserialize(base64_decode($settings_data_encoded)); 
			
			if($settings){	
				foreach($settings as $key => $value){	
					if($key === 'OPTION_KEY_CUSTOM_SECTIONS'){
						$result = update_option(EPO_Utils::OPTION_KEY_CUSTOM_SECTIONS, $value);	
					}
					if($key === 'OPTION_KEY_SECTION_HOOK_MAP'){ 
						$result1 = update_option(EPO_Utils::OPTION_KEY_SECTION_HOOK_MAP, $value);  
					}
					if($key === 'OPTION_KEY_NAME_TITLE_MAP'){ 
						$result2 = update_option(EPO_Utils::OPTION_KEY_NAME_TITLE_MAP, $value); 
					}
					if($key === 'OPTION_KEY_ADVANCED_SETTINGS'){ 
						$result3 = $this->save_advanced_settings($value);  
					}						  
				}					
			}		
									
			if($result || $result1 || $result2 || $result3){
				echo '<div class="updated"><p>'. EPO_i18n::__t('Your Settings Updated.') .'</p></div>';
				return true; 
			}else{
				echo '<div class="error"><p>'. EPO_i18n::__t('Your changes were not saved due to an error (or you made none!).') .'</p></div>';
				return false;
			}	 			
		}
	}

	public function export_settings($settings){
		ob_clean();
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=\"wcfe-checkout-field-editor-settings.csv\";" );
		echo $settings;	
        ob_flush();     
     	exit; 		
	}
	
	public function import_settings(){
	
	}
    /**********************************************
	 *-------- IMPORT & EXPORT SETTINGS - END -----
	 **********************************************/
}

endif;