<!-- begin footer -->
			<?php tha_footer_before(); ?>
			<div id="footer">
				<?php tha_footer_top(); ?>

				<?php wp_nav_menu( array( 'container_class' => 'bz-menu', 'container_id' => 'secondary2', 'fallback_cb' => false, 'theme_location' => 'secondary2', 'depth' => 1 ) ); ?>

				<?php get_sidebar( 'footer' ); ?>

				<div id="bz-credits"><?php echo boozurk_get_credits(); ?></div>
				<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->

				<?php tha_footer_bottom(); ?>
			</div><!-- close footer -->
			<?php tha_footer_after(); ?>

			<?php get_sidebar(); // show sidebar ?>

			<?php get_sidebar( 'secondary' ); // show header widgets area ?>

		</div><!-- close content -->

		</div><!-- close main -->
		<div id="print-links" class="hide_if_no_print"><a href="<?php the_permalink(); ?>"><?php echo __('Close','boozurk'); ?></a><span class="hide-if-no-js"> | <a href="javascript:window.print()"><?php _e( 'Print','boozurk' ); ?></a></span></div>

		<?php boozurk_hook_body_bottom(); ?>
		<?php wp_footer(); ?>
	</body>
</html>