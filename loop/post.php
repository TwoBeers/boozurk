<?php global $boozurk_opt; ?>
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php boozurk_hook_before_post_title(); ?>
	<?php
		switch ( $boozurk_opt['boozurk_post_formats_standard_title'] ) {
			case 'post title':
				boozurk_featured_title();
				break;
			case 'post date':
				boozurk_featured_title( array( 'alternative' => get_the_time( get_option( 'date_format' ) ) ) );
				break;
		}
	?>
	<?php boozurk_hook_after_post_title(); ?>
	<?php boozurk_hook_before_post_content(); ?>
	<div class="storycontent">
		<?php
			switch ( $boozurk_opt['boozurk_post_formats_standard_content'] ) {
				case 'content':
					the_content();
					break;
				case 'excerpt':
					the_excerpt();
					break;
			}
		?>
	</div>
	<?php boozurk_hook_after_post_content(); ?>
	<div class="fixfloat" style="padding-top: 1px;">
			<?php wp_link_pages( 'before=<div class="comment_tools">' . __( 'Pages','boozurk' ) . ':&after=</div>' ); ?>
	</div>
</div>
<?php boozurk_last_comments( get_the_ID() ); ?>