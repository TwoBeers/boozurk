<?php
/**
 * sidebar-secondary.php
 *
 * Template part file that contains the right-column dynamic sidebar
 *
 * @package Boozurk
 * @since 1.00
 */
?>

<!-- begin secondary sidebar -->

<div class="sidebar<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_secondary' ) ?>" id="sidebar-secondary">

	<div class="secondary top-fade<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_secondary' ) ?>"></div>

	<div class="inner">

		<?php boozurk_hook_sidebar_top(); ?>

		<?php boozurk_hook_this_sidebar_top( 'secondary' ); ?>

		<!-- here should be the secondary widget area -->
		<?php if ( is_active_sidebar( 'fixed-widget-area'  ) ) { ?>

			<div id="fixed-widget-area">

				<?php dynamic_sidebar( 'fixed-widget-area' ); ?>

				<br class="fixfloat"> 

			</div><!-- #fixed-widget-area -->

		<?php } ?>

		<?php boozurk_hook_this_sidebar_bottom( 'secondary' ); ?>

		<?php boozurk_hook_sidebar_bottom(); ?>

	</div>

	<?php if ( ! is_active_widget( false, false, 'bz-navbuttons', true ) ) { ?>

		<?php boozurk_navbuttons(); ?>

	<?php } else { ?>

		<div class="secondary bottom-fade<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_secondary' ) ?>"></div>

	<?php } ?>

</div>

<!-- end secondary sidebar -->