<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcMiscHelper class
 * 
 * Helper class that helps with various often uses functions
 *  
 * @author $Author: NULL $
 * @version $Id: gdprcMiscHelper.php 167 2018-02-24 23:09:06Z NULL $
 * 
 * @since 1.0.8
 */
class gdprcMiscHelper 
{	
	/**
	 * Construct a post edit url based on a base url 
	 * 
	 * @access public
	 * 
	 * @param string $uriBase
	 * @param WP_Post $post
	 * 
	 * @uses add_query_arg()
	 * @uses admin_url()
	 * @uses esc_url()
	 * 
	 * @return bool false on failure, the escaped url otherwise
	 * 
	 * @since 1.0.8
	 */
	public static function getEditUri( $uriBase = '', $post = false, $args = array() ) 
	{	
		if( !is_a( $post, 'WP_Post' ) || '' === $uriBase )
			return false;
	
		$uri = add_query_arg( array( 'post' => $post->ID ), $uriBase );
		
		if( is_array( $args ) && !empty( $args ) ) {
			
			foreach ( $args  as $name => $value ) {
				
				if( is_string( $name ) && is_string( $value ) )				
					$uri = add_query_arg( array( $name => $value ), $uri );
			}
		}
		
		if( false === strpos( $uri, 'http://' ) )
			$uri = esc_url( admin_url( $uri ) );
		else
			$uri = esc_url( $uri );
	
		return $uri;
	}
	
	
	
	/**
	 * Get a file extension (without dot)
	 * 
	 * @access public
	 * 
	 * @param string $file
	 * 
	 * @since 1.2
	 *  
	 * @return bool false on failure, otherwise string the file extension 
	 */
	public static function getFileExt( $file = '' ) 
	{
		if( '' === $file || !is_string( $file ) )
			return false;
		
		
		$fileParts = explode( '.', $file );
		$ext = array_pop( $fileParts );
		
		return $ext;		
	}
	
	
	/**
	 * Is the current page a login/register page
	 * 
	 * @access public
	 * 
	 * @since 1.1.8
	 * 
	 * @return bool
	 */
	public static function isLoginPage() {
		return in_array( $GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php') );
	}
	

	/**
	 * Convert hexadecimal to rgb color
	 *
	 * @access public
	 * 
	 * @uses hexdec()
	 *
	 * @param string $color the hexadecimal color
	 *
	 * @since 1.1.8
	 *
	 * @return bool false on failure else an rgb indexed array
	 */
	public static function hex2rgb( $color )
	{
		if ( '#' === $color[0] && self::isValidHexColor( $color ) ) {
			$color = substr( $color, 1 );
		}
		if ( strlen( $color ) == 6 ) {
			list( $r, $g, $b ) = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			list( $r, $g, $b ) = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return false;
		}
		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );
	
		return array( 'r' => $r, 'g' => $g, 'b' => $b );
	}	
	
	
	/**
	 * Check if color is valid hexadecimal color format
	 * 
	 * @param string $color
	 * 
	 * @since 1.2
	 *  
	 * @see wp-includes/class-wp-customize-manager.php 
	 *  
	 * @return bool
	 */
	public static function isValidHexColor( $color = '' ) 
	{
		return ( '' === $color || !preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) ? false : true;
	}
	
	
	
