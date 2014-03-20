<?php
/**
 * plug-n-play.php
 *
 * Plugins compatibility
 *
 * @package Boozurk
 * @since 2.08
 */


/**
 * Jetpack support
 */
class Boozurk_For_Jetpack {

	function __construct() {

		add_filter( 'boozurk_options_array'		, array( $this, 'extra_options_array' ), 10, 1 );
		add_filter( 'boozurk_options_hierarchy'	, array( $this, 'extra_options_hierarchy' ), 10, 1 );

		add_action( 'init'						, array( $this, 'init' ) );

	}


	function init() {

		if ( ! class_exists( 'Jetpack' ) ) return;

		if ( boozurk_is_mobile() ) return;

		//Sharedaddy
		if ( Jetpack::is_module_active( 'sharedaddy' ) ) {
			remove_filter( 'the_content'								, 'sharing_display', 19 );
			remove_filter( 'the_excerpt'								, 'sharing_display', 19 );
			add_action( 'boozurk_hook_share_links'						, array( $this, 'sharedaddy_display' ) );
			add_filter( 'boozurk_option_boozurk_plusone'				, '__return_false' );
		}

		//Likes
		if ( Jetpack::is_module_active( 'likes' ) ) {
			add_action		( 'boozurk_hook_entry_bottom'			, array( $this, 'likes' ) );
			remove_filter	( 'the_content'							, array( Jetpack_Likes::init(), 'post_likes' ), 30, 1);
			add_filter		( 'boozurk_filter_likes'				, array( Jetpack_Likes::init(), 'post_likes' ), 30, 1);
		}

		//Infinite Scroll
		$type = boozurk_get_opt( 'boozurk_infinite_scroll_type' ) == 'auto' ? 'scroll' : 'click';
		add_theme_support( 'infinite-scroll', array(
			'type'		=> $type,
			'container'	=> 'posts_content',
			'render'	=> array( $this, 'infinite_scroll_render' ),
			'footer'	=> false,
		) );

		if ( Jetpack::is_module_active( 'infinite-scroll' ) ) {
			add_filter( 'boozurk_option_boozurk_infinite_scroll'	, '__return_false' );
			add_filter( 'infinite_scroll_results'					, array( $this, 'infinite_scroll_encode' ), 11, 1 );
		}

		//Carousel
		if ( Jetpack::is_module_active( 'carousel' ) ) {
			remove_filter( 'post_gallery'							, 'boozurk_gallery_shortcode', 10, 2 );
			add_filter( 'boozurk_option_boozurk_js_thickbox'		, '__return_false' );
		}

	}

	function extra_options_array( $coa ) {

		//Infinite Scroll
		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) {

			$coa['boozurk_infinite_scroll_type'] = array(
				'type'			=> 'sel',
				'default'		=> 'manual',
				'description'	=> __( 'infinite pagination', 'boozurk' ),
				'info'			=> __( 'automatically append the next page of posts (via AJAX) to your current page', 'boozurk' ). '<br />' . __( 'auto: when a user scrolls to the bottom - manual: by clicking the link at the end of posts', 'boozurk' ),
				'options'		=> array( 'auto', 'manual' ),
				'options_l10n'	=> array( __( 'auto', 'boozurk' ), __( 'manual', 'boozurk' ) ),
				'req'			=> 'boozurk_jsani',
			);

		}

		return $coa;

	}

	function extra_options_hierarchy( $hierarchy ) {

		//Infinite Scroll
		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) {

			$hierarchy['features']['sub']['javascript']['sub'][] = 'boozurk_infinite_scroll_type';

		}

		return $hierarchy;

	}

	//print the "likes" button after post content
	function likes() {

		echo '<br class="fixfloat">' . apply_filters('boozurk_filter_likes','') . '<br class="fixfloat">';

	}


	//Set the code to be rendered on for calling posts,
	function infinite_scroll_render() {

		get_template_part( 'loop' );

	}


	//encodes html result to UTF8 (useless atm)
	function infinite_scroll_encode( $results ) {

		//$results['html'] = utf8_encode( utf8_decode( $results['html'] ) );

		return $results;
	}


	//print the sharedaddy buttons after post content
	function sharedaddy_display() {

		echo sharing_display();

	}

}

