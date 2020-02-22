<?php get_header(); ?>
<!-- Page Heading -->
<section class="page-heading">
	<div class="container">
		<div class="row">
		<?php if(have_posts()){ ?>
			<div class="col-md-6">
				<h1><?php the_archive_title(); ?></h1>
			</div><?php
			} ?>
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
				if(have_posts()){
				while (have_posts()): the_post();
					get_template_part('blog', 'content');
				endwhile;
				}
				wp_link_pages();
				fortune_pagination(); ?>
			</div>
			<?php get_sidebar();?>
		</div>
	</div>
</section>
<?php get_footer(); ?>