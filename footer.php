<?php global $current_user, $boozurk_opt, $boozurk_is_allcat_page, $boozurk_version, $bz_is_mobile_browser, $post; ?>

<!-- begin footer -->
			<?php boozurk_hook_before_footer(); ?>
			<div id="footer">
				<?php boozurk_hook_footer(); ?>
				<?php wp_nav_menu( array( 'container_class' => 'bz-menu', 'container_id' => 'secondary2', 'fallback_cb' => false, 'theme_location' => 'secondary2', 'depth' => 1 ) ); ?>
				<?php get_sidebar( 'footer' ); ?>

				<div id="bz-credits">&copy; <?php echo date( 'Y' ); ?>  <strong><?php bloginfo( 'name' ); ?></strong> <?php _e( 'All rights reserved','boozurk' ); ?> - Powered by <a href="http://wordpress.org/" title="<?php _e( 'Powered by WordPress','boozurk' ); ?>">WordPress</a><?php if ( $boozurk_opt['boozurk_tbcred'] == 1 ) { ?> and <a href="http://www.twobeers.net/" title="<?php _e( 'Visit authors homepage','boozurk' ); ?> @ twobeers.net">Boozurk</a><?php } ?><?php if ( ( !isset( $boozurk_opt['boozurk_mobile_css'] ) || ( $boozurk_opt['boozurk_mobile_css'] == 1) ) ) echo '<span class="hide_if_print"> - <a href="' . home_url() . '?mobile_override=mobile">'. __('Switch to Mobile View','boozurk') .'</a></span>'; ?></div>
				<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->

			</div><!-- close footer -->
			<?php boozurk_hook_after_footer(); ?>
			<?php if ( ! is_active_widget(false, false, 'bz-navbuttons', true) ) { boozurk_navbuttons(); } ?>

<?php get_sidebar(); // show sidebar ?>

		</div><!-- close content -->
		</div><!-- close main -->
		<div id="print-links" class="hide_if_no_print"><a href="<?php the_permalink(); ?>"><?php echo __('Close','boozurk'); ?></a><span class="hide-if-no-js"> | <a href="javascript:window.print()"><?php _e( 'Print','boozurk' ); ?></a></span></div>
		<!-- info: 
			<?php 
				global $boozurk_version; 
				echo ' | WP version - ' . get_bloginfo ( 'version' );
				echo ' | WP language - ' . get_bloginfo ( 'language' );
				if ( phpversion() ) echo ' | PHP v' . phpversion();
				foreach ( $boozurk_opt as $key => $val ) { echo ' | ' . $key . ' - ' . $val; };
			?>
			
		-->

		<?php wp_footer(); ?>
	</body>
</html>