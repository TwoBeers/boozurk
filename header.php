<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes( 'xhtml' ); ?> itemscope itemtype="http://schema.org/Blog">

	<head profile="http://gmpg.org/xfn/11">
		<?php tha_head_top(); ?>
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta name = "viewport" content = "width = device-width" />
		<title><?php wp_title( '&laquo;', true, 'right' ); ?></title>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php wp_get_archives( 'type=monthly&format=link&limit=10' ); ?>
		<?php tha_head_bottom(); ?>
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

		<?php boozurk_hook_body_top(); ?>

		<?php wp_nav_menu( array( 'container_class' => 'bz-menu', 'container_id' => 'secondary1', 'fallback_cb' => false, 'theme_location' => 'secondary1', 'depth' => 1 ) ); ?>

		<div id="main">

			<div id="content">

				<?php tha_header_before(); ?>
				<div id="head">
					<?php tha_header_top(); ?>
					<?php echo boozurk_get_header(); ?>
					<?php tha_header_bottom(); ?>
				</div>
				<?php tha_header_after(); ?>

				<?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'mainmenu', 'fallback_cb' => 'boozurk_pages_menu', 'theme_location' => 'primary', 'walker' => new boozurk_Thumb_Walker ) ); ?>
				<?php boozurk_hook_menu_after(); ?>
				<?php get_sidebar( 'header' ); // show header widgets area ?>
				<?php boozurk_breadcrumb(); ?>
