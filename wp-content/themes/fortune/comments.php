<?php 
if ( post_password_required() ) {
	return;
}
if (have_comments()): ?>
<div class="comments-wrapper">
  <h3><?php //esc_url(comments_popup_link(__('No Comments', 'fortune'), __('(1) Comment', 'fortune'), __('(%) Comments', 'fortune')));
  printf( // WPCS: XSS OK.
					esc_html( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'fortune' ) ),
					number_format_i18n( get_comments_number() ),
					'<span>' . get_the_title() . '</span>'
				);
   ?></h3>
  <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
                <h3 class="screen-reader-text"><?php _e('Comment navigation', 'fortune'); ?></h3>

                <div class="nav-previous">
                    <?php previous_comments_link(__('&larr; Older Comments', 'fortune')); ?>
                </div>
                <div class="nav-next">
                    <?php next_comments_link(__('Newer Comments &rarr;', 'fortune')); ?>
                </div>
            </nav><!-- #comment-nav-above --><?php
        endif; // Check for comment navigation.
        ?>
  <ol class="commentlist">
  	 <?php wp_list_comments('callback=fortune_comments&style=ol'); ?>  
  </ol>
  <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
                <h3 class="screen-reader-text"><?php _e('Comment navigation', 'fortune'); ?></h3>

                <div class="nav-previous">
                    <?php previous_comments_link(__('&larr; Older Comments', 'fortune')); ?>
                </div>
                <div class="nav-next">
                    <?php next_comments_link(__('Newer Comments &rarr;', 'fortune')); ?>
                </div>
            </nav><!-- #comment-nav-above --><?php
        endif; // Check for comment navigation.
        ?>
</div><?php
endif; 
if (comments_open()) { ?>
<div id="respond" class="comment-respond">
<?php
                $fields = array(
                    'author' => '<div class="row"><div class="col-md-6"><div class="form-group">
					<label for="author">' . __('Name', 'fortune') . ' <span class="required">*</span></label>
					<input type="text" class="form-control" id="author" name="author"></div>',
                    'email' => '<div class="form-group"><label for="email">' . __('Email', 'fortune') . ' <span class="required">*</span></label><input type="text" class="form-control" id="email" name="email"></div>',
                    'website' => '<div class="form-group"><label for="url">' . __('Website', 'fortune') . ' </label><input type="text" class="form-control" id="url" name="url"></div></div></div>',
                );
                function fortune_defaullt_fields($fields)
                {
                    return $fields;
                }

                add_filter('comment_form_default_fields', 'fortune_defaullt_fields');
                $comments_args = array(
                    'fields' => apply_filters('comment_form_default_fields', $fields),
                    'label_submit' => __('Submit Message', 'fortune'),
                    'title_reply_to' => '<h3 class="reply-title">' . __('Leave a Reply to %s', 'fortune') . '</h3>',
                    'title_reply' => '<h3 class="reply-title">' . __("Leave a reply", 'fortune') . '</h3>',
                    'comment_notes_after' => '',
                    'comment_field' => '<div class="form-group">
										<label for="comment">' . __("Comment", 'fortune') . '</label>
										<textarea cols="30" rows="10" class="form-control" id="comment" name="comment"></textarea>
									</div>',
                    'class_submit' => 'btn btn-primary',
                );
                comment_form($comments_args);?>	
</div>
<?php } ?>