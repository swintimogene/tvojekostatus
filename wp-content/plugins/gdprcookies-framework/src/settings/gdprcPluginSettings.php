<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * Abstract Class gdprcPluginSettings
 * 
 * Class for handeling the WordPress Plugin settings.  
 * This class defines a couple of abstract methods which a child class must implement
 *
 * @author $Author: NULL $
 * @version $Id: gdprcPluginSettings.php 175 2018-03-07 15:21:02Z NULL $
 * @since 0.1
 */
class gdprcPluginSettings extends gdprcBaseSettings 
{
	/**
	 * Settings group
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 * 
	 * @todo remove this because parent::optionName is equal?
	 */
	protected $group;	

	/**
	 * The path of the (xml)  settings file
	 *
	 * @since 0.1
	 *
	 * @var string
	 */
	protected $path;	
	
	/**
	 * Unique Plugin namespace
	 * 
	 * @since 1.0
	 * 
	 * @var string
	 */
	protected $namespace; 
		
	/**
	 * The settings form fields data
	 *
	 * @since 0.1
	 *
	 * @var SimpleXMLElement
	 */
	protected $settings;
	
	/**
	 * All instances of this class
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	public static $instances = array();	
	
	/**
	 * Flag if current setting is marked default
	 * 
	 * @since 1.2
	 * 
	 * @var bool
	 */
	public $isDefault = false;	
	
	/**
	 * The priority of the setting
	 * 
	 * Tabs are ordered against this number
	 * 
	 * @since 1.2
	 * 
	 * @var int
	 */
	public $priority = 100;	
	
	/**
	 * Private setting param name priority
	 * 
	 * @since 1.2
	 * 
	 * @var string
	 */
	const gdprc_PRIVATE_SETT_NAME_PRIO = '_gdprc_prio';	

	/**
	 * Default priority value
	 * 
	 * @since 1.2
	 * 
	 * @var int
	 */
	const gdprc_SETTINGS_DEFAULT_PRIO = 100;	

	/**
	 * Constructor
	 * 
	 * Calls parent gdprcBaseSettings class. Hooks to WordPress actions. The child class must set appropriate action callback
	 * - admin_init: to register the settings
	 * - pre_update_option_{$option_name}: to save the settings to the stack before updating in the db   
	 * 
	 * @access public	
	 * 
	 * @uses gdprcPluginSettings::_setGroup()  
	 * @uses gdprcPluginSettings::_setPath()
	 * @uses gdprcPluginSettings::setSettings() 	    
	 * 
	 * @param string $name
	 * @param string $path
	 * @param string $locale
	 * @param array $locales
	 * @param string $namespace
	 * 
	 * @since 0.1
	 */
	public function __construct( $name = '', $path = '', $locale = '', $locales = array(), $namespace = '' )
	{			
		try {						
			$this->_setGroup( $name );			
			$this->_setPath( $path );			
			$this->_setNamespace( $namespace );
			
			parent::__construct( $name, $locale, $locales );
			
			$this->setSettings();
			
			} catch( Exception $e ) {			
				// only show Exception messages in the WP Dashboard
				if( is_admin() ) {
					gdprcNotices::add( $this->namespace, $e->getMessage() );
				}

				// @todo: flag that errors occured. Do not continue plugin process.
			}					
			
		if( !isset( self::$instances[$name] ) ) {
			self::$instances[$name] = $this;	
		}
	}	
	
	/**
	 * Destructor to unset big arrays
	 * 
	 * This destructor will unset all instances in {@link gdprcPluginSettings::$instances}
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 */
	public function __destruct()
	{
		unset( self::$instances[$this->getOptionName()] );
	}	
		
	/**
	 * Get the settings group
	 *
	 * @access public
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getGroup()
	{
		return $this->group;
	}	
	
	/**
	 * Get the settings form field info
	 *
	 * @access public
	 *
	 * @since 0.1
	 *
	 * @return SimpleXMLElement
	 */
	public function getSettings()
	{
		return $this->settings;
	}	
	
	/**
	 * Get the setting priority
	 * 
	 * @access public
	 * 
	 * @since 1.2
	 * 
	 * @return int
	 */
	public function getPriority() 
	{
		return $this->priority;
	}
	
