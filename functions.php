<?php
/**
 * functions.php
 *
 * Contains almost all of the Theme's setup functions and custom functions.
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package Boozurk
 * @since 1.00
 */


/* Custom actions - WP hooks */

add_action( 'after_setup_theme'						, 'boozurk_setup' );
add_action( 'wp_enqueue_scripts'					, 'boozurk_stylesheet' );
add_action( 'wp_head'								, 'boozurk_custom_style' );
add_action( 'wp_enqueue_scripts'					, 'boozurk_scripts' );
add_action( 'wp_footer'								, 'boozurk_initialize_scripts' );
add_action( 'init'									, 'boozurk_post_expander_activate' );
add_action( 'init'									, 'boozurk_infinite_scroll_activate' );
add_action( 'admin_bar_menu'						, 'boozurk_admin_bar_plus', 999 );
add_action( 'wp_head'								, 'boozurk_plus_snippet' );
add_action( 'created_category'						, 'boozurk_created_category_color' );
add_action( 'comment_form_comments_closed'			, 'boozurk_comments_closed' );
add_action( 'template_redirect'						, 'boozurk_allcat' );
add_action( 'comment_form_before'					, 'boozurk_enqueue_comments_reply' );


/* Custom actions - theme hooks */

add_action( 'boozurk_hook_header_after'				, 'boozurk_primary_menu' );
add_action( 'boozurk_hook_body_top'					, 'boozurk_1st_secondary_menu' );
add_action( 'boozurk_hook_footer_before'			, 'boozurk_navigation' );
add_action( 'boozurk_hook_footer_top'				, 'boozurk_2nd_secondary_menu' );
add_action( 'boozurk_hook_header_after'				, 'boozurk_header_widget_area' );
add_action( 'boozurk_hook_entry_before'				, 'boozurk_print_details' );
add_action( 'boozurk_hook_entry_before'				, 'boozurk_navigate_attachments' );
add_action( 'boozurk_hook_entry_before'				, 'boozurk_single_nav' );
add_action( 'boozurk_hook_entry_after'				, 'boozurk_single_widgets_area' );
add_action( 'boozurk_hook_entry_bottom'				, 'boozurk_link_pages' );
add_action( 'boozurk_hook_comments_list_before'		, 'boozurk_navigate_comments' );
add_action( 'boozurk_hook_comments_list_after'		, 'boozurk_navigate_comments' );
add_action( 'boozurk_hook_secondary_sidebar_top'	, 'boozurk_secondary_sidebar_top' );


/* Custom filters - WP hooks */

add_filter( 'post_gallery'							, 'boozurk_gallery_shortcode', 10, 2 );
add_filter( 'use_default_gallery_style'				, '__return_false' );
add_filter( 'embed_oembed_html'						, 'boozurk_wmode_transparent', 10, 3);
add_filter( 'img_caption_shortcode'					, 'boozurk_img_caption_shortcode', 10, 3 );
add_filter( 'the_content'							, 'boozurk_quote_content' );
add_filter( 'smilies_src'							, 'boozurk_smiles_replace',10,2 );
add_filter( 'body_class'							, 'boozurk_body_classes' );
add_filter( 'comment_form_default_fields'			, 'boozurk_comments_form_fields');
add_filter( 'comment_form_defaults'					, 'boozurk_comment_form_defaults' );
add_filter( 'wp_get_attachment_link'				, 'boozurk_get_attachment_link', 10, 6 );
add_filter( 'get_comment_author_link'				, 'boozurk_add_quoted_on' );
add_filter( 'user_contactmethods'					, 'boozurk_new_contactmethods', 10, 1 );
add_filter( 'the_title'								, 'boozurk_titles_filter', 10, 2 );
add_filter( 'excerpt_length'						, 'boozurk_excerpt_length' );
add_filter( 'excerpt_mblength'						, 'boozurk_excerpt_length' );
add_filter( 'excerpt_more'							, 'boozurk_excerpt_more' );
add_filter( 'the_content_more_link'					, 'boozurk_more_link', 10, 2 );
add_filter( 'wp_title'								, 'boozurk_filter_wp_title' );
add_filter( 'avatar_defaults'						, 'boozurk_addgravatar' );
add_filter( 'edit_comment_link'						, 'boozurk_edit_comment_link' );
add_filter( 'wp_list_categories'					, 'boozurk_wrap_categories_count' );
add_filter( 'comment_reply_link'					, 'boozurk_comment_reply_link' );
add_filter( 'cancel_comment_reply_link'				, 'boozurk_cancel_comment_reply_link' );
add_filter( 'wp_nav_menu_items'						, 'boozurk_add_home_link', 10, 2 );
add_filter( 'wp_nav_menu_objects'					, 'boozurk_add_menu_parent_class' );
add_filter( 'page_css_class'						, 'boozurk_add_parent_class', 10, 4 );
add_filter( 'page_css_class'						, 'boozurk_add_selected_class_to_page_item', 10, 1 );
add_filter( 'nav_menu_css_class'					, 'boozurk_add_selected_class_to_menu_item', 10, 2 );
add_filter( 'get_search_form'						, 'boozurk_search_form' );


/* get the theme options */

$boozurk_opt = get_option( 'boozurk_options' );


/* theme infos */

function boozurk_get_info( $field ) {
	static $infos;

	if ( !isset( $infos ) ) {

		$infos['theme'] =			wp_get_theme( 'boozurk' );
		$infos['current_theme'] =	wp_get_theme();
		$infos['version'] =			$infos['theme']? $infos['theme']['Version'] : '';

	}

	return $infos[$field];
}


/* load modules (accordingly to http://justintadlock.com/archives/2010/11/17/how-to-load-files-within-wordpress-themes) */

require_once( 'lib/options.php' ); // load options

require_once( 'lib/admin.php' ); // load admin functions

require_once( 'lib/hooks.php' ); // load the custom hooks module

require_once( 'lib/widgets.php' ); // load the custom widgets module

require_once( 'lib/custom-header.php' ); // load the custom header module

require_once( 'lib/breadcrumb.php' ); // load the breadcrumb module

require_once( 'lib/audio-player.php' ); // load the audio player module

require_once( 'lib/plug-n-play.php' ); // load the plugin compatibility module

if ( boozurk_get_opt( 'boozurk_comment_style' ) ) require_once( 'lib/custom_comments.php' ); // load the comment style module

$boozurk_is_mobile = false;
if ( boozurk_get_opt( 'boozurk_mobile_css' ) ) require_once( 'mobile/core-mobile.php' ); // load mobile functions


/* conditional tags */

function boozurk_is_mobile() { // mobile
	global $boozurk_is_mobile;

	return $boozurk_is_mobile;

}

function boozurk_is_printpreview() { //print preview
	static $is_printpreview;

	if ( !isset( $is_printpreview ) )
		$is_printpreview = isset( $_GET['style'] ) && md5( $_GET['style'] ) == '8e77921d24c6f82c4bd783895e9d9cf1' ? true : false;

	return $is_printpreview;

}

function boozurk_is_allcat() { //is "all category" page
	static $is_allcat;

	if ( !isset( $is_allcat ) )
		$is_allcat = isset( $_GET['allcat'] ) && md5( $_GET['allcat'] ) == '415290769594460e2e485922904f345d' ? true : false;

	return $is_allcat;

}


// Set the content width based on the theme's design
if ( ! isset( $content_width ) ) {
	$content_width = 560;
}


// Add style element for custom theme options
if ( !function_exists( 'boozurk_custom_style' ) ) {
	function boozurk_custom_style(){

		if ( boozurk_is_mobile() ) return; // skip if in mobile view

?>
<style type="text/css">
	body {
		font-size: <?php echo boozurk_get_opt( 'boozurk_font_size' ); ?>;
<?php if ( boozurk_get_opt( 'boozurk_google_font_family' ) && boozurk_get_opt( 'boozurk_google_font_body' ) ) { ?>
		font-family: <?php echo boozurk_get_opt( 'boozurk_google_font_family' ); ?>;
<?php } else { ?>
		font-family: <?php echo boozurk_get_opt( 'boozurk_font_family' ); ?>;
<?php } ?>
	}
<?php if ( boozurk_get_opt( 'boozurk_google_font_family' ) && boozurk_get_opt( 'boozurk_google_font_post_title' ) ) { ?>
	h2.storytitle {
		font-family: <?php echo boozurk_get_opt( 'boozurk_google_font_family' ); ?>;
	}
<?php } ?>
<?php if ( boozurk_get_opt( 'boozurk_google_font_family' ) && boozurk_get_opt( 'boozurk_google_font_post_content' ) ) { ?>
	.storycontent {
		font-family: <?php echo boozurk_get_opt( 'boozurk_google_font_family' ); ?>;
	}
<?php } ?>
	input[type=button]:hover,
	input[type=submit]:hover,
	input[type=reset]:hover,
	textarea:hover,
	input[type=text]:hover,
	input[type=email]:hover,
	input[type=password]:hover,
	input[type=button]:focus,
	input[type=submit]:focus,
	input[type=reset]:focus,
	textarea:focus,
	input[type=text]:focus,
	input[type=email]:focus,
	input[type=password]:focus,
	button:hover,
	button:focus,
	select:hover,
	select:focus {
		border-color: <?php echo boozurk_get_opt( 'boozurk_colors_link' ); ?>;
	}
	a {
		color: <?php echo boozurk_get_opt( 'boozurk_colors_link' ); ?>;
	}
	a:hover,
	.menu-item-parent:hover > a:after,
	.current-menu-item a:hover,
	.current_page_item a:hover,
	.current-cat a:hover {
		color: <?php echo boozurk_get_opt( 'boozurk_colors_link_hover' ); ?>;
	}
	.current-menu-ancestor > a:after,
	.current-menu-parent > a:after,
	.current_page_parent > a:after,
	.current_page_ancestor > a:after,
	.current-menu-item > a,
	.current_page_item > a,
	.current-cat > a {
		color: <?php echo boozurk_get_opt( 'boozurk_colors_link_sel' ); ?>;
	}	
	div.tb_latest_commentators a:hover img {
		border-color: <?php echo boozurk_get_opt( 'boozurk_colors_link_hover' ); ?>;
	}
	#header-widget-area .bz-widget {
		width:<?php echo round ( 99 / intval( boozurk_get_opt( 'boozurk_sidebar_head_split' ) ), 1 ); ?>%;
	}
	#single-widgets-area .bz-widget {
		width:<?php echo round ( 99 / intval( boozurk_get_opt( 'boozurk_sidebar_single_split' ) ), 1 ); ?>%;
	}
	#first_fwa {
		width:<?php echo boozurk_get_opt( 'boozurk_sidebar_foot_1_width' ); ?>;
	}	
	#second_fwa {
		width:<?php echo boozurk_get_opt( 'boozurk_sidebar_foot_2_width' ); ?>;
	}	
	#third_fwa {
		width:<?php echo boozurk_get_opt( 'boozurk_sidebar_foot_3_width' ); ?>;
	}
<?php if ( boozurk_get_opt( 'boozurk_custom_css' ) ) echo boozurk_get_opt( 'boozurk_custom_css' ); ?>

<?php
	$args=array(
		'orderby' => 'name',
		'order' => 'DESC'
	);
	$categories=get_categories($args);
	foreach($categories as $category) {
		$cat_opt = boozurk_get_opt( 'boozurk_cat_colors' );
		$cat_def = boozurk_get_coa( 'boozurk_cat_colors' );
		$cat_color = isset( $cat_opt[$category->term_id] ) ? $cat_opt[$category->term_id] : $cat_def['defaultcolor'];
		$cat_class = '.category-' . sanitize_html_class($category->slug, $category->term_id);
		echo '#posts_content .hentry' . $cat_class . ' { border-left-color:' . $cat_color . ' ;}' . "\n";
		echo '.widget .cat-item-' . $category->term_id . ' > a, #posts_content .cat-item-' . $category->term_id . ' > a { border-color: ' . $cat_color . ' ; }' . "\n";
	}
