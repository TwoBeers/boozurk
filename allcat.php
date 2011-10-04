<?php
	//shows "all categories" page.
?>
<?php get_header(); ?>

<?php boozurk_hook_before_posts(); ?>
<div id="posts_content">
	<div class="post">
		<h2 class="storytitle"><?php _e( 'Categories','boozurk' ); ?></h2>
		<div class="storycontent">
			<ul>
				<?php wp_list_categories( 'title_li=' ); ?>
			</ul>
		</div>
	</div>
</div>
<?php boozurk_hook_after_posts(); ?>

<?php get_footer(); ?>
