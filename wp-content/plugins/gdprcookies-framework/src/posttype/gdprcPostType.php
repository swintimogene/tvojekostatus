<?php
/**
 * Please see gdprcookies-framework.php for more details.
 */

/**
 * gdprcPostType class
 *
 * @author $Author: NULL $
 * @version $Id: gdprcPostType.php 167 2018-02-24 23:09:06Z NULL $
 * @since 0.1
 */
final class gdprcPostType 
{	
	/**
	 * The name for the Post Type to register
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	private $postTypeStr = '';	

	/**
	 * The arguments for registering the Post Type
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	private $argsRegister = array();	
	
	/**
	 * The arguments for creating a new Post
	 * 
	 * @since 1.0
	 * 
	 * @var array
	 */
	private $argsNew = array();	
	
	/**
	 * The registered Post Type object
	 * 
	 * @since 1.0
	 * 
	 * @var object
	 */
	private $posttypeObject;	

	/**
	 * Constructor
	 *
	 * @access public
	 * @param string $postTypeStr the name for the Post Type to register
	 * 
	 * @since 0.1
	 */
	public function __construct( $postTypeStr = '', $argsRegister = array(), $argsNew = array() )
	{
		$this->postTypeStr = $postTypeStr;
		
		if( !empty( $argsRegister ) ) {
			$this->_setArgsRegister( $argsRegister );	
		}
				
		if( !empty( $argsNew ) ) {
			$this->_setArgsNew( $argsNew );
		} else {
			// set to default arguments
			$argsNew = array(
					'post_status' => 'publish',
					'post_type' => sprintf( '%s', $this->postTypeStr ),
					'ping_status' => 'closed',
					'guid' => '',
					'post_excerpt' => '',
					'post_content' => '',
					'post_title' => ''
			);
			$this->_setArgsNew( $argsNew );
		}		
	}	
	
	/**
	 * Bind taxonomie(s) to an registered Post Type
	 * 
	 * @access public
	 * 
	 * @param (string|array) $taxonomies
	 *
	 * @uses register_taxonomy_for_object_type() 
	 * 
	 * @since 1.0
	 */
	public function bindTaxonmies( $taxonomies ) 
	{	
		foreach ( (array)$taxonomies as $tax ) {				
			if( '' === $tax ) {
				continue;
			}
				
			register_taxonomy_for_object_type( $tax, $this->postTypeStr );
		}
	}	
	

	/**
	 * Register the Post Type
	 * 
	 * Saves the Post Type object to $this->posttypeObject
	 * 
	 * @access public
	 * 
	 * @uses register_post_type()  
	 * 
	 * @since 0.1
	 */
	public function register()
	{
		$this->posttypeObject = register_post_type( $this->postTypeStr, $this->argsRegister );
	}

	/**
	 * Create a Post
	 * 
	 * @access public
	 * 
	 * @param array $args
	 * @param WP_Error $wp_error
	 * 
	 * @uses wp_insert_post()
	 * @uses is_wp_error()
	 * 
	 * @since 0.1
	 * 
	 * @return bool false on error or int $postId   
	 */
	public function create( $title = '', $args = array(), $wp_error = false )
	{	
		$defaults = $this->getArgsNew( $title );
		
		if( !empty( $args ) ) {
			$args = wp_parse_args( $args, $defaults );
		} else {
			$args = $defaults;
		}
		
		$postId = wp_insert_post( $args, $wp_error );
		
		if( false == $postId || is_wp_error( $postId ) ) {
			return false;
		} else {
			return $postId;
		}
	}

	/**
	 * Retrieve a Post
	 * 
	 * @access public
	 * 
	 * @param int $postId
	 * @param (int|bool) $reset
	 * 
	 * @since 0.1
	 * 
	 * @return WP_Query object
	 */
	public function retrieve( $postId = null, $reset = false )
	{
		$args = array
		(
			'p' => $postId,
			'post_type' => $this->postTypeStr,
			'posts_per_page' => 1
		);
		
		if( $reset ) {
			wp_reset_query();
		}
		
		return new WP_Query( $args );
	}

