<aside class="sidebar col-md-3 <?php echo is_page_template('page-ls.php') ? 'col-md-pull-9' : 'col-md-offset-1'; ?> col-bordered">
  <hr class="visible-sm visible-xs lg">
   <?php if (is_active_sidebar('sidebar-widget')) {
				dynamic_sidebar('sidebar-widget');
			} else {
				$args = array(
					'before_widget' => '<div class="widget_categories widget widget__sidebar">',
					'after_widget'  => '</div>',
					'before_title'  => '<h3 class="widget-title">',
					'after_title'   => '</h3>',
				);
				the_widget('WP_Widget_Tag_Cloud', null, $args);
				the_widget('WP_Widget_Meta', null, $args);
			} ?>
 </aside>
