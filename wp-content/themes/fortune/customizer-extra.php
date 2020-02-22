<?php
/* Add Customizer Panel */
$fortune_theme_options = fortune_theme_options();
Kirki::add_config('fortune_theme', array(
    'capability'  => 'edit_theme_options',
    'option_type' => 'option',
    'option_name' => 'fortune_theme_options',
));

Kirki::add_field('fortune_theme', array(
    'settings'          => 'header_topbar_bg_color',
    'label'             => __('Header Top Bar Background Color', 'fortune'),
    'description'       => __('Change Top bar Background Color', 'fortune'),
    'section'           => 'colors',
    'type'              => 'color',
    'priority'          => 9,
    'default'           => '#2f2f2f',
    'sanitize_callback' => 'fortune_sanitize_color',
    'output'            => array(
        array(
            'element'  => '.header-top',
            'property' => 'background',
        ),
    ),

));

Kirki::add_field('fortune_theme', array(
    'settings'          => 'header_topbar_color',
    'label'             => __('Header Top Bar Color', 'fortune'),
    'description'       => __('Change Top bar Font Color', 'fortune'),
    'section'           => 'colors',
    'type'              => 'color',
    'priority'          => 9,
    'default'           => '#a3a3a3',
    'sanitize_callback' => 'fortune_sanitize_color',
    'output'            => array(
        array(
            'element'  => '.header-top, .header-top ul li a, .header-top-right a',
            'property' => 'color',
        ),
    ),
));

Kirki::add_field('fortune_theme', array(
    'settings'          => 'header_background_color',
    'label'             => __('Header Background Color', 'fortune'),
    'description'       => __('Change Header Background Color', 'fortune'),
    'section'           => 'colors',
    'type'              => 'color',
    'priority'          => 9,
    'default'           => '#ececec',
    'sanitize_callback' => 'fortune_sanitize_color',
    'output'            => array(
        array(
            'element'  => '.header-main',
            'property' => 'background',
        ),
    ),
));
Kirki::add_field( 'fortune_theme', array(
    'type'        => 'spacing',
    'settings'    => 'slider_content_spacing',
    'label'       => __( 'Slider Content Margin Right', 'fortune' ),
    'section'     => 'slider_sec',
    'default'     => array(
        'right'  => '0%',
    ),
    'output'=>array(
        array(
            'element'  => '.ei-title',
            )),
    'priority'    => 10,
) );
/* Slider content animation effect */
Kirki::add_field( 'fortune_theme', array(
    'type'        => 'select',
    'settings'    => 'slider_easing_effect',
    'label'       => __( 'Slider Animation Effects', 'fortune' ),
    'description'=>sprintf('<a href="http://easings.net/">%s</a>',__('Click here to see how each easing effects works.','fortune')),
    'section'     => 'slider_sec',
    'default'     => 'easeOutExpo',
    'choices'=>array(
        "easeInSine"=>"easeInSine",
         "easeOutSine"=>"easeOutSine",
         "easeInOutSine"=>"easeInOutSine",
         "easeInQuad"=>"easeInQuad",
         "easeOutQuad"=>"easeOutQuad",
         "easeInOutQuad"=>"easeInOutQuad",
         "easeInCubic"=>"easeInCubic",
         "easeOutCubic"=>"easeOutCubic",
         "easeInOutCubic"=>"easeInOutCubic",
         "easeInQuart"=>"easeInQuart",
         "easeOutQuart"=>"easeOutQuart",
         "easeInOutQuart"=>"easeInOutQuart",
         "easeInQuint"=>"easeInQuint",
         "easeOutQuint"=>"easeOutQuint",
         "easeInOutQuint"=>"easeInOutQuint",
         "easeInExpo"=>"easeInExpo",
         "easeOutExpo"=>"easeOutExpo",
         "easeInOutExpo"=>"easeInOutExpo",
         "easeInCirc"=>"easeInCirc",
         "easeOutCirc"=>"easeOutCirc",
         "easeInOutCirc"=>"easeInOutCirc",
         "easeInBack"=>"easeInBack",
         "easeOutBack"=>"easeOutBack",
         "easeInOutBack"=>"easeInOutBack",
         "easeInElastic"=>"easeInElastic",
         "easeOutElastic"=>"easeOutElastic",
         "easeInOutElastic"=>"easeInOutElastic",
         "easeInBounce"=>"easeInBounce",
         "easeOutBounce"=>"easeOutBounce",
         "easeInOutBounce"=>"easeInOutBounce"
    ),
    'priority'    => 10,
) );
Kirki::add_field( 'fortune_theme', array(
    'type'        => 'number',
    'settings'    => 'slider_content_anim_speed',
    'label'       => esc_attr__( 'Slider Content Animation Speed (in miliseconds)', 'fortune' ),
    'section'     => 'slider_sec',
    'default'     => 1200,
    'choices'     => array(
        'min'  => 100,
        'max'  => 10000,
        'step' => 100,
    ),
) );