	/**
	 * Get class member isDefault
	 * 
	 * @access public
	 * 
	 * @since 1.2
	 * 
	 * @return bool
	 */
	public function isDefaultSetting() 
	{
		return $this->isDefault;	
	}	
	
	/* (non-PHPdoc)
	 * @see WpSettings::setDefaults()
	*
	* @since 0.1
	*/
	public function setDefaults()
	{
		if ( '' !== $this->path ) {
			$name = $this->getOptionName();				
			$xmlStr = gdprcXmlSettingsHelper::serializeXml( file_get_contents( $this->path ) );
				
			if( $this->_setOptionXmlFields( $name, $xmlStr ) ) {
				$this->settings = simplexml_load_file( $this->path, null, LIBXML_NOCDATA  );				
				// get the priority defined in the XML like <settings prio="1">
				$this->_setSettingPriority( $this->settings );					
				// determine if current settings is marked as default (for multiple tabs)
				$this->_setOptionDefault( $this->settings );
				$this->_writeTranslations( $this->settings );	
				$this->_settingsWalker( $this->settings );	
			} else {
				throw new Exception( sprintf( 'Option not set for setting: %s', $name ) );
			}
		}
	}	
	
	/**
	 * Let Modules extend the plugin settings
	 *
	 * @access public
	 *
	 * @param string $path abolute path to the settings file
	 *
	 * @since 0.1
	 */
	public function extendSettings( $path = '', $force = false )
	{
		if( true === $this->isInit || true === $force ) {
			try {			
				$name = $this->getOptionName();
				$pathSettings = $this->getPath();				
				$extendedSettings = @simplexml_load_file( $path, null, LIBXML_NOCDATA );
				
				if( false === $extendedSettings ) { 
					throw new Exception( sprintf( 'An error occured during loading the XML-file for %s', $path ) );
				}
				
				$this->_mergeSettings( $this->settings, $extendedSettings );				
				$this->_writeTranslations( $this->settings );					
				$this->_settingsWalker( $this->settings );
								
				$xmlStr = gdprcXmlSettingsHelper::serializeXml( $this->settings->asXML() );		
				$this->_updateOption( $xmlStr, $name.'_fields' );
				$this->updateOption();
				
			} catch ( Exception $e ) {				
				gdprcNotices::add( $this->namespace, $e->getMessage() );
			}
		}
	}
	
	/**
	 * Get all instances of this class
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return array with settings gdprcPluginSettings objects or empty array
	 */
	protected function getInstances()
	{
		return self::$instances;		
	}	
	
	/**
	 * Get a single instance of this class
	 * 
	 * @access protected
	 * 
	 * @param string $name
	 * 
	 * @since 0.1
	 * 
	 * @return object gdprcPluginSettings or bool false if not exists 
	 */
	protected function getInstance( $name )
	{
		return ( isset( self::$instances[$name] ) ) ? self::$instances[$name] : false;
	}	
	
	/**
	 * Set the member settings
	 * 
	 * Child classes must implement this method
	 * This method sets the settings member with the SimpleXMLElement data from the DB wp_options {$this->optionName}_fields
	 * 	  
	 * @access protected
	 * 
	 * @since 0.1
	 */
	protected function setSettings()
	{		
		$name = $this->getOptionName();
		$fields = $this->_getOptionXmlFields( $name, false );

		if( false === $fields ) {			
			$this->setDefaults();
			$fields = $this->_getOptionXmlFields( $name, false );
		}
				
		if( false !== $fields ) { 
			$xmlStr = maybe_unserialize( $fields );			
			$this->settings = simplexml_load_string( $xmlStr, null, LIBXML_NOCDATA );			
			// get the priority defined in the XML like <settings prio="1">
			$this->_setSettingPriority( $this->settings );				
			// determine if current settings is marked as default (for multiple tabs)
			$this->_setOptionDefault( $this->settings );			
		} else {
			throw new Exception( sprintf( 'Couldn\'t read $fields for setting: %s', $name ) );
		}		
	}	
	
	/**
	 * Set the settings group for the setting being registered
	 * 
	 * @access private
	 * 
	 * @param string $group
	 * 
	 * @since 0.1
	 */
	private function _setGroup( $group )
	{			
		if( '' === $group ) {
			throw new Exception( 'input parameter $group is empty.' );
		} else {
			$this->group = $group;
		}
	}	
	
