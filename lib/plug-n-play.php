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

		add_action( 'init', array( $this, 'init' ) );

	}


	function init() {

		if ( boozurk_is_mobile() ) return;

		//Sharedaddy
		if ( function_exists( 'sharing_display' ) ) {
			remove_filter( 'the_content'								, 'sharing_display', 19 );
			remove_filter( 'the_excerpt'								, 'sharing_display', 19 );
			add_action( 'boozurk_hook_entry_bottom'						, array( $this, 'sharedaddy_display' ) );
			add_filter( 'boozurk_option_boozurk_plusone'				, '__return_false' );
		}

		//Likes
		if ( class_exists( 'Jetpack_Likes' ) ) {
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
		) );

		if ( class_exists( 'The_Neverending_Home_Page' ) ) {
			add_filter( 'boozurk_option_boozurk_infinite_scroll'	, '__return_false' );
			add_filter( 'infinite_scroll_results'					, array( $this, 'infinite_scroll_encode' ), 11, 1 );
		}

		//Carousel
		if ( class_exists( 'Jetpack_Carousel' ) ) {
			remove_filter( 'post_gallery'							, 'boozurk_gallery_shortcode', 10, 2 );
			add_filter( 'boozurk_option_boozurk_js_thickbox'		, '__return_false' );
		}

	}


	//print the "likes" button after post content
	function likes() {

		echo '<br class="fixfloat">' . apply_filters('boozurk_filter_likes','') . '<br class="fixfloat">';

	}


	//Set the code to be rendered on for calling posts,
	function infinite_scroll_render() {

		if ( isset( $_GET['page'] ) && $page = (int) $_GET['page'] )
			echo '<div class="page-reminder"><span>' . sprintf( __('Page %s','boozurk'), $page ) . '</span></div>';

		get_template_part( 'loop' );
	}


	//encodes html result to UTF8 (jetpack bug?)
	//http://localhost/wordpress/?infinity=scrolling&action=infinite_scroll&page=5&order=DESC
	function infinite_scroll_encode( $results ) {

		$results['html'] = utf8_encode( utf8_decode( $results['html'] ) );

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

		add_filter( 'bbp_after_get_user_subscribe_link_parse_args'	, array( $this, 'user_subscribe_link' ) );
		add_filter( 'bbp_after_get_user_favorites_link_parse_args'	, array( $this, 'user_favorites_link' ) );
		add_filter( 'boozurk_cooltips_selector'						, array( $this, 'user_navigation_tips' ) );
		add_filter( 'boozurk_options_array'							, array( $this, 'extra_options' ), 10, 1 );

		add_action( 'wp_head', array( $this, 'init' ), 999 );
		add_action( 'wp_enqueue_scripts'							, array( $this, 'custom_stylesheet' ), 99 );
		add_action( 'wp_head'										, array( $this, 'dinamic_style' ) );

	}

	/**
	 * Filters and hooks initialization
	 */
	function init() {

		if ( ! ( function_exists( 'is_bbpress' ) && is_bbpress() ) ) return;

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
		$args['plusone'] = 0;

		return $args;

	}

	function extra_options( $coa ) {

		$coa['boozurk_hide_bbpress_title'] = array(
			'group'				=> 'content',
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
			'group'				=> 'content',
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
