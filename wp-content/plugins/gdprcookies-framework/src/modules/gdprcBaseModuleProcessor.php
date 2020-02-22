<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * iBaseModuleProcessor interface
 *
 * @author $Author: NULL $
 * @version $Id: gdprcBaseModuleProcessor.php 141 2017-05-08 16:02:54Z NULL $
 * @since 0.1
 */
interface igdprcModuleProcessor 
{	
	public function setModule( $ns, $key, $module );
	public function setPriority( $key, $priority = 10 );
	public static function getModule( $ns, $key );
	public function getModules( $ns );	
	public function includeModules();			
}

/**
 * gdprcBaseModuleProcessor Class
 * 
 * Class for handling Plugin Modules
 *
 * @author $Author: NULL $
 * @version $Id: gdprcBaseModuleProcessor.php 141 2017-05-08 16:02:54Z NULL $
 * @since 0.1
 */
abstract class gdprcBaseModuleProcessor implements igdprcModuleProcessor 
{	
	/**
	 * The root URI for all Modules
	 * 
	 * @since 1.0
	 * 
	 * @var string
	 */
	protected $rootUri;	
	
	/**
	 * The extension that all Modules should have
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected $ext;	
	
	/**
	 * The namespace for all Modules
	 * 
	 * @since 1.0.3
	 * 
	 * @var string
	 */
	protected $nameSpace;	
	
	/**
	 * All founded Modules
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	protected $moduleFiles = array();	
	
	/**
	 * Flag if any Modules are found
	 * 
	 * @since 1.0
	 * 
	 * @var bool
	 */
	protected $hasModules = false;	
	
	/**
	 * Flag if all Modules were included
	 *
	 * @since 1.1.8
	 *
	 * @var bool
	 */
	protected $modulesIncluded = false;		
	
	/**
	 * The root path for all Modules
	 *
	 * @since 0.1
	 *
	 * @var string
	 */
	public $rootPath;	
	
	/**
	 * All module priorities
	 * 
	 * @since 1.0
	 * 
	 * @var array
	 */
	public static $priorities = array();	

	/**
	 * All Module instances
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	public static $modules = array();
	
	/**
	 * Constructor
	 * 
	 * @since 0.1
	 */
	public function __construct( $rootPath = '', $rootUri = '', $ext = '', $nameSpace = '' )
	{
		$this->_setRootPath( $rootPath );
		$this->_setRootUri( $rootUri );
		$this->_setExt( $ext );
		$this->_setNamespace( $nameSpace );
	}	
	
	/**
	 * Check if a Module exists
	 * 
	 * @access public
	 * 
	 * @param string $ns
	 * @param string $key
	 * 
	 * @since 1.1.3
	 * 
	 * @return bool
	 */
	public static function hasModule( $ns, $key )
	{
		return ( isset( self::$modules[$ns][$key] ) ) ? true : false;		
	}
	
	/**
	 * Check if Modules exists
	 *
	 * @access public
	 *
	 * @param string $ns
	 *
	 * @since 1.4.0
	 *
	 * @return bool
	 */
	public static function hasModules( $ns )
	{
		return ( isset( self::$modules[$ns] ) ) ? true : false;
	}	
	
	/**
	 * Set a module
	 * 
	 * @access public
	 * 
	 * @param string $ns
	 * @param string $key
	 * @param WpPluginModule $module
	 * 
	 * @since 0.1
	 */
	public function setModule( $ns, $key, $module )
	{
		self::$modules[$ns][$key] = $module;		
	}	
	
	/**
	 * Set module priority to $priorities array
	 * 
	 * @access public
	 * 
	 * @param string $key
	 * @param int $module
	 * 
	 * @since 1.0
	 */
	public function setPriority( $key, $priority=10 )
	{
		self::$priorities[$key] = $priority;		
	}	

	/**
	 * Get all modules from the DB option
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 */
	abstract public function findModules();	