	/**
	 * Set the path of the (xml) settings file
	 *
	 * @access private
	 * 
	 * @param string $path
	 * 
	 * @since 0.1
	 */
	private function _setPath( $path )
	{
		if( '' === $path )
			throw new Exception( 'input parameter $path is empty.' );
		else
			$this->path = $path;	
	}
	
	/**
	 * Set the namespace
	 * 
	 * @access private
	 * 
	 * @param string $namespace
	 * 
	 * @throws Exception if $namespace is empty
	 * 
	 * @since 1.0
	 */
	private function _setNamespace( $namespace )
	{
		if( '' === $namespace ) {
			throw new Exception( 'input parameter $namespace is empty.' );
		} else {
			$this->namespace = $namespace;
		}
	}	
	
	/**
	 * Merge two SimpleXMLElement settings objects 
	 * 
	 * Modules settings are added to the plugin settings
	 * 
	 * @access private
	 * 
	 * @param SimpleXMLElement $oldSettings
	 * @param SimpleXMLElement $extendedSettings
	 * 
	 * @since 0.1
	 */
	private function _mergeSettings( &$oldSettings, $extendedSettings )
	{
		foreach ( $extendedSettings->children() as $field ) {
			if( 'group' === $field->getName() ) {
				$groupName = (string)$field['name'];
				
				if( ! $oldSettings->xpath( '//settings/group[@name="'.$groupName.'"]' ) ) {	
					$newGroup = $oldSettings->addChild( 'group' );
					$newGroup->addAttribute( 'name', $groupName );

					if( $field->group_title ) {
						gdprcXmlSettingsHelper::cdataField( $newGroup->addChild( 'group_title' ), (string)$field->group_title );
					}
					if( $field->group_descr ) {
						gdprcXmlSettingsHelper::cdataField( $newGroup->addChild( 'group_descr' ), (string)$field->group_descr );
					}			
					if( $field->group_warning ) {
						gdprcXmlSettingsHelper::cdataField( $newGroup->addChild( 'group_warning' ), (string)$field->group_warning );
					}							

					foreach ( $field->children() as $subfield ) {												
						if( 'inline' === $subfield->getName() ) {
							$inlineName = (string)$subfield['name'];
							$newInline = $newGroup->addChild( 'inline' );
							$newInline->addAttribute( 'name', $inlineName );
								
							if( $subfield->inline_title ) {
								gdprcXmlSettingsHelper::cdataField( $newInline->addChild( 'inline_title' ), (string)$subfield->inline_title );
							}
							if( $subfield->inline_descr ) {
								gdprcXmlSettingsHelper::cdataField( $newInline->addChild( 'inline_descr' ), (string)$subfield->inline_descr );
							}
								
							foreach ( $subfield->field as $inlineField ) {
								$fieldName = (string)$inlineField['name'];
								if( ! $oldSettings->xpath( '//settings/group[@name="'.$groupName.'"]/inline[@name="'.$inlineName.'"]/field[@name="'.$fieldName.'"]' ) ) {
									$newInlineField = $newInline->addChild( 'field' );
									$newInlineField->addAttribute( 'name', $fieldName );
									gdprcXmlSettingsHelper::copyField( $inlineField, $newInlineField, $this->locale );
								}									
							}//end inline loop							
						}//end inline
						if( 'field' === $subfield->getName() ) {
							$fieldName = (string)$subfield['name'];
							if( ! $oldSettings->xpath( '//settings/group[@name="'.$groupName.'"]/field[@name="'.$fieldName.'"]' ) ) {
								$newGroupField = $newGroup->addChild( 'field' );
								$newGroupField->addAttribute( 'name', $fieldName );
								gdprcXmlSettingsHelper::copyField( $subfield, $newGroupField, $this->locale );
							}								
						}//end field						
					}//end loop children of group	
				}
			} elseif( 'inline' === $field->getName() ) {
				$inlineName = (string)$field['name'];
				$newInline = $oldSettings->addChild( 'inline' );
				$newInline->addAttribute( 'name', $inlineName );
				
				if( $field->inline_title ) {
					gdprcXmlSettingsHelper::cdataField( $newInline->addChild( 'inline_title' ), (string)$field->inline_title );
				}
				if( $field->inline_descr ) {
					gdprcXmlSettingsHelper::cdataField( $newInline->addChild( 'inline_descr' ), (string)$field->inline_descr );
				}						
				
				foreach ( $field->field as $inlineField ) {			
					$curName = (string)$inlineField['name'];
					if( ! $oldSettings->xpath( '//settings/inline/field[@name="'.$curName.'"]' ) ) {												
						$newInlineField = $newInline->addChild( 'field' );
						$newInlineField->addAttribute( 'name', $curName );						
						gdprcXmlSettingsHelper::copyField( $inlineField, $newInlineField, $this->locale );
					}					
				}//end inline loop
			} else { //node === field
				$curName = (string)$field['name'];
				
				if( ! $oldSettings->xpath( '//settings/field[@name="'.$curName.'"]' ) ) {									
					$newField = $oldSettings->addChild( 'field' );
					$newField->addAttribute( 'name', $curName );
					gdprcXmlSettingsHelper::copyField( $field, $newField, $this->locale );					
				}				
			}
		}
	}	
	
