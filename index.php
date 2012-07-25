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
			) ? get_post_format( $post->ID ) : '' ;
		} ?>
		
		<?php boozurk_hook_before_post(); ?>
		<?php get_template_part( 'loop/post', $bz_use_format ); ?>
		<?php boozurk_hook_after_post(); ?>
	
	<?php } //end while ?>

	<div id="bz-page-nav">
	<?php if ( function_exists( 'wp_pagenavi' ) ) { ?>
		<?php wp_pagenavi(); ?>
	<?php } else { ?>
		<div id="bz-page-nav-msg"></div>
		<div id="bz-page-nav-subcont">
			<?php //num of pages
			global $paged;
			if ( !$paged ) {
				$paged = 1;
			}
			previous_posts_link( '&laquo;' );
			printf( __( 'page %1$s of %2$s','boozurk' ), $paged, $wp_query->max_num_pages );
			?>
			<span id="bz-next-posts-link"><?php next_posts_link( '&raquo;' ); ?></span>
		</div>
		<div class="w_title"></div>
		<div id="bz-next-posts-button" class="hide-if-no-js">
			<input type="button" value="<?php echo __( 'Next Page', 'boozurk' ); ?>" onClick="boozurkScripts.AJAX_paged();" />
		</div>
	<?php } ?>
	</div>

<?php } else { ?>

	<div class="post"><p><?php _e( 'Sorry, no posts matched your criteria.','boozurk' ); ?></p></div>

<?php } //endif ?>

</div>
<?php boozurk_hook_after_posts(); ?>

<?php get_footer(); ?>
