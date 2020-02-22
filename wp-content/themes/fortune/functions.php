<?php
/** Theme Name: fortune
 *  Theme Core Functions and Codes
 **/
require get_template_directory() . '/functions/menu/default_menu_walker.php';
require get_template_directory() . '/functions/menu/fortune_nav_walker.php';
require get_template_directory(). '/inc/icon-functions.php';
require_once dirname(__FILE__) . '/default_options.php';
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';
require get_template_directory() . '/functions/customize/contact-widgets.php';
include get_template_directory() . '/inc/dashboard.php';
include get_template_directory() . '/inc/include-kirki.php';
include get_template_directory() . '/inc/class-fortune-kirki.php';

function fortune_customizer_config()
{
    $args = array(
        'capability'   => 'edit_theme_options',
        'option_type'  => 'option',
        'option_name'  => 'fortune_theme_options',
        'compiler'     => array(),
        'width'        => '22.3%',
        'description'  => __('Visit our site for more great Products.If you like this theme please rate us 5 star', 'fortune'),
    );
    return $args;
}

add_filter('kirki/config', 'fortune_customizer_config');
require get_template_directory() . '/customizer.php';
add_action('after_setup_theme', 'fortune_theme_setup');
global $fortune_theme_options;
function fortune_theme_setup()
{
    global $content_width;
    //content width
    if (!isset($content_width)) {
        $content_width = 704;
    }
    //supports featured image
    add_theme_support('post-thumbnails');
    load_theme_textdomain('fortune', get_template_directory() . '/lang');
    // image resize according to image layout
    add_image_size('fortune_blog_thumb', 280, 270, true);
    add_image_size('fortune_home_post_thumb', 276, 200, true);
    add_image_size('fortune_portfolio_thumb', 358, 258, true);
    add_image_size('fortune_slider', 1349, 530, true);
    add_image_size('fortune_page_thumb', 346, 332, true);
    add_image_size('fortune_post_single', 704, 328, true);
    // Add theme support for selective refresh for widgets.
    add_theme_support( 'customize-selective-refresh-widgets' );
    // This theme uses wp_nav_menu() in Three locations.
    register_nav_menus( array(
        'primary'    => __( 'Primary menu', 'fortune' ),
        'secondary' => __( 'Topbar Menu', 'fortune' ),
        'social' => __( 'Social Links Menu', 'fortune' ),
    ) );
    // theme support
    add_editor_style(get_stylesheet_uri());
    $args = array('default-color' => '#ffffff');
    add_theme_support('custom-background', $args);
    $args1 = array(
        'flex-width'    => true,
        'width'         => 1349,
        'flex-height'    => true,
        'height'        => 114,
        'default-image' => '',
        'header-text-color'=>'#656464',
        'header-text' => true,
    );
    add_theme_support('custom-header',$args1);
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
    add_theme_support( 'custom-logo', array(
        'height'      => 50,
        'width'       => 150,
        'flex-width'  => true,
    ) );
    /* WooCommerce Support */
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    // Recommend plugins
    add_theme_support( 'recommend-plugins', array(
        'kirki'=>array(
            'name'     => 'Fusion Slider', // The plugin name.
            'active_filename' => 'kirki/kirki.php',
        ),
        'fusion-slider'=>array(
            'name'     => 'Fusion Slider', // The plugin name.
            'active_filename' => 'fusion-slider/fusion-slider.php',
        ),
        'photo-video-gallery-master'=>array(
            'name'     => 'Photo Video Gallery Master', // The plugin name.
            'active_filename' => 'photo-video-gallery-master/photo-video-gallery-master.php',
        ),
        'ultimate-gallery-master'=>array(
            'name'     => 'Ultimate Gallery Master', // The plugin name.
            'active_filename' => 'ultimate-gallery-master/ultimate-gallery-master-lite.php',
        ),
        'social-media-gallery'=>array(
            'name'     => 'Social Media Gallery', // The plugin name.
            'active_filename' => 'social-media-gallery/social-media-gallery.php',
        ),

    ) );
    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );

    /*
     * Enable support for Post Formats.
     * See https://developer.wordpress.org/themes/functionality/post-formats/
     */
    add_theme_support( 'post-formats', array(
        'image',
        'video',
        'audio',
        'quote',
        'link',
    ) );

    add_theme_support( 'starter-content', array(

        'posts' => array(
            'home' => array(
                'template'	=> 'home-page.php',
            ),
            'about' => array(
                'thumbnail' => '{{image-sandwich}}',
            ),
            'contact' => array(
                'thumbnail' => '{{image-espresso}}',
            ),
            'blog' => array(
                'thumbnail' => '{{image-coffee}}',
            )
        ),

        'options' => array(
            'fortune_theme_options[portfolio_home]'=>1,
            'show_on_front' => 'page',
            'page_on_front' => '{{home}}',
            'page_for_posts' => '{{blog}}',
        ),
        'widgets' => array(
            'sidebar-widget' => array(
                'search',
                'text_business_info',
                'text_about',
                'category',
                'tags',
            ),

            'footer-widget' => array(
                'text_business_info',
                'text_about',
                'meta',
                'search',
            ),
        ),

        'nav_menus' => array(
            'primary' => array(
                'name' => __( 'Primary Menu', 'fortune' ),
                'items' => array(
                    'page_home',
                    'page_about',
                    'page_blog',
                    'page_contact',
                ),
            ),
            'secondary' => array(
                'name' => __( 'Top Menu', 'fortune' ),
                'items' => array(
                    'page_home',
                    'page_about',
                    'page_blog',
                    'page_contact',
                ),
            ),
            'social' => array(
                'name' => __( 'Social Links Menu', 'fortune' ),
                'items' => array(
                    'link_yelp',
                    'link_facebook',
                    'link_twitter',
                    'link_instagram',
                    'link_email',
                ),
            ),
        ),
    ) );
}

