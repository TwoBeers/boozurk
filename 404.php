<?php get_header(); ?>

<?php boozurk_hook_before_posts(); ?>
<div id="posts_content" class="posts_wide">
	<div class="post" id="post-404-not-found">
		<h2 class="storytitle">Error 404 - <?php _e( 'Page not found','boozurk' ); ?></h2>
		<p><?php _e( "Sorry, you're looking for something that isn't here" ,'boozurk' ); ?>: <u><?php echo home_url() . esc_html( $_SERVER['REQUEST_URI'] ); ?></u></p>
		<?php if ( is_active_sidebar( '404-widgets-area' ) ) { ?>
			<p><?php _e( 'You can try the following:','boozurk' ); ?></p>
			<?php boozurk_hook_before_404_sidebar(); ?>
			<div class="ul_fwa">
				<?php dynamic_sidebar( '404-widgets-area' ); ?>
			</div>
			<?php boozurk_hook_after_404_sidebar(); ?>
		<?php } else { ?>
			<p><?php _e( 'There are several links scattered around the page, maybe you can find what you\'re looking for', 'boozurk' ); ?></p>
			<p><?php _e( 'Perhaps using the search form will help...', 'boozurk' ); ?></p>
			<?php get_search_form(); ?>
		<?php } ?>
		<div class="fixfloat"> </div>
	</div>
</div>
<?php boozurk_hook_after_posts(); ?>

<?php get_footer(); ?>
