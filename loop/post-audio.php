<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php boozurk_hook_before_post_title(); ?>
	<?php
		$bz_post_title = the_title_attribute( 'echo=0' );
		if ( $bz_post_title ) {
			?><h2 class="storytitle"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo $bz_post_title; ?></a></h2><?php
		}
	?>
	<?php boozurk_hook_after_post_title(); ?>
	<div class="storycontent">
		<?php //boozurk_add_audio_player(); ?>
		<?php the_content(); ?>
	</div>
	<div class="fixfloat"> </div>
	<?php boozurk_last_comments( get_the_ID() ); ?>
</div>