?>
</style>
<!-- InternetExplorer really sucks! -->
<!--[if lte IE 8]>
<style type="text/css">
	.storycontent img.size-full,
	.gallery img {
		width:auto;
	}
	.widget .avatar {
		max-width: 64px;
	}
</style>
<![endif]-->
<?php

	}
}


// get js modules
if ( !function_exists( 'boozurk_get_js_modules' ) ) {
	function boozurk_get_js_modules( $afterajax = 0 ) {

		$modules = array();

		if ( !$afterajax ) {
			if ( boozurk_get_opt( 'boozurk_js_basic_menu' ) )
				$modules[] = 'animatemenu';
			if ( boozurk_get_opt( 'boozurk_js_basic_autoscroll' ) )
				$modules[] = 'scrolltopbottom';
			if ( boozurk_get_opt( 'boozurk_tinynav' ) )
				$modules[] = 'tinynav';
			if ( is_singular() && comments_open() )
				$modules[] = 'commentvariants';
			if ( ! class_exists( 'TB_Comment_Tools' ) && boozurk_get_opt( 'boozurk_quotethis' ) && is_singular() )
				$modules[] = 'quotethis';
			if ( ( boozurk_get_opt( 'boozurk_infinite_scroll' ) ) && !is_singular() && !is_404() )
				$modules[] = 'infinitescroll';
		}

		if ( boozurk_get_opt( 'boozurk_js_post_expander' ) )
			$modules[] = 'postexpander';
		if ( boozurk_get_opt( 'boozurk_js_thickbox' ) )
			$modules[] = 'thickbox';
		if ( boozurk_get_opt( 'boozurk_js_tooltips' ) )
			$modules[] = 'tooltips';
		if ( boozurk_get_opt( 'boozurk_plusone' ) == 'googleplus_official' )
			$modules[] = 'plusone';
		if ( boozurk_get_opt( 'boozurk_plusone' ) == 'addthis' )
			$modules[] = 'addthis';
		if ( boozurk_get_opt( 'boozurk_js_basic_video_resize' ) )
			$modules[] = 'resizevideo';


		$modules = implode(',', $modules);

		return  apply_filters( 'boozurk_filter_js_modules', $modules );

	}
}


// initialize js
if ( !function_exists( 'boozurk_initialize_scripts' ) ) {
	function boozurk_initialize_scripts() {

		if ( is_admin() || boozurk_is_mobile() ) return;

?>
	<script type="text/javascript">
		/* <![CDATA[ */
		(function(){
			var c = document.body.className;
			c = c.replace(/no-js/, 'js');
			document.body.className = c;
		})();
		/* ]]> */
	</script>
<?php

		if ( ! boozurk_get_opt( 'boozurk_jsani' ) || boozurk_is_printpreview() ) return;

		if ( boozurk_get_opt( 'boozurk_plusone' ) == 'googleplus_official' ) {

?>
	<script type="text/javascript" src="//apis.google.com/js/plusone.js">
		{parsetags: 'explicit'}
	</script>
<?php

		}

		if ( boozurk_get_opt( 'boozurk_plusone' ) == 'addthis' ) {

?>
	<script type="text/javascript">
		var addthis_config =
		{
			ui_delay:200
		}
	</script>
	<script type="text/javascript" src="//s7.addthis.com/js/250/addthis_widget.js"></script>
<?php

		}

	}
}


// Add stylesheets to page
if ( !function_exists( 'boozurk_stylesheet' ) ) {
	function boozurk_stylesheet(){

		if ( is_admin() || boozurk_is_mobile() ) return;

		if ( boozurk_is_printpreview() ) { //print preview

			wp_enqueue_style( 'boozurk-general-style', get_template_directory_uri() . '/css/print.css', false, boozurk_get_info( 'version' ), 'screen' );
			wp_enqueue_style( 'boozurk-preview-style', get_template_directory_uri() . '/css/print_preview.css', false, boozurk_get_info( 'version' ), 'screen' );

		} else { //normal view

			wp_enqueue_style( 'boozurk-general-style', get_stylesheet_uri(), array('thickbox'), boozurk_get_info( 'version' ), 'screen' );
			wp_enqueue_style( 'boozurk-font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css' );

			if ( boozurk_get_opt( 'boozurk_adaptive_layout' ) )
				wp_enqueue_style( 'boozurk-adaptive-layout', get_template_directory_uri() . '/css/adaptive.css', false, boozurk_get_info( 'version' ), 'screen' );

		}

		//google font
		if ( boozurk_get_opt( 'boozurk_google_font_family' ) )
			wp_enqueue_style( 'boozurk-google-fonts', '//fonts.googleapis.com/css?family=' . urlencode( boozurk_get_opt( 'boozurk_google_font_family' ) ) );

		wp_enqueue_style( 'boozurk-print-style', get_template_directory_uri() . '/css/print.css', false, boozurk_get_info( 'version' ), 'print' );

	}
}


// add scripts
if ( !function_exists( 'boozurk_scripts' ) ) {
	function boozurk_scripts(){

		if ( is_admin() || boozurk_is_mobile() || boozurk_is_printpreview() || ! boozurk_get_opt( 'boozurk_jsani' ) ) return;

		$deps = array('jquery');
		$deps[] = 'hoverIntent';

		if ( boozurk_get_opt( 'boozurk_tinynav' ) )
			wp_enqueue_script( 'boozurk-tinynav', get_template_directory_uri() . '/js/tinynav/tinynav.min.js', $deps, boozurk_get_info( 'version' ), true );

		if ( boozurk_get_opt( 'boozurk_js_thickbox' ) )
			$deps[] = 'thickbox';

		wp_enqueue_script( 'boozurk-script', get_template_directory_uri() . '/js/boozurk.min.js', $deps, boozurk_get_info( 'version' ), true );

		$data = array(
			'script_modules'			=> boozurk_get_js_modules(),
			'script_modules_afterajax'	=> boozurk_get_js_modules(1),
			'post_expander'				=> esc_js( __( 'Post loading, please wait...','boozurk' ) ),
			'gallery_preview'			=> esc_js( __( 'Preview','boozurk' ) ),
			'gallery_click'				=> esc_js( __( 'Click on thumbnails','boozurk' ) ),
			'infinite_scroll'			=> esc_js( __( 'Page is loading, please wait...','boozurk' ) ),
			'infinite_scroll_end'		=> esc_js( __( 'No more posts beyond this line','boozurk' ) ),
			'infinite_scroll_type'		=> boozurk_get_opt( 'boozurk_infinite_scroll_type' ),
			'quote_tip'					=> esc_js( __( 'Add selected text as a quote', 'boozurk' ) ),
			'quote'						=> esc_js( __( 'Quote', 'boozurk' ) ),
			'quote_alert'				=> esc_js( __( 'Nothing to quote. First of all you should select some text...', 'boozurk' ) ),
			'comments_closed'			=> esc_js( __( 'Comments closed', 'boozurk' ) ),
			'cooltips_selector'			=> esc_js( implode( ',', apply_filters( 'boozurk_cooltips_selector', array(
					'#quotethis',
					'#cancel-comment-reply-link',
					'.comment-reply-link',
					'.pmb_comm',
					'.minibutton',
					'.share-item img',
					'.tb_categories a',
					'#bz-quotethis',
					'.tb_latest_commentators li',
					'.tb_social a',
					'.post-format-item.compact img',
					'a.bz-tipped-anchor',
				) ) ) ),
		);

		wp_localize_script( 'boozurk-script', 'boozurk_l10n', $data );

	}
}


// post-top date, tags and categories
function boozurk_print_details() {

	if ( is_singular() ) return;

	echo '<ul class="post-top-details fixfloat">';
	if ( boozurk_get_opt( 'boozurk_post_date' ) )
		echo '<li class="post-top-date"><i class="icon-time"></i> ' . get_the_time( get_option( 'date_format' ) ) . '</li>';

	if ( boozurk_get_opt( 'boozurk_post_cat' ) && $categories = get_the_category() ) {
		echo '<li class="post-top-cat"><i class="icon-folder-close"></i> ';
		the_category(', ');
		echo '</li>';
	}

	if ( boozurk_get_opt( 'boozurk_post_tag' ) && $the_tag_list = get_the_tag_list( '', ', ', '' ) ) {
		echo '<li class="post-top-tag"><i class="icon-tags"></i> ' . $the_tag_list . '</li>';
	}
	echo '</ul>';
}


// display the post title with the featured image
if ( !function_exists( 'boozurk_featured_title' ) ) {
	function boozurk_featured_title( $args = '' ) {
		global $post;

		$defaults = array(
			'alternative'	=> '',
			'fallback'		=> '',
			'featured'		=> true,
			'href'			=> get_permalink(),
			'target'		=> '',
			'title'			=> the_title_attribute( array('echo' => 0 ) ), 
		);
		$args = wp_parse_args( $args, $defaults );

		if ( boozurk_get_opt( 'boozurk_hide_frontpage_title' ) && is_page() && is_front_page() ) return;

		if ( boozurk_get_opt( 'boozurk_hide_pages_title' ) && is_page() ) return;

		if ( boozurk_get_opt( 'boozurk_hide_posts_title' ) && is_single() ) return;

		if ( $selected_ids = boozurk_get_opt( 'boozurk_hide_selected_entries_title' ) ) {
			$selected_ids = explode( ',', $selected_ids );
			if ( in_array( $post->ID, $selected_ids ) ) return;
		}

		$post_title = $args['alternative'] ? $args['alternative'] : get_the_title();
		$post_title = $post_title ? $post_title : $args['fallback'];
		$link_target = $args['target'] ? ' target="'.$args['target'].'"' : '';
		$title_content = is_singular() ? $post_title : '<a title="' . esc_attr( $args['title'] ) . '" href="' . esc_url( $args['href'] ) . '"' . $link_target . ' rel="bookmark">' . $post_title . '</a>';
		if ( $post_title ) $post_title = '<h2 class="storytitle">' . $title_content . '</h2>';
		if ( is_front_page() && get_option('show_on_front') == "page" ) $post_title = '';

		switch ( boozurk_get_opt( 'boozurk_featured_title' ) ) {
			case 'none':
				$args['featured'] = false;
				break;
			case 'lists':
				if ( is_singular() ) $args['featured'] = false;
				break;
			case 'single':
				if ( !is_singular() ) $args['featured'] = false;
				break;
		}

		$thumb = false;
		if ( $args['featured'] && has_post_thumbnail( $post->ID ) ) {
			if ( boozurk_get_opt( 'boozurk_featured_title_thumb' ) == 'large') {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				if ( $image[1] >= 900 ) $thumb = get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
			} else {
				$thumb = get_the_post_thumbnail( $post->ID, 'thumbnail' );
			}
		}

		$post_title = apply_filters( 'boozurk_featured_title_text', $post_title );
		$thumb = apply_filters( 'boozurk_featured_title_thumb', $thumb );

		// Check if this is a post or page, if it has a thumbnail, and if it's a big one
		if ( $thumb ) {

?>
	<div class="bz-featured-title">
		<?php echo $thumb; ?>
		<?php echo $post_title; ?>
	</div>
<?php

		} else {
			echo $post_title;
		}
	}
}


