<?php
/**
 * post-gallery.php
 *
 * Template part file that contains the Gallery Format entry
 * 
 * @package Boozurk
 * @since 1.00
 */
?>

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
					if ( ! boozurk_gallery_preview() ) the_content();
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
