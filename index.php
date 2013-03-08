<?php
/**
 * index.php
 *
 * This file is the master/default template file, used for Inedx/Archives/Search
 *
 * @package Boozurk
 * @since 1.00
 */
?>

<?php get_header(); ?>

<?php boozurk_hook_content_before(); ?>

<div id="posts_content">

	<?php boozurk_hook_content_top(); ?>

	<?php get_template_part( 'loop', 'index' ); ?>

	<?php boozurk_hook_content_bottom(); ?>

</div>

<?php boozurk_hook_content_after(); ?>

<?php get_footer(); ?>