	/**
	 * Retrieve all Posts
	 * 
	 * @access	public
	 * 
	 * @param 	(int|bool) $reset
	 * @param 	(int|bool) $useWpQuery
	 * @param 	(int|bool) $fetch
	 * 
	 * @uses 	wp_reset_query() if $reset is true
	 * @uses	gdprcMiscHelper::isPostTypeAdminPage() to test if current request in on a post admin page
	 * @uses 	get_posts() if $useWpQuery is false or if current request in on a post admin page
	 * 
	 * @since 	0.1
	 * 
	 * @return 	array with posts or WP_Query object
	 */
	public function retrieveAll( $reset = false, $useWpQuery = true, $fetch = true )
	{
		$args = array
		(
				'post_type' => $this->postTypeStr,
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'order' => 'ASC',
				'orderby' => 'ID'
		);
				
		if( $reset ) {
			wp_reset_query();
		}
		
		if( gdprcMiscHelper::isPostTypeAdminPage() ) {
			return $this->getPosts( $args );
		} elseif( $useWpQuery && !$fetch ) {
			return new WP_Query( $args );
		} elseif( $useWpQuery && $fetch ) {
			return $this->fetch( new WP_Query( $args ) );
		} else {
			return $this->getPosts( $args );
		}
	}	
	
	/**
	 * Retrieve Posts with a custom query
	 * 
	 * @access public
	 * 
	 * @param array $args
	 * @param (int|bool) $reset
	 * 
	 * @uses wp_reset_query() if $reset is true
	 * @uses wp_parse_args() 
	 * 
	 * @since 0.1
	 *  
	 * @return bool false if $args is empty, WP_Query object otherwise
	 */
	public function retrieveCustom( $args = array(), $reset = false, $useWpQuery = true, $fetch = true )
	{
		if( empty( $args ) ) {
			return false;
		}
		
		$defaults = array
		(
				'post_type' => $this->postTypeStr,
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'order' => 'ASC'
		);		
		
		if( $reset ) {
			wp_reset_query();
		}
		
		$args = wp_parse_args( $args, $defaults );
				
		if( 'attachment' === $this->postTypeStr ) {
			$args['post_status'] = 'inherit';
		}
		
		if( gdprcMiscHelper::isPostTypeAdminPage() ) {
			return $this->getPosts( $args );
		} elseif( $useWpQuery && !$fetch ) {
			return new WP_Query( $args );
		} elseif( $useWpQuery && $fetch ) {
			return $this->fetch( new WP_Query( $args ) );
		} else {
			return $this->getPosts( $args );
		}
	}	
	
	/**
	 * Search the Posts table for given search terms
	 * 
	 * If an array with multiple search terms is passed,
	 * a loop will look for each term independently
	 * 
	 * @access 	public
	 * 
	 * @param 	array|string 	$terms
	 * @param 	array 			$args
	 * 
	 * @uses	gdprcPostType::retrieveCustom()
	 * @uses	gdprcPostType::fetch() 
	 * 
	 * @return array the fetched result
	 */
	public function retrieveSearch( $terms = array(), $args = array() ) 
	{
		$posts = array();
		$args['suppress_filters'] = true;
		
		if( is_string( $terms ) ) {
			$terms = array( $terms );
		}
		
		if( is_array( $terms ) && 1 === count( $terms ) ) {			
			$args['s'] = array_shift( $terms );	
			$posts = $this->fetch( $this->retrieveCustom( $args ) );
		} elseif( is_array( $terms ) && 1 < count( $terms ) ) {			
			$posts = array();			
			
			foreach ( $terms as $term ) {				
				$args['s'] = $term;				
				$posts = $posts + $this->fetch( $this->retrieveCustom( $args ) );
			}			
		} else {						
			$posts = $this->fetch( $this->retrieveCustom( $args ) );
		}
		
		return array_unique( $posts, SORT_REGULAR );
	}
	