new Boozurk_For_Jetpack;


/**
 * Functions and hooks for bbPress integration
 */
class Boozurk_For_Bbpress {

	function __construct() {

		if ( ! function_exists( 'is_bbpress' ) ) return;

		add_filter( 'bbp_after_get_user_subscribe_link_parse_args'	, array( $this, 'user_subscribe_link' ) );
		add_filter( 'bbp_after_get_user_favorites_link_parse_args'	, array( $this, 'user_favorites_link' ) );
		add_filter( 'boozurk_cooltips_selector'						, array( $this, 'user_navigation_tips' ) );
		add_filter( 'boozurk_options_array'							, array( $this, 'extra_options' ), 10, 1 );

		add_action( 'wp_head'										, array( $this, 'init' ), 999 );
		add_action( 'wp_enqueue_scripts'							, array( $this, 'custom_stylesheet' ), 99 );
		add_action( 'wp_head'										, array( $this, 'dinamic_style' ) );

	}

	/**
	 * Filters and hooks initialization
	 */
	function init() {

		if ( ! is_bbpress() ) return;

		add_filter( 'boozurk_filter_breadcrumb'						, array( $this, 'bbpress_breadcrumb' ) );

		remove_action( 'boozurk_hook_entry_before'					, 'boozurk_print_details' );
		remove_action( 'boozurk_hook_entry_before'					, 'boozurk_single_nav' );
		add_filter( 'boozurk_featured_title_text'					, array( $this, 'show_title' ) );
		add_filter( 'boozurk_navbuttons'							, array( $this, 'navbuttons' ) );
		add_filter( 'boozurk_extrainfo'								, array( $this, 'extrainfo' ) );
		add_filter( 'boozurk_skip_post_widgets_area'				, '__return_true' );

	}

	function custom_stylesheet() {

		wp_enqueue_style( 'boozurk-for-bbpress', get_template_directory_uri() . '/css/bbpress-custom.css', false, boozurk_get_info( 'version' ), 'screen' );

	}

	function dinamic_style(){

?>
	<style type="text/css">
		#bbpress-forums input.ed_button[type="button"]:hover,
		#bbpress-forums input.ed_button[type="button"]:focus,
		#bbpress-forums #bbp-your-profile fieldset input:hover,
		#bbpress-forums #bbp-your-profile fieldset textarea:hover,
		#bbpress-forums #bbp-your-profile fieldset input:focus,
		#bbpress-forums #bbp-your-profile fieldset textarea:focus,
		textarea#bbp_reply_content:hover,
		textarea#bbp_topic_content:hover,
		textarea#bbp_forum_content:hover,
		textarea#bbp_reply_content:focus,
		textarea#bbp_topic_content:focus,
		textarea#bbp_forum_content:focus {
			border-color: <?php echo boozurk_get_opt( 'boozurk_colors_link' ); ?>;
		}
	</style>
<?php

	}

	/**
	 * Filter breadcrumb for bbPress Topics
	 */
	function bbpress_breadcrumb() {

		$args = array(
			'before'	=> '<div id="bz-breadcrumb">',
			'after'		=> '<br class="fixfloat" /></div>',
			'sep'		=> ' <i class="icon-angle-right"></i> ',
			'home_text'	=> '<i class="icon-home"></i>',
		);

		if ( bbp_is_user_home() )
			$args['current_text'] = bbp_get_displayed_user_field( 'display_name' );

		return $breadcrumb = bbp_get_breadcrumb( $args );

	}

	/**
	 * Filter extra infos
	 */
	function extrainfo( $args ) {

		$args['comments'] = 0;
		$args['share'] = 0;

		return $args;

	}

	function extra_options( $coa ) {

		$coa['boozurk_hide_bbpress_title'] = array(
			'type'				=> 'chk',
			'default'			=> 0,
			'description'		=> __( 'in bbPress', 'boozurk' ),
			'info'				=> '',
			'req'				=> '',
			'sub'				=> false
		);

		$coa['boozurk_hide_titles']['sub'][]	= 'boozurk_hide_bbpress_title';

		return $coa;

	}

	function user_subscribe_link( $args ) {

		$args['subscribe'] = '<i title="' . esc_attr( strtolower( $args['subscribe'] ) ) . '" class="icon-check-empty"></i>';
		$args['unsubscribe'] = '<i title="' . esc_attr( strtolower( $args['unsubscribe'] ) ) . '" class="icon-check"></i>';
		$args['before'] = '';

		return $args;

	}

	function user_favorites_link( $args ) {

		$args['favorite'] = '<i title="' . esc_attr( strtolower( $args['favorite'] ) ) . '" class="icon-meh"></i>';
		$args['favorited'] = '<i title="' . esc_attr( strtolower( $args['favorited'] ) ) . '" class="icon-smile"></i>';

		return $args;

	}

	function user_navigation_tips( $selectors ) {

		$selectors[] = '#bbp-user-navigation a';

		return $selectors;

	}

	function show_title( $title ) {

		if ( boozurk_get_opt( 'boozurk_hide_bbpress_title' ) )
			$title = '';

		return $title;

	}

	function navbuttons( $buttons ) {

		foreach ( array( 'comment', 'feed', 'trackback', 'next_prev' ) as $hidden ) {
			$buttons[$hidden] = 0;
		}

		return $buttons;

	}

}

