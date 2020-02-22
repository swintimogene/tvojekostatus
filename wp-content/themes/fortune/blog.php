<?php /* Template Name: Blog */
get_header(); ?>
<!-- Page Heading -->
<section class="page-heading">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<h1><?php the_title(); ?></h1>
			</div>
			<div class="col-md-6">
				<?php fortune_breadcrumbs(); ?>
			</div>
		</div>
	</div>
</section>
<!-- Page Heading / End -->
<!-- Page Content -->
<section class="page-content">
	<div class="container">

		<div class="row">
			<div class="content col-md-8"><?php
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$args = array('post_type' => 'post', 'paged' => $paged);
				$wp_query = new WP_Query($args);
				while ($wp_query->have_posts()):
					$wp_query->the_post();
					global $read_more;
					$read_more = 0;
					get_template_part('blog', 'content');
				endwhile;
                wp_link_pages();
				fortune_pagination(); ?>
			</div>
			<?php get_sidebar();?>
		</div>
	</div>
</section>
<?php get_footer(); ?>