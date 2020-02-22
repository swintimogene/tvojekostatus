<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcFormHelper class
 * 
 * Helper class for rendering formfields
 * 
 * Depending on the type (e.a text, select, checkbox etc.) the corresponding HTML is returned
 *  
 * @author $Author: NULL $
 * @version $Id: gdprcFormHelper.php 167 2018-02-24 23:09:06Z NULL $
 * 
 * @since 0.1
 */
class gdprcFormHelper {	
	
	/**
	 * The type of the formfield
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected static $type;	
	
	/**
	 * The name of the formfield
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected static $name;
	
	
	/**
	 * The name of the formfield
	 *
	 * @since 1.1.8
	 *
	 * @var string
	 */
	protected static $nameOri;
	
	
	/**
	 * The namespace of the formfield
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected static $nameSpace;
	
	/**
	 * The value of the formfield
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected static $value;	
	
	/**
	 * HTML attributes
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	protected static $attributes;
	
	
	/**
	 * Element types that dont need the 'name' and 'value' attribute
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	protected static $attributesEscapeNameValue = array('div', 'buttonsubmitnoname', 'linkbutton', 'button');

	
	/**
	 * Element types that are 'buttons' 
	 *
	 * @since 0.1
	 *
	 * @var array
	 */
	protected static $attributesButton = array('buttonsubmitnoname', 'buttonsubmit', 'linkbutton', 'buttonsubmitconfirm', 'button');	
	
	
	/**
	 * Element types that don't need tabindex attribute
	 * 
	 * @since 1.1.5
	 * 
	 * @var array  
	 */
	protected static $tabindexEscapeFields = array('hidden', 'disabled', 'div');
		
	
	/**
	 * InnerHtml 
	 * 
	 * e.a. <a>{innerHtml}</a>
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected static $innerHtml;
	
	/**
	 * The options for a <select> element
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	protected static $options = array();
	
	/**
	 * The selectboxes for a multi selectbox element
	 *
	 * @since 1.1.8
	 *
	 * @var array
	 */
	protected static $checkboxes = array();
	
	
	/**
	 * Flag if current element has Attributes
	 * 
	 * @since 0.1
	 * 
	 * @var bool
	 */
	protected static $hasAttributes = false;
	
	/**
	 * Flag if current element has innerHtml
	 * 
	 * @since 0.1
	 * 
	 * @var bool
	 */
	protected static $hasInnerHtml = false;
	
	/**
	 * Flag if current element is a <select> field
	 * 
	 * @since 0.1
	 * 
	 * @var bool
	 */
	protected static $isSelect = false;
	
	
	/**
	 * Flag if current element is a checkbox multi field
	 * 
	 * @since 1.1.8
	 * 
	 * @var bool
	 */
	protected static $isCheckboxMulti = false;
	
	
	/**
	 * Counts the number of fields during the current page request
	 * 
	 * @since 1.1.5
	 * 
	 * @var int
	 */
	protected static $fieldCount = 1;
	
	
	/**
	 * Flag if current element is a disabled field
	 * 
	 * i.e. an attribute disabled="disabled" is added
	 * 
	 * @since 1.0
	 *  
	 * @var bool
	 */
	protected static $isDisabled = false;
	
	/**
	 * Placeholder for element attributes
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	const ATTR_MASK = '{attr}';
	
	/**
	 * Placeholder for element attributes
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	const NAME_MASK = '{name}';
	
	/**
	 * Placeholder for element attributes
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	const VALUE_MASK = '{value}';
	
	/**
	 * Placeholder for element attributes
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	const SELECT_OPTIONS_MASK = '{options}';
	
	/**
	 * Placeholder for element attributes
	 * 
	 * @var string
	 */
	const INNER_HTML_MASK = '{innerhtml}';
	
	/**
	 * HTML string for case selected with, e.a. <option selected="selected"></option> 
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	const SELECTED_STR = 'selected="selected"';
	
	/**
	 * HTML string for case checked with, e.a. <input type="radio" checked="checked" />  
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	const CHECKED_STR = 'checked="checked"';
	
	
	/**
	 * Handle the formfield based on type
	 * 
	 * @acces public
	 * 
	 * @param string $type the type of the formfield
	 * @param string $name the name of the formfield
	 * @param string $value the value of the formfield (optional)
	 * @param array $attributes the HTML attributes (optional)
	 * @param string $innerHtml the innerHtml (optional)
	 * @param array $selectOptions selectbox options (optional)
	 * @param bool $render (optional)
	 * 
	 * @since 0.1
	 * 
	 * @return string the formfield returned by self::render() 
	 */
	public static function formField( $type = '', $name = '', $value = '', $nameSpace = '', $attributes = array(), $innerHtml = '', $options = array(), $render = true )
	{		
		if( '' === (string)$type )
			return 'No formfield type defined.';
		
		self::$type = (string)$type;	
		
		self::$name = (string)$name;
		
		self::$nameSpace = ( '' !== $nameSpace ) ? $nameSpace : false;
		
		self::$value = stripslashes((string)$value);
		
		if( !empty($attributes) )
		{
			self::$hasAttributes = true;
			self::$attributes = $attributes;		
		}		
		
		self::$isDisabled = ( in_array( 'disabled', $attributes ) || 'disabled' === self::$type );
		
		if( !empty($innerHtml) )
		{
			self::$hasInnerHtml = true;
			self::$innerHtml = $innerHtml;
		}		
		
		if( 'textarea' === self::$type )
		{
			self::$hasInnerHtml = true;
			self::$innerHtml = self::$value;
		}
		
		if( !empty( $options ) && 'select' === self::$type )
		{			
			self::$isSelect = true;
			self::$options = $options;		
		}
		
		if( !empty( $options ) && 'checkboxmulti' === self::$type )
		{
			self::$value = (array)$value;
			self::$isCheckboxMulti = true;
			self::$checkboxes = $options;
		}		
		
		self::_prepareAttributes();
			
		if( true === $render )
			return self::_render();		
	}	

	
	/**
	 * Wether a selectbox is selected or not
	 * 
	 * @access public
	 * 
	 * @param string $currentValue
	 * @param string $value
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isSelected( $currentValue, $value='' )
	{
		if( isset($value) && '' !== $value )
		{
			if(is_string($value))
				$currentValue=strval($currentValue);
				
			if(is_int($value))
				$currentValue=intval($currentValue);
				
			if(is_bool($value))
				(boolean) intval($currentValue);
				
			return ($currentValue === $value);
	
		}else
			return false;
	}	
	
	
	/**
	 * Wether a checkbox is checked or not
	 * 
	 * @access public
	 * 
	 * @param string $value
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes and false if no
	 */
	public static function isChecked( $value = '' )
	{
		if( isset( $value ) ) {
	
			if( is_string( $value ) )
				$value = intval( $value );		
	
			(boolean) $value;
	
			return ( true == $value );
	
		} else
			return false;
	}	
		
	
	/**
	 * Validate email address
	 * 
	 * @param string $email
	 *
	 * @uses filter_var with the FILTER_VALIDATE_EMAIL option
	 * 
	 * @since 1.2.3
	 * 
	 * @return bool true or false
	 */
	public static function isEmail( $email = '' )
	{
		if( '' === $email ) {
			return false;
		}
		
		return filter_var( $email, FILTER_VALIDATE_EMAIL );
	}
	
	
	/**
	 * Set name attribute based on given namespace
	 * 
	 * If no namespace is passed, ignore and just set the name attribute normally
	 * 
	 * @since 1.1.8
	 * 
	 * @access protected
	 */
	protected static function _getNamespaceName( $name = '' )
	{
		$name = ( '' !== $name ) ? $name : self::$name;
		
		return ( false === self::$nameSpace ) ? $name : self::$nameSpace.'['.$name.']';
	}	
	
	
	/**
	 * Prepare attributes bases on type
	 * 
	 * @since 0.1
	 * 
	 * @access protected
	 */
	protected static function _prepareAttributes()
	{		
		self::$attributes['id'] = ( isset( self::$attributes['id'] ) ) ? self::$attributes['id'] : self::$name;
		self::$attributes['class'] = ( isset( self::$attributes['class'] ) && '' !== self::$attributes['class'] ) ? self::$attributes['class'] : '';
		self::$attributes['class'] = ( 'color' === self::$type ) ? self::$attributes['class'].' colorinput' : self::$attributes['class'];
		
		if( self::$isDisabled ) 
		{
			self::$attributes['class'] = ( isset( self::$attributes['class'] ) && '' !== self::$attributes['class'] ) ? self::$attributes['class'].' disabled' : 'disabled';
		}
		
		if( !in_array( self::$type, self::$attributesEscapeNameValue ) )
		{
			self::$nameOri = self::$name;			
			self::$attributes['name'] = self::_getNamespaceName();  
			//self::$name = self::$attributes['name'];			
			self::$attributes['value'] =  ( '' !== self::$value ) ? self::$value : '';
		}		

		if( 'textarea' === self::$type )
		{
			unset( self::$attributes['value'] );
		}
		
		if( 'checkbox' === self::$type )
		{
			if( self::isChecked( self::$value ) )
			{
				self::$attributes['checked'] = 'checked';
				self::$attributes['class'] = ( isset( self::$attributes['class'] ) && '' !== self::$attributes['class'] ) ? self::$attributes['class'].' checked' : 'checked';
			}
		}	
		
		if( 'checkboxmulti' === self::$type )
		{
			self::$attributes['_multiselect'] = ( isset( self::$attributes['_multiselect'] ) ) ? self::$attributes['_multiselect'] : 'false';
			if( 'true' ===  self::$attributes['_multiselect'] ) {
				self::$attributes['name'] = self::$attributes['name'] . '[]';				
				self::$name = self::$attributes['name'];
			} 
		}

		if( 'checkboxcheckall' === self::$type )
		{
			if( self::isChecked( self::$value ) )
			{
				self::$attributes['checked'] = 'checked';
				self::$attributes['class'] = ( isset( self::$attributes['class'] ) && '' !== self::$attributes['class'] ) ? self::$attributes['class'].' checked' : 'checked';				
			}			
			$parent = ( isset( self::$attributes['_parent'] ) ) ? "'".self::$attributes['_parent']."'" : 'false';			
			self::$attributes['onClick'] = 'gdprcCheckAll(this, '. $parent .');';
			self::$attributes['class'] = ( isset( self::$attributes['class'] ) && '' !== self::$attributes['class'] ) ? self::$attributes['class'].' gdprc-check-all' : 'gdprc-check-all';
		}		

		if( in_array( self::$type, self::$attributesButton ) )
		{
			self::$attributes['class'] = ( isset( self::$attributes['class'] ) && '' !== self::$attributes['class'] ) ? self::$attributes['class'].' button' : 'button';
		}

		if( !in_array( self::$type, self::$tabindexEscapeFields ) ) {
			self::$attributes['tabindex'] = self::$fieldCount;
		}

		if( isset( self::$attributes['_no_name'] ) )
		{
			unset( self::$attributes['name'] );			
		}
	}	
	
	
	/**
	 * Substitute the attributes into the formfield
	 * 
	 * For HTML selectboxes and checkboxes, a 'checked' class is added
	 * For HTML buttons, a 'button' class is added
	 *
	 * @access protected
	 *  
	 * @param object $field (passed by reference)
	 * 
	 * @since 0.1
	 * 
	 * @return void
	 */
	protected static function _substituteAttributes( &$field )
	{		
		if( preg_match_all('/{([a-zA-Z]+?)}/', $field, $matches) )
		{
			foreach( $matches[0] as $k => $attr ) 
			{								
				switch($attr)
				{
					case  self::ATTR_MASK:
						$field = preg_replace("/$attr/", self::_getAttributeString(), $field);
						break;
					
					case  self::SELECT_OPTIONS_MASK:
						$field = preg_replace("/$attr/", self::_getSelectOptions(self::$options) , $field);
						break;
					
					case  self::INNER_HTML_MASK:
						$field = preg_replace("/$attr/", self::$innerHtml, $field);
						break;	
					
					default:				
						$field = preg_replace("/$attr/", self::$attributes[$matches[1][$k]], $field);
						break;									
				}				
			}			
		}		
	}		
	
	
	/**
	 * Print JavaScript for a confirm submit button
	 * 
	 * @access protected
	 * 
	 * @since 1.1.8
	 */
	protected static function _printConfirmJs()
	{
		static $didConfirmJs = false;
		if( !$didConfirmJs ): ?>
		<script type='text/javascript'>
		function gdprcMaybeFormSubmit(e, btn, idHidden, msg) {
			e = e || window.event

			if('undefined' == typeof btn || null == btn)
				return false;

			if('undefined' == typeof idHidden || null == idHidden)
				return false;			

			if('undefined' == typeof msg || null == msg)
				msg = 'Submit?'; 
			
			var form = btn.form;
					
			if (confirm(msg)) {
				document.getElementById(idHidden).value = '1';				
				form.submit();			
			} else {						
				e.preventDefault();
				return false;
			}
		}
		</script>		
		<?php $didConfirmJs = true; endif ?>
		<?php				
	}	
	
	
	/**
	 * Print JavaScript for the check all checkbox
	 *
	 * @access protected
	 *
	 * @since 1.2
	 */
	protected static function _printCheckAllJs()
	{
		static $did = array();
			if( !$did ): ?>
			<script type='text/javascript'>
			function gdprcCheckAll(toggler, parent) {
				if(null == toggler || null == parent)
					return false;				
				if(false === parent) {
					// @todo create solution to find parent <table>
					return;
				}				
				var tbl = document.getElementById(parent);
			    var c = new Array();
			    c = tbl.getElementsByTagName('input');
			    for (var i=0; i<c.length; i++)  {
			        if (c[i].type == 'checkbox')   {            
			        c[i].checked = (toggler.checked) ? true : false;
			        }
			    }
			}
			</script>
			<?php $did = true; endif ?>
			<?php				
		}	
	

