<?php global $boozurk_opt; ?>
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php $bz_first_quote = boozurk_get_blockquote(); ?>
	<?php boozurk_hook_before_post_title(); ?>
	<?php
		switch ( $boozurk_opt['boozurk_post_formats_quote_title'] ) {
			case 'post title':
				boozurk_featured_title();
				break;
			case 'post date':
				boozurk_featured_title( array( 'alternative' => get_the_time( get_option( 'date_format' ) ) ) );
				break;
			case 'short quote excerpt':
				boozurk_featured_title( array( 'alternative' => $bz_first_quote ? '&ldquo;'.$bz_first_quote['quote'].'&rdquo;' : '' ) );
				break;
		}
	?>
	<?php boozurk_hook_after_post_title(); ?>
	<div class="storycontent">
		<?php
			switch ( $boozurk_opt['boozurk_post_formats_quote_content'] ) {
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