Kirki::add_field( 'fortune_theme', array(
    'type'        => 'number',
    'settings'    => 'slider_anim_speed',
    'label'       => esc_attr__( 'Slider Animation Speed (in miliseconds)', 'fortune' ),
    'section'     => 'slider_sec',
    'default'     => 800,
    'choices'     => array(
        'min'  => 100,
        'max'  => 10000,
        'step' => 100,
    ),
) );

Kirki::add_field( 'fortune_theme', array(
    'type'        => 'number',
    'settings'    => 'slider_interval',
    'label'       => esc_attr__( 'Slider Interval (in miliseconds)', 'fortune' ),
    'section'     => 'slider_sec',
    'default'     => 4000,
    'choices'     => array(
        'min'  => 1000,
        'max'  => 10000,
        'step' => 500,
    ),
) );

Kirki::add_field( 'fortune_theme', array(
    'type'        => 'custom',
    'settings'    => 'slider_typo',
    'label'       => 'Slider Typography',
    'section'     => 'slider_sec',
    'default'     => '<div style="padding: 30px;background-color: #333; color: #fff; border-radius: 50px;">' . esc_html__( 'Goto Typography section to customize Slider typography', 'fortune' ) . '</div>',
    'priority'    => 10,
) );

Kirki::add_section('general_sec', array(
    'title'       => __('General Options', 'fortune'),
    'description' => __('Here you can change basic settings of your site', 'fortune'),
    'panel'       => 'fortune_theme_option',
    'priority'    => 10,
    'capability'  => 'edit_theme_options',
));

Kirki::add_field('fortune_theme', array(
    'type'              => 'toggle',
    'settings'          => 'headersticky',
    'label'             => __('Fixed Header', 'fortune'),
    'description'       => __('Switch between fixed and static header', 'fortune'),
    'section'           => 'general_sec',
    'default'           => $fortune_theme_options['headersticky'],
    'priority'          => 10,
    'sanitize_callback' => 'fortune_sanitize_checkbox',
));

Kirki::add_field('fortune_theme', array(
    'type'              => 'toggle',
    'settings'          => 'show_top_bar',
    'label'             => __('Show/Hide Topbar', 'fortune'),
    'section'           => 'general_sec',
    'default'           => $fortune_theme_options['show_top_bar'],
    'priority'          => 10,
    'sanitize_callback' => 'fortune_sanitize_checkbox',
));
Kirki::add_field( 'fortune_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'navigation_style',
    'label'       => __('Navigation Style','fortune'),
    'section'     => 'general_sec',
    'default'     => 'header-default',
    'priority'    => 10,
	'choices'     => array(
		'header-default'   => esc_attr__( 'Default', 'fortune' ),
		'menu-pills' => esc_attr__( 'Pills', 'fortune' ),
		'menu-colored'  => esc_attr__( 'Colored', 'fortune' ),
	),
) );
Kirki::add_field('fortune_theme', array(
    'type'              => 'toggle',
    'settings'          => 'show_top_bar',
    'label'             => __('Show Topbar', 'fortune'),
    'section'           => 'general_sec',
    'default'           => $fortune_theme_options['show_top_bar'],
    'priority'          => 10,
    'sanitize_callback' => 'fortune_sanitize_checkbox',
));
Kirki::add_field('fortune_theme', array(
    'type'              => 'custom',
    'settings'          => 'topbarstyle',
    'label'             => __('Topbar Color Styling', 'fortune'),
    'section'           => 'general_sec',
    'default'           => '<a href="'.admin_url( '/customize.php?autofocus[section]=colors' ).'">' . esc_html__( 'Change Top Bar Color', 'fortune' ) . '</a>',
    'priority'          => 10,
    'sanitize_callback' => 'esc_attr',
));
Kirki::add_field('fortune_theme', array(
    'settings'          => 'site_layout',
    'label'             => __('Site Layout', 'fortune'),
    'description'       => __('Change your site layout to full width or boxed size.', 'fortune'),
    'section'           => 'general_sec',
    'type'              => 'radio-image',
    'priority'          => 10,
    'transport'         => 'postMessage',
    'default'           => '',
    'sanitize_callback' => 'fortune_sanitize_text',
    'choices'           => array(
        ''           => get_template_directory_uri() . '/images/1c.png',
        'boxed' => get_template_directory_uri() . '/images/3cm.png',
    ),

));

