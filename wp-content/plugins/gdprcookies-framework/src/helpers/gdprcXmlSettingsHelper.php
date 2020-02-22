<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcXmlSettingsHelper class - helper class for XML setting tasks 
 *  
 * @author $Author: NULL $
 * @version $Id: gdprcXmlSettingsHelper.php 126 2017-03-22 10:54:27Z NULL $
 */
class gdprcXmlSettingsHelper {
		
	
	/**
	 * Check if current field is a WordPress specific field
	 * 
	 * @param SimpleXMLElement $field
	 * @return boolean
	 */
	public static function isWpField( SimpleXMLElement $field )
	{
		return( false === strpos((string)$field->elem, 'wp') ) ? false : true;
	}
	
	
	/**
	 * Check if current settingsfield is a formgroup
	 *
	 * Groups are indicated in the formfields array like 'group-0', 'group-1' etc.
	 * The purpose is to create setting sections within a settings tab
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1s
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isFormGroup( SimpleXMLElement $field )
	{
		return ( 'group' === $field->getName() );
	}
	
	/**
	 * Check if current settingsfield is a formgroup meta entry
	 *
	 * @param string $id
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isFormGroupMeta( SimpleXMLElement $field )
	{
		return ( 'group_title' === $field->getName() ||  'group_descr' === $field->getName() ||  'group_warning' === $field->getName() || 'group_notice' === $field->getName() ) ? true : false;
	}
	
	
	/**
	 * Check if current settingsfield is an input type text
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.2
	 *
	 * @return bool true if yes and false if no
	 */
	public static function isText( SimpleXMLElement $field )
	{
		return ( 'text' === (string)$field->elem );
	}
		
	
	/**
	 * Check if current settingsfield is a colorpicker
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isColorPicker( SimpleXMLElement $field )
	{
		return ( 'color' === (string)$field->elem || 'colorpicker' === (string)$field->elem ) ? true : false;
	}	
	
	/**
	 * Check if current settingsfield is a datePicker
	 * 
	 * @param SimpleXMLElement $field
	 * 
	 * @since 1.0
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isDatePicker( SimpleXMLElement $field )
	{
		return ( 'wpdate' === (string)$field->elem || 'wpdatepicker' === (string)$field->elem ) ? true : false;
	}	
	
	
	/**
	 * Check if current settingsfield is a <select>
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isSelect( SimpleXMLElement $field )
	{
		return ( 'select' === (string)$field->elem );
	}
	
	
	/**
	 * Check if current settingsfield is a checkbox
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.2
	 *
	 * @return bool true if yes and false if no
	 */
	public static function isCheckbox( SimpleXMLElement $field )
	{
		return ( 'checkbox' === (string)$field->elem );
	}
	
	
	/**
	 * Check if current settingsfield is a multi checkbox
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.1.8
	 *
	 * @return bool true if yes and false if no
	 */
	public static function isCheckboxmulti( SimpleXMLElement $field )
	{
		return ( 'checkboxmulti' === (string)$field->elem );
	}

	
	/**
	 * Check if current settingsfield is a multi options element
	 * 
	 * possible elements are:select, checkboxmulti
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.1.8
	 *
	 * @return bool true if yes and false if no
	 */
	public static function isMultiElement( SimpleXMLElement $field )
	{
		if( self::isSelect( $field ) )
			return true;
		elseif( self::isCheckboxmulti( $field ) )
			return true;
		else 
			return false;		
	}	
	
	
	/**
	 * Check if current settingsfield is a <textarea>
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isTextarea( SimpleXMLElement $field )
	{
		return ( 'textarea' === (string)$field->elem );
	}
	
	
	/**
	 * Check if current settingsfield is a WP <textarea>
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.2
	 *
	 * @return bool true if yes and false if no
	 */
	public static function isWpTextarea( SimpleXMLElement $field )
	{
		return ( 'wptextarea' === (string)$field->elem || 'wptextareabasic' === (string)$field->elem );
	}
	
	/**
	 * Check if current settingsfield is a WP Custom Post Type list
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.3
	 *
	 * @return bool true if yes and false if no
	 */
	public static function isWpPostTypeList( SimpleXMLElement $field )
	{
		return ( 'wpcpostlist' === (string)$field->elem || 'wpcpostlist_edit' === (string)$field->elem );
	}	
	
