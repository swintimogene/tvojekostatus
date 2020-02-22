<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcPluginModuleTemplateProcessor Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcPluginModuleTemplateProcessor.php 132 2017-05-03 20:07:38Z NULL $
 * 
 * @since 0.1
 */
class gdprcPluginModuleTemplateProcessor 
{	
	/**
	 * The templates file name
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	public $fileName;	
	
	/**
	 * The templates default absolute path
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	public $defaultPath;
		
	/**
	 * Other optional path(s)
	 * 
	 * These path(s) can overide the default template path
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	public $otherPaths;		
	
	/**
	 * Flag if template is found
	 * 
	 * @since 0.1
	 * 
	 * @var bool
	 */
	public $haveTemplate = false;
	
	/**
	 * The final template path 
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	public $templatePath = null;	
	
	/**
	 * Constructor
	 * 
	 * @access public
	 * 
	 * @param string $fileName
	 * @param string $defaultPath
	 * @param (array|string) $otherPaths
	 * 
	 * @since 0.1
	 */
	public function __construct( $fileName = '', $defaultPath = '', $otherPaths = array() )
	{
		$this->fileName = $fileName;
		$this->defaultPath  = $defaultPath;
		$this->otherPaths = (array) $otherPaths;		
	}
	
	
	/**
	 * Get the templates file name
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return string the file name
	 */
	public function getFileName()
	{
		return $this->fileName;
	}
	
	
	/**
	 * Get the templates default path
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return string the defaultPath
	 */
	public function getDefaultPath()
	{
		return $this->defaultPath;
	}
	
	
	/**
	 * Get the optional templates path(s)
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return array with paths
	 */
	public function getOtherPaths()
	{
		return $this->otherPaths;
	}	
	
	
	/**
	 * Choose a template 
	 * 
	 * First take a look at other path(s) locations. If a template file is found, this file will be used.
	 * Otherwise the default path will be used.
	 * 
	 * When a file is found, the {@link gdprcPluginModuleTemplateProcessor::haveTemplate} flag is set to true.
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if a path is set, false otherwise
	 */
	public function choose()
	{		
		if( !empty( $this->otherPaths) ) {			
			foreach( $this->otherPaths as $path ) {
				if($template = realpath($path.'/'.$this->fileName)) {				
					if( file_exists($template) ) {
						$this->haveTemplate = true;
						$this->templatePath = $path;
						break;
					}				
				}					
			}			
		}
			
		if( false === $this->haveTemplate ) {
			if($template = realpath($this->defaultPath.'/'.$this->fileName)) {				
				if( file_exists($template) ) {
					$this->haveTemplate = true;
					$this->templatePath = $this->defaultPath;					
				}				
			}				
		}

		return ( true === $this->haveTemplate ) ? $this->templatePath : false;		
	}	
}