Kirki::add_field('fortune_theme', array(
    'type'              => 'color',
    'settings'          => 'callout_bg_color',
    'label'             => __('Background Color', 'fortune'),
    'section'           => 'callout_section',
    'default'           => '',
    'priority'          => 10,
    'sanitize_callback' => 'fortune_sanitize_color',
	'output'      => array(
        array(
            'element' => '.section.primary',
			'property'=>'background',
        ),
    ),
	
));

/* Typography */
Kirki::add_section('typography_sec', array(
    'title'       => __('Typography Section', 'fortune'),
    'description' => __('Here you can change Font Style of your site', 'fortune'),
    'panel'       => 'fortune_theme_option',
    'priority'    => 160,
    'capability'  => 'edit_theme_options',
));

Kirki::add_field('fortune_theme', array(
    'type'        => 'typography',
    'settings'    => 'logo_font',
    'label'       => __('Logo Font Style', 'fortune'),
    'description' => __('Change logo font family and font style.', 'fortune'),
    'section'     => 'typography_sec',
    'default'     => array(
        'font-style'  => array('bold', 'italic'),
        'font-family' => 'Anton',
		'variant'        => 'regular',
		'font-size'      => '36px',
		'line-height'    => '1em',
		'letter-spacing' => '0',
		'subsets'        => array( 'sans-serif' ),
		'color'          => '#2f2f2f',
		'text-transform' => 'uppercase',

    ),
    'priority'    => 10,
    'output'      => array(
        array(
            'element' => '.header .logo h1, .header .logo h2',
        ),
    ),
));

Kirki::add_field('fortune_theme', array(
    'type'        => 'typography',
    'settings'    => 'logo_tag_font',
    'label'       => __('Logo Tag line Style', 'fortune'),
    'description' => __('Change logo tag ine font family and font style.', 'fortune'),
    'section'     => 'typography_sec',
    'default'     => array(
        'font-style'  => array('bold', 'italic'),
        'font-family' => 'Muli',
		'font-size'      => '11px',
		'line-height'    => '1.5em',
		'subsets'        => array( 'sans-serif' ),
		'color'          => '#a3a3a3',
		'text-transform' => 'uppercase',

    ),
    'priority'    => 10,
    'output'      => array(
        array(
            'element' => '.header .logo .tagline',
        ),
    ),
));

Kirki::add_field('fortune_theme', array(
    'type'        => 'typography',
    'settings'    => 'prime_menu_font',
    'label'       => __('primary menu Style', 'fortune'),
    'section'     => 'typography_sec',
    'default'     => array(
        'font-style'  => array('bold', 'italic'),
        'font-family' => 'Oswald',
		'font-size'      => '16px',
		'line-height'    => '96px',
		'subsets'        => array( 'sans-serif' ),
		'text-transform' => 'uppercase',

    ),
    'priority'    => 10,
    'output'      => array(
        array(
            'element' => '.fhmm .navbar-collapse .navbar-nav > li > a',
        ),
    ),
));
/* Slider title Typography */
Kirki::add_field('fortune_theme', array(
    'type'        => 'typography',
    'settings'    => 'slider_title_font',
    'label'       => __('Slider Title Style', 'fortune'),
    'description' => __('Change Slider Title font family and font style.', 'fortune'),
    'section'     => 'typography_sec',
    'default'     => array(
        'font-style'  => array('bold', 'italic'),
        'font-family' => "Open Sans",

    ),
    'default'     => array(
        'font-style'  => array('bold', 'italic'),
        'font-family' => 'Playfair Display',
        'font-size'      => '40px',
        'line-height'    => '50px',
        'subsets'        => array( 'serif' ),
        'text-transform' => 'uppercase',
        'color'          => '#dc2a0b',

    ),
    'priority'    => 10,
    'output'      => array(
        array(
            'element' => '.ei-title h2',
        ),
    ),
));

/* Slider subtitle Typography */
Kirki::add_field('fortune_theme', array(
    'type'        => 'typography',
    'settings'    => 'slider_subtitle_font',
    'label'       => __('Slider Subtitle Font Style', 'fortune'),
    'description' => __('Change Sldier subtitle font family and font style.', 'fortune'),
    'section'     => 'typography_sec',
    'default'     => array(
        'font-family' => 'Open Sans Condensed',
        'font-size'      => '70px',
        'line-height'    => '70px',
        'subsets'        => array( 'sans-serif' ),
        'text-transform' => 'uppercase',
        'color'=>'#000',

    ),
    'priority'    => 10,
    'choices'     => array(
        'font-style'  => true,
        'font-family' => true,
    ),
    'output'      => array(
        array(
            'element' => '.ei-title h3',
        ),
    ),
));

