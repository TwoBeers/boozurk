<?php
/**** begin theme hooks ****/
// Tell WordPress to run boozurk_setup() when the 'after_setup_theme' hook is run.
add_action( 'after_setup_theme', 'boozurk_setup' );
// Tell WordPress to run boozurk_default_options()
add_action( 'admin_init', 'boozurk_default_options' );
// Register sidebars by running boozurk_widget_area_init() on the widgets_init hook
add_action( 'widgets_init', 'boozurk_widget_area_init' );
// Add stylesheets
add_action( 'wp_print_styles', 'boozurk_stylesheet' );
add_action( 'wp_head', 'boozurk_custom_style' );
add_action( 'wp_head', 'boozurk_localize_js' );
// Add js animations
add_action( 'template_redirect', 'boozurk_scripts' );
// Add custom category page
add_action( 'template_redirect', 'boozurk_allcat' );
// mobile redirect
add_action( 'template_redirect', 'boozurk_mobile' );
// Add admin menus
add_action( 'admin_menu', 'boozurk_create_menu' );
// post expander ajax request
add_action('init', 'boozurk_post_expander_activate');
// Custom filters
add_filter( 'img_caption_shortcode', 'boozurk_img_caption_shortcode', 10, 3 );
add_filter( 'use_default_gallery_style', '__return_false' );
// Custom shortcodes
/**** end theme hooks ****/

// load theme options in $boozurk_opt variable, globally retrieved in php files
$boozurk_opt = get_option( 'boozurk_options' );

// check if is mobile browser
$bz_is_mobile_browser = boozurk_mobile_device_detect();

function boozurk_mobile_device_detect() {
	global $boozurk_opt;
	if ( !isset($_SERVER['HTTP_USER_AGENT']) ) return false;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
    if ( ( !isset( $boozurk_opt['boozurk_mobile_css'] ) || ( $boozurk_opt['boozurk_mobile_css'] == 1) ) && preg_match( '/(ipad|ipod|iphone|android|opera mini|blackberry|palm|symbian|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine|iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i' , $user_agent ) ) { // there were other words for mobile detecting but this is enought ;-)
		return true;
	} else {
		return false;
	}
}

// check if is ie6
$bz_is_ie6 = boozurk_ie6_detect();