	/**
	 * Manually substitute an attribute
	 * 
	 * @access protected
	 * 
	 * @param string $field the HTML string for a field (passed by reference)
	 * @param string $mask the string to replace
	 * @param string $replace the string to replace the mask with
	 * 
	 * @since 1.0
	 */
	protected static function _substituteAttributesManual( &$field, $mask='', $replace='' ) 
	{
		if('' !== $mask)
			$field = preg_replace("/$mask/", $replace, $field);		
	}
	
	
	/**
	 * Get the HTML for input type text
	 *
	 * @access protected
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	protected static function _getInputText()
	{
		return '<input '.self::ATTR_MASK.' type="text" />';
	}
	
	
	/**
	 * Get the HTML for input type text
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getInputNumber()
	{
		return '<input '.self::ATTR_MASK.' type="number" />';		
	}
	
	
	/**
	 * Get the HTML for input type text with no name and value attribute
	 * 
	 * @access protected
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	protected static function _getInputTextNoNameValue()
	{
		return '<input '.self::ATTR_MASK.' type="text" />';		
	}
		
	
	/**
	 * Get the HTML for input type checkbox
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getInputCheckbox()
	{
		return '<input '.self::ATTR_MASK.' type="checkbox" value="1" />';	
	}
	
	
	/**
	 * Get the HTML for input type checkboxmulti
	 *
	 * @access protected
	 *
	 * @since 1.1.8
	 *
	 * @return string
	 */
	protected static function _getInputCheckboxMulti()
	{
		$html = '<div id="'.self::$nameOri.'">';
		
		if( self::$isCheckboxMulti ) 
		{			
			$i = 0;
			foreach ( self::$checkboxes as $id => $lable ) 
			{
				$checked = in_array( $id, self::$value );
				$checkedStr = ( $checked ) ? ' '.self::CHECKED_STR:'';				
				$html .= '<input '.self::ATTR_MASK.' type="checkbox" value="'.$id.'"'.$checkedStr.' />'.$lable.'<br/>';
				$i++;
			}	
		}
		$html .= '</div>';
		return $html;		
	}
	
	
	/**
	 * Get the HTML for input type checkboxcheckall
	 *
	 * @access protected
	 *
	 * @since 1.2
	 *
	 * @return string
	 */
	protected static function _getInputCheckboxCheckAll()
	{
		self::_printCheckAllJs();
		
		return '<input '.self::ATTR_MASK.' type="checkbox" value="1" />';
	}	
	
	
	/**
	 * Get the HTML for input type radio
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getInputRadio()
	{
		return '<input '.self::ATTR_MASK.' type="radio" />';	
	}
		
	
	/**
	 * Get the HTML for input type file
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getInputFile()
	{
		return '<input '.self::ATTR_MASK.' type="file" />';	
	}	
	
	
	/**
	 * Get the HTML for input type hidden
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getInputHidden()
	{
		return '<input '.self::ATTR_MASK.' type="hidden" />';	
	}	
	
	
	/**
	 * Get the HTML for input type text that is disabled
	 *  
	 * @access protected
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	protected static function _getInputDisabled() 
	{		
		return '<input '.self::ATTR_MASK.' type="text" disabled="disabled" />';	
	}
	
	
	/**
	 * Get the HTML for input type submit
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getInputSubmit()
	{
		return '<input '.self::ATTR_MASK.' type="submit" />';
	}	
	
	
	/**
	 * Get the HTML for a plain button
	 *
	 * @access protected
	 *
	 * @since 1.2.8
	 *
	 * @return string
	 */
	protected static function _getButton()
	{
		return '<button '.self::ATTR_MASK.'>'.self::INNER_HTML_MASK.'</button>';
	}
	
	
	