// print extra info for posts/pages
if ( !function_exists( 'boozurk_extrainfo' ) ) {
	function boozurk_extrainfo( $args = '' ) {
		global $post;

		$defaults = array(
			'icon'		=> 1,
			'comments'	=> 1,
			'plusone'	=> 1,
		);
		$args = wp_parse_args( $args, $defaults );

		$args = apply_filters( 'boozurk_extrainfo', $args );

?>
	<div class="post_meta_container">

		<?php if ( $args['icon'] ) { ?>
			<a class="pmb_format btn" href="<?php esc_url( the_permalink() ); ?>" rel="bookmark"><i class="icon-placeholder"></i></a>
		<?php } ?>

		<?php
			$page_cd_nc = ( is_page() && !comments_open() && !have_comments() ); //true if page with comments disabled and no comments
			if ( ( ! $page_cd_nc ) && $args['comments'] ) boozurk_comments_link( 'zero=0&one=1&more=%&none=-&class=pmb_comm'); // number of comments
		?>

		<?php if ( !post_password_required() && $args['plusone'] ) { ?>

			<?php if ( boozurk_get_opt( 'boozurk_plusone' ) == 'addthis' ) { ?>
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_16x16_style">
					<a class="addthis_button_compact" href="javascript:void(0)" addthis:url="<?php echo esc_attr( get_permalink() ); ?>" addthis:title="<?php echo esc_attr( get_the_title() ); ?>"><i class="icon icon-plus"></i></a>
				</div>
				<!-- AddThis Button END -->
			<?php } ?>

			<?php if ( ( boozurk_get_opt( 'boozurk_plusone' ) === 1 ) || ( boozurk_get_opt( 'boozurk_plusone' ) == 'googleplus' ) ) { ?>
				<a class="btn share-with-plusone pmb_comm" title="<?php echo esc_attr( sprintf( __( 'recommend this with %s', 'boozurk' ), 'Google+' ) ); ?>" href="http://plus.google.com/share?url=<?php echo rawurlencode( get_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
					<i class="icon icon-google-plus"></i>
				</a>
			<?php } ?>

			<?php if ( boozurk_get_opt( 'boozurk_plusone' ) == 'googleplus_official' ) { ?>
				<div class="bz-plusone-wrap"><div class="g-plusone" data-annotation="none" data-href="<?php echo esc_url( get_permalink() ); ?>"></div></div>
			<?php } ?>

			<?php if ( boozurk_get_opt( 'boozurk_plusone' ) == 'facebook' ) { ?>
				<a class="btn share-with-facebook pmb_comm" title="<?php echo esc_attr( sprintf( __( 'recommend this with %s', 'boozurk' ), 'Facebook' ) ); ?>" href="http://www.facebook.com/sharer.php?u=<?php echo rawurlencode( get_permalink() ); ?>&t=<?php echo rawurlencode( get_the_title() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
					<i class="icon icon-facebook"></i>
				</a>
			<?php } ?>

			<?php if ( boozurk_get_opt( 'boozurk_plusone' ) == 'twitter' ) { ?>
				<a class="btn share-with-twitter pmb_comm" title="<?php echo esc_attr( sprintf( __( 'recommend this with %s', 'boozurk' ), 'Twitter' ) ); ?>" href="https://twitter.com/intent/tweet?original_referer=<?php echo rawurlencode( get_permalink() ); ?>&text=<?php echo rawurlencode( get_the_title() ); ?>&url=<?php echo rawurlencode( get_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
					<i class="icon icon-twitter"></i>
				</a>
			<?php } ?>

		<?php } ?>

		<?php edit_post_link( '<i class="icon icon-pencil"></i>' ); ?>

	</div>
<?php

	}
}


// the last commenters of a post
if ( !function_exists( 'boozurk_last_comments' ) ) {
	function boozurk_last_comments( $id = null ) {
		global $post;

		$num = apply_filters( 'boozurk_last_comments_number', 6 );
		if ( !$id ) $id = $post->ID;

		$comments = get_comments( 'status=approve&number=' . $num . '&type=comment&post_id=' . $id ); // valid type values (not documented) : 'pingback','trackback','comment'

		$ellipsis = '';
		if ( count( $comments ) > 5 ) {
			$ellipsis = '<span class="item-label">...</span>';
			$comments = array_slice( $comments, 0, 5 );
		}

		$comments = array_reverse( $comments );

		if ( $comments ) {

?>
	<div class="bz-last-cop fixfloat">
		<span class="item-label"><?php _e('last comments','boozurk'); ?></span>
		<span class="item-label"><i class="icon-angle-right"></i></span>
		<?php echo $ellipsis; ?>
		<?php foreach ( $comments as $comment ) { ?>
			<div class="item no-grav">
				<?php echo get_avatar( $comment, 32, $default=get_option('avatar_default'), $comment->comment_author );?>
				<div class="bz-tooltip bz-300"><div class="bz-tooltip-inner">
					<?php echo $comment->comment_author; ?>
					<br /><br />
					<?php comment_excerpt( $comment->comment_ID ); ?>
				</div></div>
			</div>
		<?php } ?>
		<br class="fixfloat" />
	</div>
<?php
		}

	}
}


// display navigation
function boozurk_navigation() {

	if ( ! is_active_widget( false, false, 'bz-navbuttons', true ) ) boozurk_navbuttons();

}


// navigation buttons
if ( !function_exists( 'boozurk_navbuttons' ) ) {
	function boozurk_navbuttons( $args = '' ) {
		global $post;

		$defaults = array(
			'print'		=> 1,
			'comment'	=> 1,
			'feed'		=> 1,
			'trackback'	=> 1,
			'home'		=> 1,
			'next_prev'	=> 1,
			'up_down'	=> 1,
			'fixed'		=> 1,
		);
		$args = wp_parse_args( $args, $defaults );

		$args = apply_filters( 'boozurk_navbuttons', $args );

		extract( $args );

		$is_post = is_single() && ! is_attachment() && ! boozurk_is_allcat();
		$is_image = is_attachment() && ! boozurk_is_allcat();
		$is_page = is_singular() && ! is_single() && ! is_attachment() && ! boozurk_is_allcat();
		$is_singular = is_singular() && ! boozurk_is_allcat();

?>
	<div id="navbuttons"<?php if ( $fixed ) echo ' class="fixed"'; ?>>

		<ul>

		<?php if ( $print && $is_singular ) { 																// ------- Print ------- ?>
			<li class="minibutton minib_print btn" title="<?php esc_attr_e( 'Print','boozurk' ); ?>">
				<a rel="nofollow" href="<?php
					$arr_params['style'] = 'printme';
					if ( get_query_var('page') ) {
						$arr_params['page'] = esc_html( get_query_var( 'page' ) );
					}
					if ( get_query_var('cpage') ) {
						$arr_params['cpage'] = esc_html( get_query_var( 'cpage' ) );
					}
					echo esc_url( add_query_arg( $arr_params, get_permalink() ) );
					?>"><i class="icon-print"></i></a>
			</li>
		<?php } ?>

		<?php if ( $comment && $is_singular && comments_open( $post->ID ) && !post_password_required() ) { 	// ------- Leave a comment ------- ?>
			<li class="minibutton minib_comment btn" title="<?php esc_attr_e( 'Leave a comment','boozurk' ); ?>">
				<a href="#respond"><i class="icon-comment"></i></a>
			</li>
		<?php } ?>

		<?php if ( $feed && $is_singular && comments_open( $post->ID ) && !post_password_required() ) { 	// ------- RSS feed ------- ?>
			<li class="minibutton minib_rss btn" title="<?php esc_attr_e( 'Feed for comments on this post', 'boozurk' ); ?>">
				<a href="<?php echo esc_url( get_post_comments_feed_link( $post->ID, 'rss2' ) ); ?> "><i class="icon-rss"></i></a>
			</li>
		<?php } ?>

		<?php if ( $trackback && $is_singular && pings_open() ) { 											// ------- Trackback ------- ?>
			<li class="minibutton minib_track btn" title="<?php esc_attr_e( 'Trackback URL','boozurk' ); ?>">
				<a href="<?php echo esc_url( get_trackback_url() ); ?>" rel="trackback"><i class="icon-reply"></i></a>
			</li>
		<?php } ?>

		<?php if ( $home ) { 																				// ------- Home ------- ?>
			<li class="minibutton minib_home btn" title="<?php esc_attr_e( 'Home','boozurk' ); ?>">
				<a href="<?php echo esc_url( home_url() ); ?>"><i class="icon-home"></i></a>
			</li>
		<?php } ?>

		<?php if ( $is_image ) { 																			// ------- Back to parent post ------- ?>
			<?php if ( !empty( $post->post_parent ) ) { ?>
				<li class="minibutton minib_backtopost btn" title="<?php echo esc_attr( sprintf( __( 'Return to %s', 'boozurk' ), strip_tags( get_the_title( $post->post_parent ) ) ) ); ?>">
					<a href="<?php echo esc_url( get_permalink( $post->post_parent ) ); ?>" rel="gallery"><i class="icon-chevron-left"></i></a>
				</li>
			<?php } ?>
		<?php } ?>

		<?php if (  $next_prev && $is_post && get_previous_post() ) { 										// ------- Previous post ------- ?>
			<li class="minibutton minib_ppage btn" title="<?php echo esc_attr( __( 'Previous Post', 'boozurk' ) . ': ' . strip_tags( get_the_title( get_previous_post() ) ) ); ?>">
				<a rel="prev" href="<?php echo esc_url( get_permalink( get_previous_post() ) ); ?>"><i class="icon-chevron-left"></i></a>
			</li>
		<?php } ?>

		<?php if ( $next_prev && $is_post && get_next_post() ) { 											// ------- Next post ------- ?>
			<li class="minibutton minib_npage btn" title="<?php echo esc_attr( __( 'Next Post', 'boozurk' ) . ': ' . strip_tags( get_the_title( get_next_post() ) ) ); ?>">
				<a rel="next" href="<?php echo esc_url( get_permalink( get_next_post() ) ); ?>"><i class="icon-chevron-right"></i></a>
			</li>
		<?php } ?>

		<?php if ( $next_prev && !$is_singular && ! boozurk_is_allcat() && get_next_posts_link() ) { 			// ------- Older Posts ------- ?>
			<li class="minibutton nb-nextprev minib_npages btn" title="<?php esc_attr_e( 'Older Posts', 'boozurk' ); ?>">
				<?php next_posts_link( '<i class="icon-chevron-left"></i>' ); ?>
			</li>
		<?php } ?>

		<?php if ( $next_prev && !$is_singular && ! boozurk_is_allcat() && get_previous_posts_link() ) { 		// ------- Newer Posts ------- ?>
			<li class="minibutton nb-nextprev minib_ppages btn" title="<?php esc_attr_e( 'Newer Posts', 'boozurk' ); ?>">
				<?php previous_posts_link( '<i class="icon-chevron-right"></i>' ); ?>
			</li>
		<?php } ?>

		<?php if ( $up_down ) { 																			// ------- Top ------- ?>
			<li class="minibutton minib_top btn" title="<?php esc_attr_e( 'Top of page', 'boozurk' ); ?>">
				<a href="#"><i class="icon-chevron-up"></i></a>
			</li>
		<?php } ?>

		<?php if ( $up_down ) { 																			// ------- Bottom ------- ?>
			<li class="minibutton minib_bottom btn" title="<?php esc_attr_e( 'Bottom of page', 'boozurk' ); ?>">
				<a href="#footer"><i class="icon-chevron-down"></i></a>
			</li>
		<?php } ?>

		</ul>

	</div>
<?php

	}
}


