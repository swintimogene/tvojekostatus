<?php $fortune_theme_options = fortune_theme_options(); 
if($fortune_theme_options['social_home']==false) return; ?>
<!-- Social Links -->
<div class="social-links-section social-links-section__dark icons-rounded">
  <div class="container">
	<?php
		wp_nav_menu( array(
			'theme_location' => 'social',
			'container'		=>false,
			'menu_class'     => '',
			'depth'          => 1,
			'link_before'    => '<span class="screen-reader-text">',
			'link_after'     => '</span>' . fortune_get_icon( array( 'icon' => 'chain' ) ),
		) );
	?>
  </div>
</div>
<!-- Social Links / End -->