	/**
	 * Get the HTML for button type submit
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getButtonSubmit()
	{
		return '<button '.self::ATTR_MASK.' type="submit" name="'.self::NAME_MASK.'">'.self::INNER_HTML_MASK.'</button>';
	}

	
	/**
	 * Get the HTML for button type submit with a input hidden
	 *
	 * @access protected
	 *
	 * @since 1.1.8
	 *
	 * @return string
	 */
	protected static function _getButtonSubmitConfirm()
	{
		$html = '';		
		$name = ( isset( self::$attributes['_hidden_name'] ) ) ? self::$attributes['_hidden_name'] : self::$name;
		
		$hiddenName = self::_getNamespaceName( $name );
		
		$hidden = self::_getInputHidden();
		self::_substituteAttributesManual( $hidden, self::ATTR_MASK, 'id="'.$name.'" name="'.$hiddenName.'" value=""' );
		
		unset( self::$attributes['_hidden_name'] );
		
		self::_printConfirmJs();
		
		$msg = ( isset( self::$attributes['_msg'] ) ) ? self::$attributes['_msg'] : '';
		
		$html .= $hidden;
		$html .= '<button '.self::ATTR_MASK.' type="submit" name="'.self::NAME_MASK.'" onclick="gdprcMaybeFormSubmit(event, this, \''.$name.'\', \''.$msg.'\' )">'.self::INNER_HTML_MASK.'</button>';
		
		return $html;
	}
	
	
	/**
	 * Get the HTML for button type submit
	 *
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getButtonSubmitNoName()
	{
		return '<button '.self::ATTR_MASK.' type="submit">'.self::INNER_HTML_MASK.'</button>';
	}
		
	
	/**
	 * Get the HTML for input type button
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getInputButton()
	{
		return '<input '.self::ATTR_MASK.' type="button" />';
	}
	
	
	/**
	 * Get the HTML for a link button
	 * 
	 * @access protected
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	protected static function _getLinkButton()
	{
		return '<a '.self::ATTR_MASK.'>'.self::INNER_HTML_MASK.'</a>';
	}	
	

	/**
	 * Get the HTML for a selectbox
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getSelect()
	{
		$hiddenSpan = (self::$isDisabled) ? '<span class="disabled-value">'.self::$selectOptions[self::$value].'</span>' : '';

		return '<select '.self::ATTR_MASK.'>'.self::SELECT_OPTIONS_MASK.'</select>'.$hiddenSpan;	
	}
	
	/**
	 * Get the HTML for a textarea
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getTextarea()
	{					
		return '<textarea '.self::ATTR_MASK.'>'.self::INNER_HTML_MASK.'</textarea>';	
	}	
	
	/**
	 * Get the HTML for a div element
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getDiv()
	{
		return '<div '.self::ATTR_MASK.'>'.self::INNER_HTML_MASK.'</div>';	 		
	}
	
	
	/**
	 * Get the HTML for a colorpicker element
	 *
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function _getColorPicker()
	{
		return self::_getInputText();
	}
	
	
	/**
	 * Reset all class members to defaults
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return void
	 */
	protected static function _reset()
	{
		self::$type='';
		self::$name='';
		self::$nameSpace='';
		self::$value='';			
		self::$attributes=array();
		self::$innerHtml='';
		self::$options=array();
		self::$hasAttributes = false;
		self::$hasInnerHtml = false;
		self::$isSelect = false;
		self::$isCheckboxMulti = false;
		self::$isDisabled = false;
	}		
	
	
	/**
	 * Get selectbox options
	 * 
	 * @access private
	 *  
	 * @param array $options
	 * 
	 * @uses self::isSelected to detetmine the selected option
	 * 
	 * @since 0.1
	 * 
	 * @return bool false with no options or string the options HTML
	 */
	private static function _getSelectOptions( $options = array() )
	{		
		if( empty( $options ) )
			return false;
		
		$value = self::$attributes['value'];

		$optionsStr = "";
		foreach( $options as $currentValue => $v )
		{
			$selected = ( self::isSelected($currentValue, $value) )? ' '.self::SELECTED_STR:'';

			$optionsStr .= "<option value=\"$currentValue\"$selected>".$v."</option>";
		}

		return $optionsStr;		
	}
		
	
	/**
	 * Get the attributes string
	 * 
	 * For selectboxes, checkboxes and 'private' attributes the 'value' attribute is escaped
	 * Attributes prefixed with a '_' (underscore) are escaped
	 * 
	 * @access private  
	 * 
	 * @since 0.1
	 * 
	 * @return string the attributes
	 */
	private static function _getAttributeString()
	{		
		$str = '';
		foreach(self::$attributes as $attr => $v)
		{
			if( 'select' === self::$type && 'value' === $attr )
				continue;
			elseif( 'checkbox' === self::$type && 'value' === $attr )
				continue;
			elseif( 'checkboxcheckall' === self::$type && 'value' === $attr )
				continue;			
			elseif( 'checkboxmulti' === self::$type && ( 'value' === $attr || 'id' === $attr ) )
				continue;
			elseif( 0 === strpos($attr, '_') ) // private attributes
				continue;
			else			
				$str .= " $attr=\"$v\"";
		}
		
		return $str;		
	}	


	/**
	 * Render the formfield
	 * 
	 * @access private 
	 * 
	 * @since 0.1
	 * 
	 * @return string the formfield or an error message
	 */
	private static function _render()
	{
		switch( self::$type )
		{
			case 'text':
				$field = self::_getInputText();
				break;
				
			case 'number':
				$field = self::_getInputNumber();
				break;
				
			case 'textnoname':
				$field = self::_getInputTextNoNameValue();
				break;
					
			case 'checkbox':
				$field = self::_getInputCheckbox();
				break;
				
			case 'checkboxmulti':
				$field = self::_getInputCheckboxMulti();
				break;	

			case 'checkboxcheckall':
				$field = self::_getInputCheckboxCheckAll();
				break;				
					
			case 'radio':
				$field = self::_getInputRadio();
				break;
					
			case 'file':
				$field = self::_getInputFile();
				break;
						
			case 'hidden':
				$field = self::_getInputHidden();
				break;
				
			case 'disabled':
				$field = self::_getInputDisabled();
				break;	
				
			case 'submit':
				$field = self::_getInputSubmit();
			break;	

			case 'button':
				$field = self::_getButton();
				break;
			
			case 'buttonsubmit':
				$field = self::_getButtonSubmit();
				break;	

			case 'buttonsubmitconfirm':
				$field = self::_getButtonSubmitConfirm();
				break;

			case 'buttonsubmitnoname':
				$field = self::_getButtonSubmitNoName();
				break;				
	
			case 'button':
				$field = self::_getInputButton();
				break;

			case 'linkbutton':
				$field = self::_getLinkButton();
				break;					
			
			case 'select':
				$field = self::_getSelect();
				break;
					
			case 'textarea':
				$field = self::_getTextarea();
				break;
				
			case 'div':
				$field = self::_getDiv();
				break;

			case 'color':
			case 'colorpicker':	
				$field = self::_getColorPicker();
				break;		

			default:
				return sprintf( 'Invalid formfield type: %s', self::$type);
				break;
		}	

		self::_substituteAttributes( $field );		
				
		// flush members
		self::_reset();
		
		self::$fieldCount++;
	
		return $field;
	}		
}


/**
 * gdprcWpFormHelper Class 
 * 
 * Helper class for rendering WordPress specific formfields
 *
 * @author $Author: NULL $
 * @version $Id: gdprcFormHelper.php 167 2018-02-24 23:09:06Z NULL $
 * @since 0.1
 */
class gdprcWpFormHelper extends gdprcFormHelper {

	
	/**
	 * Placeholder for filename value
	 * 
	 * @since 1.0
	 * 
	 * @var string
	 */
	const VALUE_MASK_FILENAME = '{filename}';
	
	
	/**
	 * Template file name for one Custom Post Type list item
	 *
	 * @since 1.2.1
	 *
	 * @var string
	 */
	const TEMPL_FILE_NAME_LIST_ITEMS = 'gdprc-tpl-list-items.php';
	
	
	/**
	 * Template file name for one list item
	 *
	 * @since 1.2.1
	 *
	 * @var string
	 */
	const TEMPL_FILE_NAME_LIST_ITEM = 'gdprc-tpl-list-item.php';	
	

	/**
	 * file name that is used by the file/image upload fields
	 * 
	 * @since 1.0
	 * 
	 * @var string 
	 */
	private static $filename = '';
	
	
	/**
	 * the file extension based on the filename
	 * 
	 * @since 1.2
	 * 
	 * @var string
	 */
	private static $fileExt = '';
	
	
	/**
	 * Element types that dont need the 'name' and 'value' attribute
	 * 
	 * @since 1.0
	 * 
	 * @var array
	 */
	protected static $attributesEscapeNameValue = array('wptermlist_edit', 'wpcpostlist_edit');
	
	
	/**
	 * Element types that don't need tabindex attribute
	 *
	 * @since 1.1.5
	 *
	 * @var array
	 */
	protected static $tabindexEscapeFields = array();	
	
	
	
