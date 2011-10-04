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
	<?php boozurk_hook_before_post_content(); ?>
	<div class="storycontent">
		<?php the_content(); ?>
	</div>
	<?php boozurk_hook_after_post_content(); ?>
	<div class="fixfloat" style="padding-top: 1px;">
			<?php wp_link_pages( 'before=<div class="comment_tools">' . __( 'Pages','boozurk' ) . ':&after=</div>' ); ?>
	</div>
</div>
