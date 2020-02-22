<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>
<?php $fortune_theme_options = fortune_theme_options();?>
<body <?php body_class($fortune_theme_options['site_layout']); ?>>
	<div class="site-wrapper">
		
		<!-- Header -->
		<header class="header <?php echo $fortune_theme_options['navigation_style']?>">

		<?php if($fortune_theme_options['show_top_bar']){ ?>
		<div class="header-top">
			<div class="container">
			<?php if ( has_nav_menu( 'secondary' ) ) : ?>
				<div class="header-top-left">
					<?php wp_nav_menu(array(
						'theme_location' => 'secondary',
						'container' => false,
						'menu_class' => 'header-top-nav',
                        'fallback_cb' => 'fortune_fallback_page_menu',
						)
					); ?>
				</div><?php endif;?>
				<?php
					if($fortune_theme_options['contact_in_header']){?>
					<div class="header-top-right">
						<span class="login">
							<i class="fa fa-phone"></i> <?php _e('Call US:','fortune');?> <a href="tel:<?php echo esc_attr($fortune_theme_options['contact_phone']);?>"><?php echo esc_attr($fortune_theme_options['contact_phone']);?></a>
						</span>
						<span class="register">
						<i class="fa fa-envelope-o"></i>	<?php _e('Email:','fortune');?> <a href="mailto:<?php echo sanitize_email($fortune_theme_options['contact_email']);?>"><?php echo sanitize_email($fortune_theme_options['contact_email']);?></a>
						</span>

                        <span class="my-header-top-fa"><a href="<?php echo get_site_url();?>/obchod" title="obchod"><? echo do_shortcode('[fa class="fa-shopping-cart"]');?></a>
                        </span>                                                

					</div>                    
                    <?php
					} ?>
			</div>            
		</div><?php } ?>
			<div class="header-main" style="background-image:url(<?php echo esc_url(get_header_image()); ?>);">
				<div class="container">
					<nav class="navbar navbar-default fhmm" role="navigation">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle">
								<i class="fa fa-bars"></i>
							</button>
							<!-- Logo -->
							<div class="logo">
									<?php /*if ( function_exists( 'the_custom_logo' )) {
										the_custom_logo();
									} */?>
<a href="<? echo get_site_url();?>" class="custom-logo-link" rel="home" itemprop="url"><img src="/wp-content/uploads/logo.png" class="custom-logo" alt="Eko Status" itemprop="logo" /></a>
									<a href="<?php echo esc_url(home_url('/')); ?>">
									<?php $header_text = display_header_text();
									if($header_text)
									{ ?>
									<h1 class="site-title"><?php echo get_bloginfo('name'); ?> </h1> </a>
									<?php if(get_bloginfo('description')!=""){ ?>
											<p class="tagline"><?php echo get_bloginfo('description'); ?></p><?php
										}
									} ?>
									
							</div>
							<!-- Logo / End -->
						</div><!-- end navbar-header -->

						<div id="main-nav" class="navbar-collapse collapse" role="navigation">
						<!-- Menu Goes Here -->
						<?php wp_nav_menu(array(
								'theme_location' => 'primary',
								'container' => false,
								'menu_class' => 'nav navbar-nav',
								'fallback_cb' => 'fortune_fallback_page_menu',
								'walker' => new fortune_nav_walker(),
								)
							); ?>
						</div>
					</nav>
				</div>
			</div>
				
		</header>
		<!-- Header / End -->
		<!-- Main -->
        <?php
        if ( is_front_page() ) {
            echo do_shortcode("[smartslider3 slider=2]");
        }
        ?>
		<div class="main" role="main">