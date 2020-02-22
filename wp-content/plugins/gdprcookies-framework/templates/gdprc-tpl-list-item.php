<?php
/**
 * Template for one lists item row
 *
 * Please see gdprcookies-framework.php for more details.
 *
 * @author $NULL $
 * 
 * @since 1.2.1
 */
?>
<tr class="gdprc-list-row<?php echo (( $click_select && ($selected === $post_id)) ? ' gdprc-list-row-selected' : '' ) ?> gdprc-list-row-<?php echo $post_id ?> group" data-post-id="<?php echo $post_id ?>">
	<?php if( $has_title ): ?>
	<td class="gdprc-list-row-field field-title">	
		<?php 
		echo gdprcFormHelper::formField(
			'textnoname',
			'',
			$title,
			false,
			array(
				'class' => 'gdprc-list-row-field-post-title',
				'placeholder' => __( 'Enter a name for this item', 'gdprcookies' ),
			)
		)
		?>				
	</td>
	<?php endif;
	
	$list_row_fields = apply_filters( 'gdprc_row_fields_list_custom_posts_' . $context , array(), $post_id, $post_type );		
	foreach( $list_row_fields as $k => $field ): ?>
	<td class="gdprc-list-row-field field-<?php echo gdprcMiscHelper::convertToHyphens( $k ) ?><?php if( 'error' === $k ): ?> gdprc-sett-field-err<?php endif ?>">
	<?php echo $field ?>
	</td>
	<?php endforeach ?>
	<td class="gdprc-list-row-actions">
		<?php if( $can_save ): ?><span class="dashicons icon-save gdprc-list-row-action gdprc-btn-save"></span><?php endif ?>
		<?php if( $can_del ): ?><span class="dashicons dashicons-trash gdprc-list-row-action gdprc-btn-del"></span><?php endif ?>
		<span class="ajax-loading ajax-loading-gdprc"></span>
	</td>
</tr>	