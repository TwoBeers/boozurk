<?php
/**
 * footer.php
 *
 * Template part file that contains the site footer and
 * closing HTML body elements
 *
 * @package Boozurk
 * @since 1.00
 */
?>

<!-- begin footer -->
			</div><!-- close content -->

			<div id="sidebars">

				<?php get_sidebar(); // show primary widgets area ?>

				<?php get_sidebar( 'secondary' ); // show secondary widgets area ?>

			</div>

			<?php boozurk_hook_footer_before(); ?>

			<div id="footer">

				<?php boozurk_hook_footer_top(); ?>

				<?php get_sidebar( 'footer' ); // show footer widgets area ?>

				<div id="bz-credits">

					<?php echo boozurk_get_credits(); ?>

				</div>

				<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->

				<?php boozurk_hook_footer_bottom(); ?>

			</div><!-- close footer -->

			<?php boozurk_hook_footer_after(); ?>

		</div><!-- close main -->

		<div id="print-links" class="hide_if_no_print"><a href="<?php the_permalink(); ?>"><?php echo __('Close','boozurk'); ?></a><span class="hide-if-no-js"> | <a href="javascript:window.print()"><?php _e( 'Print','boozurk' ); ?></a></span></div>

		<?php boozurk_hook_body_bottom(); ?>

		<?php wp_footer(); ?>

	</body>

</html>