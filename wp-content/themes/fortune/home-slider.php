<?php
$fortune_theme_options = fortune_theme_options();
if(!$fortune_theme_options['slider_home']){return;}
if($fortune_theme_options['slider_plugin_code']){
    echo do_shortcode($fortune_theme_options['slider_plugin_code']);
}else{
    $slider_category_id = $fortune_theme_options['slider_category'] != ''? (int)$fortune_theme_options['slider_category'] : '';
    $fortune_slider_arg = array(
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'order'          => 'desc',
        'orderby'        => 'date',
        'ignore_sticky_posts' => 1,
        'category__in' => array($slider_category_id),
    );
    $fortune_slider = new WP_Query($fortune_slider_arg) ?>
    <div class="wrapper" >
        <div id="ei-slider" class="ei-slider"><?php
            if($slider_category_id!=""){?>
                <ul class="ei-slider-large">
                    <?php if($fortune_slider->have_posts()):
                        while($fortune_slider->have_posts()):
                            $fortune_slider->the_post();
                            $slider_image_id = get_post_thumbnail_id();
                            $slider_image = wp_get_attachment_image_src( $slider_image_id, 'fortune_slider'); ?>
                            <li>
                            <img class="img-responsive" src="<?php echo esc_url($slider_image[0]);?>" alt="<?php the_title(); ?>">
                            <div class="ei-title">
                                <h2><?php the_title(); ?></h2>
                                <h3><?php remove_filter ('the_content',  'wpautop'); ?>
                                    <?php the_content(__('Read more','fortune')); ?></h3>
                            </div>

                            </li><?php
                        endwhile;
                    endif;
                    wp_reset_query(); ?>
                </ul>
                <ul class="ei-slider-thumbs">
                <?php if($fortune_slider->have_posts()): ?>
                    <li class="ei-slider-element">Current</li><?php
                    while($fortune_slider->have_posts()):
                        $fortune_slider->the_post();
                        $slider_image_id = get_post_thumbnail_id();
                        $slider_image = wp_get_attachment_image_src( $slider_image_id, 'small'); ?>
                        <li><a href="#"><?php the_title(); ?></a><img src="<?php echo esc_url($slider_image[0]);?>" alt="<?php the_title(); ?>" /></li>
                        <?php
                    endwhile;
                endif;
                wp_reset_postdata(); ?>
                </ul><?php
            }else{

                $imgs = array('girl-from-behind-1741699_1280.jpg', 'gift-444518_1280.jpg', 'calendula-1746254_1280.jpg');?>
                <ul class="ei-slider-large">
                    <?php foreach($imgs as $img){?>
                        <li>
                        <img class="img-responsive" src="<?php echo get_template_directory_uri().'/images/'.$img; ?>" alt="<?php the_title(); ?>">
                        <div class="ei-title">
                            <h2><?php _e('Fortune', 'fortune'); ?></h2>
                            <h3><?php _e('Best WordPress Theme Ever!',  'fortune'); ?></h3>
                        </div>
                        </li><?php
                    } ?>
                </ul>
                <ul class="ei-slider-thumbs">
                    <li class="ei-slider-element">Current</li>
                    <?php foreach($imgs as $img){?>
                        <li><a href="#"><?php the_title(); ?></a><img src="<?php echo get_template_directory_uri().'/images/'.$img; ?>" /></li>
                        <?php
                    }?>
                </ul>
                <style>.ei-title {right: 35%;}</style>
                <?php
            } ?>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(function($) {
            $('#ei-slider').eislideshow({
                speed: <?php echo intval($fortune_theme_options["slider_anim_speed"]); ?>,
                easing		: '<?php echo esc_attr($fortune_theme_options["slider_easing_effect"]); ?>',
                titleeasing	: '<?php echo esc_attr($fortune_theme_options["slider_easing_effect"]); ?>',
                titlespeed	: <?php echo intval($fortune_theme_options["slider_content_anim_speed"]); ?>,
                titlesFactor: 0,
                slideshow_interval	: <?php echo intval($fortune_theme_options["slider_interval"]); ?>,
                autoplay:<?php echo intval($fortune_theme_options["slider_auto_play"]); ?>,
                height:600,
            });
        });
    </script>
<?php } ?>