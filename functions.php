<?php
// Tell WordPress to run boozurk_setup() when the 'after_setup_theme' hook is run.
add_action( 'after_setup_theme', 'boozurk_setup' );
// Tell WordPress to run boozurk_default_options()
add_action( 'admin_init', 'boozurk_default_options' );
// Register sidebars by running boozurk_widget_area_init() on the widgets_init hook
add_action( 'widgets_init', 'boozurk_widget_area_init' );
// Add stylesheets
add_action( 'wp_print_styles', 'boozurk_stylesheet' );
add_action( 'wp_head', 'boozurk_custom_style' );
// Add js scripts
add_action( 'wp_head', 'boozurk_localize_js' );
// setup for audio player
add_action( 'wp_head', 'boozurk_setup_player' );
add_action( 'template_redirect', 'boozurk_scripts' );
add_action( 'wp_footer', 'boozurk_initialize_scripts' );
// Add custom category page
add_action( 'template_redirect', 'boozurk_allcat' );
// mobile redirect
add_action( 'template_redirect', 'boozurk_mobile' );
// Add admin menus
add_action( 'admin_menu', 'boozurk_create_menu' );
// post expander ajax request
add_action('init', 'boozurk_post_expander_activate');
// Add the "quote" link
add_action( 'wp_footer', 'boozurk_quote_scripts' );
// Custom filters
add_filter( 'get_comment_author_link', 'boozurk_add_quoted_on' );
add_filter( 'img_caption_shortcode', 'boozurk_img_caption_shortcode', 10, 3 );
add_filter( 'use_default_gallery_style', '__return_false' );

// load theme options in $boozurk_opt variable, globally retrieved in php files
$boozurk_opt = get_option( 'boozurk_options' );

// check if is mobile browser
$bz_is_mobile_browser = boozurk_mobile_device_detect();
function boozurk_mobile_device_detect() {
	global $boozurk_opt;
	
	// #1 check: mobile support is off (via options)
	if ( ( isset( $boozurk_opt['boozurk_mobile_css'] ) && ( $boozurk_opt['boozurk_mobile_css'] == 0) ) ) return false;
	
	// #2 check: mobile override, the user clicked the "switch to desktop/mobile" link. a cookie will be set
	if ( isset( $_GET['mobile_override'] ) ) {
		if ( md5( $_GET['mobile_override'] ) == '532c28d5412dd75bf975fb951c740a30' ) { // 'mobile'
			setcookie( "mobile_override", "mobile", time()+(60*60*24*30*12) );
			return true;
		} else {
			setcookie( "mobile_override", "desktop", time()+(60*60*24*30*12) );
			return false;
		}
	}
	
	// #3 check: the cookie is already set
	if (isset($_COOKIE["mobile_override"])) {
		if ( md5( $_COOKIE["mobile_override"] ) == '532c28d5412dd75bf975fb951c740a30' ) { // 'mobile'
			return true;
		} else {
			return false;
		}
	}
	
	// #4 check: search for a mobile user agent
	if ( !isset($_SERVER['HTTP_USER_AGENT']) ) return false;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
    if ( ( !isset( $boozurk_opt['boozurk_mobile_css'] ) || ( $boozurk_opt['boozurk_mobile_css'] == 1) ) && preg_match( '/(ipad|ipod|iphone|android|opera mini|blackberry|palm|symbian|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine|iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i' , $user_agent ) ) { // there were other words for mobile detecting but this is enought ;-)
		return true;
	} else {
		return false;
	}
}

// check if in preview mode or not
$bz_is_printpreview = false;
if ( isset( $_GET['style'] ) && md5( $_GET['style'] ) == '8e77921d24c6f82c4bd783895e9d9cf1' ) {
	$bz_is_printpreview = true;
}

// check if in allcat view
$bz_is_allcat_page = false;
if( isset( $_GET['allcat'] ) && ( md5( $_GET['allcat'] ) == '415290769594460e2e485922904f345d' ) ) {
	$bz_is_allcat_page = true;
}

// Set the content width based on the theme's design
if ( ! isset( $content_width ) ) {
	if ( ! $bz_is_mobile_browser ) {
		$content_width = 560;
	} else {
		$content_width = 300;
	}
}

