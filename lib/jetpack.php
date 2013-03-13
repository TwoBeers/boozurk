<?php
/**
 * jetpack.php
 *
 * Jetpack support
 *
 * @package Boozurk
 * @since 2.05
 */


add_action( 'init', 'boozurk_for_jetpack_init' );


function boozurk_for_jetpack_init() {

	if ( boozurk_is_mobile() ) return;

	//Sharedaddy
	if ( function_exists( 'sharing_display' ) ) {
		remove_filter( 'the_content', 'sharing_display', 19 );
		remove_filter( 'the_excerpt', 'sharing_display', 19 );
		add_action( 'boozurk_hook_entry_bottom', 'boozurk_for_jetpack_sharedaddy_display' );
		add_filter( 'boozurk_option_override', 'boozurk_for_jetpack_sharedaddy_set', 10, 2 );
	}

	//Likes
	if ( class_exists( 'Jetpack_Likes' ) ) {
		add_filter( 'wpl_is_index_disabled', '__return_false' );
	}

	//Infinite Scroll
	$type = boozurk_get_opt( 'boozurk_infinite_scroll_type' ) == 'auto' ? 'scroll' : 'click';
	add_theme_support( 'infinite-scroll', array(
		'type'		=> $type,
		'container'	=> 'posts_content',
		'render'	=> 'boozurk_for_jetpack_infinite_scroll',
	) );

	if ( class_exists( 'The_Neverending_Home_Page' ) ) {
		add_filter( 'boozurk_option_override', 'boozurk_for_jetpack_infinite_scroll_set', 10, 2 );
		add_filter( 'infinite_scroll_results', 'boozurk_for_jetpack_infinite_scroll_encode', 11, 1 );
	}

	//Carousel
	if ( class_exists( 'Jetpack_Carousel' ) ) {
		remove_filter( 'post_gallery', 'boozurk_gallery_shortcode', 10, 2 );
		add_filter( 'boozurk_option_override', 'boozurk_for_jetpack_carousel_set', 10, 2 );
	}

}


//Set the code to be rendered on for calling posts,
function boozurk_for_jetpack_infinite_scroll() {

	if ( isset( $_GET['page'] ) && $page = (int) $_GET['page'] )
		echo '<div class="page-reminder"><span>' . sprintf( __('Page %s','boozurk'), $page ) . '</span></div>';

	get_template_part( 'loop' );
}


//skip the built-in infinite-scroll feature
function boozurk_for_jetpack_infinite_scroll_set( $value, $name ) {

	if ( 'boozurk_infinite_scroll' === $name ) return false;

	return $value;

}


//encodes html result to UTF8 (jetpack bug?)
//http://localhost/wordpress/?infinity=scrolling&action=infinite_scroll&page=5&order=DESC
function boozurk_for_jetpack_infinite_scroll_encode( $results ) {

	$results['html'] = utf8_encode( utf8_decode( $results['html'] ) );

	return $results;
}


//skip the Google+ option
function boozurk_for_jetpack_sharedaddy_set( $value, $name ) {

	if ( 'boozurk_plusone' === $name ) return false;

	return $value;

}


//print the sharedaddy buttons after post content
function boozurk_for_jetpack_sharedaddy_display() {

	echo sharing_display();

}


//skip the thickbox js module
function boozurk_for_jetpack_carousel_set( $value, $name ) {

	if ( 'boozurk_js_thickbox' === $name ) return false;

	return $value;

}
