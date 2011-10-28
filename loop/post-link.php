<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php boozurk_hook_before_post_title(); ?>
	<?php $bz_first_link = boozurk_get_first_link(); ?>
	<?php boozurk_featured_title( $bz_first_link ? array( 'alternative' => $bz_first_link['text'] , 'target' => '_blank', 'title' => $bz_first_link['text'] ) ) ); ?>
	<?php boozurk_hook_after_post_title(); ?>
	<div class="storycontent">
		<?php the_excerpt(); ?>
	</div>
	<div class="fixfloat"> </div>
</div>
<?php boozurk_last_comments( get_the_ID() ); ?>
