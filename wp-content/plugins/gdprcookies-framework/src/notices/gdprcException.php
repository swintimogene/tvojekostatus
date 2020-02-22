<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcException Class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcException.php 150 2017-05-09 10:00:44Z NULL $
 * @since 1.4.3
 */
class gdprcException extends Exception
{
	public function __construct( $message, $code = 0, Exception $previous = null ) 
	{
		parent::__construct( $message, $code, $previous );
	}
}