<?php
/**
 * breadcrumb.php
 *
 * The breadcrumb class.
 * Supports Breadcrumb NavXT and Yoast Breadcrumbs plugins.
 *
 * @package Boozurk
 * @since 2.00.1
 */


class Boozurk_Breadcrumb {

	function __construct() {

		add_action( 'boozurk_hook_header_after'				, array( $this, 'display' ) );
		add_action( 'boozurk_hook_breadcrumb_navigation'	, array( $this, 'the_breadcrumb' ) );
		add_action( 'boozurk_hook_breadcrumb_navigation'	, array( $this, 'search_reminder' ) );

	}


	function display() {

?>
	<div id="breadcrumb-wrap">

		<?php $this->get_home(); ?>

		<?php boozurk_hook_breadcrumb_navigation(); ?>

	</div>
<?php

	}


	function the_breadcrumb(){

		echo $this->get_the_breadcrumb();

	}


	function get_home( $echo = true ){

		$output = '';

		$on_front = get_option('show_on_front');

		if ($on_front == "page") {
			$output = '<a class="item-home btn" rel="nofollow" href="' . esc_url( get_permalink( get_option( 'page_on_front' ) ) ) . '"><i class="icon-home"></i></a>';
		} else {
			$output = '<a class="item-home btn" rel="nofollow" href="' . esc_url( home_url() ) . '"><i class="icon-home"></i></a>';
		}

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}

	}


	function search_reminder(){
		global $post;

		$output = '';

		if ( is_category() && category_description() ) { //prints category description

			$output = '<div class="reminder">' . category_description() . '</div>';

		} elseif (is_author()) { //prints author details

			$output = boozurk_author_badge( $post->post_author, 64 );

		} elseif ( is_page() ) { //prints subpages list

			if ( $child_list = $this->multipages() ) $output = '<div class="reminder">' . $child_list . '</div>';

		}

		echo $output;

	}


	// page hierarchy
	function multipages(){
		global $post;

		$args = array(
			'post_type' => 'page',
			'post_parent' => $post->ID,
			'order' => 'ASC',
			'orderby' => 'menu_order',
			'numberposts' => 0,
			'no_found_rows' => true
		);

		$childrens = get_posts( $args ); // retrieve the child pages

		$the_child_list = '';

		if ( $childrens ) {

			foreach ($childrens as $children) {
				$the_child_list[] = '<a href="' . esc_url( get_permalink( $children ) ) . '" title="' . esc_attr( strip_tags( get_the_title( $children ) ) ) . '">' . get_the_title( $children ) . '</a>';
			}

			$the_child_list = implode(' | ' , $the_child_list);

			$the_child_list = '<span class="item-childs"><i class="icon-caret-down"></i></span>' . $the_child_list;

		}

		return $the_child_list;

	}


	// Copied and adapted from WP source
	function get_category_parents( $id, $link = false, $separator = '/', $nicename = false ) {

		$chain = '';

		$parent = get_category( $id );

		if ( is_wp_error( $parent ) )
			return $parent;

		if ( $nicename )
			$name = $parent->slug;
		else
			$name = $parent->cat_name;

		if ( $parent->parent && ( $parent->parent != $parent->term_id ) )
			$chain .= get_category_parents( $parent->parent, true, $separator, $nicename );

		$chain .= '<span>' . $name. '</span>';

		return $chain;

	}


	/*
	the breadcrumb walker
	Based on Yoast Breadcrumbs Plugin (http://yoast.com/wordpress/breadcrumbs/)
	*/
	function get_the_breadcrumb( $before = '<div id="bz-breadcrumb">', $after = '<br class="fixfloat" /></div>' ) {
		global $wp_query, $post;

		$output = apply_filters( 'boozurk_filter_breadcrumb', '' );

		if ( ! empty ( $output ) ) return $output;

		$opt 						= array();
		$opt['home'] 				= __('Home', 'boozurk' );
		$opt['sep'] 				= ' <span class="item-sep"><i class="icon-angle-right"></i></span> ';
		$opt['archiveprefix'] 		= __('Archives for %s', 'boozurk' );
		$opt['searchprefix'] 		= __('Search for "%s"', 'boozurk' );
		$opt['nofollow']			= ' rel="nofollow" ';

		$on_front = get_option('show_on_front');

		$items_found = $wp_query->found_posts ? ' <span class="item-found">(' . $wp_query->found_posts . ')</span>' : '';

		if ( ( $on_front == "page" && is_front_page() ) || ( $on_front == "posts" && is_home() ) ) {

			$output = '<span>' . $opt['home'] . '</span>';

		} elseif ( $on_front == "page" && is_home() ) {

			$output = '<span>' . get_the_title( get_option( 'page_for_posts' ) ) . '</span>';

		} elseif ( !is_page() ) {

			$output = '';

			if ( is_single() && has_category() ) {
				$cats = get_the_category();
				$cat = $cats[0];
				if ( is_object($cat) ) {
					if ($cat->parent != 0) {
						$output .= get_category_parents( $cat->term_id, true, $opt['sep'] );
					} else {
						$output .= '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . $cat->name . '</a>' . $opt['sep']; 
					}
				}
			}

			if ( is_category() ) {

				$cat = intval( get_query_var('cat') );
				$output .= $this->get_category_parents( $cat, false, $opt['sep'] ) . $items_found;

			} elseif ( is_tag() ) {

				$title = single_term_title( '', false );
				$output .= '<span>' . sprintf( $opt['archiveprefix'], $title ) . '</span>' . $items_found;

			} elseif ( is_404() ) {

				$output .= '<span>' . __( 'Page not found', 'boozurk' ) . '</span>';

			} elseif ( is_date() ) {

				if ( is_day() ) {
					$title = get_the_date();
				} else if ( is_month() ) {
					$title = single_month_title( ' ', false );
				} else if ( is_year() ) {
					$title = get_query_var( 'year' );
				}
				$output .= '<span>' . sprintf( $opt['archiveprefix'], $title ) . '</span>' . $items_found;

			} elseif ( is_author() ) {

				$author = get_queried_object();
				$title = $author->display_name;
				$output .= '<span>' . sprintf( $opt['archiveprefix'], $title ) . '</span>' . $items_found;

			} elseif ( is_search() ) {

				$output .= '<span>' . sprintf( $opt['searchprefix'], stripslashes( strip_tags( get_search_query() ) ) ) . '</span>' . $items_found;

			} elseif ( is_attachment() ) {

				if ( $post->post_parent ) {
					$output .= '<a href="' . esc_url( get_permalink( $post->post_parent ) ) . '">' . get_the_title( $post->post_parent ) . '</a>' . $opt['sep'];
				}
				$output .= '<span>' . get_the_title() . '</span>';

			} elseif ( is_tax() ) {

				$taxonomy 	= get_taxonomy ( get_query_var('taxonomy') );
				$term 		= single_term_title( '', false );
				$output .= '<span>' . $taxonomy->label . ': '. $term. '</span>' . $items_found;

			} else {

				if ( get_query_var( 'page' ) ) {
					$output .= '<a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a>' . $opt['sep'] . '<span>' . sprintf ( __( 'Page %s', 'boozurk' ), get_query_var( 'page' ) ) . '</span>';
				} else {
					$output .= '<span>' . get_the_title() . '</span>';
				}

			}

		} else {

			$post = $wp_query->get_queried_object();

			// If this is a top level Page, it's simple to output the breadcrumb
			if ( 0 == $post->post_parent ) {

				if ( get_query_var( 'page' ) ) {
					$output = '<a href="'. esc_url( get_permalink() ) . '">' . get_the_title() . '</a>' . $opt['sep'] . '<span>' . sprintf ( __( 'Page %s', 'boozurk' ), get_query_var( 'page' ) ) . '</span>';
				} else {
					$output = '<span>' . get_the_title() . '</span>';
				}

			} else {

				if ( isset( $post->ancestors ) ) {
					if ( is_array( $post->ancestors ) )
						$ancestors = array_values( $post->ancestors );
					else 
						$ancestors = array( $post->ancestors );
				} else {
					$ancestors = array( $post->post_parent );
				}

				// Reverse the order so it's oldest to newest
				$ancestors = array_reverse( $ancestors );

				// Add the current Page to the ancestors list (as we need it's title too)
				$ancestors[] = $post->ID;

				$links = array();
				foreach ( $ancestors as $ancestor ) {
					$tmp = array();
					$tmp['title'] 	= get_the_title( $ancestor );
					$tmp['url'] 	= get_permalink( $ancestor );
					$tmp['cur']		= false;
					if ($ancestor == $post->ID) {
						$tmp['cur'] = true;
					}
					$links[] = $tmp;
				}

				$output = '';

				foreach ( $links as $link ) {
					if ( ! $link['cur'] ) {
						$output .= '<a href="' . esc_url( $link['url'] ) . '">' . $link['title'] . '</a>';
						$output .= $opt['sep'];
					} else {
						if ( get_query_var( 'page' ) ) {
							$output .= '<a href="' . esc_url( $link['url'] ) . '">' . $link['title'] . '</a>' . $opt['sep'] . '<span>' . sprintf ( __( 'Page %s', 'boozurk' ), get_query_var( 'page' ) ) . '</span>';
						} else {
							$output .= '<span>' . $link['title'] . '</span>';
						}
					}
				}

			}

		}

		if ( get_query_var('paged') )
			$output .= $opt['sep'] . '<span>' . sprintf ( __( 'Page %s', 'boozurk' ), get_query_var( 'paged' ) ) . '</span>';

		return $before . $output . $after;

	}

}

new Boozurk_Breadcrumb;