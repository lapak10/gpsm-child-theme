<?php if ( ! isset( $_SESSION ) ) session_start(); ?>
<!DOCTYPE html>
<!--[if IE 6]>
<html class="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html class="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 9]>
<html class="ie9" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html id="nrd" <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php wp_title(); ?></title>
	<?php elegant_description(); ?>
	<?php elegant_keywords(); ?>
	<?php elegant_canonical(); ?>

	<?php do_action( 'et_head_meta' ); ?>

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php $template_directory_uri = get_template_directory_uri(); ?>
	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( $template_directory_uri . '/js/html5.js"' ); ?>" type="text/javascript"></script>
	<![endif]-->

	<script type="text/javascript">
		document.documentElement.className = 'js';
	</script>

	<?php wp_head(); ?>

	<script src="//lp.gpsinsight.com/js/forms2/js/forms2.min.js"></script>
	
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/inc/fuelcalc_react/src.3ec417c1.css">

</head>
<body <?php body_class(); ?>>
	<div class="drawer js-drawer">
		<div class="drawer-content">
			<div class="drawer-content-top mix-drawer-content-top_rgt">
				<span class="iconButton iconButton_close js-closeBtn"><span class="isVisuallyHidden">Close</span></span>
			</div>

			<?php if ( is_active_sidebar( 'side_nav_top' ) ) : ?>
				<div class="drawer-content-sub">
					<div class="sideNavWidget widget-area" role="complementary">
						<?php dynamic_sidebar( 'side_nav_top' ); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="drawer-content-body">
				<?php 
					if ( has_nav_menu( 'secondary-menu' ) ) {
    						wp_nav_menu( array( 
								'theme_location' => 'secondary-menu',
								'menu_class'=>'slideOutMenu',
								'container_class'=> 'd-lg-none'
								));
					}
				?>
				<?php

				wp_nav_menu(
					array(
						'theme_location' => 'slide_out_nav',
						'menu_class'      => 'slideOutMenu'
						)
					);

					?>
				</div>
				<div class="drawer-content-foot">
					<?php if ( is_active_sidebar( 'side_nav_bottom' ) ) : ?>
						<div class="sideNavWidget widget-area" role="complementary">
							<?php dynamic_sidebar( 'side_nav_bottom' ); ?>
						</div>
					<?php endif; ?>
				</div>


			</div>
			<div class="drawer-site">
				<div id="page-container">
					<?php
					if ( is_page_template( 'page-template-blank.php' ) ) {
						return;
					}

					$et_secondary_nav_items = et_divi_get_top_nav_items();

					$et_email = $et_secondary_nav_items->email;

					$et_contact_info_defined = $et_secondary_nav_items->contact_info_defined;

					$show_header_social_icons = $et_secondary_nav_items->show_header_social_icons;

					$et_secondary_nav = $et_secondary_nav_items->secondary_nav;

					$et_top_info_defined = $et_secondary_nav_items->top_info_defined;
					?>

					<?php if ( $et_top_info_defined ) : ?>
						<div id="top-header">
							<div class="container clearfix">

								<?php if ( $et_contact_info_defined ) : ?>

									<div id="et-info">
										<span id="et-info-phone"><?php echo gps_insight_get_geo_phone_number(); ?></span>

										<?php if ( '' !== ( $et_email = et_get_option( 'header_email' ) ) ) : ?>
											<a href="<?php echo esc_attr( 'mailto:' . $et_email ); ?>"><span id="et-info-email"><?php echo esc_html( $et_email ); ?></span></a>
										<?php endif; ?>

										<?php
										if ( true === $show_header_social_icons ) {
											get_template_part( 'includes/social_icons', 'header' );
										} ?>
									</div> <!-- #et-info -->

								<?php endif; // true === $et_contact_info_defined ?>

								<div id="et-secondary-menu">
									<?php
									if ( ! $et_contact_info_defined && true === $show_header_social_icons ) {
										get_template_part( 'includes/social_icons', 'header' );
									} else if ( $et_contact_info_defined && true === $show_header_social_icons ) {
										ob_start();

										get_template_part( 'includes/social_icons', 'header' );

										$duplicate_social_icons = ob_get_contents();

										ob_end_clean();

										printf(
											'<div class="et_duplicate_social_icons">
											%1$s
										</div>',
										$duplicate_social_icons
										);
									}

									if ( '' !== $et_secondary_nav ) {
										echo $et_secondary_nav;
									}

									et_show_cart_total();
									?>
								</div> <!-- #et-secondary-menu -->

							</div> <!-- .container -->
						</div> <!-- #top-header -->
					<?php endif; // true ==== $et_top_info_defined ?>

					<header id="main-header" data-height-onload="<?php echo esc_attr( et_get_option( 'menu_height', '66' ) ); ?>">

						<div class="container clearfix et_menu_container">
							<?php
							$logo = ( $user_logo = et_get_option( 'divi_logo' ) ) && '' != $user_logo
							? $user_logo
							: $template_directory_uri . '/images/logo.png';
							?>
							<div class="logo_container">
								<span class="logo_helper"></span>
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
									<img src="<?php echo esc_attr( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" id="logo" />
								</a>
							</div>
							<div class="headerActions">


							<div class="headerActions-item headerActions-item_hidden isHidden_SM">
								<?php if ( is_active_sidebar( 'main_menu_cta_area' ) ) : ?>
											
													<?php dynamic_sidebar( 'main_menu_cta_area' ); ?>
												
								<?php endif; ?>

							</div>
								
							
							
							
							<div class="headerActions-item"><?php if ( false !== et_get_option( 'show_search_icon', true ) ) : ?>
									<div id="et_top_search">
										<span id="et_search_icon"></span>
									</div>
								<?php endif; // true === et_get_option( 'show_search_icon', false ) ?></div>
								<div class="headerActions-item headerActions-item_hidden">
									<div class="iconButton js-drawerToggle">
										<span class="iconButton-label isVisuallyHidden">Menu</span>
										<span class="iconButton-icon"> <i class="mobile_menu_bar"></i></span>
									</div>
								</div>
							</div>
							<div id="et-top-navigation">
								<nav id="top-menu-nav">
									<?php
									$menuClass = 'nav';
									if ( 'on' == et_get_option( 'divi_disable_toptier' ) ) $menuClass .= ' et_disable_top_tier';
									$primaryNav = '';

									$primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'menu_id' => 'top-menu', 'echo' => false ) );

									if ( '' == $primaryNav ) :
										?>
									<ul id="top-menu" class="<?php echo esc_attr( $menuClass ); ?>">
										<?php if ( 'on' == et_get_option( 'divi_home_link' ) ) { ?>
										<li <?php if ( is_home() ) echo( 'class="current_page_item"' ); ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'Divi' ); ?></a></li>
										<?php }; ?>

										<?php show_page_menu( $menuClass, false, false ); ?>
										<?php show_categories_menu( $menuClass, false ); ?>
									</ul>
									<?php
									else :
										echo( $primaryNav );
									endif;
									?>
								</nav>

								<?php
								if ( ! $et_top_info_defined ) {
									et_show_cart_total( array(
										'no_text' => true,
										) );
								}
								?>

								<?php //do_action( 'et_header_top' ); // adds mobile navigation ?>

							</div> <!-- #et-top-navigation -->

						</div> <!-- .container -->
						<div class="et_search_outer">
							<div class="container et_search_form_container">
								<form role="search" method="get" class="et-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
									<?php
									printf( '<input type="search" class="et-search-field" placeholder="%1$s" value="%2$s" name="s" title="%3$s" />',
										esc_attr__( 'Search &hellip;', 'Divi' ),
										get_search_query(),
										esc_attr__( 'Search for:', 'Divi' )
										);
										?>
									</form>
									<span class="et_close_search_field"></span>
								</div>
							</div>
						</header> <!-- #main-header -->

						<div id="et-main-area">
