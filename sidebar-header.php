<?php
/**
 * sidebar-header.php
 *
 * Template part file that contains the header widget area
 *
 * @package Boozurk
 * @since 1.00
 */
?>

<!-- here should be the Header widget area -->
<?php
	/* The Header widget area is triggered if any of the areas have widgets. */
	if ( !is_active_sidebar( 'header-widget-area'  ) )
		return;
?>

<?php boozurk_hook_sidebars_before(); ?>

<div id="header-widget-area">

	<?php boozurk_hook_sidebar_top(); ?>

	<?php boozurk_hook_this_sidebar_top( 'header' ); ?>

	<?php dynamic_sidebar( 'header-widget-area' ); ?>

	<?php boozurk_hook_this_sidebar_bottom( 'header' ); ?>

	<?php boozurk_hook_sidebar_bottom(); ?>

	<br class="fixfloat">

</div><!-- #header-widget-area -->

<?php boozurk_hook_sidebars_after(); ?>