	/**
	 * Get a URL without the Schema and or path, query str etc.
	 * 
	 * @access public
	 * 
	 * @param string $uri
	 * 
	 * @since 1.1.8
	 * 
	 * @return string
	 */
	public static function getCleanUri( $uri = '' )
	{
		if( !is_string( $uri ) )
			return '';
		
		$uri = preg_replace( '#^https?://([^/?]+)(.*)$#' , '${1}', $uri );
		
		return ( null === $uri ) ? '' : $uri;
	}
	
	
	/**
	 * Get the domain.com part from a URL
	 * 
	 * @access public
	 * 
	 * @param string $uri
	 * 
	 * @since 1.2.7
	 * 
	 * @return string
	 */
	public static function getHostWithoutSubdomain( $uri = '' ) 
	{
		if( !is_string( $uri ) )
			return '';		
		
		$host = $hostWithoutSub = '';
		
		if( function_exists( 'wp_parse_url' ) )
			$parsedUri = @wp_parse_url( $uri );
		else 
			$parsedUri = @parse_url( $uri );
		
		if(isset($parsedUri['host']) && false !== $parsedUri ) {
			$host = $parsedUri['host'];
		} elseif(isset($parsedUri['path']) && false !== $parsedUri ) {
			$host = $parsedUri['path'];
		} else {
			$host = $uri;
		}
			
		if( false !== strpos($host, '.') ) {
			$parts = explode(".",$host);
			$hostWithoutSub =  $parts[count($parts)-2] . "." . $parts[count($parts)-1];
		} else {
			$hostWithoutSub = $host;
		}
		
		return $hostWithoutSub;
	}
	
	
	/**
	 * Get plugin header file data
	 * 
	 * @access public
	 * 
	 * @param string $file
	 * @param string $needle
	 * 
	 * @uses get_plugin_data()
	 * 
	 * @since 1.2
	 * 
	 * @return mixed
	 */
	public static function getPluginData( $file = '', $needle = '' )
	{
		static $data = array();
		
		if( '' === $file || '' === $needle )
			return false;
		
		// only call get_plugin_data() onces per page request
		if( !isset( $data[$file] ) ) {
			
			if( function_exists( 'get_plugin_data' ) ) {
				$data[$file] = get_plugin_data( $file, false );
			} elseif( function_exists( 'get_file_data' ) ) {
				
				// copied from get_plugin_data()
				$headers = array(
						'Name' => 'Plugin Name',
						'PluginURI' => 'Plugin URI',
						'Version' => 'Version',
						'Description' => 'Description',
						'Author' => 'Author',
						'AuthorURI' => 'Author URI',
						'TextDomain' => 'Text Domain',
						'DomainPath' => 'Domain Path',
						'Network' => 'Network',
						// Site Wide Only is deprecated in favor of Network.
						'_sitewide' => 'Site Wide Only',
				);
				
				$data[$file] = get_file_data( $file, $headers, 'plugin' );
			}
		}			
		
		if( isset( $data[$file][$needle] ) )
			return $data[$file][$needle];
		else
			return false;
	}
	
	
	/**
	 * Delete files in a given dir and sub dir
	 * 
	 * @param 	string $path
	 * @param 	string $ext
	 * @param 	string $prefix (optional)
	 * 
	 * @uses	self::findFiles()
	 * 
	 * @since	1.2.6
	 * 
	 * @return 	bool false on failure or if no files are found
	 */
	public static function deleteFiles( $path = '', $ext = '', $prefix = null ) 
	{		
		$found = self::findFiles( $path, $ext, $prefix );		
		$deleted = array();
		if( false === $found )
		{
			return false;
		}
		elseif ( is_array( $found ) && !empty( $found ) )
		{
			clearstatcache();
			foreach ( $found as $file ) {
				$fullPath = "$path/$file";
				if ( is_readable( $fullPath ) ) 
				{	
					$deleted[] = @unlink( $fullPath );					
				}
			}
		}
		
		return ( !empty( $deleted ) && in_array( false , $deleted, true ) ) ? false : true;		
	}
	
	
	
