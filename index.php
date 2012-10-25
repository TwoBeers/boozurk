<?php get_header(); ?>

<?php boozurk_hook_before_posts(); ?>
<div id="posts_content">

<?php
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post(); ?>
		<?php if ( post_password_required() ) {
			$bz_use_format = 'protected';
		} else {
			$bz_use_format = ( 
				function_exists( 'get_post_format' ) && 
				boozurk_get_opt( 'boozurk_post_formats_' . get_post_format( $post->ID ) )
			) ? get_post_format( $post->ID ) : '' ;
		} ?>
		
		<?php boozurk_hook_before_post(); ?>
		<?php get_template_part( 'loop/post', $bz_use_format ); ?>
		<?php boozurk_hook_after_post(); ?>
	
	<?php } //end while ?>

	<div id="bz-page-nav" class="bz-navigate navigate_archives">
	<?php if ( function_exists( 'wp_pagenavi' ) ) { ?>
		<?php wp_pagenavi(); ?>
	<?php } elseif ( function_exists( 'wp_paginate' ) ) { ?>
		<?php wp_paginate(); ?>
	<?php } else { ?>
		<div id="bz-page-nav-msg"></div>
		<div id="bz-page-nav-subcont">
			<?php //num of pages
				global $paged;
				if ( !$paged ) $paged = 1;
			?>
			<span id="bz-next-posts-link"><?php next_posts_link( '&laquo;' ); ?></span>
			<?php printf( '<span>' . __( 'page %1$s of %2$s','boozurk' ) . '</span>', $paged, $wp_query->max_num_pages ); ?>
			<?php previous_posts_link( '&raquo;' ); ?>
		</div>
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
