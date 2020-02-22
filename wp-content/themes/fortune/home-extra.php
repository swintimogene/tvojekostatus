<?php 
$fortune_theme_options = fortune_theme_options();
if(!$fortune_theme_options['extra_home']){return;}
$postid = ($fortune_theme_options['extra_section'] == ''?'':$fortune_theme_options['extra_section']);
$fortune_portfolio_post = get_post($postid);
if($fortune_portfolio_post->post_title!=""){?>
<div class="title-centered">
  <h2 id="port_head"><?php echo esc_attr($fortune_portfolio_post->post_title);?></h2>
</div><?php
} 
?>
<!-- Project Feed -->
    <div class="home-extra row"><?php  
	if($postid):
		echo apply_filters('the_content',$fortune_portfolio_post->post_content);
endif; ?>
     </div>
    <!-- Project Feed / End -->