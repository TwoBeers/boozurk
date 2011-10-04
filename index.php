<?php get_header(); ?>

<?php boozurk_hook_before_posts(); ?>
<div id="posts_content">

<?php
global $boozurk_opt;

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post(); ?>
		<?php if ( post_password_required() ) {
			$bz_use_format = 'protected';
		} else {
			$bz_use_format = ( 
				function_exists( 'get_post_format' ) && 
				isset( $boozurk_opt['boozurk_post_formats_' . get_post_format( $post->ID ) ] ) && 
				$boozurk_opt['boozurk_post_formats_' . get_post_format( $post->ID ) ] == 1 
			) ? get_post_format( $post->ID ) : 'standard' ;
		} ?>
		
		<?php boozurk_hook_before_post(); ?>
		<?php get_template_part( 'loop/post', $bz_use_format ); ?>
		<?php boozurk_hook_after_post(); ?>
	
	<?php } //end while ?>

	<div class="w_title" id="bz-page-nav">
		<?php //num of pages
		global $paged;
		if ( !$paged ) {
			$paged = 1;
		}
		previous_posts_link( '&laquo;' );
		printf( __( 'page %1$s of %2$s','boozurk' ), $paged, $wp_query->max_num_pages );
		next_posts_link( '&raquo;' );
		?>
	</div>

<?php } else { ?>

	<p><?php _e( 'Sorry, no posts matched your criteria.','boozurk' ); ?></p>

<?php } //endif ?>

</div>
<?php boozurk_hook_after_posts(); ?>

<?php get_footer(); ?>
