<?php
/**
 * sidebar-footer.php
 *
 * Template part file that contains the footer widget area
 *
 * @package boozurk
 * @since boozurk 1.00
 */
?>

<!-- here should be the footer widget area -->
<?php
	/* The footer widget area is triggered if any of the areas have widgets. */
	if ( ! is_active_sidebar( 'first-footer-widget-area' ) && ! is_active_sidebar( 'second-footer-widget-area' ) && ! is_active_sidebar( 'third-footer-widget-area'  ) && ! is_active_sidebar( 'fourth-footer-widget-area' ) ) {
		return;
	}
?>

<?php boozurk_hook_sidebars_before(); ?>
<div id="footer-widget-area">
	<?php boozurk_hook_sidebar_top(); ?>
	<div class="fixfloat"><?php boozurk_hook_footer_sidebar_top(); ?></div>

	<div id="first_fwa" class="widget-area">
			<?php if ( is_active_sidebar( 'first-footer-widget-area' ) ) { dynamic_sidebar( 'first-footer-widget-area' ); } ?>
	</div><!-- #first .widget-area -->

	<?php if ( is_active_sidebar( 'second-footer-widget-area' ) ) { ?>
		<div id="second_fwa" class="widget-area">
				<?php dynamic_sidebar( 'second-footer-widget-area' ); ?>
		</div><!-- #second .widget-area -->
	<?php } ?>

	<?php if ( is_active_sidebar( 'third-footer-widget-area' ) ) { ?>
		<div id="third_fwa" class="widget-area">
				<?php dynamic_sidebar( 'third-footer-widget-area' ); ?>
		</div><!-- #third .widget-area -->
	<?php } ?>

	<div class="fixfloat"><?php boozurk_hook_footer_sidebar_bottom(); ?></div>

	<?php boozurk_hook_sidebar_bottom(); ?>
</div><!-- #footer-widget-area -->
<?php boozurk_hook_sidebars_after(); ?>
