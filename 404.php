<?php
/**
 * 404.php
 *
 * This file is the Error 404 Page template file, which is output whenever
 * the server encounters a "404 - file not found" error.
 *
 * @package Boozurk
 * @since 1.00
 */


get_header(); ?>

<?php boozurk_hook_content_before(); ?>

<div id="posts_content">

	<?php boozurk_hook_content_top(); ?>

	<div class="hentry not-found" id="post-404-not-found">

		<div class="post_meta_container"><span class="pmb_format btn"><i class="icon-placeholder"></i></span></div>

		<h2 class="storytitle"><?php _e( 'Error 404','boozurk' ); ?> - <?php _e( 'Page not found','boozurk' ); ?></h2>

		<div class="storycontent">

			<p><?php _e( "Sorry, you're looking for something that isn't here" ,'boozurk' ); ?>: <u><?php echo home_url() . esc_html( $_SERVER['REQUEST_URI'] ); ?></u></p>

			<br>

			<?php if ( is_active_sidebar( 'error404-widgets-area' ) ) { ?>

				<p><?php _e( 'Here is something that might help:','boozurk' ); ?></p>

				<?php get_sidebar( 'error404' ); ?>

			<?php } else { ?>

				<p><?php _e( "There are several links scattered around the page, maybe they can help you on finding what you're looking for.", 'boozurk' ); ?></p>

				<p><?php _e( 'Perhaps using the search form will help too...', 'boozurk' ); ?></p>

				<?php get_search_form(); ?>

			<?php } ?>

		</div>

		<br class="fixfloat">

	</div>

	<?php boozurk_hook_content_bottom(); ?>

</div>

<?php boozurk_hook_content_after(); ?>

<?php get_footer(); ?>
