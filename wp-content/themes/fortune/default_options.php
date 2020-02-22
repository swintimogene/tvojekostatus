<?php
/* General Options */
function fortune_theme_options()
{
    $fortune_theme_options = array(
        'site_layout' => '',
        'headercolorscheme' => 'light_header',
        'headersticky' => 1,
        'custom_css' => '',
		'home_service_enabled' => 1,
        'service_type'=>2,
        'show_top_bar'=>1,
		'color_scheme'=>'',
		'navigation_style'=>'header-default',
		'service_heading'=>__('Our Features','fortune'),
        'service_title_1' => __("Responsive", 'fortune'),
        'service_icon_1' => "fa fa-mobile",
        'service_text_1' => __("Lorem ipsum dolor sit amet, consectetur adipisicing elit ipsum lorem sit amet.", 'fortune'),
        'service_link_1' => "#",
		'service_target_1' =>'' ,

        'service_title_2' => __("Highly Customizable", 'fortune'),
        'service_icon_2' => "fa fa-wrench",
        'service_text_2' => __("Lorem ipsum dolor sit amet, consectetur adipisicing elit ipsum lorem sit amet.", 'fortune'),
        'service_link_2' => "#",
		'service_target_2'=>'',

        'service_title_3' => __("WooCommerce Support", 'fortune'),
        'service_icon_3' => "fa fa-shopping-cart",
        'service_text_3' => __("Lorem ipsum dolor sit amet, consectetur adipisicing elit ipsum lorem sit amet", 'fortune'),
        'service_link_3' => "#",
		'service_target_3'=>'',
		'slider_content_anim_speed'=>5200,
        'slider_anim_speed'=>800,
		//Slider Settings:
        'slider_home' => 1,
		'slider_plugin_code'=>'',
		'slider_category'=>'',
        'slider_interval'=>4000,
        'slider_easing_effect'=>'easeOutExpo',
        'slider_auto_play'=>1,
        //Portfolio Settings:
        'portfolio_home' => 0,
        'portfolio_post' => "",
        'footer_copyright' => __('Fortune Theme', 'fortune'),
        'developed_by_text' => __('Developed By', 'fortune'),
        'developed_by_link_text' => __('Webhunt Infotech', 'fortune'),
        'developed_by_link' => 'http://www.webhuntinfotech.com/',
        'footer_layout' => 4,
        'show_footer_widget'=>1,
		'blog_home'=>1,
        'blog_title' => __('Recent Posts', 'fortune'),
		'blog_desc' => __('There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour of this randomised words which don\'t look even slightly believable', 'fortune'),
		'home_post_cat' => '',
        /* footer callout */
        'callout_home' => 1,
        'callout_title' => __('Best Wordpress Resposnive Theme Ever!', 'fortune'),
        'callout_btn_text' => __('Download Now', 'fortune'),
        'callout_btn_link' => 'http://www.example.com',
        'callout_bg_color'=>'#dc2a0b',
        /* Social media icons */
        'contact_info_header' => 1,
        'social_footer' => 1,
		'contact_in_header'=>1,
        'contact_phone' => '+09101-9999',
        'contact_email' => 'example@gmail.com',
		'social_home'=>0,
        'social_skype_link' => '#',
		'home_sections'=>array('service', 'portfolio','extra', 'blog', 'callout')

    );
    return wp_parse_args(get_option('fortune_theme_options', array()), $fortune_theme_options);
}

?>