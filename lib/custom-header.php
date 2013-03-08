<?php
/**
 * custom-header.php
 *
 * The custom header support
 *
 * @package Boozurk
 * @since 2.04
 */


add_action( 'after_setup_theme', 'boozurk_custom_header' );

// set up custom colors and header image
if ( !function_exists( 'boozurk_custom_header' ) ) {
	function boozurk_custom_header() {

		$args = array(
			'width'						=> 1000, // Header image width (in pixels)
			'height'					=> 288, // Header image height (in pixels)
			'default-image'				=> '', // Header image default
			'header-text'				=> true, // Header text display default
			'default-text-color'		=> str_replace( '#' , '' , boozurk_get_opt( 'boozurk_colors_link' ) ), // Header text color default
			'wp-head-callback'			=> 'boozurk_header_style_front',
			'admin-head-callback'		=> 'boozurk_header_style_admin',
			'flex-height'				=> true,
			'flex-width'				=> true,
			'admin-preview-callback'	=> 'boozurk_header_preview_admin',
		);
	 
		$args = apply_filters( 'boozurk_custom_header_args', $args );
	 
		if ( function_exists( 'get_custom_header' ) ) {
			add_theme_support( 'custom-header', $args );
		}

		// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
		register_default_headers( array(
			'boozcode' => array(
				'url' => '%s/images/headers/boozcode.png',
				'thumbnail_url' => '%s/images/headers/boozcode_thumb.jpg',
				'description' => 'boozcode'
			)
		) );

	}
}

// included in the admin head
if ( !function_exists( 'boozurk_header_style_admin' ) ) {
	function boozurk_header_style_admin() {
?>

<style type="text/css">
	.appearance_page_custom-header #headimg {
		border: none;
	}
	#headimg h1 {
		margin: 0;
		text-align: center;
		font-family: monospace;
	}
	#headimg h1 a {
		font-size: 2em;
		line-height: 2em;
		text-decoration: none;
		color: #<?php echo get_header_textcolor(); ?>;
	}
	#headimg img {
		height: auto;
		width: 100%;
	}
</style>

<?php
	}
}

// included in the front head
if ( !function_exists( 'boozurk_header_style_front' ) ) {
	function boozurk_header_style_front() {

		$color = get_header_textcolor();
		if ( display_header_text() && $color && $color != 'blank' ) {
?>

<style type="text/css">
	#head a {
		color: #<?php header_textcolor(); ?>
	}
</style>

<?php
		}
	}
}

// included in the admin head
if ( !function_exists( 'boozurk_header_preview_admin' ) ) {
	function boozurk_header_preview_admin() { ?>
		<div id="headimg">
			<?php
			$color = get_header_textcolor();
			$image = get_header_image();
			if ( $color && $color != 'blank' )
				$style = ' style="color:#' . $color . '"';
			else
				$style = ' style="display:none"';
			?>
			<h1><a id="name"<?php echo $style; ?> href="#"><?php bloginfo( 'name' ); ?></a></h1>
			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="header" />
			<?php endif; ?>
		</div>
	<?php }
}