	/**
	 * Determine if current setting is flagged as default
	 *
	 * Find the XML node <settings default="true">
	 * Set the self::isDefault flag to true if found
	 *
	 * @access	private
	 *
	 * @param	string	$settings
	 *
	 * @uses	gdprcXmlSettingsHelper::isDefaultSetting()
	 *
	 * @since 	1.2
	 *
	 * @return	bool
	 */
	private function _setSettingPriority( $settings )
	{
		if( $this->offsetExists( self::gdprc_PRIVATE_SETT_NAME_PRIO ) ) {
			$prio = $this->offsetGet( self::gdprc_PRIVATE_SETT_NAME_PRIO );
			$this->priority = $prio;
		} else {
			if( false !== ( $prio = gdprcXmlSettingsHelper::getSettingPriority( $settings ) ) ) {
				$this->priority = $prio;
			} else {
				$this->priority = self::gdprc_SETTINGS_DEFAULT_PRIO;
			}	
			$this->offsetSet( self::gdprc_PRIVATE_SETT_NAME_PRIO, $prio );
		}	 
	}	
	
	/**
	 * Determine if current setting is flagged as default
	 * 
	 * Find the XML node <settings default="true">
	 * Set the self::isDefault flag to true if found 
	 * 
	 * @access	private
	 * 
	 * @param	string	$settings
	 * 
	 * @uses	gdprcXmlSettingsHelper::isDefaultSetting()
	 * 
	 * @since 	1.2
	 * 
	 * @return	bool
	 */
	private function _setOptionDefault( $settings ) 
	{		
		$value = get_option( $this->namespace.'_setting_default', false );		
		
		if( $this->getOptionName() === $value ) {
			$this->isDefault = true;
		} elseif( false === $value && gdprcXmlSettingsHelper::isDefaultSetting( $settings ) ) {
			$this->isDefault = true;
			return add_option( $this->namespace.'_setting_default', $this->getOptionName() );
		}
	}	
	
	/**
	 * Set an option for the current setting
	 * 
	 * This option holds the string presentation of the current settings XML
	 * 
	 * @access private
	 * 
	 * @param string $name
	 * @param string $xml
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if succeeded or false on failure
	 */
	private function _setOptionXmlFields( $name, $xml )
	{
		if( !$xml ) {
			return false;
		} else {
			return add_option( $name.'_fields' , $xml );
		}								
	}	
	
	/**
	 * Get the current settings XML fields option
	 * 
	 * @access private
	 * @param string $name the settings name
	 * 
	 * @since 0.1
	 * 
	 * @return mixed
	 */
	private function _getOptionXmlFields( $name, $unserialize = true )
	{
		if( true === $unserialize ) {
			return maybe_unserialize( get_option( $name.'_fields' ) );
		} else {
			return get_option( $name.'_fields', false );
		}
	}	
	
