<?php
/**
 * Custom product field File data object.
 *
 * @link       https://epo.localhost
 * @since      2.3.0
 *
 * @package    woocommerce-epo
 * @subpackage woocommerce-epo/includes/model/fields
 */
if(!defined('WPINC')){	die; }

if(!class_exists('WEPO_Product_Field_File')):

class WEPO_Product_Field_File extends WEPO_Product_Field{
	public $maxsize = false;
	public $accept = false;
	
	public function __construct() {
		$this->type = 'file';
	}	
}

endif;