add_action('wp_enqueue_scripts', 'fortune_enqueue_style');
function fortune_enqueue_style(){
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.css');
    wp_enqueue_style('fortune', get_stylesheet_uri());
    wp_enqueue_style('elements-styles', get_template_directory_uri() . '/css/elements-styles.css');
    if(get_theme_mod('color_scheme')!=""){
        wp_enqueue_style('site-color-scheme', get_template_directory_uri() . '/css/skins/'.get_theme_mod('color_scheme'));
    }
    wp_enqueue_style('fontawesome', get_template_directory_uri() . '/css/fonts/font-awesome/css/font-awesome.css');
    wp_enqueue_style('owl.carousel', get_template_directory_uri() . '/vendor/owl-carousel/owl.carousel.css');
    wp_enqueue_style('owl.theme', get_template_directory_uri() . '/vendor/owl-carousel/owl.theme.css');
    wp_enqueue_style('ElasticSlider', get_template_directory_uri() . '/vendor/ElasticSlider/css/estyle.css');
    wp_enqueue_style('magnific-popup', get_template_directory_uri() . '/vendor/magnific-popup/magnific-popup.css');
    wp_enqueue_script('modernizr', get_template_directory_uri() . '/vendor/modernizr.js', array('jquery'));
    wp_enqueue_script ('htmlshiv', get_template_directory_uri() . '/vendor/htmlshiv.js');
    wp_script_add_data( 'htmlshiv', 'conditional', 'lt IE 9' );
    wp_enqueue_script ('respond', get_template_directory_uri() . '/vendor/respond.js');
    wp_script_add_data( 'respond', 'conditional', 'lt IE 9' );
    wp_enqueue_style('animate', get_template_directory_uri() . '/css/animate.css');
    $fortune_custom_css='.header .logo h1, .header .logo .tagline, .fhmm .navbar-collapse .navbar-nav > li > a{color:#'. esc_attr(get_header_textcolor()).';}';
    wp_add_inline_style( 'fortune', $fortune_custom_css );
    if (is_singular()) {
        wp_enqueue_script("comment-reply");
    }
    wp_enqueue_style('Playfair','//fonts.googleapis.com/css?family=Open+Sans+Condensed:300|Playfair+Display:400italic');
    wp_enqueue_style('Goudy', '//fonts.googleapis.com/css?family=Goudy+Bookletter+1911&text=&');
    wp_enqueue_style('antom','//fonts.googleapis.com/css?family=Anton|Muli:300,400,400italic,300italic|Oswald');
}