	/* (non-PHPdoc)
	 * @see FormHelper::formField()
	 * 
	 * @since 0.1
	 */
	public static function formField( $type = '', $name = '', $value = '', $nameSpace = '', $attributes = array(), $innerHtml = '', $options = array(), $render = true )
	{
		parent::$attributesEscapeNameValue = array_unique(array_merge(parent::$attributesEscapeNameValue, self::$attributesEscapeNameValue));		
		
		parent::formField( $type, $name, $value, $nameSpace, $attributes, $innerHtml, $options, false );	
		
		self::_prepareAttributes();
		
		if( 'wptextareabasic' === parent::$type )
		{
			parent::$hasInnerHtml = true;
			parent::$innerHtml = parent::$value;
		}	

		if( 'wpcpostlist' === parent::$type || 'wpcpostlist_edit' === parent::$type ) {
			if( 0 < (int)parent::$value ) {
				parent::$value = (int)parent::$value;
			}
		}
		
		if( ( 'wpfile' === parent::$type || 'wpfilebasic' === parent::$type || 'wpimage' === parent::$type ) && '' !== parent::$value ) {
			
			self::$filename = esc_html( wp_basename( parent::$value ) );
			self::$fileExt	= gdprcMiscHelper::getFileExt( self::$filename );					
		}
			
		return self::_render();	
	}	
	
	
	/**
	 * Get a taxonomy selectbox with terms
	 * 
	 * See the documentation for {@link wp_dropdown_categories()}
	 * 
	 * @param string $id
	 * @param string $name
	 * @param string $orderby
	 * @param (bool|int) $echo
	 * @param int $selected
	 * @param string $tax
	 * @param array $args
	 * 
	 * @uses wp_parse_args()
	 * @uses wp_dropdown_categories()
	 * 
	 * @since 0.1
	 * 
	 * @return Ambigous <string, mixed>
	 */
	public static function getTermDropdown( $id, $name, $orderby='ID', $echo=1, $selected=0, $tax='category', $args=array() )
	{
		$defaults = array(
				'orderby'            => $orderby,
				'echo'               => $echo,
				'selected'           => $selected,
				'name'               => $name,
				'id'                 => $id,
				'taxonomy'           => $tax,
				'hide_empty'		 => 0	
		);		
		
		if( !empty($args) ) 
		{
			$args = wp_parse_args($args, $defaults);
		} else {			
			$args = $defaults;			
		}
		
		if( $echo )
			wp_dropdown_categories( $args );
		else
			return wp_dropdown_categories( $args );		
	}
	
	
	/**
	 * Get a HTML list with taxonomy terms
	 * 
	 * @param string $tax, the taxonomy to retrieve the terms for
	 * @param string $orderby
	 * @param array $args optional arguments to pass to get_terms()
	 * 
	 * @uses wp_parse_args()
	 * @uses get_terms()
	 * @uses gdprcWpFormHelper::_getTermListRow()
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	public static function getTermList( $tax='category', $orderby='ID', $args=array() )
	{		
		$defaults = array(
				'orderby'   => $orderby,
				'taxonomy'  => $tax,
				'get'		=> 'all'
		);		
		
		if( !empty($args) ) 
		{
			$args = wp_parse_args($args, $defaults);
		} else {			
			$args = $defaults;			
		}

		$terms = get_terms( $tax, $args );
		
		if( empty($terms) || is_wp_error($terms) ) 
			return sprintf('No term list possible for taxonomy "%s".', $tax);	
		
		$list = '';
		
		$list .= '<ul '.parent::ATTR_MASK.'>';
		foreach($terms as $term) {
			
			$list .= sprintf(self::_getTermListRow(), $term->term_id, $term->term_id, $term->name, $term->name);
			
		}
		$list .= '</ul>';
		
		return $list;
	}
	
	
	/**
	 * Get a editable list with custom post type items
	 * 
	 * @access	public 
	 * 
	 * @param		string 	$postType			the custom post type
	 * @param 	string	$layout 			the type of layout
	 * @param 	string	$outerW 			the outer width (%)
	 * @param		string 	$context 			the unique context, like products, cookies, bullets etc.
	 * @param		bool		$canAdd				flag if user can add rows to the list
	 * @param		bool		$canDel				flag if user can delete rows from the list
	 * @param		bool		$canSave			flag if user can update rows from the list
	 * @param   bool    $clickSelect  flag if items should implement click select 
	 * @param		bool		$hasTitle			flag if the list rows should have the post title field
	 * @param		bool		$hasMedia			flag if media JS should be enqueued
	 * @param		bool		$groupMeta		flag if meta data should be grouped and saved as one entry
	 * @param		string	$groupMetaKey the wp_postmeta "meta_key" column
	 * @param   bool    $hasHeading   flag if has headings
	 * 
	 * @since	1.2.1
	 * 
	 * @return 	Ambigous <string, boolean>
	 */
	public static function getCustomPostTypeList( $postType = 'post', $layout = 'normal', $outerW = '20', $context = '', $canAdd = true, $canDel = true, $canSave = true, $clickSelect = false, $hasTitle = false, $hasMedia = false, $groupMeta = false, $groupMetaKey = '', $hasHeading = false ) 
	{		
		// all post (objects)
		$havePosts = false;
		$headingsAr = array();
		
		if( !post_type_exists( $postType ) )
			return sprintf( 'Invalid Post Type "%s" for custom post type list', $postType );
		
		$postTypeObj = new gdprcPostType( $postType );
					
		/**
		 * Let others filter the WP_Query args array to retieve the custom post type posts
		 *
		 * @param array $args
		 *
		 * @since 2.1.2
		 *
		 * @return array
		 */
		$args = apply_filters( 'gdprc_wp_query_args_' . $context,  array( 'orderby' => 'ID' ) );
		
		// query for the posts
		$posts = $postTypeObj->retrieveCustom( $args );
		
		// flag if posts are available
		if( ! empty( $posts ) )
			$havePosts = true;

		// if have posts and $hasMedia is true, enqeue/print the necessary scripts
		if( $havePosts && $hasMedia ) 
		{
			self::enqueueMediaScripts();
			self::printInputImageJs();
		}
		
		$hook_before_list_posts = apply_filters( 'gdprc_before_list_custom_posts_' . $context , '', $postType );
		$hook_after_list_posts = apply_filters( 'gdprc_after_list_custom_posts_' . $context , '', $postType );	

		// alternative method to go 2 dirs back
		//$wfPath = dirname(dirname(dirname( __FILE__ )));
		$wfPath = dirname( __FILE__ ) . "/../../.";
		$templ = new gdprcTemplate( self::TEMPL_FILE_NAME_LIST_ITEMS , $wfPath . '/templates', self::TEMPL_FILE_NAME_LIST_ITEMS );		
		
		$selected = ( $clickSelect ) ? parent::$value : 0;	

		// get heading items
		if( $hasHeading ) {
			/**
			 * Let others add headings to the list
			 *
			 * @param array $headingsAr
			 *
			 * @since 2.1.2
			 *
			 * @return array
			 */			
			$headingsAr = apply_filters( 'gdprc_list_headings_' . $context , array() );
			if( 0 < count( $headingsAr ) && ( $canDel || $canSave ) ) {
				$headingsAr[] = '';
			} else {
				$hasHeading = false;
			}		
		}
		
		$templ->setVar( 'selected', $selected );
		$templ->setVar( 'layout', $layout );
		$templ->setVar( 'outer_w', $outerW );
		$templ->setVar( 'context', $context );
		$templ->setVar( 'posts', $posts );
		$templ->setVar( 'post_type', $postType );
		$templ->setVar( 'have_posts', $havePosts );		
		$templ->setVar( 'can_add', $canAdd );
		$templ->setVar( 'can_del', $canDel );
		$templ->setVar( 'can_save', $canSave );
		$templ->setVar( 'click_select', $clickSelect );
		$templ->setVar( 'has_title', $hasTitle );
		$templ->setVar( 'has_media', $hasMedia );
		$templ->setVar( 'group_meta', $groupMeta );
		$templ->setVar( 'group_meta_key', $groupMetaKey );
		$templ->setVar( 'templ_list_item', $wfPath . '/templates/' .self::TEMPL_FILE_NAME_LIST_ITEM );
		$templ->setVar( 'hook_before_list_posts', $hook_before_list_posts );
		$templ->setVar( 'hook_after_list_posts', $hook_after_list_posts );
		$templ->setVar( 'has_heading', $hasHeading );
		$templ->setVar( 'headings', $headingsAr );
		
		return $templ->render( false, true );
	}
	
	
	
