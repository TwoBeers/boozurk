<?php global $boozurk_opt; ?>
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
	<?php boozurk_extrainfo(); ?>
	<?php boozurk_hook_before_post_title(); ?>
	<?php $bz_first_link = boozurk_get_first_link(); ?>
	<?php
		switch ( $boozurk_opt['boozurk_post_formats_link_title'] ) {
			case __( 'post title','boozurk' ):
				boozurk_featured_title();
				break;
			case __( 'post date','boozurk' ):
				boozurk_featured_title( array( 'alternative' => get_the_time( get_option( 'date_format' ) ) ) );
				break;
			case __( 'first link text','boozurk' ):
				boozurk_featured_title( $bz_first_link ? array( 'alternative' => $bz_first_link['text'] , 'target' => '_blank', 'title' => $bz_first_link['text'] ) : '' ) ;
				break;
		}
	?>
	<?php boozurk_hook_after_post_title(); ?>
	<div class="storycontent">
		<?php
			switch ( $boozurk_opt['boozurk_post_formats_link_content'] ) {
				case __( 'content','boozurk' ):
					the_content();
					break;
				case __( 'excerpt','boozurk' ):
					the_excerpt();
					break;
			}
		?>
	</div>
	<div class="fixfloat"> </div>
</div>
<?php boozurk_last_comments( get_the_ID() ); ?>