add_action('wp_footer', 'fortune_enqueue_in_footer');
function fortune_enqueue_in_footer()
{	$fortune_theme_options = fortune_theme_options();
    wp_enqueue_script('jquery-migrate-1.2.1', get_template_directory_uri() . '/vendor/jquery-migrate-1.2.1.js', array('jquery'));
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/vendor/bootstrap.js', array('jquery'));
    wp_enqueue_script('headhesive.js', get_template_directory_uri() . '/vendor/headhesive.js', array('jquery'));
    wp_enqueue_script('jquery.magnific-popup', get_template_directory_uri() . '/vendor/magnific-popup/jquery.magnific-popup.js', array('jquery'));
    if(class_exists('WooCommerce')){
        if(is_shop() || is_cart() || is_product() || is_checkout() || is_product_category()){
            wp_enqueue_script('jquery.dcjqaccordion', get_template_directory_uri() . '/vendor/jquery.dcjqaccordion.js', array('jquery'));
            $dcjq ='  jQuery(".product-categories").dcAccordion({
					saveState: false,
					autoExpand: true,
					showCount: true,
				});
			jQuery(".dcjq-icon").click(function(){
				jQuery(this).toggleClass("less");
			});';
            wp_add_inline_script('jquery.dcjqaccordion',$dcjq);
        }
    }
    wp_enqueue_script('owl.carousel', get_template_directory_uri() . '/vendor/owl-carousel/owl.carousel.js', array('jquery'));
    wp_enqueue_script('ElasticSlider-js', get_template_directory_uri() . '/vendor/ElasticSlider/js/jquery.eislideshow.js');
    wp_enqueue_script('jquery.fitvids', get_template_directory_uri() . '/vendor/jquery.fitvids.js', array('jquery'));
    wp_enqueue_script('jquery.appear', get_template_directory_uri() . '/vendor/jquery.appear.js', array('jquery'));
    wp_enqueue_script('jquery.easing', get_template_directory_uri() . '/vendor/jquery.easing.1.3.js', array('jquery'));
    wp_enqueue_script('custom', get_template_directory_uri() . '/vendor/custom.js', array('jquery'));
    wp_localize_script('custom','header',array('is_sticky'=>$fortune_theme_options['headersticky']));

}
// Read more tag to formatting in blog page
function fortune_content_more($read_more)
{
    return '<div class=""><a class="main-button" href="' . get_permalink() . '">' . __('Read More', 'fortune') . '<i class="fa fa-angle-right"></i></a></div>';
}

add_filter('the_content_more_link', 'fortune_content_more');
// Replaces the excerpt "more" text by a link
function fortune_excerpt_more($more)
{	return '<footer class="entry-footer">
		<a href="'.esc_url(get_permalink()).'" class="btn btn-default">' . __('Read More', 'fortune') . '</a>
	</footer>';
}

add_filter('excerpt_more', 'fortune_excerpt_more');
/*
 * fortune widget area
 */