function boozurk_ie6_detect() {
if ( isset($_SERVER['HTTP_USER_AGENT']) && ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false ) && !( strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false ) ) {
		return true;
	} else {
		return false;
	}
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
		'boozurk_sidebar_head_split' => array( 'group' =>'sidebar', 'type' =>'sel', 'default'=>'3', 'options'=>array('1','2','3'), 'description'=>__( 'split Header widget area','boozurk' ),'info'=>__( 'number of widget that can stay in the widget area side by side','boozurk' ),'req'=>'' ),
		'boozurk_sidebar_single_split' => array( 'group' =>'sidebar', 'type' =>'sel', 'default'=>'1', 'options'=>array('1','2','3'), 'description'=>__( 'split Post widget area','boozurk' ),'info'=>__( 'number of widget that can stay in the widget area side by side','boozurk' ),'req'=>'' ),
		'boozurk_sidebar_foot_1_width' => array( 'group' =>'sidebar', 'type' =>'sel', 'default'=>'33%', 'options'=>array('100%','50%','33%'), 'description'=>__( 'footer widget area #1','boozurk' ),'info'=>__( 'width of the widget area','boozurk' ),'req'=>'' ),
		'boozurk_sidebar_foot_2_width' => array( 'group' =>'sidebar', 'type' =>'sel', 'default'=>'33%', 'options'=>array('100%','50%','33%'), 'description'=>__( 'footer widget area #2','boozurk' ),'info'=>__( 'width of the widget area','boozurk' ),'req'=>'' ),
		'boozurk_sidebar_foot_3_width' => array( 'group' =>'sidebar', 'type' =>'sel', 'default'=>'33%', 'options'=>array('100%','50%','33%'), 'description'=>__( 'footer widget area #3','boozurk' ),'info'=>__( 'width of the widget area','boozurk' ),'req'=>'' ),
		'boozurk_share_this' => array( 'group' =>'content', 'type' =>'chk', 'default'=>1,'description'=>__( 'share this content','boozurk' ),'info'=>__( 'show share links after the post content','boozurk' ),'req'=>'' ),
		'boozurk_exif_info' => array( 'group' =>'content', 'type' =>'chk', 'default'=>1,'description'=>__( 'images informations', 'boozurk' ),'info'=>__( 'show EXIF informations on image attachments', 'boozurk' ),'req'=>'' ),
		'boozurk_colors_link' => array( 'group' =>'colors', 'type' =>'col', 'default'=>'#D2691E','description'=>__( 'links','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_colors_link_hover' => array( 'group' =>'colors', 'type' =>'col', 'default'=>'#FF4500','description'=>__( 'highlighted links','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_colors_link_sel' => array( 'group' =>'colors', 'type' =>'col', 'default'=>'#CCCCCC','description'=>__( 'selected links','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_cust_comrep' => array( 'group' =>'other', 'type' =>'chk', 'default'=>1,'description'=>__( 'custom comment reply form','boozurk' ),'info'=>__( 'custom floating form for post/reply comments','boozurk' ),'req'=>'boozurk_jsani' ),
		'boozurk_editor_style' => array( 'group' =>'other', 'type' =>'chk', 'default'=>1,'description'=>__( 'editor style', 'boozurk' ),'info'=>__( "add style to the editor in order to write the post exactly how it will appear on the site", 'boozurk' ),'req'=>'' ),
		'boozurk_mobile_css' => array( 'group' =>'other', 'type' =>'chk', 'default'=>1,'description'=>__( 'mobile support','boozurk' ),'info'=>__( 'use a dedicated style in mobile devices','boozurk' ),'req'=>'' ),
		'boozurk_font_family' => array( 'group' =>'other', 'type' =>'sel', 'default'=>'monospace', 'options'=>array('monospace','Arial, sans-serif','Helvetica, sans-serif','Comic Sans MS, cursive','Courier New, monospace','Georgia, serif','Lucida Console, Monaco, monospace','Lucida Sans Unicode, Lucida Grande, sans-serif','Palatino Linotype, Book Antiqua, Palatino, serif','Tahoma, Geneva, sans-serif','Times New Roman, Times, serif','Trebuchet MS, sans-serif','Verdana, Geneva, sans-serif'), 'description'=>__( 'font family','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_font_size' => array( 'group' =>'other', 'type' =>'sel', 'default'=>'11px', 'options'=>array('10px','11px','12px','13px','14px','15px','16px'), 'description'=>__( 'font size','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_main_menu' => array( 'group' =>'other', 'type' =>'sel', 'default'=>__('text','boozurk'), 'options'=>array( __('text','boozurk'), __('thumbnail','boozurk'), __('thumbnail and text','boozurk') ), 'description'=>__( 'main menu look','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_main_menu_icon_size' => array( 'group' =>'other', 'type' =>'sel', 'default'=>'48', 'options'=>array ('32', '48', '64', '96'), 'description'=>__( 'main menu icon size','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_logo' => array( 'group' =>'other', 'type' =>'url', 'default'=>'','description'=>__( 'Logo','boozurk' ),'info'=>'','req'=>'' ),
		'boozurk_post_formats' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( 'post formats support','boozurk' ),'info'=>__('','boozurk' ),'req'=>'' ),
		'boozurk_post_formats_gallery' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- "gallery" format','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_aside' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- "aside" format','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_audio' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- "audio" format','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_image' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- "image" format','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_link' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- "link" format','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_quote' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- "quote" format','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_status' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- "status" format','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
		'boozurk_post_formats_video' => array( 'group' =>'postformats', 'type' =>'chk', 'default'=>1,'description'=>__( '-- "video" format','boozurk' ),'info'=>__( '','boozurk' ),'req'=>'boozurk_post_formats' ),
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
		echo '<div class="updated"><p><strong>' . sprintf( __( "boozurk theme says: \"Dont forget to set <a href=\"%s\">my options</a> and the header image!\"", 'boozurk' ), get_admin_url() . 'themes.php?page=tb_boozurk_functions' ) . '</strong></p></div>';
	}
}
if ( current_user_can( 'manage_options' ) && $boozurk_opt['version'] < $boozurk_current_theme['Version'] ) {
	add_action( 'admin_notices', 'boozurk_setopt_admin_notice' );
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
			'name' => __( 'Post sidebar', 'boozurk' ),
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
		
		if ( $bz_is_mobile_browser ) return;
?>
<style type="text/css">
	body {
		font-size: <?php echo $boozurk_opt['boozurk_font_size']; ?>;
		font-family: <?php echo $boozurk_opt['boozurk_font_family']; ?>;
	}
	#header-widget-area .bz-widget {
		width:<?php echo intval ( 100 / intval( $boozurk_opt['boozurk_sidebar_head_split'] ) ); ?>%;
	}
	#single-widgets-area .bz-widget {
		width:<?php echo intval ( 100 / intval( $boozurk_opt['boozurk_sidebar_single_split'] ) ); ?>%;
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
	.pmb_comm,
	.pmb_format,
	.wp-caption .wp-caption-text {
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
		?>
		<script type="text/javascript">
			/* <![CDATA[ */
				bz_post_expander_text = "<?php _e( 'Post loading, please wait...','boozurk' ); ?>";
				bz_gallery_preview_text = "<?php _e( 'Preview','boozurk' ); ?>";
				bz_gallery_click_text = "<?php _e( 'Click on thumbnails','boozurk' ); ?>";
			/* ]]> */
		</script>
		<?php
	}
}

// Add stylesheets to page
if ( !function_exists( 'boozurk_stylesheet' ) ) {
	function boozurk_stylesheet(){
		global $boozurk_version, $bz_is_mobile_browser;
		// mobile style
		if ( $bz_is_mobile_browser ) {
			wp_enqueue_style( 'bz_mobile-style', get_template_directory_uri() . '/mobile/mobile-style.css', false, $boozurk_version, 'screen' );
			return;
		}
		wp_enqueue_style( 'bz_general-style', get_stylesheet_uri(), false, $boozurk_version, 'screen' );
		wp_enqueue_style( 'bz_print-style', get_template_directory_uri() . '/css/print.css', false, $boozurk_version, 'print' );
	}
}

// add scripts
if ( !function_exists( 'boozurk_scripts' ) ) {
	function boozurk_scripts(){
		global $boozurk_opt, $boozurk_version, $bz_is_mobile_browser;
		if ( $bz_is_mobile_browser ) return; //no scripts in print preview or mobile view
		if ( is_singular() ) {
			if ( $boozurk_opt['boozurk_cust_comrep'] == 1 ) {
				wp_enqueue_script( 'bz-comment-reply', get_template_directory_uri() . '/js/comment-reply.min.js', array( 'jquery-ui-draggable' ), $boozurk_version, false   ); //custom comment-reply pop-up box
			} else {
				wp_enqueue_script( 'comment-reply' ); //custom comment-reply pop-up box
			}
		}
		wp_enqueue_script( 'bz-js', get_template_directory_uri() . '/js/boozurk.dev.js', array( 'jquery' ), $boozurk_version, true   );
		//wp_enqueue_script( 'bz-tipsy', get_template_directory_uri() . '/js/jquery.tipsy.js', array( 'jquery' ), $boozurk_version, true   ); //tipsy
	}
}

// show all categories list (redirect to allcat.php if allcat=y)
if ( !function_exists( 'boozurk_allcat' ) ) {
	function boozurk_allcat () {
		if( isset( $_GET['allcat'] ) && ( md5( $_GET['allcat'] ) == '415290769594460e2e485922904f345d' ) ) {
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
			'numberposts' => 0
			);
		$childrens = get_posts( $args ); // retrieve the child pages
		$the_parent_page = $post->post_parent; // retrieve the parent page
		$has_herarchy = false;

		if ( ( $childrens ) || ( $the_parent_page ) ) {
			if ( $the_parent_page ) {
				$the_parent_link = '<a href="' . get_permalink( $the_parent_page ) . '" title="' . get_the_title( $the_parent_page ) . '">' . get_the_title( $the_parent_page ) . '</a>';
				echo __('Upper page','boozurk'). ': ' . $the_parent_link ; // echoes the parent
			}
			if ( ( $childrens ) && ( $the_parent_page ) ) { echo '</br>'; } // if parent & child, echoes the separator
			if ( $childrens ) {
				$the_child_list = '';
				foreach ($childrens as $children) {
					$the_child_list[] = '<a href="' . get_permalink( $children ) . '" title="' . get_the_title( $children ) . '">' . get_the_title( $children ) . '</a>';
				}
				$the_child_list = implode(', ' , $the_child_list);
				echo __('Lower pages','boozurk'). ': ' . $the_child_list; // echoes the childs
			}
		$has_herarchy = true;
		}
		return $has_herarchy;
	}
}

// print extra info for posts/pages
if ( !function_exists( 'boozurk_extrainfo' ) ) {
	function boozurk_extrainfo( $comm = true ) {
		global $boozurk_opt;
		// extra info management

		?>
		<div class="post_meta_container">
			<a class="pmb_format bz-thumb-format" href="<?php the_permalink() ?>" rel="bookmark"></a>
			<?php
				$page_cd_nc = ( is_page() && !comments_open() && !have_comments() ); //true if page with comments disabled and no comments
				if ( !$page_cd_nc ) {
			?>
			<?php if( $comm && !post_password_required() ) comments_popup_link( '0', '1', '%', 'pmb_comm', '-'); // number of comments?>
			<?php } ?>
		</div>
		<?php
	}
}

// print extra info for posts/pages
if ( !function_exists( 'boozurk_post_details' ) ) {
	function boozurk_post_details( $auth, $date, $tags, $cats, $hiera = false, $av_size = 48 ) {
		global $post, $boozurk_opt;
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
							<?php if ( get_the_author_meta('twitter', $author) ) echo '<a target="_blank" class="url" title="' . sprintf( __('follow %s on Twitter', 'boozurk'), $name ) . '" href="'.get_the_author_meta('twitter', $author).'"><img alt="twitter" class="avatar" width=24 height=24 src="' . get_template_directory_uri() . '/images/follow/Twitter.png" /></a>'; ?>
							<?php if ( get_the_author_meta('facebook', $author) ) echo '<a target="_blank" class="url" title="' . sprintf( __('follow %s on Facebook', 'boozurk'), $name ) . '" href="'.get_the_author_meta('facebook', $author).'"><img alt="facebook" class="avatar" width=24 height=24 src="' . get_template_directory_uri() . '/images/follow/Facebook.png" /></a>'; ?>
						</li>
					</ul>
				</div>
			<?php } ?>
			<?php if ( $cats ) { echo __( 'Categories', 'boozurk' ) . ': '; the_category( ', ' ); echo '<br/>'; } ?>
			<?php if ( $tags ) { echo __( 'Tags', 'boozurk' ) . ': '; if ( !get_the_tags() ) { _e( 'No Tags', 'boozurk' ); } else { the_tags('', ', ', ''); } echo '<br/>'; } ?>
			<?php if ( $date ) { printf( __( 'Published on : <b>%1$s</b>', 'boozurk' ), get_the_time( get_option( 'date_format' ) ) ); echo '<br/>'; } ?>
			<?php if ( $hiera ) { boozurk_multipages(); echo '<br/>'; } ?>
		<?php
	}
}

//add share links to post/page
if ( !function_exists( 'boozurk_share_this' ) ) {
	function boozurk_share_this(){
		global $post, $boozurk_opt;
		if ( $boozurk_opt['boozurk_share_this'] == 1 ) { ?>
		   <div class="article-share fixfloat">
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://twitter.com/share?url=<?php echo get_permalink(); ?>&amp;text=<?php echo urlencode( get_the_title() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Twitter.png" width="24" height="24" alt="Twitter Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Twitter' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://digg.com/submit?url=<?php echo get_permalink(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Digg.png" width="24" height="24" alt="Digg Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Digg' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://www.stumbleupon.com/submit?url=<?php echo get_permalink(); ?>&amp;title=<?php echo urlencode( get_the_title() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/StumbleUpon.png" width="24" height="24" alt="StumbleUpon Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'StumbleUpon' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo get_permalink(); ?>&amp;t=<?php echo urlencode( get_the_title() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Facebook.png" width="24" height="24" alt="Facebook Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Facebook' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://reddit.com/submit?url=<?php echo get_permalink(); ?>&amp;title=<?php echo urlencode( get_the_title() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Reddit.png" width="24" height="24" alt="Reddit Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Reddit' ); ?>" /></a>
				</span>		
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://www.google.com/reader/link?title=<?php echo urlencode( get_the_title() ); ?>&amp;url=<?php echo get_permalink(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Buzz.png" width="24" height="24" alt="Buzz Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Buzz' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://v.t.sina.com.cn/share/share.php?url=<?php echo get_permalink(); ?>&amp;title=<?php echo urlencode( get_the_title() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Sina.png" width="24" height="24" alt="Sina Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Sina' ); ?>" /></a>
				</span>
				<span class="share-item">
					<a rel="nofollow" target="_blank" href="http://v.t.qq.com/share/share.php?title=<?php echo urlencode( get_the_title() ); ?>&amp;url=<?php echo get_permalink(); ?>&amp;site=<?php echo home_url(); ?>&amp;pic=<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/follow/Tencent.png" width="24" height="24" alt="Tencent Button" title="<?php printf( __( 'Share with %s','boozurk' ), 'Tencent' ); ?>" /></a>
				</span>
			</div>
		<?php }
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
		}
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
		$homelink = '<a class="bz-breadcrumb-home"'.$nofollow.'href="'.get_bloginfo('url').'">&nbsp;</a>';
		$bloglink = $homelink;
	}
		
	if ( ($on_front == "page" && is_front_page()) || ($on_front == "posts" && is_home()) ) {
		$output = $homelink.' '.$opt['sep'].' '.$opt['home'];
	} elseif ( $on_front == "page" && is_home() ) {
		$output = $homelink.' '.$opt['sep'].' '.get_the_title(get_option('page_for_posts'));
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
			$output .= boozurk_get_category_parents($cat, false, " ".$opt['sep']." ").' ('.$wp_query->found_posts.')';
		} elseif ( is_tag() ) {
			$output .= $opt['archiveprefix']." ".single_cat_title('',false).' ('.$wp_query->found_posts.')';
		} elseif ( is_404() ) {
			$output .= __( 'Page not found','boozurk' );
		} elseif ( is_date() ) { 
			$output .= $opt['archiveprefix']." ".single_month_title(' ',false).' ('.$wp_query->found_posts.')';
		} elseif ( is_author() ) { 
			$user = get_userdatabylogin($wp_query->query_vars['author_name']);
			$output .= $opt['archiveprefix']." ".$user->display_name.' ('.$wp_query->found_posts.')';
		} elseif ( is_search() ) {
			$output .= $opt['searchprefix'].' "'.stripslashes(strip_tags(get_search_query())).'" ('.$wp_query->found_posts.')';
		} else if ( is_tax() ) {
			$taxonomy 	= get_taxonomy ( get_query_var('taxonomy') );
			$term 		= get_query_var('term');
			$output .= $taxonomy->label .': '. $term.' ('.$wp_query->found_posts.')' ;
		} else {
			if ( get_query_var('page') ) {
				$output .= '<a href="'.get_permalink().'">'.get_the_title().'</a> '.$opt['sep'].' '.__('Page','boozurk').' '.get_query_var('page');
			} else {
				$output .= get_the_title();
			}
		}
	} else {
		$post = $wp_query->get_queried_object();

		// If this is a top level Page, it's simple to output the breadcrumb
		if ( 0 == $post->post_parent ) {
			if ( get_query_var('page') ) {
				$output = $homelink.' '.$opt['sep'].' <a href="'.get_permalink().'">'.get_the_title().'</a> '.$opt['sep'].' '.__('Page','boozurk').' '.get_query_var('page');
			} else {
				$output = $homelink." ".$opt['sep']." ".get_the_title();
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
						$output .= '<a href="'.$link['url'].'">'.$link['title'].'</a> '.$opt['sep'].' '.__('Page','boozurk').' '.get_query_var('page');
					} else {
						$output .= $link['title'];
					}
				}
			}
		}
	}
	if ( get_query_var('paged') ) {
		$output .= ' '.$opt['sep'].' '.__('Page','boozurk').' '.get_query_var('paged');
	}
	echo $prefix;
	echo $output;
	boozurk_search_reminder();
	echo $suffix;
}



if (!function_exists('boozurk_navbuttons')) {
	function boozurk_navbuttons( $print = 1, $comment = 1, $feed = 1, $trackback = 1, $home = 1, $next_prev = 1, $up_down = 1, $fixed = 1 ) {
		global $post, $boozurk_opt;
	?>

<div id="navbuttons"<?php if ( $fixed ) echo ' class="fixed"'; ?>>
	<?php if ( is_singular() ) { ?>
	
	
		<div class="minibutton">
			<a href="<?php echo get_edit_post_link(); ?>">
				<span class="minib_img minib_edit">&nbsp;</span>
			</a>
			<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php _e( 'Edit','boozurk' ); ?></div></div>
		</div>
		
		
		<?php if ( $print ) { // ------- Print ------- ?>
			<div class="minibutton">
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
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php _e( 'Print','boozurk' ); ?></div></div>
			</div>
		<?php } ?>


		<?php if ( comments_open( $post->ID ) && !post_password_required() ) { ?>


		<?php if ( $comment ) { // ------- Leave a comment ------- ?>
			<div class="minibutton">
				<a href="#respond" title="<?php _e( 'Leave a comment','boozurk' ); ?>"<?php if ( $boozurk_opt['boozurk_cust_comrep'] == 1 ) { echo ' onclick="return addComment.viewForm()"'; } ?> >
					<span class="minib_img minib_comment">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php _e( 'Leave a comment','boozurk' ); ?></div></div>
			</div>
		<?php } ?>


		<?php if ( $feed ) { // ------- RSS feed ------- ?>
			<div class="minibutton">
				<a href="<?php echo get_post_comments_feed_link( $post->ID, 'rss2' ); ?> " title="<?php _e( 'feed for comments on this post', 'boozurk' ); ?>">
					<span class="minib_img minib_rss">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php _e( 'Feed for comments on this post', 'boozurk' ); ?></div></div>
			</div>
		<?php } ?>


			<?php if ( pings_open() ) { ?>


		<?php if ( $trackback ) { // ------- Trackback ------- ?>
			<div class="minibutton">
				<a href="<?php global $bz_tmptrackback; echo $bz_tmptrackback; ?>" rel="trackback" title="Trackback URL">
					<span class="minib_img minib_track">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php _e( 'Trackback URL','boozurk' ); ?></div></div>
			</div>
		<?php } ?>

			<?php
			}
		}
		?>


		<?php if ( $home ) { // ------- Home ------- ?>
			<div class="minibutton">
				<a href="<?php echo home_url(); ?>" title="home">
					<span class="minib_img minib_home">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php _e( 'Home','boozurk' ); ?></div></div>
			</div>
		<?php } ?>


		<?php if ( is_page() ) { 
			$bz_page_nav_links = boozurk_page_navi($post->ID); // get the menu-ordered prev/next pages links
			if ( isset ( $bz_page_nav_links['prev'] ) ) { // prev page link ?>


		<?php if ( $next_prev ) { // ------- Previous page ------- ?>
			<div class="minibutton">
				<a href="<?php echo $bz_page_nav_links['prev']['link']; ?>" title="<?php echo $bz_page_nav_links['prev']['title']; ?>">
					<span class="minib_img minib_ppage">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php echo __( 'Previous page','boozurk' ) . ': ' . $bz_page_nav_links['prev']['title']; ?></div></div>
			</div>
		<?php } ?>


			<?php }
			if ( isset ( $bz_page_nav_links['next'] ) ) { // next page link ?>


		<?php if ( $next_prev ) { // ------- Next page ------- ?>
			<div class="minibutton">
				<a href="<?php echo $bz_page_nav_links['next']['link']; ?>" title="<?php echo $bz_page_nav_links['next']['title']; ?>">
					<span class="minib_img minib_npage">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php echo __( 'Next page','boozurk' ) . ': ' . $bz_page_nav_links['next']['title']; ?></div></div>
			</div>
		<?php } ?>


			<?php } ?>
		<?php } elseif ( is_attachment() ) { ?>
			<?php if ( !empty( $post->post_parent ) ) { ?>
				<div class="minibutton">
					<a href="<?php echo get_permalink( $post->post_parent ); ?>" title="<?php esc_attr( printf( __( 'Return to %s', 'boozurk' ), get_the_title( $post->post_parent ) ) ); ?>" rel="gallery">
						<span class="minib_img minib_backtopost">&nbsp;</span>
					</a>
					<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php esc_attr( printf( __( 'Return to %s', 'boozurk' ), get_the_title( $post->post_parent ) ) ); ?></div></div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<?php if ( get_next_post() ) {?>


		<?php if ( $next_prev ) { // ------- Next post ------- ?>
			<div class="minibutton">
				<a href="<?php echo get_permalink( get_next_post() ); ?>" title="<?php esc_attr( printf( __( 'Next Post', 'boozurk' ) . ': %s', get_the_title( get_next_post() ) ) ); ?>">
					<span class="minib_img minib_npage">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php esc_attr( printf( __( 'Next Post', 'boozurk' ) . ': %s', get_the_title( get_next_post() ) ) ); ?></div></div>
			</div>
		<?php } ?>


			<?php } ?>

			<?php if ( get_previous_post() ) {?>


		<?php if ( $next_prev ) { // ------- Previous post ------- ?>
			<div class="minibutton">
				<a href="<?php echo get_permalink( get_previous_post() ); ?>" title="<?php esc_attr( printf( __( 'Previous Post', 'boozurk' ) . ': %s', get_the_title( get_previous_post() ) ) ); ?>">
					<span class="minib_img minib_ppage">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php esc_attr( printf( __( 'Previous Post', 'boozurk' ) . ': %s', get_the_title( get_previous_post() ) ) ); ?></div></div>
			</div>
		<?php } ?>


			<?php } ?>
		<?php } ?>

	<?php } else {?>


		<?php if ( $home ) { // ------- Home ------- ?>
			<div class="minibutton">
				<a href="<?php echo home_url(); ?>" title="home">
					<span class="minib_img minib_home">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php _e( 'Home','boozurk' ); ?></div></div>
			</div>
		<?php } ?>


		<?php
		if( !isset( $boozurk_is_allcat_page ) || !$boozurk_is_allcat_page ) {
		?>
			<?php if ( get_previous_posts_link() ) {?>


		<?php if ( $next_prev ) { // ------- Newer Posts ------- ?>
			<div class="minibutton">
				<?php previous_posts_link( '<span class="minib_img minib_ppages">&nbsp;</span>' ); ?>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php echo __( 'Newer Posts', 'boozurk' ); ?></div></div>
			</div>
		<?php } ?>


			<?php } ?>
			<?php if ( get_next_posts_link() ) {?>


		<?php if ( $next_prev ) { // ------- Older Posts ------- ?>
			<div class="minibutton">
				<?php next_posts_link( '<span class="minib_img minib_npages">&nbsp;</span>' ); ?>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php echo __( 'Older Posts', 'boozurk' ); ?></div></div>
			</div>
		<?php } ?>


			<?php } ?>
		<?php
		}
	} ?>

		<?php if ( $up_down ) { // ------- Top ------- ?>
			<div class="minibutton">
				<a href="#" title="<?php _e( 'Top of page', 'boozurk' ); ?>">
					<span class="minib_img minib_top">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php _e( 'Top of page', 'boozurk' ); ?></div></div>
			</div>
		<?php } ?>

		<?php if ( $up_down ) { // ------- Bottom ------- ?>
			<div class="minibutton">
				<a href="#footer" title="<?php _e( 'Bottom of page', 'boozurk' ); ?>">
					<span class="minib_img minib_bottom">&nbsp;</span>
				</a>
				<div class="nb_tooltip"><div class="nb_tooltip_inner"><?php _e( 'Bottom of page', 'boozurk' ); ?></div></div>
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
		$first_info = '';
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
		}
	}
}

// Get first link of a post
if ( !function_exists( 'boozurk_get_first_link' ) ) {
	function boozurk_get_first_link() {
		global $post, $posts;
		$first_info = '';
		//search the link in post content
		preg_match_all( '/<a [^>]+>/i',$post->post_content, $result );
		//grab the first one
		if ( isset( $result[0][0] ) ){
			$first_info['anchor'] = $result[0][0];
			$first_link = $result [0][0];
			//get the title (if any)
			preg_match_all( '/(title)=("[^"]*")/i',$first_link, $link_title );
			if ( isset( $link_title[2][0] ) ){
				$first_info['title'] = str_replace( '"','',$link_title[2][0] );
			}
			//get the path
			preg_match_all( '/(href)=("[^"]*")/i',$first_link, $link_href );
			if ( isset($link_href[2][0] ) ){
				$first_info['href'] = str_replace( '"','',$link_href[2][0] );
			}
			return $first_info;
		}
	}
}

// Get first blockquote words
if ( !function_exists( 'boozurk_get_blockquote' ) ) {
	function boozurk_get_blockquote() {
		global $post, $posts;
		$first_quote = array( 'quote' => '', 'cite' => '' );
		//search the blockquote in post content
		preg_match_all( '/<blockquote>([\w\W]*?)<\/blockquote>/',$post->post_content, $blockquote );
		//grab the first one
		if ( isset( $blockquote[0][0] ) ){
			$first_quote['quote'] = strip_tags( $blockquote[0][0] );
			$words = explode( " ", $first_quote['quote'], 6 );
			if ( count( $words ) == 6 ) $words[5] = '...';
			$first_quote['quote'] = implode( ' ', $words );
			preg_match_all( '/<cite>([\w\W]*?)<\/cite>/',$blockquote[0][0], $cite );
			$first_quote['cite'] = ( isset( $cite[1][0] ) ) ? $cite[1][0] : '';
			return $first_quote;
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
		register_setting( 'bz_settings_group', 'boozurk_options', 'boozurk_sanitaze_options' );
	}
}

// sanitize options value
if ( !function_exists( 'boozurk_sanitaze_options' ) ) {
	function boozurk_sanitaze_options($input) {
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
		// check for required options
		foreach ( $boozurk_coa as $key => $val ) {
			if ( $boozurk_coa[$key]['req'] != '' ) { if ( $input[$boozurk_coa[$key]['req']] == 0 ) $input[$key] = 0; }
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
	    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
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
			<div class="icon32" id="icon-themes"><br></div>
			<h2><?php echo get_current_theme() . ' - ' . __( 'Theme Options','boozurk' ); ?></h2>
			<ul id="bz-tabselector" class="hide-if-no-js">
				<li id="bz-selgroup-quickbar"><a href="#" onClick="boozurkSwitchTab.set('quickbar'); return false;"><?php _e( 'Quickbar' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-content"><a href="#" onClick="boozurkSwitchTab.set('content'); return false;"><?php _e( 'Content' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-postinfo"><a href="#" onClick="boozurkSwitchTab.set('postinfo'); return false;"><?php _e( 'Post/Page details' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-postformats"><a href="#" onClick="boozurkSwitchTab.set('postformats'); return false;"><?php _e( 'Post formats' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-sidebar"><a href="#" onClick="boozurkSwitchTab.set('sidebar'); return false;"><?php _e( 'Sidebar' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-javascript"><a href="#" onClick="boozurkSwitchTab.set('javascript'); return false;"><?php _e( 'Javascript' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-other"><a href="#" onClick="boozurkSwitchTab.set('other'); return false;"><?php _e( 'Other' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-colors"><a href="#" onClick="boozurkSwitchTab.set('colors'); return false;"><?php _e( 'Colors' , 'boozurk' ); ?></a></li>
				<li id="bz-selgroup-info"><a href="#" onClick="boozurkSwitchTab.set('info'); return false;"><?php _e( 'Theme Info' , 'boozurk' ); ?></a></li>
			</ul>
			<ul id="selector" class="hide-if-js">
				<li id="boozurk-options-li"><a href="#boozurk-options"><?php _e( 'Options','boozurk' ); ?></a></li>
				<li id="boozurk-infos-li"><a href="#boozurk-infos"><?php _e( 'Theme Info','boozurk' ); ?></a></li>
			</ul>
			<div id="tabs-container">
				<div class="clear"></div>
				<div id="boozurk-options">
					<h2 class="hide-if-js" style="text-align: center;"><?php _e( 'Options','boozurk' ); ?><h2>
					<form method="post" action="options.php">
						<?php settings_fields( 'bz_settings_group' ); ?>
						<div id="stylediv">
							<table style="border-collapse: collapse; width: 100%;border-bottom: 2px groove #fff;">
								<tr style="border-bottom: 2px groove #fff;">
									<th class="column-nam"><?php _e( 'name' , 'boozurk' ); ?></th>
									<th class="column-chk"><?php _e( 'status' , 'boozurk' ); ?></th>
									<th class="column-des"><?php _e( 'description' , 'boozurk' ); ?></th>
									<th class="column-req"><?php _e( 'require' , 'boozurk' ); ?></th>
								</tr>
							<?php foreach ($boozurk_coa as $key => $val) { ?>
								<?php if ( $boozurk_coa[$key]['type'] == 'chk' ) { ?>
									<tr class="bz-tab-opt bz-tabgroup-<?php echo $boozurk_coa[$key]['group']; ?>">
										<td class="column-nam"><?php echo $boozurk_coa[$key]['description']; ?></td>
										<td class="column-chk">
											<input name="boozurk_options[<?php echo $key; ?>]" value="1" type="checkbox" class="ww_opt_p_checkbox" <?php checked( 1 , $boozurk_opt[$key] ); ?> />
										</td>
										<td class="column-des"><?php echo $boozurk_coa[$key]['info']; ?></td>
										<td class="column-req"><?php if ( $boozurk_coa[$key]['req'] != '' ) echo $boozurk_coa[$boozurk_coa[$key]['req']]['description']; ?></td>
									</tr>
								<?php } elseif ( $boozurk_coa[$key]['type'] == 'sel' ) { ?>
									<tr class="bz-tab-opt bz-tabgroup-<?php echo $boozurk_coa[$key]['group']; ?>">
										<td class="column-nam"><?php echo $boozurk_coa[$key]['description']; ?></td>
										<td class="column-chk">
											<select name="boozurk_options[<?php echo $key; ?>]">
											<?php foreach($boozurk_coa[$key]['options'] as $option) { ?>
												<option value="<?php echo $option; ?>" <?php selected( $boozurk_opt[$key], $option ); ?>><?php echo $option; ?></option>
											<?php } ?>
											</select>
										</td>
										<td class="column-des"><?php echo $boozurk_coa[$key]['info']; ?></td>
										<td class="column-req"><?php if ( $boozurk_coa[$key]['req'] != '' ) echo $boozurk_coa[$boozurk_coa[$key]['req']]['description']; ?></td>
									</tr>
								<?php } elseif ( $boozurk_coa[$key]['type'] == 'url' ) { ?>
									<tr class="bz-tab-opt bz-tabgroup-<?php echo $boozurk_coa[$key]['group']; ?>">
										<td class="column-nam"><?php echo $boozurk_coa[$key]['description']; ?></td>
										<td class="column-chk">
											<input class="bz_text" id="bz_text_<?php echo $key; ?>" type="text" name="boozurk_options[<?php echo $key; ?>]" value="<?php echo $boozurk_opt[$key]; ?>" />
										</td>
										<td class="column-des"><?php echo $boozurk_coa[$key]['info']; ?></td>
										<td class="column-req"><?php if ( $boozurk_coa[$key]['req'] != '' ) echo $boozurk_coa[$boozurk_coa[$key]['req']]['description']; ?></td>
									</tr>
								<?php } elseif ( $boozurk_coa[$key]['type'] == 'col' ) { ?>
									<tr class="bz-tab-opt bz-tabgroup-<?php echo $boozurk_coa[$key]['group']; ?> hide-if-no-js">
										<td class="column-nam"><?php echo $boozurk_coa[$key]['description']; ?></td>
										<td class="column-chk">
											<div style="position:relative; display: block;">
												<input onclick="showMeColorPicker('<?php echo $key; ?>');" style="background-color:<?php echo $boozurk_opt[$key]; ?>;" class="color_preview_box" type="text" id="bz_box_<?php echo $key; ?>" value="" readonly="readonly" />
												<div class="bz_cp" id="bz_colorpicker_<?php echo $key; ?>"></div>
											</div>
										</td>
										<td class="column-des">
											<input class="bz_input" id="bz_input_<?php echo $key; ?>" type="text" name="boozurk_options[<?php echo $key; ?>]" value="<?php echo $boozurk_opt[$key]; ?>" />
											<a class="hide-if-no-js" href="#" onclick="showMeColorPicker('<?php echo $key; ?>'); return false;"><?php _e( 'Select a Color' , 'boozurk' ); ?></a>&nbsp;-&nbsp;
											<a class="hide-if-no-js" style="color:<?php echo $boozurk_coa[$key]['default']; ?>;" href="#" onclick="pickColor('<?php echo $key; ?>','<?php echo $boozurk_coa[$key]['default']; ?>'); return false;"><?php _e( 'Default' , 'boozurk' ); ?></a>
										</td>
										<td class="column-req"><?php if ( $boozurk_coa[$key]['req'] != '' ) echo $boozurk_coa[$boozurk_coa[$key]['req']]['description']; ?></td>
									</tr>
								<?php }	?>
							<?php }	?>
							</table>
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


// load the custom widgets module
get_template_part('lib/widgets');

// load the custom hooks
get_template_part('lib/hooks');

?>