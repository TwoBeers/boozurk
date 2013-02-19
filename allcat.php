<?php
/**
 * allcat.php
 *
 * The template file used to display the whole category list 
 * as a page.
 *
 * @package boozurk
 * @since boozurk 1.00
 */
?>

<?php get_header(); ?>

<?php boozurk_hook_content_before(); ?>
<div id="posts_content">
	<?php boozurk_hook_content_top(); ?>
	<div class="hentry post">
		<h2 class="storytitle"><?php _e( 'Categories','boozurk' ); ?></h2>
		<div class="storycontent">
			<ul>
				<?php wp_list_categories( 'title_li=' ); ?>
			</ul>
		</div>
	</div>
	<?php boozurk_hook_content_bottom(); ?>
</div>
<?php boozurk_hook_content_after(); ?>

<?php get_footer(); ?>