	/**
	 * Check if current settingsfield is an inline field
	 *
	 * E.a. {field} {another field}
	 *
	 * @param string $id
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isInline( SimpleXMLElement $field )
	{
		return ( 'inline' === $field->getName() );
	}
	
	/**
	 * Check if current settingsfield is an inline title
	 *
	 * @param string $id
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isInlineTitle( SimpleXMLElement $field )
	{
		return ( 'inline_title' === $field->getName() );
	}
	
	/**
	 * Check if current settingsfield is an inline description
	 *
	 * @param string $id
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isInlineDescr( SimpleXMLElement $field )
	{
		return ( 'inline_descr' === $field->getName() );
	}
	
	/**
	 * Check if current settingsfield is a external template
	 *
	 * @param SimpleXMLElement $field
	 * @param string $name the name of the template
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isTemplate( SimpleXMLElement $field )
	{	
		return ( 'true' === (string)$field->inner['template']) ? true : false;
	}
	
	
	/**
	 * Check if curren field is a disabled or hidden element
	 * 
	 * @param SimpleXMLElement $field
	 * 
	 * @since 1.0
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isHiddenOrDisabled( SimpleXMLElement $field ) 
	{
		return ('disabled' === (string)$field->elem || 'hidden' === (string)$field->elem) ? true : false;
	}

	
	/**
	 * Check if curren field is a disabled element
	 * 
	 * @param SimpleXMLElement $field
	 * 
	 * @since 1.0
	 * 
	 * @return bool
	 */
	public static function isDisabled( SimpleXMLElement $field ) 
	{
		return ('disabled' === (string)$field->elem) ? true : false;
	}			
	
	
	/**
	 * Check if curren field is a hidden element
	 * 
	 * @param SimpleXMLElement $field
	 * 
	 * @since 1.0
	 * 
	 * @return bool
	 */
	public static function isHidden( SimpleXMLElement $field ) 
	{
		return ('hidden' === (string)$field->elem) ? true : false;
	}			
	
	
	/**
	 * Check if current field is has type attribute for numeric
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.2
	 *
	 * @return bool
	 */
	public static function isNumeric( SimpleXMLElement $field )
	{
		return ( $field['type'] && 'numeric' === (string)$field['type'] );   
	}

	
	/**
	 * Check if current field is has type attribute for numeric absolute
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.2
	 *
	 * @return bool
	 */
	public static function isNumericAbs( SimpleXMLElement $field )
	{
		return ( $field['type'] && 'numeric_abs' === (string)$field['type'] );
	}	
	
	/**
	 * Check if current field is custom post type list with _click_select true
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @uses self::hasAttrNameValue()
	 *
	 * @since 1.3.1
	 *
	 * @return bool
	 */
	public static function isClickSelect( SimpleXMLElement $field )
	{
		return self::hasAttrNameValue( $field, '_click_select', 'value', 'true' );
	}	
	
	/**
	 * Check if passed setting has the default flag
	 * 
	 * Attribute is set to true: <settings default="true|1">
	 * 
	 * @param SimpleXMLElement $settings
	 *
	 * @since 1.2
	 *
	 * @return bool
	 */
	public static function isDefaultSetting( SimpleXMLElement $settings ) 
	{
		return ( isset( $settings['default'] ) && ( 'true' === (string)$settings['default'] || '1' === (string)$settings['default'] ) );
	}
	
	
	/**
	 * Check if field needs sanitizing
	 * 
	 * SKIP: checkbox, select, wptextarea, wptextareabasic
	 * 
	 * @param SimpleXMLElement $field
	 * 
	 * @since 1.2
	 * 
	 * @return bool
	 */
	public static function isFieldToSanitize( SimpleXMLElement $field )
	{
		if( self::isCheckbox( $field ) )
			return false;
		elseif( self::isSelect( $field ) )
			return false;
		elseif( self::isWpTextarea( $field ) )
			return false;
		else
			return true;
	}
	
	/**
	 * Check if current form field/group has a name attribute
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.3.3
	 *
	 * @return bool true if yes and false if no
	 */
	public static function hasAttributeName( SimpleXMLElement $field )
	{
		return ( isset($field['name']) && '' !== (string)$field['name'] ) ? true : false;
	}	
	