// the header
if ( !function_exists( 'boozurk_get_header' ) ) {
	function boozurk_get_header() {

		if ( display_header_text() ) { 
			$header = '<h1><a href="' . esc_url( home_url() ) . '/">' . get_bloginfo( 'name' ) . '</a></h1>';
		} else {
			$header = '<h1 class="hide_if_no_print"><a href="' . esc_url( home_url() ) . '/">' . get_bloginfo( 'name' ) . '</a></h1>';
		}

		if ( get_header_image() ) { 
			if ( display_header_text() ) { 
				$header .= '<img alt="' . esc_attr( home_url() ) . '" src="' . esc_url( get_header_image() ) . '" />';
			} else {
				$header .= '<a href="' . esc_url( home_url() ) . '/"><img alt="' . esc_attr( home_url() ) . '" src="' . esc_url( get_header_image() ) . '" /></a>';
			}
		}

		return apply_filters( 'boozurk_filter_header', $header );

	}
}


// archives pages navigation
if ( !function_exists( 'boozurk_navigate_archives' ) ) {
	function boozurk_navigate_archives() {
		global $paged, $wp_query;

		if ( !$paged ) $paged = 1;

?>
	<div id="bz-page-nav" class="bz-navigate navigate_archives">
	<?php if ( function_exists( 'wp_pagenavi' ) ) { ?>

		<?php wp_pagenavi(); ?>

	<?php } elseif ( function_exists( 'wp_paginate' ) ) { ?>

		<?php wp_paginate(); ?>

	<?php } else { ?>

		<div id="bz-page-nav-msg"></div>
		<div id="bz-page-nav-subcont">
			<span id="bz-next-posts-link"><?php next_posts_link( '&laquo;' ); ?></span>
			<?php printf( '<span>' . __( 'page %1$s of %2$s','boozurk' ) . '</span>', $paged, $wp_query->max_num_pages ); ?>
			<?php previous_posts_link( '&raquo;' ); ?>
		</div>
		<div id="bz-next-posts-button" class="hide-if-no-js">
			<input type="button" value="<?php echo __( 'Next Page', 'boozurk' ); ?>" onClick="boozurkScripts.AJAX_paged();" />
		</div>

	<?php } ?>
	</div>
<?php

	}
}


// attachments navigation
if ( !function_exists( 'boozurk_navigate_attachments' ) ) {
	function boozurk_navigate_attachments() {
		global $post;

		if ( is_attachment() && wp_attachment_is_image() ) {
			$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
			foreach ( $attachments as $k => $attachment ) {
				if ( $attachment->ID == $post->ID )
					break;
			}
			$nextk = $k + 1;
			$prevk = $k - 1;

?>
	<div class="nav-single">

		<?php if ( isset( $attachments[ $prevk ] ) ) { ?>
			<span class="nav-previous ">
				<i class="icon-angle-left"></i>
				<a rel="prev" title="" href="<?php echo esc_url( get_attachment_link( $attachments[ $prevk ]->ID ) ); ?>"><?php echo wp_get_attachment_image( $attachments[ $prevk ]->ID, array( 70, 70 ) ); ?></a>
			</span>
		<?php } ?>

		<?php if ( isset( $attachments[ $nextk ] ) ) { ?>
			<span class="nav-next">
				<a rel="next" title="" href="<?php echo esc_url( get_attachment_link( $attachments[ $nextk ]->ID ) ); ?>"><?php echo wp_get_attachment_image( $attachments[ $nextk ]->ID, array( 70, 70 ) ); ?></a>
				<i class="icon-angle-right"></i>
			</span>
		<?php } ?>

	</div><!-- #nav-single -->
<?php

		} 
	}
}


// displays page-links for paginated posts
function boozurk_link_pages() {

?>
	<div class="fixfloat">
		<?php echo str_replace( '> <', '><', wp_link_pages( 'before=<div class="bz-navigate navigate_page">' . '<span>' . __( 'Pages','boozurk' ) . ':</span>' . '&after=</div>&echo=0' ) ); ?>
	</div>
<?php

}


// the widget area for single posts/pages
function boozurk_single_widgets_area() {

	if ( !is_singular() ) return;

	if ( apply_filters( 'boozurk_skip_post_widgets_area', false ) ) return;

	if ( is_active_sidebar( 'single-widgets-area' ) ) {

?>
	<div id="single-widgets-area" class="ul_swa fixfloat">
		<?php dynamic_sidebar( 'single-widgets-area' ); ?>
		<br class="fixfloat" />
	</div>
<?php

	}
}


// default widgets to be printed in primary sidebar
if ( !function_exists( 'boozurk_default_widgets' ) ) {
	function boozurk_default_widgets() {

		$default_widgets = array(
			'WP_Widget_Search'		=> 'widget_search',
			'WP_Widget_Meta'		=> 'widget_meta',
			'WP_Widget_Pages'		=> 'widget_pages',
			//'WP_Widget_Links'		=> 'widget_links',
			'WP_Widget_Categories'	=> 'widget_categories',
			'WP_Widget_Archives'	=> 'widget_archive',
		);

		foreach ( apply_filters( 'boozurk_default_widgets', $default_widgets ) as $widget => $class ) {
			the_widget( $widget, '', boozurk_get_default_widget_args( 'id=&class=' . $class ) );
		}

	}
}


// get the post thumbnail or (if not set) the format related icon
if ( !function_exists( 'boozurk_get_the_thumb' ) ) {
	function boozurk_get_the_thumb( $args ) {

		$defaults = array(
			'id'		=> 0,
			'size_w'	=> 32,
			'size_h'	=> 0,
			'class'		=> '',
			'default'	=> '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! $args['size_h'] ) $args['size_h'] = $args['size_w'];

		if ( $args['id'] && has_post_thumbnail( $args['id'] ) ) {

			return get_the_post_thumbnail( $args['id'], array( $args['size_w'],$args['size_h'] ) );

		} else {

			if ( $args['id'] )
				$format = get_post_format( $args['id'] ) ? get_post_format( $args['id'] ) : 'standard';
			else
				$format = $args['default'];

			return apply_filters( 'boozurk_hook_get_the_thumb', '<i class="' . esc_attr( $args['class'] . ' icon-' . $args['size_h'] . ' ' . $format ) . '"></i>', $format, $args );

		}

	}
}


// get the post format string
if ( !function_exists( 'boozurk_get_post_format' ) ) {
	function boozurk_get_post_format( $id ) {

		if ( post_password_required() )
			$format = 'protected';
		else
			$format = ( boozurk_get_opt( 'boozurk_post_formats_' . get_post_format( $id ) ) ) ? get_post_format( $id ) : '' ;

		return $format;

	}
}


// set up custom colors and header image
if ( !function_exists( 'boozurk_setup' ) ) {
	function boozurk_setup() {

		// Register localization support
		load_theme_textdomain( 'boozurk', get_template_directory() . '/languages' );

		// Theme uses wp_nav_menu() in three location
		register_nav_menus( array( 'primary'	=> __( 'Main Navigation Menu', 'boozurk' ) ) );
		register_nav_menus( array( 'secondary1'	=> __( 'Secondary Navigation Menu #1', 'boozurk' ) ) );
		register_nav_menus( array( 'secondary2'	=> __( 'Secondary Navigation Menu #2', 'boozurk' ) ) );

		// Used for featured posts if a large-feature doesn't exist.
		set_post_thumbnail_size( 1000, 288, true );
		add_image_size( 'large-feature', 1000, 288, true );

		// Register Features Support
		add_theme_support( 'automatic-feed-links' );

		// Thumbnails support
		add_theme_support( 'post-thumbnails' );

		// Add the editor style
		if ( boozurk_get_opt( 'boozurk_editor_style' ) ) add_editor_style( 'css/editor-style.css' );

		// This theme uses post formats
		$format = array();
		if ( boozurk_get_opt( 'boozurk_post_formats_aside'		) ) $format[] = 'aside';
		if ( boozurk_get_opt( 'boozurk_post_formats_audio'		) ) $format[] = 'audio';
		if ( boozurk_get_opt( 'boozurk_post_formats_chat'		) ) $format[] = 'chat';
		if ( boozurk_get_opt( 'boozurk_post_formats_gallery'	) ) $format[] = 'gallery';
		if ( boozurk_get_opt( 'boozurk_post_formats_image'		) ) $format[] = 'image';
		if ( boozurk_get_opt( 'boozurk_post_formats_link'		) ) $format[] = 'link';
		if ( boozurk_get_opt( 'boozurk_post_formats_quote'		) ) $format[] = 'quote';
		if ( boozurk_get_opt( 'boozurk_post_formats_status'		) ) $format[] = 'status';
		if ( boozurk_get_opt( 'boozurk_post_formats_video'		) ) $format[] = 'video';
		add_theme_support( 'post-formats', $format );

	}
}


//add a default gravatar
function boozurk_addgravatar( $avatar_defaults ) {

	$myavatar = get_template_directory_uri() . '/images/user.png';

	$avatar_defaults[$myavatar] = __( 'boozurk Default Gravatar', 'boozurk' );

	return $avatar_defaults;

}


// create a random nick name
if ( !function_exists( 'boozurk_random_nick' ) ) {
	function boozurk_random_nick (  ) {

		$prefix = array(
			'ATX-',
			'Adorable ',
			'Adventurous ',
			'Alien ',
			'Angry ',
			'Annoyed ',
			'Anxious ',
			'Atrocious ',
			'Attractive ',
			'Bad ',
			'Bad ',
			'Barbarious ',
			'Bavarian ',
			'Beautiful ',
			'Bewildered ',
			'Bitter ',
			'Black ',
			'Blond ',
			'Blue ',
			'Blue-Eyed  ',
			'Bored ',
			'Breezy ',
			'Bright ',
			'Brown ',
			'Cloudy ',
			'Clumsy ',
			'Colorful ',
			'Combative ',
			'Condemned ',
			'Confused ',
			'Cool ',
			'Crazy ',
			'Creepy ',
			'Cruel ',
			'Cubic ',
			'Curly ',
			'Cute ',
			'Dance ',
			'Dangerous ',
			'Dark ',
			'Death ',
			'Delicious ',
			'Dinky ',
			'Distinct ',
			'Disturbed ',
			'Dizzy ',
			'Drunk ',
			'Drunken ',
			'Dull ',
			'Dumb ',
			'E-',
			'Electro ',
			'Elegant ',
			'Elite ',
			'Embarrassed ',
			'Envious ',
			'Evil ',
			'Fancy ',
			'Fast ',
			'Fat ',
			'Fierce ',
			'Flipped-out ',
			'Flying ',
			'Fourios ',
			'Frantic ',
			'Fresh ',
			'Frustraded ',
			'Funny ',
			'Furious ',
			'Fuzzy ',
			'Gameboy ',
			'Giant ',
			'Giga ',
			'Green ',
			'Handsome ',
			'Hard ',
			'Harsh ',
			'Hazardous ',
			'Hiphop ',
			'Hi-res ',
			'Holy ',
			'Horny ',
			'Hot ',
			'House ',
			'i-',
			'Icy ',
			'Infested ',
			'Insane ',
			'Joyous ',
			'Kentucky Fried ',
			'Lame ',
			'Leaking ',
			'Lone ',
			'Lovely ',
			'Lucky ',
			'Mc',
			'Melodic ',
			'Micro ',
			'Mighty ',
			'Mini ',
			'Mutated ',
			'Nasty ',
			'Nice ',
			'Orange ',
			'PS/2-',
			'Pretty ',
			'Purple ',
			'Purring ',
			'Quiet ',
			'Radioactive ',
			'Red ',
			'Resonant ',
			'Salty ',
			'Sexy ',
			'Slow ',
			'Smooth ',
			'Stinky ',
			'Strong ',
			'Supa-Dupa-',
			'Super ',
			'USB-',
			'Ugly ',
			'Unholy ',
			'Vivacious ',
			'Whispering ',
			'White ',
			'Wild ',
			'X',
			'XBox ',
			'Yellow '
		);

		$suffix = array(
			'16',
			'3',
			'6',
			'7',
			'Abe',
			'Bee',
			'Bird',
			'Boy',
			'Cat',
			'Cow',
			'Crow',
			'Cypher',
			'DJ',
			'Dad',
			'Deer',
			'Dog',
			'Donkey',
			'Duck',
			'Eagle',
			'Elephant',
			'Fly',
			'Fox',
			'Frog',
			'Girl',
			'Girlie',
			'Guinea Pig',
			'Hasi',
			'Hawk',
			'Jackal',
			'Lizard',
			'MC',
			'Men',
			'Mom',
			'Morpheus',
			'Mouse',
			'Mule',
			'Neo',
			'Pig',
			'Rabbit',
			'Rat',
			'Rhino',
			'Smurf',
			'Snail',
			'Snake',
			'Star',
			'Tank',
			'Tiger',
			'Wolf',
			'Butterfly',
			'Elk',
			'Godzilla',
			'Horse',
			'Penguin',
			'Pony',
			'Reindeer',
			'Sheep',
			'Sock-Puppet',
			'Worm',
			'Bermuda'
		);

		return $prefix[array_rand($prefix)] . $suffix[array_rand($suffix)];

	}
}


// Get first image of a post
if ( !function_exists( 'boozurk_get_first_image' ) ) {
	function boozurk_get_first_image( $post_id = null, $filtered_content = false ) {

		$post = get_post( $post_id );

		$first_image = array( 'img' => '', 'title' => '', 'src' => '' );

		//search the images in post content
		preg_match_all( '/<img[^>]+>/i',$filtered_content ? apply_filters( 'the_content', $post->post_content ): $post->post_content, $result );
		//grab the first one
		if ( isset( $result[0][0] ) ){
			$first_image['img'] = $result[0][0];
			//get the title (if any)
			preg_match_all( '/(title)=(["|\'][^"|\']*["|\'])/i',$first_image['img'], $title );
			if ( isset( $title[2][0] ) ){
				$first_image['title'] = str_replace( '"','',$title[2][0] );
			}
			//get the path
			preg_match_all( '/(src)=(["|\'][^"|\']*["|\'])/i',$first_image['img'], $src );
			if ( isset( $src[2][0] ) ){
				$first_image['src'] = str_replace( array( '"', '\''),'',$src[2][0] );
			}
			return $first_image;
		} else {
			return false;
		}

	}
}


// Get first link of a post
if ( !function_exists( 'boozurk_get_first_link' ) ) {
	function boozurk_get_first_link() {
		global $post;

		$first_info = array( 'anchor' => '', 'title' => '', 'href' => '', 'text' => '' );
		//search the link in post content
		preg_match_all( "/<a\b[^>]*>(.*?)<\/a>/i",$post->post_content, $result );
		//grab the first one
		if ( isset( $result[0][0] ) ){
			$first_info['anchor'] = $result[0][0];
			$first_info['text'] = isset( $result[1][0] ) ? $result[1][0] : '';
			//get the title (if any)
			preg_match_all( '/(title)=(["\'][^"]*["\'])/i',$first_info['anchor'], $link_title );
			$first_info['title'] = isset( $link_title[2][0] ) ? str_replace( array('"','\''),'',$link_title[2][0] ) : '';
			//get the path
			preg_match_all( '/(href)=(["\'][^"]*["\'])/i',$first_info['anchor'], $link_href );
			$first_info['href'] = isset( $link_href[2][0] ) ? str_replace( array('"','\''),'',$link_href[2][0] ) : '';
			return $first_info;
		} else {
			return false;
		}

	}
}


// Get first blockquote words
if ( !function_exists( 'boozurk_get_blockquote' ) ) {
	function boozurk_get_blockquote() {
		global $post;

		$first_quote = array( 'quote' => '', 'cite' => '' );
		//search the blockquote in post content
		preg_match_all( '/<blockquote\b[^>]*>([\w\W]*?)<\/blockquote>/',$post->post_content, $blockquote );
		//grab the first one
		if ( isset( $blockquote[0][0] ) ){
			$first_quote['quote'] = strip_tags( $blockquote[0][0] );
			$words = explode( " ", $first_quote['quote'], 6 );
			if ( count( $words ) == 6 ) $words[5] = '...';
			$first_quote['quote'] = implode( ' ', $words );
			preg_match_all( '/<cite>([\w\W]*?)<\/cite>/',$blockquote[0][0], $cite );
			$first_quote['cite'] = ( isset( $cite[1][0] ) ) ? $cite[1][0] : '';
			return $first_quote;
		} else {
			return false;
		}

	}
}


// Get first gallery
if ( !function_exists( 'boozurk_get_gallery_shortcode' ) ) {
	function boozurk_get_gallery_shortcode() {
		global $post;

		$pattern = get_shortcode_regex();

		if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
			&& array_key_exists( 2, $matches )
			&& in_array( 'gallery', $matches[2] ) ) // gallery shortcode is being used
		{
			$key = array_search( 'gallery', $matches[2] );
			$attrs = shortcode_parse_atts( $matches['3'][$key] );
			return $attrs;
		}

	}
}


