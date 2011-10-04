<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php
		$bz_first_quote = boozurk_get_blockquote();
		$bz_post_title = the_title( '','',false );
		if ( $bz_first_quote['quote'] ) {
	?>
	<?php boozurk_hook_before_post_title(); ?>
	<h2 class="storytitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php echo '&#8220;' . $bz_first_quote['quote'] . '&#8221;'; ?></a></h2>
	<?php boozurk_hook_after_post_title(); ?>
	<?php $bz_auth = ( $bz_first_quote['cite'] == '' ) ? false : $bz_first_quote['cite']; ?>
	<div class="storycontent">
		<?php the_content(); ?>
	</div>
	<?php
		}
	?>
	<div class="fixfloat"> </div>
</div>
