<!-- Footer -->
<?php $fortune_theme_options = fortune_theme_options();
$col = 12 / (int)$fortune_theme_options['footer_layout']; ?>
<div>
<?php //echo do_shortcode("[enjoyinstagram_mb]"); ?>
<?php echo do_shortcode("[iscwp-slider username='tvoj_ekostatus' ]"); ?>
</div>
<footer class="footer" id="footer" role="contentinfo"><?php
    if ($fortune_theme_options['show_footer_widget']==true){?>
        <div class="footer-widgets">
        <div class="container">
            <div class="row" role="complementary">
                <?php 
                if(is_active_sidebar('footer-widget')){
                    dynamic_sidebar('footer-widget'); 
                }else{
                    $args = array('before_widget' => '<div class="widget_archive col-sm-6 col-md-'.$col.'">
                                <div class=" widget widget__footer">',
                    'after_widget'  => '</div></div>',
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>');
                    the_widget('WP_Widget_Calendar', null, $args);
                    the_widget('WP_Widget_Archives', null, $args);
                    the_widget('WP_Widget_Tag_Cloud', null, $args);
                    the_widget('WP_Widget_Search', null, $args);
                }?>
            </div>
        </div>
        </div><?php
    } ?>
    <div class="footer-copyright">
        <div class="container">
            <div class="row">
                <div id="f-copyright" class="col-sm-6 col-md-4"> <?php echo esc_attr($fortune_theme_options['footer_copyright'] . ' ' . $fortune_theme_options['developed_by_text']); ?>
                    <a href="<?php echo esc_url($fortune_theme_options['developed_by_link']); ?>"><?php echo esc_attr($fortune_theme_options['developed_by_link_text']); ?></a> </div>
                <?php	if ( $fortune_theme_options['social_footer']==true ) {?>
                    <div id="social_footer" class="col-sm-6 col-md-8">
                    <div class="social-links-wrapper"> <span class="social-links-txt"><?php _e('Connect with us', 'fortune'); ?></span>
                        <?php
                        if(has_nav_menu( 'social' )){
                        wp_nav_menu( array(
                            'theme_location' => 'social',
                            'container'		=>false,
                            'menu_class'     => 'social-links social-links__dark',
                            'depth'          => 1,
                            'link_before'    => '<span class="screen-reader-text">',
                            'link_after'     => '</span>' . fortune_get_icon( array( 'icon' => 'chain' ) ),
                        ) );
                    }else{ ?>
                        <ul id="menu-social-menu" class="social-links social-links__dark"><li id="menu-item-1488" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1488"><a href="http://facebook.com"><span class="screen-reader-text"><?php _e('facebook','fortune'); ?></span><i class="fa fa-facebook"></i></a></li>
                        <li id="menu-item-1489" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1489"><a href="http://twitter.com/"><span class="screen-reader-text"><?php _e('twitter','fortune'); ?></span><i class="fa fa-twitter"></i></a></li>
                        <li id="menu-item-1490" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1490"><a href="http://instagram.com"><span class="screen-reader-text"><?php _e('instagram','fortune'); ?></span><i class="fa fa-instagram"></i></a></li>
                        <li id="menu-item-1491" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1491"><a href="http://plus.google.com"><span class="screen-reader-text"><?php _e('Google+','fortune'); ?></span><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                     <?php  } ?>
                    </div>
                    </div><?php
                }
                ?>
            </div>
        </div>
    </div>
</footer>
<!-- Footer / End -->
</div>
<!-- Main / End -->
</div>
<?php wp_footer();?>
<script>
    jQuery(document).ready(function(){
        jQuery("body").tooltip({ selector: '[data-toggle=tooltip]' });
    });
</script>
</body>
</html>