	/**
	 * Check if current settingsfield has a formgroup title
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasFormGroupTitle( SimpleXMLElement $field )
	{
		return ( isset($field->group_title) && '' !== (string)$field->group_title ) ? true : false;
	}
	
	/**
	 * Check if current settingsfield has a formgroup description
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasFormGroupDescr( SimpleXMLElement $field )
	{
		return ( isset($field->group_descr) && '' !== (string)$field->group_descr ) ? true : false;
	}
	
	/**
	 * Check if current settingsfield has a formgroup warning
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasFormGroupWarning( SimpleXMLElement $field )
	{
		return ( isset($field->group_warning) && '' !== (string)$field->group_warning ) ? true : false;
	}
	
	/**
	 * Check if current settingsfield has a formgroup notice
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasFormGroupNotice( SimpleXMLElement $field )
	{
		return ( isset($field->group_notice) && '' !== (string)$field->group_notice ) ? true : false;
	}
	
	/**
	 * Check if current settingsfield has a description
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasDescr( SimpleXMLElement $field )
	{
		return ( isset($field->descr) && '' !== (string)$field->descr ) ? true : false;
	}
	
	/**
	 * Check if current settingsfield has HTML attributes
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasAttr( SimpleXMLElement $field )
	{
		return ( isset($field->attributes) && isset($field->attributes->attr) ) ? true : false;
	}
	
	
	/**
	 * Check if current settingsfield has an attribute with given value
	 * 
	 * @param SimpleXMLElement $field
	 * @param string $nameAttr
	 * @param string $nameValue
	 * @param string $valueValue
	 * 
	 * @since 1.0
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasAttrNameValue( SimpleXMLElement $field, $nameAttr, $nameValue, $valueValue )
	{
		if( isset( $field->attributes ) ) 
		{			
			foreach ( $field->attributes->attr as $attr ) 
			{			
				if( $nameAttr === (string)$attr['name'] ) 
				{
					return ( isset( $attr[$nameValue] ) && $valueValue === (string)$attr[$nameValue]) ? true : false;
				}
			}
		}
		return false;
	}
	
	
	/**
	 * Check if current settingsfield has selectbox options
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasSelectOptions( SimpleXMLElement $field )
	{
		return ( isset($field->options) && isset($field->options->option) );
	}
	
	
	/**
	 * Check if current settingsfield has multi checkbox check boxes
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.1.8
	 *
	 * @return bool true if yes and false if no
	 */
	public static function hasMultiCheckbox( SimpleXMLElement $field )
	{
		return ( isset($field->check) && isset($field->check->box) );
	}	
	
	
	/**
	 * Check if current settingsfield has innerHTml
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasInnerHtml( SimpleXMLElement $field )
	{
		return ( isset($field->inner) && '' !== (string)$field->inner ) ? true : false;
	}
	
	/**
	 * Check if current settingsfield has a title
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasTitle( SimpleXMLElement $field )
	{
		return ( isset($field->title) && '' !== (string)$field->title ) ? true : false;
	}
	
	/**
	 * Check if current settingsfield has an inline title
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasInlineTitle( SimpleXMLElement $field )
	{
		return ( isset($field->inline_title) && '' !== (string)$field->inline_title ) ? true : false;
	}
	
	
	/**
	 * Check if current settingsfield has an inline description
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasInlineDescr( SimpleXMLElement $field )
	{
		return ( isset($field->inline_descr) && '' !== (string)$field->inline_descr ) ? true : false;
	}	
	
	
	
	/** Check if current field has a default value
	 * 
	 * @param SimpleXMLElement $field
	 * 
	 * @since 1.0
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function hasDefault( SimpleXMLElement $field )
	{
		return ( isset($field->default) && '' !== (string)$field->default ) ? true : false;		
	}
	
	
	
	/**
	 * Check in all fields if a datepicker element is present
	 * 
	 * @param SimpleXMLElement $fields
	 * 
	 * @since 1.0
	 * 
	 * @return boolean
	 */
	public static function hasDatePicker( SimpleXMLElement $fields )
	{		
		$hasDatePicker = false;
		
		foreach ( $fields as $field ) {
						
			if( self::isDatePicker( $field ) ) { 
				$hasDatePicker = true; break;
			}
		}

		return $hasDatePicker;		
	}
	
	
	
