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
				<?php
				$bz_post_title = the_title_attribute( 'echo=0' );
				if ( $bz_post_title ) {
					?><h2 class="storytitle"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo $bz_post_title; ?></a></h2><?php
				}
				?>
				<?php boozurk_hook_after_post_title(); ?>
				<div class="storycontent">
					<?php the_excerpt(); ?>
				</div>
				<div class="fixfloat"> </div>
			</div>
			<?php boozurk_hook_after_post(); ?>
		<?php } ?>
		<div class="w_title" id="bz-page-nav">
			<?php //num of pages
			global $paged;
			if ( !$paged ) { $paged = 1; }
			previous_posts_link( '&laquo;' );
			printf( __( 'page %1$s of %2$s','boozurk' ), $paged, $wp_query->max_num_pages );
			next_posts_link( '&raquo;' );
			?>
		</div>
	<?php } else { ?>
		<p class="bz-no-post"><?php _e( 'Sorry, no posts matched your criteria.','boozurk' );?></p>
	<?php } ?>
</div>
<?php boozurk_hook_after_posts(); ?>

<?php get_sidebar(); // show sidebar ?>

<?php get_footer(); ?>
