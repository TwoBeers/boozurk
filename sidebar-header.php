<!-- here should be the Header widget area -->
<?php
	/* The Header widget area is triggered if any of the areas have widgets. */
	if ( !is_active_sidebar( 'header-widget-area'  ) ) {
		return;
	}
?>

<?php tha_sidebars_before(); ?>
<div id="header-widget-area">
	<?php tha_sidebar_top(); ?>
	<?php dynamic_sidebar( 'header-widget-area' ); ?>
	<?php tha_sidebar_bottom(); ?>
	<div class="fixfloat"></div> 
</div><!-- #header-widget-area -->
<?php tha_sidebars_after(); ?>
