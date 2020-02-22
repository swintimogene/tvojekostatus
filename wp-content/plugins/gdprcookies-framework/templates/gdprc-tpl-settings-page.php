<?php
/**
 * Template for the admin settings page (parent)
 * 
 * Please see gdprcookies-framework.php for more details.
 * 
 * @author $Author: NULL $
 * @version $Id: gdprc-tpl-settings-page.php 78 2015-12-14 16:08:14Z NULL $
 * @since 1.2
 */
?>
<div class="wrap">	

	<div id="gdprc-plugin-header">
		<div class="logo"></div>
		<h2><?php echo $title ?></h2>
	</div>
		
	<div id="notice-holder"></div>
	
	<div id="gdprc-nav-wrapper">
		<ul class="group<?php if( !$has_tabs ): ?> no-tabs<?php endif ?>">
		<?php if( $has_tabs ): ?>
			<?php foreach( $tabs as $k => $tab_data ): ?>		
			<li class="<?php echo $tab_data['class'] ?>"><a href="<?php echo $tab_data['uri']?>"><?php _e( $tab_data['tab'], 'gdprcookies' ) ?></a></li>		
			<?php endforeach ?>
			<li id="instr-<?php echo $namespace ?>" class="gdprc-plugin-instr"><a href="<?php echo $instructions_uri ?>" title="<?php esc_attr_e( "Read more details and intructions about the $title", $namespace ) ?>" target="_blank"><?php _e( 'Instruction guide ', 'gdprcookies' ) ?> &rsaquo;</a></li>	
		<?php else: ?>		
			<li id="instr-<?php echo $namespace ?>" class="gdprc-plugin-instr"><a href="<?php echo $instructions_uri ?>" title="<?php esc_attr_e( "Read more details and intructions about the $title", $namespace ) ?>" target="_blank"><?php _e( 'Instruction guide ', 'gdprcookies' ) ?> &rsaquo;</a></li>		
		<?php endif ?>
		</ul>
	</div>	
	
	<div <?php if( $has_tabs ): ?>id="tab-<?php echo $current_tab ?>" <?php endif?>class="tab-content">				
	
		<?php if( $has_tabs ): ?>
		<form id="gdprc-settings-form-<?php echo $current_tab ?>" class="<?php echo $namespace ?>-settings-form gdprc-settings-form" data-setting-group="<?php echo $setting ?>" method="post" action="options.php">
				
		<?php echo gdprcFormHelper::formField( 
				'hidden',
				"{$namespace}_tab",
				$current_tab,
				$setting ) ?>
					
		<?php settings_fields( $setting ) ?>	
		
		<?php if( 'tools' === $current_tab ): ?>	
		<h3><?php _e( 'Reset', 'gdprcookies' ) ?></h3>
		<p><span class="dashicons dashicons-megaphone"></span><span class="warning"><?php _e( 'This action cannot be undone. All content will be set to default values!', 'gdprcookies' ) ?></span></p>
		
		<?php echo gdprcFormHelper::formField( 
				'buttonsubmitconfirm',
				'reset',
				'',
				'',
				array( '_msg' => __( 'Are you sure you want to RESET to default settings?', $namespace ), '_hidden_name' => 'gdprc_do_reset' ),			
				__( 'Reset', $namespace )
				) ?>		
			
		<?php endif ?>
		
		<?php echo $table ?>				
		</form>
					
		<?php else: ?>
		<form class="<?php echo $namespace ?>-settings-form gdprc-settings-form" data-setting-group="<?php echo $setting ?>" method="post" action="options.php">
		
		<?php echo gdprcFormHelper::formField( 
				'hidden',
				"{$namespace}_tab",
				$current_tab,
				$setting ) ?>
				
		<?php settings_fields( $setting ) ?>	
		<?php echo $table ?>				

		</form>			
		<?php endif ?>		
				
	</div>
</div>