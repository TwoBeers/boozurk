<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<div class="storycontent">
		<?php the_content(); ?>
		<div class="fixfloat" style="font-size: 11px; font-style: italic; color: #404040;"><?php the_author(); ?> - <?php the_time( get_option( 'date_format' ) ); ?></div>
	</div>
</div>
<?php boozurk_last_comments( get_the_ID() ); ?>