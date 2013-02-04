<?php
add_action( 'after_setup_theme', 'boozurk_setup' ); // tell WordPress to run boozurk_setup() when the 'after_setup_theme' hook is run.
add_action( 'widgets_init', 'boozurk_widget_area_init' ); // Register sidebars by running boozurk_widget_area_init() on the widgets_init hook
add_action( 'wp_enqueue_scripts', 'boozurk_stylesheet' ); // Add stylesheets
add_action( 'wp_head', 'boozurk_custom_style' ); // Add custom style
add_action( 'wp_enqueue_scripts', 'boozurk_scripts' ); // Add js scripts
add_action( 'wp_footer', 'boozurk_initialize_scripts' ); // start js scripts
add_action( 'admin_menu', 'boozurk_create_menu' ); // Add admin menus
add_action( 'init', 'boozurk_post_expander_activate' ); // post expander ajax request
add_action( 'init', 'boozurk_infinite_scroll_activate' ); // infinite scroll ajax request
add_action( 'tha_entry_before', 'boozurk_print_details' ); // boozurk_print_details
add_action( 'admin_bar_menu', 'boozurk_admin_bar_plus', 999 ); // add links to admin bar
add_action( 'wp_head', 'boozurk_plus_snippet' ); // localize js scripts
add_action( 'created_category', 'boozurk_created_category_color' ); // add a random color to every new category
add_action( 'comment_form_comments_closed', 'boozurk_comments_closed' ); // comments-are-closed message
add_action( 'tha_entry_before', 'boozurk_navigate_attachments' ); // boozurk_navigate_attachments
add_action( 'tha_entry_before', 'boozurk_single_nav' ); // boozurk_single_nav
add_action( 'tha_entry_after', 'boozurk_single_widgets_area' ); // boozurk_single_widgets_area
add_action( 'tha_entry_bottom', 'boozurk_link_pages' ); // boozurk_link_pages


// Custom filters
add_filter( 'post_gallery', 'boozurk_gallery_shortcode', 10, 2 );
add_filter( 'embed_oembed_html', 'boozurk_wmode_transparent', 10, 3);
add_filter( 'img_caption_shortcode', 'boozurk_img_caption_shortcode', 10, 3 );
add_filter( 'the_content', 'boozurk_quote_content' );
add_filter( 'smilies_src', 'boozurk_smiles_replace',10,2 ); //custom smiles
add_filter( 'body_class' , 'boozurk_body_classes' );
add_filter( 'comment_form_default_fields', 'boozurk_comments_form_fields');
add_filter( 'comment_form_defaults', 'boozurk_comment_form_defaults' );
add_filter( 'wp_get_attachment_link', 'boozurk_get_attachment_link', 10, 6 );


$boozurk_opt = get_option( 'boozurk_options' );

$boozurk_is_mobile_browser = false;

// load modules (accordingly to http://justintadlock.com/archives/2010/11/17/how-to-load-files-within-wordpress-themes)
require_once( 'lib/options.php' ); // load options
require_once( 'lib/the_bird.php' ); // load "the bird" core functions
require_once('tha/tha-theme-hooks.php'); // load the Theme Hook Alliance hook stub list
require_once('lib/hooks.php'); // load the custom hooks module
if ( boozurk_get_opt( 'boozurk_mobile_css' ) ) require_once( 'mobile/core-mobile.php' ); // load mobile functions
if ( boozurk_get_opt( 'boozurk_js_swfplayer' ) ) require_once( 'lib/audio-player.php' ); // load the audio player module
if ( boozurk_get_opt( 'boozurk_custom_widgets' ) ) require_once('lib/widgets.php'); // load the custom widgets module
if ( boozurk_get_opt( 'boozurk_comment_style' ) ) require_once('lib/custom_comments.php'); // load the comment style module
require_once('lib/custom-header.php'); // load the custom header module
require_once('lib/breadcrumb.php'); // load the breadcrumb module

// Set the content width based on the theme's design
if ( ! isset( $content_width ) ) {
	$content_width = 560;
}

