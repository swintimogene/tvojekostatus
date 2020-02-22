<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * igdprcTemplate interface
 *
 * @author $Author: NULL $
 * @version $Id: gdprcTemplate.php 132 2017-05-03 20:07:38Z NULL $
 * @since 0.1
 */
interface igdprcTemplate 
{		
	public function setVar( $key, $value );	
	public function setTag( $key, $value );
	public function getName();	
	public function getVars();
	public function getVar( $key );	
	public function getTags();	
	public function getTag( $key );	
	public function getChilds();	
	public function getChild( $key );	
	public function getContent();
	public function render( $exit = false, $return = false, $save = false );		
}

/**
 * Final Class gdprcTemplate
 * 
 * Class for handling template related action. Templates can have a parent -> child relation. 
 * Child templates inherit parent vars
 * 
 * Main functionality is: 
 * - Adding tags to the template like {tag}.  
 * - Adding vars to the template
 * - Rendering the template file
 *
 * @author $Author: NULL $
 * @version $Id: gdprcTemplate.php 132 2017-05-03 20:07:38Z NULL $
 * 
 * @since 0.1
 */
final class gdprcTemplate implements igdprcTemplate 
{
	/**
	 * The name of the template
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */	
	protected $name;	
	
	/**
	 * The base file path
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected $basePath;	
	
	/**
	 * The filename
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected $fileName;	
	
	/**
	 * The full path to the file
	 *
	 * @since 0.1
	 *
	 * @var string
	 */
	protected $filePath;		
	
	/**
	 * Holder for the template vars
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	protected $vars = array();	
	
	/**
	 * Holder for the template tags
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	protected $tags = array();	
	
	/**
	 * Attached parent gdprcTemplate instance
	 * 
	 * @since 0.1
	 * 
	 * @var bool
	 */
	protected $parent = false;	
	
	/**
	 * Flag if template has a parent
	 * 
	 * @since 0.1
	 * 
	 * @var bool
	 */
	protected $hasParent = false;	
	
	/**
	 * Flag if template is a child
	 * 
	 * @since 0.1
	 * 
	 * @var bool
	 */
	protected $isChild = false;	
	
	/**
	 * Attached child gdprcTemplate instances
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	protected $childs = array();	
	
	/**
	 * Flag if template has child(s)
	 * 
	 * @since 0.1
	 * 
	 * @var bool
	 */
	protected $hasChilds = false;	
	
	/**
	 * Content of the template
	 * 
	 * This will hold the template content if rendering is set to save
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected $content;	
	
	/**
	 * Flag if template exist
	 * 
	 * @since 0.1
	 * 
	 * @var bool
	 */
	protected $templateExist = false;	
	
	/**
	 * Flag if file should be created if not exists
	 * 
	 * @since 1.2
	 * 
	 * @var bool
	 */
	protected $createFileIfNotExist = true;	
	
	/**
	 * Constructor
	 * 
	 * @access public
	 * 
	 * @param string $name
	 * @param string $path
	 * @param string $fileName
	 * 
	 * @since 0.1
	 */
	public function __construct( $name = '', $path = '', $fileName = '', $createFileIfNotExist = true )
	{
		try {			
			$this->createFileIfNotExist = $createFileIfNotExist;
			
			$this->setName( $name );
			$this->setBasePath( $path );
			$this->setFileName( $fileName );
			$this->setFilePath();
			$this->setExistFlag();
			
		} catch( Exception $e ) {	
			if( is_admin() ) {
				gdprcNotices::add( '', $e->getMessage() );
			}
		}
	}		

	/**
	 * Add a single var to the vars holder
	 * 
	 * @access public
	 *  
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @since 0.1
	 */
	public function setVar( $key, $value )
	{
		$this->vars[$key] = $value;
	}	

	/**
	 * Add an array to the vars holder
	 * 
	 * @access public
	 * 
	 * @param array $vars
	 * 
	 * @since 0.1
	 */
	public function setVars( $vars )
	{
		if( is_array( $vars ) ) {
			$this->vars = array_merge( $this->vars, $vars );
		}
	}

	/**
	 * Set the parent gdprcTemplate instance
	 * 
	 * Some flags are also set
	 * 
	 * @access public 
	 * 
	 * @param gdprcTemplate $parent
	 * 
	 * @since 0.1
	 */
	public function setParent( $parent )
	{
		if( is_object( $parent ) ) {
			$this->hasParent = true;
			$this->parent = $parent;
		}
	}
		
