<?php global $boozurk_opt; ?>
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php boozurk_hook_before_post_title(); ?>
	<?php $bz_first_img = boozurk_get_first_image(); ?>
	<?php
		switch ( $boozurk_opt['boozurk_post_formats_image_title'] ) {
			case 'post title':
				boozurk_featured_title();
				break;
			case 'post date':
				boozurk_featured_title( array( 'alternative' => get_the_time( get_option( 'date_format' ) ) ) );
				break;
			case 'first image title':
				boozurk_featured_title( array( 'alternative' => $bz_first_img ? $bz_first_img['title'] : '' ) );
				break;
		}
	?>
	<?php boozurk_hook_after_post_title(); ?>
	<div class="storycontent">
		<?php
			switch ( $boozurk_opt['boozurk_post_formats_image_content'] ) {
				case 'first image':
					if ( $bz_first_img ) {
						?><a href="<?php echo $bz_first_img['src']; ?>" title="<?php echo $bz_first_img['title']; ?>"><?php echo $bz_first_img['img']; ?></a><br /><?php
						the_excerpt();
					} else {
						the_content();
					}
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
	<div class="fixfloat"> </div>
</div>
<?php boozurk_last_comments( get_the_ID() ); ?>
