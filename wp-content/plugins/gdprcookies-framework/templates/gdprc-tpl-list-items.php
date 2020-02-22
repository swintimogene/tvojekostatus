<?php
/**
 * Template for managing custom lists
 *
 * Please see gdprcookies-framework.php for more details.
 *
 * @author $NULL $
 * 
 * @since 1.2.1
 */
?>
<div class="gdprc-list-wrapper gdprc-list-layout-<?php echo $layout ?><?php if( $can_add ): ?> gdprc-has-add<?php endif ?><?php if( $have_posts ): ?> gdprc-list-has-posts<?php endif ?><?php if( $click_select ): ?> gdprc-click-select<?php endif ?>"
	data-layout = "<?php echo $layout ?>"
	data-outer-w = "<?php echo $outer_w ?>"  
	data-context = "<?php echo $context ?>" 
	data-post-type = "<?php echo $post_type ?>"	
	data-can-add = "<?php echo $can_add ?>" 
	data-can-del = "<?php echo $can_del ?>" 
	data-can-save = "<?php echo $can_save ?>"
	data-click-select = "<?php echo $click_select ?>"	 		 
	data-has-title = "<?php echo $has_title ?>" 
	data-has-media = "<?php echo $has_media ?>" 
	data-group-meta = "<?php echo $group_meta ?>"
	data-group-meta-key = "<?php echo $group_meta_key ?>">

	<?php if( $can_add ): ?>
	<label for="gdprc-list-new-post-<?php echo gdprcMiscHelper::convertToHyphens( $context ) ?>"><?php _e( 'Add new item', 'gdprcookies' ) ?>:</label><br/>		
	<?php 
	echo gdprcFormHelper::formField(
		'textnoname',
		'gdprcclb_new_list',
		'',
		false,
		array(
			'id' 	=> 'gdprc-list-new-post-' . gdprcMiscHelper::convertToHyphens( $context ),
			'class' => 'gdprc-field-medium gdprc-list-new-post',
			'placeholder' => sprintf( __( 'Enter a new %s name', 'gdprcookies' ), gdprcMiscHelper::convertToHyphens( $context ) ),
		)
	)
	?><span class="gdprc-list-controls"><?php 
		echo gdprcFormHelper::formField(
			'linkbutton',
			'',
			'',
			false,
			array(
				'class' => 'gdprc-btn-add gdprc-btn-add-post',
			),
			__( 'Add', 'gdprcookies' )
			)
		?>		
	</span><span class="ajax-loading ajax-loading-gdprc"></span>
	<?php endif ?>	
	<?php		
	/**
	 * Let others add content before displaying the posts list
	 * 
	 * @since 1.2.1
	 */
	echo $hook_before_list_posts;
	?>
	
	<?php if( $click_select ):
	echo gdprcFormHelper::formField(
		'hidden',
		'',
		$selected,
		false,
		array( 'class' => 'hide gdprc-list-selected-post' )
	);			
	endif ?>
	
	<p class="no-items-msg"><?php printf( __( 'There are no <strong>custom %s\'s</strong> to display yet.', 'gdprcookies' ), gdprcMiscHelper::convertToHyphens( $context ), __( 'Add', 'gdprcookies' ) ) ?></p>
	
	<table id="gdprc-list-posts-<?php echo gdprcMiscHelper::convertToHyphens( $context ) ?>s" class="group gdprc-list-posts-table gdprc-list-rows-table">
	<?php if( $have_posts ): ?>
	
	<?php if( $has_heading ): ?>
	<tr class="gdprc-list-heading gdprc-list-row group">
	<?php foreach ( $headings as $heading ): ?>
	<th class="gdprc-list-heading-item"><?php echo $heading ?></th>
	<?php endforeach ?>
	</tr>
	<?php endif ?>	
			
	<?php foreach( $posts as $post_id => $post ): ?>
	<?php gdprcTemplate::get( $templ_list_item, 
							 array( 
							 		'post_id' => $post_id, 
							 		'post_type' => $post_type,							 		
							 		'title' => $post->post_title,
							 		'can_del' => $can_del,
							 		'can_save' => $can_save,
							 		'has_title' => $has_title, 
							 		'context' => $context,
							 		'layout' => $layout,
							 		'group_meta_key' => $group_meta_key,
							 		'click_select' => $click_select,
							 		'selected' => $selected
							 		
							 		) ) ?>
	<?php endforeach; endif ?>
	</table>
	<?php
	/**
	 * Let others add content after displaying the posts list
	 *
	 * @since 1.2.1
	 */
	echo $hook_after_list_posts; ?>	
</div>