	/**
	 * Retrieve a Post fields
	 * 
	 * @access public
	 * 
	 * @param string $field the field to retrieve
	 * @param int|bool) $reset
	 * 
	 * @uses wp_reset_query() if $reset is true
	 * 
	 * @since 0.1
	 * 
	 * @return WP_Query object
	 */
	public function retrieveField( $field = 'ID', $reset = false )
	{
		$args = array
		(
				'post_type' => $this->postTypeStr,
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'order' => 'ASC'
		);

		if( 'ID' === $field )
		{
			$args['fields'] = 'ids';

		} else {
				
			// return all
				
			/*
			 WP now only supports 'ids' and '', if code is more flexible, we can use this:

			$this->customQueryField = $field;
				
			$args['suppress_filters'] = false;
				
			add_filter( 'posts_fields',	array(&$this, 'updatePostsFields') );
			*/
		}
		
		if( $reset ) {
			wp_reset_query();
		}
		
		return new WP_Query( $args );
	}
	
	/**
	 * Get registered Post Type object  
	 * 
	 * @access public
	 * 
	 * @since 1.0
	 * 
	 * @return object
	 */
	public function getPosttypeObject() 
	{
		return $this->posttypeObject;
	}	
	
	/**
	 * Get the arguments for a new Post
	 * 
	 * @access public
	 * 
	 * @param string $title
	 * 
	 * @uses add_magic_quotes()
	 * 
	 * @since 1.0
	 * 
	 * @return array, empty array if $title is empty
	 */
	public function getArgsNew( $title = '' )
	{
		if( '' === $title ) {
			return array();
		}
		
		$args =  $this->argsNew;
		$args['post_title'] = $title;

		$args = add_magic_quotes( $args );
	
		return $args;
	}	
	
	/**
	 * Wrapper function for WP's get_Posts() function
	 * 
	 * This method adds the Posts ID as the array index
	 * 
	 * @access 	public
	 * 
	 * @param 	array $args
	 *  
	 * @uses 	get_posts()
	 * 
	 * @since 	1.2.1
	 * 
	 * @return	array 
	 */
	public function getPosts( $args = array() ) 
	{		
		$postsArr = array();
		$posts = get_posts( $args );
		if( !empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$postsArr[$post->ID] = $post;
			}			
			unset( $posts );
		}
					
