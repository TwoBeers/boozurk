<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php boozurk_hook_before_post_title(); ?>
	<?php $bz_first_img = boozurk_get_first_image(); ?>
	<?php boozurk_featured_title( array( 'alternative' => $bz_first_img ? $bz_first_img['title'] : '' ) ); ?>
	<?php boozurk_hook_after_post_title(); ?>
	<div class="storycontent">
		<?php if ( $bz_first_img ) { ?>
			<a href="<?php echo $bz_first_img['src']; ?>" title="<?php echo $bz_first_img['title']; ?>"><?php echo $bz_first_img['img']; ?></a>
			<br />
			<?php the_excerpt(); ?>
		<?php } else { ?>
			<?php the_content(); ?>
		<?php } ?>
	</div>
	<div class="fixfloat"> </div>
</div>
<?php boozurk_last_comments( get_the_ID() ); ?>