if ( !function_exists( 'boozurk_widget_area_init' ) ) {
	function boozurk_widget_area_init() {

		// Area 0, in the left sidebar.
		register_sidebar( array(
			'name' => __( 'Primary Sidebar', 'boozurk' ),
			'id' => 'primary-widget-area',
			'description' => __( 'The primary sidebar widget area', 'boozurk' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="w_title">',
			'after_title' => '</div>',
		) );

		// Area 1, in the right sidebar.
		register_sidebar( array(
			'name' => __( 'Secondary sidebar', 'boozurk' ),
			'id' => 'fixed-widget-area',
			'description' => __( 'The secondary sidebar widget area', 'boozurk' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="w_title">',
			'after_title' => '</div>',
		) );

		// Area 2, located under the main menu.
		register_sidebar( array(
			'name' => __( 'Menu Widget Area', 'boozurk' ),
			'id' => 'header-widget-area',
			'description' => __( 'The widget area under the main menu', 'boozurk' ),
			'before_widget' => '<div class="bz-widget"><div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div></div>',
			'before_title' => '<div class="w_title">',
			'after_title' => '</div>',
		) );
	
		// Area 3, located in the footer. Empty by default.
		register_sidebar( array(
			'name' => __( 'First Footer Widget Area', 'boozurk' ),
			'id' => 'first-footer-widget-area',
			'description' => __( 'The first footer widget area', 'boozurk' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="w_title">',
			'after_title' => '</div>',
		) );
	
		// Area 4, located in the footer. Empty by default.
		register_sidebar( array(
			'name' => __( 'Second Footer Widget Area', 'boozurk' ),
			'id' => 'second-footer-widget-area',
			'description' => __( 'The second footer widget area', 'boozurk' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="w_title">',
			'after_title' => '</div>',
		) );
	
		// Area 5, located in the footer. Empty by default.
		register_sidebar( array(
			'name' => __( 'Third Footer Widget Area', 'boozurk' ),
			'id' => 'third-footer-widget-area',
			'description' => __( 'The third footer widget area', 'boozurk' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="w_title">',
			'after_title' => '</div>',
		) );
	
		// Area 6, located in page 404.
		register_sidebar( array(
			'name' => __( 'Page 404', 'boozurk' ),
			'id' => '404-widgets-area',
			'description' => __( 'Enrich the page 404 with some useful widgets', 'boozurk' ),
			'before_widget' => '<div class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="w_title">',
			'after_title' => '</div>',
		) );
		// Area 7, located after the post body.
		register_sidebar( array(
			'name' => __( 'Post Widget Area', 'boozurk' ),
			'id' => 'single-widgets-area',
			'description' => __( 'a widget area located after the post body', 'boozurk' ),
			'before_widget' => '<div class="bz-widget"><div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div></div>',
			'before_title' => '<div class="w_title">',
			'after_title' => '</div>',
		) );
	}
}

// Add style element for custom theme options
if ( !function_exists( 'boozurk_custom_style' ) ) {
	function boozurk_custom_style(){
		global $boozurk_is_mobile_browser;
		if ( $boozurk_is_mobile_browser ) return; // skip if in mobile view
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
	a {
		color: <?php echo boozurk_get_opt( 'boozurk_colors_link' ); ?>;
	}
	a:hover,
	ul li:hover .hiraquo,
	.current-menu-item a:hover,
	.current_page_item a:hover,
	.current-cat a:hover {
		color: <?php echo boozurk_get_opt( 'boozurk_colors_link_hover' ); ?>;
	}
	.current-menu-item > a,
	.current_page_item > a,
	.current-cat > a,
	li.current_page_ancestor .hiraquo {
		color: <?php echo boozurk_get_opt( 'boozurk_colors_link_sel' ); ?>;
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
	.storycontent img.size-full {
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
		if ( boozurk_get_opt( 'boozurk_js_post_expander' ) )	$modules[] = 'postexpander';
		if ( boozurk_get_opt( 'boozurk_js_thickbox' ) )			$modules[] = 'thickbox';
		if ( boozurk_get_opt( 'boozurk_js_tooltips' ) )			$modules[] = 'tooltips';
		if ( boozurk_get_opt( 'boozurk_plusone' ) )				$modules[] = 'plusone';
		$modules[] = 'resizevideo';

		if ( !$afterajax ) {
			$modules[] = 'animatemenu';
			$modules[] = 'scrolltopbottom';
			if ( is_singular() && comments_open() )
				$modules[] = 'commentvariants';
			if ( ( boozurk_get_opt( 'boozurk_quotethis' ) ) && is_singular() )
				$modules[] = 'quotethis';
			if ( ( boozurk_get_opt( 'boozurk_infinite_scroll' ) ) && !is_singular() && !is_404() )
				$modules[] = 'infinitescroll';
		}

		$modules = implode(',', $modules);

		return $modules;

	}
}

// initialize js
if ( !function_exists( 'boozurk_initialize_scripts' ) ) {
	function boozurk_initialize_scripts() {
		global $boozurk_is_mobile_browser, $boozurk_is_printpreview;

		if ( is_admin() || $boozurk_is_mobile_browser ) return;
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
<?php if ( !boozurk_get_opt( 'boozurk_jsani' ) || $boozurk_is_printpreview ) return; ?>

<?php if ( boozurk_get_opt( 'boozurk_plusone' ) ) { ?>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
  {parsetags: 'explicit'}
</script>
<?php } ?>

<?php
	}
}

// Add stylesheets to page
if ( !function_exists( 'boozurk_stylesheet' ) ) {
	function boozurk_stylesheet(){
		global $boozurk_version, $boozurk_is_mobile_browser, $boozurk_is_printpreview;

		if ( is_admin() || $boozurk_is_mobile_browser ) return;

		if ( $boozurk_is_printpreview ) { //print preview

			wp_enqueue_style( 'bz_general-style', get_template_directory_uri() . '/css/print.css', false, $boozurk_version, 'screen' );
			wp_enqueue_style( 'bz_preview-style', get_template_directory_uri() . '/css/print_preview.css', false, $boozurk_version, 'screen' );

		} else { //normal view

			wp_enqueue_style( 'bz_general-style', get_stylesheet_uri(), array('thickbox'), $boozurk_version, 'screen' );

		}

		//google font
		if ( boozurk_get_opt( 'boozurk_google_font_family' ) ) wp_enqueue_style( 'bz-google-fonts', 'http://fonts.googleapis.com/css?family=' . urlencode( boozurk_get_opt( 'boozurk_google_font_family' ) ) );

		wp_enqueue_style( 'bz_print-style', get_template_directory_uri() . '/css/print.css', false, $boozurk_version, 'print' );

	}
}

// add scripts
if ( !function_exists( 'boozurk_scripts' ) ) {
	function boozurk_scripts(){
		global $boozurk_version, $boozurk_is_mobile_browser, $boozurk_is_printpreview;

		if ( is_admin() || $boozurk_is_mobile_browser || $boozurk_is_printpreview ) return;

		if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); // comment-reply js

		$deps = array('jquery');
		if ( boozurk_get_opt( 'boozurk_js_thickbox' ) ) $deps[] = 'thickbox';
		if ( boozurk_get_opt( 'boozurk_jsani' ) ) wp_enqueue_script( 'boozurk-script', get_template_directory_uri() . '/js/boozurk.min.js', $deps, $boozurk_version, true );

		$data = array(
			'script_modules' => boozurk_get_js_modules(),
			'script_modules_afterajax' => boozurk_get_js_modules(1),
			'post_expander' => __( 'Post loading, please wait...','boozurk' ),
			'gallery_preview' => __( 'Preview','boozurk' ),
			'gallery_click' => __( 'Click on thumbnails','boozurk' ),
			'infinite_scroll' => __( 'Page is loading, please wait...','boozurk' ),
			'infinite_scroll_end' => __( 'No more posts beyond this line','boozurk' ),
			'infinite_scroll_type' => boozurk_get_opt( 'boozurk_infinite_scroll_type' ),
			'quote_tip' => esc_attr( __( 'Add selected text as a quote', 'boozurk' ) ),
			'quote' => __( 'Quote', 'boozurk' ),
			'quote_alert' => __( 'Nothing to quote. First of all you should select some text...', 'boozurk' ),
			'comments_closed' => __( 'Comments closed', 'boozurk' )
		);
		wp_localize_script( 'boozurk-script', 'boozurk_l10n', $data );

	}
}

// post-top date, tags and categories
if ( !function_exists( 'boozurk_print_details' ) ) {
	function boozurk_print_details() {

		if ( is_singular() ) return;

		if ( boozurk_get_opt( 'boozurk_post_date' ) )
			echo '<div class="bz-post-top-date fixfloat">' . get_the_time( get_option( 'date_format' ) ) . '</div>';

		if ( boozurk_get_opt( 'boozurk_post_cat' ) ) {
			echo '<div class="bz-post-top-cat fixfloat">';
			the_category(', ');
			echo '</div>';
		}

		if ( boozurk_get_opt( 'boozurk_post_tag' ) ) {
			echo '<div class="bz-post-top-tag fixfloat">';
			the_tags(' ');
			echo '</div>';
		}
	}
}

// Pages Menu
if ( !function_exists( 'boozurk_pages_menu' ) ) {
	function boozurk_pages_menu() {
		echo '<ul id="mainmenu">';
		wp_list_pages( 'sort_column=menu_order&title_li=' ); // menu-order sorted
		echo '</ul>';
	}
}

// display the post title with the featured image
if ( !function_exists( 'boozurk_featured_title' ) ) {
	function boozurk_featured_title( $args = '' ) {
		global $post;
		
		$defaults = array( 'alternative' => '', 'fallback' => '', 'featured' => true, 'href' => get_permalink(), 'target' => '', 'title' => the_title_attribute( array('echo' => 0 ) ) );
		$args = wp_parse_args( $args, $defaults );
		
		$post_title = $args['alternative'] ? $args['alternative'] : get_the_title();
		$post_title = $post_title ? $post_title : $args['fallback'];
		$link_target = $args['target'] ? ' target="'.$args['target'].'"' : '';
		$title_content = is_singular() ? $post_title : '<a title="' . esc_attr( $args['title'] ) . '" href="' . $args['href'] . '"' . $link_target . ' rel="bookmark">' . $post_title . '</a>';
		if ( $post_title ) $post_title = '<h2 class="storytitle">' . $title_content . '</h2>';
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
	function boozurk_extrainfo( $comm = true ) {
		global $post;
		// extra info management

		?>
		<div class="post_meta_container">
			<a class="pmb_format tb-thumb-format" href="<?php the_permalink(); ?>" rel="bookmark"></a>
			<?php
				$page_cd_nc = ( is_page() && !comments_open() && !have_comments() ); //true if page with comments disabled and no comments
				if ( !$page_cd_nc ) {
					if( $comm && !post_password_required() ) comments_popup_link( '0', '1', '%', 'pmb_comm', '-'); // number of comments
				}
			?>
			<?php if ( boozurk_get_opt( 'boozurk_plusone' ) ) { ?>
				<div class="bz-plusone-wrap"><div class="g-plusone" data-annotation="none" data-href="<?php the_permalink(); ?>"></div></div>
			<?php } ?>
		</div>
		<?php if ( !is_singular() ) edit_post_link(); ?>
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

		if ( $comments ) { ?>
			<div class="bz-last-cop fixfloat">
				<span class="item-label"><?php _e('last comments','boozurk'); ?></span>
				<span class="bz-breadcrumb-sep item-label">&nbsp;</span>
				<?php echo $ellipsis; ?>
				<?php foreach ( $comments as $comment ) { ?>
					<div class="item">
						<?php echo get_avatar( $comment, 32, $default=get_option('avatar_default'), $comment->comment_author );?>
						<div class="bz-tooltip bz-300"><div class="bz-tooltip-inner">
							<?php echo $comment->comment_author; ?>
							<br/><br/>
							<?php comment_excerpt( $comment->comment_ID ); ?>
						</div></div>
					</div>
				<?php } ?>
				<div class="fixfloat"></div>
			</div>
		<?php }
	}
}

if (!function_exists('boozurk_navbuttons')) {
	function boozurk_navbuttons( $print = 1, $comment = 1, $feed = 1, $trackback = 1, $home = 1, $next_prev = 1, $up_down = 1, $fixed = 1 ) {
		global $post, $boozurk_is_allcat_page;
		
		$is_post = is_single() && !is_attachment() && !$boozurk_is_allcat_page;
		$is_image = is_attachment() && !$boozurk_is_allcat_page;
		$is_page = is_singular() && !is_single() && !is_attachment() && !$boozurk_is_allcat_page;
		$is_singular = is_singular() && !$boozurk_is_allcat_page;
	?>

<div id="navbuttons"<?php if ( $fixed ) echo ' class="fixed"'; ?>>

		<?php if ( $is_singular && get_edit_post_link() ) { 												// ------- Edit ------- ?>
			<div class="minibutton" title="<?php esc_attr_e( 'Edit','boozurk' ); ?>">
				<a rel="nofollow" href="<?php echo get_edit_post_link(); ?>">
					<span class="minib_img minib_edit">&nbsp;</span>
				</a>
			</div>
		<?php } ?>
		
		<?php if ( $print && $is_singular ) { 																// ------- Print ------- ?>
			<div class="minibutton" title="<?php esc_attr_e( 'Print','boozurk' ); ?>">
				<a rel="nofollow" href="<?php
					$arr_params['style'] = 'printme';
					if ( get_query_var('page') ) {
						$arr_params['page'] = esc_html( get_query_var( 'page' ) );
					}
					if ( get_query_var('cpage') ) {
						$arr_params['cpage'] = esc_html( get_query_var( 'cpage' ) );
					}
					echo add_query_arg( $arr_params, get_permalink() );
					?>">
					<span class="minib_img minib_print">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $comment && $is_singular && comments_open( $post->ID ) && !post_password_required() ) { 	// ------- Leave a comment ------- ?>
			<div class="minibutton" title="<?php esc_attr_e( 'Leave a comment','boozurk' ); ?>">
				<a href="#respond">
					<span class="minib_img minib_comment">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $feed && $is_singular && comments_open( $post->ID ) && !post_password_required() ) { 	// ------- RSS feed ------- ?>
			<div class="minibutton" title="<?php esc_attr_e( 'Feed for comments on this post', 'boozurk' ); ?>">
				<a href="<?php echo get_post_comments_feed_link( $post->ID, 'rss2' ); ?> ">
					<span class="minib_img minib_rss">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $trackback && $is_singular && pings_open() ) { 											// ------- Trackback ------- ?>
			<div class="minibutton" title="<?php esc_attr_e( 'Trackback URL','boozurk' ); ?>">
				<a href="<?php echo get_trackback_url(); ?>" rel="trackback">
					<span class="minib_img minib_track">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $home ) { 																				// ------- Home ------- ?>
			<div class="minibutton" title="<?php esc_attr_e( 'Home','boozurk' ); ?>">
				<a href="<?php echo home_url(); ?>">
					<span class="minib_img minib_home">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $is_image ) { 																			// ------- Back to parent post ------- ?>
			<?php if ( !empty( $post->post_parent ) ) { ?>
				<div class="minibutton" title="<?php esc_attr( printf( __( 'Return to %s', 'boozurk' ), strip_tags( get_the_title( $post->post_parent ) ) ) ); ?>">
					<a href="<?php echo get_permalink( $post->post_parent ); ?>" rel="gallery">
						<span class="minib_img minib_backtopost">&nbsp;</span>
					</a>
				</div>
			<?php } ?>
		<?php } ?>

		<?php if (  $next_prev && $is_post && get_previous_post() ) { 										// ------- Previous post ------- ?>
			<div class="minibutton" title="<?php esc_attr( printf( __( 'Previous Post', 'boozurk' ) . ': %s', strip_tags( get_the_title( get_previous_post() ) ) ) ); ?>">
				<a rel="prev" href="<?php echo get_permalink( get_previous_post() ); ?>">
					<span class="minib_img minib_ppage">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $next_prev && $is_post && get_next_post() ) { 											// ------- Next post ------- ?>
			<div class="minibutton" title="<?php esc_attr( printf( __( 'Next Post', 'boozurk' ) . ': %s', strip_tags( get_the_title( get_next_post() ) ) ) ); ?>">
				<a rel="next" href="<?php echo get_permalink( get_next_post() ); ?>">
					<span class="minib_img minib_npage">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $next_prev && !$is_singular && !$boozurk_is_allcat_page && get_next_posts_link() ) { 			// ------- Older Posts ------- ?>
			<div class="minibutton nb-nextprev" title="<?php esc_attr_e( 'Older Posts', 'boozurk' ); ?>">
				<?php next_posts_link( '<span class="minib_img minib_npages">&nbsp;</span>' ); ?>
			</div>
		<?php } ?>

		<?php if ( $next_prev && !$is_singular && !$boozurk_is_allcat_page && get_previous_posts_link() ) { 		// ------- Newer Posts ------- ?>
			<div class="minibutton nb-nextprev" title="<?php esc_attr_e( 'Newer Posts', 'boozurk' ); ?>">
				<?php previous_posts_link( '<span class="minib_img minib_ppages">&nbsp;</span>' ); ?>
			</div>
		<?php } ?>

		<?php if ( $up_down ) { 																			// ------- Top ------- ?>
			<div class="minibutton" title="<?php esc_attr_e( 'Top of page', 'boozurk' ); ?>">
				<a href="#">
					<span class="minib_img minib_top">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $up_down ) { 																			// ------- Bottom ------- ?>
			<div class="minibutton" title="<?php esc_attr_e( 'Bottom of page', 'boozurk' ); ?>">
				<a href="#footer">
					<span class="minib_img minib_bottom">&nbsp;</span>
				</a>
			</div>
		<?php } ?>
	<div class="fixfloat"> </div>
</div>

	<?php
	}
}

// the header
if ( !function_exists( 'boozurk_get_header' ) ) {
	function boozurk_get_header() {

		if ( display_header_text() ) { 
			$header = '<h1><a href="' . home_url() . '/">' . get_bloginfo( 'name' ) . '</a></h1>';
		} else {
			$header = '<h1 class="hide_if_no_print"><a href="' . home_url() . '/">' . get_bloginfo( 'name' ) . '</a></h1>';
		}

		if ( get_header_image() ) { 
			if ( display_header_text() ) { 
				$header .= '<img alt="' . home_url() . '" src="' . get_header_image() . '" />';
			} else {
				$header .= '<a href="' . home_url() . '/"><img alt="' . home_url() . '" src="' . get_header_image() . '" /></a>';
			}
		}
		return apply_filters( 'boozurk_filter_header', $header );
	}
}

// archives pages navigation
if ( !function_exists( 'boozurk_navigate_archives' ) ) {
	function boozurk_navigate_archives() {
		global $paged;

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
	<div class="img-navi">
		<?php if ( isset( $attachments[ $prevk ] ) ) { ?>
			<a class="img-navi-prev" rel="prev" title="" href="<?php echo get_attachment_link( $attachments[ $prevk ]->ID ); ?>"><?php echo wp_get_attachment_image( $attachments[ $prevk ]->ID, array( 70, 70 ) ); ?></a>
		<?php } ?>
		<?php if ( isset( $attachments[ $nextk ] ) ) { ?>
			<a class="img-navi-next" rel="next" title="" href="<?php echo get_attachment_link( $attachments[ $nextk ]->ID ); ?>"><?php echo wp_get_attachment_image( $attachments[ $nextk ]->ID, array( 70, 70 ) ); ?></a>
		<?php } ?>
	</div>
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

	if ( is_active_sidebar( 'single-widgets-area' ) ) {

?>
	<div id="single-widgets-area" class="ul_swa fixfloat">
	<?php dynamic_sidebar( 'single-widgets-area' ); ?>
	<div class="fixfloat"></div>
	</div>
<?php

	}
}

// get the post thumbnail or (if not set) the format related icon
if ( !function_exists( 'boozurk_get_the_thumb' ) ) {
	function boozurk_get_the_thumb( $id, $size_w, $size_h, $class, $default = '' ) {
		if ( has_post_thumbnail( $id ) ) {
			return get_the_post_thumbnail( $id, array( $size_w,$size_h ) );
		} else {
			if ( get_post_format( $id ) ) {
				$format = get_post_format( $id );
			} else {
				$format = 'standard';
			}
			return '<img class="' . $class . ' wp-post-image ' . $format . '" alt="thumb" src="' . get_template_directory_uri() . '/images/img40.png" />';
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

// create theme option page
if ( !function_exists( 'boozurk_create_menu' ) ) {
	function boozurk_create_menu() {
		//create new top-level menu
		$pageopt = add_theme_page( __( 'Theme Options','boozurk' ), __( 'Theme Options','boozurk' ), 'edit_theme_options', 'boozurk_functions', 'boozurk_edit_options' );
		//call register settings function
		add_action( 'admin_init', 'boozurk_register_tb_settings' );
		add_action( 'admin_print_styles-' . $pageopt, 'boozurk_theme_admin_styles' );
		add_action( 'admin_print_scripts-' . $pageopt, 'boozurk_theme_admin_scripts' );
		add_action( 'admin_print_styles-widgets.php', 'boozurk_widgets_style' );
		add_action( 'admin_print_scripts-widgets.php', 'boozurk_widgets_scripts' );
	}
}

if ( !function_exists( 'boozurk_register_tb_settings' ) ) {
	function boozurk_register_tb_settings() {
		//register boozurk settings
		register_setting( 'boozurk_settings_group', 'boozurk_options', 'boozurk_sanitize_options' );
	}
}

// set up custom colors and header image
if ( !function_exists( 'boozurk_setup' ) ) {
	function boozurk_setup() {

		// Register localization support
		load_theme_textdomain( 'boozurk', get_template_directory() . '/languages' );

		// Theme uses wp_nav_menu() in three location
		register_nav_menus( array( 'primary' => __( 'Main Navigation Menu', 'boozurk' ) ) );
		register_nav_menus( array( 'secondary1' => __( 'Secondary Navigation Menu #1', 'boozurk' ) ) );
		register_nav_menus( array( 'secondary2' => __( 'Secondary Navigation Menu #2', 'boozurk' ) ) );

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
if ( !function_exists( 'boozurk_addgravatar' ) ) {
	function boozurk_addgravatar( $avatar_defaults ) {
	  $myavatar = get_template_directory_uri() . '/images/user.png';
	  $avatar_defaults[$myavatar] = __( 'boozurk Default Gravatar', 'boozurk' );
	
	  return $avatar_defaults;
	}
	add_filter( 'avatar_defaults', 'boozurk_addgravatar' );
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

/**
 * Create HTML list of nav menu items.
 * Replacement for the native Walker, using the thumbnail.
 *
 * @see    http://wordpress.stackexchange.com/q/14037/
 * @author toscho, http://toscho.de
 */
class boozurk_Thumb_Walker extends Walker_Nav_Menu
{
    /**
     * Start the element output.
     *
     * @param  string $output Passed by reference. Used to append additional content.
     * @param  object $item   Menu item data object.
     * @param  int $depth     Depth of menu item. May be used for padding.
     * @param  array $args    Additional strings.
     * @return void
     */
    function start_el(&$output, $item, $depth, $args) {
		$classes = empty ( $item->classes ) ? array () : (array) $item->classes;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );

		! empty ( $class_names )
		and $class_names = ' class="'. esc_attr( $class_names ) . '"';

		$output .= "<li id='menu-item-$item->ID' $class_names>";

		$attributes = '';

		! empty( $item->attr_title )
		and $attributes .= ' title="'  . esc_attr( $item->attr_title ) .'"';
		! empty( $item->target )
		and $attributes .= ' target="' . esc_attr( $item->target     ) .'"';
		! empty( $item->xfn )
		and $attributes .= ' rel="'    . esc_attr( $item->xfn        ) .'"';
		! empty( $item->url )
		and $attributes .= ' href="'   . esc_attr( $item->url        ) .'"';

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		
		if ( 0 == $depth ) {
			$thumb = '<img class="bz-menu-thumb default" width="' . (int)boozurk_get_opt( 'boozurk_main_menu_icon_size' ) . '" height="' . (int)boozurk_get_opt( 'boozurk_main_menu_icon_size' ) . '" alt="' . $title . '" src="' . get_template_directory_uri() . '/images/img40.png" />';
			if ( has_post_thumbnail((int)$item->object_id) ) {
				$thumb = get_the_post_thumbnail( (int)$item->object_id, array((int)boozurk_get_opt( 'boozurk_main_menu_icon_size' ),(int)boozurk_get_opt( 'boozurk_main_menu_icon_size' )), array( 'title' => $title, 'class' => 'bz-menu-thumb' ) );
			}
			if ( boozurk_get_opt( 'boozurk_main_menu' ) == 'thumbnail' ) {
				$title = $thumb;
			} elseif ( boozurk_get_opt( 'boozurk_main_menu' ) == 'thumbnail and text' ) {
				$title = $thumb . $title;
			}
		}

		$item_output = $args->before
			. "<a $attributes>"
			. $args->link_before
			. $title
			. '</a> '
			. $args->link_after
			. $args->after;

		// Since $output is called by reference we don't need to return anything.
		$output .= apply_filters(
			'walker_nav_menu_start_el'
			,$item_output
			,$item
			,$depth
			,$args
		);
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
	if ( isset( $_POST["bz_post_expander"] ) ) {
		add_action( 'wp', 'boozurk_post_expander_show_post' );
	}
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
				<?php if ( post_password_required() ) {
					$use_format = 'protected';
				} else {
					$use_format = ( 
						function_exists( 'get_post_format' ) &&
						boozurk_get_opt( 'boozurk_post_formats_' . get_post_format( $post->ID ) )
					) ? get_post_format( $post->ID ) : '' ;
				} ?>
				
				<?php boozurk_hook_before_post(); ?>
				<?php get_template_part( 'loop/post', $use_format ); ?>
				<?php boozurk_hook_after_post(); ?>
			
			<?php } //end while ?>

			<div class="ajaxed" id="bz-page-nav">
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
	if ( isset( $_POST["bz_infinite_scroll"] ) ) {
		add_action( 'wp', 'boozurk_infinite_scroll_show_page' );
	}
}

// add links to admin bar
if ( !function_exists( 'boozurk_admin_bar_plus' ) ) {
	function boozurk_admin_bar_plus() {
		global $wp_admin_bar;
		if (!is_super_admin() || !is_admin_bar_showing() || !current_user_can( 'edit_theme_options' ) )
			return;
		$add_menu_meta = array(
			'target'    => '_blank'
		);
		$wp_admin_bar->add_menu(array(
			'id'        => 'bz_theme_options',
			'parent'    => 'appearance',
			'title'     => __( 'Theme Options','boozurk' ),
			'href'      => get_admin_url() . 'themes.php?page=boozurk_functions',
			'meta'      => $add_menu_meta
		));
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

		<meta itemprop="name" content="<?php the_title(); ?>">
		<meta itemprop="description" content="<?php echo $content; ?>">
		<?php if( has_post_thumbnail() ) { $image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id() ); ?><meta itemprop="image" content="<?php echo $image_attributes[0]; ?>"><?php } ?>

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
			<div class="fixfloat"> </div>
		</div>
<?php 

		}

	}
}

// comments-are-closed message when post type supports comments and we're not on a page
function boozurk_comments_closed() {
	if ( ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) {
?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'boozurk' ); ?></p>
<?php
	}
}

// Custom form fields for the comment form
function boozurk_comments_form_fields( $fields ) {
	$commenter	=	wp_get_current_commenter();
	$req		=	get_option( 'require_name_email' );
	$aria_req	=	( $req ? " aria-required='true'" : '' );

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

	$defaults['label_submit'] = __( 'Say It!','boozurk' );
	$defaults['comment_field'] = '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="7" aria-required="true"></textarea></p>';

	return $defaults;

}

// add a random color to every new category
function boozurk_created_category_color( $term_id, $tt_id, $taxonomy ) {

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
if ( !function_exists( 'boozurk_img_caption_shortcode' ) ) {
	function boozurk_img_caption_shortcode( $deprecated, $attr, $content = null ) {

		extract(shortcode_atts(array(
			'id'	=> '',
			'align'	=> 'alignnone',
			'width'	=> '',
			'caption' => ''
		), $attr));

		if ( 1 > (int) $width || empty($caption) )
			return $content;

		if ( $id ) $id = 'id="' . esc_attr($id) . '" ';

		return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . $width . 'px"><div class="wp-caption-inside">'
		. do_shortcode( $content ) . '<div class="wp-caption-text">' . $caption . '</div></div></div>';
	}
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
function boozurk_body_classes($classes) {

	$temp_class = has_nav_menu( 'secondary1' )? 'top-menu' : '';

	$classes[] = 'no-js';
	if ( $temp_class ) $classes[] = $temp_class;

	return $classes;

}

// custom gallery shortcode function
function boozurk_gallery_shortcode( $output, $attr ) {
	global $post;

	static $instance = 0;
	$instance++;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => ''
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

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
	$float = is_rtl() ? 'right' : 'left';

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

function boozurk_get_attachment_link( $markup, $id, $size, $permalink, $icon, $text ) {
	$id = intval( $id );
	$_post = get_post( $id );

	if ( empty( $_post ) || ( 'attachment' != $_post->post_type ) || ! $url = wp_get_attachment_url( $_post->ID ) )
		return __( 'Missing Attachment' );

	if ( $permalink )
		$url = get_attachment_link( $_post->ID );

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

function boozurk_get_credits() {

	$credits = apply_filters( 'boozurk_credits', '&copy; ' . date( 'Y' ) . ' <strong>' . get_bloginfo( 'name' ) . '</strong>. ' . __( 'All rights reserved','boozurk' ) );

	if ( boozurk_get_opt('boozurk_tbcred') )
		$credits .= '<br>' . sprintf( __('Powered by %s and %s','boozurk'), '<a target="_blank" href="http://wordpress.org/" title="WordPress">WordPress</a>', '<a target="_blank" href="http://www.twobeers.net/" title="' . esc_attr( __( 'Visit theme authors homepage','boozurk' ) ) . ' @ twobeers.net">' . esc_attr( __( 'Boozurk theme','boozurk' ) ) . '</a>');

	if ( boozurk_get_opt('boozurk_mobile_css') )
		$credits .= '<span class="hide_if_print"> - <a rel="nofollow" href="' . home_url() . '?mobile_override=mobile">'. __('Mobile View','boozurk') .'</a></span>';

	return $credits;

}

?>