	/**
	 * Get a selectbox with Post Types
	 * 
	 * @uses get_post_types()
	 * 
	 * @since 1.0
	 * @since 1.4.7 renamed to getPostTypeTypesDropDown()
	 * 
	 * @return string
	 */
	public static function getPostTypeTypesDropDown()
	{
		$args = array('public' => true);
		
		parent::$options = get_post_types($args);		

		$select = '<select '.parent::ATTR_MASK.'>'.parent::SELECT_OPTIONS_MASK.'</select>';

		return $select;		
	}
	
	
	/**
	 * Get the HTML for a selectbox with pages
	 * 
	 * @param string $id
	 * @param string $name
	 * @param string $sortColumn
	 * @param (bool|int) $echo
	 * @param int $selected
	 * 
	 * @uses wp_dropdown_pages()
	 * 
	 * @since 0.1
	 * 
	 * @return Ambigous <string, mixed>
	 */
	public static function getPageDropdown( $id, $name, $sortColumn='ID', $echo=1, $selected=0  )
	{
		$args = array(
				'id' => $id,
				'name' => $name,
				'sort_column' => $sortColumn,
				'echo' => $echo,
				'show_option_no_change' => '-- '. __( 'Select', 'gdprcookies' ) .' --',
				'selected' => $selected
		);
	
		if( $echo )
			wp_dropdown_pages( $args );
		else
			return wp_dropdown_pages( $args );
	}	
	
	
	/**
	 * Get the HTML for a selectbox with pages
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $sortColumn
	 * @param (bool|int) $echo
	 * @param int $selected
	 *
	 * @uses wp_dropdown_pages()
	 *
	 * @since 1.4.7
	 *
	 * @return Ambigous <string, mixed>
	 */
	public static function getCustomPostTypeDropdown( $id, $name, $selected=0, $echo=0, $postType='post' )
	{
		// all post (objects)
		$select = '';
		$havePosts = false;
		$options = array();
		
		if( !post_type_exists( $postType ) ) {
			return sprintf( 'Invalid Post Type "%s" for custom post type dropdown', $postType );
		}
			
		/**
		 * Let others filter the WP_Query args array to retieve the custom post type posts
		 *
		 * @param array $args
		 *
		 * @since 1.4.7
		 *
		 * @return array
		 */
		$args = apply_filters( 'gdprc_wp_query_args_post_type_dropdown',  array( 
				'orderby' => 'ID',
				'order' => 'DESC',
				'post_type' => $postType,
				'post_status' => 'publish' ) );
		
		$postType = $args['post_type'];

		$postTypeObj = new gdprcPostType( $postType );		
		
		// query for the posts
		$posts = $postTypeObj->retrieveCustom( $args, false, false );
		
		// flag if posts are available
		if( ! empty( $posts ) ) {
			$options[-1] = __( '-- Select --', 'gdprcookies' );
			foreach( $posts as $postId => $post ) {
				$isPdf = ( wp_attachment_is( 'pdf', $postId ) );
				if( 'attachment' === $postType && !$isPdf ) {
					continue;
				}							
				if( 'attachment' === $postType && $isPdf ) {
					$title = basename( get_post_meta( $postId, '_wp_attached_file', true ) );
				} else {
					$title = $post->post_title;
				}					
				$options[$postId] = $title;
			}
			parent::$options = $options;		
			$select = '<select '.parent::ATTR_MASK.'>'.parent::SELECT_OPTIONS_MASK.'</select>';
		}		
		
		if( $echo ) {
			echo $select;
		} else {
			return $select;
		}		
	}
		
	
	/**
	 * Enqueue WordPress media scripts
	 * 
	 * @access public
	 * 
	 * @uses wp_enqueue_media()
	 *  
	 * @since 1.1.5
	 */
	public static function enqueueMediaScripts()
	{
		static $isEnqueued = false;
		
		if( !$isEnqueued ) {

			wp_enqueue_media();
			$isEnqueued = true;		
		}
	}
	
	
	/**
	 * Callback for the admin_print_footer_scripts hook 
	 * 
	 * Print jQuery JavaScript that is needed for an image/file upload field
	 * 
	 * The code can handle multiple uploader instances at one page
	 * 
	 * To force extensions, add the HTML data attribute 'data-force-ext' with comma separated extensions that are allowed, simplified example: <input type="file" data-force-ext="png,jpg">
	 * 
	 * @since 1.0
	 */
	public static function printInputImageJs()
	{		
		static $isPrinted = false;
		
		if( !$isPrinted ):
		?>
		<script type='text/javascript'>
		jQuery(function($) {

		// prevent the media popup triggering when hitting 'Enter' key	
		$('.wrap').on('keypress', function(e) {
			if (13 === (e.keyCode || e.which) && !$(e.target).hasClass('wp_upload_button') ) return false;
		});
			
		// code from http://www.webmaster-source.com/2013/02/06/using-the-wordpress-3-5-media-uploader-in-your-plugin-or-theme/
		// Slightly modified
		var custom_uploader;  
	  
  	$('.wrap').on('click', '.wp_upload_button', function(e) {
			e.preventDefault();
	      
		  var thiz = $(this),
	    	  type = thiz.data('button-type'),
	    	  input = thiz.siblings('input.wp'+type),
	    	  filenameEl = thiz.siblings('.filename'),
	    	  previewEl = thiz.siblings('.preview'),
	    	  haveForcedExt = false,
	    	  haveForcedDim = false,
	    	  showPreview = false;      		  	
  		  	
			// get forced extensions
			if(input.data('force-ext')) {
				var extstr = input.data('force-ext'),
					forcedext = extstr.split(','),
					haveForcedExt = true;    			
			}
			// get forced dimensions
			if(input.data('force-dim')) {
				var extstr = input.data('force-dim'),
					forceddimAr = extstr.split('x'),
					forcedDimW = forceddimAr[0],
					forcedDimH = forceddimAr[1],
					haveForcedDim = true;    			
			}			
			
			if(input.data('show-preview')) {				
				showPreview = (true == input.data('show-preview')) ? true : false;
	
				var previewMaxWh = parseInt(input.data('preview-max-wh'));
				previewMaxWh = (!isNaN( previewMaxWh )) ? previewMaxWh : false;								
			}    	
	
      // If the uploader object has already been created, reopen the dialog	      	
      custom_uploader = ( 'undefined' !== typeof thiz.data('custom_uploader')) ? thiz.data('custom_uploader') : false;
      
      if ( custom_uploader ) {
      	custom_uploader.open();
       	return;
      }
	      	
      // Extend the wp.media object
      custom_uploader = wp.media.frames.file_frame = wp.media({
          title: 'Choose '+type,
	      button: {
	      	text: 'Choose '+type
	      },
	      multiple: false
	    });
      thiz.data('custom_uploader', custom_uploader);	      
	
      // When a file is selected, grab the URL and set it as the text field's value
      custom_uploader.on('select', function() {          
 	
 	    	var attachment = custom_uploader.state().get('selection').first().toJSON();
	        // filename
			var name = attachment.url.split('/').pop(),
				relUrl = attachment.url.split('/uploads/').pop();

			input.data('attachment-id', attachment.id);
			input.attr('data-attachment-id', attachment.id);
          
          if(haveForcedExt) {
          	// get ext from url
			var ext = attachment.url.split('.').pop();
			
			if(ext && -1 !== forcedext.indexOf(ext)) {
					input.val(relUrl);
					filenameEl.html(name);								
				} else {
					alert('Only files with "'+extstr+'" extension are allowed');
				}					          		
          } else if(haveForcedDim) {
              //
          } else {
			input.val(relUrl);
			if(showPreview) {
				var preview = $('<img src="'+attachment.url+'" />'),
					ratio = (attachment.height/attachment.width).toFixed(2);								
				if(previewMaxWh && attachment.width < previewMaxWh && attachment.height < previewMaxWh) {
  					preview.attr({ 'width':attachment.width, 'height':attachment.height });
				} else { 
					preview.attr({ 'width':previewMaxWh+'px', 'height': Math.round(previewMaxWh*ratio)+'px' });
				}									
				previewEl.append(preview);									
			} else {
				filenameEl.html(name);
			}		          	
          }	
      });
	  // Open the uploader dialog
	  custom_uploader.open();
	  });  	
	});
	</script>
	<?php 
	$isPrinted = true;
	endif;				
	}	
	
	
	/**
	 * Callback for the admin_print_footer_scripts hook 
	 * 
	 * Print jQuery JavaScript that is needed for a  basic file upload field
	 * 
	 * To force extensions, add the HTML data attribute 'data-force-ext' with comma separated extensions that are allowed, simplified example: <input type="file" data-force-ext="docx,pdf">
	 * 
	 * @todo check if using .on() is needed
	 * 
	 * @since 1.0
	 */
	public static function printInputFileJs()
	{		
		?>
		<script type='text/javascript'>
		jQuery(function($) {

			$('.gdprc-change-file').click(function(e) {

				$(this).prev('.wpfilebasic').trigger('click');			
			});
			
		  $('.wpfilebasic').change(function(e) {
		  	
	      e.preventDefault();
	      
	      var input = $(this),
	      		filename = input.val(),
	      		filenameEl = input.siblings('.filename');
	      
    		// get forced extensions
    		if(input.data('force-ext')) {

				var extstr = input.data('force-ext'),
    				forcedext = extstr.split(',');
    			
				// get ext from url
				var ext = filename.split('.')[1];		
				
				// filename
				var name = filename.split('.')[0];
					
				if(ext && -1 !== forcedext.indexOf(ext)) {

					if( '' !== filenameEl.html() ) {						
						filenameEl.html(name);							
					}
					
				}	else {

					input.val(null);
					alert('Only files with "'+extstr+'" extension are allowed');
				}	    			
    		}
		  });
		});
	</script>
	<?php 				
	}	
	
	
	/**
	 * Callback for the admin_print_footer_scripts hook 
	 * 
	 * Print jQuery JavaScript that is needed for a term list with $edit is true, see {@link gdprcWpFormHelper::_getWpTermlist()}
	 * 
	 * @uses gdprcWpFormHelper::_getInputTextNoNameValue()
	 * @uses gdprcWpFormHelper::_getTermListRow()
	 * @uses gdprcWpFormHelper::_getTermListRowActions()
	 * 
	 * @since 1.0
	 */
	public static function printTermListJs() 
	{
		?>
		<script type='text/javascript'>
		jQuery(function($) {		

			var gdprcookiesTermList = {

				termLists: [],
				namespaceAction: '',
					
				getTermId: function (el) {
					
					return el.data('term-id');		
				},					

				getTermLi: function (id) {
						
					var li = this.termLists.find('li[data-term-id="'+id+'"]');
					
					if(0 < li.length) {
						return li
					} else {
						 return false;
					}		
				},					

				switchEditUpdate: function (id, show) {		
						
					var li = this.getTermLi(id);
					
					if(false !== li) {
						
						var btnUpdate = li.find('.gdprc-action-upd');
						var btnEdit = li.find('.gdprc-action-edit');
						
						switch(show) {			
							case 'edit':				
								btnUpdate.hide();	
								btnEdit.show();				
								break;				
							case 'update':				
								btnUpdate.show();	
								btnEdit.hide();				
								break;			
						}
					}
				},

				handlerMouseenter: function (e) {

					var li = $(this),
							termListliActions = li.find('.gdprc-list-row-actions');  

					termListliActions.show();									
				},


				handlerMouseleave: function (e) {

					var li = $(this),
							termListliActions = li.find('.gdprc-list-row-actions');  

					termListliActions.hide();					
				},


				handlerKeydown: function (e) {

					var thiz = e.data.thiz;					

					var li = $(this).parents('li'),
							termId = thiz.getTermId(li);
					
					thiz.switchEditUpdate(termId, 'update');
				},

				handlerBlur: function (e) {

					var thiz = e.data.thiz;

					var li = $(this).parents('li'),
							input = $(this),
							termNameNew = input.val(),
							termNameOri = li.data('term-name'),
							termId = thiz.getTermId(li);
			
					if('' === termNameNew) {
			
						thiz.switchEditUpdate(termId, 'edit');
			
						li.on('mouseenter', thiz.handlerMouseenter);	
						li.on('mouseleave', thiz.handlerMouseleave);						
												
						li.find('.gdprc-term-name').val(termNameOri);
						li.find('.gdprc-term-name').show();
						input.hide();		
						li.trigger('mouseleave');			
					}
				},			
					
				handlerBtnDel: function (e) {

					var thiz = e.data.thiz;	
					
					if (confirm(commonL10n.warnDelete)) {

						var li = $(this).parents('li.gdprc-term-item'),
								termId = li.data('term-id'),
								tax = e.data.tax,
								termList = e.data.termList;
	
						if('' === termId || 'undefined' === typeof termId)
							return false;

						var args = {};
						args['action'] = 'gdprc-action';
						args[thiz.namespaceAction] = 'gdprc-del-term';
						args['nonce'] = gdprcNonce;
						args['data'] = {termId:termId, tax:tax};

						$.ajax({ type: 'POST', url: ajaxurl, dataType: 'json', data: args, success: function(r) {
				    		
								//console.log('r: ',r);	
						  	switch(r.state) {
						  		case '-1': 	alert(r.out); break;
						  		case '1':								  		
										var termId = r.out.term_id;	
										termList.find('li.gdprc-term-'+termId).remove();	
							  		break;
						  	}								
					    },
					    error: function (XMLHttpRequest, textStatus, errorThrown) { }
					  });
					} // end confirm delete						
				},

				handlerBtnEdit: function (e) {

					var thiz = e.data.thiz;	
					
					var li = $(this).parents('li.gdprc-term-item'),
							termId = li.data('term-id'),
							termName = li.data('term-name'),
							liSpan = li.find('.gdprc-term-name'), 
							input = $('<?php echo str_replace(parent::ATTR_MASK, 'class="gdprc-edit-term" value=""', self::_getInputTextNoNameValue()) ?>');

					//console.log(termId, termName);						
			
					li.off('mouseenter', thiz.handlerMouseenter);	
					li.off('mouseleave', thiz.handlerMouseleave);
			
					liSpan.hide();
					liSpan.after(input);
			
					input.trigger('focus');
				},		
					
				handlerBtnUpd: function (e) {
					
					var li = $(this).parents('li.gdprc-term-item'),
							termId = li.data('term-id'),
							liSpan = li.find('.gdprc-term-name'), 
							input = li.find('input.gdprc-edit-term'),
							termNameNew = input.val(),
							tax = e.data.tax;
			
					if('' === termId || 'undefined' === typeof termId || '' === termNameNew || 'undefined' === typeof termNameNew)
						return false;
			
					var args = {};
					args['action'] = 'gdprc-action';
					args[thiz.namespaceAction] = 'gdprc-upd-term';
					args['nonce'] = gdprcNonce;
					args['data'] = {termId:termId, tax:tax, termName:termNameNew};
			
					$.ajax({ type: 'POST', url: ajaxurl, dataType: 'json', data: args, success: function(r) {
			    		
					  	switch(r.state) {
					  		case '-1': 	alert(r.out); break;
					  		case '1':			
									liSpan.html(r.out.name).show();
									li.attr('data-term-name', r.out.name).data('term-name', r.out.name);	
						  		break;
					  	}
					  	
					  	input.val('').hide();								
				    },
				    error: function (XMLHttpRequest, textStatus, errorThrown) { }
				  });						
				},

				handlerBtnAdd: function (e) {

					var termInput = $(this).prev('input.gdprc-new-term'),				
							termVal =	termInput.val();

					if('' === termVal)
						return false;

					var thiz = e.data.thiz,
							tax = e.data.tax,
							termList = e.data.termList;

					var args = {};
					args['action'] = 'gdprc-action';
					args[thiz.namespaceAction] = 'gdprc-add-term';
					args['nonce'] = gdprcNonce;
					args['data'] = {tax:tax, termVal:termVal};

					$.ajax({ type: 'POST', url: ajaxurl, dataType: 'json', data: args, success: function(r) {

					  	switch(r.state) {
					  		case '-1': 	alert(r.out); break;
					  		case '1':							  		
									var termId = r.out.term_id,
											termName = r.out.name,
					  					li = '<?php echo self::_getTermListRow()?>';
					  					
					  			li = li.replace(/%d/g, termId);
					  			li = li.replace(/%s/g, termName);
					  			termList.append(li);

					  			// Re-init all term lists again 
					  			// The just newly created term li will then also have all logic binded 					  			
					  			thiz.init();
						  		break;
					  	}							
							termInput.val('');													
				    },
				    error: function (XMLHttpRequest, textStatus, errorThrown) { }
				  });				
				},

				init: function () {

					if(0 < $('.gdprc-term-list').length)
						this.termLists = $('.gdprc-term-list');
					else
						return false;	

					// WordPress AJAX action
					// gdprcNamespace is an unique string passed bij gdprcookies Framework
					// So that multiple Instances of the Framework can use this JS
					// @todo: fix gdprcNamespace does not exist
					this.namespaceAction = gdprcNamespace+'_action';
					
					var thiz = this;					

					this.termLists.each(function() {

						var termList = $(this),
								termInput = $(this).next('input.gdprc-new-term'),
						 		btnAdd = termInput.next('.gdprc-btn-add'),
								termListli = termList.find('li'),
								tax = termInput.data('tax');

						// Append row actions to each li
						termListli.each(function() {
							var li = $(this);					
							if( 0 === li.find('.gdprc-list-row-actions').length ) { 
								li.append(' <?php echo self::_getTermListRowActions() ?>');
							}					
						});
						
						var termListliActions = termListli.find('.gdprc-list-row-actions'),   
								btnDel = termListliActions.find('.gdprc-action-del'),
								btnEdit = termListliActions.find('.gdprc-action-edit'),
								btnUpd = termListliActions.find('.gdprc-action-upd');

						
						termListli.on('keydown', 'input.gdprc-edit-term', {thiz:thiz}, thiz.handlerKeydown);										
						termListli.on('blur', 'input.gdprc-edit-term', {thiz:thiz}, thiz.handlerBlur);
						
						termListli.on({
							mouseenter: thiz.handlerMouseenter,
							mouseleave: thiz.handlerMouseleave,
						});				
						
						btnDel.on('click', {thiz:thiz, tax:tax, termList:termList}, thiz.handlerBtnDel);
						btnEdit.on('click', {thiz:thiz, tax:tax, termList:termList}, thiz.handlerBtnEdit);
						btnUpd.on('click', {thiz:thiz, tax:tax, termList:termList}, thiz.handlerBtnUpd);	
						btnAdd.on('click', {thiz:thiz, tax:tax, termList:termList}, thiz.handlerBtnAdd);						
					});	//end termLists each							
				}
			};

			// Init all params and events and loop trew all term lists 
			gdprcookiesTermList.init();							
		});
		</script>
		<?php
	}	
	
	
	/**
	 * Callback for the admin_print_footer_scripts hook 
	 * 
	 * Print jQuery JavaScript that is needed for a color picker field
	 * 
	 * @uses wpColorPicker()
	 * 
	 * @since 1.0
	 */
	public static function printColorPickerJs() 
	{
	?>
	<script type='text/javascript'>
	jQuery(function($) {	
		$('.colorpicker').wpColorPicker();	
	});
	</script>
	<?php		
	}	
	
	
	/* (non-PHPdoc)
	 * @see FormHelper::_prepareAttributes()
	 * 
	 * @since 0.1
	 */
	protected static function _prepareAttributes()
	{			
		//add default classes
		if( 'wpajaxbutton' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' button' : 'button';
		}		
		
		if( 'wpimage' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' wpimage' : 'wpimage';
		}
		
		if( 'wpfile' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' wpfile' : 'wpfile';			
		}

		if( 'wpfilebasic' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' wpfilebasic' : 'wpfilebasic';			
		}		

		if( 'wppageselect' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' wppageselect' : 'wppageselect';
		}	

		if( 'wptermselect' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' wptermselect' : 'wptermselect';		
			self::$attributes['_tax'] = ( taxonomy_exists(self::$attributes['_tax']) ) ? self::$attributes['_tax'] : 'category';			
		}	

		if( 'wptermlist' === self::$type || 'wptermlist_edit' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' gdprc-term-list' : 'gdprc-term-list';		
			self::$attributes['_tax'] = ( taxonomy_exists(self::$attributes['_tax']) ) ? self::$attributes['_tax'] : 'category';			
		}		
		
		if( 'wpcpostlist_edit' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' gdprc-cpostype-list' : 'gdprc-cpostype-list';
			
			self::$attributes['_layout'] = ( isset( self::$attributes['_layout'] ) ) ? self::$attributes['_layout'] : 'normal';
			self::$attributes['_outer_w'] = ( isset( self::$attributes['_outer_w'] ) ) ? self::$attributes['_outer_w'] : 20;
			self::$attributes['_context'] = ( isset( self::$attributes['_context'] ) ) ? self::$attributes['_context'] : '';
			self::$attributes['_post_type'] = ( isset( self::$attributes['_post_type'] ) && '' !== self::$attributes['_post_type'] ) ? self::$attributes['_post_type'] : '';			
			
			self::$attributes['_can_add'] = ( !isset( self::$attributes['_can_add'] ) || ( isset( self::$attributes['_can_add'] ) && 'true' === self::$attributes['_can_add'] ) ) ? true : false;
			self::$attributes['_can_del'] = ( !isset( self::$attributes['_can_del'] ) || ( isset( self::$attributes['_can_del'] ) && 'true' === self::$attributes['_can_del'] ) ) ? true : false;
			self::$attributes['_can_save'] = ( !isset( self::$attributes['_can_save'] ) || ( isset( self::$attributes['_can_save'] ) && 'true' === self::$attributes['_can_save'] ) ) ? true : false;
			self::$attributes['_click_select'] = ( isset( self::$attributes['_click_select'] ) && 'true' === self::$attributes['_click_select'] ) ? true : false;
			
			self::$attributes['_has_title'] = ( !isset( self::$attributes['_has_title'] ) || ( isset( self::$attributes['_has_title'] ) && 'true' === self::$attributes['_has_title'] ) ) ? true : false;
			self::$attributes['_has_media'] = ( !isset( self::$attributes['_has_media'] ) || ( isset( self::$attributes['_has_media'] ) && 'false' === self::$attributes['_has_media'] ) ) ? false : true;
			
			self::$attributes['_group_meta'] = ( !isset( self::$attributes['_group_meta'] ) || ( isset( self::$attributes['_group_meta'] ) && 'false' === self::$attributes['_group_meta'] ) ) ? false : true;
			self::$attributes['_group_meta_key'] = ( isset( self::$attributes['_group_meta_key'] ) ) ? self::$attributes['_group_meta_key'] : '';

			self::$attributes['_headings'] = ( isset( self::$attributes['_headings'] ) ) ? self::$attributes['_headings'] : '';
		}		
		
		if( 'wpposttypeselect' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' wpposttypeselect' : 'wpposttypeselect';
		}			
		
		if( 'wpcustomptselect' === self::$type )
		{
			self::$attributes['_post_type'] = ( isset( self::$attributes['_post_type'] ) && '' !== self::$attributes['_post_type'] ) ? self::$attributes['_post_type'] : 'post';	
		}
		
		if( 'wpdate' === self::$type || 'wpdatepicker' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' dateinput datepicker' : 'dateinput datepicker';	
		}		

		if( 'wpcolor' === self::$type || 'wpcolorpicker' === self::$type )
		{
			self::$attributes['class'] = ( isset(self::$attributes['class']) ) ? self::$attributes['class'].' colorpicker' : 'colorpicker';
		}		
		
		if( 'wptextarea' === self::$type ) {}

		if( 'wptextareabasic' === self::$type )
		{
			unset( self::$attributes['value'] );
		}
	}		
	
	
	/* (non-PHPdoc)
	 * @see FormHelper::_substituteAttributes()
	 * 
	 * @since 1.0
	 */
	protected static function _substituteAttributes( &$field )
	{		
		if( preg_match_all('/{([a-zA-Z]+?)}/', $field, $matches) )
		{	
			foreach( $matches[0] as $k => $attr ){
								
				switch($attr)
				{
					case self::VALUE_MASK_FILENAME:						
						
						$field = preg_replace("/$attr/", self::$filename, $field);
						break;
				}									
				
			}			
		}	

		parent::_substituteAttributes($field);
	}		
	
	
	
