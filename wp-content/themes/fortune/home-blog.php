<?php 
$fortune_theme_options = fortune_theme_options();
if(!$fortune_theme_options['blog_home']){return;}

if($fortune_theme_options['blog_title']!=""){
$home_blog_title = explode(' ', $fortune_theme_options['blog_title']);
if (isset($home_blog_title[1])) {
	$home_blog_title = preg_split("/\s+/", $fortune_theme_options['blog_title']);
	// Replace the first word.
	$home_blog_title[0] = $home_blog_title[0];
	$home_blog_title[1] = "<span>" . $home_blog_title[1] . "</span>";
	// Re-create the string.
	$home_blog_title = join(" ", $home_blog_title);
	stripslashes($home_blog_title);
} else {
	$home_blog_title = $fortune_theme_options['blog_title'];
}
?>
<!-- Recent Posts -->

<div class="title-decorated" data-animation="fadeInUp">
  <h1 id="blog-heading"><?php echo stripslashes($home_blog_title); ?></h1>
</div>
<?php } if($fortune_theme_options['blog_desc']!=""){ ?>
<p class="text-center" data-animation="fadeInUp" id="blog-desc"><?php echo esc_attr($fortune_theme_options['blog_desc']); ?></p>
<?php } ?>
<div class="spacer-xl"></div>
<div class="row">
  <div id="owl-carousel" class="owl-carousel owl-carousel__posts">
    <?php  	$all_posts = wp_count_posts('post')->publish;
		$query = array('post_type' => 'post', 'posts_per_page' =>$all_posts ,'post__not_in' => get_option( 'sticky_posts' ), 'category__not_in'=>array($fortune_theme_options['slider_category']));
		if(isset($fortune_theme_options['home_post_cat']) && $fortune_theme_options['home_post_cat']!=""){
			$query['category__in']=$fortune_theme_options['home_post_cat'];
		}
		if (query_posts($query)) {
			while (have_posts()):the_post(); ?>
    <div class="project-item">
      <div class="project-item-inner">
	  <?php if(has_post_thumbnail()){?>
        <figure class="alignnone project-img"><?php
			$img_class = array('class'=>'img_responsive');
			the_post_thumbnail('fortune_home_post_thumb', $img_class); ?>
          <div class="overlay"> <a href="<?php the_permalink(); ?>" class="dlink"><i class="fa fa-link"></i></a> </div>
        </figure><?php
		} ?>
        <div class="project-desc">
          <div class="meta">
            </a> <span class="date"><?php echo get_post_time(get_option('date_format'), true); ?></span> </div>
          <h4 class="title"><a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
            </a></h4>
        </div>
      </div>
    </div>
    <?php
	endwhile;
	} ?>
  </div>
</div>
<?php if($all_posts>4){ ?>
<div class="prev-next-holder text-center"> <a class="prev-btn" id="carousel-prev"><i class="fa fa-angle-left"></i></a> <a class="next-btn" id="carousel-next"><i class="fa fa-angle-right"></i></a> </div>
<?php } ?>
<!-- Recent Posts / End -->
<div class="spacer"></div>