// run the gallery preview
if ( !function_exists( 'boozurk_gallery_preview' ) ) {
	function boozurk_gallery_preview() {

			$attrs = boozurk_get_gallery_shortcode();
			$attrs['preview'] = true;
			return boozurk_gallery_shortcode( '', $attrs );

	}
}


// the gallery preview walker
if ( !function_exists( 'boozurk_gallery_preview_walker' ) ) {
	function boozurk_gallery_preview_walker( $attachments = '', $id = 0 ) {

		if ( ! $id )
			return false;

		if ( empty( $attachments ) )
			$attachments = get_children( array( 'post_parent' => $id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999 ) );

		if ( empty( $attachments ) )
			return false;

		$permalink = get_permalink( $id );

		$images_count = count( $attachments );
		$first_image = array_shift( $attachments );
		$other_imgs = array_slice( $attachments, 0, 3 );

		$output = '<span class="gallery-item size-medium">' . wp_get_attachment_image( $first_image->ID, 'medium' ) . '</span><!-- .gallery-item -->';

		$output .= '<div class="thumbnail-wrap">';
		foreach ($other_imgs as $image) {
			$output .= '
				<div class="gallery-item size-thumbnail">
					' . wp_get_attachment_image( $image->ID, 'thumbnail' ) . '
				</div>
			';
		}
		$output .= '</div>';

		$output .= '
			<p class="info">
				<em>' . sprintf( _n( 'This gallery contains <a %1$s><strong>%2$s</strong> image</a>', 'This gallery contains <a %1$s><strong>%2$s</strong> images</a>', $images_count, 'boozurk' ),
				'href="' . esc_url( get_permalink() ) . '" title="' . esc_attr__( 'View gallery', 'boozurk' ) . '" rel="bookmark"',
				number_format_i18n( $images_count )
				) . '</em>
			</p>
			';

		$output = apply_filters( 'boozurk_gallery_preview_walker', $output );

		$output = '<div class="gallery gallery-preview">' . $output . '<br class="fixfloat" /></div>';

		echo $output;

		return true;

	}
}


//add share links to post/page
if ( !function_exists( 'boozurk_share_this' ) ) {
	function boozurk_share_this(){

?>
	<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
		<a class="addthis_button_facebook"></a>
		<a class="addthis_button_twitter"></a>
		<a class="addthis_button_google_plusone_share"></a>
		<a class="addthis_button_compact"></a>
		<a class="addthis_counter addthis_bubble_style"></a>
	</div>
	<br class="fixfloat"/>
	<script type="text/javascript">
		var addthis_share =
		{
			title: '<?php echo get_bloginfo( 'name' ) ?>',
			url : '<?php echo home_url( '/' ) ?>'
		}
	</script>
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-523ad91d38c2da47"></script>
	<!-- AddThis Button END -->
<?php

	}
}


//get a thumb for a post/page
if ( !function_exists( 'boozurk_get_the_thumb_url' ) ) {
	function boozurk_get_the_thumb_url( $post_id = 0 ){
		global $post;

		if ( !$post_id ) $post_id = $post->ID;

		// has featured image
		if ( get_post_thumbnail_id( $post_id ) )
			return wp_get_attachment_thumb_url( get_post_thumbnail_id( $post_id ) );

		$attachments = get_children( array(
			'post_parent'		=> $post_id,
			'post_status'		=> 'inherit',
			'post_type'			=> 'attachment',
			'post_mime_type'	=> 'image',
			'orderby'			=> 'menu_order',
			'order'				=> 'ASC',
			'numberposts'		=> 1,
		) );

		//has attachments
		if ( $attachments )
			return wp_get_attachment_thumb_url( key($attachments) );

		//has an hardcoded <img>
		if ( $img = boozurk_get_first_image() )
			return $img['src'];

		//has a generated <img>
		if ( $img = boozurk_get_first_image( false, true) )
			return $img['src'];

		if ( $img = get_header_image() )
			return $img;

		//nothing found
		return '';
	}

}


//Display navigation to next/previous post when applicable
if ( !function_exists( 'boozurk_single_nav' ) ) {
	function boozurk_single_nav() {
		global $post;

		if ( ! is_single() || is_attachment() ) return;

		if ( ! boozurk_get_opt( 'boozurk_browse_links' ) ) return;

		$next = get_next_post();
		$prev = get_previous_post();
		$next_title = get_the_title( $next ) ? get_the_title( $next ) : __( 'Next Post', 'boozurk' );
		$prev_title = get_the_title( $prev ) ? get_the_title( $prev ) : __( 'Previous Post', 'boozurk' );
?>
	<div class="nav-single fixfloat">
		<?php if ( $prev ) { ?>
			<span class="nav-previous ">
				<i class="icon-angle-left"></i>
				<a rel="prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>" title="<?php echo esc_attr(strip_tags( __( 'Previous Post', 'boozurk' ) . ': ' . $prev_title ) ); ?>">
					<?php echo boozurk_get_the_thumb( array( 'id' => $prev->ID, 'size_w' => 32, 'class' => 'tb-thumb-format' ) ); ?> <span><?php echo $prev_title; ?></span>
				</a>
			</span>
		<?php } ?>
		<?php if ( $next ) { ?>
			<span class="nav-next">
				<a rel="next" href="<?php echo esc_url( get_permalink( $next ) ); ?>" title="<?php echo esc_attr(strip_tags( __( 'Next Post', 'boozurk' ) . ': ' . $next_title ) ); ?>">
					<span><?php echo $next_title; ?></span> <?php echo boozurk_get_the_thumb( array( 'id' => $next->ID, 'size_w' => 32, 'class' => 'tb-thumb-format' ) ); ?>
				</a>
				<i class="icon-angle-right"></i>
			</span>
		<?php } ?>
	</div><!-- #nav-single -->
<?php

	}
}