	/**
	 * Add a gdprcTemplate instance to the childs holder
	 * 
	 * Some flags are also set 
	 * 
	 * @access public
	 * 
	 * @param gdprcTemplate $child
	 * 
	 * @since 0.1
	 */
	public function setChild( $child )
	{ 
		if( is_object( $child ) ) {
			$child->isChild = true;
			$child->setParent($this);									
			$this->hasChilds = true;
			$this->childs[$child->getName()] = $child;
		}		
	}	

	/**
	 * Add a template tag to the tags holder
	 * 
	 * Tags are placeholders for substituting data (variables) in the template
	 * The tag keys have the format: {tag}
	 * 
	 * @access public 
	 * 
	 * @param string $key
	 * @param string $value
	 * 
	 * @since 0.1
	 */
	public function setTag( $key, $value )
	{
		$this->tags[$key] = $value;
	}		
	
	/**
	 * Set the content of the template
	 *
	 * @access protected 
	 *
	 * @param string $content
	 * 
	 * @since 0.1
	 */
	protected function setContent( $content )
	{
		$this->content = $content;
	}	
	
	/**
	 * Set the name of the template
	 *
	 * @access protected
	 *
	 * @param string $name
	 * @throws Exception
	 * 
	 * @since 0.1
	 */
	protected function setName( $name = '' )
	{
		if( '' === $name ) {
			throw new Exception( 'input parameter $name is empty.' );
		} else {
			$this->name = $name;
		}
	}	
	
	/**
	 * Set the absolute path of the template
	 * 
	 * @access protected
	 *
	 * @param string $path
	 * @throws Exception
	 * 
	 * @since 0.1
	 */
	protected function setBasePath( $path = '' )
	{		
		if( '' === $path ) {
			throw new Exception( 'input parameter $path is empty.' );
		} else {
			//@since 1.2.1
			//remove leading slash /
			$path = rtrim( $path, '/' );
			$this->basePath = $path;
		}	
	}	
	
	/**
	 * Set the fileName of the template
	 * 
	 * @access protected
	 * 
	 * @param string $fileName
	 * @throws Exception
	 * 
	 * @since 0.1
	 */
	protected function setFileName( $fileName = '' )
	{
		if( '' === $fileName ) {
			throw new Exception( 'input parameter $fileName is empty.' );
		} else {			
			$this->fileName = $fileName;
		}
	}	
	
	/**
	 * Set the complete path to the template file
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 */
	protected function setFilePath()
	{
		$this->filePath = ( '' !== $this->basePath ) ? $this->basePath . '/' . $this->fileName : $this->fileName;
	}	
	
	/**
	 * Set the templateExist flag
	 * 
	 * If flag self::createFileIfNotExist is true, the file will be created if not exists
	 * 
	 * @access protected
	 * 
	 * @uses clearstatcache 
	 * 
	 * @throws Exception if file does not exist
	 * 
	 * @since 0.1
	 */
	protected function setExistFlag()
	{
		clearstatcache();
		
		$exist = file_exists( $this->filePath );
		
		if( true === $exist ) {
			$this->templateExist = true;
		} elseif( false === $exist && $this->createFileIfNotExist ) {
			$h = @fopen( $this->filePath, 'w' );
			if(false !== $h) {
				$h = fclose( $h );
				$this->templateExist = true;
			} else {
				$this->templateExist = false;
			}
		} else {
			$this->templateExist = false;
		}

		if( false === $this->templateExist ) {
			throw new Exception( sprintf( 'Template file does not exist or couldn\'t be created due to file permissions on path or invalid path "%s".' , $this->filePath ) );
		}
	}		
	
	/**
	 * Get the template name
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return string the template name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get all template vars
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return array with vars
	 */
	public function getVars()
	{
		return $this->vars;
	}

	/**
	 * Get a single var from the vars holder
	 * 
	 * @access public
	 * 
	 * @param string $key
	 * 
	 * @since 0.1
	 * 
	 * @return mixed
	 */
	public function getVar( $key )
	{
		return $this->vars[$key];
	}

	/**
	 * Get all template tags
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return array with tags
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * Get a single tag from the tags holder 
	 * 
	 * @access public
	 * 
	 * @param string $key
	 * 
	 * @since 0.1
	 * 
	 * @return string the tag value
	 */
	public function getTag( $key )
	{
		return $this->tags[$key];
	}

	/**
	 * Get attached child templates
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return array with child templates
	 */
	public function getChilds()
	{
		return $this->childs;
	}

	/**
	 * Get a single attached child template
	 * 
	 * @access public
	 * 
	 * @param string $key
	 * 
	 * @since 0.1
	 * 
	 * @return gdprcTemplate instance
	 */
	public function getChild( $key )
	{
		return $this->childs[$key];
	}

