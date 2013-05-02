<?php
/**
 * header.php
 *
 * Template part file that contains the HTML document head and 
 * opening HTML body elements, as well as the site header and 
 * the "breadcrumb" bar.
 *
 *
 * @package Boozurk
 * @since 1.00
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes( 'xhtml' ); ?> itemscope itemtype="http://schema.org/Blog">

	<head profile="http://gmpg.org/xfn/11">

		<?php boozurk_hook_head_top(); ?>

		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />

		<meta name="viewport" content="width=100%; initial-scale=1; maximum-scale=1; minimum-scale=1; user-scalable=no;" />

		<title><?php wp_title( '&laquo;', true, 'right' ); ?></title>

		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<?php wp_get_archives( 'type=monthly&format=link&limit=10' ); ?>

		<?php boozurk_hook_head_bottom(); ?>

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>

		<?php boozurk_hook_body_top(); ?>

		<div id="main">

			<div id="fixed-bg"></div>

			<div id="content">

				<?php boozurk_hook_header_before(); ?>

				<div id="head">

					<?php boozurk_hook_header_top(); ?>

					<?php echo boozurk_get_header(); ?>

					<?php boozurk_hook_header_bottom(); ?>

				</div>

				<?php boozurk_hook_header_after(); ?>
