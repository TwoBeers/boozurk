<?php get_header(); ?>

<?php tha_content_before(); ?>
<div id="posts_content">
	<?php tha_content_top(); ?>
	<?php if ( have_posts() ) {
		while ( have_posts() ) {
			the_post(); ?>
			<?php tha_entry_before(); ?>
			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<?php boozurk_extrainfo(); ?>
				<?php tha_entry_top(); ?>
				<div class="storycontent">
					<?php if ( wp_attachment_is_image() ) { ?>
							<div class="att_content">
								<a class="bz-view-full-size" href="<?php echo wp_get_attachment_url(); ?>" title="<?php esc_attr_e( 'View full size','boozurk' ) ;  // link to Full size image ?>" rel="attachment"><?php
								echo wp_get_attachment_image( $post->ID, array( apply_filters( 'boozurk_attachment_width', 1000 ), apply_filters( 'boozurk_attachment_height', 1000 ) ), false, array( 'class' => 'size-full') );
								?></a>
								<?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?>
							</div>
							<?php if ( !empty( $post->post_content ) ) the_content(); ?>
					<?php } else { ?>
							<?php echo wp_get_attachment_link( $post->ID,'thumbnail', 0,1 ); ?> 
							<p><?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?></p>
					<?php } ?>
				</div>
				<?php tha_entry_bottom(); ?>
			</div>	
			<?php tha_entry_after(); ?>
			
			<?php comments_template(); // Get wp-comments.php template ?>

		<?php	} //end while
	} else {?>
		
		<div class="post"><p><?php _e( 'Sorry, no posts matched your criteria.','boozurk' ); ?></p></div>
		
	<?php } ?>

	<?php tha_content_bottom(); ?>
</div>
<?php tha_content_after(); ?>

<?php get_footer(); ?>