new Boozurk_For_Bbpress;


/**
 * Functions and hooks for BuddyPress integration
 */
class Boozurk_For_BuddyPress {

	function __construct() {

		if ( ! function_exists( 'is_buddypress' ) ) return;

		add_action( 'wp_head'										, array( $this, 'init' ), 999 );
		add_action( 'wp_enqueue_scripts'							, array( $this, 'custom_stylesheet' ), 99 );
		add_action( 'wp_head'										, array( $this, 'dinamic_style' ) );
		add_filter( 'boozurk_options_array'							, array( $this, 'extra_options' ), 10, 1 );

	}

	/**
	 * Filters and hooks initialization
	 */
	function init() {

		if ( ! is_buddypress() ) return;

		add_filter( 'boozurk_option_boozurk_I_like_it'				, '__return_false' );
		add_filter( 'boozurk_option_boozurk_hide_frontpage_title'	, '__return_false' );
		add_filter( 'boozurk_skip_post_widgets_area'				, '__return_true' );
		add_filter( 'boozurk_featured_title_text'					, array( $this, 'show_title' ) );
		add_filter( 'boozurk_navbuttons'							, array( $this, 'navbuttons' ) );
		add_filter( 'boozurk_extrainfo'								, array( $this, 'extrainfo' ) );

	}

	function custom_stylesheet() {

		wp_enqueue_style( 'boozurk-for-buddypress', get_template_directory_uri() . '/css/buddypress-custom.css', false, boozurk_get_info( 'version' ), 'screen' );

	}

	function dinamic_style(){

?>
	<style type="text/css">
		div#buddypress button:hover,
		div#buddypress a.button:hover,
		div#buddypress input[type="submit"]:hover,
		div#buddypress input[type="button"]:hover,
		div#buddypress input[type="reset"]:hover,
		div#buddypress ul.button-nav li a:hover,
		div#buddypress div.generic-button a:hover,
		div#buddypress .comment-reply-link:hover,
		a.bp-title-button:hover,
		div#buddypress button:focus,
		div#buddypress a.button:focus,
		div#buddypress input[type="submit"]:focus,
		div#buddypress input[type="button"]:focus,
		div#buddypress input[type="reset"]:focus,
		div#buddypress ul.button-nav li a:focus,
		div#buddypress div.generic-button a:focus,
		div#buddypress .comment-reply-link:focus,
		a.bp-title-button:focus {
			border-color: <?php echo boozurk_get_opt( 'boozurk_colors_link' ); ?>;
		}
	</style>
<?php

	}