	/**
	 * Get all Module files
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	public function getModuleFiles() 
	{
		return $this->moduleFiles;
	}	
	
	/**
	 * Get a module from the stack
	 * 
	 * @access public
	 * 
	 * @param string $ns
	 * @param string $key
	 * 
	 * @since 0.1
	 * 
	 * @return WpPluginModule
	 */
	public static function getModule( $ns, $key )
	{
		return self::$modules[$ns][$key];		
	}	
	
	/**
	 * Get all Modules
	 * 
	 * @access public
	 * 
	 * @param string $ns
	 * 
	 * @since 0.1
	 * 
	 * @return array, empty array with no modules 
	 */
	public function getModules( $ns )
	{	
		if( self::hasModules( $ns ) ) {
			return self::$modules[$ns];		
		} else {
			return array();
		}
	}	
	
	/**
	 * Get root Path 
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function getRootPath()
	{
		return $this->rootPath;		
	}	
	
	/**
	 * Get root URI
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	protected function getRootUri()
	{
		return $this->rootUri;
	}		
	
	/**
	 * Get module generic ext (like .php)
	 *
	 * @access protected
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected function getExt()
	{
		return $this->ext;
	}
	
	/**
	 * Get module generic namespace
	 * 
	 * @access protected
	 * 
	 * @since 1.0.3
	 * 
	 * @return string
	 */
	protected function getNamespace()
	{
		return $this->nameSpace;		
	}	
	
	/**
	 * Include the module(s)
	 * 
	 * @access public
	 * 
	 * @since 0.1
	 */
	public function includeModules()
	{		
		$moduleFiles = $this->moduleFiles;		
		$missing = array();
		
		if( !empty( $moduleFiles ) ) {
			ob_start();
			foreach ( $moduleFiles as $k => $file ) {			
				$path = "{$this->rootPath}/$file";
				
				if( !is_readable( $path ) ) {
					$missing[] = $file;
				} else {
					include_once $path;
				}
			}
			
			$err = ob_get_contents();
			ob_end_clean();
			
			if( '' !== $err ) {
				throw new Exception( sprintf( 'Something went wrong during including the modules:' . "\n" . '%s' , $err ) );
			}			
			if( !empty( $missing ) ) {
				throw new Exception( sprintf( 'The following modules are missing: %s.', join(', ', $missing ) ) );
			} else {
				$this->modulesIncluded = true;
			}
		}				
	}	
		
	/**
	 * Set the root path to the modules folder
	 * 
	 * @access private
	 * 
	 * @param string $rootPath
	 * 
	 * @throws Exception if path is empty
	 * 
	 * @since 0.1
	 */
	private function _setRootPath( $path ) 
	{
		if( '' === $path ) {
			throw new Exception( 'input parameter $rootPath is empty.' );
		} else {
			$this->rootPath = $path;
		}
	}	
	
	/**
	 * Set the URI to the modules folder
	 * 
	 * @access private
	 * 
	 * @param string $rootPath
	 * 
	 * @throws Exception if uri is empty
	 * 
	 * @since 1.0
	 */
	private function _setRootUri( $uri ) 
	{
		if( '' === $uri ) {
			throw new Exception( 'input parameter $rootUri is empty.' );
		} else {
			$this->rootUri = $uri;
		}
	}	
	
	/**
	 * Set the file extension of the module
	 * 
	 * @access private
	 * 
	 * @param string $ext
	 * 
	 * @throws Exception if ext is empty else string extension
	 * 
	 * @since 0.1
	 */
	private function _setExt( $ext ) 
	{
		if( '' === $ext ) {
			throw new Exception( 'input parameter $ext is empty.' );
		} else {
			$this->ext = $ext;
		}
	}	
	
	/**
	 * Set the namespace of the module
	 * 
	 * All modules should have the same prefix
	 * 
	 * @access private
	 * 
	 * @param string $nameSpace
	 * 
	 * @throws Exception if namespace is empty else string prefix
	 * 
	 * @since 1.0.3
	 */
	private function _setNamespace( $nameSpace )
	{
		if( '' === $nameSpace ) {
			throw new Exception( 'input parameter $ns is empty.' );
		} else {
			$this->nameSpace = $nameSpace;
		}
	}	
}