<?php boozurk_hook_entry_before(); ?>
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php boozurk_hook_entry_top(); ?>
	<div class="storycontent">
		<?php the_content(); ?>
		<div class="fixfloat details"><?php the_author(); ?> - <?php the_time( get_option( 'date_format' ) ); ?></div>
	</div>
	<?php boozurk_hook_entry_bottom(); ?>
</div>	
<?php boozurk_hook_entry_after(); ?>
<?php boozurk_last_comments( get_the_ID() ); ?>