<?php
/**
 * Template for option groups
 *
 * Please see gdprcookies-framework.php for more details.
 *
 * @author $Author: NULL $
 * @version $Id: gdprc-tpl-settings-page-table.php 167 2018-02-24 23:09:06Z NULL $
 * @since 1.2
 */
?>
<?php foreach( $form_fields as $field ): ?>

	<?php if(apply_filters( "{$namespace}_settings_escape_field_{$current_templ}", false, $field, $vars )) continue; ?>
	
	<?php if( gdprcXmlSettingsHelper::isFormGroup( $field ) ):	
		$groupName = gdprcXmlSettingsHelper::getFieldAttributeName( $field ); ?>	
		
	  <?php if(apply_filters( "{$namespace}_settings_escape_group", false, $groupName, $vars )) continue; ?>
		<?php if(apply_filters( "{$namespace}_settings_escape_group_{$current_templ}", false, $groupName, $vars )) continue; ?>
	
		<?php if( gdprcXmlSettingsHelper::hasFormGroupTitle( $field ) )	: ?><h3><?php _e( trim($field->group_title) ) ?></h3><?php endif ?>							
		<?php if( gdprcXmlSettingsHelper::hasFormGroupDescr( $field ) )	: ?><p><?php _e( trim($field->group_descr) ) ?></p><?php endif ?>					
		<?php if( gdprcXmlSettingsHelper::hasFormGroupWarning( $field ) ): ?><p><span class="dashicons dashicons-megaphone"></span><span class="warning"><?php _e( trim($field->group_warning) ) ?></span></p><?php endif ?>

	<table<?php echo gdprcXmlSettingsHelper::getFieldCssIdString( $field ) ?> class="form-table form-fields-<?php echo $namespace ?> form-group">
	
	<?php foreach( $field as $groupField ): ?>
	
	<?php if( gdprcXmlSettingsHelper::isFormGroupMeta( $groupField ) ) continue; ?>
	
	<?php $name = (string)$groupField['name'] ?>

	<?php if(apply_filters( "{$namespace}_settings_escape_groupfield", false, $groupField, $vars )) continue; ?>
	<?php if(apply_filters( "{$namespace}_settings_escape_groupfield_{$current_templ}", false, $groupField, $vars )) continue; ?>
	
	<?php do_action( "{$setting}_groupfield_before_tr", $groupField, $vars ) ?>		
	
	  <tr valign="top" class="<?php echo $groupField->elem ?>">						
		<?php if( gdprcXmlSettingsHelper::isInline($groupField) ): ?>
		
			<?php if( gdprcXmlSettingsHelper::hasInlineTitle( $groupField ) ): ?><th scope="row"><?php _e( trim( $groupField->inline_title ), $namespace ) ?><?php if( gdprcXmlSettingsHelper::hasInlineDescr( $groupField ) ): ?><span class="descr-i toggler"><i></i></span><?php endif ?></th><?php endif ?>
					
			<td colspan="2">							
			<?php $i=1; foreach( $groupField as $groupFieldInline ): $name = (string)$groupFieldInline['name'] ?>
			
				<?php if( gdprcXmlSettingsHelper::isInlineTitle( $groupFieldInline ) ): continue; endif ?>
				<?php if( gdprcXmlSettingsHelper::isInlineDescr( $groupFieldInline ) ): continue; endif ?>		
								
				<div class="inline">
				<?php $class = (gdprcXmlSettingsHelper::isWpField($groupFieldInline)) ? 'gdprcWpFormHelper' : 'gdprcFormHelper' ?>
				<?php echo $class::formField( $groupFieldInline->elem, $name, (isset( ${$name} ) ? ${$name} : ''), $setting,
																				( gdprcXmlSettingsHelper::hasAttr($groupFieldInline) ? gdprcXmlSettingsHelper::getAttr($groupFieldInline) : array() ), 
																				( gdprcXmlSettingsHelper::hasInnerHtml($groupFieldInline) ? gdprcXmlSettingsHelper::getInnerHtml($groupFieldInline, $module_path, $vars) : '' ), 
																				( gdprcXmlSettingsHelper::isSelect($groupFieldInline) ? gdprcXmlSettingsHelper::getSelectOptions($groupFieldInline) : array() ) ) ?>
				</div>									
				<?php endforeach ?>						
			</td>
		<?php else: ?> 
		<?php if( gdprcXmlSettingsHelper::hasTitle( $groupField ) ): ?><th scope="row"><label><?php  _e( trim($groupField->title), $namespace ) ?></label><?php if( gdprcXmlSettingsHelper::hasDescr( $groupField ) ): ?><span class="descr-i toggler"><i></i></span><?php endif ?></th><?php endif ?>
			<td colspan="<?php echo ( gdprcXmlSettingsHelper::hasTitle( $groupField ) ) ? 1 : 2 ?>">
			
				<?php do_action( "{$setting}_groupfield_before_td_content", $groupField, $vars ) ?>
				
				<?php $class = (gdprcXmlSettingsHelper::isWpField($groupField)) ? 'gdprcWpFormHelper' : 'gdprcFormHelper' ?>			
				<?php echo $class::formField( $groupField->elem, $name, (isset( ${$name} ) ? ${$name} : ''), $setting,
																				( gdprcXmlSettingsHelper::hasAttr($groupField) ? gdprcXmlSettingsHelper::getAttr($groupField) : array() ), 
																				( gdprcXmlSettingsHelper::hasInnerHtml($groupField) ? gdprcXmlSettingsHelper::getInnerHtml($groupField, $module_path, $vars) : '' ), 
																				( gdprcXmlSettingsHelper::isSelect($groupField) ? gdprcXmlSettingsHelper::getSelectOptions($groupField) : array() ) ) ?>
														
			</td>													
		<?php endif ?>											
	  </tr>	  
	  <?php if( gdprcXmlSettingsHelper::hasInlineDescr( $groupField ) ): ?><tr class="description" valign="top"><td colspan="2"><p class="togglethis" data-toggle-status="hide"><?php  _e( trim($groupField->inline_descr), $namespace ) ?></p></td></tr><?php
 			elseif( gdprcXmlSettingsHelper::hasDescr( $groupField ) ): ?><tr class="description" valign="top"><td colspan="2"><p class="togglethis" data-toggle-status="hide"><?php  _e( trim($groupField->descr), $namespace ) ?></p></td></tr><?php endif ?>		  
	<?php endforeach; ?>
	
	</table>
	
	<?php elseif( gdprcXmlSettingsHelper::isInline( $field ) ):	?>
	<table<?php echo gdprcXmlSettingsHelper::getFieldCssIdString( $field ) ?> class="form-table form-fields-<?php echo $namespace ?>">
	  <tr valign="top" class="<?php echo (string)$field['name'] ?>">
	  <?php if( gdprcXmlSettingsHelper::hasInlineTitle($field) ): ?><th scope="row"><?php _e( trim($field->inline_title), $namespace ) ?><?php if( gdprcXmlSettingsHelper::hasInlineDescr( $field ) ): ?><span class="descr-i toggler"><i></i></span><?php endif ?></th><?php endif ?>
			<td>
				<?php $i=1; foreach( $field->field as $fieldInline ): $name = (string)$fieldInline['name'] ?>													
					<div class="inline">
					<?php $class = (gdprcXmlSettingsHelper::isWpField($fieldInline)) ? 'gdprcWpFormHelper' : 'gdprcFormHelper' ?>
					<?php echo $class::formField( $fieldInline->elem, $name, (isset( ${$name} ) ? ${$name} : ''), $setting,
																					( gdprcXmlSettingsHelper::hasAttr($fieldInline) ? gdprcXmlSettingsHelper::getAttr($fieldInline) : array() ), 
																					( gdprcXmlSettingsHelper::hasInnerHtml($fieldInline) ? gdprcXmlSettingsHelper::getInnerHtml($fieldInline, $module_path, $vars) : '' ), 
																					( gdprcXmlSettingsHelper::isSelect($fieldInline) ? gdprcXmlSettingsHelper::getSelectOptions($fieldInline) : array() ) ) ?>
					</div>									
				<?php endforeach ?>				
			</td>			
	  </tr>
	<?php if( gdprcXmlSettingsHelper::hasInlineDescr( $field ) ): ?><tr class="description" valign="top"><td colspan="2"><p class="togglethis" data-toggle-status="hide"><?php  _e( trim($field->inline_descr), $namespace ) ?></p></td></tr><?php endif ?>		
	</table>					
	<?php else: $name = (string)$field['name'] ?>
	<table<?php echo gdprcXmlSettingsHelper::getFieldCssIdString( $field ) ?> class="form-table form-fields-<?php echo $namespace ?>">		
	  <tr valign="top" class="<?php echo $field->elem ?>">
			<?php if( gdprcXmlSettingsHelper::hasTitle( $field ) ): ?><th scope="row"><?php _e( trim($field->title), $namespace ) ?><?php if( gdprcXmlSettingsHelper::hasDescr( $field ) ): ?><span class="descr-i toggler"><i></i></span><?php endif ?></th><?php endif ?>
			<td colspan="<?php echo ( gdprcXmlSettingsHelper::hasTitle( $field ) ) ? 1 : 2 ?>">	
							
			<?php do_action( "{$setting}_field_before_td_content", $field, $vars ) ?>
					
			<?php $class = (gdprcXmlSettingsHelper::isWpField($field)) ? 'gdprcWpFormHelper' : 'gdprcFormHelper' ?>
			<?php echo $class::formField( $field->elem, $name, (isset( ${$name} ) ? ${$name} : ''), $setting,
																			( gdprcXmlSettingsHelper::hasAttr($field) ? gdprcXmlSettingsHelper::getAttr($field) : array() ), 
																			( gdprcXmlSettingsHelper::hasInnerHtml($field) ? gdprcXmlSettingsHelper::getInnerHtml($field, $module_path, $vars) : '' ), 
																			( gdprcXmlSettingsHelper::isSelect($field) ? gdprcXmlSettingsHelper::getSelectOptions($field) : array() ) ) ?>			
			</td>
	  </tr>	
	  <?php if( gdprcXmlSettingsHelper::hasDescr( $field ) ): ?><tr class="description" valign="top"><td colspan="2"><p class="togglethis" data-toggle-status="hide"><?php  _e( trim($field->descr), $namespace ) ?></p></td></tr><?php endif ?>
	</table>  
	<?php endif; endforeach ?>
	
	<?php if($do_submit_btn): ?><p class="submit">	
	<?php 
	echo gdprcFormHelper::formField(
		'submit',
		'submit',
		__( 'Save changes' ),
		false,
		array(
			'class' => 'button-submit button button-primary',
			'aria-label' => __( 'Save changes' )
		),
		__( 'Add', $namespace )
		)
	?>
	</p><?php endif ?>