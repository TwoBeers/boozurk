<?php global $boozurk_version; ?>

<!-- begin footer -->
			<?php boozurk_hook_before_footer(); ?>
			<div id="footer">
				<?php boozurk_hook_footer(); ?>
				<?php wp_nav_menu( array( 'container_class' => 'bz-menu', 'container_id' => 'secondary2', 'fallback_cb' => false, 'theme_location' => 'secondary2', 'depth' => 1 ) ); ?>
				<?php get_sidebar( 'footer' ); ?>

				<div id="bz-credits">&copy; <?php echo date( 'Y' ); ?>  <strong><?php bloginfo( 'name' ); ?></strong> <?php _e( 'All rights reserved','boozurk' ); ?><?php if ( boozurk_get_opt( 'boozurk_tbcred' ) ) { ?> - <?php echo sprintf( __('Powered by %s and %s','boozurk'), '<a target="_blank" href="http://www.twobeers.net/" title="' . esc_attr( __( 'Visit authors homepage','boozurk' ) ) . ' @ twobeers.net">Boozurk</a>', '<a target="_blank" href="http://wordpress.org/" title="WordPress">WordPress</a>') ; ?><?php } ?><?php if ( boozurk_get_opt( 'boozurk_mobile_css' ) ) echo '<span class="hide_if_print"> - <a rel="nofollow" href="' . home_url() . '?mobile_override=mobile">'. __('Mobile View','boozurk') .'</a></span>'; ?></div>
				<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->

			</div><!-- close footer -->
			<?php boozurk_hook_after_footer(); ?>

		</div><!-- close content -->

		</div><!-- close main -->
		<div id="print-links" class="hide_if_no_print"><a href="<?php the_permalink(); ?>"><?php echo __('Close','boozurk'); ?></a><span class="hide-if-no-js"> | <a href="javascript:window.print()"><?php _e( 'Print','boozurk' ); ?></a></span></div>

		<?php wp_footer(); ?>
	</body>
</html>