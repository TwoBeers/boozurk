<?php
/**
 * breadcrumb.php
 *
 * The breadcrumb code.
 * Supports Breadcrumb NavXT plugin.
 *
 * @package boozurk
 * @since boozurk 2.00.1
 */


// the breadcrumb
if (!function_exists('boozurk_breadcrumb')) {
	function boozurk_breadcrumb(){
		?>
		<div id="bz-breadcrumb-wrap">
			<?php if(function_exists('bcn_display_list')) {?>
				<div id="navxt-crumbs"><?php bcn_display(); ?></div>
			<?php } else { ?>
				<div id="bz-breadcrumb">
				<?php echo boozurk_get_the_breadcrumb(); // show breadcrumb ?>
				<div class="fixfloat"></div>
				</div>
			<?php } ?>
			<?php boozurk_search_reminder(); ?>
		</div>
		<?php
	}
}

// search reminder
if (!function_exists('boozurk_search_reminder')) {
	function boozurk_search_reminder(){
		if ( is_category() ) { //prints category description
			if ( category_description() ) {
				echo '<div class="bz-breadcrumb-reminder">' . category_description() . '</div>';
			}
		} elseif (is_author()) { //prints author details
			echo '<div class="bz-breadcrumb-reminder">';
			boozurk_post_details( array( 'date' => 0, 'tags' => 0, 'categories' => 0, 'avatar_size' => 64 ) );
			echo '</div>';
		} elseif ( is_page() ) { //prints subpages list
			boozurk_multipages();
		}
	}
}

// page hierarchy
if ( !function_exists( 'boozurk_multipages' ) ) {
	function boozurk_multipages(){
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
		$has_herarchy = false;
		if ( $childrens ) {
			$the_child_list = '';
			foreach ($childrens as $children) {
				$the_child_list[] = '<a href="' . get_permalink( $children ) . '" title="' . esc_attr( strip_tags( get_the_title( $children ) ) ) . '">' . get_the_title( $children ) . '</a>';
			}
			$the_child_list = implode(' | ' , $the_child_list);
			echo '<div class="bz-breadcrumb-reminder"><span class="bz-breadcrumb-childs">&nbsp;</span>' . $the_child_list . '</div>'; // echoes the childs
			$has_herarchy = true;
		}
		return $has_herarchy;
	}
}

