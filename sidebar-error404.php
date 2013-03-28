<?php
/**
 * sidebar-error404.php
 *
 * Template part file that contains the 404 page widget area
 *
 * @package Boozurk
 * @since 2.05
 */
?>

<?php boozurk_hook_sidebars_before( 'error404' ); ?>

<div class="ul_fwa">

	<?php boozurk_hook_sidebar_top( 'error404' ); ?>

	<?php dynamic_sidebar( 'error404-widgets-area' ); ?>

	<?php boozurk_hook_sidebar_bottom( 'error404' ); ?>

</div>

<?php boozurk_hook_sidebars_after( 'error404' ); ?>
