<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcTaxonomy class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcTaxonomy.php 167 2018-02-24 23:09:06Z NULL $
 * @since 0.1
 */
final class gdprcTaxonomy 
{
	/**
	 * The name for the Taxonomy to register
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected $taxomomyStr = '';	

	/**
	 * The arguments for registering the Taxonomy
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	protected $argsRegister = array();
	
	/**
	 * Flag if adding custom Taxonomy term fields
	 * 
	 * @since 1.0
	 * 
	 * @var bool
	 */
	public $addCustomFields = false;
	
	/**
	 * Priority for the hooks that handle the extra term fields
	 * 
	 * @since 1.0
	 * 
	 * @var int
	 */
	public $customFieldsPriority = 100;

	/**
	 * The prefix that the database custom fields will have
	 * 
	 * @since 1.0
	 * 
	 * @var string
	 */
	const CUSTOM_FIELDS_PREFIX = 'term_meta';

	/**
	 * Constructor
	 * 
	 * @access public
	 *  
	 * @param string $taxonomyStr the name for the Taxonomy to register
	 * @param array $argsRegister register settings
	 * @param array $args
	 * 
	 * @since 0.1
	 */
	public function __construct( $taxonomyStr = '', $argsRegister = array(), $args = array() )
	{
		$this->taxomomyStr = $taxonomyStr;
				
		if( !empty( $argsRegister ) ) {		
			$this->_setArgsRegister( $argsRegister );			
		}

		// parse optional arguments
		// @since 1.3.3
		$args = wp_parse_args( $args, array(
				'add_custom_fields' => false,
				'custom_fields_cb' => false
				) );
		
		extract( $args );
		
		if( true === $add_custom_fields && false !== $custom_fields_cb ) {			
			$this->addCustomFields = true;
			
			add_action( $this->taxomomyStr . '_add_form_fields', $custom_fields_cb, $this->customFieldsPriority );
			add_action( $this->taxomomyStr . '_edit_form_fields', $custom_fields_cb, $this->customFieldsPriority );
			add_action( 'edited_'.$this->taxomomyStr, array( &$this, 'saveFields' ), 10, 2 );
			add_action( 'create_'.$this->taxomomyStr, array( &$this, 'saveFields' ), 10, 2 );
			add_action( 'delete_'.$this->taxomomyStr, array( &$this, 'deleteFields' ), 10, 3 );
		}
	}

	/**
	 * Save custom term fields
	 * 
	 * @access public
	 * 
	 * @param int $term_id
	 * 
	 * @uses get_option()
	 * @uses update_option()
	 * 
	 * @since 1.0
	 */
	public function saveFields( $term_id ) 
	{
		if ( isset( $_POST[self::CUSTOM_FIELDS_PREFIX] ) ) {				
			$meta_data = $_POST[self::CUSTOM_FIELDS_PREFIX];
			$term_meta = get_option( sprintf( '%s_%d', $this->taxomomyStr, $term_id ) );				
			$field_names = array_keys( $meta_data );
				
			foreach ( $field_names as $name ) {
				if ( isset ( $meta_data[$name] ) ) {						
					$data = $meta_data[$name];
						
					if( is_string( $var ) ) {
						$data = wp_unslash( $data );
					}
						
					$term_meta[$name] = $data;
				}
			}
				
			// save the option array.
			update_option( sprintf( '%s_%d', $this->taxomomyStr, $term_id ), $term_meta );
		}
	}

	/**
	 * Delete a custom term field
	 * 
	 * @access public
	 * 
	 * @param int $term
	 * @param int $tt_id
	 * @param mixed|null|WP_Error $deleted_term 
	 * 
	 * @uses delete_option()
	 * 
	 * @since 1.0
	 */
	public function deleteFields( $term, $tt_id, $deleted_term ) 
	{
		$option_name = sprintf( '%s_%d', $this->taxomomyStr, $term );

		delete_option( $option_name );
	}	

	/**
	 * Bind Post Types to the registered Taxonomy
	 * 
	 * @access public
	 * 
	 * @param (string|array) $post_type
	 * @uses register_taxonomy_for_object_type()
	 * 
	 * @since 1.0
	 */
	public function bindToPostType( $post_types = array() ) 
	{
		$post_types = (array)$post_types;

		if( !empty( $post_types ) ) {				
			foreach ( (array)$post_types as $post_type ) {
				register_taxonomy_for_object_type( $this->taxomomyStr, $post_type );
			}
		}
	}

	/**
	 * Register the taxonomy/taxonomies
	 * 
	 * @access public
	 * 
	 * @param array|string $posttypeStr
	 * 
	 * @uses register_taxonomy()
	 * 
	 * @since 0.1
	 * 
	 * @return null|WP_Error WP_Error if errors, otherwise null.
	 */
	public function register( $post_types )
	{
		return register_taxonomy( $this->taxomomyStr, $post_types, $this->argsRegister );
	}

