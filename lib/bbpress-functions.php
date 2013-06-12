<?php
/**
 * Functions and hooks for bbPress integration
 */
 
class Boozurk_Bbpress {

	function __construct() {

		add_filter( 'bbp_after_get_user_subscribe_link_parse_args'	, array( $this, 'user_subscribe_link' ) );
		add_filter( 'bbp_after_get_user_favorites_link_parse_args'	, array( $this, 'user_favorites_link' ) );
		add_filter( 'boozurk_cooltips_selector'						, array( $this, 'user_navigation_tips' ) );

		add_action( 'wp_head', array( $this, 'init' ), 999 );

	}

	/**
	 * Filters and hooks initialization
	 */
	function init() {

		if ( ! ( function_exists( 'is_bbpress' ) && is_bbpress() ) ) return;

		add_filter( 'boozurk_filter_breadcrumb'						, array( $this, 'bbpress_breadcrumb' ) );
		add_filter( 'boozurk_extrainfo'								, array( $this, 'extrainfo' ) );

		remove_action( 'boozurk_hook_entry_before'					, 'boozurk_print_details' );
		remove_action( 'boozurk_hook_entry_before'					, 'boozurk_single_nav' );

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

		if ( bbp_is_user_home() )
			$args['plusone'] = 0;

		return $args;

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

}

new Boozurk_Bbpress;