/*
Based on Yoast Breadcrumbs Plugin (http://yoast.com/wordpress/breadcrumbs/)
*/
if ( !function_exists( 'boozurk_get_the_breadcrumb' ) ) {
	function boozurk_get_the_breadcrumb() {
		global $wp_query, $post;
		
		$opt 						= array();
		$opt['home'] 				= __('Home', 'boozurk' );
		$opt['sep'] 				= '<span class="bz-breadcrumb-sep">&nbsp;</span>';
		$opt['archiveprefix'] 		=  __('Archives for %s', 'boozurk' );
		$opt['searchprefix'] 		=  __('Search for "%s"', 'boozurk' );

		$nofollow = ' rel="nofollow" ';
		
		if (!function_exists('boozurk_get_category_parents')) {
			// Copied and adapted from WP source
			function boozurk_get_category_parents($id, $link = FALSE, $separator = '/', $nicename = FALSE){
				$chain = '';
				$parent = &get_category($id);
				if ( is_wp_error( $parent ) )
				   return $parent;

				if ( $nicename )
				   $name = $parent->slug;
				else
				   $name = $parent->cat_name;

				if ( $parent->parent && ($parent->parent != $parent->term_id) )
				   $chain .= get_category_parents($parent->parent, true, $separator, $nicename);

				$chain .= $name;
				return $chain;
			}
		}
		
		$on_front = get_option('show_on_front');
		if ($on_front == "page") {
			$homelink = '<a class="bz-breadcrumb-home"'.$nofollow.'href="'.get_permalink(get_option('page_on_front')).'">&nbsp;</a>';
			$bloglink = $homelink.' '.$opt['sep'].' <a href="'.get_permalink(get_option('page_for_posts')).'">'.get_the_title( get_option( 'page_for_posts' ) ).'</a>';
		} else {
			$homelink = '<a class="bz-breadcrumb-home"'.$nofollow.'href="'.home_url().'">&nbsp;</a>';
			$bloglink = $homelink;
		}
			
		if ( ($on_front == "page" && is_front_page()) || ($on_front == "posts" && is_home()) ) {
			$output = $homelink.' '.$opt['sep'].' '.'<span>'.$opt['home'].'</span>';
		} elseif ( $on_front == "page" && is_home() ) {
			$output = $homelink.' '.$opt['sep'].' '.'<span>'.get_the_title( get_option( 'page_for_posts' ) ).'</span>';
		} elseif ( !is_page() ) {
			$output = $bloglink.' '.$opt['sep'].' ';
			if ( is_single() && has_category() ) {
				$cats = get_the_category();
				$cat = $cats[0];
				if ( is_object($cat) ) {
					if ($cat->parent != 0) {
						$output .= get_category_parents($cat->term_id, true, " ".$opt['sep']." ");
					} else {
						$output .= '<a href="'.get_category_link($cat->term_id).'">'.$cat->name.'</a> '.$opt['sep'].' '; 
					}
				}
			}
			if ( is_category() ) {
				$cat = intval( get_query_var('cat') );
				$output .= '<span>'.boozurk_get_category_parents($cat, false, " ".$opt['sep']." ").'</span>'.' <span class="bz-breadcrumb-found">('.$wp_query->found_posts.')</span>';
			} elseif ( is_tag() ) {
				$title = single_term_title( '', false );
				$output .= '<span>'.sprintf( $opt['archiveprefix'], $title ).'</span>'.' <span class="bz-breadcrumb-found">('.$wp_query->found_posts.')</span>';
			} elseif ( is_404() ) {
				$output .= '<span>'.__( 'Page not found', 'boozurk' ).'</span>';
			} elseif ( is_date() ) {
				if ( is_day() ) {
					$title = get_the_date();
				} else if ( is_month() ) {
					$title = single_month_title( ' ', false );
				} else if ( is_year() ) {
					$title = get_query_var( 'year' );
				}
				$output .= '<span>'.sprintf( $opt['archiveprefix'], $title ).'</span>'.' <span class="bz-breadcrumb-found">('.$wp_query->found_posts.')</span>';
			} elseif ( is_author() ) { 
				$author = get_queried_object();
				$title = $author->display_name;
				$output .= '<span>'.sprintf( $opt['archiveprefix'], $title ).'</span>'.' <span class="bz-breadcrumb-found">('.$wp_query->found_posts.')</span>';
			} elseif ( is_search() ) {
				$output .= '<span>'.sprintf( $opt['searchprefix'], stripslashes(strip_tags(get_search_query())) ).'</span>'.' <span class="bz-breadcrumb-found">('.$wp_query->found_posts.')</span>';
			} elseif ( is_attachment() ) {
				if ( $post->post_parent ) {
					$output .= '<a href="'.get_permalink( $post->post_parent ).'">'.get_the_title( $post->post_parent ).'</a> '.$opt['sep'];
				}
				$output .= '<span>'.get_the_title().'</span>';
			} else if ( is_tax() ) {
				$taxonomy 	= get_taxonomy ( get_query_var('taxonomy') );
				$term 		= get_query_var('term');
				$output .= '<span>'.$taxonomy->label .': '. $term.'</span>'.' <span class="bz-breadcrumb-found">('.$wp_query->found_posts.')</span>';
			} else {
				if ( get_query_var('page') ) {
					$output .= '<a href="'.get_permalink().'">'.get_the_title().'</a> '.$opt['sep'].' '.'<span>'.__('Page','boozurk').' '.get_query_var('page').'</span>';
				} else {
					$output .= '<span>'.get_the_title().'</span>';
				}
			}
		} else {
			$post = $wp_query->get_queried_object();

			// If this is a top level Page, it's simple to output the breadcrumb
			if ( 0 == $post->post_parent ) {
				if ( get_query_var('page') ) {
					$output = $homelink.' '.$opt['sep'].' <a href="'.get_permalink().'">'.get_the_title().'</a> '.$opt['sep'].' '.'<span>'.__('Page','boozurk').' '.get_query_var('page').'</span>';
				} else {
					$output = $homelink." ".$opt['sep']." ".'<span>'.get_the_title().'</span>';
				}
			} else {
				if (isset($post->ancestors)) {
					if (is_array($post->ancestors))
						$ancestors = array_values($post->ancestors);
					else 
						$ancestors = array($post->ancestors);				
				} else {
					$ancestors = array($post->post_parent);
				}

				// Reverse the order so it's oldest to newest
				$ancestors = array_reverse($ancestors);

				// Add the current Page to the ancestors list (as we need it's title too)
				$ancestors[] = $post->ID;

				$links = array();			
				foreach ( $ancestors as $ancestor ) {
					$tmp  = array();
					$tmp['title'] 	= get_the_title( $ancestor );
					$tmp['url'] 	= get_permalink($ancestor);
					$tmp['cur'] = false;
					if ($ancestor == $post->ID) {
						$tmp['cur'] = true;
					}
					$links[] = $tmp;
				}

				$output = $homelink;
				foreach ( $links as $link ) {
					$output .= ' '.$opt['sep'].' ';
					if (!$link['cur']) {
						$output .= '<a href="'.$link['url'].'">'.$link['title'].'</a>';
					} else {
						if ( get_query_var('page') ) {
							$output .= '<a href="'.$link['url'].'">'.$link['title'].'</a> '.$opt['sep'].' '.'<span>'.__('Page','boozurk').' '.get_query_var('page').'</span>';
						} else {
							$output .= '<span>'.$link['title'].'</span>';
						}
					}
				}
			}
		}
		if ( get_query_var('paged') ) {
			$output .= ' '.$opt['sep'].' '.'<span>'.__('Page','boozurk').' '.get_query_var('paged').'</span>';
		}

		return $output;
	}
}
