<?php tha_entry_before(); ?>
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php tha_entry_top(); ?>
	<?php $bz_first_quote = boozurk_get_blockquote(); ?>
	<?php boozurk_hook_post_title_before(); ?>
	<?php
		switch ( boozurk_get_opt( 'boozurk_post_formats_quote_title' ) ) {
			case 'post title':
				boozurk_featured_title();
				break;
			case 'post date':
				boozurk_featured_title( array( 'alternative' => get_the_time( get_option( 'date_format' ) ) ) );
				break;
			case 'short quote excerpt':
				boozurk_featured_title( array( 'alternative' => $bz_first_quote ? '&ldquo;'.$bz_first_quote['quote'].'&rdquo;' : wp_trim_words( $post->post_content, 5, '...' ) ) );
				break;
		}
	?>
	<?php boozurk_hook_post_title_after(); ?>
	<div class="storycontent">
		<?php
			switch ( boozurk_get_opt( 'boozurk_post_formats_quote_content' ) ) {
				case 'content':
					the_content();
					break;
				case 'excerpt':
					the_excerpt();
					break;
			}
		?>
	</div>
	<?php tha_entry_bottom(); ?>
</div>	
<?php tha_entry_after(); ?>
<?php boozurk_last_comments( get_the_ID() ); ?>