add_action('widgets_init', 'fortune_widget');
function fortune_widget()
{
    /*sidebar*/
    $fortune_theme_options = fortune_theme_options();
    $col                = 12 / (int) $fortune_theme_options['footer_layout'];
    register_sidebar(array(
        'name'          => __('Sidebar Widget Area', 'fortune'),
        'id'            => 'sidebar-widget',
        'description'   => __('Sidebar widget area', 'fortune'),
        'before_widget' => '<div id="%1$s" class="%2$s widget widget__sidebar">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'fortune'),
        'id'            => 'footer-widget',
        'description'   => __('Footer widget area', 'fortune'),
        'before_widget' => '<div id="%1$s" class="%2$s col-sm-6 col-md-'.$col.'">
								<div class=" widget widget__footer">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}

/* Breadcrumbs  */
function fortune_breadcrumbs()
{
    $delimiter = "";
    $home      = __('Home', 'fortune'); // text for the 'Home' link
    $pre_text  = '';
    $before    = '<li>'; // tag before the current crumb
    $after     = '</li>'; // tag after the current crumb
    echo '<ul class="breadcrumb">';
    global $post;
    $homeLink = home_url();
    echo '<li>' . $pre_text . '<a href="' . $homeLink . '">' . $home . '</a>' . $after;
    if (is_category()) {
        global $wp_query;
        $cat_obj   = $wp_query->get_queried_object();
        $thisCat   = $cat_obj->term_id;
        $thisCat   = get_category($thisCat);
        $parentCat = get_category($thisCat->parent);
        if ($thisCat->parent != 0) {
            echo (get_category_parents($parentCat, true, ' ' . $delimiter . '</li> '));
        }

        echo $before .  single_cat_title('', false) . $after;
    } elseif (is_day()) {
        echo '<li><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a>' . $delimiter . '</li>';
        echo '<li><a href="' . esc_url(get_month_link(get_the_time('Y')), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter;
        echo $before . get_the_time('d') . '</li>';
    } elseif (is_month()) {
        echo '<li><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a>' . $delimiter;
        echo $before . get_the_time('F') . '</li>';
    } elseif (is_year()) {
        echo $before . get_the_time('Y') . '</li>';
    } elseif (is_single() && !is_attachment()) {
        if (get_post_type() != 'post') {
            $post_type = get_post_type_object(get_post_type());
            $slug      = $post_type->rewrite;
            echo '<li><a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter;
            echo $before . get_the_title() . '</li>';
        } else {
            $cat = get_the_category();
            $cat = $cat[0];
            echo $before . get_the_title() . '</li>';
        }
    } elseif (is_search()) {
        echo $before . __('Search results for: ', 'fortune') . '"' . esc_attr(get_search_query()) . '"' . $after;
    } elseif (!is_single() && !is_page() && get_post_type() && get_post_type() != 'post') {
        $post_type = get_post_type_object(get_post_type());
        echo $before . $post_type->labels->singular_name . $after;
    } elseif (is_attachment()) {
        $parent = get_post($post->post_parent);
        $cat    = get_the_category($parent->ID);
        $cat    = $cat[0];
        echo get_category_parents($cat, true, ' ' . $delimiter . ' ');
        echo '<li><a href="' . esc_url(get_permalink($parent)) . '">' . $parent->post_title . '</a>' . $delimiter;
        echo $before . esc_attr(get_the_title()) . $after;
    } elseif (is_page() && !$post->post_parent) {
        echo $before . esc_attr(get_the_title()) . $after;
    } elseif (is_page() && $post->post_parent) {
        $parent_id   = $post->post_parent;
        $breadcrumbs = array();
        while ($parent_id) {
            $page          = get_page($parent_id);
            $breadcrumbs[] = '<li><a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_attr(get_the_title($page->ID)) . '</a></li>';
            $parent_id     = $page->post_parent;
        }
        $breadcrumbs = array_reverse($breadcrumbs);
        foreach ($breadcrumbs as $crumb) {
            echo $crumb . ' ' . $delimiter . ' ';
        }

        echo $before . esc_attr(get_the_title()) . $after;
    }elseif (is_tag()) {
        echo $before . single_tag_title('', false) . $after;
    } elseif (is_author()) {
        global $author;
        $userdata = get_userdata($author);
        echo $before . $userdata->display_name . $after;
    } elseif (is_404()) {
        echo '<li>' . _e(" Error 404 ", 'fortune') . '</li>';
    }
    //echo do_shortcode('[Web-Dorado_Zoom]');    
    echo do_shortcode('[zeno_font_resizer]');
    echo '</ul>';
}
/* add a class to avatar */
add_filter('get_avatar','fortune_change_avatar_css');
function fortune_change_avatar_css($class) {
    $class = str_replace("class='avatar", "class='gravatar ", $class) ;
    return $class;
}
/* change class of comment reply link */
add_filter('comment_reply_link', 'fortune_replace_reply_link_class');
function fortune_replace_reply_link_class($class){
    $class = str_replace("class='comment-reply-link", "class='btn btn-sm btn-default", $class);
    $class = str_replace(">Reply</a>", "><i class=\"fa fa-reply\"></i>"._e("Reply", 'fortune')."</a>", $class);
    return $class;
}
function fortune_comments($comments, $args, $depth)
{
    $GLOBALS['comment'] = $comments;
    extract($args, EXTR_SKIP);
    if ('div' == $args['style']) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    ?>
    <li  <?php comment_class("comment"); ?>>
    <div class="comment-wrapper">
        <div class="comment-author vcard"><?php
            if ($args['avatar_size'] != 0) {
                echo get_avatar($comments, $args['avatar_size']);
            } ?>
            <h5><?php printf('%s', esc_attr(get_comment_author()));?></h5>
            <span class="says"><?php _e('says:','fortune'); ?></span>
            <div class="comment-meta"> <a href="#"><?php printf(__('%1$s at %2$s', 'fortune'), get_comment_date(), get_comment_time());?></a>
                <?php edit_comment_link(); ?>
            </div>
        </div><?php
        if ($comments->comment_approved != '0') { ?>
            <div class="comment-reply"> <?php comment_reply_link(array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'])));?> </div>
            <div class="comment-body"><?php comment_text();?></div><?php
        }else{
            echo '<p>'.__('Your comment is awaitting for moderation.','fortune').'</p>';
        } ?>
    </div>
    <?php
}
/* Blog Pagination */
if (!function_exists('fortune_pagination')) {
    function fortune_pagination() {
        global $wp_query;
        $big = 999999999; // need an unlikely integer
        $pages = paginate_links( array(
            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var('paged') ),
            'total' => $wp_query->max_num_pages,
            'prev_next' => false,
            'type'  => 'array',
            'prev_next'   => TRUE,
            'prev_text'    => '&#171;',
            'next_text'    => '&#187;',
        ) );
        if( is_array( $pages ) ) {
            $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
            echo '<div class="text-center"><ul class="pagination-custom list-unstyled list-inline">';
            foreach ( $pages as $page ) {
                $page = str_replace('page-numbers','btn btn-sm btn-default',$page);
                echo "<li>$page</li>";
            }
            echo '</ul></div>';
        }
    }
}

/* TGMPA register */
add_action('tgmpa_register', 'fortune_register_required_plugins');
function fortune_register_required_plugins()
{
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        // This is an example of how to include a plugin bundled with a theme.
        array(
            'name'     => 'Kirki', // The plugin name.
            'slug'     => 'kirki', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        ),

        array(
            'name'     => 'Fusion Slider', // The plugin name.
            'slug'     => 'fusion-slider', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        ),

        array(
            'name'     => 'Photo Video Gallery Master', // The plugin name.
            'slug'     => 'photo-video-gallery-master', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        ),
        array(
            'name'     => 'Ultimate Gallery Master', // The plugin name.
            'slug'     => 'ultimate-gallery-master', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        ),
        array(
            'name'     => 'Social Media Gallery', // The plugin name.
            'slug'     => 'social-media-gallery', // The plugin slug (typically the folder name).
            'required' => false, // If false, the plugin is only 'recommended' instead of required.
        ),
    );
    $config = array(
        'id'           => 'fortune', // Unique ID for hashing notices for multiple instances of fortune.
        'default_path' => '', // Default absolute path to bundled plugins.
        'menu'         => 'fortune-install-plugins', // Menu slug.
        'parent_slug'  => 'themes.php', // Parent menu slug.
        'capability'   => 'edit_theme_options', // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => true, // Show admin notices or not.
        'dismissable'  => true, // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '', // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false, // Automatically activate plugins after installation or not.
        'message'      => '', // Message to output right before the plugins table.
    );
    tgmpa($plugins, $config);
}

/* Woocommerce supoport */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_before_main_content', 'fortune_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'fortune_theme_wrapper_end', 10);
function fortune_theme_wrapper_start()
{?>
    <section class="page-heading">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <?php if(!is_product()): ?>
                    <h1><?php woocommerce_page_title(); ?></h1>
                <?php else: 
                    woocommerce_template_single_title();
                endif;  ?>
            </div>
            <div class="col-md-6">
                <?php fortune_breadcrumbs(); ?>
            </div>
        </div>
    </div>
    </section><?php
    echo '<section class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">';
}
function fortune_theme_wrapper_end()
{?>
    </div><?php get_sidebar(); ?></div></div>
    </section>
<?php }
add_filter( 'woocommerce_show_page_title' , 'fortune_hide_page_title' );
/**
 * fortune_hide_page_title
 *
 * Removes the "shop" title on the main shop page
 */
function fortune_hide_page_title() {
    return false;
}
/**
 * Removes breadcrumbs
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
/** Remove Showing results functionality site-wide */
function woocommerce_result_count() {
    return;
}

add_filter( 'get_product_search_form' , 'fortune_custom_product_searchform' );

/**
 * fortune_custom_product_searchform
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function fortune_custom_product_searchform( $form ) {
    $form = '<form action="'.esc_url(home_url("/")).'" role="search" method="get" class="woocommerce-product-search">
	  <div class="input-group">
		<input type="text" value="' . get_search_query() . '" name="s" id="s" class="form-control" placeholder="'.__("Search Product...","fortune").'">
		<span class="input-group-btn">
		<button id="searchsubmit" class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
		<input type="hidden" name="post_type" value="product" />
		</span> </div>
	</form>';
    return $form;

}

// Create a helper function for easy SDK access.
function for_fs() {
    global $for_fs;

    if ( ! isset( $for_fs ) ) {
        // Include Freemius SDK.
        require_once dirname(__FILE__) . '/freemius/start.php';

        $for_fs = fs_dynamic_init( array(
            'id'                  => '1449',
            'slug'                => 'fortune',
            'type'                => 'theme',
            'public_key'          => 'pk_25f2d7322739197c9d1f6be408a9f',
            'is_premium'          => false,
            'has_addons'          => false,
            'has_paid_plans'      => false,
            'menu'                => array(
                'slug'           => 'ft-fortune',
                'account'        => false,
                'parent'         => array(
                    'slug' => 'themes.php',
                ),
            ),
        ) );
    }

    return $for_fs;
}

// Init Freemius.
for_fs();
// Signal that SDK was initiated.
do_action( 'for_fs_loaded' );
add_filter( 'woocommerce_get_availability', 'wcs_custom_get_availability', 1, 2);
function wcs_custom_get_availability( $availability, $_product ) {

    if ( $_product->is_in_stock() ) {
        $availability['availability'] = __('Na sklade', 'woocommerce');
    }

    if ( ! $_product->is_in_stock() ) {
        $availability['availability'] = __('Nie je na sklade', 'woocommerce');
    }

    if ( $_product->is_on_backorder() ) {
        $availability['availability'] = __('Na objednávku', 'woocommerce');
    }

    return $availability;
}

add_filter( 'woocommerce_email_attachments', 'attach_terms_conditions_pdf_to_email', 10, 3);

function attach_terms_conditions_pdf_to_email ( $attachments , $email_id, $object ) {

    if( $email_id === 'customer_processing_order'){
/*
'cancelled_order','customer_processing_order',customer_completed_order','customer_invoice','customer_new_account','customer_note','customer_on_hold_order','customer_refunded_order','customer_reset_password','failed_order','new_order',
*/
        $customer_id = $object->get_customer_id();
        $ico = get_user_meta( $customer_id, "registration_field_1", true );

        $attachments = array();

        if ( empty( $ico ) ) {
            $pdf_path = ABSPATH . '/wp-content/documents/VOP_FOaPO.pdf';
            array_push($attachments,$pdf_path);
            $pdf_path = ABSPATH . '/wp-content/documents/Poucenie_o_uplatneni_prava_spotrebitela.pdf';
            array_push($attachments,$pdf_path);
            $pdf_path = ABSPATH . '/wp-content/documents/Odstupenie_od_zmluvy.pdf';
            array_push($attachments,$pdf_path);
        } else {
            //$pdf_path = ABSPATH . '/wp-content/documents/Odstupenie_od_zmluvy.pdf';
            //array_push($attachments,$pdf_path);
        }
    }

	return $attachments;
}

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