	/* (non-PHPdoc)
	 * @see FormHelper::_reset()
	 * 
	 * @since 1.0
	 */
	protected static function _reset() {
	
		self::$filename='';
		
		parent::_reset();		
	}	
	
	
	/**
	 * Get the HTML for the WordPress image upload button
	 *
	 * @access private
	 * 
	 * @uses gdprcWpFormHelper::enqueueMediaScripts()
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	private static function _getWpInputImage()
	{
		self::enqueueMediaScripts();
		add_action('admin_print_footer_scripts', array(__CLASS__, 'printInputImageJs'));		
	
		$tabindex		= ( isset( parent::$attributes['tabindex'] ) ) ? ' tabindex="'.parent::$attributes['tabindex'].'"' : '';
		$previewMaxWh	= ( isset( parent::$attributes['data-preview-max-wh'] ) && is_numeric( parent::$attributes['data-preview-max-wh'] ) ) ? parent::$attributes['data-preview-max-wh'] : false;
		$previewW		= ( isset( parent::$attributes['data-preview-w'] ) && is_numeric( parent::$attributes['data-preview-w'] ) ) ? parent::$attributes['data-preview-w'] : false;
		$previewH		= ( isset( parent::$attributes['data-preview-h'] ) && is_numeric( parent::$attributes['data-preview-h'] ) ) ? parent::$attributes['data-preview-h'] : false;
		$ratio			= ( isset( parent::$attributes['data-preview-ratio'] ) && is_numeric( parent::$attributes['data-preview-ratio'] ) ) ? parent::$attributes['data-preview-ratio'] : 1;
		
		$w = ( $previewMaxWh && $previewW && $previewMaxWh <= $previewW ) ? $previewMaxWh : $previewW; 
		$h = ( $previewMaxWh && $previewH && $previewMaxWh <= $previewH ) ? round($previewMaxWh*$ratio) : $previewH;

		$style = '';		
		if( $previewMaxWh && 'svg' === self::$fileExt ) {
			$w = $previewMaxWh;
			$h = '';
			$style = " style='max-width:{$previewMaxWh}px; max-height:{$previewMaxWh}px'";			
		}			
		
		$preview = ( isset( parent::$attributes['data-show-preview'] ) && true == parent::$attributes['data-show-preview'] && parent::$value ) ? '<img width="'.$w.'" height="'.$h.'" src="'.WP_CONTENT_URL . '/uploads/' . parent::VALUE_MASK.'"'.$style.' />' : '';
		unset( parent::$attributes['tabindex'] );
		
		$input  = '<input '.parent::ATTR_MASK.' type="hidden" name="'.parent::NAME_MASK.'" value="'.parent::VALUE_MASK.'" />';
		$input .= '<button class="button wp_upload_button upload_img_button" data-button-type="image"'.$tabindex.'>'.parent::INNER_HTML_MASK.'</button><span class="filename">'. (( '' === $preview ) ? self::VALUE_MASK_FILENAME : '') . '</span><span class="preview">'.$preview.'</span>';
	
		return $input;
	}
	
	
	/**
	 * Get the HTML for the WordPress file upload button
	 *
	 * @access private
	 * 
	 * @uses gdprcWpFormHelper::enqueueMediaScripts()
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	private static function _getWpInputFile()
	{
		self::enqueueMediaScripts();
		add_action('admin_print_footer_scripts', array(__CLASS__, 'printInputFileJs'));		
	
		$disabled = (parent::$isDisabled) ? ' disabled="disabled"' : '';
		
		$input  = '<input '.parent::ATTR_MASK.' type="hidden" name="'.parent::NAME_MASK.'" value="'.parent::VALUE_MASK.'" /><span class="filename">'.self::VALUE_MASK_FILENAME.'</span>';
		$input .= '<button class="button wp_upload_button upload_file_button" data-button-type="file"'.$disabled.'>'.parent::INNER_HTML_MASK.'</button>';
	
		return $input;
	}

	
	
	/**
	 * Get the HTML for a basic file upload button
	 * 
	 * @access private  
	 * 
	 * @uses FormHelper::_getInputFile()
	 * @uses FormHelper::_getLinkButton()
	 * @uses FormHelper::_substituteAttributesManual()
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	private static function _getWpInputFileBasic()
	{
		add_action('admin_print_footer_scripts', array(__CLASS__, 'printInputFileJs'));	
		
		$input = '';
		
		if( '' !== self::$filename ) {

			$input  = '<span class="filename">'.self::VALUE_MASK_FILENAME.'</span>';
			
			$file .= parent::_getInputFile();

			if( isset(parent::$attributes['style']) ) {
			
				parent::$attributes['style'] = parent::$attributes['style'] . ';display:none;';
				
			} else {
				parent::$attributes['style'] = 'display:none';
			}
									
			$change = parent::_getLinkButton();
			$disabled = (parent::$isDisabled) ? 'disabled' : '';
			parent::_substituteAttributesManual($change, parent::ATTR_MASK, 'class="button gdprc-change-file '.$disabled.'"');		
			
			$input .= $file.$change;
			
		} else {
			
			$input .= parent::_getInputFile();
		}
	
		return $input;
	}	
	
	
	/**
	 * Get the HTML for a WordPress link (a) button with ajax loader gif
	 *
	 * @access private
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	private static function _getWpInputAjaxButton()
	{
		return '<a '.self::ATTR_MASK.'>'.self::INNER_HTML_MASK.'</a><img alt="" class="ajax-loading" src="'.admin_url('images/wpspin_light.gif').'" />';
	}	
	
	
	/**
	 * Get the HTML for a selectbox with WordPress pages
	 *
	 * @access private
	 * 
	 * @since 0.1 
	 *  
	 * @return string
	 */
	private static function _getWpPageSelect()
	{
		return self::getPageDropdown(self::$attributes['id'], self::$attributes['name'], 'ID', 0, self::$attributes['value']);
	}	
	
	
	/**
	 * Get the HTML for a selectbox with WordPress pages
	 *
	 * @access private
	 *
	 * @since 1.4.7
	 *
	 * @return string
	 */
	private static function _getWpCustomPostTypeSelect()
	{
		$id       = self::$attributes['id'];
		$name     = self::$attributes['name'];
		$selected = self::$attributes['value'];
		$postType = self::$attributes['_post_type'];
		
		return self::getCustomPostTypeDropdown( $id, $name, $selected, 0, $postType );
	}	
	
	
	/**
	 * Get the HTML for a selectbox with Post Types
	 * 
	 * @uses gdprcWpFormHelper::getPostTypeTypesDropDown()
	 * 
	 * @since 1.0
	 * @since 1.4.7 renamed to _getWpPostTypeTypesSelect()
	 * 
	 * @return string
	 */
	private static function _getWpPostTypeTypesSelect()
	{		
		return self::getPostTypeTypesDropDown(self::$attributes['id'], self::$attributes['name'], 'ID', 0, self::$attributes['value']);
	}		
	
	
	/**
	 * Get the HTML for a selectbox with WordPress terms of given taxonomy
	 *
	 * @access private
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	private static function _getWpTermSelect()
	{
		return self::getTermDropdown(self::$attributes['id'], self::$attributes['name'], 'ID', 0, self::$attributes['value'], self::$attributes['_tax'] );
	}	
	
	
	/**
	 * Get a WordPress editor 
	 * 
	 * @uses wp_editor()
	 * 
	 * @since 1.0
	 * 
	 * @todo possibility to change wp_editor paramaters
	 * 
	 * @return string
	 */
	private static function _getWpTextarea()
	{
		$value = html_entity_decode( self::$value, ENT_QUOTES, 'UTF-8' );
		
		$name_in_ar = ( preg_match( '/^[^\[]+\[(.+)]$/', self::$attributes['name'], $m ) ) ? $m[1] : '';
		$editor_id = 	( '' !== $name_in_ar ) ? $name_in_ar : self::$name;
		
		// attributes
		$teeny  	= ( isset( self::$attributes['_teeny'] ) 	&& ( 'true' === self::$attributes['_teeny'] 	|| true === self::$attributes['_teeny'] ) 	) ? true : false; 		
		$minimal	= ( isset( self::$attributes['_minimal'] ) 	&& ( 'true' === self::$attributes['_minimal']	|| true === self::$attributes['_minimal'] ) ) ? true : false;
		$media		= ( isset( self::$attributes['_media'] ) 	&& ( 'false' === self::$attributes['_media']	|| true === self::$attributes['_media'] ) ) ? false : true;
		$settings 	= array( 'wpautop' => true, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '5', 'textarea_name' => self::$attributes['name'], 'teeny' => $teeny );
		
		if( $minimal ) {					
			$settings['media_buttons'] 	= false;
			$settings['tinymce']		= false;
			$settings['quicktags'] 		= array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
		} else {
			$settings['media_buttons'] 	= $media;
		}
		
		ob_start();		
		//echo '<style type="text/css">.form-field input {width:auto}</style>';
		wp_editor( $value, $editor_id, $settings );
		$editor = ob_get_contents();
		ob_end_clean();
		
		return $editor;		
	}	
	
	
	/**
	 * Get a default textarea but validated by WordPress
	 * 
	 * @uses esc_textarea()
	 * 
	 * @since 1.0.1
	 * 
	 * @return string
	 */
	private static function _getWpTextareaBasic() {

		$textarea = parent::_getTextarea();	
		
		parent::_substituteAttributesManual( $textarea, parent::INNER_HTML_MASK, esc_textarea( parent::$value ) );
		
		return $textarea;		
	}	


