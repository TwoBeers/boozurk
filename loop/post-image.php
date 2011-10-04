<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php
		$bz_first_img = boozurk_get_first_image();
		$bz_def_vals = array( 'img' => '', 'title' => '', 'src' => '',);
		if ( $bz_first_img ) {
			$bz_first_img = array_merge( $bz_def_vals, $bz_first_img );
	?>
		<?php boozurk_hook_before_post_title(); ?>
		<?php
			$bz_post_title = the_title_attribute( 'echo=0' );
			if ( $bz_post_title ) {
				?><h2 class="storytitle"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo $bz_post_title; ?></a></h2><?php
			}
		?>
		<?php boozurk_hook_after_post_title(); ?>
		<div class="storycontent">
			<a href="<?php echo $bz_first_img['src']; ?>" title="<?php echo $bz_first_img['title']; ?>"><?php echo $bz_first_img['img']; ?></a>
			<br />
			<?php the_excerpt(); ?>
		</div>
	<?php 
		}
	?>
	<div class="fixfloat"> </div>
</div>
