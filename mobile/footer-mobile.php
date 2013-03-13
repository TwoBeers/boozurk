<?php
/**
 * The mobile theme - Footer template
 *
 * @package Boozurk
 * @subpackage mobile
 * @since 1.05
 */
?>

			<?php if ( has_nav_menu( 'mobile' ) ) { ?>

				<?php echo apply_filters( 'boozurk_mobile_filter_seztitle', __('Menu','boozurk') ); ?>

				<?php wp_nav_menu( array( 'menu_class' => 'widget-body', 'container_class' => 'widget_pages tbm-padded', 'fallback_cb' => false, 'theme_location' => 'mobile', 'depth' => 1, 'menu_id' => 'mobile-menu' ) ); ?>

			<?php } ?>

			<?php locate_template( array( 'mobile/sidebar-mobile.php' ), true, false ); ?>

			<?php echo apply_filters( 'boozurk_mobile_filter_seztitle', '&copy; ' . date( 'Y' ) . ' - ' . get_bloginfo( 'name' ) ); ?>

			<p id="themecredits">
				<?php echo sprintf( __('Powered by %s and %s','boozurk'), '<a target="_blank" href="http://wordpress.org/" title="WordPress">WordPress</a>', '<a target="_blank" href="http://www.twobeers.net/" title="' . esc_attr( __( 'Visit theme authors homepage','boozurk' ) ) . ' @ twobeers.net">Boozurk</a>') ; ?>
				<br>
				<br>
				<?php wp_loginout(); wp_register( ' | ', '' ); ?><?php echo ' | <a href="' . home_url() . '?mobile_override=desktop">'. __( 'Desktop View', 'boozurk' ) .'</a>'; ?>
			</p>

		</div>

		<?php wp_footer(); ?>

	</body>

</html>