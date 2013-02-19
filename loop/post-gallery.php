<?php boozurk_hook_entry_before(); ?>
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php boozurk_hook_entry_top(); ?>
	<?php boozurk_hook_post_title_before(); ?>
	<?php
		switch ( boozurk_get_opt( 'boozurk_post_formats_gallery_title' ) ) {
			case 'post title':
				boozurk_featured_title();
				break;
			case 'post date':
				boozurk_featured_title( array( 'alternative' => get_the_time( get_option( 'date_format' ) ) ) );
				break;
		}
	?>
	<?php boozurk_hook_post_title_after(); ?>
	<div class="storycontent">
		<?php
			switch ( boozurk_get_opt( 'boozurk_post_formats_gallery_content' ) ) {
				case 'presentation':
					$bz_images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999 ) );
					if ( $bz_images ) {
						$bz_total_images = count( $bz_images );
						$bz_image = array_shift( $bz_images );
						?>
							<div class="gallery-thumb" style="width: <?php echo get_option('medium_size_w'); ?>px;"><?php echo wp_get_attachment_image( $bz_image->ID, 'medium' ); ?></div><!-- .gallery-thumb -->
						<?php 
						$bz_otherimgs = array_slice( $bz_images, 0, 4 );
						foreach ($bz_otherimgs as $bz_image) {
							$bz_image_img_tag = wp_get_attachment_image( $bz_image->ID, array( 75, 75 ) );
							?>
								<div class="gallery-thumb" style="width: <?php echo floor( get_option('thumbnail_size_w')/2 ); ?>px;"><?php echo $bz_image_img_tag; ?></div>
							<?php
						}
					?>
						<p style="float: left; white-space: nowrap;">
							<em><?php printf( _n( 'This gallery contains <a %1$s><strong>%2$s</strong> image</a>', 'This gallery contains <a %1$s><strong>%2$s</strong> images</a>', $bz_total_images, 'boozurk' ),
								'href="' . get_permalink() . '" title="' . esc_attr ( __( 'View gallery', 'boozurk' ) ) . '" rel="bookmark"',
								number_format_i18n( $bz_total_images )
								); ?></em>
						</p>
						<div class="fixfloat"> </div>
					<?php }
					the_excerpt();
					break;
				case 'content':
					the_content();
					break;
				case 'excerpt':
					the_excerpt();
					break;
			}
		?>
	</div>
	<?php boozurk_hook_entry_bottom(); ?>
</div>	
<?php boozurk_hook_entry_after(); ?>
<?php boozurk_last_comments( get_the_ID() ); ?>