	/**
	 * Get all field names 
	 * 
	 * The name attribute like <field name="my_field_name">
	 * 
	 * @param SimpleXMLElement $fields
	 * 
	 * @since 1.0
	 * 
	 * @return array with field names 
	 */
	public static function getFieldNames( SimpleXMLElement $fields ) 
	{
		$names = array();
		
		foreach ($fields as $k => $field) {
			
			if( $field['name'] && '' !== (string)$field['name'] ) 
				$names[] = (string)$field['name'];
		}
		
		return $names;
	}
	

	/**
	 * Get all field names that needs validation
	 *
	 * Field should have attribute like <field validate="y">
	 *
	 * @param SimpleXMLElement $fields
	 *
	 * @since 1.1x
	 *
	 * @return array with field names
	 */
	public static function getFieldNamesToValidate( SimpleXMLElement $fields )
	{
		$names = array();
	
		foreach ($fields as $k => $field) {
				
			if( $field['validate'] && 'y' === (string)$field['validate'] )
				$names[] = (string)$field['name'];
		}
	
		return $names;
	}
	
	
	/**
	 * Get formfield attributes
	 *
	 * If the field is a colorpicker input, a class 'colorinput' is added
	 * For div elements 'name' and 'value' are not applied
	 *
	 * @param string $id the HTML id attribute
	 * @param SimpleXMLElement $field the current formfield
	 * @param string $value  the HTML value attribute
	 * @param string $namespace for array implentation of value's
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	public static function getAttr( SimpleXMLElement $field )
	{
		//TODO: loop threw attributes? Now only HTML 'class' attribute can be set from formfields array in BaseForm child classes
		$attributes = array();
	
		if( self::hasAttr($field) )
		{
			foreach ( $field->attributes->attr as $at )
			{
				$attributes[(string)$at['name']] = (string)$at['value'];
			}
		}
	
		return $attributes;
	}
	
	
	/**
	 * Get selectbox options
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return array or empty array
	 */
	public static function getSelectOptions( SimpleXMLElement $field )
	{
		$options = array();
	
		if( self::hasSelectOptions( $field ) )
		{
			foreach ( $field->options->option as $option )
			{
				$options[(string)$option['id']] = trim( $option );
			}
		}
	
		return $options;
	}
	
	
	/**
	 * Get multi checkbox options
	 *
	 * @param SimpleXMLElement $field
	 *
	 * @since 1.1.8
	 *
	 * @return array or empty array
	 */
	public static function getCheckboxOptions( SimpleXMLElement $field )
	{
		$checkboxes = array();
		
		if( self::hasMultiCheckbox( $field ) )
		{
			foreach ( $field->check->box as $box )
			{
				$checkboxes[(string)$box['id']] = trim( $box );
			}
		}
	
		return $checkboxes;
	}
	
	
	/**
	 * Get options for a multi field
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @uses self::getSelectOptions()
	 * @uses self::getCheckboxOptions() 
	 *
	 * @since 1.1.8
	 * 
	 * @return array or empty array
	 */
	public static function getMultiOptions( SimpleXMLElement $field  )
	{
		if( self::isSelect( $field ) ) 
		{
			return self::getSelectOptions( $field );
		} 
		elseif( self::isCheckboxmulti( $field ) ) 
		{
			return self::getCheckboxOptions( $field );
		} 
		else {
			return array();
		}
	}
	
	
	/**
	 * Get InnerHtml
	 *
	 * @param SimpleXMLElement $field
	 * @param string $modulePath optional
	 * @param array $vars optional
	 * 
	 * @since 0.1
	 * 
	 * @return string or empty string
	 */
	public static function getInnerHtml( SimpleXMLElement $field, $modulePath = '', $vars = array() )
	{
		$innerHtml = '';
	
		if( self::hasInnerHtml($field) && self::isTemplate($field) )
		{
			$file = $modulePath.'/'.$field->inner;
						
			if( file_exists( $file ) ) {
				
				$fileName = basename( $file );
				$basePath = dirname( $file );
				
				$template =  new gdprcTemplate( (string)$field['name'], $basePath, $fileName );
				$template->setVars( $vars );
				$innerHtml = $template->render(false, true);
				
				unset($template, $vars);
				
			} else {
				$innerHtml = __( sprintf( 'Template file "%s" is not a valid file path.', $file ), 'gdprcookies' );
			}	
				
		} elseif ( self::hasInnerHtml( $field ) && !self::isTemplate( $field ) ) {
				
			$innerHtml = $field->inner;
				
		} else {
				
			//$innerHtml = '';
		}
	
		return  $innerHtml;
	}
	
