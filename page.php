<?php get_header(); ?>

<?php boozurk_hook_before_posts(); ?>
<div id="posts_content">
	<?php if ( have_posts() ) {
		while ( have_posts() ) {
			the_post(); ?>
			<?php boozurk_hook_before_post(); ?>
			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<?php boozurk_extrainfo(); ?>
				<?php boozurk_hook_before_post_title(); ?>
				<?php boozurk_featured_title();	?>
				<?php boozurk_hook_after_post_title(); ?>
				<?php boozurk_hook_before_post_content(); ?>
				<div class="storycontent">
					<?php the_content(); ?>
				</div>
				<?php boozurk_hook_after_post_content(); ?>
				<div class="fixfloat" style="padding-top: 20px;">
						<?php wp_link_pages( 'before=<div class="comment_tools">' . __( 'Pages','boozurk' ) . ':&after=</div>' ); ?>
				</div>
			</div>
			<?php boozurk_hook_after_post(); ?>
			<?php if ( is_active_sidebar( 'single-widgets-area' ) ) { ?>
				<div id="single-widgets-area" class="ul_swa fixfloat">
					<?php dynamic_sidebar( 'single-widgets-area' ); ?>
					<div class="fixfloat"></div>
				</div>
			<?php } ?>
			<?php comments_template(); // Get wp-comments.php template ?>
		<?php	} //end while
	} else {?>
		<p class="bz-no-post"><?php _e( 'Sorry, no posts matched your criteria.','boozurk' );?></p>
	<?php } //endif ?>
</div>
<?php boozurk_hook_after_posts(); ?>

<?php get_sidebar(); // show sidebar ?>

<?php get_footer(); ?>
