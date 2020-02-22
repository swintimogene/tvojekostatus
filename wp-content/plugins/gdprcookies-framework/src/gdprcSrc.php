<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcSrc class
 *
 * This class provides a layer with plugin data for all src classes
 *
 * @author $Author: NULL $
 * @version $Id: gdprcSrc.php 141 2017-05-08 16:02:54Z NULL $
 *
 * @since 1.4.0
 */
class gdprcSrc
{
	/**
	 * The plugins namespace
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	public $nameSpace;
	
	/**
	 * Container for all src instances
	 * 
	 * @since 1.4.0
	 * 
	 * @var array
	 */
	public $src = array();
	
	/**
	 * Constructor
	 *
	 * @access public
	 * 
	 * @param string $nameSpace
	 * @param gdprcPluginGlobals $globals
	 * 
	 * @since 1.4.0
	 */
	public function __construct( $nameSpace = '', $globals = false ) 
	{		
		if( '' === $nameSpace ) {
			throw new Exception( 'Namespace input parameter is empty in gdprcSrc::__construct().' );	
		}
		if( !is_a( $globals, 'gdprcPluginGlobals' ) ) {
			throw new Exception( 'Globals input parameter is not valid.' );
		}
		
		$this->nameSpace = $nameSpace;
		
		$this->set( 'globals', $globals );
	}
	
	/**
	 * Create an instance
	 * 
	 * @access public
	 * 
	 * @param string $class
	 * @param array $args
	 * @param string $name
	 * @param bool $return
	 * @param bool $addGlobals
	 * 
	 * @throws Exception
	 * 
	 * @return object
	 */
	public function create( $class = '', $args = array(), $name = '', $return = true, $addGlobals = true )
	{
		if( '' === $class ) {
			throw new Exception( 'Class input parameter is empty in gdprcSrc::create().' );	
		}
		if( !class_exists( $class ) ) {
			throw new Exception( sprintf( 'Class %s does not exist. Could not create instance in gdprcSrc::create().', $class ) );
		}

		$args = (array) $args;
		if( $addGlobals ) {
			$args[] = $this->get( 'globals' );
		}
		
		$rfc = new ReflectionClass( $class );		
		$instance = $rfc->newInstanceArgs( $args );
			
		if( is_a( $instance, $class ) ) {			
			if( '' === $name ) {
				$name = $class;
			}
			if( 0 === strpos( $name,  'gdprcPlugin' ) ) {
				$name = strtolower( str_replace( 'gdprcPlugin' , '', $name ) );
			} elseif( 0 === strpos( $name,  'gdprc' ) ) {
				$name = strtolower( str_replace( 'gdprc' , '', $name ) );
			}
			
			$name = ( '' === $name ) ? $class : $name;
			$this->set( $name, $instance );
		}
		
		if( $return ) {
			return $instance;
		} else {
			unset( $instance );
		}
	}
	
	
	/**
	 * Set an src instance
	 * 
	 * @param string $key
	 * @param mixed $instance
	 * @param bool $return
	 * 
	 * @since 1.4.0
	 * 
	 * @throws Exception
	 */
	public function set( $key = '', &$instance = null, $return = false )
	{
		if( '' === $key ) {
			throw new Exception( 'Key input parameter is empty in gdprcSrc::set().' );	
		}
		if( !is_object( $instance ) ) {
			throw new Exception( 'Key input parameter is empty in gdprcSrc::set().' );
		}
		
		$this->src[$key] = &$instance;
		
		if( $return ) {
			return $instance;
		}
	}

	/**
	 * Get an src instance
	 *
	 * @param string $key
	 * @param mixed $default
	 *
	 * @uses self::exists()
	 *
	 * @since 1.4.0
	 *
	 * @return mixed|boolean, the instance or False if $key not exist and default is null
	 */
	public function get( $key = '', $default = null )
	{
		if( $this->exists( $key ) ) {
			return $this->src[$key];
		} elseif( !$this->exists( $key ) && null !== $default ) {
			return $default;
		} else {
			throw new Exception( sprintf( 'instance %s does not exist.', $key ) );			
		}
	}
	
	/**
	 * Get the stored namespace
	 * 
	 * @since 1.4.0
	 * 
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->nameSpace;
	}
	
	/**
	 * Determine if instance exists for key
	 * 
	 * @param string $key
	 * 
	 * @since 1.4.0
	 * 
	 * @return boolean
	 */
	public function exists( $key = '' )
	{
		if( isset( $this->src[$key] ) ) {
			return true;
		} else {
			return false;
		}
	}
}