	/**
	 * Create a term
	 * 
	 * @access public
	 * 
	 * @param string $term
	 * 
	 * @uses wp_insert_term()
	 * 
	 * @since 0.1
	 * 
	 * @return Bool false if $result is WP_Error, otherwise an array containing the term_id and term_taxonomy_id.
	 */
	public function create( $term = '' )
	{
		$result = wp_insert_term( $term, $this->taxomomyStr );
		
		if( is_wp_error( $result ) ) {
			return false;
		} else {
			return $result;
		}
	}


	/**
	 * Retrieve a term
	 * 
	 * @access public
	 * 
	 * @param int $termId
	 * @param string $output Constant OBJECT, ARRAY_A, or ARRAY_N
	 * @param string $filter
	 * 
	 * @uses get_term()
	 * 
	 * @since 0.1
	 * 
	 * @return WP_Term|array|false Term Row from database.
	 */
	public function retrieve( $termId, $output = OBJECT, $filter = 'raw' )
	{
			$term = get_term( $termId, $this->taxomomyStr, $output, $filter );
		
			if( is_null( $term ) || is_wp_error( $term ) ) {
				return false;
			} else {
				return $term;
			}	
	}


	/**
	 * Retrieve multiple terms
	 * 
	 * @access public
	 * 
	 * @param array $args
	 * 
	 * @uses get_terms()
	 * 
	 * @since 0.1
	 * 
	 * @return array|WP_Error List of Term Objects and their children. Will return WP_Error, if any of $this->taxomomyStr do not exist.
	 */
	public function retrieveAll( $args = array() )
	{
		if( empty( $args ) ) {
			$args = array('get' => 'all');
		} else {
			$args['get'] = 'all';
		}

		$terms = get_terms( $this->taxomomyStr, $args );
		
		if( empty( $terms ) || is_wp_error( $terms ) ) {
			return false;
		} else {
			return $terms;
		}
	}

	/**
	 * Retrieve the terms for the given object ID(s)
	 *
	 * @access public
	 *
	 * @param array $object_id
	 * 
	 * @uses wp_get_object_terms()
	 * 
	 * @since 1.0
	 *
	 * @return the term(s) object or boolean false on failure
	 */
	public function retrieveByObjectId( $object_id ) 
	{
		if( !$object_id ) {
			return false;
		}

		if( !is_array( $object_id ) ) {
			$object_id = array( $object_id );
		}

		$terms = wp_get_object_terms( $object_id, $this->taxomomyStr );

		if( empty( $terms ) || is_wp_error( $terms ) ) {
			return false;
		} else {
			return $terms;
		}
	}	
		
	/**
	 * Retrieve the object ID's for the given term
	 * 
	 * @access 	public
	 * 
	 * @param 	int 	$termId
	 * @param 	array $args
	 * 
	 * @uses	get_objects_in_term()
	 * 
	 * @since 	1.1.8
	 * 
	 * @return 	array with object ID's or boolean false on failure
	 */
	public function retrieveObjectIds( $termId = 0, $args = array() )
	{
		if( 0 === $termId ) {
			return false;
		}
	
		if( !is_array($args) ) {
			$args = array($args);
		}
	
		$ids = get_objects_in_term( $termId, $this->taxomomyStr, $args );
	
		if( empty( $ids ) || is_wp_error( $ids ) ) {
			return false;
		} else {
			return $ids;
		}
	}	

	/**
	 * Retrieve a term field by another field value pair
	 *
	 * @access public
	 *
	 * @param string $needle the term field to return
	 * @param string $field
	 * @param mixed $value the value for $field
	 * 
	 * @uses get_term_by() 
	 * 
	 * @since 0.1
	 * 
	 * @return the term field ($needle) or boolean false on failure
	 */
	public function retrieveFieldBy( $needle = '', $field = '', $value = '' )
	{
		if( empty( $field ) || empty( $value ) ) {
			return false;
		}

		if( $term = get_term_by( $field, $value, $this->taxomomyStr ) ) {
			if( empty( $needle ) ) {
				return $term;
			} else {
				(string) $needle;
				return $term->{$needle};
			}
		} else {
			return false;
		}
	}

