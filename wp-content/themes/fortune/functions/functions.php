<?php 
function fortune_front_page_section( $partial = null, $id = 0 ) {
	if ( $fortune_theme_options['portfolio_post'] ) {
		
		get_template_part( 'home', 'portfolio' );

	}
}