//Image EXIF details
if ( !function_exists( 'boozurk_exif_details' ) ) {
	function boozurk_exif_details( $echo = 1 ){

		$imgmeta = wp_get_attachment_metadata();

		// convert the shutter speed retrieve from database to fraction
		if ( $imgmeta['image_meta']['shutter_speed'] && (1 / $imgmeta['image_meta']['shutter_speed']) > 1) {
			if ((number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1)) == 1.3
			or number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 1.5
			or number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 1.6
			or number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 2.5){
				$imgmeta['image_meta']['shutter_speed'] = "1/" . number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1, '.', '');
			} else {
				$imgmeta['image_meta']['shutter_speed'] = "1/" . number_format((1 / $imgmeta['image_meta']['shutter_speed']), 0, '.', '');
			}
		}

		$output = '';
		// get other EXIF and IPTC data of digital photograph
		$output														.= __("Width", "boozurk" ) . ": " . $imgmeta['width']."px<br />";
		$output														.= __("Height", "boozurk" ) . ": " . $imgmeta['height']."px<br />";
		if ( $imgmeta['image_meta']['created_timestamp'] ) $output	.= __("Date Taken", "boozurk" ) . ": " . date("d-M-Y H:i:s", $imgmeta['image_meta']['created_timestamp'])."<br />";
		if ( $imgmeta['image_meta']['copyright'] ) $output			.= __("Copyright", "boozurk" ) . ": " . $imgmeta['image_meta']['copyright']."<br />";
		if ( $imgmeta['image_meta']['credit'] ) $output				.= __("Credit", "boozurk" ) . ": " . $imgmeta['image_meta']['credit']."<br />";
		if ( $imgmeta['image_meta']['title'] ) $output				.= __("Title", "boozurk" ) . ": " . $imgmeta['image_meta']['title']."<br />";
		if ( $imgmeta['image_meta']['caption'] ) $output			.= __("Caption", "boozurk" ) . ": " . $imgmeta['image_meta']['caption']."<br />";
		if ( $imgmeta['image_meta']['camera'] ) $output				.= __("Camera", "boozurk" ) . ": " . $imgmeta['image_meta']['camera']."<br />";
		if ( $imgmeta['image_meta']['focal_length'] ) $output		.= __("Focal Length", "boozurk" ) . ": " . $imgmeta['image_meta']['focal_length']."mm<br />";
		if ( $imgmeta['image_meta']['aperture'] ) $output			.= __("Aperture", "boozurk" ) . ": f/" . $imgmeta['image_meta']['aperture']."<br />";
		if ( $imgmeta['image_meta']['iso'] ) $output				.= __("ISO", "boozurk" ) . ": " . $imgmeta['image_meta']['iso']."<br />";
		if ( $imgmeta['image_meta']['shutter_speed'] ) $output		.= __("Shutter Speed", "boozurk" ) . ": " . sprintf( __("%s seconds", "boozurk" ), $imgmeta['image_meta']['shutter_speed']) . "<br />";

		$output = '<div class="exif-attachment-info">' . $output . '</div>';

		if ( $echo )
			echo $output;
		else
			return $output;

	}
}


// print extra info for posts/pages
if ( !function_exists( 'boozurk_post_details' ) ) {
	function boozurk_post_details( $args = '' ) {
		global $post;

		$defaults = array(
			'author'		=> 1,
			'date'			=> 1,
			'tags'			=> 1,
			'categories'	=> 1,
			'avatar_size'	=> 48,
			'featured'		=> 0,
			'echo'			=> 1,
		);

		$args = wp_parse_args( $args, $defaults );

		$tax_separator = apply_filters( 'boozurk_filter_taxomony_separator', ', ' );

		$output = '<ul class="post-details">';

		if ( $args['featured'] &&  has_post_thumbnail( $post->ID ) )
			$output .= '<li class="post-details-thumb">' . get_the_post_thumbnail( $post->ID, 'thumbnail') . '</li>';

		if ( $args['author'] )
			$output .= '<li>' . boozurk_author_badge( $post->post_author, $args['avatar_size'] ) . '</li>';

		if ( $args['categories'] )
			$output .= '<li class="post-details-cats"><i class="icon-folder-close"></i> <span>' . __( 'Categories', 'boozurk' ) . ': </span>' . get_the_category_list( $tax_separator ) . '</li>';

		if ( $args['tags'] )
			$tags = get_the_tags() ? '</span>' . get_the_tag_list( '', $tax_separator, '' ) : __( 'No Tags', 'boozurk' ) . '</span>';
			$output .= '<li class="post-details-tags"><i class="icon-tags"></i> <span>' . __( 'Tags', 'boozurk' ) . ': ' . $tags . '</li>';

		if ( $args['date'] )
			$output .= '<li class="post-details-date"><i class="icon-time"></i> <span>' . __( 'Published', 'boozurk' ) . ': </span><a href="' . esc_url( get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) ) . '">' . get_the_time( get_option( 'date_format' ) ) . '</a></li>';

		$output .= '</ul>';

		if ( ! $args['echo'] )
			return $output;

		echo $output;

	}
}


// get the author badge
function boozurk_author_badge( $author = '', $size ) {

	if ( ! $author ) return;

	$name = get_the_author_meta( 'nickname', $author ); // nickname

	$avatar = get_avatar( $author, $size, 'Gravatar Logo', get_the_author_meta( 'user_nicename', $author ) . '-photo' ); // gravatar

	$description = get_the_author_meta( 'description', $author ); // bio

	$author_link = get_author_posts_url($author); // link to author posts

	$author_net = ''; // author social networks
	foreach ( array( 'twitter' => 'Twitter', 'facebook' => 'Facebook', 'googleplus' => 'Google+' ) as $s_key => $s_name ) {
		if ( get_the_author_meta( $s_key, $author ) ) $author_net .= '<a target="_blank" class="url" title="' . esc_attr( sprintf( __('Follow %s on %s', 'boozurk'), $name, $s_name ) ) . '" href="' . esc_url( get_the_author_meta( $s_key, $author ) ) . '"><img alt="' . esc_attr( $s_key ) . '" class="avatar" width="24" height="24" src="' . esc_url( get_template_directory_uri() . '/images/follow/' . $s_key . '.png' ) . '" /></a>';
	}

	$output = '<li class="author-avatar">' . $avatar . '</li>';
	$output .= '<li class="author-name"><a class="fn" href="' . esc_url( $author_link ) . '" >' . $name . '</a></li>';
	$output .= $description ? '<li class="author-description note">' . $description . '</li>' : '';
	$output .= $author_net ? '<li class="author-social">' . $author_net . '</li>' : '';

	$output = '<div class="tb-post-details tb-author-bio vcard"><ul>' . $output . '</ul></div>';

	return apply_filters( 'boozurk_filter_author_badge', $output );

}


//Displays the amount of time since a post or page was written in a nice friendly manner.
//Based on Plugin: Date in a nice tone (http://wordpress.org/extend/plugins/date-in-a-nice-tone/)
if ( !function_exists( 'boozurk_friendly_date' ) ) {
	function boozurk_friendly_date() {

		$postTime = get_the_time('U');
		$currentTime = time();
		$timeDifference = $currentTime - $postTime;

		$minInSecs = 60;
		$hourInSecs = 3600;
		$dayInSecs = 86400;
		$monthInSecs = $dayInSecs * 31;
		$yearInSecs = $dayInSecs * 366;

		//if over 2 years
		if ($timeDifference > ($yearInSecs * 2)) {
			$dateWithNiceTone = __( 'quite a long while ago...', 'boozurk' );

		//if over a year 
		} else if ($timeDifference > $yearInSecs) {
			$dateWithNiceTone = __( 'over a year ago', 'boozurk' );

		//if over 2 months
		} else if ($timeDifference > ($monthInSecs * 2)) {
			$num = round($timeDifference / $monthInSecs);
			$dateWithNiceTone = sprintf(__('%s months ago', 'boozurk' ),$num);
		
		//if over a month	
		} else if ($timeDifference > $monthInSecs) {
			$dateWithNiceTone = __( 'a month ago', 'boozurk' );
				   
		//if more than 2 days ago
		} else {
			$htd = human_time_diff( get_the_time('U'), current_time('timestamp') );
			$dateWithNiceTone = sprintf(__('%s ago', 'boozurk' ), $htd );
		} 
		
		return $dateWithNiceTone;
			
	}
}


/**
 * Create HTML list of nav menu items.
 * Replacement for the native Walker, using the thumbnail.
 *
 * @see http://wordpress.stackexchange.com/q/14037/
 * @author toscho, http://toscho.de
 */
class boozurk_Thumb_Walker extends Walker_Nav_Menu {

