<!-- here should be the footer widget area -->
<?php
	/* The footer widget area is triggered if any of the areas have widgets. */
	if ( ! is_active_sidebar( 'first-footer-widget-area' ) && ! is_active_sidebar( 'second-footer-widget-area' ) && ! is_active_sidebar( 'third-footer-widget-area'  ) && ! is_active_sidebar( 'fourth-footer-widget-area' ) ) {
		return;
	}
?>

<?php tha_sidebars_before(); ?>
<div id="footer-widget-area">
	<?php tha_sidebar_top(); ?>
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

	<?php tha_sidebar_bottom(); ?>
</div><!-- #footer-widget-area -->
<?php tha_sidebars_after(); ?>
