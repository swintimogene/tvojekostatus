<?php get_header(); ?>
<section class="page-content">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center">
        <h2 class="error-title"><?php _e('404','fortune'); ?></h2>
        <h3><?php esc_attr_e('We are sorry, but the page you were looking for doesn\'t exist','fortune'); ?></h3>
        <p class="error-desc"><?php esc_attr_e('Please try using our search box below to look for information on the our site.','fortune'); ?></p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4 col-md-offset-4">
       <?php get_search_form();?>
      </div>
    </div>
    <div class="spacer-lg"></div>
  </div>
</section>
<?php get_footer(); ?>