	/**
	 * @see Walker::start_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		if ( 0 == $depth ) {

			$thumb = $this->get_the_thumb( $item );

			if ( boozurk_get_opt( 'boozurk_main_menu' ) == 'thumbnail' )
				$title = $thumb;
			elseif ( boozurk_get_opt( 'boozurk_main_menu' ) == 'thumbnail and text' )
				$title = $thumb . $title;

		}

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

	}

	function get_the_thumb( $item ) {

		if ( $item->object == 'page' ) {

			$thumb = boozurk_get_the_thumb( array(
				'id'		=> (int)$item->object_id,
				'size_w'	=> (int)boozurk_get_opt( 'boozurk_main_menu_icon_size' ),
				'class'		=> 'tb-thumb-format',
			) );

		} elseif ( $item->object == 'category' ) {

			$thumb = '<i class="' . esc_attr( 'icon-' . (int)boozurk_get_opt( 'boozurk_main_menu_icon_size' ) . ' icon-folder-close' ) . '"></i>';

		} elseif ( $item->object == 'custom' ) {

			if ( $item->url ) {
				if ( substr( $item->url, 0, strlen(home_url())) == home_url() )
					$thumb = '<i class="' . esc_attr( 'icon-' . (int)boozurk_get_opt( 'boozurk_main_menu_icon_size' ) . ' icon-circle' ) . '"></i>';
				else
					$thumb = '<i class="' . esc_attr( 'icon-' . (int)boozurk_get_opt( 'boozurk_main_menu_icon_size' ) . ' icon-external-link' ) . '"></i>';
			} else {
				$thumb = '<i class="' . esc_attr( 'icon-' . (int)boozurk_get_opt( 'boozurk_main_menu_icon_size' ) . ' icon-circle-blank' ) . '"></i>';
			}

		} elseif ( $item->object == 'post_format' ) {

			$term = get_term($item->object_id, 'post_format' );
			$thumb = '<i class="' . esc_attr( 'tb-thumb-format icon-' . (int)boozurk_get_opt( 'boozurk_main_menu_icon_size' ) . ' ' . str_replace('post-format-', '', $term->slug ) ) . '"></i>';

		} else {

			$thumb = '<i class="' . esc_attr( 'icon-' . (int)boozurk_get_opt( 'boozurk_main_menu_icon_size' ) . ' icon-check-empty' ) . '"></i>';

		}

		return $thumb;

	}

}


// retrieve the post content, then die (for "post_expander" ajax request)
if ( !function_exists( 'boozurk_post_expander_show_post' ) ) {
	function boozurk_post_expander_show_post (  ) {

		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_content();
			}
		}

		die();

	}
}


//is a "post_expander" ajax request?
function boozurk_post_expander_activate ( ) {

	if ( isset( $_POST["boozurk_post_expander"] ) )
		add_action( 'wp', 'boozurk_post_expander_show_post' );

}


// retrieve the posts page, then die (for "infinite_scroll" ajax request)
if ( !function_exists( 'boozurk_infinite_scroll_show_page' ) ) {
	function boozurk_infinite_scroll_show_page (  ) {
		global $post, $wp_query, $paged;
		
		if ( !$paged ) {
			$paged = 1;
		}
		
		if ( have_posts() ) {
			echo '<div class="paged-separator" id="paged-separator-' . $paged . '"><h3>' . sprintf( __('Page %s','boozurk'), $paged ) . '</h3></div>';
			while ( have_posts() ) {
				the_post(); ?>
				<?php get_template_part( 'loop/post', boozurk_get_post_format( $post->ID ) ); ?>
			
			<?php } //end while ?>

			<div class="ajaxed bz-navigate navigate_archives" id="bz-page-nav">
				<div id="bz-page-nav-msg">
					<?php 
						echo __( 'Pages', 'boozurk' ); 
						echo '<a href="#posts_content">1</a>'; 
						for ($i=2; $i<=$paged; $i++) {
							echo '<a href="#paged-separator-' . $i . '">' . $i . '</a>';
						} 
					?>
				</div>
				<div id="bz-page-nav-subcont">
					<?php //num of pages
					previous_posts_link( '&laquo;' );
					printf( __( 'page %1$s of %2$s','boozurk' ), $paged, $wp_query->max_num_pages );
					?>
					<span id="bz-next-posts-link"><?php next_posts_link( '&raquo;' ); ?></span>
				</div>
				<div class="w_title"></div>
				<div id="bz-next-posts-button" class="hide-if-no-js">
					<input type="button" value="<?php echo __( 'Next Page', 'boozurk' ); ?>" />
				</div>
			</div>
		<?php 
		}
		die();
	}
}


//is a "infinite_scroll" ajax request?
function boozurk_infinite_scroll_activate ( ) {

	if ( isset( $_POST["boozurk_infinite_scroll"] ) )
		add_action( 'wp', 'boozurk_infinite_scroll_show_page' );

}


// add links to admin bar
if ( !function_exists( 'boozurk_admin_bar_plus' ) ) {
	function boozurk_admin_bar_plus() {
		global $wp_admin_bar;

		if (!is_super_admin() || !is_admin_bar_showing() || !current_user_can( 'edit_theme_options' ) ) return;

		$add_menu_meta = array(
			'target'    => '_blank'
		);

		$wp_admin_bar->add_menu( array(
			'id'		=> 'boozurk_theme_options',
			'parent'	=> 'appearance',
			'title'		=> __( 'Theme Options','boozurk' ),
			'href'		=> get_admin_url() . 'themes.php?page=boozurk_functions',
			'meta'		=> $add_menu_meta,
		) );

	}
}


// add the +snippet elements
if ( !function_exists( 'boozurk_plus_snippet' ) ) {
	function boozurk_plus_snippet(){

		if ( !is_singular() ) return;

		$_post = get_queried_object();

		if ( post_password_required($_post) ) return;

		$content = esc_attr( wp_trim_words( strip_shortcodes( $_post->post_content ) ) );
		if ($content == '') $content = __('read me!','boozurk');

?>
	<meta itemprop="name" content="<?php esc_attr( get_the_title() ); ?>" />
	<meta itemprop="description" content="<?php echo $content; ?>" />
	<?php if( has_post_thumbnail() ) { $image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id() ); ?><meta itemprop="image" content="<?php echo $image_attributes[0]; ?>" /><?php } ?>
<?php

	}
}


// comments navigation
if ( !function_exists( 'boozurk_navigate_comments' ) ) {
	function boozurk_navigate_comments(){

		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {

?>
	<div class="bz-navigate navigate_comments">
		<?php if(function_exists('wp_paginate_comments')) {
			wp_paginate_comments();
		} else {
			paginate_comments_links();
		} ?>
		<br class="fixfloat" />
	</div>
<?php 

		}

	}
}


// comments-are-closed message when post type supports comments and we're not on a page
function boozurk_comments_closed() {
	if ( ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) {

?>
	<div class="comments_note"><?php _e( 'Comments are closed', 'boozurk' ); ?></div>
<?php

	}
}


//the comments link ( based on http://core.trac.wordpress.org/browser/tags/3.5.1/wp-includes/comment-template.php#L968 )
function boozurk_comments_link( $args = '' ) {

	$defaults = array(
		'zero'	=> __( 'No Comments', 'boozurk' ),
		'one'	=> __( '1 Comment', 'boozurk' ),
		'more'	=> __( '% Comments', 'boozurk' ),
		'none'	=> __( 'Comments are closed', 'boozurk' ),
		'id'	=> get_the_ID(),
		'class'	=> '',
		'echo'	=> 1,
	);

	$args = wp_parse_args( $args, $defaults );

	$number = get_comments_number( $args['id'] );

	$css_class = ( ! empty( $args['class'] ) ) ? ' class="' . esc_attr( $args['class'] ) . '"' : '';

	if ( 0 == $number && !comments_open( $args['id'] ) ) {

		$output = '<span title="' . esc_attr__( 'Comments are closed', 'boozurk' ) . '"' . $css_class . '>' . $args['none'] . '</span>';

	} elseif ( post_password_required( $args['id'] ) ) {

		$output = '<span title="' . esc_attr__( 'Enter your password to view comments', 'boozurk' ) . '"' . $css_class . '><i class="icon-ban-circle"></i></span>';

	} else {

		if ( 0 == $number )
			$href = get_permalink( $args['id'] ) . '#respond';
		else
			$href = get_comments_link( $args['id'] );

		$title = ' title="' . esc_attr( sprintf( __( 'Comments on %s', 'boozurk' ), strip_tags( get_the_title( $args['id'] ) ) ) ) . '"';

		if ( $number > 1 )
			$text = str_replace( '%', number_format_i18n( $number ), $args['more'] );
		elseif ( $number == 0 )
			$text = $args['zero'];
		else // must be one
			$text = $args['one'];
		$text = apply_filters( 'comments_number', $text, $number );

		$output = '<a href="' . esc_url( $href ) . '"' . $css_class . $title . '>' . $text . '</a>';

	}

	$output = apply_filters( 'boozurk_comments_link', $output );

	if ( $args['echo'] )
		echo $output;
	else
		return $output;

}


//enqueue the 'comment-reply' script
function boozurk_enqueue_comments_reply() {

	if( get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

}


// Custom form fields for the comment form
function boozurk_comments_form_fields( $fields ) {

	$commenter	= wp_get_current_commenter();
	$req		= get_option( 'require_name_email' );
	$aria_req	= ( $req ? " aria-required='true'" : '' );

	$custom_fields =  array(
		'author' => '<p class="comment-form-author">' . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />' .
					'<label for="author">' . __( 'Name', 'boozurk' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .'</p>',
		'email'  => '<p class="comment-form-email">' . '<input id="email" name="email" type="text" value="' . sanitize_email(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />' .
					'<label for="email">' . __( 'Email', 'boozurk' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .'</p>',
		'url'    => '<p class="comment-form-url">' . '<input id="url" name="url" type="text" value="' . esc_url( $commenter['comment_author_url'] ) . '" size="30" />' .
					'<label for="url">' . __( 'Website', 'boozurk' ) . '</label>' .'</p>',
	);

	return $custom_fields;

}


// filters comments_form() default arguments
function boozurk_comment_form_defaults( $defaults ) {

	$defaults['label_submit']		= __( 'Say It!','boozurk' );
	$defaults['comment_field']		= '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="7" aria-required="true"></textarea></p>';
	$defaults['cancel_reply_link']	= '<i class="icon-remove-circle"></i>';

	return $defaults;

}


// add a random color to every new category
function boozurk_created_category_color( $term_id, $tt_id = null, $taxonomy = null ) {
	global $boozurk_opt;

	$hexnumber = '#';

	for ($i2=1; $i2<=3; $i2++) {
		$hexnumber .= dechex( rand(64,255) );
	}

	$boozurk_opt['boozurk_cat_colors'][$term_id] = $hexnumber;

	update_option( 'boozurk_options', $boozurk_opt );

}


//custom smiles
function boozurk_smiles_replace( $src, $img ) {

	if ( boozurk_get_opt( 'boozurk_smilies' ) ) return get_template_directory_uri() . '/images/smilies/' . $img;

	return $src;
}


// add a fix for embed videos
if ( !function_exists( 'boozurk_wmode_transparent' ) ) {
	function boozurk_wmode_transparent($html, $url = null, $attr = null) {

		if ( strpos( $html, '<embed ' ) !== false ) {

			$html = str_replace('</param><embed', '</param><param name="wmode" value="transparent"></param><embed', $html);
			$html = str_replace('<embed ', '<embed wmode="transparent" ', $html);
			return $html;

		} elseif ( strpos ( $html, 'feature=oembed' ) !== false )

			return str_replace( 'feature=oembed', 'feature=oembed&wmode=transparent', $html );

		else

			return $html;

	}
}


// custom image caption
function boozurk_img_caption_shortcode( $deprecated, $attr, $content = null ) {

	extract(shortcode_atts(array(
		'id'		=> '',
		'align'		=> 'alignnone',
		'width'		=> '',
		'caption'	=> '',
	), $attr));

	if ( 1 > (int) $width || empty($caption) )
		return $content;

	if ( $id ) $id = 'id="' . esc_attr($id) . '" ';

	return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . $width . 'px"><div class="wp-caption-inside">'
	. do_shortcode( $content ) . '<div class="wp-caption-text">' . $caption . '</div></div></div>';

}


// convert post content in blockquote for quote format posts)
function boozurk_quote_content( $content ) {

	/* Check if we're displaying a 'quote' post. */
	if ( has_post_format( 'quote' ) && boozurk_get_opt( 'boozurk_post_formats_quote' ) ) {

		/* Match any <blockquote> elements. */
		preg_match( '/<blockquote.*?>/', $content, $matches );

		/* If no <blockquote> elements were found, wrap the entire content in one. */
		if ( empty( $matches ) )
			$content = "<blockquote>{$content}</blockquote>";

	}

	return $content;

}


// Add specific CSS class by filter
function boozurk_body_classes( $classes ) {

	$classes[] = 'no-js';

	if ( has_nav_menu( 'secondary1' ) ) $classes[] = 'top-menu';

	if ( ! is_active_widget( false, false, 'bz-navbuttons', true ) ) $classes[] = 'fixed-navigation';

	if ( ! ( boozurk_get_opt( 'boozurk_sidebar_primary' ) == 'hidden' ) ) $classes[] = 'primary-sidebar';

	if ( ! ( boozurk_get_opt( 'boozurk_sidebar_secondary' ) == 'hidden' ) ) $classes[] = 'secondary-sidebar';

	return $classes;

}

// Add specific CSS class by filter
function boozurk_post_classes( $classes ) {

	$classes[] = 'post-element';

	return $classes;

}add_filter('post_class','boozurk_post_classes');