function custom_action_woocommerce_new_order( $order_id, $instance ) {
    $order = new WC_Order( $order_id );

    $subject_text = "Nová objednávka č." . $order->id;

/*
    write_log( '--- order begin ---' );
    write_log( $order );
    write_log( '--- order end ---' );
*/

    // load the mailer class
    $mailer = WC()->mailer();
    //format the email
    $recipient = "status.ecobag@gmail.com";
    $subject = __($subject_text, 'theme_name');
    $content = get_custom_email_admin_new_order( $order, $subject, $mailer );
    $headers = "Content-Type: text/html\r\n";
    //send the email through wordpress
    $mailer->send( $recipient, $subject, $content, $headers );


    $subject_text = "Oznámenie o zaevidovaní objednávky č." . $order->id;

    // load the mailer class
    $mailer = WC()->mailer();
    //format the email
    $recipient = $order->get_billing_email();
    $subject = __($subject_text, 'theme_name');
    $content = get_custom_email_customer_new_order( $order, $subject, $mailer );
    $headers = "Content-Type: text/html\r\n";
    //send the email through wordpress
    $mailer->send( $recipient, $subject, $content, $headers );

};
add_action( "woocommerce_new_order", 'custom_action_woocommerce_new_order', 10, 2 );

function get_custom_email_admin_new_order( $order, $heading = false, $mailer ) {
	$template = 'emails/admin-new-order.php';
	return wc_get_template_html( $template, array(
		'order'         => $order,
		'email_heading' => $heading,
		'sent_to_admin' => false,
		'plain_text'    => false,
		'email'         => $mailer
	) );
}

function get_custom_email_customer_new_order( $order, $heading = false, $mailer ) {
	$template = 'emails/customer-new-order.php';
	return wc_get_template_html( $template, array(
		'order'         => $order,
		'email_heading' => $heading,
		'sent_to_admin' => false,
		'plain_text'    => false,
		'email'         => $mailer
	) );
}
?>