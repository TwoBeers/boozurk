<?php
/**
 * sidebar.php
 *
 * Template part file that contains the default sidebar content
 *
 * @package boozurk
 * @since boozurk 1.00
 */
?>

<!-- begin primary sidebar -->

<div class="sidebar<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_primary' ) ?>" id="sidebar-primary">

	<div class="primary top-fade<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_primary' ) ?>"></div>

	<div class="inner">
		<?php boozurk_hook_sidebar_top(); ?>
		<?php boozurk_hook_primary_sidebar_top(); ?>
		<?php if ( !dynamic_sidebar( 'primary-widget-area' ) ) { ?>

			<div id="bz-search">
				<?php get_search_form(); ?>
			</div>
			<div id="w_meta" class="widget"><div class="w_title"><?php _e( 'Meta','boozurk' ); ?></div>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</div>

			<div id="w_pages" class="widget"><div class="w_title"><?php _e( 'Pages','boozurk' ); ?></div><ul><?php wp_list_pages( 'title_li=' ); ?></ul></div>
			<div id="w_bookmarks" class="widget"><div class="w_title"><?php _e( 'Blogroll','boozurk' ); ?></div><ul><?php wp_list_bookmarks( 'title_li=0&categorize=0' ); ?></ul></div>
			<div id="w_categories" class="widget"><div class="w_title"><?php _e( 'Categories','boozurk' ); ?></div><ul><?php wp_list_categories( 'title_li=' ); ?></ul></div>

			<div id="w_archives" class="widget"><div class="w_title"><?php _e( 'Archives','boozurk' ); ?></div>
				<ul>
				<?php wp_get_archives( 'type=monthly' ); ?>
				</ul>
			</div>

		<?php } ?>

		<div class="fixfloat"> </div>
		<?php boozurk_hook_primary_sidebar_bottom(); ?>
		<?php boozurk_hook_sidebar_bottom(); ?>
	</div>

	<div class="primary bottom-fade<?php echo ' ' . boozurk_get_opt( 'boozurk_sidebar_primary' ) ?>"></div>

</div>

<!-- end primary sidebar -->