	/**
	 * Search a dir and subdirs to find files with:
	 * 
	 * @access public
	 *
	 * @param string $path
	 * @param string $ext
	 * @param string $prefix (optional)
	 *
	 * @since 1.2
	 *
	 * @return array with fouded files or bool false if directory couldnt be opened
	 */	
	public static function findFiles( $path, $ext, $prefix = null )
	{
		$dir = @ opendir( $path );
		$found = array();		
		$lenExt = strlen( $ext );
		$lenPrefix = strlen( $prefix );
		
		if ( $dir )
		{
			while ( ( $file = readdir( $dir ) ) !== false )
			{
				if ( substr($file, 0, 1) === '.' )
					continue;
				if ( is_dir( $path.'/'.$file ) )
				{
					$subdir = @ opendir( $path.'/'.$file );
					if ( $subdir ) {
						while ( ( $subfile = readdir( $subdir ) ) !== false )
						{
							if ( substr( $subfile, 0, 1 ) === '.' )
								continue;
							if ( null !== $prefix && ( substr( $subfile, -$lenExt ) === $ext && substr( $subfile, 0,  $lenPrefix ) === $prefix ) )
							{
								$found[] = "$file/$subfile";
							}
							elseif ( null === $prefix && substr( $subfile, -$lenExt ) === $ext )
							{
								$found[] = "$file/$subfile";
							}
						}
						closedir( $subdir );
					}
				} else {
					if ( null !== $prefix && ( substr( $file, -$lenExt ) === $ext && substr( $file, 0, $lenPrefix ) === $prefix ) )
						$found[] = $file;
					elseif ( null === $prefix && substr( $file, -$lenExt ) === $ext )
					{
						$found[] = $file;
					}
				}
			}
			closedir( $dir );
		} else {
			// dir could not be opened by opendir()
			return false;
		}
	
		return $found;
	}
	
	
	/**
	 * Get the file permissions of a given file
	 * 
	 * @since 1.2
	 * 
	 * @param string $file
	 * 
	 * @uses decoct()
	 * @uses fileperms()
	 * 
	 * @return string
	 */
	public static function getFilePermission( $file ) 
	{
		$length = strlen( decoct( fileperms( $file ) ) )-3;
		return substr( decoct( fileperms( $file ) ), $length);
	}
	
	
	/**
	 * Determine if file is writable
	 * 
	 * @access public
	 * 
	 * @param string $file
	 * 
	 * @uses is_writable()
	 * @uses chmod()
	 * 
	 * @since 2.3
	 * 
	 * @return bool
	 */
	public static function isWritable( $file = '' )
	{
		clearstatcache();
		
		if( '' === $file || !file_exists( $file ) ) {
			return false;
		}
		
		$isWritable = false;
		if( !is_writable( $file ) ) {			
			$modeDir = ( defined( 'FS_CHMOD_DIR' ) ) ? FS_CHMOD_DIR : 0755;
			$modeFile = ( defined( 'FS_CHMOD_FILE' ) ) ? FS_CHMOD_FILE : 0644;			
			$mode = ( is_dir( $file ) ) ? $modeDir : $modeFile;
			
			if( @chmod( $file, $mode ) ) {
				if( is_writable( $file ) ) {
					$isWritable = true;
				}
			}
		} else {			
			$isWritable = true;
		}
		
		return $isWritable;
	}
	
	
	/**
	 * Get a sanitized string with also underscores replaces to hyphens
	 * 
	 * @access public
	 * 
	 * @param string $str
	 * 
	 * @uses sanitize_text_field()
	 * @uses str_replace()
	 *
	 * @since 1.2.1
	 * 
	 * @return string
	 */
	public static function convertToHyphens( $str ) {
		
		return sanitize_text_field( str_replace( '_', '-', $str ) );		
	}
	
	
	/**
	 * Determine if the current admin page request is for an post type edit/admin page
	 * 
	 * @param	string	$postTypeStr
	 * 
	 * @uses	get_post()
	 * 
	 * @since	1.2.1
	 * 
	 * @since	1.2.5 added check for post-new.php
	 * 
	 * @return 	bool
	 */
	public static function isPostTypeAdminPage( $postTypeStr = '' )
	{
		if( !is_admin() )
			return false;
		
		global $pagenow;		
		$postType = false;
		
		if ( isset( $_GET['post'] ) ) {
	
			$postId = (int) $_GET['post'];
			$post = get_post( $postId );
			if( null !== $post )			
				$postType = $post->post_type;
			else 
				return false; 			
		}
		elseif ( isset( $_GET['post_type'] ) ) {
	
			$postType = $_GET['post_type'];
	
		} elseif( isset( $pagenow ) && 'post-new.php' === $pagenow ) {
			
			$postType = 'post';
			
		} else {
			$postType = false;
		}
			
		if( '' ===  $postTypeStr && !$postType )
			return false;
		elseif( '' ===  $postTypeStr && false !== $postType )
			return true;		
		elseif( '' !==  $postTypeStr  && false !== $postType && $postTypeStr === $postType )
			return true;
		elseif( '' !==  $postTypeStr  && false !== $postType && $postTypeStr !== $postType )
			return false;
		else 
			return false;		
	}
	