	/**
	 * Get the HTML for a datepicker element
	 * 
	 * @uses FormHelper::_getInputText()
	 * 
	 * @access private
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	private static function _getWpDatePicker() 
	{
		return parent::_getInputText();
	}	
	
	
	/**
	 * Get the HTML for the 'add' fields used by formfield 'wptermlist_edit' 
	 * 
	 * @access private 
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	private static function _getTermListActions() 
	{
		$html = $input = $add = '';
		
		$input = parent::_getInputTextNoNameValue();
		parent::_substituteAttributesManual($input, parent::ATTR_MASK, sprintf('value="" id="%s" class="gdprc-new-term" data-tax="%s"', parent::$name, parent::$attributes['_tax']));
		
		$add = parent::_getLinkButton();
		parent::_substituteAttributesManual($add, parent::INNER_HTML_MASK, 'add');
		parent::_substituteAttributesManual($add, parent::ATTR_MASK, 'class="button gdprc-btn-add"');
		
		$html = $input.$add;
		
		return $html;		
	}
	
	
	/**
	 * Get the HTML template for one row in a terms list
	 * 
	 * @access private
	 * 
	 * @since 1.0
	 *  
	 * @return string
	 */
	private static function _getTermListRow() {
		
		return '<li data-term-id="%d" class="gdprc-term-item gdprc-term-%d" data-term-name="%s"><span class="gdprc-term-name">%s</span></li>';		
	}
	
		
	/**
	 * Get the HTML template for the actions in one row of a terms list
	 * 
	 * @access private
	 * 
	 * @since 1.0 
	 *  
	 * @return string
	 */
	private static function _getTermListRowActions() {
		
		return '<a class="gdprc-list-row-actions" style="display:none"><span class="gdprc-action-del" style="color:red">delete </span><span class="gdprc-action-edit">| edit</span><span class="gdprc-action-upd" style="display:none">| update</span></a>';		
	}	
	
	
	