// custom gallery shortcode function
function boozurk_gallery_shortcode( $output, $attr ) {

	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) )
			$attr['orderby'] = 'post__in';
		$attr['include'] = $attr['ids'];
	}

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract( shortcode_atts( array(
		'order'			=> 'ASC',
		'orderby'		=> 'menu_order ID',
		'id'			=> $post->ID,
		'itemtag'		=> 'dl',
		'icontag'		=> 'dt',
		'captiontag'	=> 'dd',
		'columns'		=> 3,
		'size'			=> 'thumbnail',
		'include'		=> '',
		'exclude'		=> '',
	), $attr) );

	$id = intval( $id );
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( ! empty( $include ) ) {
		$_attachments = get_posts( array( 'include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( ! empty( $exclude ) ) {
		$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
	} else {
		$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
	}

	if ( isset( $attr['preview'] ) && $attr['preview'] )
		return boozurk_gallery_preview_walker( $attachments, $id );

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;

	$selector = "gallery-{$instance}";

	$size_class = sanitize_html_class( $size );
	$output = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

	$i = 0;
	if ( boozurk_get_opt( 'boozurk_js_thickbox_force' ) ) $attr['link'] = 'file';
	foreach ( $attachments as $id => $attachment ) {
		$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
			<{$icontag} class='gallery-icon'>
				$link
			</{$icontag}>";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= '<br style="clear: both" />';
	}

	$output .= "
			<br style='clear: both;' />
		</div>\n";

	return $output;
}


//add attachment description to thickbox
function boozurk_get_attachment_link( $markup = '', $id = 0, $size = 'thumbnail', $permalink = false, $icon = false, $text = false ) {

	$id = intval( $id );
	$_post = get_post( $id );

	if ( empty( $_post ) || ( 'attachment' != $_post->post_type ) || ! $url = wp_get_attachment_url( $_post->ID ) )
		return __( 'Missing Attachment','boozurk' );

	if ( $permalink )
		$url = get_attachment_link( $_post->ID );

	$url = esc_url( $url );

	$post_title = esc_attr( $_post->post_excerpt ? $_post->post_excerpt : $_post->post_title );

	if ( $text )
		$link_text = $text;
	elseif ( $size && 'none' != $size )
		$link_text = wp_get_attachment_image( $id, $size, $icon );
	else
		$link_text = '';

	if ( trim( $link_text ) == '' )
		$link_text = $_post->post_title;

	return "<a href='$url' title='$post_title'>$link_text</a>";

}


//print credits
function boozurk_get_credits() {

	$credits = apply_filters( 'boozurk_credits', '&copy; ' . date( 'Y' ) . ' <strong>' . get_bloginfo( 'name' ) . '</strong>. ' . __( 'All rights reserved','boozurk' ) );

	if ( boozurk_get_opt('boozurk_tbcred') )
		$credits .= '<br />' . sprintf( __('Powered by %s and %s','boozurk'), '<a target="_blank" href="http://wordpress.org/" title="WordPress">WordPress</a>', '<a target="_blank" href="http://www.twobeers.net/" title="' . esc_attr( __( 'Visit theme authors homepage','boozurk' ) . ' @ twobeers.net' ) . '">' . esc_attr( __( 'Boozurk theme','boozurk' ) ) . '</a>');

	if ( boozurk_get_opt('boozurk_mobile_css') )
		$credits .= '<span class="hide_if_print"> - <a rel="nofollow" href="' . esc_url( home_url() . '?mobile_override=mobile' ) . '">'. __('Mobile View','boozurk') .'</a></span>';

	return $credits;

}


//filter wp_title
function boozurk_filter_wp_title( $title ) {

	if ( is_single() && empty( $title ) ) {
		$_post = get_queried_object();
		$title = boozurk_titles_filter( '', $_post->ID ) . ' &laquo; ';
	}

	// Get the Site Name
	$site_name = get_bloginfo( 'name' );

	// Append name
	$filtered_title = $title . $site_name;

	// If site front page, append description
	if ( is_front_page() ) {
		// Get the Site Description
		$site_description = get_bloginfo( 'description' );
		// Append Site Description to title
		$filtered_title .= ' - ' . $site_description;
	}

	// Return the modified title
	return $filtered_title;

}


//Add new contact methods to author panel
function boozurk_new_contactmethods( $contactmethods ) {

	$contactmethods['twitter']		= 'Twitter'; //add Twitter

	$contactmethods['facebook']		= 'Facebook'; //add Facebook

	$contactmethods['googleplus']	= 'Google+'; //add Google+

	return $contactmethods;

}


// add 'quoted on' before trackback/pingback comments link
function boozurk_add_quoted_on( $return ) {
	global $comment;

	$text = '';
	if ( get_comment_type() != 'comment' )
		$text = '<span style="font-weight: normal;">' . __( 'quoted on', 'boozurk' ) . ' </span>';

	return $text . $return;

}


// strip tags and apply title format for blank titles
function boozurk_titles_filter( $title, $id = null ) {

	if ( is_admin() ) return $title;

	$title = strip_tags( $title, '<abbr><acronym><em><i><del><ins><bdo><strong><a>' );

	if ( $id == null ) return $title;

	if ( ! boozurk_get_opt( 'boozurk_blank_title' ) ) return $title;

	if ( empty( $title ) ) {

		if ( ! boozurk_get_opt( 'boozurk_blank_title_text' ) ) return __( '(no title)', 'boozurk' );
		$postdata = array( get_post_format( $id )? get_post_format_string( get_post_format( $id ) ): __( 'Post', 'boozurk' ), get_the_time( get_option( 'date_format' ), $id ), $id );
		$codes = array( '%f', '%d', '%n' );

		return str_replace( $codes, $postdata, boozurk_get_opt( 'boozurk_blank_title_text' ) );

	} else

		return $title;

}


//set the excerpt length
function boozurk_excerpt_length( $length ) {

	return (int) boozurk_get_opt( 'boozurk_excerpt_length' );

}


// use the "excerpt more" string as a link to the post
function boozurk_excerpt_more( $more ) {

	if ( is_admin() ) return $more;

	if ( boozurk_get_opt( 'boozurk_excerpt_more_txt' ) )
		$more = boozurk_get_opt( 'boozurk_excerpt_more_txt' );

	if ( boozurk_get_opt( 'boozurk_excerpt_more_link' ) )
		$more = '<a href="' . esc_url( get_permalink() ) . '">' . $more . '</a>';

	return $more;

}


// custom text for the "more" tag
function boozurk_more_link( $more_link, $more_link_text ) {

	if ( boozurk_get_opt( 'boozurk_more_tag' ) && !is_admin() ) {

		$text = str_replace ( '%t', get_the_title(), boozurk_get_opt( 'boozurk_more_tag' ) );

		$more_link = str_replace( $more_link_text, $text, $more_link );

	}

	if ( boozurk_get_opt( 'boozurk_more_tag_scroll' ) ) $more_link = preg_replace( '|#more-[0-9]+|', '', $more_link );

	return $more_link;

}


/**
 * Add parent class to wp_page_menu top parent list items
 */
function boozurk_add_parent_class( $css_class, $page, $depth, $args ) {

	if ( ! empty( $args['has_children'] ) && $depth == 0 )
		$css_class[] = 'menu-item-parent';

	return $css_class;

}


/**
 * Add parent class to wp_nav_menu top parent list items
 */
function boozurk_add_menu_parent_class( $items ) {

	$parents = array();
	foreach ( $items as $item ) {
		if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
			$parents[] = $item->menu_item_parent;
		}
	}

	foreach ( $items as $item ) {
		if ( in_array( $item->ID, $parents ) ) {
			if ( ! $item->menu_item_parent )
				$item->classes[] = 'menu-item-parent'; 
		}
	}

	return $items;    
}


/**
 * Add 'selected' class to current page item in menus
 */
function boozurk_add_selected_class_to_page_item( $css_class ) {

	if ( in_array( 'current_page_item', $css_class ) ) $css_class[] = 'selected';

	return $css_class;    
}


/**
 * Add 'selected' class to current menu item in menus
 */
function boozurk_add_selected_class_to_menu_item( $classes, $item ) {

	if ( $item->current )
		$classes[] = 'selected';

	return $classes;    
}


// wrap the categories count with a span
function boozurk_wrap_categories_count( $output ) {
	$pattern = '/<\/a>\s(\(\d+\))/i';
	$replacement = ' <span class="details">$1</span></a>';
	return preg_replace( $pattern, $replacement, $output );
}


// show all categories list (redirect to allcat.php if allcat=y)
if ( !function_exists( 'boozurk_allcat' ) ) {
	function boozurk_allcat () {

		if( boozurk_is_allcat() ) {

			get_template_part( 'allcat' );

			exit;

		}

	}
}


//replace the comment_reply_link text
function boozurk_comment_reply_link( $link ) {

	preg_match_all( '/<a\b[^>]*>(.*?)<\/a>/',$link, $text );

	if ( isset( $text[1][0] ) )
		$link = str_replace( '>' . $text[1][0], ' title="' . esc_attr__( 'Reply to comment', 'boozurk' ) . '" ><i class="icon icon-share-alt"></i>', $link);

	return $link;

}


//replace the cancel_comment_reply_link text
function boozurk_cancel_comment_reply_link( $link ) {

	$link = str_replace( 'id="cancel-comment-reply-link"', 'id="cancel-comment-reply-link" title="' . esc_attr__( 'Cancel reply', 'boozurk' ) . '"', $link);

	return $link;

}


//replace the edit_comment_link text
function boozurk_edit_comment_link( $link ) {

	preg_match_all( '/<a\b[^>]*>(.*?)<\/a>/',$link, $text );

	if ( isset( $text[1][0] ) )
		$link = str_replace( $text[1][0], '<i class="icon icon-pencil"></i>', $link);

	return $link;

}


// display the second secondary menu
function boozurk_primary_menu() {

	wp_nav_menu( array(
		'container'			=> false,
		'menu_id'			=> 'mainmenu',
		'menu_class'		=> 'nav-menu',
		'fallback_cb'		=> 'boozurk_pages_menu',
		'theme_location'	=> 'primary',
		'walker'			=> new boozurk_Thumb_Walker,
	) );

}


// display the first secondary menu
function boozurk_1st_secondary_menu() {

	wp_nav_menu( array(
		'container'			=> false,
		'menu_id'			=> 'secondary1',
		'menu_class'		=> 'nav-menu',
		'fallback_cb'		=> false,
		'theme_location'	=> 'secondary1',
		'depth'				=> 1,
	) );

}


// display the second secondary menu
function boozurk_2nd_secondary_menu() {

	wp_nav_menu( array(
		'container'			=> false,
		'menu_id'			=> 'secondary2',
		'menu_class'		=> 'nav-menu',
		'fallback_cb'		=> false,
		'theme_location'	=> 'secondary2',
		'depth'				=> 1,
	) );

}


//add "Home" link
function boozurk_add_home_link( $items = '', $args = null ) {

	if ( ! $items ) return $items;

	$defaults = array(
		'theme_location'	=> 'undefined',
		'before'			=> '',
		'after'				=> '',
		'link_before'		=> '',
		'link_after'		=> '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( ( $args['theme_location'] === 'primary' ) && ( 'posts' == get_option( 'show_on_front' ) ) ) {
		if ( is_front_page() || is_single() )
			$class = ' selected';
		else
			$class = '';

		$homeMenuItem =
				'<li class="navhome' . $class . '">' .
				$args['before'] .
				'<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr__( 'Home', 'boozurk' ) . '">' .
				$args['link_before'] . __( 'Home', 'boozurk' ) . $args['link_after'] .
				'</a>' .
				$args['after'] .
				'</li>';

		$items = $homeMenuItem . $items;
	}

	return $items;

}


// Display search form.
function boozurk_search_form() {

	$form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/' ) ) . '" >
	<div>
		<label class="screen-reader-text" for="s">Search for:</label>
		<input type="text" value="' . get_search_query() . '" name="s" id="s" />
		<button type="submit">
			<i class="icon-search"></i>
		</button>
		</div>
	</form>';

	return $form;

}


// Pages Menu
if ( !function_exists( 'boozurk_pages_menu' ) ) {
	function boozurk_pages_menu() {

		$menu = boozurk_add_home_link( $items = wp_list_pages( 'sort_column=menu_order&title_li=&echo=0' ), $args = 'theme_location=primary' );

		if ( ! $menu ) return;

		echo '<ul id="mainmenu" class="nav-menu">' . $menu . '</ul>';

	}
}


// display the logo and site description
function boozurk_secondary_sidebar_top() {

	if ( boozurk_get_opt( 'boozurk_logo' ) )
		echo '<img class="bz-logo" alt="logo" src="' . esc_url( boozurk_get_opt( 'boozurk_logo' ) ) . '" />';

	if ( boozurk_get_opt( 'boozurk_logo_description' ) )
		echo '<div class="bz-description">' . get_bloginfo( 'description' ) . '</div>';

}


// display the header widget area
function boozurk_header_widget_area() {

	get_sidebar( 'header' );

}


