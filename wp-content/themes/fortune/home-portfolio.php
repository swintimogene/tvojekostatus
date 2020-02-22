<?php 
$fortune_theme_options = fortune_theme_options();
if(!$fortune_theme_options['portfolio_home']){return;}
$postid = ($fortune_theme_options['portfolio_post'] == ''?'':$fortune_theme_options['portfolio_post']);
$fortune_portfolio_post = get_post($postid);
if($fortune_portfolio_post->post_title!=""){?>
<div class="title-centered">
  <h2 id="port_head"><?php echo esc_attr($fortune_portfolio_post->post_title);?></h2>
</div><?php
} 
?>
<!-- Project Feed -->
    <div class="project-feed project-feed__3cols row"><?php  
	if($postid):
		echo apply_filters('the_content',$fortune_portfolio_post->post_content);
	else:
	for($i=1 ; $i<=3 ; $i++){
	?>
      <div class="col-sm-6 col-md-4 project-item">
        <div class="project-item-inner">
          <figure class="alignnone project-img"> <img class="img-responsive" src="<?php echo get_template_directory_uri(); ?>/images/p<?php echo $i; ?>.jpg" alt="" />
            <div class="overlay"> <a href="#" class="dlink"><i class="fa fa-link"></i></a> <a href="<?php echo get_template_directory_uri(); ?>/images/p<?php echo $i; ?>.jpg" class="popup-link zoom"><i class="fa fa-search-plus"></i></a> </div>
          </figure>
          <div class="project-desc">
            <h4 class="title"><a href="#"><?php _e('Project #', 'fortune'); echo $i; ?></a></h4>
            <span class="desc"><?php _e('Photography / Web Design', 'fortune'); ?></span> </div>
        </div>
      </div>
	<?php } endif; ?>
     </div>
    <!-- Project Feed / End -->