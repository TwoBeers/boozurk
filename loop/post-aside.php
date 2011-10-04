<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<div class="storycontent">
		<?php the_content(); ?>
		<span style="font-size: 11px; font-style: italic; color: #404040;"><?php the_author(); ?> - <a href="<?php the_permalink() ?>" rel="bookmark"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
	</div>
</div>