<!-- here should be the fixed widget area -->
<?php
	/* The fixed widget area is triggered if any of the areas have widgets. */
	if ( !is_active_sidebar( 'fixed-widget-area'  ) ) {
		return;
	}
?>

<div id="fixed-widget-area">
	<?php dynamic_sidebar( 'fixed-widget-area' ); ?>
	<div class="fixfloat"></div> 
</div><!-- #fixed-widget-area -->
