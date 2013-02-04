<?php tha_entry_before(); ?>
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php tha_entry_top(); ?>
	<h2 class="storytitle"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<div class="storycontent">
		<?php the_content(); ?>
	</div>
	<?php tha_entry_bottom(); ?>
</div>	
<?php tha_entry_after(); ?>