	/**
	 * Walk threw a settings (xml) field and add data to the stack
	 * 
	 * @access private
	 * 
	 * @param SimpleXMLElement $field
	 * 
	 * @since 0.1
	 */
	private function _fieldWalkerDefaults( $field )
	{		
		foreach ( $field->xpath( '//field' ) as $subField ) {
			$curName = (string)$subField['name'];
			$hasCurStackVal = $this->offsetExists( $curName );
			$elem = (string)$subField->elem;
			$isDiv = ( 'div' === $elem ) ? true : false;
			$isSelect = ( 'select' === $elem || 'wppageselect' === $elem || 'wptermselect' === $elem || 'wpposttypeselect' === $elem ) ? true : false;
			$isCheckbox = ( 'checkbox' === $elem ) ? true : false;
			$isText = ( 'text' === $elem ) ? true : false;
			$isTextarea = ( 'textarea' === $elem || 'wptextarea' === $elem || 'wptextareabasic' === $elem ) ? true : false;
			$hasDefault = false;			
		
			if( $subField->xpath( 'defaults/default[@lang="'.$this->locale.'"]' ) ) {
				$hasDefault = true;
				$default = $subField->xpath( 'defaults/default[@lang="'.$this->locale.'"]' );
				$v = gdprcXmlSettingsHelper::trimXmlField( $default[0] );				
			} elseif( $subField->xpath( 'default[@lang="'.$this->locale.'"]' ) ) {
				$hasDefault = true;
				$default = $subField->xpath( 'default[@lang="'.$this->locale.'"]' );
				$v = gdprcXmlSettingsHelper::trimXmlField( $default[0] );				
			} elseif( $subField->default  ) {
				$hasDefault = true;
				$v = gdprcXmlSettingsHelper::trimXmlField( $subField->default );				
			} else {
				// make sure the following elements without a default value, 
				// are being added to the stack
				if( gdprcXmlSettingsHelper::isTemplate( $subField ) ) {
					$hasDefault = true;
					$v = '';
				} elseif( $isDiv ) {
					$hasDefault = true;
					$v = '';
				} elseif( $isTextarea || $isText ) {
					$hasDefault = true;
					$v = '';
				} elseif( $isSelect ) {
					$hasDefault = true;
					$v = '-1';					
				} elseif( $isCheckbox ) {
					$hasDefault = true;
					$v = '0';					
				} elseif( gdprcXmlSettingsHelper::isClickSelect( $subField ) ) {
					$hasDefault = true;
					$v = '';					
				}			
			}
			
			if( $hasDefault ) {
				if( $isCheckbox && ( '1' === $v || '0' === $v ) ) {
					$v = ( '1' === $v ) ? true : false;
				}
				$this->offsetSet( $curName, $hasCurStackVal ? $this->offsetGet( $curName ) : $v );
			}			
		}		
	}	
	
	/**
	 * Walk threw a settings (xml) field and add I18n fields to the content returned.
	 * 
	 * Two types are used: normal and inline. Inline fields do not have 'title' and 'descr' fields.
	 * So these are only set for 'normal' fields.
	 * 
	 * @access private
	 * 
	 * @param SimpleXMLElement $field
	 * @param string $type
	 * 
	 * @since 0.1
	 *  
	 * @return string
	 */
	private function _fieldWalkerI18n( $field, $type='normal' )
	{
		$content = '';

		if( 'normal' === $type ) {	
			if( $field->title ) {
				$content .= gdprcXmlSettingsHelper::I18nField( $field->title, $this->namespace );
			}
			if( $field->descr ) {
				$content .= gdprcXmlSettingsHelper::I18nField( $field->descr, $this->namespace );
			}			
		}		
		
		if( $field->inner ) {
			$content .= gdprcXmlSettingsHelper::I18nField( $field->inner, $this->namespace );
		}
		if( $field->options->option ) {
			foreach( $field->options->option as $option ) {
				$content .= gdprcXmlSettingsHelper::I18nField( $option[0], $this->namespace );
			}
		}
	
		return $content;		
	}	
	
	/**
	 * Walk threw an (xml) settings object
	 * 
	 * @access private
	 * 
	 * @uses gdprcPluginSettings::_fieldWalkerDefaults() 
	 * 
	 * @param SimpleXMLElement $settings
	 * 
	 * @since 0.1
	 */
	private function _settingsWalker( $settings )
	{		
		foreach ( $settings->children() as $field ) {
			if( 'group' === $field->getName() || 'inline' === $field->getName() ) {			
				$this->_fieldWalkerDefaults( $field );				
			} else { 
				//node === field
				$this->_fieldWalkerDefaults( $settings );
			}
		}		
	}
		