//complete options array, with type, defaults values, description, infos and required option
function boozurk_get_coa() {
	$boozurk_coa = array(
		'boozurk_jsani' => array( 'group' =>'javascript', 'type' =>'chk', 'default'=>1,'description'=>__( 'javascript animations','boozurk' ),'info'=>__( 'try disable animations if you encountered problems with javascript','boozurk' ),'req'=>'' ),
		'boozurk_js_gallery' => array( 'group' =>'javascript', 'type' =>'chk', 'default'=>1,'description'=>__( 'gallery preview','boozurk' ),'info'=>__( 'load gallery images on fly','boozurk' ),'req'=>'boozurk_jsani' ),
		'boozurk_js_post_expander' => array( 'group' =>'javascript', 'type' =>'chk', 'default'=>1,'description'=>__( 'post expander','boozurk' ),'info'=>__( 'expands a post to show the full content when the reader clicks the "Read more..." link','boozurk' ),'req'=>'boozurk_jsani' ),
		'boozurk_js_tooltips' => array( 'group' =>'javascript', 'type' =>'chk', 'default'=>1,'description'=>__( 'cool tooltips','boozurk' ),'info'=>__( 'replace link titles with cool tooltips','boozurk' ),'req'=>'boozurk_jsani' ),
		'boozurk_js_swfplayer' => array( 'group' =>'javascript', 'type' =>'chk', 'default'=>1,'description'=>__( 'swf audio player','boozurk' ),'info'=>__( 'create an audio player for linked audio files (mp3,ogg and m4a) in the audio format posts','boozurk' ),'req'=>'boozurk_jsani' ),
		'boozurk_quotethis' => array( 'group' =>'javascript', 'type' =>'chk', 'default'=>1,'description'=>__( 'quote link', 'boozurk' ),'info'=>__( 'show a link for easily add the selected text as a quote inside the comment form', 'boozurk' ),'req'=>'' ),
		'boozurk_sidebar_head_split' => array( 'group' =>'widgets', 'type' =>'sel', 'default'=>'3', 'options'=>array('1','2','3'), 'description'=>__( 'split Header widget area','boozurk' ),'info'=>__( 'number of widget that can stay in the widget area side by side','boozurk' ),'req'=>'' ),
		'boozurk_sidebar_single_split' => array( 'group' =>'widgets', 'type' =>'sel', 'default'=>'1', 'options'=>array('1','2','3'), 'description'=>__( 'split Post widget area','boozurk' ),'info'=>__( 'number of widget that can stay in the widget area side by side','boozurk' ),'req'=>'' ),
		'boozurk_sidebar_foot_1_width' => array( 'group' =>'widgets', 'type' =>'sel', 'default'=>'33%', 'options'=>array('100%','50%','33%'), 'description'=>__( 'footer widget area #1','boozurk' ),'info'=>__( 'width of the widget area','boozurk' ),'req'=>'' ),
		'boozurk_sidebar_foot_2_width' => array( 'group' =>'widgets', 'type' =>'sel', 'default'=>'33%', 'options'=>array('100%','50%','33%'), 'description'=>__( 'footer widget area #2','boozurk' ),'info'=>__( 'width of the widget area','boozurk' ),'req'=>'' ),
		'boozurk_sidebar_foot_3_width' => array( 'group' =>'widgets', 'type' =>'sel', 'default'=>'33%', 'options'=>array('100%','50%','33%'), 'description'=>__( 'footer widget area #3','boozurk' ),'info'=>__( 'width of the widget area','boozurk' ),'req'=>'' ),
		'boozurk_custom_widgets' => array( 'group' =>'widgets', 'type' =>'chk', 'default'=>1, 'description'=>__( 'custom widgets','boozurk' ),'info'=>__( 'add a lot of new usefull widgets','boozurk' ),'req'=>'' ),
		'boozurk_colors_link' => array( 'group' =>'colors', 'type' =>'col', 'default'=>'#21759b','description'=>__( 'links','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_colors_link_hover' => array( 'group' =>'colors', 'type' =>'col', 'default'=>'#404040','description'=>__( 'highlighted links','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_colors_link_sel' => array( 'group' =>'colors', 'type' =>'col', 'default'=>'#87CEEB','description'=>__( 'selected links','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_cat_colors' => array( 'group' =>'colors', 'type' =>'catcol', 'default'=>'#87CEEB','description'=>__( 'colors for categories','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_font_family' => array( 'group' =>'other', 'type' =>'sel', 'default'=>'monospace', 'options'=>array('monospace','Arial, sans-serif','Helvetica, sans-serif','Comic Sans MS, cursive','Courier New, monospace','Georgia, serif','Lucida Console, Monaco, monospace','Lucida Sans Unicode, Lucida Grande, sans-serif','Palatino Linotype, Book Antiqua, Palatino, serif','Tahoma, Geneva, sans-serif','Times New Roman, Times, serif','Trebuchet MS, sans-serif','Verdana, Geneva, sans-serif'), 'description'=>__( 'font family','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_font_size' => array( 'group' =>'other', 'type' =>'sel', 'default'=>'11px', 'options'=>array('10px','11px','12px','13px','14px','15px','16px'), 'description'=>__( 'font size','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_browse_links' => array( 'group' =>'other', 'type' =>'chk', 'default'=>1,'description'=>__( 'quick browsing links', 'boozurk' ),'info'=>__( 'show navigation links before post content', 'boozurk' ),'req'=>'' ),
		'boozurk_featured_title' => array( 'group' =>'other', 'type' =>'chk', 'default'=>1,'description'=>__( 'enhanced post title', 'boozurk' ),'info'=>__( 'show the post title with the featured image', 'boozurk' ),'req'=>'' ),
		'boozurk_main_menu' => array( 'group' =>'other', 'type' =>'sel', 'default'=>__('text','boozurk'), 'options'=>array( __('text','boozurk'), __('thumbnail','boozurk'), __('thumbnail and text','boozurk') ), 'description'=>__( 'main menu look','boozurk' ),'info'=>__( 'select the style of the main menu: text, thumbnails or both','boozurk' ),'req'=>'' ),
		'boozurk_main_menu_icon_size' => array( 'group' =>'other', 'type' =>'sel', 'default'=>'48', 'options'=>array ('32', '48', '64', '96'), 'description'=>__( 'main menu icon size','boozurk' ),'info'=>__( 'the dimension of the thumbnails in main menu (if "thumbnails" style is selected)','boozurk' ),'req'=>'' ),
		'boozurk_logo' => array( 'group' =>'other', 'type' =>'url', 'default'=>'','description'=>__( 'Logo','boozurk' ),'info'=>__( 'a logo in the upper right corner of the window. paste here the complete path to image location. leave empty to ignore','boozurk' ),'req'=>'' ),
		'boozurk_logo_login' => array( 'group' =>'other', 'type' =>'chk', 'default'=>1,'description'=>__( 'Logo in login page','boozurk' ),'info'=>__( 'use the logo in the login page','boozurk' ),'req'=>'boozurk_logo' ),
		'boozurk_editor_style' => array( 'group' =>'other', 'type' =>'chk', 'default'=>1,'description'=>__( 'editor style', 'boozurk' ),'info'=>__( "add style to the editor in order to write the post exactly how it will appear on the site", 'boozurk' ),'req'=>'' ),
		'boozurk_mobile_css' => array( 'group' =>'other', 'type' =>'chk', 'default'=>1,'description'=>__( 'mobile support','boozurk' ),'info'=>__( 'use a dedicated style in mobile devices','boozurk' ),'req'=>'' ),
		'boozurk_post_formats' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( 'post formats support','boozurk' ),'info'=>__('','boozurk' ),'req'=>'' ),
		'boozurk_post_formats_gallery' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- gallery','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_aside' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- aside','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_audio' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- audio','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_image' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- image','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_link' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- link','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_quote' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- quote','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_status' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- status','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_video' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- video','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_tbcred' => array( 'group' =>'other', 'type' =>'chk', 'default'=>1,'description'=>__( 'theme credits','boozurk' ),'info'=>__( "please, don't hide theme credits",'boozurk' ),'req'=>'' )
	);
	return $boozurk_coa;
}

// get theme version
if ( get_theme( 'Boozurk' ) ) {
	$boozurk_current_theme = get_theme( 'Boozurk' );
	$boozurk_version = $boozurk_current_theme['Version'];
}

// check and set default options 
function boozurk_default_options() {
		global $boozurk_current_theme;
		$boozurk_coa = boozurk_get_coa();
		$boozurk_opt = get_option( 'boozurk_options' );

		// if options are empty, sets the default values
		if ( empty( $boozurk_opt ) || !isset( $boozurk_opt ) ) {
			foreach ( $boozurk_coa as $key => $val ) {
				$boozurk_opt[$key] = $boozurk_coa[$key]['default'];
			}
			$boozurk_opt['version'] = ''; //null value to keep admin notice alive and invite user to discover theme options
			update_option( 'boozurk_options' , $boozurk_opt );
		} else if ( !isset( $boozurk_opt['version'] ) || $boozurk_opt['version'] < $boozurk_current_theme['Version'] ) {
			// check for unset values and set them to default value -> when updated to new version
			foreach ( $boozurk_coa as $key => $val ) {
				if ( !isset( $boozurk_opt[$key] ) ) $boozurk_opt[$key] = $boozurk_coa[$key]['default'];
			}
			$boozurk_opt['version'] = ''; //null value to keep admin notice alive and invite user to discover theme options
			update_option( 'boozurk_options' , $boozurk_opt );
		}
}

// print a reminder message for set the options after the theme is installed or updated
if ( !function_exists( 'boozurk_setopt_admin_notice' ) ) {
	function boozurk_setopt_admin_notice() {
		echo '<div class="updated"><p><strong>' . sprintf( __( "boozurk theme says: \"Dont forget to set <a href=\"%s\">my options</a>!\"", 'boozurk' ), get_admin_url() . 'themes.php?page=tb_boozurk_functions' ) . '</strong></p></div>';
	}
}
if ( current_user_can( 'manage_options' ) && $boozurk_opt['version'] < $boozurk_current_theme['Version'] ) {
	add_action( 'admin_notices', 'boozurk_setopt_admin_notice' );
}

if ( ( $boozurk_opt['boozurk_logo_login'] == 1 ) && ( $boozurk_opt['boozurk_logo'] != '' ) ) {
	add_action( 'login_footer', 'boozurk_login_footer' );
	add_action( 'login_head', 'boozurk_login_head' );
}


if ( !function_exists( 'boozurk_widget_area_init' ) ) {
	function boozurk_widget_area_init() {
		// Area 0, in the fixed sidebar.
		register_sidebar( array(
			'name' => __( 'Fixed Widget Area', 'boozurk' ),
			'id' => 'fixed-widget-area',
			'description' => __( 'The fixed widget area', 'boozurk' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="w_title">',
			'after_title' => '</div>',
		) );

		// Area 1, located at the top of the sidebar.
		register_sidebar( array(
			'name' => __( 'Sidebar Widget Area', 'boozurk' ),
			'id' => 'primary-widget-area',
			'description' => __( 'The sidebar widget area', 'boozurk' ),
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
		global $boozurk_opt, $bz_is_mobile_browser;
		if ( $bz_is_mobile_browser ) return; // skip if in mobile view
?>
<style type="text/css">
	body {
		font-size: <?php echo $boozurk_opt['boozurk_font_size']; ?>;
		font-family: <?php echo $boozurk_opt['boozurk_font_family']; ?>;
	}
	a {
		color: <?php echo $boozurk_opt['boozurk_colors_link']; ?>;
	}
	a:hover,
	ul li:hover .hiraquo,
	.current-menu-item a:hover,
	.current_page_item a:hover,
	.current-cat a:hover {
		color: <?php echo $boozurk_opt['boozurk_colors_link_hover']; ?>;
	}
	.current-menu-item > a,
	.current_page_item > a,
	.current-cat > a,
	li.current_page_ancestor .hiraquo {
		color: <?php echo $boozurk_opt['boozurk_colors_link_sel']; ?>;
	}	
	#header-widget-area .bz-widget {
		width:<?php echo round ( 99 / intval( $boozurk_opt['boozurk_sidebar_head_split'] ), 1 ); ?>%;
	}
	#single-widgets-area .bz-widget {
		width:<?php echo round ( 99 / intval( $boozurk_opt['boozurk_sidebar_single_split'] ), 1 ); ?>%;
	}
	#first_fwa {
		width:<?php echo $boozurk_opt['boozurk_sidebar_foot_1_width']; ?>;
	}	
	#second_fwa {
		width:<?php echo $boozurk_opt['boozurk_sidebar_foot_2_width']; ?>;
	}	
	#third_fwa {
		width:<?php echo $boozurk_opt['boozurk_sidebar_foot_3_width']; ?>;
	}
<?php
	$args=array(
		'orderby' => 'name',
		'order' => 'DESC'
	);
	$categories=get_categories($args);
	foreach($categories as $category) {
		$catcolor = isset($boozurk_opt['boozurk_cat_colors'][$category->term_id]) ? $boozurk_opt['boozurk_cat_colors'][$category->term_id] : $boozurk_coa['boozurk_cat_colors']['default'];
		echo '#posts_content .category-' . $category->slug . ' { border-color:' . $catcolor . ' ;}' . "\n";
		echo '.widget .cat-item-' . $category->term_id . ' > a, #posts_content .cat-item-' . $category->term_id . ' > a { border-left: 1em solid ' . $catcolor . ' ; }' . "\n";
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
	.wp-caption .wp-caption-text,
	.bz-featured-title .storytitle {
		background-color: #fff;
	}
</style>
<![endif]-->
<?php
	}
}

// localize js
if ( !function_exists( 'boozurk_localize_js' ) ) {
	function boozurk_localize_js() {
		global $boozurk_opt, $bz_is_mobile_browser, $bz_is_printpreview;
		if ( is_admin() || ( $boozurk_opt['boozurk_jsani'] == 0 ) || $bz_is_mobile_browser || $bz_is_printpreview ) return;
		?>
<script type="text/javascript">
	/* <![CDATA[ */
		bz_post_expander_text = "<?php _e( 'Post loading, please wait...','boozurk' ); ?>";
		bz_gallery_preview_text = "<?php _e( 'Preview','boozurk' ); ?>";
		bz_gallery_click_text = "<?php _e( 'Click on thumbnails','boozurk' ); ?>";
		bz_unknown_media_format = "<?php _e( 'this audio type is not supported by your browser','boozurk' ); ?>";
	/* ]]> */
</script>
		<?php
	}
}

// initialize js
if ( !function_exists( 'boozurk_initialize_scripts' ) ) {
	function boozurk_initialize_scripts() {
		global $boozurk_opt, $bz_is_mobile_browser, $bz_is_printpreview;
		if ( is_admin() || ( $boozurk_opt['boozurk_jsani'] == 0 ) || $bz_is_mobile_browser || $bz_is_printpreview ) return;
		?>
<script type="text/javascript">
	/* <![CDATA[ */
	(function(){
		var c = document.body.className;
		c = c.replace(/no-js/, 'js');
		document.body.className = c;
	})();
	jQuery(document).ready(function($){
		$('#mainmenu').boozurk_AnimateMenu();
		$('.bz-tooltip,.nb_tooltip').boozurk_Tooltips();
		<?php if ( $boozurk_opt['boozurk_js_swfplayer'] == 1 ) { ?>
		$('audio').boozurk_AudioPlayer();<?php } ?>
		<?php if ( $boozurk_opt['boozurk_js_post_expander'] == 1 ) { ?>
		$('a.more-link').boozurk_PostExpander();<?php } ?>
		<?php if ( $boozurk_opt['boozurk_js_gallery'] == 1 ) { ?>
		$('.storycontent .gallery').boozurk_GallerySlider();<?php } ?>
		<?php if ( $boozurk_opt['boozurk_js_tooltips'] == 1 ) { ?>
		$('.minibutton,.share-item img,.bz_widget_categories a,#bz-quotethis,.bz_widget_latest_commentators li,.bz-widget-social a,.post-format-item.compact img').boozurk_Cooltips({fade: true});
		$('.pmb_comm').boozurk_Cooltips({fade: true, fallback: '<?php _e( 'Comments closed','boozurk' ); ?>'});<?php } ?>
	});
	/* ]]> */
</script>
		<?php
	}
}

// add "quote" link
if ( !function_exists( 'boozurk_quote_scripts' ) ) {
	function boozurk_quote_scripts(){
		global $boozurk_opt, $bz_is_mobile_browser, $bz_is_printpreview;
		if ( is_admin() || ( $boozurk_opt['boozurk_quotethis'] == 0 ) || $bz_is_mobile_browser || $bz_is_printpreview || !is_singular() ) return;
		?>
<script type="text/javascript">
	/* <![CDATA[ */
	if ( document.getElementById('reply-title') && document.getElementById("comment") ) {
		bz_qdiv = document.createElement('small');
		bz_qdiv.innerHTML = ' - <a id="bz-quotethis" href="#" onclick="bz_quotethis(); return false" title="<?php _e( 'Add selected text as a quote', 'boozurk' ); ?>" ><?php _e( 'Quote', 'boozurk' ); ?></a>';
		bz_replink = document.getElementById('reply-title');
		bz_replink.appendChild(bz_qdiv);
	}
	function bz_quotethis() {
		var posttext = '';
		if (window.getSelection){
			posttext = window.getSelection();
		}
		else if (document.getSelection){
			posttext = document.getSelection();
		}
		else if (document.selection){
			posttext = document.selection.createRange().text;
		}
		else {
			return true;
		}
		posttext = posttext.toString().replace(/&/g,"&amp;").replace(/"/g,"&quot;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
		if ( posttext.length !== 0 ) {
			document.getElementById("comment").value = document.getElementById("comment").value + '<blockquote>' + posttext + '</blockquote>';
		} else {
			alert("<?php _e( 'Nothing to quote. First of all you should select some text...', 'boozurk' ) ?>");
		}
	}
	/* ]]> */
</script>
		<?php
	}
}

// setup for audio player
if ( !function_exists( 'boozurk_setup_player' ) ) {
	function boozurk_setup_player(){
		global $boozurk_opt, $bz_is_mobile_browser, $bz_is_printpreview;
		if ( is_admin() || ( $boozurk_opt['boozurk_jsani'] == 0 ) || $bz_is_mobile_browser || $bz_is_printpreview ) return;
		?>
<script type="text/javascript">
	/* <![CDATA[ */
	bz_AudioPlayer.setup("<?php echo get_template_directory_uri().'/resources/audio-player/player.swf'; ?>", {  
		width: 300,
		loop: "yes",
		transparentpagebg: "yes",
		animation: "no",
		bg: "5C5959",
		leftbg: "5C5959",
		rightbg: "5C5959",
		rightbghover : "5C5959",
		righticon: "FFFFFF",
		lefticon: "FFFFFF",
		track: "5C5959",
		text: "FFFFFF",
		tracker: "828282",
		border: "828282"
	});  
	/* ]]> */
</script>
		<?php
	}
}


// Add stylesheets to page
if ( !function_exists( 'boozurk_stylesheet' ) ) {
	function boozurk_stylesheet(){
		global $boozurk_version, $bz_is_mobile_browser, $bz_is_printpreview;
		// mobile style
		if ( $bz_is_mobile_browser ) {
			wp_enqueue_style( 'bz_mobile-style', get_template_directory_uri() . '/mobile/mobile-style.min.css', false, $boozurk_version, 'screen' );
			return;
		}
		if ( $bz_is_printpreview ) { //print preview
			wp_enqueue_style( 'bz_general-style', get_template_directory_uri() . '/css/print.css', false, $boozurk_version, 'screen' );
			wp_enqueue_style( 'bz_preview-style', get_template_directory_uri() . '/css/print_preview.css', false, $boozurk_version, 'screen' );
		} else { //normal view
			wp_enqueue_style( 'bz_general-style', get_stylesheet_uri(), false, $boozurk_version, 'screen' );
		}
		wp_enqueue_style( 'bz_print-style', get_template_directory_uri() . '/css/print.css', false, $boozurk_version, 'print' );
	}
}

// add scripts
if ( !function_exists( 'boozurk_scripts' ) ) {
	function boozurk_scripts(){
		global $boozurk_opt, $boozurk_version, $bz_is_mobile_browser, $bz_is_printpreview;
		if ( $bz_is_mobile_browser || $bz_is_printpreview ) return; // skip if in print preview or mobile view
		if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); //custom comment-reply pop-up box
		if ( $boozurk_opt['boozurk_jsani'] == 1 ) wp_enqueue_script( 'bz-js', get_template_directory_uri() . '/js/boozurk.dev.js', array( 'jquery','swfobject' ), $boozurk_version, false   );
	}
}

// show all categories list (redirect to allcat.php if allcat=y)
if ( !function_exists( 'boozurk_allcat' ) ) {
	function boozurk_allcat () {
		global $bz_is_allcat_page;
		if( $bz_is_allcat_page ) {
			get_template_part( 'allcat' );
			exit;
		}
	}
}

// show mobile version
if ( !function_exists( 'boozurk_mobile' ) ) {
	function boozurk_mobile () {
		global $bz_is_mobile_browser;
		if ( $bz_is_mobile_browser ) {
			if ( is_singular() ) { 
				get_template_part( 'mobile/mobile-single' ); 
			} else {
				get_template_part( 'mobile/mobile-index' );
			}
			exit;
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

// Pages Menu (mobile)
if ( !function_exists( 'boozurk_pages_menu_mobile' ) ) {
	function boozurk_pages_menu_mobile() {
		echo '<div id="bz-pri-menu" class="bz-menu "><ul id="mainmenu" class="bz-group">';
		wp_list_pages( 'sort_column=menu_order&title_li=&depth=1' ); // menu-order sorted
		echo '</ul></div>';
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
				$the_child_list[] = '<a href="' . get_permalink( $children ) . '" title="' . get_the_title( $children ) . '">' . get_the_title( $children ) . '</a>';
			}
			$the_child_list = implode(' | ' , $the_child_list);
			echo '<div class="bz-breadcrumb-reminder"><span class="bz-breadcrumb-childs">&nbsp;</span>' . $the_child_list . '</div>'; // echoes the childs
			$has_herarchy = true;
		}
		return $has_herarchy;
	}
}

//Display navigation to next/previous post when applicable
if ( !function_exists( 'boozurk_single_nav' ) ) {
	function boozurk_single_nav() {
		global $post, $boozurk_opt;
		if ( $boozurk_opt['boozurk_browse_links'] == 0 ) return;
		$next = get_previous_post();
		$prev = get_next_post();
	?>
		<div class="nav-single fixfloat">
			<?php if ( $prev ) { ?>
				<span class="nav-previous"><a rel="prev" href="<?php echo get_permalink( $prev ); ?>" title="<?php echo esc_attr(strip_tags( __( 'Next Post', 'boozurk' ) . ': ' . get_the_title( $prev ) ) ); ?>"><?php echo get_the_title( $prev ); ?><?php echo boozurk_get_the_thumb( $prev->ID, 32, 32, 'bz-thumb-format' ); ?></a></span>
			<?php } ?>
			<?php if ( $next ) { ?>
				<span class="nav-next"><a rel="next" href="<?php echo get_permalink( $next ); ?>" title="<?php echo esc_attr(strip_tags( __( 'Previous Post', 'boozurk' ) . ': ' . get_the_title( $next ) ) ); ?>"><?php echo boozurk_get_the_thumb( $next->ID, 32, 32, 'bz-thumb-format' ); ?><?php echo get_the_title( $next ); ?></a></span>
			<?php } ?>
		</div><!-- #nav-single -->
	<?php
	}
}

// display the post title with the featured image
if ( !function_exists( 'boozurk_featured_title' ) ) {
	function boozurk_featured_title( $args = array() ) {
		global $post, $boozurk_opt;
		
		$defaults = array( 'alternative' => '', 'fallback' => '', 'featured' => false, 'href' => get_permalink(), 'target' => '', 'title' => the_title_attribute( array('echo' => 0 ) ) );
		$args = wp_parse_args( $args, $defaults );
		
		$post_title = $args['alternative'] ? $args['alternative'] : get_the_title();
		$post_title = $post_title ? $post_title : $args['fallback'];
		$link_target = $args['target'] ? ' target="'.$args['target'].'"' : '';
		if ( $post_title ) $post_title = '<h2 class="storytitle"><a title="' . $args['title'] . '" href="' . $args['href'] . '"' . $link_target . ' rel="bookmark">' . $post_title . '</a></h2>';

		// Check if this is a post or page, if it has a thumbnail, and if it's a big one
		if ( $args['featured'] && ( $boozurk_opt['boozurk_featured_title'] == 1 ) && has_post_thumbnail( $post->ID ) && ( $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'post-thumbnail' ) ) ) {
			?>
			<div class="storycontent bz-featured-title">
				<?php echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' ); ?>
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
		// extra info management

		?>
		<div class="post_meta_container">
			<a class="pmb_format bz-thumb-format" href="<?php the_permalink() ?>" rel="bookmark"></a>
			<?php
				$page_cd_nc = ( is_page() && !comments_open() && !have_comments() ); //true if page with comments disabled and no comments
				if ( !$page_cd_nc ) {
					if( $comm && !post_password_required() ) comments_popup_link( '0', '1', '%', 'pmb_comm', '-'); // number of comments
				}
			?>
		</div>
		<?php if ( !is_singular() ) edit_post_link(); ?>
		<?php
	}
}

// print extra info for posts/pages
if ( !function_exists( 'boozurk_post_details' ) ) {
	function boozurk_post_details( $auth, $date, $tags, $cats, $hiera = false, $av_size = 48 ) {
		global $post;
		?>
			<?php if ( $auth ) {
				$author = $post->post_author;
				
				$name = get_the_author_meta('nickname', $author);
				$alt_name = get_the_author_meta('user_nicename', $author);
				$avatar = get_avatar($author, $av_size, 'Gravatar Logo', $alt_name.'-photo');
				$description = get_the_author_meta('description', $author);
				$author_link = get_author_posts_url($author);

				?>
				<div class="bz-author-bio">
					<ul>
						<li class="author-avatar"><?php echo $avatar; ?></li>
						<li class="author-name"><a href= "<?php echo $author_link; ?>" ><?php echo $name; ?></a></li>
						<li class="author-description"><?php echo $description; ?> </li>
						<li class="author-social">
							<?php if ( get_the_author_meta('twitter', $author) ) echo '<a target="_blank" class="url" title="' . sprintf( __('follow %s on Twitter', 'boozurk'), $name ) . '" href="'.get_the_author_meta('twitter', $author).'"><img alt="twitter" class="avatar" width="24" height="24" src="' . get_template_directory_uri() . '/images/follow/Twitter.png" /></a>'; ?>
							<?php if ( get_the_author_meta('facebook', $author) ) echo '<a target="_blank" class="url" title="' . sprintf( __('follow %s on Facebook', 'boozurk'), $name ) . '" href="'.get_the_author_meta('facebook', $author).'"><img alt="facebook" class="avatar" width="24" height="24" src="' . get_template_directory_uri() . '/images/follow/Facebook.png" /></a>'; ?>
						</li>
					</ul>
				</div>
			<?php } ?>
			<?php if ( $cats ) { echo '<span class="bz-post-details-cats">' . __( 'Categories', 'boozurk' ) . ': ' . '</span>'; the_category( ', ' ); echo '<br/>'; } ?>
			<?php if ( $tags ) { echo '<span class="bz-post-details-tags">' . __( 'Tags', 'boozurk' ) . ': ' . '</span>'; if ( !get_the_tags() ) { _e( 'No Tags', 'boozurk' ); } else { the_tags('', ', ', ''); } echo '<br/>'; } ?>
			<?php if ( $date ) { echo '<span class="bz-post-details-date">' . __( 'Published on', 'boozurk' ) . ': ' . '</span>'; echo '<b>' . get_the_time( get_option( 'date_format' ) ) . '</b>'; } ?>
		<?php
	}
}

//add share links to post/page
if ( !function_exists( 'boozurk_share_this' ) ) {
	function boozurk_share_this( $icon_size = 24 ){
		global $post;
		?>
		   <div class="article-share fixfloat">
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://twitter.com/share?url=<?php echo get_permalink(); ?>&amp;text=<?php echo urlencode( get_the_title() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Twitter.png" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" alt="Twitter Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Twitter' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://digg.com/submit?url=<?php echo get_permalink(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Digg.png" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" alt="Digg Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Digg' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://www.stumbleupon.com/submit?url=<?php echo get_permalink(); ?>&amp;title=<?php echo urlencode( get_the_title() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/StumbleUpon.png" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" alt="StumbleUpon Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'StumbleUpon' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo get_permalink(); ?>&amp;t=<?php echo urlencode( get_the_title() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Facebook.png" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" alt="Facebook Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Facebook' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://reddit.com/submit?url=<?php echo get_permalink(); ?>&amp;title=<?php echo urlencode( get_the_title() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Reddit.png" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" alt="Reddit Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Reddit' ); ?>" /></a>
				</span>		
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://www.google.com/reader/link?title=<?php echo urlencode( get_the_title() ); ?>&amp;url=<?php echo get_permalink(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Buzz.png" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" alt="Buzz Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Buzz' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://v.t.sina.com.cn/share/share.php?url=<?php echo get_permalink(); ?>&amp;title=<?php echo urlencode( get_the_title() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Sina.png" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" alt="Sina Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Sina' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://v.t.qq.com/share/share.php?title=<?php echo urlencode( get_the_title() ); ?>&amp;url=<?php echo get_permalink(); ?>&amp;site=<?php echo home_url(); ?>&amp;pic=<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Tencent.png" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" alt="Tencent Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Tencent' ); ?>" /></a>
				</span>
			</div>
		<?php
	}
}

//Image EXIF details
if ( !function_exists( 'boozurk_exif_details' ) ) {
	function boozurk_exif_details(){
		global $post; ?>
		<div class="exif-attachment-info">
			<?php
			$bz_imgmeta = wp_get_attachment_metadata();

			// Convert the shutter speed retrieve from database to fraction
			if ( $bz_imgmeta['image_meta']['shutter_speed'] && (1 / $bz_imgmeta['image_meta']['shutter_speed']) > 1) {
				if ((number_format((1 / $bz_imgmeta['image_meta']['shutter_speed']), 1)) == 1.3
				or number_format((1 / $bz_imgmeta['image_meta']['shutter_speed']), 1) == 1.5
				or number_format((1 / $bz_imgmeta['image_meta']['shutter_speed']), 1) == 1.6
				or number_format((1 / $bz_imgmeta['image_meta']['shutter_speed']), 1) == 2.5){
					$bz_pshutter = "1/" . number_format((1 / $bz_imgmeta['image_meta']['shutter_speed']), 1, '.', '');
				} else {
					$bz_pshutter = "1/" . number_format((1 / $bz_imgmeta['image_meta']['shutter_speed']), 0, '.', '');
				}
			} else {
				$bz_pshutter = $bz_imgmeta['image_meta']['shutter_speed'];
			}

			// Start to display EXIF and IPTC data of digital photograph
			echo __("Width", "boozurk" ) . ": " . $bz_imgmeta['width']."px<br />";
			echo __("Height", "boozurk" ) . ": " . $bz_imgmeta['height']."px<br />";
			if ( $bz_imgmeta['image_meta']['created_timestamp'] ) echo __("Date Taken", "boozurk" ) . ": " . date("d-M-Y H:i:s", $bz_imgmeta['image_meta']['created_timestamp'])."<br />";
			if ( $bz_imgmeta['image_meta']['copyright'] ) echo __("Copyright", "boozurk" ) . ": " . $bz_imgmeta['image_meta']['copyright']."<br />";
			if ( $bz_imgmeta['image_meta']['credit'] ) echo __("Credit", "boozurk" ) . ": " . $bz_imgmeta['image_meta']['credit']."<br />";
			if ( $bz_imgmeta['image_meta']['title'] ) echo __("Title", "boozurk" ) . ": " . $bz_imgmeta['image_meta']['title']."<br />";
			if ( $bz_imgmeta['image_meta']['caption'] ) echo __("Caption", "boozurk" ) . ": " . $bz_imgmeta['image_meta']['caption']."<br />";
			if ( $bz_imgmeta['image_meta']['camera'] ) echo __("Camera", "boozurk" ) . ": " . $bz_imgmeta['image_meta']['camera']."<br />";
			if ( $bz_imgmeta['image_meta']['focal_length'] ) echo __("Focal Length", "boozurk" ) . ": " . $bz_imgmeta['image_meta']['focal_length']."mm<br />";
			if ( $bz_imgmeta['image_meta']['aperture'] ) echo __("Aperture", "boozurk" ) . ": f/" . $bz_imgmeta['image_meta']['aperture']."<br />";
			if ( $bz_imgmeta['image_meta']['iso'] ) echo __("ISO", "boozurk" ) . ": " . $bz_imgmeta['image_meta']['iso']."<br />";
			if ( $bz_pshutter ) echo __("Shutter Speed", "boozurk" ) . ": " . sprintf( '%s seconds', $bz_pshutter) . "<br />"
			?>
		</div>
		<?php
	}
}

if (!function_exists('boozurk_search_reminder')) {
	function boozurk_search_reminder(){
		// search reminder
		if ( is_category() ) {
			if ( category_description() ) {
				echo '<div class="bz-breadcrumb-reminder">' . category_description() . '</div>';
			}
		} elseif (is_author()) {
			echo '<div class="vcard bz-breadcrumb-reminder">' . __( 'Author','boozurk' ) . ': <span class="fn"><strong>' . wp_title( '',false,'right' ) . '</strong></span>';
			$bz_author = get_queried_object();
			// If a user has filled out their description, show a bio on their entries.
			if ( $bz_author->description ) { ?>
				<div id="entry-author-info">
					<?php echo get_avatar( $bz_author->user_email, 32, $default= get_template_directory_uri() . '/images/user.png','user-avatar' ); ?>
					<?php
						if ( $bz_author->twitter ) echo '<a class="url" title="' . sprintf( __('follow %s on Twitter', 'boozurk'), $bz_author->display_name ) . '" href="'.$bz_author->twitter.'"><img alt="twitter" class="avatar" width=32 height=32 src="' . get_template_directory_uri() . '/images/follow/Twitter.png" /></a>';
						if ( $bz_author->facebook ) echo '<a class="url" title="' . sprintf( __('follow %s on Facebook', 'boozurk'), $bz_author->display_name ) . '" href="'.$bz_author->facebook.'"><img alt="facebook" class="avatar" width=32 height=32 src="' . get_template_directory_uri() . '/images/follow/Facebook.png" /></a>';
					?>
					<br />
					<?php echo $bz_author->description; ?>
				</div><!-- #entry-author-info -->
			<?php }
			echo '</div>';
		} elseif ( is_page() ) {
			boozurk_multipages();
		}
	}
}

// the last commenters of a post
if ( !function_exists( 'boozurk_last_comments' ) ) {
	function boozurk_last_comments( $id , $num = 5 ) {
		global $boozurk_opt;
		$comments = get_comments( 'status=approve&number=' . $num . '&type=comment&post_id=' . $id ); // valid type values (not documented) : 'pingback','trackback','comment'
		if ( $comments ) { ?>
			<div class="bz-last-cop fixfloat">
				<span class="item-label"><?php _e('last comments','boozurk'); ?></span>
				<span class="bz-breadcrumb-sep item-label">&nbsp;</span>
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

/*
Based on Yoast Breadcrumbs Plugin (http://yoast.com/wordpress/breadcrumbs/)
*/
function boozurk_breadcrumb($prefix = '<div id="bz-breadcrumb">', $suffix = '</div>') {
	global $wp_query, $post;
	
	$opt 						= array();
	$opt['home'] 				= "Home";
	$opt['sep'] 				= '<span class="bz-breadcrumb-sep">&nbsp;</span>';
	$opt['archiveprefix'] 		= "Archives for";
	$opt['searchprefix'] 		= "Search for";

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
		$bloglink = $homelink.' '.$opt['sep'].' <a href="'.get_permalink(get_option('page_for_posts')).'">'.get_the_title(get_option('page_for_posts')).'</a>';
	} else {
		$homelink = '<a class="bz-breadcrumb-home"'.$nofollow.'href="'.home_url().'">&nbsp;</a>';
		$bloglink = $homelink;
	}
		
	if ( ($on_front == "page" && is_front_page()) || ($on_front == "posts" && is_home()) ) {
		$output = $homelink.' '.$opt['sep'].' '.'<span>'.$opt['home'].'</span>';
	} elseif ( $on_front == "page" && is_home() ) {
		$output = $homelink.' '.$opt['sep'].' '.'<span>'.get_the_title(get_option('page_for_posts')).'</span>';
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
			$output .= '<span>'.boozurk_get_category_parents($cat, false, " ".$opt['sep']." ").' ('.$wp_query->found_posts.')'.'</span>';
		} elseif ( is_tag() ) {
			$output .= '<span>'.$opt['archiveprefix']." ".single_cat_title('',false).' ('.$wp_query->found_posts.')'.'</span>';
		} elseif ( is_404() ) {
			$output .= '<span>'.__( 'Page not found','boozurk' ).'</span>';
		} elseif ( is_date() ) { 
			$output .= '<span>'.$opt['archiveprefix']." ".single_month_title(' ',false).' ('.$wp_query->found_posts.')'.'</span>';
		} elseif ( is_author() ) { 
			$user = get_userdatabylogin($wp_query->query_vars['author_name']);
			$output .= '<span>'.$opt['archiveprefix']." ".$user->display_name.' ('.$wp_query->found_posts.')'.'</span>';
		} elseif ( is_search() ) {
			$output .= '<span>'.$opt['searchprefix'].' "'.stripslashes(strip_tags(get_search_query())).'" ('.$wp_query->found_posts.')'.'</span>';
		} elseif ( is_attachment() ) {
			if ( $post->post_parent ) {
				$output .= '<a href="'.get_permalink( $post->post_parent ).'">'.get_the_title( $post->post_parent ).'</a> '.$opt['sep'];
			}
			$output .= '<span>'.get_the_title().'</span>';
		} else if ( is_tax() ) {
			$taxonomy 	= get_taxonomy ( get_query_var('taxonomy') );
			$term 		= get_query_var('term');
			$output .= '<span>'.$taxonomy->label .': '. $term.' ('.$wp_query->found_posts.')'.'</span>';
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
				$tmp['title'] 	= strip_tags( get_the_title( $ancestor ) );
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
	echo $prefix;
	echo $output;
	boozurk_search_reminder();
	echo $suffix;
}



if (!function_exists('boozurk_navbuttons')) {
	function boozurk_navbuttons( $print = 1, $comment = 1, $feed = 1, $trackback = 1, $home = 1, $next_prev = 1, $up_down = 1, $fixed = 1 ) {
		global $post, $boozurk_opt, $bz_is_allcat_page;
		
		$is_post = is_single() && !is_attachment() && !$bz_is_allcat_page;
		$is_image = is_attachment() && !$bz_is_allcat_page;
		$is_page = is_singular() && !is_single() && !is_attachment() && !$bz_is_allcat_page;
		$is_singular = is_singular() && !$bz_is_allcat_page;
	?>

<div id="navbuttons"<?php if ( $fixed ) echo ' class="fixed"'; ?>>

		<?php if ( $is_singular && get_edit_post_link() ) { 																		// ------- Edit ------- ?>
			<div class="minibutton" title="<?php _e( 'Edit','boozurk' ); ?>">
				<a href="<?php echo get_edit_post_link(); ?>">
					<span class="minib_img minib_edit">&nbsp;</span>
				</a>
			</div>
		<?php } ?>
		
		<?php if ( $print && $is_singular ) { 																// ------- Print ------- ?>
			<div class="minibutton" title="<?php _e( 'Print','boozurk' ); ?>">
				<a href="<?php
					$bz_arr_params['style'] = 'printme';
					if ( get_query_var('page') ) {
						$bz_arr_params['page'] = esc_html( get_query_var( 'page' ) );
					}
					if ( get_query_var('cpage') ) {
						$bz_arr_params['cpage'] = esc_html( get_query_var( 'cpage' ) );
					}
					echo add_query_arg( $bz_arr_params, get_permalink() );
					?>">
					<span class="minib_img minib_print">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $comment && $is_singular && comments_open( $post->ID ) && !post_password_required() ) { 	// ------- Leave a comment ------- ?>
			<div class="minibutton" title="<?php _e( 'Leave a comment','boozurk' ); ?>">
				<a href="#respond">
					<span class="minib_img minib_comment">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $feed && $is_singular && comments_open( $post->ID ) && !post_password_required() ) { 	// ------- RSS feed ------- ?>
			<div class="minibutton" title="<?php _e( 'Feed for comments on this post', 'boozurk' ); ?>">
				<a href="<?php echo get_post_comments_feed_link( $post->ID, 'rss2' ); ?> ">
					<span class="minib_img minib_rss">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $trackback && $is_singular && pings_open() ) { 											// ------- Trackback ------- ?>
			<div class="minibutton" title="<?php _e( 'Trackback URL','boozurk' ); ?>">
				<a href="<?php global $bz_tmptrackback; echo $bz_tmptrackback; ?>" rel="trackback">
					<span class="minib_img minib_track">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $home ) { 																				// ------- Home ------- ?>
			<div class="minibutton" title="<?php _e( 'Home','boozurk' ); ?>">
				<a href="<?php echo home_url(); ?>">
					<span class="minib_img minib_home">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $is_image ) { 																			// ------- Back to parent post ------- ?>
			<?php if ( !empty( $post->post_parent ) ) { ?>
				<div class="minibutton" title="<?php esc_attr( printf( __( 'Return to %s', 'boozurk' ), get_the_title( $post->post_parent ) ) ); ?>">
					<a href="<?php echo get_permalink( $post->post_parent ); ?>" rel="gallery">
						<span class="minib_img minib_backtopost">&nbsp;</span>
					</a>
				</div>
			<?php } ?>
		<?php } ?>

		<?php if ( $next_prev && $is_post && get_next_post() ) { 											// ------- Next post ------- ?>
			<div class="minibutton" title="<?php esc_attr( printf( __( 'Next Post', 'boozurk' ) . ': %s', get_the_title( get_next_post() ) ) ); ?>">
				<a href="<?php echo get_permalink( get_next_post() ); ?>">
					<span class="minib_img minib_npage">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if (  $next_prev && $is_post && get_previous_post() ) { 										// ------- Previous post ------- ?>
			<div class="minibutton" title="<?php esc_attr( printf( __( 'Previous Post', 'boozurk' ) . ': %s', get_the_title( get_previous_post() ) ) ); ?>">
				<a href="<?php echo get_permalink( get_previous_post() ); ?>">
					<span class="minib_img minib_ppage">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $next_prev && !$is_singular && !$bz_is_allcat_page && get_previous_posts_link() ) { 		// ------- Newer Posts ------- ?>
			<div class="minibutton" title="<?php echo __( 'Newer Posts', 'boozurk' ); ?>">
				<?php previous_posts_link( '<span class="minib_img minib_ppages">&nbsp;</span>' ); ?>
			</div>
		<?php } ?>

		<?php if ( $next_prev && !$is_singular && !$bz_is_allcat_page && get_next_posts_link() ) { 			// ------- Older Posts ------- ?>
			<div class="minibutton" title="<?php echo __( 'Older Posts', 'boozurk' ); ?>">
				<?php next_posts_link( '<span class="minib_img minib_npages">&nbsp;</span>' ); ?>
			</div>
		<?php } ?>

		<?php if ( $up_down ) { 																			// ------- Top ------- ?>
			<div class="minibutton" title="<?php _e( 'Top of page', 'boozurk' ); ?>">
				<a href="#">
					<span class="minib_img minib_top">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $up_down ) { 																			// ------- Bottom ------- ?>
			<div class="minibutton" title="<?php _e( 'Bottom of page', 'boozurk' ); ?>">
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

// get the post thumbnail or (if not set) the format related icon
if ( !function_exists( 'boozurk_get_the_thumb' ) ) {
	function boozurk_get_the_thumb( $id, $size_w, $size_h, $class, $default = '' ) {
		if ( has_post_thumbnail( $id ) ) {
			return get_the_post_thumbnail( $id, array( $size_w,$size_h ), array( 'class' => $class ) );
		} else {
			if ( function_exists( 'get_post_format' ) && get_post_format( $id ) ) {
				$format = get_post_format( $id );
			} else {
				$format = 'standard';
			}
			return '<img class="' . $class . ' wp-post-image bz-thumb-format ' . $format . '" alt="thumb" src="' . get_template_directory_uri() . '/images/img40.png" />';
		}
	}
}

// Get first image of a post
if ( !function_exists( 'boozurk_get_first_image' ) ) {
	function boozurk_get_first_image() {
		global $post, $posts;
		$first_info = array( 'img' => '', 'title' => '', 'src' => '' );
		//search the images in post content
		preg_match_all( '/<img[^>]+>/i',$post->post_content, $result );
		//grab the first one
		if ( isset( $result[0][0] ) ){
			$first_info['img'] = $result[0][0];
			$first_img = $result [0][0];
			//get the title (if any)
			preg_match_all( '/(title)=("[^"]*")/i',$first_img, $img_title );
			if ( isset( $img_title[2][0] ) ){
				$first_info['title'] = str_replace( '"','',$img_title[2][0] );
			}
			//get the path
			preg_match_all( '/(src)=("[^"]*")/i',$first_img, $img_src );
			if ( isset( $img_src[2][0] ) ){
				$first_info['src'] = str_replace( '"','',$img_src[2][0] );
			}
			return $first_info;
		} else {
			return false;
		}
	}
}

// Get first link of a post
if ( !function_exists( 'boozurk_get_first_link' ) ) {
	function boozurk_get_first_link() {
		global $post, $posts;
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
		global $post, $posts;
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

// search for linked mp3's and add an audio player
if ( !function_exists( 'boozurk_add_audio_player' ) ) {
	function boozurk_add_audio_player( $text = '' ) {
		global $boozurk_opt, $bz_is_mobile_browser, $bz_is_printpreview, $post;
		if ( is_admin() || ( $boozurk_opt['boozurk_js_swfplayer'] == 0 ) || $bz_is_mobile_browser || $bz_is_printpreview ) return;
		$pattern = "/<a ([^=]+=['\"][^\"']+['\"] )*href=['\"](([^\"']+\.(mp3|ogg|m4a)))['\"]( [^=]+=['\"][^\"']+['\"])*>([^<]+)<\/a>/i";
		if ( $text == '')
			preg_match_all( $pattern, $post->post_content, $result );
		else
			preg_match_all( $pattern, $text, $result );

		foreach ($result[0] as $key => $value) {
?>
<div class="bz-player-container">
	<small><?php echo $result[0][0];?></small>
	<div class="bz-player-content">
		<audio controls="">
			<source src="<?php echo $result[3][$key];?>" />
		</audio>
	</div>
</div>
<?php
		}
	}
}

// create theme option page
if ( !function_exists( 'boozurk_create_menu' ) ) {
	function boozurk_create_menu() {
		//create new top-level menu
		$pageopt = add_theme_page( __( 'Theme Options','boozurk' ), __( 'Theme Options','boozurk' ), 'edit_theme_options', 'tb_boozurk_functions', 'boozurk_edit_options' );
		//call register settings function
		add_action( 'admin_init', 'boozurk_register_tb_settings' );
		add_action( 'admin_print_styles-' . $pageopt, 'boozurk_theme_admin_styles' );
		add_action( 'admin_print_scripts-' . $pageopt, 'boozurk_theme_admin_scripts' );
		add_action( 'admin_print_styles-widgets.php', 'boozurk_widgets_style' );
		add_action( 'admin_print_scripts-widgets.php', 'boozurk_widgets_scripts' );
	}
}

if ( !function_exists( 'boozurk_theme_admin_scripts' ) ) {
	function boozurk_theme_admin_scripts() {
		global $boozurk_version;
		wp_enqueue_script( 'boozurk-options-script', get_template_directory_uri().'/js/options.dev.js',array('jquery','farbtastic'),$boozurk_version, true ); //boozurk js
	}
}

if ( !function_exists( 'boozurk_widgets_style' ) ) {
	function boozurk_widgets_style() {
		//add custom stylesheet
		wp_enqueue_style( 'bz-widgets-style', get_template_directory_uri() . '/css/widgets.css', false, '', 'screen' );
	}
}

if ( !function_exists( 'boozurk_widgets_scripts' ) ) {
	function boozurk_widgets_scripts() {
		global $boozurk_version;
		wp_enqueue_script( 'bz-widgets-scripts', get_template_directory_uri() . '/js/widgets.dev.js', array('jquery'), $boozurk_version, true );
	}
}

if ( !function_exists( 'boozurk_register_tb_settings' ) ) {
	function boozurk_register_tb_settings() {
		//register boozurk settings
		register_setting( 'bz_settings_group', 'boozurk_options', 'boozurk_sanitize_options' );
	}
}

// sanitize options value
if ( !function_exists( 'boozurk_sanitize_options' ) ) {
	function boozurk_sanitize_options($input) {
		global $boozurk_current_theme;
		$boozurk_coa = boozurk_get_coa();
		// check for updated values and return 0 for disabled ones <- index notice prevention
		foreach ( $boozurk_coa as $key => $val ) {
	
			if( $boozurk_coa[$key]['type'] == 'chk' ) {
				if( !isset( $input[$key] ) ) {
					$input[$key] = 0;
				} else {
					$input[$key] = ( $input[$key] == 1 ? 1 : 0 );
				}
			} elseif( $boozurk_coa[$key]['type'] == 'sel' ) {
				if ( !in_array( $input[$key], $boozurk_coa[$key]['options'] ) ) $input[$key] = $boozurk_coa[$key]['default'];
			} elseif( $boozurk_coa[$key]['type'] == 'col' ) {
				$color = str_replace( '#' , '' , $input[$key] );
				$color = preg_replace( '/[^0-9a-fA-F]/' , '' , $color );
				$input[$key] = '#' . $color;
			} elseif( $boozurk_coa[$key]['type'] == 'url' ) {
				$input[$key] = esc_url( $input[$key] );
			}
		}
		foreach ( $input['boozurk_cat_colors'] as $key => $val ) {
			$color = str_replace( '#' , '' , $input['boozurk_cat_colors'][$key] );
			$color = preg_replace( '/[^0-9a-fA-F]/' , '' , $color );
			$input['boozurk_cat_colors'][$key] = '#' . $color;
		}
		// check for required options
		foreach ( $boozurk_coa as $key => $val ) {
			if ( $boozurk_coa[$key]['req'] != '' ) { if ( $input[$boozurk_coa[$key]['req']] == ( 0 || '') ) $input[$key] = 0; }
		}
		//$input['hidden_opt'] = 'default'; //this hidden option avoids empty $boozurk_options when updated
		$input['version'] = $boozurk_current_theme['Version']; // keep version number
		return $input;
	}
}

// the custon header style - called only on your theme options page
if ( !function_exists( 'boozurk_theme_admin_styles' ) ) {
	function boozurk_theme_admin_styles() {
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_style( 'bz-options-style', get_template_directory_uri() . '/css/options.css', false, '', 'screen' );
		?>
		<style type="text/css">
			#boozurk-infos-li div.wp-menu-image {
				background: url('<?php echo admin_url(); ?>/images/menu.png') no-repeat scroll -38px -39px transparent;
			}
			#boozurk-infos-li:hover div.wp-menu-image,
			#boozurk-infos-li.tab-selected div.wp-menu-image {
				background: url('<?php echo admin_url(); ?>/images/menu.png') no-repeat scroll -38px -7px transparent;
			}
		</style>
		<?php
	}
}

// the theme option page
if ( !function_exists( 'boozurk_edit_options' ) ) {
	function boozurk_edit_options() {
	  if ( !current_user_can( 'edit_theme_options' ) ) {
	    wp_die( 'You do not have sufficient permissions to access this page.' );
	  }
		global $boozurk_opt, $boozurk_current_theme;
		$boozurk_coa = boozurk_get_coa();
		
		// update version value when admin visit options page
		if ( $boozurk_opt['version'] < $boozurk_current_theme['Version'] ) {
			$boozurk_opt['version'] = $boozurk_current_theme['Version'];
			update_option( 'boozurk_options' , $boozurk_opt );
		}
		
		// options have been updated
		if ( isset( $_REQUEST['settings-updated'] ) ) {
			//return options save message
			echo '<div style="position: absolute;left: 50%;" id="message" class="updated fade"><p><strong>' . __( 'Options saved.','boozurk' ) . '</strong></p></div>';
		}
		
	?>
		<div class="wrap" id="bz-main-wrap">
			<div class="icon32" id="bz-icon"><br></div>
			<h2><?php echo get_current_theme() . ' - ' . __( 'Theme Options','boozurk' ); ?></h2>
			<ul id="bz-tabselector" class="hide-if-no-js">
				<li id="bz-selgroup-colors"><a href="#" onClick="boozurkSwitchTab.set('colors'); return false;"><?php _e( 'Colors' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-postformats"><a href="#" onClick="boozurkSwitchTab.set('postformats'); return false;"><?php _e( 'Post formats' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-widgets"><a href="#" onClick="boozurkSwitchTab.set('widgets'); return false;"><?php _e( 'Widgets' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-javascript"><a href="#" onClick="boozurkSwitchTab.set('javascript'); return false;"><?php _e( 'Javascript' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-other"><a href="#" onClick="boozurkSwitchTab.set('other'); return false;"><?php _e( 'Other' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-info"><a href="#" onClick="boozurkSwitchTab.set('info'); return false;"><?php _e( 'Theme Info' , 'boozurk' ); ?></a></li>
			</ul>
			<ul id="selector" class="hide-if-js">
				<li id="boozurk-options-li"><a href="#boozurk-options"><?php _e( 'Options','boozurk' ); ?></a></li>
				<li id="boozurk-infos-li"><a href="#boozurk-infos"><?php _e( 'Theme Info','boozurk' ); ?></a></li>
			</ul>
			<div id="tabs-container">
				<div class="clear"></div>
				<div id="boozurk-options">
					<h2 class="hide-if-js" style="text-align: center;"><?php _e( 'Options','boozurk' ); ?></h2>
					<form method="post" action="options.php">
						<?php settings_fields( 'bz_settings_group' ); ?>
						<div id="stylediv">
							<?php foreach ($boozurk_coa as $key => $val) { ?>
								<div class="bz-tab-opt bz-tabgroup-<?php echo $boozurk_coa[$key]['group']; ?>">
									<span class="column-nam"><?php echo $boozurk_coa[$key]['description']; ?></span>
								<?php if ( $boozurk_coa[$key]['type'] == 'chk' ) { ?>
										<input name="boozurk_options[<?php echo $key; ?>]" value="1" type="checkbox" class="ww_opt_p_checkbox" <?php checked( 1 , $boozurk_opt[$key] ); ?> />
										<?php if ( $boozurk_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $boozurk_coa[$key]['info']; ?></div><?php } ?>
								<?php } elseif ( $boozurk_coa[$key]['type'] == 'sel' ) { ?>
										<select name="boozurk_options[<?php echo $key; ?>]">
										<?php foreach($boozurk_coa[$key]['options'] as $option) { ?>
											<option value="<?php echo $option; ?>" <?php selected( $boozurk_opt[$key], $option ); ?>><?php echo $option; ?></option>
										<?php } ?>
										</select>
										<?php if ( $boozurk_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $boozurk_coa[$key]['info']; ?></div><?php } ?>
								<?php } elseif ( $boozurk_coa[$key]['type'] == 'url' ) { ?>
										<input class="bz_text" id="bz_text_<?php echo $key; ?>" type="text" name="boozurk_options[<?php echo $key; ?>]" value="<?php echo $boozurk_opt[$key]; ?>" />
										<?php if ( $boozurk_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $boozurk_coa[$key]['info']; ?></div><?php } ?>
								<?php } elseif ( $boozurk_coa[$key]['type'] == 'col' ) { ?>
										<div class="bz-col-tools">
											<input onclick="showMeColorPicker('<?php echo $key; ?>');" style="background-color:<?php echo $boozurk_opt[$key]; ?>;" class="color_preview_box" type="text" id="bz_box_<?php echo $key; ?>" value="" readonly="readonly" />
											<div class="bz_cp" id="bz_colorpicker_<?php echo $key; ?>"></div>
											<input class="bz_input" id="bz_input_<?php echo $key; ?>" type="text" name="boozurk_options[<?php echo $key; ?>]" value="<?php echo $boozurk_opt[$key]; ?>" />
											<a class="hide-if-no-js" href="#" onclick="showMeColorPicker('<?php echo $key; ?>'); return false;"><?php _e( 'Select a Color' , 'boozurk' ); ?></a>&nbsp;-&nbsp;
											<a class="hide-if-no-js" style="color:<?php echo $boozurk_coa[$key]['default']; ?>;" href="#" onclick="pickColor('<?php echo $key; ?>','<?php echo $boozurk_coa[$key]['default']; ?>'); return false;"><?php _e( 'Default' , 'boozurk' ); ?></a>
										</div>
								<?php } elseif ( $boozurk_coa[$key]['type'] == 'catcol' ) { ?>
										<div>
										<?php
											$args=array(
												'orderby' => 'name',
												'order' => 'ASC'
											);
											$categories=get_categories($args);
											foreach($categories as $category) {
												$catcolor = isset($boozurk_opt[$key][$category->term_id]) ? $boozurk_opt[$key][$category->term_id] : $boozurk_coa[$key]['default'];
										?>
											<?php echo $category->name; ?>
											<div class="bz-col-tools">
												<input onclick="showMeColorPicker('<?php echo $key.'-'.$category->term_id; ?>');" style="background-color:<?php echo $catcolor; ?>;" class="color_preview_box" type="text" id="bz_box_<?php echo $key.'-'.$category->term_id; ?>" value="" readonly="readonly" />
												<div class="bz_cp" id="bz_colorpicker_<?php echo $key.'-'.$category->term_id; ?>"></div>
												<input class="bz_input" id="bz_input_<?php echo $key.'-'.$category->term_id; ?>" type="text" name="boozurk_options[<?php echo $key; ?>][<?php echo $category->term_id; ?>]" value="<?php echo $catcolor; ?>" />
												<a class="hide-if-no-js" href="#" onclick="showMeColorPicker('<?php echo $key.'-'.$category->term_id; ?>'); return false;"><?php _e( 'Select a Color' , 'boozurk' ); ?></a>&nbsp;-&nbsp;
												<a class="hide-if-no-js" style="color:<?php echo $boozurk_coa[$key]['default']; ?>;" href="#" onclick="pickColor('<?php echo $key.'-'.$category->term_id; ?>','<?php echo $boozurk_coa[$key]['default']; ?>'); return false;"><?php _e( 'Default' , 'boozurk' ); ?></a>
											</div>
										<?php }	?>
										
										</div>
								<?php }	?>
									<?php if ( $boozurk_coa[$key]['req'] != '' ) { ?><div class="column-req"><?php echo '<u>' . __('requires','boozurk') . '</u>: ' . $boozurk_coa[$boozurk_coa[$key]['req']]['description']; ?></div><?php } ?>
								</div>
							<?php }	?>
						</div>
						<p>
							<input type="hidden" name="boozurk_options[hidden_opt]" value="default" />
							<input class="button" type="submit" name="Submit" value="<?php _e( 'Update Options' , 'boozurk' ); ?>" />
							<a style="font-size: 10px; text-decoration: none; margin-left: 10px; cursor: pointer;" href="themes.php?page=functions" target="_self"><?php _e( 'Undo Changes' , 'boozurk' ); ?></a>
						</p>
					</form>
					<p class="stylediv" style="clear: both; text-align: center; border: 1px solid #ccc;">
						<small>
							<?php _e( 'If you like/dislike this theme, or if you encounter any issues using it, please let us know it.', 'boozurk' ); ?><br />
							<a href="<?php echo esc_url( 'http://www.twobeers.net/annunci/tema-per-wordpress-boozurk' ); ?>" title="boozurk theme" target="_blank"><?php _e( 'Leave a feedback', 'boozurk' ); ?></a>
						</small>
					</p>
					<p class="stylediv" style="clear: both; text-align: center; border: 1px solid #ccc; margin-top: 10px;">
						<small>Support the theme in your language, provide a <a href="<?php echo esc_url( 'http://www.twobeers.net/temi-wp/wordpress-themes-translations' ); ?>" title="Themes translation" target="_blank">translation</a>.</small>
					</p>
				</div>
				<div id="boozurk-infos">
					<h2 class="hide-if-js" style="text-align: center;"><?php _e( 'Theme Info','boozurk' ); ?><h2>
					<?php get_template_part( 'readme' ); ?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	<?php
	}
}

// set up custom colors and header image
if ( !function_exists( 'boozurk_setup' ) ) {
	function boozurk_setup() {
		global $boozurk_opt;
		
		// Register localization support
		load_theme_textdomain('boozurk', TEMPLATEPATH . '/languages' );
		// Theme uses wp_nav_menu() in three location
		register_nav_menus( array( 'primary' => __( 'Main Navigation Menu', 'boozurk' )	) );
		register_nav_menus( array( 'secondary1' => __( 'Secondary Navigation Menu #1', 'boozurk' )	) );
		register_nav_menus( array( 'secondary2' => __( 'Secondary Navigation Menu #2', 'boozurk' )	) );
		// Register Features Support
		add_theme_support( 'automatic-feed-links' );
		// Thumbnails support
		add_theme_support( 'post-thumbnails' );
		// Add the editor style
		if ( isset( $boozurk_opt['boozurk_editor_style'] ) && ( $boozurk_opt['boozurk_editor_style'] == 1 ) ) add_editor_style( 'css/editor-style.css' );
	
		// This theme uses post formats
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
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

// pages navigation links
if ( !function_exists( 'boozurk_page_navi' ) ) {
	function boozurk_page_navi($this_page_id) {
		$pages = get_pages( array('sort_column' => 'menu_order') ); // get the menu-ordered list of the pages
		$page_links = array();
		foreach ($pages as $k => $pagg) {
			if ( $pagg->ID == $this_page_id ) { // we are in this $pagg
				if ( $k == 0 ) { // is first page
					$page_links['next']['link'] = get_page_link($pages[1]->ID);
					$page_links['next']['title'] = $pages[1]->post_title;
					if ( $page_links['next']['title'] == '' ) $page_links['next']['title'] = __( '(no title)','boozurk' );
				} elseif ( $k == ( count( $pages ) -1 ) ) { // is last page
					$page_links['prev']['link'] = get_page_link($pages[$k - 1]->ID);
					$page_links['prev']['title'] = $pages[$k - 1]->post_title;
					if ( $page_links['prev']['title'] == '' ) $page_links['prev']['title'] = __( '(no title)','boozurk' );
				} else {
					$page_links['next']['link'] = get_page_link($pages[$k + 1]->ID);
					$page_links['next']['title'] = $pages[$k + 1]->post_title;
					if ( $page_links['next']['title'] == '' ) $page_links['next']['title'] = __( '(no title)','boozurk' );
					$page_links['prev']['link'] = get_page_link($pages[$k - 1]->ID);
					$page_links['prev']['title'] = $pages[$k - 1]->post_title;
					if ( $page_links['prev']['title'] == '' ) $page_links['prev']['title'] = __( '(no title)','boozurk' );
				}
			}
		}
		return $page_links;
	}
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
		
		echo $dateWithNiceTone;
			
	}
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

// add 'quoted on' before trackback/pingback comments link
if ( !function_exists( 'boozurk_add_quoted_on' ) ) {
	function boozurk_add_quoted_on( $return ) {
		global $comment;
		$text = '';
		if ( get_comment_type() != 'comment' ) {
			$text = '<span style="font-weight: normal;">' . __( 'quoted on', 'boozurk' ) . ' </span>';
		}
		return $text . $return;
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
    function start_el(&$output, $item, $depth, $args)
    {
		global $boozurk_opt;
        $classes     = empty ( $item->classes ) ? array () : (array) $item->classes;

        $class_names = join(
            ' '
        ,   apply_filters(
                'nav_menu_css_class'
            ,   array_filter( $classes ), $item
            )
        );

        ! empty ( $class_names )
            and $class_names = ' class="'. esc_attr( $class_names ) . '"';

        $output .= "<li id='menu-item-$item->ID' $class_names>";

        $attributes  = '';

        ! empty( $item->attr_title )
            and $attributes .= ' title="'  . esc_attr( $item->attr_title ) .'"';
        ! empty( $item->target )
            and $attributes .= ' target="' . esc_attr( $item->target     ) .'"';
        ! empty( $item->xfn )
            and $attributes .= ' rel="'    . esc_attr( $item->xfn        ) .'"';
        ! empty( $item->url )
            and $attributes .= ' href="'   . esc_attr( $item->url        ) .'"';


        $title = apply_filters( 'the_title', $item->title, $item->ID );
		
		if (  0 == $depth )	{
			$thumb = '<img class="bz-menu-thumb default" width="' . (int)$boozurk_opt['boozurk_main_menu_icon_size'] . '" height="' . (int)$boozurk_opt['boozurk_main_menu_icon_size'] . '" alt="' . $title . '" src="' . get_template_directory_uri() . '/images/img40.png" />';
			if ( has_post_thumbnail((int)$item->object_id) ) {
				$thumb = get_the_post_thumbnail( (int)$item->object_id, array((int)$boozurk_opt['boozurk_main_menu_icon_size'],(int)$boozurk_opt['boozurk_main_menu_icon_size']), array( 'title' => $title, 'class' => 'bz-menu-thumb' ) );
			}
			if ( $boozurk_opt['boozurk_main_menu'] == __('thumbnail','boozurk') ) {
				$title = $thumb;
			} elseif ( $boozurk_opt['boozurk_main_menu'] == __('thumbnail and text','boozurk') ) {
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
        ,   $item_output
        ,   $item
        ,   $depth
        ,   $args
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

function boozurk_login_footer() {
	global $boozurk_opt;
	?>	
<script type="text/javascript">
	/* <![CDATA[ */
		div = document.createElement('div');
		div.id = 'bz-logo';
		div.innerHTML = '<a href="<?php echo home_url(); ?>"><img src="<?php echo $boozurk_opt['boozurk_logo']; ?>" alt="logo" title="<?php echo get_bloginfo('description'); ?>" /></a>';
		d = document.getElementById('login');
		first = d.firstChild;
		d.insertBefore(div,first);
	/* ]]> */
</script>
	<?php 
}

function boozurk_login_head() {
	?>	
<style type="text/css">
	#backtoblog,
	#login h1 {
		display: none;
	}
	#login {
		margin-top: 20px;
	}
	#bz-logo img {
		border: none;
		margin: 0 0 16px 8px;
		max-width: 312px;
	}
	#bz-logo {
		text-align: center;
	}
</style>
	<?php 
}

// Add Thumbnails in Manage Posts/Pages List
function boozurk_addthumbcolumn($cols) {
	$cols['thumbnail'] = ucwords( __('thumbnail','boozurk') );
	return $cols;
}
function boozurk_addthumbvalue($column_name, $post_id) {
		$width = (int) 60;
		$height = (int) 60;
		if ( 'thumbnail' == $column_name ) {
			// thumbnail of WP 2.9
			$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
			if ($thumbnail_id) $thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
			if ( isset($thumb) && $thumb ) {
				echo $thumb;
			} else {
				echo '';
			}
		}
}
// Add style element in Manage Posts/Pages List
if ( !function_exists( 'boozurk_post_manage_style' ) ) {
	function boozurk_post_manage_style(){
?>
<style type="text/css">
	.fixed .column-thumbnail {
		width: 70px;
	}
</style>
<?php
	}
}

add_action( 'admin_head', 'boozurk_post_manage_style' );
// for posts column-thumbnail
add_filter( 'manage_posts_columns', 'boozurk_addthumbcolumn' );
add_action( 'manage_posts_custom_column', 'boozurk_addthumbvalue', 10, 2 );
// for pages
add_filter( 'manage_pages_columns', 'boozurk_addthumbcolumn' );
add_action( 'manage_pages_custom_column', 'boozurk_addthumbvalue', 10, 2 );


// load the custom widgets module
if ( $boozurk_opt['boozurk_custom_widgets'] == 1 ) get_template_part('lib/widgets');

// load the custom hooks
get_template_part('lib/hooks');

?>