<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes( 'xhtml' ); ?>>

	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta name = "viewport" content = "width = device-width" />
		<title><?php
			if ( is_front_page() ) {
				bloginfo( 'name' ); ?> - <?php bloginfo( 'description' );
			} else {
				wp_title( '&laquo;', true, 'right' );
				bloginfo( 'name' );
			}
			?></title>
		<?php global $boozurk_opt; ?>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php wp_get_archives( 'type=monthly&format=link&limit=10' ); ?>
		<?php wp_head(); ?>
	</head>

	<body <?php body_class( 'no-js' ); ?>>
		<?php wp_nav_menu( array( 'container_class' => 'bz-menu', 'container_id' => 'secondary1', 'fallback_cb' => false, 'theme_location' => 'secondary1', 'depth' => 1 ) ); ?>
		<div id="main">

		<div id="content">
			<div id="pages">
				<div id="pages_c">
					<?php if ( isset( $boozurk_opt['boozurk_logo'] ) && ( $boozurk_opt['boozurk_logo'] != '' ) ) echo '<img class="bz-logo" alt="logo" src="' . $boozurk_opt['boozurk_logo'] . '" />';?>
					<div class="bz-description"><?php bloginfo( 'description' ); ?></div>
					<?php get_sidebar( 'fixed' ); // show header widgets area ?>
				</div>
			</div>
			<?php if ( ! is_active_widget(false, false, 'bz-navbuttons', true) ) { boozurk_navbuttons(); } ?>
			<?php boozurk_hook_before_header(); ?>
			<div id="head">
				<?php boozurk_hook_before_site_title(); ?>
				<h1><a href="<?php echo home_url(); ?>/"><?php bloginfo( 'name' ); ?></a></h1>
				<?php boozurk_hook_after_site_title(); ?>
			</div>
			<?php boozurk_hook_after_header(); ?>
			
			<?php boozurk_hook_before_pages(); ?>
			<div id="mainmenu_container">
				<?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'mainmenu', 'fallback_cb' => 'boozurk_pages_menu', 'theme_location' => 'primary', 'walker' => new boozurk_Thumb_Walker ) ); ?>
				<div class="fixfloat"></div>
			</div>
			<?php boozurk_hook_after_pages(); ?>
			<?php get_sidebar( 'header' ); // show header widgets area ?>
			<?php boozurk_breadcrumb(); ?>
