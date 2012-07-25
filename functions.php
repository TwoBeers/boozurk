<?php
add_action( 'after_setup_theme', 'boozurk_setup' ); // tell WordPress to run boozurk_setup() when the 'after_setup_theme' hook is run.
add_action( 'widgets_init', 'boozurk_widget_area_init' ); // Register sidebars by running boozurk_widget_area_init() on the widgets_init hook
add_action( 'wp_enqueue_scripts', 'boozurk_stylesheet' ); // Add stylesheets
add_action( 'wp_head', 'boozurk_custom_style' ); // Add custom style
add_action( 'wp_head', 'boozurk_localize_scripts' ); // localize js scripts
add_action( 'wp_enqueue_scripts', 'boozurk_scripts' ); // Add js scripts
add_action( 'wp_footer', 'boozurk_initialize_scripts' ); // start js scripts
add_action( 'admin_menu', 'boozurk_create_menu' ); // Add admin menus
add_action( 'init', 'boozurk_post_expander_activate' ); // post expander ajax request
add_action( 'init', 'boozurk_infinite_scroll_activate' ); // infinite scroll ajax request
add_action( 'boozurk_hook_before_post', 'boozurk_print_date' ); // boozurk_print_date
add_action( 'admin_bar_menu', 'boozurk_admin_bar_plus', 999 ); // add links to admin bar
add_action( 'wp_head', 'boozurk_plus_snippet' ); // localize js scripts

// Custom filters
add_filter( 'the_content', 'boozurk_content_replace', 100 );
add_filter( 'img_caption_shortcode', 'boozurk_img_caption_shortcode', 10, 3 );
add_filter( 'use_default_gallery_style', '__return_false' );

$boozurk_opt = get_option( 'boozurk_options' );

$boozurk_is_mobile_browser = false;

// load modules (accordingly to http://justintadlock.com/archives/2010/11/17/how-to-load-files-within-wordpress-themes)
require_once( 'lib/the_bird.php' ); // load "the bird" core functions
require_once( 'mobile/core-mobile.php' ); // load mobile functions
require_once('lib/hooks.php'); // load the custom hooks module
if ( $boozurk_opt['boozurk_js_swfplayer'] == 1 ) require_once( 'lib/audio-player.php' ); // load the audio player module
if ( isset( $boozurk_opt['boozurk_custom_widgets'] ) && $boozurk_opt['boozurk_custom_widgets'] == 1 ) require_once('lib/widgets.php'); // load the custom widgets module
if ( isset( $boozurk_opt['boozurk_comment_style'] ) && $boozurk_opt['boozurk_comment_style'] == 1 ) require_once('lib/custom_comments.php'); // load the comment style module

// Set the content width based on the theme's design
if ( ! isset( $content_width ) ) {
	$content_width = 560;
}

