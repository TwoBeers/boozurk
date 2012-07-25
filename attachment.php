<?php get_header(); ?>

<?php boozurk_hook_before_posts(); ?>
<div id="posts_content">
	<?php if ( have_posts() ) {
		global $boozurk_opt;
		while ( have_posts() ) {
			the_post(); ?>
			<?php boozurk_hook_before_post(); ?>
			<?php if ( wp_attachment_is_image() ) {
				$bz_attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
				foreach ( $bz_attachments as $bz_k => $bz_attachment ) {
					if ( $bz_attachment->ID == $post->ID )
						break;
				}
				$bz_nextk = $bz_k + 1;
				$bz_prevk = $bz_k - 1;
				?>
				<div class="img-navi">
					<?php if ( isset( $bz_attachments[ $bz_prevk ] ) ) { ?>
						<a class="img-navi-prev" title="" href="<?php echo get_attachment_link( $bz_attachments[ $bz_prevk ]->ID ); ?>"><?php echo wp_get_attachment_image( $bz_attachments[ $bz_prevk ]->ID, array( 70, 70 ) ); ?></a>
					<?php } ?>
					<?php if ( isset( $bz_attachments[ $bz_nextk ] ) ) { ?>
						<a class="img-navi-next" title="" href="<?php echo get_attachment_link( $bz_attachments[ $bz_nextk ]->ID ); ?>"><?php echo wp_get_attachment_image( $bz_attachments[ $bz_nextk ]->ID, array( 70, 70 ) ); ?></a>
					<?php } ?>
				</div>
			<?php } ?>
			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<?php boozurk_extrainfo(); ?>
				<?php boozurk_hook_before_post_content(); ?>
				<div class="storycontent">
					<?php if ( wp_attachment_is_image() ) { ?>
							<div class="att_content">
								<a class="bz-view-full-size" href="<?php echo wp_get_attachment_url(); ?>" title="<?php esc_attr_e( 'View full size','boozurk' ) ;  // link to Full size image ?>" rel="attachment"><?php
								$bz_attachment_width  = apply_filters( 'boozurk_attachment_size', 1000 );
								$bz_attachment_height = apply_filters( 'boozurk_attachment_height', 1000 );
								echo wp_get_attachment_image( $post->ID, array( $bz_attachment_width, $bz_attachment_height ), false, array( 'class' => 'size-full') ); // filterable image width with, essentially, no limit for image height. 
								?></a>
								<?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?>
							</div>
							<?php if ( !empty( $post->post_content ) ) the_content(); ?>
					<?php } else { ?>
							<?php echo wp_get_attachment_link( $post->ID,'thumbnail', 0,1 ); ?> 
							<p><?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?></p>
					<?php } ?>
				</div>
				<?php boozurk_hook_after_post_content_single(); ?>
				<?php boozurk_hook_after_post_content(); ?>
				<div class="fixfloat"></div>
				<?php $bz_tmptrackback = get_trackback_url(); ?>
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
		
		<div class="post"><p><?php _e( 'Sorry, no posts matched your criteria.','boozurk' ); ?></p></div>
		
	<?php } ?>

</div>	
<?php boozurk_hook_after_posts(); ?>

<?php get_footer(); ?>
