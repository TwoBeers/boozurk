<?php
	//shows "all categories" page.
?>
<?php get_header(); ?>

<?php tha_content_before(); ?>
<div id="posts_content">
	<?php tha_content_top(); ?>
	<div class="hentry post">
		<h2 class="storytitle"><?php _e( 'Categories','boozurk' ); ?></h2>
		<div class="storycontent">
			<ul>
				<?php wp_list_categories( 'title_li=' ); ?>
			</ul>
		</div>
	</div>
	<?php tha_content_bottom(); ?>
</div>
<?php tha_content_after(); ?>

<?php get_footer(); ?>