	/**
	 * Get inline formfield titel
	 *
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public static function getInlineTitle( SimpleXMLElement $field )
	{
		return  $field->inline_title;
	}	
	
	
	/**
	 * Get setting priority
	 *
	 * @access public
	 *
	 * @param SimpleXMLElement $settings
	 *
	 * @since 1.2
	 *
	 * @return int or bool false if no prio is found
	 */
	public static function getSettingPriority( SimpleXMLElement $settings )
	{
		return ( isset( $settings['prio'] ) && ( is_int( (int)$settings['prio'] ) ) ) ? (int)$settings['prio'] : false; 
	}
	
	/**
	 * Get a formgroup name attribute
	 * 
	 * @param SimpleXMLElement $field
	 * 
	 * @since 1.3.3
	 * 
	 * @return string
	 */
	public static function getFieldAttributeName( SimpleXMLElement $field )
	{
		if( self::hasAttributeName( $field ) ) {
			return (string)$field['name'];
		} else {
			return '';
		}
	}
	
	/**
	 * Get a CSS id attribute string based on field name
	 * 
	 * @param SimpleXMLElement $field
	 * 
	 * @since 1.3.3
	 * 
	 * @return string
	 */
	public static function getFieldCssIdString( SimpleXMLElement $field ) 
	{
		$str = '';
		if( self::hasAttributeName( $field) ) {			
			$elementName = $field->getName();
			$AttributeNameNice = str_replace( '_', '-', self::getFieldAttributeName( $field ) ) ;
			$str = sprintf( ' id="form-%s-%s"', $elementName, $AttributeNameNice );
		}
		
		return $str;
	}
		
	/**
	 * Trim a XML node
	 * 
	 * @access public
	 * @param string $str
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public static function trimXmlField( $str )
	{
		return (string) trim( $str );
	}	
	
	/**
	 * Serialize and prepare xml string 
	 * 
	 * @access public
	 * @param string $xml
	 * 
	 * @since 0.1
	 * 
	 * @return boolean false if string is empty else serialized string 
	 */
	public static function serializeXml( $xml = '' )
	{
		if( '' === $xml )
			return false;
	
		$xml = trim( $xml );
		$xml = preg_replace( '/>\s+</', '><', $xml );
		$xml = maybe_serialize( $xml );
	
		return $xml;
	}
	
	/**
	 * Escape single quotes in xml field 
	 * 
	 * @access public
	 * @param string $str
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public static function prepareXmlField( $str )
	{
		return (string) trim( str_replace( "'", "\'", $str ) );
	}
	
	
	
	/**
	 * Add disabled attribute 
	 * 
	 * @param SimpleXMLElement $field passed by reference
	 * 
	 * @since 1.0
	 */
	public static function addDisabled( SimpleXMLElement &$field ) 
	{
		if( isset( $field->attributes ) ) {
		
			$attr = $field->attributes->addChild( 'attr' );
			$attr->addAttribute( 'name', 'disabled' );
			$attr->addAttribute( 'value', 'disabled' );
		
		} else {
			
			$attributes = $field->addChild( 'attributes' );
			$attr = $attributes->addChild( 'attr' );
			$attr->addAttribute( 'name', 'disabled' );
			$attr->addAttribute( 'value', 'disabled' );
		}	
	}
	
	
	
