<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<h2 class="storytitle"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<div class="storycontent">
		<?php the_content(); ?>
	</div>
	<div class="fixfloat"> </div>
</div>
