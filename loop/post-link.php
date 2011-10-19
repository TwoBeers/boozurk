<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php
		$bz_post_title = the_title( '','',false );
		
		$bz_first_link = boozurk_get_first_link();
		$bz_def_vals = array( 'anchor' => '', 'title' => '', 'href' => '',);
		if ( $bz_first_link ) {
			$bz_first_link = array_merge( $bz_def_vals, $bz_first_link );
			if ( $bz_first_link['title'] != '' )
				$bz_post_title = $bz_first_link['title'];
	?>
		<?php boozurk_hook_before_post_title(); ?>
		<h2 class="storytitle"><a target="_blank" href="<?php echo $bz_first_link['href']; ?>" rel="bookmark"><?php echo $bz_post_title; ?></a></h2>
		<?php boozurk_hook_after_post_title(); ?>
	<?php 
		} 
	?>
	<div class="storycontent">
		<?php the_excerpt(); ?>
	</div>
	<div class="fixfloat"> </div>
</div>
<?php boozurk_last_comments( get_the_ID() ); ?>
