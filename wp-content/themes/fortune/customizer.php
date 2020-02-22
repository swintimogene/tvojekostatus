<?php
if (class_exists('Kirki')) {
    include 'customizer-extra.php';
}
add_action('customize_register', 'fortune_customizer');
function fortune_customizer($wp_customize)
{
    wp_enqueue_style('custom_css', get_template_directory_uri() . '/css/customizer.css');
    $fortune_theme_options = fortune_theme_options();
    /* Get all stylesheet files */
    $alt_stylesheets = array();
    if (is_dir(get_template_directory() . '/css/skins/')) {
        if ($alt_stylesheet_dir = opendir(get_template_directory() . '/css/skins/')) {
            $alt_stylesheets[''] = __('Select a color scheme--', 'fortune');
            while (($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false) {
                if (stristr($alt_stylesheet_file, ".css") !== false) {
                    $alt_stylesheets[$alt_stylesheet_file] = $alt_stylesheet_file;
                }
            }
        }
    }
    if (!function_exists('fortune_get_categories_select')):
        function fortune_get_categories_select()
    {
            $fortune_cat = get_categories();
            $results;
            if (!empty($fortune_cat)):
                $count              = count($fortune_cat);
                $results['default'] = __('Select Category', 'fortune');
                for ($i = 0; $i < $count; $i++) {
                    if (isset($fortune_cat[$i])) {
                        $results[$fortune_cat[$i]->cat_ID] = $fortune_cat[$i]->name;
                    }
                }
            endif;
            return $results;
        }
    endif;
    if (!function_exists('fortune_get_post_select')):
        function fortune_get_post_select()
    {
            $all_posts = wp_count_posts('post')->publish;
            $latest    = new WP_Query(array(
                'post_type'     => 'post',
                'post_per_page' => $all_posts,
                'post_status'   => 'publish',
                'orderby'       => 'date',
                'order'         => 'DESC',
            ));
            $results;
            if (!empty($latest)):
                $results['default'] = __('Select Post', 'fortune');
                while ($latest->have_posts()) {
                    $latest->the_post();
                    $results[get_the_id()] = get_the_title();

                }
            endif;

            return $results;
        }
    endif;
    /* Genral section */
    $wp_customize->add_panel('fortune_theme_option', array(
        'title'    => __('Theme Options', 'fortune'),
        'priority' => 2, // Mixed with top-level-section hierarchy.
    ));
    $wp_customize->add_section('color_sec',
        array(
            'title'      => __('Color Schemes', 'fortune'),
            'panel'      => 'fortune_theme_option',
            'capability' => 'edit_theme_options',
            'priority'   => 30, // Mixed with top-level-section hierarchy.
        )
    );
    $wp_customize->add_setting('color_scheme',
        array(
            'sanitize_callback' => 'fortune_sanitize_text',
            'default'           => '',
        )
    );
    $wp_customize->add_control('color_scheme', array(
        'label'    => __('Color Scheme', 'fortune'),
        'type'     => 'select',
        'section'  => 'color_sec',
        'settings' => 'color_scheme',
        'choices'  => $alt_stylesheets,
    )
    );
    $wp_customize->add_section('slider_sec',
        array(
            'title'      => __('Slider Options', 'fortune'),
            'panel'      => 'fortune_theme_option',
            'capability' => 'edit_theme_options',
            'priority'   => 35, // Mixed with top-level-section hierarchy.
        )
    );

    $wp_customize->add_setting('fortune_theme_options[slider_home]',
        array(
            'type'              => 'option',
            'sanitize_callback' => 'fortune_sanitize_checkbox',
            'default'           => $fortune_theme_options['slider_home'],
        )
    );
    $wp_customize->add_control('slider_home', array(
        'label'    => __('Show Slider', 'fortune'),
        'section'  => 'slider_sec',
        'settings' => 'fortune_theme_options[slider_home]',
        'type'     => 'checkbox',
    )
    );
    $wp_customize->add_setting('fortune_theme_options[slider_plugin_code]',
        array(
            'type'              => 'option',
            'sanitize_callback' => 'fortune_sanitize_text',
            'default'           => $fortune_theme_options['slider_plugin_code'],
        )
    );
    $wp_customize->add_control('slider_plugin_code', array(
        'label'    => __('Either Put  Slider Plugin Shortcode here or select a post category below to display slider.', 'fortune'),
        'section'  => 'slider_sec',
        'settings' => 'fortune_theme_options[slider_plugin_code]',
        'type'     => 'text',
        'priority' => 10,
    )
    );
    $wp_customize->add_setting('fortune_theme_options[slider_category]',
        array(
            'type'              => 'option',
            'sanitize_callback' => 'fortune_sanitize_number',
            'default'           => '',
        )
    );
    $wp_customize->selective_refresh->add_partial('fortune_theme_options[slider_category]', array(
        'selector'            => '.ei-slider-large li',
        'container_inclusive' => true,
    ));
    ///////////
    $wp_customize->add_setting('fortune_theme_options[slider_auto_play]',
        array(
            'type'              => 'option',
            'sanitize_callback' => 'fortune_sanitize_number',
            'default'           => 1,
        )
    );
    $wp_customize->add_control('slider_auto_play', array(
        'label'    => __('Auto Play Slider', 'fortune'),
        'section'  => 'slider_sec',
        'settings' => 'fortune_theme_options[slider_auto_play]',
        'type'     => 'checkbox',
    )
    );

    $wp_customize->add_control('slider_category', array(
        'label'    => __('Select Category', 'fortune'),
        'section'  => 'slider_sec',
        'settings' => 'fortune_theme_options[slider_category]',
        'type'     => 'select',
        'choices'  => fortune_get_categories_select(),
    )
    );

    /* Service Options */
    $wp_customize->add_section('service_section', array(
        'title'      => __("Service Options", "fortune"),
        'panel'      => 'fortune_theme_option',
        'capability' => 'edit_theme_options',
        'priority'   => 35,
    ));
    $wp_customize->add_setting(
        'fortune_theme_options[home_service_enabled]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['home_service_enabled'],
            'sanitize_callback' => 'fortune_sanitize_checkbox',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_setting(
        'fortune_theme_options[service_type]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['service_type'],
            'sanitize_callback' => 'fortune_sanitize_number',
            'capability'        => 'edit_theme_options',
        )
    );
    $wp_customize->add_control('service_type', array(
        'label'    => __('Service style', 'fortune'),
        'section'  => 'service_section',
        'settings' => 'fortune_theme_options[service_type]',
        'type'     => 'select',
        'choices'  => array(
            1 => __("Default", "fortune"),
            2 => __("Box Style", "fortune"),
        ),
    )
    );
    $wp_customize->add_control('home_service_enabled', array(
        'label'    => __('Enable Home Service', 'fortune'),
        'section'  => 'service_section',
        'settings' => 'fortune_theme_options[home_service_enabled]',
        'type'     => 'checkbox',
    )
    );
    $wp_customize->add_setting(
        'fortune_theme_options[service_heading]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['service_heading'],
            'sanitize_callback' => 'fortune_sanitize_text',
            'capability'        => 'edit_theme_options',
        )
    );
    $wp_customize->add_control('service_heading', array(
        'label'    => __('Service Heading', 'fortune'),
        'section'  => 'service_section',
        'settings' => 'fortune_theme_options[service_heading]',
        'type'     => 'text',
    )
    );
    $wp_customize->selective_refresh->add_partial('fortune_theme_options[service_heading]', array(
        'selector'            => '#service_head',
        'container_inclusive' => true,
    ));
    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_setting('fortune_service_heading_' . $i, array(
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_setting(
            'fortune_theme_options[service_icon_' . $i . ']',
            array(
                'default'           => esc_attr($fortune_theme_options['service_icon_' . $i]),
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'fortune_sanitize_text',
                'transport'         => 'postMessage',
            )
        );

        $wp_customize->add_setting(
            'fortune_theme_options[service_title_' . $i . ']',
            array(
                'default'           => esc_attr($fortune_theme_options['service_title_' . $i]),
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'fortune_sanitize_text',
                'transport'         => 'postMessage',
            )
        );
        $wp_customize->add_setting(
            'fortune_theme_options[service_text_' . $i . ']',
            array(
                'default'           => esc_attr($fortune_theme_options['service_text_' . $i]),
                'type'              => 'option',
                'sanitize_callback' => 'fortune_sanitize_text',
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
            )
        );
        $wp_customize->add_setting(
            'fortune_theme_options[service_link_' . $i . ']',
            array(
                'type'              => 'option',
                'default'           => $fortune_theme_options['service_link_' . $i],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'esc_url_raw',
                'transport'         => 'postMessage',
            )
        );
        $wp_customize->add_setting(
            'fortune_theme_options[service_target_' . $i . ']',
            array(
                'type'              => 'option',
                'default'           => $fortune_theme_options['service_target_' . $i],
                'sanitize_callback' => 'fortune_sanitize_checkbox',
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
            )
        );
        $wp_customize->selective_refresh->add_partial('fortune_theme_options[service_icon_' . $i . ']', array(
            'selector'            => '#service_icon_' . $i,
            'container_inclusive' => true,
        ));
        $wp_customize->selective_refresh->add_partial('fortune_theme_options[service_title_' . $i . ']', array(
            'selector'            => '#service_title_' . $i,
            'container_inclusive' => true,
        ));
        $wp_customize->selective_refresh->add_partial('fortune_theme_options[service_text_' . $i . ']', array(
            'selector'            => '#service_text_' . $i,
            'container_inclusive' => true,
        ));
    }
    $j = array('', __(' One', 'fortune'), __(' Two', 'fortune'), __(' Three', 'fortune'));
    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_control(new Fortune_Customize_Heading($wp_customize, 'fortune_service_heading_' . $i, array(
            'label'   => sprintf(__('Service %s ', 'fortune'), $j[$i]),
            'section' => 'service_section',
        )));
        $wp_customize->add_control(new Fortune_Pro_Control($wp_customize, 'fortune_service_icon' . $i, array(
            'type'     => 'iconpicker',
            'label'    => sprintf(__('Service Icon %s ', 'fortune'), $j[$i]),
            'section'  => 'service_section',
            'settings' => 'fortune_theme_options[service_icon_' . $i . ']',
        )));

        $wp_customize->add_control('fortune_service_title' . $i, array(
            'label'    => __('Service Title', 'fortune') . $j[$i],
            'type'     => 'text',
            'section'  => 'service_section',
            'settings' => 'fortune_theme_options[service_title_' . $i . ']',
        ));
        $wp_customize->add_control('fortune_service_text_' . $i, array(
            'label'    => __('Service Description', 'fortune') . $j[$i],
            'type'     => 'textarea',
            'section'  => 'service_section',
            'settings' => 'fortune_theme_options[service_text_' . $i . ']',
        ));
        $wp_customize->add_control('fortune_service_link_' . $i, array(
            'label'    => __('Service Link', 'fortune') . $j[$i],
            'type'     => 'text',
            'section'  => 'service_section',
            'settings' => 'fortune_theme_options[service_link_' . $i . ']',
        ));
        $wp_customize->add_control('fortune_service_link_target_' . $i, array(
            'label'    => __('Open link in new tab', 'fortune'),
            'type'     => 'checkbox',
            'section'  => 'service_section',
            'settings' => 'fortune_theme_options[service_target_' . $i . ']',
        ));

    }

    /* Portfolio Optionds */
    $wp_customize->add_section('portfolio_section', array(
        'title'      => __("Portfolio Options", "fortune"),
        'panel'      => 'fortune_theme_option',
        'capability' => 'edit_theme_options',
        'priority'   => 35,
    ));
    $wp_customize->add_setting(
        'fortune_theme_options[portfolio_home]',
        array(
            'type'              => 'option',
            'sanitize_callback' => 'fortune_sanitize_checkbox',
            'capability'        => 'edit_theme_options',
            'default'           => 1,
        )
    );
    $wp_customize->add_control('portfolio_home', array(
        'label'    => __('Enable Home Portfolio', 'fortune'),
        'section'  => 'portfolio_section',
        'settings' => 'fortune_theme_options[portfolio_home]',
        'type'     => 'checkbox',
    )
    );

    $wp_customize->add_setting('fortune_theme_options[portfolio_post]',
        array(
            'type'              => 'option',
            'sanitize_callback' => 'fortune_sanitize_number',
            'default'           => '',
        )
    );
    $wp_customize->add_control('portfolio_post', array(
        'label'       => __('Select Post', 'fortune'),
        'description' => __('Select the post in which you have put the shortcode to display portfolio.', 'fortune'),
        'section'     => 'portfolio_section',
        'settings'    => 'fortune_theme_options[portfolio_post]',
        'type'        => 'select',
        'choices'     => fortune_get_post_select(),
    )
    );
    $wp_customize->selective_refresh->add_partial('fortune_theme_options[portfolio_post]', array(
        'selector'            => '.project-feed',
        'container_inclusive' => true,
    ));
    /* Extra Options */
    $wp_customize->add_section('extra_section', array(
        'title'      => __("Extra Section", "fortune"),
        'panel'      => 'fortune_theme_option',
        'capability' => 'edit_theme_options',
        'priority'   => 35,
    ));
    $wp_customize->add_setting(
        'fortune_theme_options[extra_home]',
        array(
            'type'              => 'option',
            'sanitize_callback' => 'fortune_sanitize_checkbox',
            'capability'        => 'edit_theme_options',
            'default'           => 0,
        )
    );
    $wp_customize->add_control('extra_home', array(
        'label'    => __('Enable Home extra', 'fortune'),
        'section'  => 'extra_section',
        'settings' => 'fortune_theme_options[extra_home]',
        'type'     => 'checkbox',
    )
    );

    $wp_customize->add_setting('fortune_theme_options[extra_section]',
        array(
            'type'              => 'option',
            'sanitize_callback' => 'fortune_sanitize_number',
        )
    );
    $wp_customize->add_control('extra_section', array(
        'label'          => __('Select Page', 'fortune'),
        'description'    => __('Select Page to show content on home page.', 'fortune'),
        'section'        => 'extra_section',
        'settings'       => 'fortune_theme_options[extra_section]',
        'type'           => 'dropdown-pages',
        'allow_addition' => true,
    )
    );
    /* Blog Optionds */
    $wp_customize->add_section('blog_section', array(
        'title'      => __("Blog Options", "fortune"),
        'panel'      => 'fortune_theme_option',
        'capability' => 'edit_theme_options',
        'priority'   => 35,
    ));
    $wp_customize->add_setting(
        'fortune_theme_options[blog_home]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['blog_home'],
            'sanitize_callback' => 'fortune_sanitize_checkbox',
            'capability'        => 'edit_theme_options',
        )
    );

    $wp_customize->add_control('blog_home', array(
        'label'    => __('Enable Home Blog', 'fortune'),
        'section'  => 'blog_section',
        'settings' => 'fortune_theme_options[blog_home]',
        'type'     => 'checkbox',
    )
    );
    $wp_customize->add_setting(
        'fortune_theme_options[blog_title]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['blog_title'],
            'sanitize_callback' => 'fortune_sanitize_text',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->selective_refresh->add_partial('fortune_theme_options[blog_title]', array(
        'selector'            => '#blog-heading',
        'container_inclusive' => true,
    ));

    $wp_customize->add_control('blog_title', array(
        'label'    => __('Home Blog Title', 'fortune'),
        'section'  => 'blog_section',
        'settings' => 'fortune_theme_options[blog_title]',
        'type'     => 'text',
    )
    );
    $wp_customize->add_setting(
        'fortune_theme_options[blog_desc]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['blog_desc'],
            'sanitize_callback' => 'fortune_sanitize_text',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->selective_refresh->add_partial('fortune_theme_options[blog_desc]', array(
        'selector'            => '#blog-desc',
        'container_inclusive' => true,
    ));
    $wp_customize->add_control('blog_desc', array(
        'label'    => __('Home Blog Description', 'fortune'),
        'section'  => 'blog_section',
        'settings' => 'fortune_theme_options[blog_desc]',
        'type'     => 'text',
    )
    );
    /* Callout Optionds */
    $wp_customize->add_section('callout_section', array(
        'title'      => __("Callout Options", "fortune"),
        'panel'      => 'fortune_theme_option',
        'capability' => 'edit_theme_options',
        'priority'   => 35,
    ));
    $wp_customize->add_setting(
        'fortune_theme_options[callout_home]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['callout_home'],
            'sanitize_callback' => 'fortune_sanitize_checkbox',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control('callout_home', array(
        'label'    => __('Enable Callout Section', 'fortune'),
        'section'  => 'callout_section',
        'settings' => 'fortune_theme_options[callout_home]',
        'type'     => 'checkbox',
    )
    );

    $wp_customize->add_setting(
        'fortune_theme_options[callout_title]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['callout_title'],
            'sanitize_callback' => 'fortune_sanitize_text',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->selective_refresh->add_partial('fortune_theme_options[callout_title]', array(
        'selector'            => '#callout-title',
        'container_inclusive' => true,
    ));
    $wp_customize->add_control('callout_title', array(
        'label'    => __('Callout Title', 'fortune'),
        'section'  => 'callout_section',
        'settings' => 'fortune_theme_options[callout_title]',
        'type'     => 'text',
    )
    );

    $wp_customize->add_setting(
        'fortune_theme_options[callout_btn_text]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['callout_btn_text'],
            'sanitize_callback' => 'fortune_sanitize_text',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->selective_refresh->add_partial('fortune_theme_options[callout_btn_text]', array(
        'selector'            => '#callout_btn_link',
        'container_inclusive' => true,
    ));
    $wp_customize->add_control('callout_btn_text', array(
        'label'    => __('Callout Button Text', 'fortune'),
        'section'  => 'callout_section',
        'settings' => 'fortune_theme_options[callout_btn_text]',
        'type'     => 'text',
    )
    );

    $wp_customize->add_setting(
        'fortune_theme_options[callout_btn_link]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['callout_btn_link'],
            'sanitize_callback' => 'esc_url_raw',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control('callout_btn_link', array(
        'label'    => __('Callout Button Link', 'fortune'),
        'section'  => 'callout_section',
        'settings' => 'fortune_theme_options[callout_btn_link]',
        'type'     => 'text',
    )
    );
    /* contact options */
    $wp_customize->add_section('contact_section', array(
        'title'      => __("Contact Options", "fortune"),
        'panel'      => 'fortune_theme_option',
        'capability' => 'edit_theme_options',
        'priority'   => 35,
    ));

    $wp_customize->add_setting(
        'fortune_theme_options[contact_in_header]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['contact_in_header'],
            'sanitize_callback' => 'fortune_sanitize_checkbox',
            'capability'        => 'edit_theme_options',
        )
    );
    $wp_customize->add_control('contact_in_header', array(
        'label'    => __('Show Contact Info in Top bar', 'fortune'),
        'section'  => 'contact_section',
        'settings' => 'fortune_theme_options[contact_in_header]',
        'type'     => 'checkbox',
    )
    );

    $wp_customize->add_setting(
        'fortune_theme_options[contact_email]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['contact_email'],
            'sanitize_callback' => 'sanitize_email',
            'capability'        => 'edit_theme_options',
        )
    );
    $wp_customize->selective_refresh->add_partial('fortune_theme_options[contact_email]', array(
        'selector'            => '.header-top-right .register',
        'container_inclusive' => true,
    ));
    $wp_customize->add_control('contact_email', array(
        'label'    => __('Contact Email', 'fortune'),
        'section'  => 'contact_section',
        'settings' => 'fortune_theme_options[contact_email]',
        'type'     => 'text',
    )
    );
    $wp_customize->add_setting(
        'fortune_theme_options[contact_phone]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['contact_phone'],
            'sanitize_callback' => 'esc_attr',
            'capability'        => 'edit_theme_options',
        )
    );
    $wp_customize->selective_refresh->add_partial('fortune_theme_options[contact_phone]', array(
        'selector'            => '.header-top-right .login',
        'container_inclusive' => true,
    ));
    $wp_customize->add_control('contact_phone', array(
        'label'    => __('Contact Email', 'fortune'),
        'section'  => 'contact_section',
        'settings' => 'fortune_theme_options[contact_phone]',
        'type'     => 'text',
    )
    );
    /* Social Optionds */
    $wp_customize->add_section('social_section', array(
        'title'      => __("Social Options", "fortune"),
        'panel'      => 'fortune_theme_option',
        'capability' => 'edit_theme_options',
        'priority'   => 35,
    ));
    $wp_customize->add_setting(
        'fortune_theme_options[social_home]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['social_home'],
            'sanitize_callback' => 'fortune_sanitize_checkbox',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control('social_home', array(
        'label'    => __('Enable Social Media Option in Home', 'fortune'),
        'section'  => 'social_section',
        'settings' => 'fortune_theme_options[social_home]',
        'type'     => 'checkbox',
    )
    );

    $wp_customize->add_setting(
        'fortune_theme_options[social_footer]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['social_footer'],
            'sanitize_callback' => 'fortune_sanitize_checkbox',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control('social_footer', array(
        'label'    => __('Enable Social Media in Footer', 'fortune'),
        'section'  => 'social_section',
        'settings' => 'fortune_theme_options[social_footer]',
        'type'     => 'checkbox',
    )
    );

    /* Footer Option */
    $wp_customize->add_section('footer_section', array(
        'title'      => __("Footer Options", "fortune"),
        'panel'      => 'fortune_theme_option',
        'capability' => 'edit_theme_options',
        'priority'   => 35,
    ));
    $wp_customize->add_setting(
        'fortune_theme_options[footer_copyright]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['footer_copyright'],
            'sanitize_callback' => 'fortune_sanitize_text',
            'capability'        => 'edit_theme_options',
        )
    );
    $wp_customize->selective_refresh->add_partial('fortune_theme_options[footer_copyright]', array(
        'selector'            => '#f-copyright',
        'container_inclusive' => true,
    ));
    $wp_customize->add_control('footer_copyright', array(
        'label'    => __('Copyright Text', 'fortune'),
        'section'  => 'footer_section',
        'settings' => 'fortune_theme_options[footer_copyright]',
        'type'     => 'text',
    )
    );
    $wp_customize->add_setting(
        'fortune_theme_options[developed_by_text]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['developed_by_text'],
            'sanitize_callback' => 'fortune_sanitize_text',
            'capability'        => 'edit_theme_options',
        )
    );
    $wp_customize->add_control('developed_by_text', array(
        'label'    => __('Developed by Text', 'fortune'),
        'section'  => 'footer_section',
        'settings' => 'fortune_theme_options[developed_by_text]',
        'type'     => 'text',
    )
    );

    $wp_customize->add_setting(
        'fortune_theme_options[developed_by_link_text]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['developed_by_link_text'],
            'sanitize_callback' => 'fortune_sanitize_text',
            'capability'        => 'edit_theme_options',
        )
    );
    $wp_customize->add_control('developed_by_link_text', array(
        'label'    => __('Link Text', 'fortune'),
        'section'  => 'footer_section',
        'settings' => 'fortune_theme_options[developed_by_link_text]',
        'type'     => 'text',
    )
    );
    $wp_customize->add_setting(
        'fortune_theme_options[developed_by_link]',
        array(
            'type'              => 'option',
            'default'           => $fortune_theme_options['developed_by_link'],
            'sanitize_callback' => 'esc_url_raw',
            'capability'        => 'edit_theme_options',
        )
    );
    $wp_customize->add_control('developed_by_link', array(
        'label'    => __('Developed by Link', 'fortune'),
        'section'  => 'footer_section',
        'settings' => 'fortune_theme_options[developed_by_link]',
        'type'     => 'text',
    )
    );
    $wp_customize->add_section('fortune_pro', array(
        'title'    => __('Upgrade to Fortune Premium', 'fortune'),
        'priority' => 999,
    ));

    $wp_customize->add_setting('fortune_pro', array(
        'default'           => null,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control(new Fortune_Pro_Control($wp_customize, 'fortune_pro', array(
        'label'    => __('Fortune Premium', 'fortune'),
        'section'  => 'fortune_pro',
        'settings' => 'fortune_pro',
        'priority' => 1,
    )));
}
if (class_exists('WP_Customize_Control') && !class_exists('Fortune_Customize_Heading')):
    class Fortune_Customize_Heading extends WP_Customize_Control
{
        public $type = 'heading';

        public function render_content()
    {
            if (!empty($this->label)): ?>
                <h3 class="fortune-accordion-section-title"><?php echo esc_html($this->label); ?></h3>
            <?php endif;

        if ($this->description) {?>
            <span class="description customize-control-description">
            <?php echo wp_kses_post($this->description); ?>
            </span>
        <?php }
    }
}
endif;
/* Custom Sanitization Function  */
function fortune_sanitize_text($input)
{
    return wp_kses_post(force_balance_tags($input));
}

function fortune_sanitize_checkbox($checked)
{
    return ((isset($checked) && (true == $checked || 'on' == $checked)) ? true : false);
}

/**
 * Sanitize number options
 */
function fortune_sanitize_number($value)
{
    if (is_array($value)) {
        foreach ($value as $key => $val) {
            $v[$key] = is_numeric($val) ? $val : intval($val);
        }
        return $v;
    } else {
        return (is_numeric($value)) ? $value : intval($value);
    }
}
function fortune_sanitize_selected($value)
{
    if ($value[0] == '') {
        return $value = '';
    } else {
        return wp_kses_post($value);
    }
}
function fortune_sanitize_color($color)
{

    if ($color == "transparent") {
        return $color;
    }

    $named = json_decode('{"transparent":"transparent", "aliceblue":"#f0f8ff","antiquewhite":"#faebd7","aqua":"#00ffff","aquamarine":"#7fffd4","azure":"#f0ffff", "beige":"#f5f5dc","bisque":"#ffe4c4","black":"#000000","blanchedalmond":"#ffebcd","blue":"#0000ff","blueviolet":"#8a2be2","brown":"#a52a2a","burlywood":"#deb887", "cadetblue":"#5f9ea0","chartreuse":"#7fff00","chocolate":"#d2691e","coral":"#ff7f50","cornflowerblue":"#6495ed","cornsilk":"#fff8dc","crimson":"#dc143c","cyan":"#00ffff", "darkblue":"#00008b","darkcyan":"#008b8b","darkgoldenrod":"#b8860b","darkgray":"#a9a9a9","darkgreen":"#006400","darkkhaki":"#bdb76b","darkmagenta":"#8b008b","darkolivegreen":"#556b2f", "darkorange":"#ff8c00","darkorchid":"#9932cc","darkred":"#8b0000","darksalmon":"#e9967a","darkseagreen":"#8fbc8f","darkslateblue":"#483d8b","darkslategray":"#2f4f4f","darkturquoise":"#00ced1", "darkviolet":"#9400d3","deeppink":"#ff1493","deepskyblue":"#00bfff","dimgray":"#696969","dodgerblue":"#1e90ff", "firebrick":"#b22222","floralwhite":"#fffaf0","forestgreen":"#228b22","fuchsia":"#ff00ff", "gainsboro":"#dcdcdc","ghostwhite":"#f8f8ff","gold":"#ffd700","goldenrod":"#daa520","gray":"#808080","green":"#008000","greenyellow":"#adff2f", "honeydew":"#f0fff0","hotpink":"#ff69b4", "indianred ":"#cd5c5c","indigo ":"#4b0082","ivory":"#fffff0","khaki":"#f0e68c", "lavender":"#e6e6fa","lavenderblush":"#fff0f5","lawngreen":"#7cfc00","lemonchiffon":"#fffacd","lightblue":"#add8e6","lightcoral":"#f08080","lightcyan":"#e0ffff","lightgoldenrodyellow":"#fafad2", "lightgrey":"#d3d3d3","lightgreen":"#90ee90","lightpink":"#ffb6c1","lightsalmon":"#ffa07a","lightseagreen":"#20b2aa","lightskyblue":"#87cefa","lightslategray":"#778899","lightsteelblue":"#b0c4de", "lightyellow":"#ffffe0","lime":"#00ff00","limegreen":"#32cd32","linen":"#faf0e6", "magenta":"#ff00ff","maroon":"#800000","mediumaquamarine":"#66cdaa","mediumblue":"#0000cd","mediumorchid":"#ba55d3","mediumpurple":"#9370d8","mediumseagreen":"#3cb371","mediumslateblue":"#7b68ee", "mediumspringgreen":"#00fa9a","mediumturquoise":"#48d1cc","mediumvioletred":"#c71585","midnightblue":"#191970","mintcream":"#f5fffa","mistyrose":"#ffe4e1","moccasin":"#ffe4b5", "navajowhite":"#ffdead","navy":"#000080", "oldlace":"#fdf5e6","olive":"#808000","olivedrab":"#6b8e23","orange":"#ffa500","orangered":"#ff4500","orchid":"#da70d6", "palegoldenrod":"#eee8aa","palegreen":"#98fb98","paleturquoise":"#afeeee","palevioletred":"#d87093","papayawhip":"#ffefd5","peachpuff":"#ffdab9","peru":"#cd853f","pink":"#ffc0cb","plum":"#dda0dd","powderblue":"#b0e0e6","purple":"#800080", "red":"#ff0000","rosybrown":"#bc8f8f","royalblue":"#4169e1", "saddlebrown":"#8b4513","salmon":"#fa8072","sandybrown":"#f4a460","seagreen":"#2e8b57","seashell":"#fff5ee","sienna":"#a0522d","silver":"#c0c0c0","skyblue":"#87ceeb","slateblue":"#6a5acd","slategray":"#708090","snow":"#fffafa","springgreen":"#00ff7f","steelblue":"#4682b4", "tan":"#d2b48c","teal":"#008080","thistle":"#d8bfd8","tomato":"#ff6347","turquoise":"#40e0d0", "violet":"#ee82ee", "wheat":"#f5deb3","white":"#ffffff","whitesmoke":"#f5f5f5", "yellow":"#ffff00","yellowgreen":"#9acd32"}', true);

    if (isset($named[strtolower($color)])) {
        /* A color name was entered instead of a Hex Value, convert and send back */
        return $named[strtolower($color)];
    }

    $color = str_replace('#', '', $color);
    if (strlen($color) == 3) {
        $color = $color . $color;
    }
    if (preg_match('/^[a-f0-9]{6}$/i', $color)) {
        return '#' . $color;
    }
    //$this->error = $this->field;
    return false;
}

function fortune_sanitize_textarea($value)
{
    return wp_kses_post(force_balance_tags($value));
}
function fortune_customizer_preview_js()
{
    wp_enqueue_script('custom_css_preview', get_template_directory_uri() . '/vendor/customize-preview.js', array('customize-preview', 'jquery'));
    wp_add_inline_style('customize-preview', 'ul.ei-slider-large .customize-partial-edit-shortcut button {
    left: 0px;
    top: 26px;
}');
}
add_action('customize_preview_init', 'fortune_customizer_preview_js');
add_action('customize_controls_enqueue_scripts', 'fortune_customize_controls_scripts');
function fortune_customize_controls_scripts()
{
    wp_enqueue_style('fontawesome', get_template_directory_uri() . '/css/fonts/font-awesome/css/font-awesome.css');
    wp_enqueue_style('simple-icon-picker', get_template_directory_uri() . '/css/simple-iconpicker.css');
    wp_enqueue_script('simple-icon-picker-js', get_template_directory_uri() . '/vendor/simple-iconpicker.js');
    $actions       = fortune_get_actions_required();
    $number_action = $actions['number_notice'];
    wp_localize_script('simple-icon-picker-js', 'fortune_customizer_settings', array(
        'number_action' => $number_action,
        'action_url'    => admin_url('themes.php?page=ft_fortune&tab=actions_required'),
    ));
}
if (class_exists('WP_Customize_Control') && !class_exists('Fortune_Pro_Control')):
    class Fortune_Pro_Control extends WP_Customize_Control
{

        /**
         * Render the content on the theme customizer page
         */
        public function render_content()
    {
            switch ($this->type) {
                case 'iconpicker': ?>
                        <label>
                        <span class="customize-control-title">
                        <?php echo esc_attr($this->label); ?>
                    </span>

                        <?php if (!empty($this->description)): ?>
                            <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
                        <?php endif;?>

                    <input id="input_<?php echo esc_attr($this->id); ?>" class="iconpicker" type="text"
                           value="<?php echo esc_attr($this->value()); ?>" <?php echo $this->link(); ?>>
                    </label><?php
break;
            default: ?>
                    <label style="overflow: hidden; zoom: 1;">
                        <div class="col-md-2 col-sm-6 upsell-btn">
                            <a style="margin-bottom:20px;margin-left:20px;"
                               href="http://www.webhuntinfotech.com/webhunt_theme/fortune-premium-39/" target="blank"
                               class="btn btn-success btn"><?php _e('Upgrade to Fortune Premium', 'fortune');?> </a>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <img class="fortune_img_responsive "
                                 src="<?php echo get_template_directory_uri() . '/images/fortune.png' ?>">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <h3 style="margin-top:10px;margin-left: 20px;text-decoration:underline;color:#333;"><?php echo _e('Fortune Premium - Features', 'fortune'); ?></h3>
                            <ul style="padding-top:10px">
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('Responsive Design', 'fortune');?>
                                </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('Rich Shortcodes', 'fortune');?> </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('More than 15 Templates', 'fortune');?>
                                </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('Custom Widgets', 'fortune');?> </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('Pricing Tables', 'fortune');?> </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('Redux Options Panel', 'fortune');?>
                                </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('2 types of Service Section', 'fortune');?>
                                </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('3 Different Types of Blog Templates', 'fortune');?>
                                </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('4 Types of Portfolio Templates', 'fortune');?>
                                </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('Unlimited Colors Scheme', 'fortune');?>
                                </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('Patterns Background', 'fortune');?>
                                </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('WPML Compatible', 'fortune');?>
                                </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('Woo-commerce Compatible', 'fortune');?>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('Portfolio layout with Isotope effect', 'fortune');?>
                                </li>
                                <li class="upsell-fortune">
                                    <div class="dashicons dashicons-yes"></div> <?php _e('Translation Ready', 'fortune');?>
                                </li>

                            </ul>
                        </div>
                        <div class="col-md-2 col-sm-6 upsell-btn upsell-btn-bottom">
                            <a style="margin-bottom:20px;margin-left:20px;"
                               href="http://www.webhuntinfotech.com/webhunt_theme/fortune-premium-39/" target="blank"
                               class="btn btn-success btn"><?php _e('Upgrade to Fortune Premium', 'fortune');?> </a>
                        </div>

                        <p>
                            <?php
printf(__('If you Like our Products , Please Rate us 5 star on %sWordPress.org%s.  We\'d really appreciate it! </br></br>  Thank You', 'fortune'), '<a target="" href="https://wordpress.org/support/view/theme-reviews/fortune?filter=5">', '</a>');
                ?>
                        </p>
                    </label>
                    <?php
break;
        }
    }
}
endif;
?>
