<?php
/**
 * single.php
 *
 * The single page template file, used to display single pages.
 *
 * @package boozurk
 * @since boozurk 1.00
 */
?>

<?php get_header(); ?>

<?php boozurk_hook_content_before(); ?>
<div id="posts_content">
	<?php boozurk_hook_content_top(); ?>
	<?php if ( have_posts() ) {
		while ( have_posts() ) {
			the_post(); ?>
			<?php boozurk_hook_entry_before(); ?>
			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<?php boozurk_extrainfo(); ?>
				<?php boozurk_hook_entry_top(); ?>
				<?php boozurk_hook_post_title_before(); ?>
				<?php boozurk_featured_title( array( 'featured' => true ) ); ?>
				<?php boozurk_hook_post_title_after(); ?>
				<div class="storycontent">
					<?php the_content(); ?>
				</div>
				<?php boozurk_hook_entry_bottom(); ?>
			</div>
			<?php boozurk_hook_entry_after(); ?>

			<?php comments_template(); // Get wp-comments.php template ?>

		<?php	} //end while
	} else {?>
		<div class="post"><p><?php _e( 'Sorry, no posts matched your criteria.','boozurk' ); ?></p></div>
	<?php } //endif ?>
	<?php boozurk_hook_content_bottom(); ?>
</div>
<?php boozurk_hook_content_after(); ?>

<?php get_footer(); ?>
