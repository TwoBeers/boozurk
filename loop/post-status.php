<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo( false ); ?>
	<div class="storycontent">
		<?php echo get_avatar( $post->post_author, 50, $default=get_option('avatar_default'), get_the_author() ); ?>
		<div class="status-subcont">
			<div style="font-size: 11px; font-weight: bold;"><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" title="<?php echo esc_attr( printf( __( 'View all posts by %s', 'boozurk' ), get_the_author() ) ); ?>"><?php echo get_the_author(); ?></a></div>
			<?php the_content(); ?>
			<div class="fixfloat" style="font-size: 11px; color: #aaa;"><?php echo boozurk_friendly_date(); ?></div>
		</div>
	</div>
	<div class="fixfloat"> </div>
</div>