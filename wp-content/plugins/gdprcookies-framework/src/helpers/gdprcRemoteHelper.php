<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcRemoteHelper class
 *
 * Helper class that helps with remote requests
 *
 * @author $Author: NULL $
 * @version $Id: gdprcRemoteHelper.php 126 2017-03-22 10:54:27Z NULL $
 *
 * @since 1.2.3
 */
class gdprcRemoteHelper {		
	
	/**
	 * Performs a request
	 * 
	 * @param 	string	$url
	 * @param 	array 	$postdata
	 * @param 	array	$headers
	 * 
	 * @since	1.2.3
	 * 
	 * @return 	mixed
	 */
	public static function request( $url = '', $args = array() )
	{		
		$isSsl = ( false !== strpos( $url, 'https' ) ) ? true : false;
		
		if( false === self::hasCurl( $isSsl ) ) {
			
			$response = self::_errorResponse( 0, 'Curl is not supported in this install. Could not complete request' );
			
		} else {
			
			$response = self::_doRequest( 'curl', $url, $args );			
		}

		return $response;
	}
	
	
	/**
	 * Test if current server support Curl
	 * 
	 * @param 	bool $isSsl
	 * 
	 * @since 	1.2.3
	 * 
	 * @return 	bool
	 */
	public static function hasCurl( $isSsl = false ) 
	{
		if ( ! function_exists( 'curl_init' ) || ! function_exists( 'curl_exec' ) )
			return false;
				
		if ( $isSsl ) {
			$curl_version = curl_version();
			// Check whether this cURL version support SSL requests.
			if ( ! (CURL_VERSION_SSL & $curl_version['features']) )
				return false;
		}
		
		return true;	
	}
	
	
	/**
	 * Do the actual request
	 * 
	 * @access	private
	 * 
	 * @param 	string	$type - default is "curl"
	 * @param 	string	$url
	 * @param 	array 	$args
	 * 
	 * @since	1.2.3
	 * 
	 * @return 	mixed the request response
	 */
	private static function _doRequest( $type = 'curl', $url = '', $args = array() )
	{	
		$jsonEncode = ( isset( $args['headers'] ) && isset( $args['headers']['Content-Type'] ) && 'application/json' === $args['headers']['Content-Type'] );
		$jsonDecode = ( isset( $args['headers'] ) && isset( $args['headers']['Accept'] ) && 'application/json' === $args['headers']['Accept'] );		
		$args['json_encode'] = $jsonEncode;
		$args['json_decode'] = $jsonDecode;
		
		if( !isset( $args['user-agent'] ) || '' === $args['user-agent'] ) {
			global $wp_version;
			$args['user-agent'] = 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' );
		}
		
		switch( $type ) {
			
			case 'curl':				
				$response = self::_doRequestWithCurl( $url, $args );
				break;
			default:
				
				break;			
		}
		
		if( $jsonDecode ) {	

			if( !is_string( $response ) ) 
				$response = json_encode( $response );
				
			return json_decode( $response );
						
		} else {
			return $response;
		}			
	}
	
	
	/**
	 * Do the actual request with Curl
	 * 
	 * @access	private
	 * 
	 * @param 	string	$url
	 * @param 	array 	$args
	 * 
	 * @since	1.2.3
	 * 
	 * @return 	object
	 */
	private static function _doRequestWithCurl( $url = '', $args = array() ) 
	{	
		$postFields = array();
		$method 	= ( isset( $args['method'] ) ) ? $args['method'] : null;		
		$headersArr = ( isset( $args['headers'] ) ) ? $args['headers'] : array();
		$body 		= ( isset( $args['body'] ) && !empty( $args['body'] ) && 1 <= count( $args['body'] ) ) ? $args['body'] : array();		

		// populate the headers array
		$headers = array();
		foreach ( $headersArr as $name => $value ) {
			$headers[] = "{$name}: $value";
		}

		// flag if body has params
		$hasParams = ( 1 <= count( $body ) );		
				
		// flag if CURLOPT_POSTFIELDS must be set
		$doPostFields = ( 'POST' === $method ) ? true : false; 
	
		// only json_encode when NOT doing GET request
		if( 'GET' !== $method && true == $args['json_encode'] && $hasParams ) {
			$postFields = json_encode( $body );			 
		}
		elseif( 'GET' === $method && $hasParams ) {
			$url =  add_query_arg( $body, $url );
		}			
		
		$ch = curl_init();
			
		if( !empty( $headers ) )
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

		if( isset( $args['method'] ) && 'POST' === $args['method'] )
			curl_setopt( $ch, CURLOPT_POST, true );
		
		if( $doPostFields )
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields );
		
		curl_setopt( $ch, CURLOPT_URL, $url );		
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); // prevent error: certificate verify failed
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_USERAGENT, $args['user-agent'] );
		//curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
			
		$response = curl_exec( $ch );
			
		$error = '';
		if ( curl_errno( $ch ) ) {
			$errcode = curl_errno( $ch );
			$error = 'CURL ERROR: ' . $errcode . ': ' . curl_error( $ch );
		} else {
			$status = (int)curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			switch($status) {
				case 200:
					break;
				default:
					$errcode = $status;
					$error = 'HTTP CODE: ' . $status;
					break;
			}
		}
			
		curl_close( $ch );
			
		if( '' !== $error && WP_DEBUG ) {			
			return self::_errorResponse( $errcode, $error );
		} else {
			//Output the results
			return $response;
		}		
	}
	
	
	/**
	 * Return an error resonse object
	 * 
	 * @param 	int		$errcode
	 * @param 	string 	$msg
	 * 
	 * @since	1.2.3
	 * 
	 * @return stdClass
	 */
	private static function _errorResponse( $errcode = -1, $msg = '' ) 
	{
		$err = new stdClass;
		$err->errcode = $errcode;
		$err->msg = $msg;
		
		return $err;		
	}
	
	
	/**
	 * Check if response has errors
	 *
	 * @access	public
	 *
	 * @since	1.2.3
	 *
	 * @return	bool
	 */
	public static function responseHasErrors( $response )
	{
		if( is_object( $response ) && isset( $response->error ) ) {
			return ( 200 !== (int)$response->error && 0 <= (int)$response->error && '' !== $response->description ) ? true : false;
		} elseif( is_object( $response ) && isset( $response->errcode ) ) {
			return ( 200 !== (int)$response->errcode && 0 <= (int)$response->errcode && '' !== $response->msg ) ? true : false;
		} else
			return false;
	}
	
	
	/**
	 * Get the response error code (if any)
	 *
	 * @access	public
	 *
	 * @since	1.2.3
	 *
	 * @return	int
	 */
	public static function responseGetErrorCode( $response )
	{
		$code = null;
		if( self::responseHasErrors( $response ) ) 
		{			
			if( isset( $response->errcode ) )
				$code = $response->errcode;
			elseif( isset( $response->error ) )
				$code = $response->error;			
		}
	
		return $code;
	}
	
	
	/**
	 * Get a response error (if any)
	 *
	 * @access	public
	 *
	 * @since	1.2.3
	 *
	 * @return	string
	 */
	public static function responseGetErrorMsg( $response )
	{
		$msg = '';
		if( self::responseHasErrors( $response ) ) 
		{						
			if( isset( $response->msg ) )
				$msg = $response->msg;
			elseif( isset( $response->description ) )
				$msg = $response->description;			
		}
			
		return $msg;
	}	
}