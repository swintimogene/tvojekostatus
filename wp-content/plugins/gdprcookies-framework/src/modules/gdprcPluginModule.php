<?php
/**
 * Please see gdprcookies-framework.php for more details.
*/

/**
 * igdprcPluginModule interface
*
* @author $Author: NULL $
* @version $Id: gdprcPluginModule.php 141 2017-05-08 16:02:54Z NULL $
* @since 0.1
*/
interface igdprcPluginModule
{
	/**
	 * callback for [PREFIX]_module_init hook
	 *
	 * Every Module is hooked threw this init method with given $priority
	 *
	 * @since 0.1
	 */
	public function init();
}

/**
 * gdprcPluginModule Class
 *
 * Parent class for Plugin Modules
 *
 * @author $Author: NULL $
 * @version $Id: gdprcPluginModule.php 141 2017-05-08 16:02:54Z NULL $
 * @since 0.1
 */
abstract class gdprcPluginModule implements igdprcPluginModule 
{
	/**
	 * Flag if Module is active.
	 *
	 * If false, module is not loaded
	 *
	 * @since 1.0
	 *
	 * @var bool
	 */
	protected $active = true;

	/**
	 * The loading priority for the Module
	 *
	 * 1 will load first, 2 after etc.
	 *
	 * @since 0.1
	 *
	 * @var int
	 */
	protected $priority = 10;

	/**
	 * The index for the Module
	 *
	 * @since 0.1
	 *
	 * @var string
	 */
	protected $index;

	/**
	 * Absolute path to the Module
	 *
	 * @since 0.1
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Uri to the module
	 *
	 * @since 0.1
	 *
	 * @var string
	 */
	protected $uri;


	/**
	 * Data that is passes to the Module
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	protected $vars;

	/**
	 * Flag if Plugin is being activated
	 *
	 * @since 1.1.8
	 *
	 * @var bool
	 */
	protected $activating = false;

	/**
	 * Flag if module has settings dir
	 *
	 * @since 1.2
	 *
	 * @var bool
	 */
	protected $hasSettingsDir = false;

	/**
	 * path to settings dir if exist
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	protected $settingsDir = '';

	/**
	 * Instance of gdprcMultilangProcessor class
	 *
	 * @since 1.2
	 *
	 * @var gdprcMultilangProcessor
	 */
	protected $multilang;

	/**
	 * Instance of gdprcPluginSettingsPage class
	 *
	 * @since 1.2
	 *
	 * @var gdprcPluginSettingsPage
	 */
	protected $settingsPage;

	/**
	 * Instance of gdprcPluginSettingsProcessor class
	 *
	 * @since 1.2
	 *
	 * @var gdprcPluginSettingsProcessor
	 */
	protected $settingsProcessor;
	
	/**
	 * Instance of gdprcPluginGlobals class
	 * 
	 * @since 1.4.0
	 * 
	 * @var gdprcPluginGlobals
	 */
	protected $globals;

	/**
	 * Set class member $status
	 *
	 * @access public
	 * 
	 * @param bool $status
	 *
	 * @since 1.0
	 */
	public function setActive( $status )
	{
		$this->active = $status;
	}

	/**
	 * Set class member $priority
	 *
	 * @access public
	 * 
	 * @param int $priority
	 *
	 * @since 0.1
	 */
	public function setPriority( $priority )
	{
		$this->priority = $priority;
	}

	/**
	 * Set class member $index
	 *
	 * @access public
	 * 
	 * @param string $index
	 *
	 * @since 0.1
	 */
	public function setIndex( $index )
	{
		$this->index = $index;
	}

	/**
	 * Set class member $path
	 *
	 * @access public
	 * 
	 * @param string $path
	 *
	 * @since 0.1
	 */
	public function setPath( $path )
	{
		$this->path = $path;
	}

	/**
	 * Set class member $uri
	 *
	 * @access public
	 * 
	 * @param string $uri
	 *
	 * @since 0.1
	 */
	public function setUri( $uri )
	{
		$this->uri = $uri;
	}

	/**
	 * Set class member $vars
	 *
	 * @access public
	 * 
	 * @param array $vars
	 *
	 * @since 1.0
	 */
	public function setVars( $vars )
	{
		$this->vars = $vars;
	}

	/**
	 * Set class member $activating
	 *
	 * @access public
	 * 
	 * @param bool $activating
	 *
	 * @since 1.1.8
	 */
	public function setActivating( $activating = false )
	{
		$this->activating = $activating;
	}

	/**
	 * Set class member $hasSettingsDir and $settingsDir
	 *
	 * @access public
	 *
	 * @since 1.2
	 */
	public function setHasSettings( $dir )
	{
		$this->hasSettingsDir = ( is_dir( $dir ) ) ? true : false;
		
		if( $this->hasSettingsDir ) {
			$this->settingsDir = $dir;
		}
	}

	/**
	 * Set class member $multilang
	 *
	 * @access public
	 *
	 * @param gdprcMultilangProcessor $multilang
	 *
	 * @since 1.2
	 */
	public function setMultilang( &$multilang )
	{
		$this->multilang = $multilang;
	}

	/**
	 * Set class member $settingsPage
	 *
	 * @access public
	 *
	 * @param gdprcPluginSettingsPage $settingsPage
	 *
	 * @since 1.2
	 */
	public function setSettingsPage( &$settingsPage )
	{
		$this->settingsPage = $settingsPage;
	}

	/**
	 * Set class member $settingsProcessor
	 *
	 * @access public
	 *
	 * @param gdprcPluginSettingsProcessor $settingsProcessor
	 *
	 * @since 1.2
	 */
	public function setSettingsProcessor( &$settingsProcessor )
	{
		$this->settingsProcessor = $settingsProcessor;
	}

	/**
	 * Set class member $globals
	 * 
	 * @since 1.4.0
	 * 
	 * @param gdprcPluginGlobals $globals
	 */
	public function setGlobals( &$globals )
	{
		$this->globals = $globals;
	}
	
	/**
	 * Get class member $active
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function getActive()
	{
		return $this->active;
	}

	/**
	 * Get class member $priority
	 *
	 * @access public
	 *
	 * @since 0.1
	 *
	 * @return int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * Get class member $index
	 *
	 * @access public
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getIndex()
	{
		return $this->index;
	}

	/**
	 * Get class member $path
	 *
	 * @access public
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Get class member $uri
	 *
	 * @access public
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * Get class member $vars
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function getVars()
	{
		return $this->vars;
	}

	/**
	 * Get the settings dir
	 *
	 * @access public
	 *
	 * @since 1.2
	 *
	 * @return string
	 */
	public function getSettingsDir()
	{
		return $this->settingsDir;
	}

	/**
	 * Return the module has a settings dir
	 *
	 * @access public
	 *
	 * @since 1.2
	 *
	 * @return bool
	 */
	public function hasSettingsDir()
	{
		return $this->hasSettingsDir;
	}

	/**
	 * Callback for hook {namespace}_module_init_activate
	 *
	 * @access public
	 *
	 * @since 1.2
	 */
	public function activating() {}

	/**
	 * Replacement of class constructor
	 *
	 * Modules could use this method to do some contructor like tasks
	 *
	 * @access public
	 *
	 * @since 1.2
	 */
	public function start() {}
}

class gdprcBoldPluginModule {
	protected $active = false;
	public function getActive()
	{
		return $this->active;
	}	
}