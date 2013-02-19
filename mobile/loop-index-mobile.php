<?php
/**
 * The mobile theme - Index/Arcive/Search template
 *
 * @package boozurk
 * @subpackage mobile
 * @since boozurk 1.05
 */
?>

<?php locate_template( array( 'mobile/header-mobile.php' ), true, false ); ?>
<?php
	$sw_strtype = __( 'Posts', 'boozurk' );
	if ( is_category() )	{ $sw_strtype = sprintf( __( 'Category', 'boozurk' ) . ' : %s', wp_title( '',false ) ); }
	elseif ( is_tag() )		{ $sw_strtype = sprintf( __( 'Tag', 'boozurk' ) . ' : %s', wp_title( '',false ) ); }
	elseif ( is_date() )	{ $sw_strtype = sprintf( __( 'Archives', 'boozurk' ) . ' : %s', wp_title( '',false ) ); }
	elseif ( is_author() ) 	{ $sw_strtype = sprintf( __( 'Posts by %s', 'boozurk'), wp_title( '',false ) ); }
	elseif ( is_search() )	{ $sw_strtype = sprintf( __( 'Search results for &#8220;%s&#8221;', 'boozurk' ), esc_html( get_search_query() ) ); }
?>
<?php if ( have_posts() ) { ?>
	<?php echo boozurk_mobile_seztitle( 'before' ) . $sw_strtype . boozurk_mobile_seztitle( 'after' ); ?>
	<ul class="tbm-group">
	<?php while ( have_posts() ) {
		the_post(); ?>
		<?php $tbm_alter_style = ( !isset( $tbm_alter_style ) || $tbm_alter_style == 'tbm-odd' ) ? 'tbm-even' : 'tbm-odd'; ?>
		<li class="<?php echo $tbm_alter_style; ?>">
			<a href="<?php the_permalink() ?>" rel="bookmark">
				<span class="tb-thumb-format <?php echo get_post_format( $post->ID ); ?>"></span>
				<?php the_title(); ?>
				<br />
				<span class="tbm-details"><?php the_author(); ?> - <?php the_time( get_option( 'date_format' ) ); ?> - <?php comments_number('(0)', '(1)','(%)'); ?></span>
			</a>
		</li>
	<?php } ?>
	</ul>
	<?php if ( $wp_query->max_num_pages > 1 ) { ?>
		<?php //num of pages
		global $paged;
		if ( !$paged ) { $paged = 1; }
		?>
		<?php printf( boozurk_mobile_seztitle( 'before' ) . __( 'page %1$s of %2$s', 'boozurk' ) . boozurk_mobile_seztitle( 'after' ), $paged, $wp_query->max_num_pages ); ?>
		<div class="tbm-index-navi">
			<?php if ( function_exists( 'wp_pagenavi' ) ) { ?>
				<?php wp_pagenavi(); ?>
			<?php } else { ?>
						<?php previous_posts_link( __( 'Previous page', 'boozurk' ) ); ?>
						<?php next_posts_link( __( 'Next page', 'boozurk' ) ); ?>
			<?php } ?>
		</div>
	<?php } ?>
<?php } else { ?>
		<?php echo boozurk_mobile_seztitle( 'before' ) . $sw_strtype . boozurk_mobile_seztitle( 'after' ); ?>
		<p class="tbm-padded"><?php _e( 'Sorry, no posts matched your criteria.', 'boozurk' );?></p>
<?php } ?>
<?php locate_template( array( 'mobile/footer-mobile.php' ), true, false ); ?>