		return $postsArr;
	}	

	/**
	 * Callback for the posts_fields hook
	 * 
	 * @access public
	 * 
	 * @param string $field
	 * 
	 * @since 0.1
	 * 
	 * @todo implement when WP is ready 
	 * 
	 * @return string
	 */
	public function updatePostsFields( $field )
	{
		$field = sprintf( 'wp_posts.%s', $this->customQueryField );		

		remove_filter( 'posts_fields', array( &$this, 'updatePostsFields' ) );
		
		return $field;
	}	
	
	/**
	 * Fetch a WP_Query result
	 * 
	 * if $query {@link WP_Query::have_posts()} loop threw posts 
	 * 
	 * @access public
	 * 
	 * @param WP_Query $query
	 * 
	 * @uses WP_Query::have_posts()
	 * @uses WP_Query::the_post()
	 * 
	 * @since 0.1
	 * 
	 * @todo if result is 1 post, array_shift before return
	 * 
	 * @return array with Posts
	 */
	public function fetch( WP_Query $query )
	{
		$result = array();
		$all = ( isset( $query->posts[0]) && is_a( $query->posts[0], 'WP_Post' ) ) ? true : false;

		switch( $all ) {
			case false:
				if( $query->have_posts() ) {
					foreach( $query->posts as $k => $post ) {
						$result[] = $post;
					}
				}
				break;
			case true:
			default:
				if( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$result[$query->post->ID] = $query->post;
					}
				}
				break;
		}

		return $result;
	}

	/**
	 * Update a Post
	 * 
	 * @access public
	 * 
	 * @param int $postId, the Post ID to update
	 * @param array $args with update values
	 * @param bool $wp_error
	 * @param bool $forceGuid
	 * 
	 * @uses wp_insert_post()
	 * 
	 * @since 0.1
	 * 
	 * @todo: verify return values in doc
	 * @return bool false on invalid $postId or int Post ID, 0 or WP_Error on failure
	 */
	public function update( $postId = 0, $args = array(), $wp_error = false, $forceGuid = false )
	{
		if( 0 !== $postId ) {
			$args['ID'] = (int)$postId;
		} else {
			return false;
		}
		
		$postId = wp_update_post( $args, $wp_error );		
		if( false == $postId || is_wp_error( $postId ) ) {
			return false;
		} else {			
			if( isset( $args['guid'] ) && true === $forceGuid ) {					
				global $wpdb;
				$wpdb->update( "{$wpdb->prefix}posts" , array( 'guid' => $args['guid'] ), array( 'ID' => (int)$postId ) );
			}			
			
			return $postId;		
		}
	}

	/**
	 * Determine if registered Post Type has any Posts
	 * 
	 * @access public
	 * 
	 * @uses gdprcPostType::retrieveAll() 
	 * 
	 * @since 0.1
	 * 
	 * @return bool true if Posts are found, false otherwise
	 */
	public function hasPosts()
	{
		// get WP_Query result (not fetched)
		$posts = $this->retrieveAll( false, true, false );

		return $posts->have_posts();
	}

	/**
	 * Check if given post ID is a valid Post
	 * 
	 * @access public
	 * 
	 * @param int $postId
	 * 
	 * @uses gdprcPostType::retrieve()
	 * @uses gdprcPostType::fetch()
	 * 
	 * @since 1.2.1
	 * 
	 * @return bool
	 */
	public function isPost( $postId ) 
	{
		$post = $this->fetch( $this->retrieve( $postId ) );
		
		if ( is_array( $post ) && isset( $post[$postId] ) && is_a( $post[$postId], 'WP_Post' ) ) {
			return true;			
		} else {
			return false;	
		}		
	}
	
	/**
	 * Determmin if current request if for current post type admin page
	 * 
	 * @access public
	 * 
	 * @uses	get_post()
	 * 
	 * @since 	1.2.1
	 * 
	 * @return 	bool
	 */
	public function isAdminPage()
	{
		if ( isset( $_GET['post'] ) ) {	
			$postId = (int) $_GET['post'];
			$post = get_post( $postId );
			$postType = $post->post_type;				
		} elseif ( isset($_GET['post_type']) ) {	
			$postType = $_GET['post_type'];	
		} else {
			$postType = false;
		}
			
		return ( $postType && $this->postTypeStr === $postType );
	}	
	
	/**
	 * Delete a Post
	 * 
	 * @access public
	 * 
	 * @param int|array $postid the ID of the Post to delete
	 * @param bool $force_delete, whether to bypass trash and force deletion
	 * 
	 * @since 0.1
	 * 
	 * @return Ambigous <multitype:, boolean, WP_Post, unknown, mixed, string, NULL>
	 */
	public function delete( $postid = 0, $force_delete = false )
	{
		if( is_array( $postid ) && 0 < count( $postid ) ) {			
			foreach ( $postid as $id ) {
				wp_delete_post( $id, $force_delete );
			}						
		} else { 
			return wp_delete_post( $postid, $force_delete );
		}
	}	

	/**
	 * Delete all Posts for the registered Post Type
	 * 
	 * @access public
	 * 
	 * @param bool $force_delete, whether to bypass trash and force deletion
	 * 
	 * @uses gdprcPostType::fetch()
	 * @uses gdprcPostType::retrieveField()
	 * @uses wp_delete_post()
	 * 
	 * @since 0.1
	 */
	public function deleteAll( $force_delete = false )
	{
		$posts = $this->fetch( $this->retrieveField() );

		if( is_array( $posts ) && !empty( $posts ) ) {			
			foreach( $posts as $postid ) {
				wp_delete_post( $postid, $force_delete );
			}
		}
	}

	/**
	 * Set the arguments for registering the Post Type
	 * 
	 * @access private
	 * 
	 * @param array $args
	 * 
	 * @since 0.1
	 */
	private function _setArgsRegister( $args )
	{		
		$this->argsRegister	= $args;	
	}
	
	
	/**
	 * Set the arguments for creating a new Post
	 * 
	 * @access private
	 * 
	 * @param array $args
	 * 
	 * @since 1.0
	 */
	private function _setArgsNew( $args = array() )
	{
		$this->argsNew = $args;
	}	
}