	/**
	 * Retrieve a (custom) term field
	 * 
	 * Possible fields: term_id, name, descr, slug or custom field. 
	 * 
	 * Custom fields are stored in the wp_options database table
	 * 
	 * @access public
	 * 
	 * @param int $term_id
	 * @param string $field
	 * 
	 * @uses gdprcTaxonomy::retrieveFieldBy()
	 * @uses gdprcTaxonomy::retrieveCustomField() 
	 * @uses term_description()
	 * 
	 * @todo: implement WP's 4.4 Term meta for custom fields, 
	 * @see add_term_meta(), get_term_meta(), update_term_meta()	
	 * 
	 * @since 1.0
	 * 
	 * @return mixed 
	 */
	public function retrieveField( $term_id = 0, $field = '' ) 
	{
		if( 0 === $term_id || empty( $field ) ) {
			return false;
		}

		switch( $field ) {				
			case 'term_id': return $this->retrieveFieldBy( 'term_id', 'term_id', $term_id );	break;				
			case 'name': 	return $this->retrieveFieldBy( 'name', 'term_id', $term_id ); 		break;				
			case 'descr': 	return term_description( $term_id, $this->taxomomyStr ); 			break;				
			case 'slug': 	return $this->retrieveFieldBy( 'slug', 'term_id', $term_id ); 		break;				
			default: 		return $this->retrieveCustomField( $term_id, $field ); 				break;				
		}
	}


	/**
	 * Retrieve a custom term field
	 * 
	 * @access public
	 * 
	 * @param int $term_id
	 * @param string $field
	 * 
	 * @uses get_option()
	 * 
	 * @since 1.0
	 * 
	 * @return string
	 */
	public function retrieveCustomField( $term_id, $field = '' ) 
	{
		$term_meta = get_option( sprintf( '%s_%d', $this->taxomomyStr, $term_id ) );

		return ( is_array( $term_meta ) && isset( $term_meta[$field] ) ) ? $term_meta[$field] : '';
	}

	/**
	 * Retrieve a label value
	 * 
	 * @access public
	 * 
	 * @param string $needle
	 * 
	 * @uses get_taxonomy() 
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function retrieveTaxLabel( $needle = 'name' )
	{
		$needle = (string) $needle;
		$taxonomy = get_taxonomy( $this->taxomomyStr );

		if( isset( $taxonomy->labels->{$needle} ) ) {
			return $taxonomy->labels->{$needle};
		} else {
			return '';
		}
	}


	/**
	 * Update a term
	 * 
	 * @access public
	 * 
	 * @param int $termId
	 * @param array $args
	 * 
	 * @uses wp_update_term()
	 * 
	 * @since 0.1
	 * 
	 * @return array|WP_Error Returns Term ID and Taxonomy Term ID
	 */
	public function update( $termId, $args = array() )
	{
		if( !isset( $args['slug'] ) && isset($args['name'] ) ) {			
			$args['slug'] = sanitize_title( $args['name'] );
		}
		
		$result = wp_update_term( $termId, $this->taxomomyStr, $args );	
		
		if( is_wp_error( $result ) ) {
			return false;
		} else {
			return $result;
		}
	}
	
	/**
	 * Delete a term
	 * 
	 * @access public
	 * 
	 * @param int $termId
	 * @param array $args
	 * 
	 * @uses wp_delete_term()
	 * 
	 * @since 0.1
	 * 
	 * @return Bool false if $result is WP_Error or term does not exist, otherwise true on succes.
	 */
	public function delete( $termId = 0, $args = array() )
	{
		$term = wp_delete_term( $termId, $this->taxomomyStr, $args = array() );
		
		if( false === $term || is_wp_error( $term ) ) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Delete all terms for $this->taxomomyStr
	 * 
	 * @access public
	 * 
	 * @param array $args, advanced use, see WordPress doc for wp_delete_term()
	 * 
	 * @since 0.1
	 * 
	 * @todo: use method gdprcTaxonomy::delete() instead of wp_delete_term() 
	 * 
	 * @uses wp_delete_term()
	 */
	public function deleteAll( $args = array() )
	{
		$terms = $this->retrieveAll( array( 'fields' => 'ids' ) );

		if( false !== $terms ) {
			foreach ( $terms as $termId ) {
				wp_delete_term( $termId, $this->taxomomyStr, $args );
			}
		}	
	}

	/**
	 * Check if registered Taxonomy has terms
	 * 
	 * @access public
	 * 
	 * @uses gdprcTaxonomy::retrieveAll()
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if yes or false when no terms are found
	 */
	public function hasTerms()
	{
		$terms = $this->retrieveAll();

		if( empty( $terms ) || is_wp_error( $terms ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Is the current request for the admin Taxonomy page
	 * 
	 * c
	 * 
	 * @since 1.0
	 * 
	 * @return bool
	 */
	public function isTaxAdminPage() 
	{
		return ( is_admin() && $this->taxomomyStr === $_GET['taxonomy'] );
	}
	
	/**
	 * Set the arguments for registering the Taxonomy
	 * 
	 * @access private
	 * 
	 * @since 0.1
	 * 
	 * @param array $args
	 */
	private function _setArgsRegister( $args )
	{		
		$this->argsRegister	= $args;		
	}
}