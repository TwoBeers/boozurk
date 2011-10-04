<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php
		$bz_post_title = the_title_attribute( 'echo=0' );
		if ( $bz_post_title ) {
			?><h2 class="storytitle"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo $bz_post_title; ?></a></h2><?php
		}
	?>
	<div class="storycontent">
		<?php the_content(); ?>
	</div>
	<div class="fixfloat"> </div>
</div>