	/**
	 * Create a php file with WordPress translation functions
	 * 
	 * This will let WordPress know about translatable strings inside the settings XML
	 * 
	 * @access private
	 * 
	 * @param SimpleXMLElement $settings
	 * 
	 * @uses gdprcXmlSettingsHelper::I18nField()
	 * @uses gdprcPluginSettings::_fieldWalkerI18n() 
	 * 
	 * @since 0.1
	 */
	private function _writeTranslations( $settings )
	{
		$path = $this->getPath();
		$basePath = dirname( $path );
		$file = "$basePath/I18n.php";		
		$name = $this->getOptionName();	
		$beginPhrase = '//BEGIN ' . $name;
		$endPhrase = '//END ' . $name;
		$content = $beginPhrase . "\n";
		$i = 1;
		clearstatcache();
		
		foreach ( $settings->children() as $field ) {
			if( 'group' === $field->getName() ) {
				foreach ( $field->children() as $subField ) {
					//check for meta fields
					if( 'group_title' === $subField->getName() ) { 
						$content .= gdprcXmlSettingsHelper::I18nField( $subField, $this->namespace ); 
					}
					if( 'group_descr' === $subField->getName() ) { 
						$content .= gdprcXmlSettingsHelper::I18nField( $subField, $this->namespace ); 
					}
					if( 'group_warning' === $subField->getName() ) { 
						$content .= gdprcXmlSettingsHelper::I18nField( $subField , $this->namespace); 
					}
					//check for inline fields
					if( 'inline' === $subField->getName() ) {
						foreach ( $subField->children() as $inlineField ) {
							if( 'inline_title' === $inlineField->getName() ) { 
								$content .= gdprcXmlSettingsHelper::I18nField( $inlineField, $this->namespace ); 
							}
							if( 'inline_descr' === $inlineField->getName() ) { 
								$content .= gdprcXmlSettingsHelper::I18nField( $inlineField, $this->namespace ); 
							}
						}						
						$content .= $this->_fieldWalkerI18n( $subField, 'inline' );					
					}//end inline						
				
					if( 'field' === $subField->getName() ) { 
						$content .= $this->_fieldWalkerI18n( $subField, 'normal' ); 
					}
									
				}//end group loop
			} elseif( 'inline' === $field->getName() ) {		
				foreach ( $field->children() as $inlineField ) {
					if( 'inline_title' === $inlineField->getName() ) { 
						$content .= gdprcXmlSettingsHelper::I18nField( $inlineField, $this->namespace ); 
					}
					if( 'inline_descr' === $inlineField->getName() ) { 
						$content .= gdprcXmlSettingsHelper::I18nField( $inlineField, $this->namespace ); 
					}
				}										
				$content .= $this->_fieldWalkerI18n( $field, 'inline' );					
			}			
			else { //node === field
				$content .= $this->_fieldWalkerI18n( $field, 'normal' );			
			}
		}
		$content .= $endPhrase. "\n";		
		
		if( file_exists( $file ) ) {			
			$curContent = file_get_contents( $file );			
			$beginPhrase = str_replace('/', '\/', $beginPhrase);
			$endPhrase =  str_replace('/', '\/', $endPhrase);			
			$pattern = '/('.$beginPhrase.'.+'.$endPhrase.')/s';
			
			if( preg_match( $pattern, $curContent, $matches) ) {
				$content = trim($content);
				$content = preg_replace($pattern, $content, $curContent);
				// be sure we have a php opening tag
				if( !preg_match('/\?php{1}/', $content) ) {
					$content = '<?php' ."\n" . $content;
				}								
			} else {				
				$content = $curContent . $content;	
				// be sure we have a php opening tag
				if( !preg_match('/\?php{1}/', $content) ) {
					$content = '<?php' ."\n" . $content;
				}				
			}								
		} else { 
			$content = '<?php' . "\n" . $content;
		}
	
		if( $handle = @fopen( $file, 'w+' ) ) {
			fwrite( $handle, $content );
			fclose($handle);
		}
	}	
}