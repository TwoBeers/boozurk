<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php boozurk_hook_before_post_title(); ?>
	<?php boozurk_featured_title(); ?>
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
<?php boozurk_last_comments( get_the_ID() ); ?>