<?php $fortune_theme_options = fortune_theme_options(); 

if($fortune_theme_options['callout_home']==false) return; ?>
<!-- Section Full Width -->
<section class="section primary section__fullw section__no-bottom-margin section__close-to-footer" id="callout">
  <div class="container">
	<div class="call-to-action">
	  <div class="cta-txt">
		<h2 id="callout-title"><?php echo esc_attr($fortune_theme_options['callout_title']);?></h2>
	  </div><?php if($fortune_theme_options['callout_btn_link']!=""){?>
	  <div class="cta-btn"> <a id="callout_btn_link" href="<?php echo esc_url($fortune_theme_options['callout_btn_link']);?>" class="btn btn-default"><?php echo esc_attr($fortune_theme_options['callout_btn_text']);?></a> </div>
	</div>
	<?php } ?>
  </div>
</section>
<!-- Section Full Width / End -->