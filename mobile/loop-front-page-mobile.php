<?php
/**
 * The mobile theme - Front Page template
 *
 * No title, no comments, just page content.
 *
 * @package Boozurk
 * @subpackage mobile
 * @since 1.05
 */
?>

<?php locate_template( array( 'mobile/header-mobile.php' ), true, false ); ?>
<?php if ( have_posts() ) { ?>
	<?php while ( have_posts() ) { 
		the_post(); ?>
		<div <?php post_class( 'tbm-post tbm-padded' ) ?> id="post-<?php the_ID(); ?>">
			<?php the_content(); ?>
			<?php wp_link_pages( 'before=<div class="tbm-pc-navi">' . __( 'Pages', 'boozurk' ) . ':&after=</div>' ); ?>
		</div>
	<?php } ?>
<?php } else { ?>
	<p class="tbm-padded"><?php _e( 'Sorry, no posts matched your criteria.', 'boozurk' );?></p>
<?php } ?>
<?php locate_template( array( 'mobile/footer-mobile.php' ), true, false ); ?>
