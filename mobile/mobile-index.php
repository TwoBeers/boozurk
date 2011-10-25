<?php
	global $boozurk_opt;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta name = "viewport" content = "width = device-width">
		<title><?php
			if ( is_front_page() ) {
				bloginfo( 'name' ); ?> - <?php bloginfo( 'description' );
			} else {
				wp_title( '&laquo;', true, 'right' );
				bloginfo( 'name' );
			}
			?></title>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php wp_get_archives( 'type=monthly&format=link' ); ?>
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>
		<div id="main">
			<div id="head">
				<h1><a href="<?php echo home_url(); ?>/"><?php bloginfo( 'name' ); ?></a></h1>
			</div>
			<?php // search reminder
			if ( is_archive() ) { ?>
				<div class="bz-padded">
					<?php 
						if ( is_category() )	{ $bz_strtype = __( 'Category', 'boozurk' ) . ' : %s'; }
						elseif ( is_tag() )		{ $bz_strtype = __( 'Tag', 'boozurk' ) . ' : %s'; }
						elseif ( is_date() )	{ $bz_strtype = __( 'Archives', 'boozurk' ) . ' : %s'; }
						elseif (is_author()) 	{ $bz_strtype = __( 'Posts by %s', 'boozurk') ; }
					?>
					<?php printf( $bz_strtype, '<strong>' . wp_title( '',false ) . '</strong>'); ?>
				</div>
			<?php } elseif ( is_search() ) { ?>
				<div class="bz-padded">
					<?php printf( __( 'Search results for &#8220;%s&#8221;', 'boozurk' ), '<strong>' . esc_html( get_search_query() ) . '</strong>' ); ?>
				</div>
			<?php } ?>
			<?php if ( have_posts() ) { ?>
				<h2 class="bz-seztit"><span><?php _e( 'Posts', 'boozurk' ); ?></span> <a href="#themecredits">&#8744;</a></h2>
				<ul class="bz-group">
				<?php while ( have_posts() ) {
					the_post(); ?>
					<?php $bz_alter_style = ( !isset($bz_alter_style) || $bz_alter_style == 'bz-odd' ) ? 'bz-even' : 'bz-odd'; ?>
					<li class="<?php echo $bz_alter_style; ?>">
						<a href="<?php the_permalink() ?>" rel="bookmark">
							<span class="bz-format f-<?php echo get_post_format( $post->ID ); ?>"></span>
							<?php 
							$bz_post_title = the_title( '','',false );
							if ( !$bz_post_title ) {
								_e( '(no title)', 'boozurk' );
							} else {
								echo $bz_post_title;
							}
							?><br /><span class="bz-details"><?php the_author(); ?> - <?php the_time( get_option( 'date_format' ) ); ?> - <?php comments_number('(0)', '(1)','(%)'); ?></span>
						</a>
					</li>
				<?php } ?>
				</ul>
				<?php if ( $wp_query->max_num_pages > 1 ) { ?>
					<?php //num of pages
					global $paged;
					if ( !$paged ) { $paged = 1; }
					?>
					<h2 class="bz-seztit"><a href="#head">&#8743;</a> <span><?php printf( __( 'page %1$s of %2$s', 'boozurk' ), $paged, $wp_query->max_num_pages ); ?></span> <a href="#themecredits">&#8744;</a></h2>
					<div class="bz-navi halfsep">
							<span class="bz-halfspan bz-prev"><?php previous_posts_link( __( 'Previous page', 'boozurk' ) ); ?></span>
							<span class="bz-halfspan bz-next"><?php next_posts_link( __( 'Next page', 'boozurk' ) ); ?></span>
							<div class="fixfloat"> </div>
					</div>
				<?php } ?>
			<?php } else { ?>
				<p class="bz-padded"><?php _e( 'Sorry, no posts matched your criteria.', 'boozurk' );?></p>
			<?php } ?>
			<h2 class="bz-seztit"><a href="#head">&#8743;</a> <span><?php _e( 'Search', 'boozurk' ); ?></span> <a href="#themecredits">&#8744;</a></h2>
			<div>
				<form id="search" action="<?php echo home_url(); ?>" method="get">
					<div>
						<input type="text" name="s" id="s" inputmode="predictOn" value="" />
						<input type="submit" name="submit_button" value="Search" />
					</div>
				</form>
			</div>
			<h2 class="bz-seztit"><a href="#head">&#8743;</a> <span><?php _e( 'Pages', 'boozurk' ); ?></span> <a href="#themecredits">&#8744;</a></h2>
			<?php wp_nav_menu( array( 'menu_class' => 'bz-group', 'menu_id' => 'mainmenu', 'fallback_cb' => 'boozurk_pages_menu_mobile', 'theme_location' => 'primary', 'depth' => 1 ) ); //main menu ?>
			<h2 class="bz-seztit"><a href="#head">&#8743;</a> <span>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?></span></h2>
			<p id="themecredits">
				<?php if ( $boozurk_opt['boozurk_tbcred'] == 1 ) { ?>
					Powered by <a href="http://wordpress.org"><strong>WordPress</strong></a> and <a href="http://www.twobeers.net/"><strong>Boozurk</strong></a>.
				<?php } ?><br/>
				<?php wp_loginout(); wp_register(' | ', ''); ?><?php if ( ( !isset( $boozurk_opt['boozurk_mobile_css'] ) || ( $boozurk_opt['boozurk_mobile_css'] == 1) ) ) echo ' | <a href="' . home_url() . '?mobile_override=desktop">'. __('Switch to Desktop View','boozurk') .'</a>'; ?>
			</p>
		</div>
		<?php wp_footer(); ?>
	</body>
</html>