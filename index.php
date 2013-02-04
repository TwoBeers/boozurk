<?php get_header(); ?>

<?php tha_content_before(); ?>
<div id="posts_content">
	<?php tha_content_top(); ?>

<?php
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post(); ?>
		<?php get_template_part( 'loop/post', boozurk_get_post_format( $post->ID ) ); ?>
	
	<?php } //end while ?>

	<?php boozurk_navigate_archives(); ?>

<?php } else { ?>

	<div class="post"><p><?php _e( 'Sorry, no posts matched your criteria.','boozurk' ); ?></p></div>

<?php } //endif ?>

	<?php tha_content_bottom(); ?>
</div>
<?php tha_content_after(); ?>

<?php get_footer(); ?>
