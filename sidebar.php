<?php
/**
 * sidebar.php
 *
 * Template part file that contains the default sidebar content
 *
 * @package Boozurk
 * @since 1.00
 */
?>

<?php if ( boozurk_get_opt( 'boozurk_sidebar_primary' ) == 'hidden' ) return; ?>

<?php boozurk_hook_sidebars_before( 'primary' ); ?>

<!-- begin primary sidebar -->

<div class="sidebar<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_primary' ) ?>" id="sidebar-primary">

	<div class="viewport">

		<div class="overview">

			<?php boozurk_hook_sidebar_top( 'primary' ); ?>

			<?php if ( !dynamic_sidebar( 'primary-widget-area' ) ) { //if the widget area is empty, we print some standard wigets ?>

				<?php boozurk_default_widgets(); ?>

			<?php } ?>

			<br class="fixfloat" />

			<?php boozurk_hook_sidebar_bottom( 'primary' ); ?>

		</div>

	</div>

</div>

<!-- end primary sidebar -->

<?php boozurk_hook_sidebars_after( 'primary' ); ?>
