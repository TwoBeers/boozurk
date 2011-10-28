<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php $bz_first_quote = boozurk_get_blockquote(); ?>
	<?php boozurk_hook_before_post_title(); ?>
	<?php boozurk_featured_title( array( 'alternative' => $bz_first_quote ? $bz_first_quote['quote'] : '' ) ); ?>
	<?php boozurk_hook_after_post_title(); ?>
	<div class="storycontent">
		<?php the_content(); ?>
	</div>
	<div class="fixfloat"> </div>
</div>
<?php boozurk_last_comments( get_the_ID() ); ?>