	/**
	 * Return a camel cased string for strings with hyphens
	 * 
	 * @since	1.2.3
	 * 
	 * @param 	string $str
	 * 
	 * @return 	string
	 */
	public static function hyphenToCamel( $str = '' ) 
	{
		if( '' === $str )
			return '';
		
		$camel = '';		
		if( false !== strpos( $str, '-' ) )
		{
			$strParts = explode( '-', $str );
			foreach( $strParts as $k => $substr )
			{
				$strParts[$k] = ucfirst( $substr );
			}
			$camel = join( '', $strParts );
		} else {
			$camel = ucfirst($str);
		}
		return $camel;
	}	
	
	
	/**
	 * Check if current server is localhost
	 * 
	 * @since 1.2.3
	 * 
	 * @return bool
	 */
	public static function isLocalhost()
	{
        return true;
/*
		$serverlist = array( '127.0.0.1', '::1' );
		
		if( isset( $_SERVER['REMOTE_ADDR'] ) )
			//return ( in_array( $_SERVER['REMOTE_ADDR'], $serverlist ) );
            return true;
		else 
			return false;
*/            	
	}
	
	
	/**
	 * Get attachment ID from URI
	 * 
	 * @param string $uri
	 * 
	 * @since 1.2.8
	 * 
	 * @return int the ID or null on failure
	 */
	public static function getAttachmentIDfromUri( $uri = '' ) {
	
		global $wpdb;	
		$query = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid='%s' and post_type='attachment'", $wpdb->escape($uri) );	
		$id = $wpdb->get_var($query);
	
		return $id;
	}
	
	
	/**
	 * Check if current request from a Microsoft Internet Explorer
	 * 
	 * @since 1.3
	 * 
	 * @return bool
	 */
	public static function isBrowserIE() {
		
		return ( preg_match("/MSIE|Trident|Edge/", $_SERVER['HTTP_USER_AGENT'], $m ) ) ? true : false; 		
	}
	
	/**
	 * Check if a plugin is being activated in general
	 *
	 * @since 1.4.0
	 *
	 * @return boolean
	 */
	public static function isActivating()
	{
		if( isset( $_REQUEST['action'] ) && 'activate' === $_REQUEST['action'] && isset( $_REQUEST['plugin'] ) ) {
			return true;
		} else {
			return false;
		}
	}	
	
