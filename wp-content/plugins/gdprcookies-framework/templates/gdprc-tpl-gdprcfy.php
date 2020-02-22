<?php
/**
 * Template for gdprcFy form
 * 
 * @since 1.2.3
 */
?>
<div class="wrap">
	<h1><?php printf( __( 'Validate your Envato Purchase code for: %s', 'gdprcookies' ), $plugin_name ) ?></h1>
	<img width="160" height="100" src="<?php echo $logo_uri ?>" />
	<div class="gdprcfy-wrap" id="<?php echo $tb_content_id ?>">
	
		<p><?php _e( 'In order to use this plugin, please enter the following fields and click on "Validate".', 'gdprcookies' ) ?></p>
		
		<div class="gdprc-app-form-msg-wrap">
			<?php if( $has_err ): ?><div class="gdprc-app-form-msg-err gdprc-app-form-msg"><?php echo $msg_err ?></div><?php endif ?>
			<div class="gdprc-app-form-msg-suc6 gdprc-app-form-msg"><?php echo $msg_suc6 ?></div>
		</div>
		
		<form id="gdprcfy-form" class="gdprc-app-form" action="" method="post">
			
			<?php echo gdprcFormHelper::formField(		
					'hidden',
					$field_name_ns,
					$wf_gdprcfy_ns
					); ?>
			<?php echo gdprcFormHelper::formField(		
					'hidden',
					$field_name_ac,
					$action
					); ?>
										
			<div class="gdprc-form-field<?php echo ( in_array( $field_name_pc, $error_fields ) || $all_has_error ) ? ' has-err' : '' ?>">				
			<label for="wf_gdprcfy_pc"><?php _e( 'Envato purchase code', 'gdprcookies' ) ?></label>	
			<?php echo gdprcFormHelper::formField(		
					'text',
					$field_name_pc,
					$wf_gdprcfy_pc,
					'',
					array( 'placeholder' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx' )				
					); ?>
			</div>
			<div class="gdprc-form-field<?php echo ( in_array( $field_name_wc, $error_fields ) || $all_has_error ) ? ' has-err' : '' ?>">			
			<label for="wf_gdprcfy_wc"><?php _e( 'gdprcookies code', 'gdprcookies' ) ?></label>				
			<?php echo gdprcFormHelper::formField(		
					'text',
					$field_name_wc,
					$wf_gdprcfy_wc
					); ?>
				<span class="description"><?php printf( __( 'You should have recieved an email with your <strong>unique gdprcookies code</strong>, after you\'ve <a href="%s" target="_blank">registered</a> your <strong>Envato purchase code</strong>.', 'gdprcookies' ), $uri_register ) ?></span>
			</div>			
			<?php if( !$gdprcfied ): ?>
			<div class="gdprc-form-field">
			<?php echo gdprcFormHelper::formField(		
					'submit',
					'wf_gdprcfy_send',
					'Validate!',
					'',
					$btn_attributes		
					); ?>
			</div>
			<?php endif ?>
			<?php if( $gdprcfied ): ?>
			<div class="gdprc-form-field">
			<?php echo gdprcFormHelper::formField(		
				'button',
				'',
				'',
				'',
				$btn_attributes,
				__( 'Done', 'gdprcookies' )
				); ?>
			</div>
			<?php endif ?>													
		</form>
		
	
	</div>
</div>