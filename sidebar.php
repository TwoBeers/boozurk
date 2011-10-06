<!-- begin sidebar -->
<div id="sidebarsx">
	<?php boozurk_hook_before_right_sidebar_content(); ?>
	<?php 	/* Widgetized sidebar, if you have the plugin installed. */
	if ( !dynamic_sidebar( 'primary-widget-area' ) ) { ?>

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
	<?php boozurk_hook_after_right_sidebar_content(); ?>

</div>
<!-- end sidebar -->