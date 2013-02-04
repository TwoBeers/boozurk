<?php tha_entry_before(); ?>
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php tha_entry_top(); ?>
	<?php boozurk_hook_post_title_before(); ?>
	<?php $bz_first_link = boozurk_get_first_link(); ?>
	<?php
		switch ( boozurk_get_opt( 'boozurk_post_formats_link_title' ) ) {
			case 'post title':
				boozurk_featured_title();
				break;
			case 'post date':
				boozurk_featured_title( array( 'alternative' => get_the_time( get_option( 'date_format' ) ) ) );
				break;
			case 'first link text':
				boozurk_featured_title( $bz_first_link ? array( 'alternative' => $bz_first_link['text'] , 'href' => $bz_first_link['href'], 'target' => '_blank', 'title' => $bz_first_link['text'] ) : '' ) ;
				break;
		}
	?>
	<?php boozurk_hook_post_title_after(); ?>
	<div class="storycontent">
		<?php
			switch ( boozurk_get_opt( 'boozurk_post_formats_link_content' ) ) {
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
