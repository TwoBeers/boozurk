<!-- begin secondary sidebar -->

<div class="sidebar<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_secondary' ) ?>" id="sidebar-secondary">
	<div class="inner">
		<?php if ( boozurk_get_opt( 'boozurk_logo' ) ) echo '<img class="bz-logo" alt="logo" src="' . boozurk_get_opt( 'boozurk_logo' ) . '" />';?>
		<?php if ( boozurk_get_opt( 'boozurk_logo_description' ) ) echo '<div class="bz-description">' . get_bloginfo( 'description' ) . '</div>';?>
		<!-- here should be the fixed widget area -->
		<?php
			/* The fixed widget area is triggered if any of the areas have widgets. */
			if ( is_active_sidebar( 'fixed-widget-area'  ) ) {
		?>

		<div id="fixed-widget-area">
			<?php dynamic_sidebar( 'fixed-widget-area' ); ?>
			<div class="fixfloat"></div> 
		</div><!-- #fixed-widget-area -->

		<?php
			}
		?>
	</div>
</div>

<div class="secondary top-fade<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_secondary' ) ?>"></div>

<?php if ( ! is_active_widget(false, false, 'bz-navbuttons', true) ) { ?>
	<?php boozurk_navbuttons(); ?>
<?php } else { ?>
<div class="secondary bottom-fade<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_secondary' ) ?>"></div>
<?php } ?>
<!-- end secondary sidebar -->