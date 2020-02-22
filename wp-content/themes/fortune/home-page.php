<?php 
/* Template Name: Home */
get_header();
get_template_part('home','slider'); 
$fortune_theme_options = fortune_theme_options(); ?>
<section class="page-content">
  <div class="container">
    <?php foreach($fortune_theme_options['home_sections'] as $section){
			get_template_part('home',$section);
			} ?>
  </div>
</section>
<!-- Page Content / End -->
<?php 
get_template_part('home','social');
get_footer();?>
