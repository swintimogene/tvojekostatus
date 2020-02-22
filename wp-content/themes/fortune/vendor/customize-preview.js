(function ($) {
    wp.customize( 'header_textcolor', function( value ) {
        value.bind( function( to ) {
            $( '#logo a h3, .light_header #navy > li > a,  .menu_button_mode:not(.header_on_side) #navy > li.current_page_item > a, .menu_button_mode:not(.header_on_side) #navy > li.current_page_item:hover > a' ).css( 'color', to );
        } );
    } );
	
    wp.customize('fortune_theme_options[logo_layout]', function (value) {
        value.bind(function (to) {
            $('#logo').css('float', to);
        });
    });
    wp.customize('fortune_theme_options[topbarcolor]', function (value) {
        value.bind(function (to) {
            if (to != '') {
                $('.topbar').addClass(to);
            } else {
                $('.topbar').removeClass('topbar_colored');
            }
        });
    });
    /* layout option */
    wp.customize('fortune_theme_options[headercolorscheme]', function (value) {
        value.bind(function (to) { alert(to);
            if (to != '') {
                $('body').addClass(to);
            } else {
                $('body').removeClass('light_header');
            }
        });
    });
    wp.customize('fortune_theme_options[site_layout]', function (value) {
        value.bind(function (to) {
            if (to!='') {
                $('body').addClass(to);
            } else {
                $('body').removeClass('boxed');
            }
        });
    });
    wp.customize('fortune_theme_options[footer_layout]', function (value) {
        value.bind(function (to) {
            var col = 12 / parseInt(to);
            $('.footer-widgets .container .row').children().attr('class', 'col-md-' + col);
        });
    });

    /* Service Options */
    wp.customize('fortune_theme_options[home_service_enabled]', function (value) {
        value.bind(function (to) {
			if(to==1){
				$('#service_sectoin').show();	
			}else{
				$('#service_sectoin').hide();
			}
        });
    });
wp.customize('fortune_theme_options[service_head]', function (value) {
        value.bind(function (to) {
            $('#service_head').attr(to);
        });
    });
    wp.customize('fortune_theme_options[service_icon_1]', function (value) {
        value.bind(function (to) {
            $('#service_icon_1').attr('class', to);
        });
    });
    wp.customize('fortune_theme_options[service_icon_2]', function (value) {
        value.bind(function (to) {
            $('#service_icon_2').attr('class', to);
        });
    });
    wp.customize('fortune_theme_options[service_icon_3]', function (value) {
        value.bind(function (to) {
            $('#service_icon_3').attr('class', to);
        });
    });

    wp.customize('fortune_theme_options[service_title_1]', function (value) {
        value.bind(function (to) {
            $('#service_title_1').html(to);
        });
    });
    wp.customize('fortune_theme_options[service_title_2]', function (value) {
        value.bind(function (to) {
            $('#service_title_2').html(to);
        });
    });
    wp.customize('fortune_theme_options[service_title_3]', function (value) {
        value.bind(function (to) {
            $('#service_title_3').html(to);
        });
    });
    wp.customize('fortune_theme_options[service_text_1]', function (value) {
        value.bind(function (to) {
            $('#service_text_1').html(to);
        });
    });
    wp.customize('fortune_theme_options[service_text_2]', function (value) {
        value.bind(function (to) {
            $('#service_text_2').html(to);
        });
    });
    wp.customize('fortune_theme_options[service_text_3]', function (value) {
        value.bind(function (to) {
            $('#service_text_3').html(to);
        });
    });
    wp.customize('fortune_theme_options[service_link_1]', function (value) {
        value.bind(function (to) {
			var anchor = '<a href="'+to+'" class="service_link" id="service_link_1"><h3 id="service_title_1">'+ $('#service_title_1').html() +'</h3></a>';
            $('#service_title_1').replaceWith(anchor);
			$('#service_link_1').replaceWith(anchor);
        });
    });
    wp.customize('fortune_theme_options[service_link_2]', function (value) {
        value.bind(function (to) {
           var anchor = '<a href="'+to+'" class="service_link" id="service_link_2"><h3 id="service_title_2">'+ $('#service_title_2').html() +'</h3></a>';
            $('#service_title_2').replaceWith(anchor);
			$('#service_link_2').replaceWith(anchor);
        });
    });
    wp.customize('fortune_theme_options[service_link_3]', function (value) {
        value.bind(function (to) {
            var anchor = '<a href="'+to+'" class="service_link" id="service_link_3"><h3 id="service_title_3">'+ $('#service_title_3').html() +'</h3></a>';
            $('#service_title_3').replaceWith(anchor);
			$('#service_link_3').replaceWith(anchor);
        });
    });
	
	wp.customize('fortune_theme_options[service_target_1]', function (value) {
        value.bind(function (to) {
			if(to==true){
				$('#service_link_1').attr('target','_blank');
			}else{
				$('#service_link_1').attr('target','');	
			}
        });
    });
	
	wp.customize('fortune_theme_options[service_target_2]', function (value) {
        value.bind(function (to) {
			if(to==true){
				$('#service_link_2').attr('target','_blank');
			}else{
				$('#service_link_2').attr('target','');	
			}
        });
    });
	
	wp.customize('fortune_theme_options[service_target_3]', function (value) {
        value.bind(function (to) {
			if(to==true){
				$('#service_link_3').attr('target','_blank');
			}else{
				$('#service_link_3').attr('target','');	
			}
        });
    });

    /* Portfolio Options */
    wp.customize('fortune_theme_options[port_heading]', function (value) {
        value.bind(function (to) {
            $('#port_head').html(to);
        });
    });

    /* Blog Title */
    wp.customize('fortune_theme_options[blog_title]', function (value) {
        value.bind(function (to) {
            $('h1#blog-heading').html(to);
        });
    });
	    wp.customize('fortune_theme_options[blog_desc]', function (value) {
        value.bind(function (to) {
            $('p#blog-desc').html(to);
        });
    });
    /* Footer Callout */
    wp.customize('fortune_theme_options[callout_home]', function (value) {
        value.bind(function (to) {
            if (!to)
                $('#callout').hide();
            else
                $('#callout').show();
        });
    });
    wp.customize('fortune_theme_options[callout_title]', function (value) {
        value.bind(function (to) {
            $('h2#callout-title').html(to);
        });
    });
   
    wp.customize('fortune_theme_options[callout_btn_link]', function (value) {
        value.bind(function (to) {
            $('a#callout_btn_link').attr('href', to);
        });
    });
    wp.customize('fortune_theme_options[callout_btn_text]', function (value) {
        value.bind(function (to) {
            $('a#callout_btn_link').html(to);
        });
    });
	/* Social Options */
    wp.customize('fortune_theme_options[social_home]', function (value) {
        value.bind(function (to) {
            if (!to)
                $('.social-links-section').hide();
            else
                $('.social-links-section').show();
        });
    });
	wp.customize('fortune_theme_options[social_footer]', function (value) {
        value.bind(function (to) {
            if (!to)
                $('#social_footer').hide();
            else
                $('#social_footer').show();
        });
    });

})(jQuery);