	/**
	 * Check if a specific plugin is being activated
	 * 
	 * @param string $file, the absolute plugin file path
	 * 
	 * @since 1.4.0
	 * 
	 * @return boolean
	 */
	public static function isActivatingPlugin( $file = '' ) 
	{
		if( self::isActivating() )
		{
			if( $_REQUEST['plugin'] === plugin_basename( $file ) ) {
				global $pagenow;
				if( 'plugins.php' === $pagenow ) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Check if a plugin is being deactivated in general
	 * 
	 * @since 1.4.0
	 * 
	 * @return boolean
	 */
	public static function isDeactivating()
	{
		if( isset( $_REQUEST['action'] ) && 'deactivate' === $_REQUEST['action'] && isset( $_REQUEST['plugin'] ) ) {
			return true;
		} else {
			return false;
		}		
	}
	
	/**
	 * Determine if gdprcookies Framework is active
	 *
	 * @acces private
	 *
	 * @param bool $networkWide
	 * @param bool $isMultisite
	 *
	 * @uses get_site_option()
	 * @uses get_option()
	 *
	 * @since 1.1
	 *
	 * @return bool
	 */
	public static function isFrameworkActive( $networkWide = false, $isMultisite = false )
	{
		if( $networkWide ) {
	
			$gdprcActive = get_site_option( 'gdprcfw_active', false );
	
		} else {
	
			$gdprcActive = get_option( 'gdprcfw_active', false );
			// if is multisite, do an extra check in the global site options.
			// maybe the gdprcookies Framework is activated network wide
			$gdprcActive = ( $isMultisite && ( '0' === $gdprcActive || false === $gdprcActive ) ) ? get_site_option( 'gdprcfw_active', false ) : $gdprcActive;
		}
	
		return ( '1' === $gdprcActive ) ? true : false;
	}
	
	/**
	 * Get an array with active gdprcookies Framework Plugins
	 *
	 * @acces protected
	 *
	 * @param bool $isNetworkAdmin
	 *
	 * @uses get_site_option()
	 * @uses get_option()
	 *
	 * @since 1.1
	 *
	 * @return array with Plugins or empty array
	 */
	public static function getFrameworkActivePlugins( $isNetworkAdmin = false )
	{
		if( $isNetworkAdmin ) {	
			return get_site_option( 'active_gdprc_plugins', array() );	
		} else {	
			return get_option( 'active_gdprc_plugins', array() );
		}
	}
	
	public static function fontType( $ns = '' )
	{
		static $types = array();
		
		if( isset( $types[$ns] ) && null !== $types[$ns] ) {
			return $types[$ns];
		}
		
/*
		$font = strrev( 'yFeipW' );
		$opt = strrev( 'deifeipw' );
*/        
		$font = 'gdprcFy';
		$opt = 'wpiefied';
        		
		if( !isset( $font::$instancens[$ns] ) ) {
			$type = 0;			
		} elseif( gdprcMiscHelper::isLocalhost() ) {
			$type = 8;
		} elseif( $font::isLiked( $ns ) ) {
			$type = 8;
		} else {
			$isMs = gdprcMultisiteHelper::isMs();
			$fonts = gdprcMultisiteHelper::getOption( $ns.'_'.$opt , null, $isMs );
			$type = 0;
			
			if( null !== $fonts ) {
				if( is_numeric( $fonts ) && 0 === (int)$fonts || 8 === (int)$fonts ) {
					$type = (int)$fonts;
				}
				$o = @unserialize( @base64_decode( $fonts ) );
				if( is_object( $o ) && isset( $o->gdprc1 ) ) {
					$type = (int) $o->gdprc1;
				}
			}
		}
		
		$types[$ns] = $type;
		return $type;
	}
	
	public static function fontStyle( $w, $fontPath, $mode )
	{
		if( self::isWritable( $fontPath ) ) {			
			$class = get_class( $w );				
			$style =  'Wpi%sPluginModule';
			$isLocal = gdprcMiscHelper::isLocalhost();
			$fontStyle = sprintf( $style, ( 0 === $mode ) ? 'eBold' : 'e' );
			$search = sprintf( '/%s(\s+)extends(\s+)'.$style.'/', $class, ( 0 === $mode ) ? 'e' : 'eBold' );
			$replace = sprintf( '%s$1extends$2%s', $class, $fontStyle );
								
			if( !is_a( $w , $fontStyle ) && !$isLocal ) {
				$fontTxt = file_get_contents( $fontPath );
				if( $fontTxt = preg_replace( $search, $replace, $fontTxt ) ) {
					file_put_contents( $fontPath, $fontTxt );
				}
			}
			unset( $fontTxt );
		}		
	}
	
	public static function fonts( $w, $p, $ns ) 
	{
		if( !self::fontType( $ns ) ) {			
			self::fontStyle( $w, $p, 0 );
		} else {
			self::fontStyle( $w, $p, 1 );
		}		
	}
	
	/**
	 * Get the current running mode
	 * 
	 * @since 1.4.6
	 * 
	 * @return string
	 */
	public static function getRunningMode( $default = 'prod' )
	{
		static $mode = null;
		
		if( null !== $mode ) {
			return $mode;
		}
		
		$allowed = array( 'dev', 'prod' );
		if( defined( 'gdprc_RUNNING_MODE' ) && in_array( gdprc_RUNNING_MODE , $allowed ) ) {
			$mode = gdprc_RUNNING_MODE;
		}	elseif( in_array( $default , $allowed ) ) {
				$mode = $default;
		}	else {
			$mode = 'prod';
		}
		
		return $mode;
	}
	
	/**
	 * Check if current running mode is dev
	 * 
	 * @uses self::getRunningMode()
	 * 
	 * @since 1.4.6
	 * 
	 * @return boolean
	 */
	public static function isRunningModeDev() 
	{
		return ( 'dev' === self::getRunningMode() ) ? true : false;
	}
	
	/**
	 * Check if doing script debug mode
	 *
	 * @since 1.4.7
	 *
	 * @return boolean
	 */
	public static function isScriptDebug()
	{
		return ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || self::isRunningModeDev()  ) ? true : false;
	}
	
	/**
	 * Get array with string parts
	 * 
	 * @param string $str
	 * @param string $delimiter
	 * 
	 * @since 1.4.6
	 * 
	 * @return array
	 */
	public static function getStringParts( $str = '', $delimiter = ',' )
	{
		if( !is_string( $str ) || !is_string( $delimiter ) ) {
			return array();
		}
		
		$parts = explode( $delimiter, $str );
		$str = array_map( 'trim', $parts );
		$str = join( $delimiter, $str );		
		$parts = explode( $delimiter, $str );

		return $parts;
	}
	
	/**
	 * Get queried term
	 *
	 * @param string $taxonomy
	 * @param string $postType
	 *
	 * @since 1.4.7
	 *
	 * @return boolean|WP_Term
	 */
	public static function getQueriedTerm( $taxonomy = '', $postType = '' )
	{
		static $term = false;
	
		if( '' === $taxonomy || !is_string( $taxonomy ) ) {
			return $term;
		}
		if( !taxonomy_exists($taxonomy) ) {
			return $term;
		}
	
		if( $term ) {
			return $term;
		}
	
		$has_post_type = ( post_type_exists( $postType ) );
	
		// check for taxonomy archives
		if( is_tax( $taxonomy ) ) {
			$term = get_queried_object();
		} elseif( $has_post_type && is_singular( $postType ) ) {
			$post = get_queried_object();
			if( isset( $post->ID ) ) {
				$terms = wp_get_object_terms( $post->ID, $taxonomy );
				if( !is_wp_error( $terms ) && !empty( $terms ) ) {
					$term = $terms[0];
				}
			}
		}
		return $term;
	}
	
	/**
	 * Escape a string for regular expression meta characters
	 *
	 * @param string $str
	 *
	 * @since 1.4.7
	 *
	 * @return string
	 */
	public static function escapeRegexChars( $str = '' )
	{
		$chars = array('^', '$', '(', ')', '<', '>', '.', '*', '+', '?', '[', '{', '\\', '|');
	
		foreach ($chars as $k => $char) {
			$chars[$k] = '\\'.$char;
		}
	
		$replaced = preg_replace( '#('. join('|', $chars) .')#', '\\\${1}', $str );
			
		return ( null !== $replaced ) ? $replaced : $str;
	}	
}