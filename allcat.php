<?php
/**
 * Template Name: List of Categories
 *
 * allcat.php
 *
 * The template file used to display the whole category list 
 * as a page.
 *
 * @package Boozurk
 * @since 1.00
 */


get_header(); ?>

<?php boozurk_hook_content_before(); ?>

<div id="posts_content">

	<?php boozurk_hook_content_top(); ?>

	<?php if ( have_posts() && ! boozurk_is_allcat() ) {

		while ( have_posts() ) {

			the_post(); ?>

			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

				<div class="post_meta_container"><span class="pmb_format btn"><i class="icon-folder-close"></i></span></div>

				<?php boozurk_featured_title(); ?>

				<div class="storycontent">

					<?php the_content(); ?>

					<ul>
						<?php wp_list_categories( 'title_li=' ); ?>
					</ul>

				</div>

			</div>

		<?php } //end while ?>

	<?php } else { ?>

		<div class="hentry post post-element">

			<div class="post_meta_container"><span class="pmb_format btn"><i class="icon-folder-close"></i></span></div>

			<h2 class="storytitle"><?php _e( 'Categories','boozurk' ); ?></h2>

			<div class="storycontent">
				<ul>
					<?php wp_list_categories( 'title_li=' ); ?>
				</ul>
			</div>

		</div>

	<?php } //endif ?>

	<?php boozurk_hook_content_bottom(); ?>

</div>

<?php boozurk_hook_content_after(); ?>

<?php get_footer(); ?>
