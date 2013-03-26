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

<!-- begin primary sidebar -->

<div class="sidebar<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_primary' ) ?>" id="sidebar-primary">

	<div class="primary top-fade<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_primary' ) ?>"></div>

	<div class="inner">

		<?php boozurk_hook_sidebar_top(); ?>

		<?php boozurk_hook_this_sidebar_top( 'primary' ); ?>

		<?php if ( !dynamic_sidebar( 'primary-widget-area' ) ) { //if the widget area is empty, we print some standard wigets ?>

			<?php boozurk_default_widgets(); ?>

		<?php } ?>

		<br class="fixfloat">

		<?php boozurk_hook_this_sidebar_bottom( 'primary' ); ?>

		<?php boozurk_hook_sidebar_bottom(); ?>

	</div>

	<div class="primary bottom-fade<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_primary' ) ?>"></div>

</div>

<!-- end primary sidebar -->