	/**
	 * Get the HTML for a term list
	 * 
	 * If $edit is true, JavaScript is added by WordPress hook "admin_print_footer_scripts" with {@link gdprcWpFormHelper::printTermListJs()}
	 * 
	 * @access private 
	 * 
	 * @param bool $edit flag if edit controls should be added or not
	 * 
	 * @uses gdprcWpFormHelper::getTermList()
	 * @uses gdprcWpFormHelper::_getTermListActions()
	 * @uses gdprcWpFormHelper::getTermList()
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	private static function _getWpTermlist( $edit = false ) {
		
		if($edit) {		
			
			add_action('admin_print_footer_scripts', array(__CLASS__, 'printTermListJs'));	
			
			$list = self::getTermList(self::$attributes['_tax']);	
			
			$controls = self::_getTermListActions();	 
			
			return $list.$controls;			
			
		} else {
		
			return self::getTermList(self::$attributes['_tax']);		
		}	
	}
	
	
	/**
	 * Get the HTML for a custom post type gdprcookies list
	 * 
	 * @access	private
	 * 
	 * @param bool $edit flag if edit controls should be added or not
	 * 
	 * @since	1.2.1
	 * 
	 * @uses	self::getCustomPostTypeList()
	 * 
	 * @return Ambigous <Ambigous, string, boolean>
	 */
	private static function _getWpCustomPostTypeList( $edit = false ) 
	{		
		// general attributes
		$layout				= self::$attributes['_layout'];
		$outerW				= self::$attributes['_outer_w'];
		$context			= self::$attributes['_context'];
		$postType			= self::$attributes['_post_type'];
		$hasTitle 		= self::$attributes['_has_title'];
		$groupMeta 		= self::$attributes['_group_meta'];
		$groupMetaKey	= self::$attributes['_group_meta_key'];		
		$clickSelect	= self::$attributes['_click_select'];
		$hasHeading	  = self::$attributes['_has_heading'];
				
		if( $edit ) {
			
			$canAdd				= self::$attributes['_can_add'];
			$canDel				= self::$attributes['_can_del'];
			$canSave			= self::$attributes['_can_save'];			
			$hasMedia 		= self::$attributes['_has_media'];
			
		} else {			
			$canAdd	= $canDel = $canSave = $hasMedia = false;									
		}

		return self::getCustomPostTypeList( $postType, $layout, $outerW, $context, $canAdd, $canDel, $canSave, $clickSelect, $hasTitle, $hasMedia, $groupMeta, $groupMetaKey, $hasHeading );		
	}	
	
	
	/**
	 * Get HTMNL for a color picker 
	 * 
	 * JavaScript is added by WordPress hook "admin_print_footer_scripts" with {@link gdprcWpFormHelper::printColorPickerJs()}
	 * 
	 * @uses wp_enqueue_style to enqueue the color picker styles 
	 * @uses wp_enqueue_script to enqueue the color picker scripts 
	 * @uses gdprcWpFormHelper::_getColorPicker()
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	private static function _getWpColorPicker() {
			 
		wp_enqueue_style( 'wp-color-picker' );		 
		wp_enqueue_script( 'wp-color-picker' );
		
		add_action('admin_print_footer_scripts', array(__CLASS__, 'printColorPickerJs'), 9999 );

		$input = parent::_getColorPicker();

		return $input;
	}
	
	
	/**
	 * Render the WordPress formfield
	 * 
	 * @access private 
	 * 
	 * @uses gdprcWpFormHelper::_substituteAttributes()
	 * @uses gdprcWpFormHelper::_reset()
	 * 
	 * @since 0.1
	 * 
	 * @return string the formfield or a translatable error message
	 */
	private static function _render()
	{
		switch( parent::$type )
		{
			case 'wpimage':
					$field = self::_getWpInputImage();
				break;
				
			case 'wpfile':
					$field = self::_getWpInputFile();
				break;		

			case 'wpfilebasic':
					$field = self::_getWpInputFileBasic();
				break;
								
			case 'wpajaxbutton':
				$field = self::_getWpInputAjaxButton();
				break;	

			case 'wppageselect' :
				$field = self::_getWpPageSelect();
				break;
				
			case 'wpcustomptselect':
				$field = self::_getWpCustomPostTypeSelect();
				break;
				
			case 'wptermselect' :
				$field = self::_getWpTermSelect();
				break;	
				
			case 'wpposttypeselect' :
				$field = self::_getWpPostTypeTypesSelect();
				break;					
				
			case 'wptextarea':	
				$field = self::_getWpTextarea();
				break;	
				
			case 'wptextareabasic':
				$field = self::_getWpTextareaBasic();
				break;
				
			case 'wpdate':
			case 'wpdatepicker':				
				$field = self::_getWpDatePicker();
				break;

			case 'wptermlist':
				$field = self::_getWpTermlist();
				break;
				
			case 'wptermlist_edit':
				$field = self::_getWpTermlist(true);
				break;
				
			case 'wpcpostlist':
				$field = self::_getWpCustomPostTypeList();
				break;				
				
			case 'wpcpostlist_edit':
				$field = self::_getWpCustomPostTypeList(true);
				break;
				
			case 'wpcolor':
			case 'wpcolorpicker':
				$field = self::_getWpColorPicker();
				break;
				
			default:
				return sprintf( __( 'Invalid formfield type: %s', 'gdprcookies' ), self::$type);
				break;
		}	
		
		self::_substituteAttributes( $field );
		
		// flush members
		self::_reset();
	
		return $field;
	}
}