	function extra_options( $coa ) {

		$coa['boozurk_hide_buddypress_title'] = array(
			'type'				=> 'chk',
			'default'			=> 0,
			'description'		=> __( 'in BuddyPress', 'boozurk' ),
			'info'				=> '',
			'req'				=> '',
			'sub'				=> false
		);

		$coa['boozurk_hide_titles']['sub'][]	= 'boozurk_hide_buddypress_title';

		return $coa;

	}

	function show_title( $title ) {

		if ( boozurk_get_opt( 'boozurk_hide_buddypress_title' ) )
			$title = '';

		return $title;

	}

	function navbuttons( $buttons ) {

		foreach ( array( 'comment', 'feed', 'trackback', 'next_prev' ) as $hidden ) {
			$buttons[$hidden] = 0;
		}

		return $buttons;

	}

	/**
	 * Filter extra infos
	 */
	function extrainfo( $args ) {

		$args['comments'] = 0;
		$args['plusone'] = 0;

		return $args;

	}

}

new Boozurk_For_BuddyPress;


/**
 * Functions and hooks for Breadcrumb NavXT integration
 */
class Boozurk_For_NavXT {

	function __construct() {

		add_filter( 'wpseo_breadcrumb_links'	, array( $this, 'remove_home_from_breadcrumb' ) );
		add_filter( 'boozurk_filter_breadcrumb'	, array( $this, 'display_breadcrumb' ) );

	}

	function remove_home_from_breadcrumb( $links ) {

		$on_front = get_option('show_on_front');

		if ( ( $on_front == "page" && is_front_page() ) || ( $on_front == "posts" && is_home() ) ) {
			//nop
		} else {
			if ( $links[0]['url'] == get_home_url() ) { array_shift( $links ); }
		}

		return $links;

	}

	function display_breadcrumb( $output ) {

		if ( function_exists( 'bcn_display' ) ) {

			$output = bcn_display( $return = true );

		}

		return $output;

	}

}

new Boozurk_For_NavXT;


/**
 * Functions and hooks for Yoast Breadcrumbs integration
 */
class Boozurk_For_Yoast_Breadcrumbs {

	function __construct() {

		add_filter( 'boozurk_filter_breadcrumb'	, array( $this, 'display_breadcrumb' ) );

	}

	function display_breadcrumb( $output ) {

		if ( function_exists( 'yoast_breadcrumb' ) ) {

			$_output = yoast_breadcrumb( '', '', false );

			if ( $_output )
				$output = $_output ;

		}

		return $output;

	}

}

new Boozurk_For_Yoast_Breadcrumbs;


/**
 * Functions and hooks for WP Paginate integration
 */
class Boozurk_For_WP_Paginate {

	function __construct() {

		add_action( 'wp_print_styles', array( $this, 'dequeue_style' ), 99 );

		add_filter( 'boozurk_filter_navigation_comments'	, array( $this, 'comments_links' ) );
		add_filter( 'boozurk_filter_navigation_archives'	, array( $this, 'navigate_archives' ) );

	}

	function dequeue_style() {

		wp_dequeue_style( 'wp-paginate' );

	}

	function comments_links( $bool ) {

		if ( function_exists( 'wp_paginate_comments' ) ) {

			wp_paginate_comments();

			$bool = true;

		}

		return $bool;

	}

	function navigate_archives( $bool ) {

		if ( function_exists( 'wp_paginate' ) ) {

			wp_paginate();

			$bool = true;

		}

		return $bool;

	}

}

new Boozurk_For_WP_Paginate;


/**
 * Functions and hooks for WP-Pagenavi integration
 */
class Boozurk_For_WP_Pagenavi {

	function __construct() {

		add_action( 'wp_print_styles', array( $this, 'dequeue_style' ), 99 );

		add_filter( 'boozurk_filter_navigation_archives', array( $this, 'navigate_archives' ) );

	}

	function dequeue_style() {

		wp_dequeue_style( 'wp-pagenavi' );

	}

	function navigate_archives( $bool ) {

		if ( function_exists( 'wp_pagenavi' ) ) {

			wp_pagenavi();

			$bool = true;

		}

		return $bool;

	}

}

new Boozurk_For_WP_Pagenavi;