	/**
	 * Create an WordPress I18n function field
	 * 
	 * Create WordPress translation funciton__()
	 * 
	 * @access public
	 * @param string $str
	 * @param string $domain
	 * @return string
	 * 
	 * @since 0.1
	 */
	public static function I18nField( $str, $domain )
	{
		$str = gdprcXmlSettingsHelper::prepareXmlField( $str );
		
		return ( '' !== $str ) ? "__('$str','$domain');\n" : '';
	}	
	
	
	/**
	 * Create a CDATA field based on a SimpleXMLElement node
	 * 
	 * @access public
	 * @param SimpleXMLElement $node
	 * @param string $str
	 * 
	 * @since 0.1
	 */
	public static function cdataField( SimpleXMLElement $node, $str )
	{		
		$domNode = dom_import_simplexml( $node );
		$no = $domNode->ownerDocument;
		$domNode->appendChild( $no->createCDATASection( $str ) );	
	}
	
	
	/**
	 * Copy an XML field 
	 * 
	 * This method is used to combine XML settings from Plugin and Modules
	 * 
	 * @access public
	 * @param SimpleXMLElement $field
	 * @param SimpleXMLElement $newField
	 * @param string $locale
	 * 
	 * @since 0.1
	 */
	public static function copyField( SimpleXMLElement $field, SimpleXMLElement &$newField, $locale )
	{
		if( $field->elem )
		{
			$newField->addChild( 'elem', (string)$field->elem );			
		}		
			
		if( $field->title )
		{
			self::cdataField( $newField->addChild( 'title' ), (string)$field->title );
		}
		if( $field->descr )
		{
			self::cdataField( $newField->addChild( 'descr' ), (string)$field->descr );
		}
		if( $field->inner )
		{
			$newFieldInner = $newField->addChild( 'inner', (string)$field->inner );
			
			if($field->inner['template']) 
			{
				$newFieldInner->addAttribute( 'template', (string)$field->inner['template'] );				
			}		
			if($field->inner['tmplclass'])
			{
				$newFieldInner->addAttribute( 'tmplclass', (string)$field->inner['tmplclass'] );
			}				
		}
		if( $field->options->option )
		{
			$newFieldOptions = $newField->addChild( 'options' );
			foreach( $field->options->option as $option )//TODO Check!
			{
				$id = (string)$option['id'];
				$newFieldOption = $newFieldOptions->addChild( 'option' );
				self::cdataField( $newFieldOption, (string)$option );
				$newFieldOption->addAttribute( 'id', $id );
			}
		}		
		if( $field->check->box )
		{
			$newFieldCheck = $newField->addChild( 'check' );
			foreach( $field->check->box as $box )
			{
				$id = (string)$box['id'];
				$newFieldBox = $newFieldCheck->addChild( 'box' );
				self::cdataField( $newFieldBox, (string)$box );
				$newFieldBox->addAttribute( 'id', $id );
			}
		}		
		if( $field->attributes->attr )
		{
			$newFieldAttributes = $newField->addChild( 'attributes' );
			foreach( $field->attributes->attr as $attr )
			{
				$name = (string)$attr['name'];
				$value = (string)$attr['value'];
				$newFieldAttr = $newFieldAttributes->addChild( 'attr', (string)$attr );
				$newFieldAttr->addAttribute( 'name', $name );
				$newFieldAttr->addAttribute( 'value', $value );
			}
		}				
		if( $field->xpath( 'defaults/default[@lang="'.$locale.'"]' ) )
		{
			$newFieldDefaults =  $newField->addChild( 'defaults' );
			
			foreach( $field->defaults->default as $default )
			{
				$lang = (string)$default['lang'];
				$newFieldDefault = $newFieldDefaults->addChild( 'default' );
				self::cdataField( $newFieldDefault, (string)$default);				
				$newFieldDefault->addAttribute( 'lang', $lang );
			}			
		}
		if( $field->xpath( 'default[@lang="'.$locale.'"]' ) )
		{
			$default = $field->xpath( 'default[@lang="'.$locale.'"]' );
			$newDefault = $newField->addChild( 'default' );
			self::cdataField( $newDefault, (string)$default );
			$newDefault->addAttribute( 'lang', $locale );
		}
		if( $field->default )
		{
			$newField->addChild( 'default', (string)$field->default );	
		}
	}	

	
	/**
	 * Unset a group in a group list
	 * 
	 * Onlt 1 level deep supported
	 * 
	 * @param 	SimpleXMLElement 	$fields
	 * @param 	string 				$group
	 * 
	 * @since 1.1.8
	 * 
	 * @return 	SimpleXMLElement	$fields
	 */
	public static function unsetGroup( SimpleXMLElement $fields, $group = null ) 
	{
		if( null == $group )
			return $fields;
		
		foreach ( $fields as $field ) 
		{		
			if( $group === (string)$field['name'] ) 
			{		
				unset( $field[0] );
				break;
			}
		}
		return $fields;		
	}
}