/* Full body typography */
Kirki::add_field('fortune_theme', array(
    'type'        => 'typography',
    'settings'    => 'site_font',
    'label'       => __('Site Font Style', 'fortune'),
    'description' => __('Change whole site font family and font style.', 'fortune'),
    'section'     => 'typography_sec',
    'default'     => array(
        'font-style'  => array('bold', 'italic'),
        'font-family' => "Open Sans",

    ),
    'priority'    => 10,
    'choices'     => array(
        'font-style'  => true,
        'font-family' => true,
    ),
    'output'      => array(
        array(
            'element' => 'body, h1, h2, h3, h4, h5, h6, p, em, blockquote, .main_title h2',
        ),
    ),
));

/* Home Page Customizer */
Kirki::add_section('home_customize_section', array(
    'title'      => __('Home Page Reorder Sections', 'fortune'),
    'panel'      => 'fortune_theme_option',
    'priority'   => 160,
    'capability' => 'edit_theme_options',
));
Kirki::add_field( 'fortune_theme', array(
	'type'        => 'sortable',
	'settings'    => 'home_sections',
	'label'       => __( 'Here You can reorder your homepage section', 'fortune' ),
	'section'     => 'home_customize_section',
	'default'     => array(
		'service',
		'portfolio',
        'extra',
		'blog',
		'callout'
	),
	'choices'     => array(
		'service' => esc_attr__( 'Service Section', 'fortune' ),
		'portfolio' => esc_attr__( 'Portfolio Section', 'fortune' ),
        'extra'=> esc_attr__( 'Extra Section', 'fortune' ),
		'blog' => esc_attr__( 'Blog Section', 'fortune' ),
		'callout' => esc_attr__( 'Callout Section', 'fortune' ),
	),
	'priority'    => 10,
) );
/* footer options */
Kirki::add_section('footer_section', array(
    'title'      => __('Footer Options', 'fortune'),
    'panel'      => 'fortune_theme_option',
    'priority'   => 160,
    'capability' => 'edit_theme_options',
));
Kirki::add_field('fortune_theme', array(
    'settings'          => 'footer_bg_color',
    'label'             => __('Footer-1 Background Color', 'fortune'),
    'section'           => 'footer_section',
    'type'              => 'color-alpha',
    'default'           => '#2f2f2f',
    'priority'          => 10,
    'output'            => array(
        array(
            'element'  => '.footer',
            'property' => 'background',
        ),
    ),
    'transport'         => 'auto',
    'sanitize_callback' => 'fortune_sanitize_color',
));

Kirki::add_field('fortune_theme', array(
    'settings'          => 'footer_2_bg_color',
    'label'             => __('Footer-2 Background Color', 'fortune'),
    'section'           => 'footer_section',
    'type'              => 'color-alpha',
    'default'           => '#212121',
    'priority'          => 10,
    'output'            => array(
        array(
            'element'  => '.footer-copyright',
            'property' => 'border-top',
        ),
		array(
            'element'  => '.footer-copyright',
            'property' => 'background',
        ),
    ),
    'transport'         => 'auto',
    'sanitize_callback' => 'fortune_sanitize_color',
)); 
Kirki::add_field('fortune_theme', array(
    'type'              => 'toggle',
    'settings'          => 'show_footer_widget',
    'label'             => __('Show/Hide Footer Widget Area', 'fortune'),
    'section'           => 'footer_section',
    'default'           => $fortune_theme_options['show_footer_widget'],
    'priority'          => 10,
    'sanitize_callback' => 'fortune_sanitize_checkbox',
));

Kirki::add_field('fortune_theme', array(
    'settings'          => 'footer_layout',
    'label'             => __('Footer Widget Layout', 'fortune'),
    'description'       => __('Change footer widget area into 2, 3 or 4 column', 'fortune'),
    'section'           => 'footer_section',
    'type'              => 'radio-image',
    'priority'          => 10,
    'default'           => $fortune_theme_options['footer_layout'],
    'transport'         => 'postMessage',
    'choices'           => array(
        2 => get_template_directory_uri() . '/images/footer-widgets-2.png',
        3 => get_template_directory_uri() . '/images/footer-widgets-3.png',
        4 => get_template_directory_uri() . '/images/footer-widgets-4.png',
    ),
    'sanitize_callback' => 'fortune_sanitize_number',
    'active_callback'    => array(
        array(
            'setting'  => 'show_footer_widget',
            'operator' => '==',
            'value'    => true,
        ),
    ),
));

?>