//complete options array, with type, defaults values, description, infos and required option
function boozurk_get_coa( $option = false ) {

	$boozurk_groups = array(
							'colors' => __( 'Colors' , 'boozurk' ),
							'index' => __( 'Posts archives' , 'boozurk' ),
							'content' => __( 'Contents' , 'boozurk' ),
							'widgets' => __( 'Sidebars and Widgets' , 'boozurk' ),
							'javascript' => __( 'Javascript' , 'boozurk' ),
							'mobile' => __( 'Mobile' , 'boozurk' ),
							'other' => __( 'Other' , 'boozurk' )
	);

	$boozurk_coa = array(
		'boozurk_jsani'=>	array( 
								'group'=>'javascript',
								'type'=>'chk',
								'default'=>1,
								'description'=>__( 'javascript animations','boozurk' ),
								'info'=>__( 'try disable animations if you encountered problems with javascript','boozurk' ),
								'req'=>'' 
							),
		'boozurk_js_thickbox'=>	array(
									'group'=>'javascript',
									'type'=>'chk',
									'default'=>1,
									'description'=>__( 'thickbox preview','boozurk' ),
									'info'=>__( 'add the thickbox effect to each linked image and galleries in post content','boozurk' ),
									'req'=>'boozurk_jsani',
									'sub'=>array('boozurk_js_thickbox_force') 
								),
		'boozurk_js_thickbox_force'=>array( 
											'group'=>'javascript',
											'type'=>'chk',
											'default'=>1,
											'description'=>__( 'replace links','boozurk' ),
											'info'=>__( 'force galleries to use links to image instead of links to attachment','boozurk' ),
											'req'=>'',
											'sub'=>false 
										),
		'boozurk_js_post_expander'=>array( 
										'group'=>'javascript',
										'type'=>'chk',
										'default'=>1,
										'description'=>__( 'post expander','boozurk' ),
										'info'=>__( 'expands a post to show the full content when the reader clicks the "Read more..." link','boozurk' ),
										'req'=>'boozurk_jsani'
									),
		'boozurk_js_tooltips'=>	array(
									'group'=>'javascript',
									'type'=>'chk',
									'default'=>1,
									'description'=>__( 'cool tooltips','boozurk' ),
									'info'=>__( 'replace titles of some links with cool tooltips','boozurk' ),
									'req'=>'boozurk_jsani' 
								),
		'boozurk_js_swfplayer'=>array( 
									'group'=>'javascript',
									'type'=>'chk',
									'default'=>1,
									'description'=>__( 'swf audio player','boozurk' ),
									'info'=>__( 'create an audio player for linked audio files (mp3,ogg and m4a) in the audio format posts','boozurk' ),
									'req'=>'boozurk_jsani' 
								),
		'boozurk_quotethis'=>	array( 
									'group'=>'javascript',
									'type'=>'chk',
									'default'=>1,
									'description'=>__( 'quote link', 'boozurk' ),
									'info'=>__( 'show a link for easily add the selected text as a quote inside the comment form', 'boozurk' ),
									'req'=>'' 
								),
		'boozurk_infinite_scroll'=>	array( 
										'group'=>'javascript',
										'type'=>'chk',
										'default'=>0,
										'description'=>__( 'infinite pagination','boozurk' ),
										'info'=>__( 'automatically append the next page of posts (via AJAX) to your current page','boozurk' ),
										'req'=>'boozurk_jsani',
										'sub'=>array('boozurk_infinite_scroll_type') 
									),
		'boozurk_infinite_scroll_type'=>array( 
											'group'=>'javascript',
											'type'=>'sel',
											'default'=>'manual',
											'description'=>__( 'behaviour','boozurk' ),
											'info'=>__( 'auto: when a user scrolls to the bottom - manual: by clicking the link at the end of posts','boozurk' ),
											'options'=>array('auto','manual'),
											'options_l10n'=>array(__('auto','boozurk'),__('manual','boozurk')),
											'req'=>'',
											'sub'=>false 
										),
		'boozurk_sidebar_primary'=>	array( 
										'group'=>'widgets',
										'type'=>'sel',
										'default'=>'scroll',
										'description'=>__( 'primary sidebar','boozurk' ),
										'info'=> '',
										'options'=>array('scroll','fixed'),
										'options_l10n'=>array(__('scroll','boozurk'),__('fixed','boozurk')),
										'req'=>'' 
									),
		'boozurk_sidebar_secondary'=>	array( 
										'group'=>'widgets',
										'type'=>'sel',
										'default'=>'fixed',
										'description'=>__( 'secondary sidebar','boozurk' ),
										'info'=> '',
										'options'=>array('scroll','fixed'),
										'options_l10n'=>array(__('scroll','boozurk'),__('fixed','boozurk')),
										'req'=>'' 
									),
		'boozurk_sidebar_head_split'=>	array( 
											'group'=>'widgets',
											'type'=>'sel',
											'default'=>'3',
											'description'=>__( 'split Header widget area','boozurk' ),
											'info'=>__( 'number of widget that can stay in the widget area side by side','boozurk' ),
											'options'=>array('1','2','3'),
											'options_l10n'=>array('1','2','3'),
											'req'=>'' 
										),
		'boozurk_sidebar_single_split'=>array( 
											'group'=>'widgets',
											'type'=>'sel',
											'default'=>'1',
											'description'=>__( 'split Post widget area','boozurk' ),
											'info'=>__( 'number of widget that can stay in the widget area side by side','boozurk' ),
											'options'=>array('1','2','3'),
											'options_l10n'=>array('1','2','3'),
											'req'=>''
										),
		'boozurk_sidebar_foot_1_width'=>array( 
											'group'=>'widgets',
											'type'=>'sel',
											'default'=>'33%',
											'description'=>__( 'footer widget area #1','boozurk' ),
											'info'=>__( 'width of the widget area','boozurk' ),
											'options'=>array('100%','50%','33%'),
											'options_l10n'=>array('100%','50%','33%'),
											'req'=>'' 
										),
		'boozurk_sidebar_foot_2_width'=>array( 
											'group'=>'widgets',
											'type'=>'sel',
											'default'=>'33%',
											'description'=>__( 'footer widget area #2','boozurk' ),
											'info'=>__( 'width of the widget area','boozurk' ),
											'options'=>array('100%','50%','33%'),
											'options_l10n'=>array('100%','50%','33%'),
											'req'=>'' 
										),
		'boozurk_sidebar_foot_3_width'=>array( 
											'group'=>'widgets',
											'type'=>'sel',
											'default'=>'33%',
											'description'=>__( 'footer widget area #3','boozurk' ),
											'info'=>__( 'width of the widget area','boozurk' ),
											'options'=>array('100%','50%','33%'),
											'options_l10n'=>array('100%','50%','33%'),
											'req'=>'' 
										),
		'boozurk_custom_widgets'=>	array( 
										'group'=>'widgets',
										'type'=>'chk',
										'default'=>1,
										'description'=>__( 'custom widgets','boozurk' ),
										'info'=>__( 'add a lot of new usefull widgets','boozurk' ),
										'req'=>'' 
									),
		'boozurk_colors_link_wrap'=>	array( 
									'group'=>'colors',
									'type'=>'',
									'default'=>'',
									'description'=>__( 'links colors','boozurk' ),
									'info'=>'',
									'req'=>'',
									'sub'=>array('boozurk_colors_link', 'boozurk_colors_link_hover', 'boozurk_colors_link_sel')
								),
		'boozurk_colors_link'=>	array( 
									'group'=>'colors',
									'type'=>'col',
									'default'=>'#21759b',
									'description'=>'',
									'info'=>__( 'links','boozurk' ),
									'req'=>'',
									'sub'=>false
								),
		'boozurk_colors_link_hover'=>	array( 
											'group'=>'colors',
											'type'=>'col',
											'default'=>'#404040',
											'description'=>'',
											'info'=>__( 'highlighted links','boozurk' ),
											'req'=>'',
											'sub'=>false 
										),
		'boozurk_colors_link_sel'=>	array( 
										'group'=>'colors',
										'type'=>'col',
										'default'=>'#87CEEB',
										'description'=>'',
										'info'=>__( 'selected links','boozurk' ),
										'req'=>'',
										'sub'=>false 
									),
		'boozurk_cat_colors_wrap'=>	array(
										'group'=>'colors',
										'type'=>'',
										'default'=>'',
										'description'=>__( 'colors for categories','boozurk' ),
										'info'=>'',
										'req'=>'',
										'sub'=>array('boozurk_cat_colors') 
									),
		'boozurk_blank_title'=>	array( 
										'group'=>'content',
										'type'=>'chk',
										'default'=>1,
										'description'=> __( 'blank titles', 'boozurk' ),
										'info' => __( 'set the standard text for blank titles', 'boozurk' ),
										'req'=>'',
										'sub'=>array('boozurk_blank_title_text') 
									),
		'boozurk_blank_title_text' =>	array(
										'group' => 'content',
										'type' => 'txt',
										'default' => __( '(no title)', 'boozurk' ),
										'description' => __( 'default text', 'boozurk' ),
										'info' => __( '<br />you may use these codes:<br /><code>%d</code> for post date<br /><code>%f</code> for post format (if any)', 'boozurk' ),
										'req' => '',
										'sub'=>false
									),
		'boozurk_excerpt' =>	array(
									'group' => 'content',
									'type' => '',
									'default' => '',
									'description' => __( 'excerpt', 'boozurk' ),
									'info' => '',
									'req' => '',
									'sub'=>array('boozurk_excerpt_length','boozurk_excerpt_more_txt','boozurk_excerpt_more_link') 
								),
		'boozurk_excerpt_length' =>	array(
											'group' => 'content',
											'type' => 'int',
											'default' => 55,
											'description' => __( 'excerpt length', 'boozurk' ),
											'info' => '',
											'req' => '',
											'sub'=>false 
										),
		'boozurk_excerpt_more_txt' =>	array(
											'group' => 'content',
											'type' => 'txt',
											'default' => '[...]',
											'description' => __( '<em>excerpt more</em> string', 'boozurk' ),
											'info' => '',
											'req' => '',
											'sub'=>false 
										),
		'boozurk_excerpt_more_link' =>	array(
											'group' => 'content',
											'type' => 'chk',
											'default' => 0,
											'description' => __( '<em>excerpt more</em> linked', 'boozurk' ),
											'info' => __( 'use the <em>excerpt more</em> string as a link to the full post', 'boozurk' ),
											'req' => '',
											'sub'=>false 
										),
		'boozurk_more_tag' =>	array(
									'group' => 'content',
									'type' => 'txt',
									'default' => __( '(more...)', 'boozurk' ),
									'description' => __( '"more" tag string', 'boozurk' ),
									'info' => __( 'only plain text. use <code>%t</code> as placeholder for the post title', 'boozurk' ) . ' (<a href="http://codex.wordpress.org/Customizing_the_Read_More" target="_blank">Codex</a>)',
									'req' => ''
								),
		'boozurk_cat_colors'=>	array(									'group'=>'colors',									'type'=>'catcol',									'default'=>array(),									'defaultcolor'=>'#87CEEB',
									'description'=>'',									'info'=>'',									'req'=>'',
									'sub'=>false 
								),
		'boozurk_font_family'=>	array( 									'group'=>'other',									'type'=>'sel',									'default'=>'monospace',									'description'=>__( 'font family','boozurk' ),									'info'=>'',									'options'=>array('monospace','Arial, sans-serif','Helvetica, sans-serif','Comic Sans MS, cursive','Courier New, monospace','Georgia, serif','Lucida Console, Monaco, monospace','Lucida Sans Unicode, Lucida Grande, sans-serif','Palatino Linotype, Book Antiqua, Palatino, serif','Tahoma, Geneva, sans-serif','Times New Roman, Times, serif','Trebuchet MS, sans-serif','Verdana, Geneva, sans-serif'),									'options_l10n'=>array('monospace','Arial, sans-serif','Helvetica, sans-serif','Comic Sans MS, cursive','Courier New, monospace','Georgia, serif','Lucida Console, Monaco, monospace','Lucida Sans Unicode, Lucida Grande, sans-serif','Palatino Linotype, Book Antiqua, Palatino, serif','Tahoma, Geneva, sans-serif','Times New Roman, Times, serif','Trebuchet MS, sans-serif','Verdana, Geneva, sans-serif'),
									'req'=>'',
									'sub'=>array('boozurk_font_size') 
								),
		'boozurk_font_size'=>	array( 									'group'=>'other',									'type'=>'sel',									'default'=>'14px',									'description'=>__( 'font size','boozurk' ),									'info'=>'',									'options'=>array('10px','11px','12px','13px','14px','15px','16px'),									'options_l10n'=>array('10px','11px','12px','13px','14px','15px','16px'),
									'req'=>'',
									'sub'=>false 
								),
		'boozurk_google_font_family'=>	array(
											'group' => 'other',
											'type' => 'txt',
											'default' => '',
											'description' => __( 'Google web font', 'boozurk' ),
											'info' => __( 'Copy and paste <a href="http://www.google.com/webfonts" target="_blank"><strong>Google web font</strong></a> name here. Example: <code>Architects Daughter</code>', 'boozurk' ),
											'req' => '',
											'sub' => array( 'boozurk_google_font_body', 'boozurk_google_font_post_title', 'boozurk_google_font_post_content' )
										),
		'boozurk_google_font_body' =>	array(
											'group' => 'other',
											'type' => 'chk',
											'default' => 0,
											'description' => __( 'for whole site', 'boozurk' ),
											'info' => '',
											'req' => '',
											'sub' => false
										),
		'boozurk_google_font_post_title' =>	array(
													'group' => 'other',
													'type' => 'chk',
													'default' => 1,
													'description' => __( 'for posts/pages title', 'boozurk' ),
													'info' => '',
													'req' => '',
													'sub' => false
												),
		'boozurk_google_font_post_content' =>	array(
													'group' => 'other',
													'type' => 'chk',
													'default' => 0,
													'description' => __( 'for posts/pages content', 'boozurk' ),
													'info' => '',
													'req' => '',
													'sub' => false
												),
		'boozurk_post_formats'=>array( 
									'group'=>'index',
									'type'=>'chk',
									'default'=>1,
									'description'=>__( 'post formats support','boozurk' ),
									'info'=>'<a href="http://codex.wordpress.org/Post_Formats" target="_blank">WordPress Codex : Post Formats</a>',
									'req'=>'' 
								),
		'boozurk_browse_links'=>array( 									'group'=>'content',									'type'=>'chk',									'default'=>1,									'description'=>__( 'quick browsing links', 'boozurk' ),									'info'=>__( 'show navigation links before post content', 'boozurk' ),									'req'=>'' 
								),
		'boozurk_post_date'=>	array( 									'group'=>'content',									'type'=>'chk',									'default'=>1,									'description'=>__( 'post date', 'boozurk' ),									'info'=>__( 'show date right before post content (only in posts index)', 'boozurk' ),									'req'=>'' 
								),
		'boozurk_featured_title'=>	array( 												'group'=>'content',												'type'=>'sel',												'default'=>'lists',												'description'=>__( 'enhanced post title','boozurk' ),												'info'=>__( 'use the featured image as background for the post title','boozurk' ),												'options'=>array('lists','single','both','none'),												'options_l10n'=>array(__('in lists','boozurk'),__('in single posts/pages','boozurk'),__('both','boozurk'),__('none','boozurk')),
												'req'=>'',
												'sub'=>array('boozurk_featured_title_thumb') 
											),
		'boozurk_featured_title_thumb'=>	array( 
												'group'=>'content',
												'type'=>'chk',
												'default'=>0,
												'description'=>__( 'thumbnail','boozurk' ),
												'info'=>'use small thumbnail instead of the full image',
												'req'=>'',
												'sub'=>false 
											),
		'boozurk_smilies' =>	array(
									'group' => 'content',
									'type' => 'chk',
									'default' => 1,
									'description' => __( 'custom smilies', 'boozurk' ),
									'info' => '(^_^) (ToT) (o_O) ...',
									'req' => ''
								),
		'boozurk_plusone'=>	array( 								'group'=>'other',								'type'=>'chk',								'default'=>1,								'description'=>'<a href="https://plus.google.com/" target="_blank">Google +1</a>',								'info'=>__( 'integrates the +1 feature for your contents', 'boozurk' ),								'req'=>'' 
							),
		'boozurk_main_menu'=>	array( 									'group'=>'other',									'type'=>'sel',									'default'=>'text',									'description'=>__( 'main menu look','boozurk' ),									'info'=>__( 'select the style of the main menu: text, thumbnails or both','boozurk' ),									'options'=>array( 'text', 'thumbnail', 'thumbnail and text' ),									'options_l10n'=>array( __('text','boozurk'), __('thumbnail','boozurk'), __('thumbnail and text','boozurk') ),
									'req'=>'',
									'sub'=>array('boozurk_main_menu_icon_size') 
								),
		'boozurk_main_menu_icon_size'=>	array( 											'group'=>'other',											'type'=>'sel',											'default'=>'48',											'description'=>__( 'main menu icon size','boozurk' ),											'info'=>__( 'the dimension of the thumbnails in main menu (if "thumbnails" style is selected)','boozurk' ),											'options'=>array ('32', '48', '64', '96'),											'options_l10n'=>array ('32', '48', '64', '96'),
											'req'=>'',
											'sub'=>false 
										),
		'boozurk_logo'=>array( 							'group'=>'other',							'type'=>'url',							'default'=>'',							'description'=>__( 'Logo','boozurk' ),							'info'=>__( 'a logo in the upper right corner of the window. paste here the complete path to image location. leave empty to ignore','boozurk' ),							'req'=>'',
							'sub'=>array('boozurk_logo_description','','boozurk_logo_login') 
						),
		'boozurk_logo_description'=>	array( 
									'group'=>'other',
									'type'=>'chk',
									'default'=>1,
									'description'=>__( 'tagline','boozurk' ),
									'info'=>__( 'show site description below the logo','boozurk' ),
									'req'=>'',
									'sub'=>false 
								),
		'boozurk_logo_login'=>	array( 									'group'=>'other',									'type'=>'chk',									'default'=>1,									'description'=>__( 'Logo in login page','boozurk' ),									'info'=>__( 'use the logo in the login page','boozurk' ),									'req'=>'boozurk_logo',
									'sub'=>false 
								),
		'boozurk_editor_style'=>array( 									'group'=>'other',									'type'=>'chk',									'default'=>1,									'description'=>__( 'editor style', 'boozurk' ),									'info'=>__( "add style to the editor in order to write the post exactly how it will appear on the site", 'boozurk' ),									'req'=>'' 
								),
		'boozurk_comment_style'=>array( 
									'group'=>'other',
									'type'=>'chk',
									'default'=>0,
									'description'=>__( 'comment style', 'boozurk' ),
									'info'=>__( 'let the commenters to choose their comment background', 'boozurk' ),
									'req'=>'' 
								),
		'boozurk_custom_css' =>	array(
										'group' => 'other',
										'type' => 'txtarea',
										'default' => '',
										'description' => __( 'custom CSS code', 'boozurk' ),
										'info' => __( '<strong>For advanced users only</strong>: paste here your custom css code. it will be added after the defatult style', 'boozurk' ) . ' (<a href="'. get_stylesheet_uri() .'" target="_blank">style.css</a>)',
										'req' => ''
									),
		'boozurk_mobile_css'=>	array( 									'group'=>'mobile',									'type'=>'chk',									'default'=>1,									'description'=>__( 'mobile support','boozurk' ),									'info'=>__( 'use a dedicated style in mobile devices','boozurk' ),									'req'=>'',
									'sub' => array('boozurk_mobile_css_color')
								),
		'boozurk_mobile_css_color'=>	array(
											'group' => 'mobile',
											'type' => 'opt',
											'default' => 'light',
											'options' => array('light','dark'),
											'options_readable' => array('<img src="' . get_template_directory_uri() . '/images/mobile-light.png" alt="light" />','<img src="' . get_template_directory_uri() . '/images/mobile-dark.png" alt="dark" />'),
											'description' => __( 'colors', 'boozurk' ),
											'info' => '',
											'req' => '',
											'sub' => false
										),
		'boozurk_post_formats_standard'=>array( 
											'group'=>'index',
											'type'=>'gro',
											'default'=>1,
											'description'=>__( 'standard','boozurk' ),
											'info'=>sprintf( __( '%s format posts', 'boozurk' ), __( 'standard','boozurk' ) ),
											'sub'=>array('boozurk_post_formats_standard_title','boozurk_post_formats_standard_content'),
											'req'=>'' 
										),
		'boozurk_post_formats_standard_title'=>	array( 
													'group'=>'index',
													'type'=>'sel',
													'default'=>'post title',
													'description'=>__( 'title','boozurk' ),
													'info'=>'',
													'options'=>array('post title', 'post date','none'),
													'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'none','boozurk' )),
													'req'=>'',
													'sub'=>false 
												),
		'boozurk_post_formats_standard_content'=>array( 
													'group'=>'index',
													'type'=>'sel',
													'default'=>'content',
													'description'=>__( 'content','boozurk' ),
													'info'=>'',
													'options'=>array( 'content', 'excerpt', 'none'),
													'options_l10n'=>array(__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
													'req'=>'',
													'sub'=>false 
												),
		'boozurk_post_formats_gallery'=>array( 											'group'=>'index',											'type'=>'chk',											'default'=>1,											'description'=>__( 'gallery','boozurk' ),											'info'=>sprintf( __( '%s format posts', 'boozurk' ), __( 'gallery','boozurk' ) ),
											'req'=>'boozurk_post_formats',
											'sub'=>array('boozurk_post_formats_gallery_title','boozurk_post_formats_gallery_content') 
										),
		'boozurk_post_formats_gallery_title'=>	array( 													'group'=>'index',													'type'=>'sel',													'default'=>'none',													'description'=>__( 'title','boozurk' ),													'info'=>'',													'options'=>array('post title', 'post date','none'),													'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'none','boozurk' )),
													'req'=>'boozurk_post_formats_gallery',
													'sub'=>false 
												),
		'boozurk_post_formats_gallery_content'=>array( 													'group'=>'index',													'type'=>'sel',													'default'=>'presentation',													'description'=>__( 'content','boozurk' ),													'info'=>'',													'options'=>array( 'presentation', 'content', 'excerpt', 'none'),													'options_l10n'=>array(__( 'presentation','boozurk' ),__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
													'req'=>'boozurk_post_formats_gallery',
													'sub'=>false 
												),
		'boozurk_post_formats_aside'=>	array( 											'group'=>'index',											'type'=>'chk',											'default'=>1,											'description'=>__( 'aside','boozurk' ),											'info'=>sprintf( __( '%s format posts', 'boozurk' ), __( 'aside','boozurk' ) ),
											'req'=>'boozurk_post_formats' 
										),
		'boozurk_post_formats_audio'=>	array( 											'group'=>'index',											'type'=>'chk',											'default'=>1,											'description'=>__( 'audio','boozurk' ),											'info'=>sprintf( __( '%s format posts', 'boozurk' ), __( 'audio','boozurk' ) ),
											'req'=>'boozurk_post_formats',
											'sub'=>array('boozurk_post_formats_audio_title','boozurk_post_formats_audio_content') 
										),
		'boozurk_post_formats_audio_title'=>array( 												'group'=>'index',												'type'=>'sel',												'default'=>'first link text',												'description'=>__( 'title','boozurk' ),												'info'=>'',												'options'=>array( 'post title', 'post date', 'first link text', 'none'),												'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'first link text','boozurk' ),__( 'none','boozurk' )),
												'req'=>'boozurk_post_formats_audio',
												'sub'=>false 
											),
		'boozurk_post_formats_audio_content'=>	array( 													'group'=>'index',													'type'=>'sel',													'default'=>'audio player',													'description'=>__( 'content','boozurk' ),													'info'=>'',													'options'=>array( 'audio player', 'content', 'excerpt', 'none'),													'options_l10n'=>array(__( 'audio player','boozurk' ),__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
													'req'=>'boozurk_post_formats_audio',
													'sub'=>false 
												),
		'boozurk_post_formats_image'=>	array( 											'group'=>'index',											'type'=>'chk',											'default'=>1,											'description'=>__( 'image','boozurk' ),											'info'=>sprintf( __( '%s format posts', 'boozurk' ), __( 'image','boozurk' ) ),
											'req'=>'boozurk_post_formats',
											'sub'=>array('boozurk_post_formats_image_title','boozurk_post_formats_image_content') 
										),
		'boozurk_post_formats_image_title'=>array( 												'group'=>'index',												'type'=>'sel',												'default'=>'first image title',												'description'=>__( 'title','boozurk' ),												'info'=>'',												'options'=>array( 'post title', 'post date', 'first image title', 'none'),												'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'first image title','boozurk' ),__( 'none','boozurk' )),
												'req'=>'boozurk_post_formats_image',
												'sub'=>false 
											),
		'boozurk_post_formats_image_content'=>	array( 													'group'=>'index',													'type'=>'sel',													'default'=>'first image',													'description'=>__( 'content','boozurk' ),													'info'=>'',													'options'=>array( 'first image', 'content', 'excerpt', 'none'),													'options_l10n'=>array(__( 'first image','boozurk' ),__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
													'req'=>'boozurk_post_formats_gallery',
													'sub'=>false 
												),
		'boozurk_post_formats_link'=>	array( 											'group'=>'index',											'type'=>'chk',											'default'=>1,											'description'=>__( 'link','boozurk' ),											'info'=>sprintf( __( '%s format posts', 'boozurk' ), __( 'link','boozurk' ) ),
											'req'=>'boozurk_post_formats',
											'sub'=>array('boozurk_post_formats_link_title','boozurk_post_formats_link_content') 
										),
		'boozurk_post_formats_link_title'=>	array( 												'group'=>'index',												'type'=>'sel',												'default'=>'first link text',												'description'=>__( 'title','boozurk' ),												'info'=>'',												'options'=>array( 'post title', 'post date', 'first link text', 'none'),												'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'first link text','boozurk' ),__( 'none','boozurk' )),
												'req'=>'boozurk_post_formats_link',
												'sub'=>false 
											),
		'boozurk_post_formats_link_content'=>	array( 													'group'=>'index',													'type'=>'sel',													'default'=>'none',													'description'=>__( 'content','boozurk' ),													'info'=>'',													'options'=>array( 'content', 'excerpt', 'none'),													'options_l10n'=>array(__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
													'req'=>'boozurk_post_formats_gallery',
													'sub'=>false 
												),
		'boozurk_post_formats_quote'=>	array( 											'group'=>'index',											'type'=>'chk',											'default'=>1,											'description'=>__( 'quote','boozurk' ),											'info'=>sprintf( __( '%s format posts', 'boozurk' ), __( 'quote','boozurk' ) ),
											'req'=>'boozurk_post_formats',
											'sub'=>array('boozurk_post_formats_quote_title','boozurk_post_formats_quote_content') 
										),
		'boozurk_post_formats_quote_title'=>array( 												'group'=>'index',												'type'=>'sel',												'default'=>'short quote excerpt',												'description'=>__( 'title','boozurk' ),												'info'=>'',												'options'=>array( 'post title', 'post date', 'short quote excerpt', 'none'),												'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'short quote excerpt','boozurk' ),__( 'none','boozurk' )),
												'req'=>'boozurk_post_formats_quote',
												'sub'=>false 
											),
		'boozurk_post_formats_quote_content'=>	array( 													'group'=>'index',													'type'=>'sel',													'default'=>'content',													'description'=>__( 'content','boozurk' ),													'info'=>'',													'options'=>array( 'content', 'excerpt', 'none'),													'options_l10n'=>array(__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
													'req'=>'boozurk_post_formats_gallery',
													'sub'=>false 
												),
		'boozurk_post_formats_status'=>	array( 											'group'=>'index',											'type'=>'chk',											'default'=>1,											'description'=>__( 'status','boozurk' ),											'info'=>sprintf( __( '%s format posts', 'boozurk' ), __( 'status','boozurk' ) ),
											'req'=>'boozurk_post_formats' 
										),
		'boozurk_post_formats_video'=>	array( 											'group'=>'index',											'type'=>'chk',											'default'=>1,											'description'=>__( 'video','boozurk' ),											'info'=>sprintf( __( '%s format posts', 'boozurk' ), __( 'video','boozurk' ) ),
											'req'=>'boozurk_post_formats' 
										),
		'boozurk_tbcred'=>	array( 								'group'=>'other',								'type'=>'chk',								'default'=>1,								'description'=>__( 'theme credits','boozurk' ),								'info'=>__( "please, don't hide theme credits",'boozurk' ),								'req'=>'' 
							)
	);

	if ( $option == 'groups' )
		return $boozurk_groups;
	elseif ( $option )
		return $boozurk_coa[$option];
	else
		return $boozurk_coa;
}

if ( ( $boozurk_opt['boozurk_logo_login'] == 1 ) && ( $boozurk_opt['boozurk_logo'] != '' ) ) {
	add_action( 'login_footer', 'boozurk_login_footer' );
	add_action( 'login_head', 'boozurk_login_head' );
}

// custom gallery shortcode function
if ( isset( $boozurk_opt['boozurk_js_thickbox_force'] ) && ( $boozurk_opt['boozurk_js_thickbox_force'] == 1 ) ) {
	remove_shortcode( 'gallery', 'gallery_shortcode' );
	add_shortcode( 'gallery', 'boozurk_gallery_shortcode' );
}

function boozurk_gallery_shortcode($attr) {
	$attr['link'] = 'file';
	echo gallery_shortcode($attr);
}

if ( !function_exists( 'boozurk_widget_area_init' ) ) {
	function boozurk_widget_area_init() {

		// Area 0, in the left sidebar.
		register_sidebar( array(
			'name' => __( 'Primary idebar', 'boozurk' ),
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
		global $boozurk_opt, $boozurk_is_mobile_browser;
		if ( $boozurk_is_mobile_browser ) return; // skip if in mobile view
?>
<style type="text/css">
	body {
		font-size: <?php echo $boozurk_opt['boozurk_font_size']; ?>;
<?php if ( $boozurk_opt['boozurk_google_font_family'] && $boozurk_opt['boozurk_google_font_body'] ) { ?>
		font-family: <?php echo $boozurk_opt['boozurk_google_font_family']; ?>;
<?php } else { ?>
		font-family: <?php echo $boozurk_opt['boozurk_font_family']; ?>;
<?php } ?>
	}
<?php if ( $boozurk_opt['boozurk_google_font_family'] && $boozurk_opt['boozurk_google_font_post_title'] ) { ?>
	h2.storytitle {
		font-family: <?php echo $boozurk_opt['boozurk_google_font_family']; ?>;
	}
<?php } ?>
<?php if ( $boozurk_opt['boozurk_google_font_family'] && $boozurk_opt['boozurk_google_font_post_content'] ) { ?>
	.storycontent {
		font-family: <?php echo $boozurk_opt['boozurk_google_font_family']; ?>;
	}
<?php } ?>
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
<?php if ( $boozurk_opt['boozurk_custom_css'] ) echo $boozurk_opt['boozurk_custom_css']; ?>

<?php
	$args=array(
		'orderby' => 'name',
		'order' => 'DESC'
	);
	$categories=get_categories($args);
	foreach($categories as $category) {
		$cat_color = isset($boozurk_opt['boozurk_cat_colors'][$category->term_id]) ? $boozurk_opt['boozurk_cat_colors'][$category->term_id] : '#87CEEB';
		$cat_class = '.category-' . sanitize_html_class($category->slug, $category->term_id);
		echo '#posts_content ' . $cat_class . ' { border-color:' . $cat_color . ' ;}' . "\n";
		echo '.widget .cat-item-' . $category->term_id . ' > a, #posts_content .cat-item-' . $category->term_id . ' > a { border-left: 1em solid ' . $cat_color . ' ; }' . "\n";
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

// localize js
if ( !function_exists( 'boozurk_localize_scripts' ) ) {
	function boozurk_localize_scripts() {
		global $boozurk_opt, $boozurk_is_mobile_browser, $boozurk_is_printpreview;
		if ( is_admin() || ( $boozurk_opt['boozurk_jsani'] == 0 ) || $boozurk_is_mobile_browser || $boozurk_is_printpreview ) return;
		?>
<script type="text/javascript">
	/* <![CDATA[ */
		bz_post_expander_text = "<?php _e( 'Post loading, please wait...','boozurk' ); ?>";
		bz_gallery_preview_text = "<?php _e( 'Preview','boozurk' ); ?>";
		bz_gallery_click_text = "<?php _e( 'Click on thumbnails','boozurk' ); ?>";
		bz_infinite_scroll_text = "<?php _e( 'Page is loading, please wait...','boozurk' ); ?>";
		bz_infinite_scroll_text_end = "<?php _e( 'No more posts beyond this line','boozurk' ); ?>";
	/* ]]> */
</script>
		<?php
	}
}

// initialize js
if ( !function_exists( 'boozurk_initialize_scripts' ) ) {
	function boozurk_initialize_scripts() {
		global $boozurk_opt, $boozurk_is_mobile_browser, $boozurk_is_printpreview;

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

<?php if ( ( $boozurk_opt['boozurk_jsani'] == 0 ) || $boozurk_is_printpreview ) return; ?>

<script type="text/javascript">
	/* <![CDATA[ */
	function boozurk_Init(notfirsttime) {
<?php if ( $boozurk_opt['boozurk_js_post_expander'] == 1 ) { ?>
		boozurkScripts.post_expander();
<?php } ?>
<?php if ( $boozurk_opt['boozurk_js_thickbox'] == 1 ) { ?>
		boozurkScripts.init_thickbox();
<?php } ?>
<?php if ( $boozurk_opt['boozurk_js_tooltips'] == 1 ) { ?>
		boozurkScripts.tooltips();
		boozurkScripts.cooltips('.minibutton,.share-item img,.tb_widget_categories a,#bz-quotethis,.tb_widget_latest_commentators li,.tb_widget_social a,.post-format-item.compact img,a.bz-tipped-anchor',true,'');
		boozurkScripts.cooltips('.pmb_comm',true,'<?php _e( 'Comments closed','boozurk' ); ?>');
<?php } ?>
<?php if ( $boozurk_opt['boozurk_plusone'] == 1 ) { ?>
		if(notfirsttime) gapi.plusone.go("posts_content");
<?php } ?>
	}

	jQuery(document).ready(function($){
		boozurkScripts.animate_menu();
		boozurkScripts.scroll_top_bottom();
<?php if ( is_singular() && comments_open() ) { ?>
		boozurkScripts.comment_variants();
<?php } ?>
		boozurk_Init(0);
<?php if ( ( $boozurk_opt['boozurk_infinite_scroll'] == 1 ) && !is_singular() && !is_404() ) { ?>
		boozurkScripts.infinite_scroll('<?php echo $boozurk_opt['boozurk_infinite_scroll_type']; ?>');
<?php } ?>
	});
	/* ]]> */
</script>

<?php
	}
}

// Add stylesheets to page
if ( !function_exists( 'boozurk_stylesheet' ) ) {
	function boozurk_stylesheet(){
		global $boozurk_opt, $boozurk_version, $boozurk_is_mobile_browser, $boozurk_is_printpreview;

		if ( is_admin() || $boozurk_is_mobile_browser ) return;

		if ( $boozurk_is_printpreview ) { //print preview

			wp_enqueue_style( 'bz_general-style', get_template_directory_uri() . '/css/print.css', false, $boozurk_version, 'screen' );
			wp_enqueue_style( 'bz_preview-style', get_template_directory_uri() . '/css/print_preview.css', false, $boozurk_version, 'screen' );

		} else { //normal view

			wp_enqueue_style( 'bz_general-style', get_stylesheet_uri(), array('thickbox'), $boozurk_version, 'screen' );

		}

		//google font
		if ( $boozurk_opt['boozurk_google_font_family'] ) wp_enqueue_style( 'bz-google-fonts', 'http://fonts.googleapis.com/css?family=' . str_replace( ' ', '+' , $boozurk_opt['boozurk_google_font_family'] ) );

		wp_enqueue_style( 'bz_print-style', get_template_directory_uri() . '/css/print.css', false, $boozurk_version, 'print' );

	}
}

// add scripts
if ( !function_exists( 'boozurk_scripts' ) ) {
	function boozurk_scripts(){
		global $boozurk_opt, $boozurk_version, $boozurk_is_mobile_browser, $boozurk_is_printpreview;

		if ( is_admin() || $boozurk_is_mobile_browser || $boozurk_is_printpreview ) return;

		if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); //custom comment-reply pop-up box

		$deps = array('jquery');
		if ( $boozurk_opt['boozurk_js_thickbox'] == 1 ) $deps[] = 'thickbox';
		if ( $boozurk_opt['boozurk_jsani'] == 1 ) wp_enqueue_script( 'bz-js', get_template_directory_uri() . '/js/boozurk.min.js', $deps, $boozurk_version, false );

		if ( $boozurk_opt['boozurk_plusone'] == 1 ) wp_enqueue_script( 'bz-Plus1', 'https://apis.google.com/js/plusone.js', array(), false, true );

	}
}

// post-top-date
if ( !function_exists( 'boozurk_print_date' ) ) {
	function boozurk_print_date() {
		global $boozurk_opt;
		if ( $boozurk_opt['boozurk_post_date'] == 1 && !is_singular() ) echo '<div class="bz-post-top-date fixfloat">' . get_the_time( get_option( 'date_format' ) ) . '</div>';
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


// display the post title with the featured image
if ( !function_exists( 'boozurk_featured_title' ) ) {
	function boozurk_featured_title( $args = '' ) {
		global $post, $boozurk_opt;
		
		$defaults = array( 'alternative' => '', 'fallback' => '', 'featured' => true, 'href' => get_permalink(), 'target' => '', 'title' => the_title_attribute( array('echo' => 0 ) ) );
		$args = wp_parse_args( $args, $defaults );
		
		$post_title = $args['alternative'] ? $args['alternative'] : get_the_title();
		$post_title = $post_title ? $post_title : $args['fallback'];
		$link_target = $args['target'] ? ' target="'.$args['target'].'"' : '';
		if ( $post_title ) $post_title = '<h2 class="storytitle"><a title="' . esc_attr( $args['title'] ) . '" href="' . $args['href'] . '"' . $link_target . ' rel="bookmark">' . $post_title . '</a></h2>';
		switch ( $boozurk_opt['boozurk_featured_title'] ) {
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
			if ( $boozurk_opt['boozurk_featured_title_thumb'] = 'large') {
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
		global $post, $boozurk_opt;
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
			<?php if ( $boozurk_opt['boozurk_plusone'] == 1 ) { ?>
				<div class="bz-plusone-wrap"><div class="g-plusone" data-annotation="none" data-href="<?php the_permalink(); ?>"></div></div>
			<?php } ?>
		</div>
		<?php if ( !is_singular() ) edit_post_link(); ?>
		<?php
	}
}

// the breadcrumb
if (!function_exists('boozurk_breadcrumb')) {
	function boozurk_breadcrumb(){
		?>
		<div id="bz-breadcrumb-wrap">
			<div id="bz-breadcrumb">
				<?php echo boozurk_get_the_breadcrumb(); ?>
				<div class="fixfloat"></div>
			</div>
			<?php boozurk_search_reminder(); ?>
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
			echo '<div class="bz-breadcrumb-reminder">';
			boozurk_post_details( array( 'date' => 0, 'tags' => 0, 'categories' => 0, 'avatar_size' => 64 ) );
			echo '</div>';
		} elseif ( is_page() ) {
			boozurk_multipages();
		}
	}
}

// the last commenters of a post
if ( !function_exists( 'boozurk_last_comments' ) ) {
	function boozurk_last_comments( $id , $num = 6 ) {
		global $boozurk_opt;
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
		global $post, $boozurk_opt, $boozurk_is_allcat_page;
		
		$is_post = is_single() && !is_attachment() && !$boozurk_is_allcat_page;
		$is_image = is_attachment() && !$boozurk_is_allcat_page;
		$is_page = is_singular() && !is_single() && !is_attachment() && !$boozurk_is_allcat_page;
		$is_singular = is_singular() && !$boozurk_is_allcat_page;
	?>

<div id="navbuttons"<?php if ( $fixed ) echo ' class="fixed"'; ?>>

		<?php if ( $is_singular && get_edit_post_link() ) { 												// ------- Edit ------- ?>
			<div class="minibutton" title="<?php esc_attr_e( 'Edit','boozurk' ); ?>">
				<a href="<?php echo get_edit_post_link(); ?>">
					<span class="minib_img minib_edit">&nbsp;</span>
				</a>
			</div>
		<?php } ?>
		
		<?php if ( $print && $is_singular ) { 																// ------- Print ------- ?>
			<div class="minibutton" title="<?php esc_attr_e( 'Print','boozurk' ); ?>">
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
				<a href="<?php global $bz_tmptrackback; echo $bz_tmptrackback; ?>" rel="trackback">
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

		<?php if ( $next_prev && $is_post && get_next_post() ) { 											// ------- Next post ------- ?>
			<div class="minibutton" title="<?php esc_attr( printf( __( 'Next Post', 'boozurk' ) . ': %s', strip_tags( get_the_title( get_next_post() ) ) ) ); ?>">
				<a href="<?php echo get_permalink( get_next_post() ); ?>">
					<span class="minib_img minib_npage">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if (  $next_prev && $is_post && get_previous_post() ) { 										// ------- Previous post ------- ?>
			<div class="minibutton" title="<?php esc_attr( printf( __( 'Previous Post', 'boozurk' ) . ': %s', strip_tags( get_the_title( get_previous_post() ) ) ) ); ?>">
				<a href="<?php echo get_permalink( get_previous_post() ); ?>">
					<span class="minib_img minib_ppage">&nbsp;</span>
				</a>
			</div>
		<?php } ?>

		<?php if ( $next_prev && !$is_singular && !$boozurk_is_allcat_page && get_previous_posts_link() ) { 		// ------- Newer Posts ------- ?>
			<div class="minibutton nb-nextprev" title="<?php esc_attr_e( 'Newer Posts', 'boozurk' ); ?>">
				<?php previous_posts_link( '<span class="minib_img minib_ppages">&nbsp;</span>' ); ?>
			</div>
		<?php } ?>

		<?php if ( $next_prev && !$is_singular && !$boozurk_is_allcat_page && get_next_posts_link() ) { 			// ------- Older Posts ------- ?>
			<div class="minibutton nb-nextprev" title="<?php esc_attr_e( 'Older Posts', 'boozurk' ); ?>">
				<?php next_posts_link( '<span class="minib_img minib_npages">&nbsp;</span>' ); ?>
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

// get the post thumbnail or (if not set) the format related icon
if ( !function_exists( 'boozurk_get_the_thumb' ) ) {
	function boozurk_get_the_thumb( $id, $size_w, $size_h, $class, $default = '' ) {
		if ( has_post_thumbnail( $id ) ) {
			return get_the_post_thumbnail( $id, array( $size_w,$size_h ) );
		} else {
			if ( function_exists( 'get_post_format' ) && get_post_format( $id ) ) {
				$format = get_post_format( $id );
			} else {
				$format = 'standard';
			}
			return '<img class="' . $class . ' wp-post-image ' . $format . '" alt="thumb" src="' . get_template_directory_uri() . '/images/img40.png" />';
		}
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
		register_setting( 'bz_settings_group', 'boozurk_options', 'boozurk_sanitize_options' );
	}
}

// set up custom colors and header image
if ( !function_exists( 'boozurk_setup' ) ) {
	function boozurk_setup() {
		global $boozurk_opt;

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
		if ( isset( $boozurk_opt['boozurk_editor_style'] ) && ( $boozurk_opt['boozurk_editor_style'] == 1 ) ) add_editor_style( 'css/editor-style.css' );
	
		// This theme uses post formats
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );

		$args = array(
			'width'					=> 1000, // Header image width (in pixels)
			'height'				=> 288, // Header image height (in pixels)
			'default-image'			=> '', // Header image default
			'header-text'			=> false, // Header text display default
			'default-text-color'	=> '21759B', // Header text color default
			'wp-head-callback'		=> 'boozurk_header_style',
			'admin-head-callback'	=> '',
			'flex-height'			=> true,
			'flex-width'			=> true
		);
	 
		$args = apply_filters( 'boozurk_custom_header_args', $args );
	 
		if ( function_exists( 'get_custom_header' ) ) {
			add_theme_support( 'custom-header', $args );
		}

	}
}

// custom header image style - gets included in the site header
if ( !function_exists( 'boozurk_header_style' ) ) {
	function boozurk_header_style() {
		//the custom header style
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

//custom smiles
if ( isset( $boozurk_opt['boozurk_smilies'] ) && $boozurk_opt['boozurk_smilies'] ) add_filter( 'smilies_src', 'boozurk_smiles_replace',10,2 );
function boozurk_smiles_replace( $src, $img ) {
	return get_template_directory_uri() . '/images/smilies/' . $img;
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
			if ( $boozurk_opt['boozurk_main_menu'] == 'thumbnail' ) {
				$title = $thumb;
			} elseif ( $boozurk_opt['boozurk_main_menu'] == 'thumbnail and text' ) {
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

// retrieve the posts page, then die (for "infinite_scroll" ajax request)
if ( !function_exists( 'boozurk_infinite_scroll_show_page' ) ) {
	function boozurk_infinite_scroll_show_page (  ) {
		global $post, $boozurk_opt, $wp_query, $paged;
		
		if ( !$paged ) {
			$paged = 1;
		}
		
		if ( have_posts() ) {
			echo '<div class="paged-separator" id="paged-separator-' . $paged . '"><h3>' . sprintf( __('Page %s','boozurk'), $paged ) . '</h3></div>';
			while ( have_posts() ) {
				the_post(); ?>
				<?php if ( post_password_required() ) {
					$bz_use_format = 'protected';
				} else {
					$bz_use_format = ( 
						function_exists( 'get_post_format' ) && 
						isset( $boozurk_opt['boozurk_post_formats_' . get_post_format( $post->ID ) ] ) && 
						$boozurk_opt['boozurk_post_formats_' . get_post_format( $post->ID ) ] == 1 
					) ? get_post_format( $post->ID ) : '' ;
				} ?>
				
				<?php boozurk_hook_before_post(); ?>
				<?php get_template_part( 'loop/post', $bz_use_format ); ?>
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
					<input type="button" value="<?php echo __( 'Next Page', 'boozurk' ); ?>" onClick="boozurkScripts.AJAX_paged();" />
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

function boozurk_login_footer() {
	global $boozurk_opt;
	?>	
<script type="text/javascript">
	/* <![CDATA[ */
		div = document.createElement('div');
		div.id = 'bz-logo';
		div.innerHTML = '<a href="<?php echo home_url(); ?>"><img src="<?php echo $boozurk_opt['boozurk_logo']; ?>" alt="logo" title="<?php echo esc_attr( get_bloginfo('description') ); ?>" /></a>';
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

//add a "wmode" fix for embed videos
if ( !function_exists( 'boozurk_content_replace' ) ) {
	function boozurk_content_replace( $content ){
		$content = str_replace( '</object>', '<param name="wmode" value="transparent"></object>', $content );
		$content = str_replace( '<embed ', '<embed wmode="transparent" ', $content );
		return $content;
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

?>