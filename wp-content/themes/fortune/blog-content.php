<!-- Post (Standard Format) -->
<?php $class = get_post_format()=="" ? 'standard' : get_post_format(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('entry entry__'.$class); ?> >
	<div class="row"><?php
			$col = 12;
			if(has_post_thumbnail()){
			$img_class = array('class'=>'img_responsive');
			$col = 7; ?>
		<div class="col-sm-5 col-md-5">
			<figure class="alignnone entry-thumb">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('fortune_blog_thumb', $img_class);?></a>
			</figure>
		</div><?php
			} ?>
		<div class="col-sm-<?php echo $col; ?> col-md-<?php echo (int)$col; ?>">
			<header class="entry-header">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry-meta">
					<span class="entry-date"><?php if(get_the_title()!=""){ echo get_the_date(get_option('date_format'), get_the_ID());}else{ ?><a href="<?php the_permalink(); ?>"><?php echo get_the_date(get_option('date_format'), get_the_ID()); ?></a><?php } ?></span>
					<span class="entry-comments"><?php esc_url(comments_popup_link(__('No Comments', 'fortune'), __('1 Comment', 'fortune'), __('% Comments', 'fortune'))); ?></span><?php 
					if (get_the_category_list() != '') {?>
					<span class="entry-category"><?php _e('in ','fortune');  echo get_the_category_list(','); ?></span><?php
					} if(get_the_tag_list()!=""){ ?>
					<span class="entry-category"><?php _e('Tags  ','fortune');  echo get_the_tag_list('',', ',''); ?></span><?php
					} ?>
					<span class="entry-author"><?php _e('by','fortune'); ?> <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php esc_attr(the_author()); ?></a></span>
				</div>
			</header>
            <?php is_page_template('index.php') ? the_content() : the_excerpt();?>		</div>
	</div>
</article>
<!-- Post (Standard Format) / End -->