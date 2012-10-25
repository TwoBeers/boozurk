<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes( 'xhtml' ); ?> itemscope itemtype="http://schema.org/Blog">

	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta name = "viewport" content = "width = device-width" />
		<title><?php wp_title( '&laquo;', true, 'right' ); ?></title>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php wp_get_archives( 'type=monthly&format=link&limit=10' ); ?>
		<?php wp_head(); ?>
	</head>

	<body <?php body_class( 'no-js' ); ?>>
<!--[if lte IE 6]>
<div style="background:#e29808;color:#fff;padding:10px;">
	It looks like you're using an old and insecure version of Internet Explorer. Using an outdated browser makes your computer unsafe. For the best WordPress experience, please update your browser
</div>
<![endif]-->
		<?php wp_nav_menu( array( 'container_class' => 'bz-menu', 'container_id' => 'secondary1', 'fallback_cb' => false, 'theme_location' => 'secondary1', 'depth' => 1 ) ); ?>
		<div id="main">

		<div id="content">

			<?php get_sidebar(); // show sidebar ?>

			<?php get_sidebar( 'secondary' ); // show header widgets area ?>

			<?php boozurk_hook_before_header(); ?>
			<div id="head">
				<?php boozurk_hook_before_site_title(); ?>
				<h1<?php echo display_header_text() ? '' : ' class="hide_if_no_print"'; ?>><a href="<?php echo home_url(); ?>/"><?php bloginfo( 'name' ); ?></a></h1>
				<?php boozurk_hook_after_site_title(); ?>
<?php if ( get_header_image() ) { ?>
	<?php if ( display_header_text() ) { ?>
				<img alt="<?php echo home_url(); ?>" src="<?php header_image(); ?>" />
	<?php } else { ?>
				<a href="<?php echo home_url(); ?>/"><img alt="<?php echo home_url(); ?>" src="<?php header_image(); ?>" /></a>
	<?php } ?>
<?php } ?>
			</div>
			<?php boozurk_hook_after_header(); ?>
			
			<?php boozurk_hook_before_pages(); ?>
			<?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'mainmenu', 'fallback_cb' => 'boozurk_pages_menu', 'theme_location' => 'primary', 'walker' => new boozurk_Thumb_Walker ) ); ?>
			<?php boozurk_hook_after_pages(); ?>
			<?php get_sidebar( 'header' ); // show header widgets area ?>
			<?php boozurk_breadcrumb(); ?>