	/**
	 * Get template content
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}	
	
	/**
	 * Get the template path
	 *
	 * @access protected
	 * 
	 * @since 0.1
	 *
	 * @return string the base path
	 */
	protected function getBasePath()
	{
		return $this->basePath;
	}
		
	/**
	 * Get the complete path to the template file
	 * 
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string the file path
	 */
	protected function getFilePath()
	{
		return $this->filePath;
	}	
		
	/**
	 * Get the template file name
	 *
	 * @access protected
	 * 
	 * @since 0.1
	 *
	 * @return string the file name
	 */
	protected function getFileName()
	{
		return $this->fileName;
	}
	
	/**
	 * Remove new line characters
	 * 
	 * @access protected
	 * 
	 * @param string $template
	 * 
	 * @since
	 */
	protected function removeNewLines( &$template ) 
	{
		$template = trim( $template );
		$template = trim( preg_replace( '/\s\s+/', ' ', $template ) );
	}	
	
	/**
	 * Apply some cleaning up to the template content (passed by reference)
	 *
	 * @param string $template the Template passed by refernce
	 * 
	 * @uses trim()
	 * @uses preg_replace()
	 * 
	 * @since 0.1
	 */
	protected function escape( &$template, $removeNewLines = false )
	{
		$template = trim( $template );
		$template = preg_replace( '/>\s+</', '><', $template );
	}
		
	/**
	 * Substitute the template tags in the template (passed by reference)
	 *
	 * @access protected
	 *
	 * @param string $template the Template passed by refernce
	 * 
	 * @uses str_replace()
	 * 
	 * @since 0.1
	 */
	protected function substituteTags( &$template )
	{
		$tags = $this->getTags();
		
		if( !empty( $tags ) ) {
			$tagNames = $tagValues = array();
			
			foreach( $tags as $name => $value ) {
				$tagNames[] = '{'.$name.'}';
				$tagValues[] = $value;
			}
			
			$template = str_replace( $tagNames, $tagValues, $template );
		}
	}

	/**
	 * Render the template content
	 * 
	 * @access public
	 * 
	 * @param bool $exit
	 * @param bool $return
	 * @param bool $save
	 * @throws Exception if template path is not found
	 * @return string when $return is set to true
	 * 
	 * @since 0.1
	 * 
	 * @todo test Exception whit wrong path and return === true	
	 */
	public function render( $exit = false, $return = false, $save = false, $removeNewLines = false )
	{
		try {
			if( false === $this->templateExist ) {
				throw new Exception( sprintf( 'Template does not exist or couldn\'t be loaded due to file permissions on path "%s".' , $path ) );
			}
					
			$path = $this->getFilePath();	
			$vars = $this->getVars();
		
			if( true === $this->isChild ) {			
				$parentVars = $this->parent->getVars();				
				$vars = array_merge( $vars, $parentVars );
			}			
			if( is_array( $vars ) ) {
				extract( $vars );
			}		
			if( true === $this->hasChilds ) {
				$childTemplates = $this->getChilds();
			}		

			ob_start();
			if( @!include $path ) {				
				throw new Exception( sprintf( 'Template not found on: %s' , $path ) );
			} else {				
				$template = ob_get_contents();
				// @since 1.2 use ob_get_clean()
				// instead of ob_end_clean() 
				ob_get_clean();
				
				$this->substituteTags( $template );					
				$this->escape( $template );

				if( $removeNewLines ) {
					$this->removeNewLines( $template );
				}				
				if( true === $save ) {
					$this->setContent( $template );
				}

				unset( $vars );
				
				if( false === $return ) {
					echo $template;
					if( true === $exit ) { 
						exit();
					}
				} else {
					return $template;
				}
			}	
		} catch( Exception $e ) {	
			if( is_admin() ) {
				gdprcNotices::add( '', $e->getMessage() );
			}
		}
	}	
	
	/**
	 * Get a template by path
	 * 
	 * @access public
	 * 
	 * @param string $path the absolute path to the template
	 * @param array $vars
	 * 
	 * @throws Exception if template is not found
	 * 
	 * @since 1.0
	 */
	public static function get( $path = '', $vars = array() )
	{		
		if( is_array( $vars ) ) {
			extract( $vars );
		}			
		
		try {	
			ob_start();
			
			if( !include $path ) {
				throw new Exception( sprintf( 'Template not found on: %s' , $path ) );
			} else {
				$template = ob_get_contents();
				ob_end_clean();
				
				self::escape( $template );
				unset( $vars );				
				
				echo $template;
			}	
		} catch( Exception $e ) {	
			if( is_admin() ) {
				gdprcNotices::add( '', $e->getMessage() );
			}
		}		
	} 
}