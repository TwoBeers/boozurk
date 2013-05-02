<?php
/**
 * options.php
 *
 * the options array
 *
 * @package Boozurk
 * @since 2.03
 */


// Complete Options Array, with type, defaults values, description, infos and required option
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
	$boozurk_groups = apply_filters( 'boozurk_options_groups', $boozurk_groups );

	$boozurk_coa = array(
		'boozurk_colors_link_wrap'=> array(
							'group'=>'colors',
							'type'=>'',
							'default'=>'',
							'description'=>__( 'links colors','boozurk' ),
							'info'=>'',
							'req'=>'',
							'sub'=>array('boozurk_colors_link', 'boozurk_colors_link_hover', 'boozurk_colors_link_sel')
		),
		'boozurk_colors_link'=> array(
							'group'=>'colors',
							'type'=>'col',
							'default'=>'#21759b',
							'description'=>'',
							'info'=>__( 'links','boozurk' ),
							'req'=>'',
							'sub'=>false
		),
		'boozurk_colors_link_hover'=> array(
							'group'=>'colors',
							'type'=>'col',
							'default'=>'#404040',
							'description'=>'',
							'info'=>__( 'highlighted links','boozurk' ),
							'req'=>'',
							'sub'=>false 
		),
		'boozurk_colors_link_sel'=> array(
							'group'=>'colors',
							'type'=>'col',
							'default'=>'#87CEEB',
							'description'=>'',
							'info'=>__( 'selected links','boozurk' ),
							'req'=>'',
							'sub'=>false 
		),
		'boozurk_cat_colors_wrap'=> array(
							'group'=>'colors',
							'type'=>'',
							'default'=>'',
							'description'=>__( 'colors for categories','boozurk' ),
							'info'=>'',
							'req'=>'',
							'sub'=>array('boozurk_cat_colors') 
		),
		'boozurk_cat_colors'=> array(
							'group'=>'colors',
							'type'=>'catcol',
							'default'=>array(),
							'defaultcolor'=>'#87CEEB',
							'description'=>'',
							'info'=>'',
							'req'=>'',
							'sub'=>false 
		),
		'boozurk_post_formats'=> array(
							'group'=>'index',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'post formats support','boozurk' ),
							'info'=>'<a href="http://codex.wordpress.org/Post_Formats" target="_blank">WordPress Codex : Post Formats</a>',
							'req'=>'' 
		),
		'boozurk_post_formats_standard'=> array(
							'group'=>'index',
							'type'=>'gro',
							'default'=>1,
							'description'=>get_post_format_string( 'standard' ),
							'info'=>sprintf( __( '%s format posts', 'boozurk' ), '&quot;' . get_post_format_string( 'standard' ) . '&quot;' ),
							'sub'=>array('boozurk_post_formats_standard_title','boozurk_post_formats_standard_content'),
							'req'=>'' 
		),
		'boozurk_post_formats_standard_title'=> array(
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
		'boozurk_post_formats_standard_content'=> array(
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
		'boozurk_post_formats_aside'=> array(
							'group'=>'index',
							'type'=>'chk',
							'default'=>1,
							'description'=>get_post_format_string( 'aside' ),
							'info'=>sprintf( __( '%s format posts', 'boozurk' ), '&quot;' . get_post_format_string( 'aside' ) . '&quot;' ),
							'req'=>'boozurk_post_formats' 
		),
		'boozurk_post_formats_audio'=> array(
							'group'=>'index',
							'type'=>'chk',
							'default'=>1,
							'description'=>get_post_format_string( 'audio' ),
							'info'=>sprintf( __( '%s format posts', 'boozurk' ), '&quot;' . get_post_format_string( 'audio' ) . '&quot;' ),
							'req'=>'boozurk_post_formats',
							'sub'=>array('boozurk_post_formats_audio_title','boozurk_post_formats_audio_content') 
		),
		'boozurk_post_formats_audio_title'=> array(
							'group'=>'index',
							'type'=>'sel',
							'default'=>'first link text',
							'description'=>__( 'title','boozurk' ),
							'info'=>'',
							'options'=>array( 'post title', 'post date', 'first link text', 'none'),
							'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'first link text','boozurk' ),__( 'none','boozurk' )),
							'req'=>'boozurk_post_formats_audio',
							'sub'=>false 
		),
		'boozurk_post_formats_audio_content'=> array(
							'group'=>'index',
							'type'=>'sel',
							'default'=>'audio player',
							'description'=>__( 'content','boozurk' ),
							'info'=>'',
							'options'=>array( 'audio player', 'content', 'excerpt', 'none'),
							'options_l10n'=>array(__( 'audio player','boozurk' ),__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
							'req'=>'boozurk_post_formats_audio',
							'sub'=>false 
		),
		'boozurk_post_formats_chat'=> array(
							'group'=>'index',
							'type'=>'chk',
							'default'=>1,
							'description'=>get_post_format_string( 'chat' ),
							'info'=>sprintf( __( '%s format posts', 'boozurk' ), '&quot;' . get_post_format_string( 'chat' ) . '&quot;' ),
							'req'=>'boozurk_post_formats',
							'sub'=>array('boozurk_post_formats_audio_title','boozurk_post_formats_audio_content') 
		),
		'boozurk_post_formats_gallery'=> array(
							'group'=>'index',
							'type'=>'chk',
							'default'=>1,
							'description'=>get_post_format_string( 'gallery' ),
							'info'=>sprintf( __( '%s format posts', 'boozurk' ), '&quot;' . get_post_format_string( 'gallery' ) . '&quot;' ),
							'req'=>'boozurk_post_formats',
							'sub'=>array('boozurk_post_formats_gallery_title','boozurk_post_formats_gallery_content') 
		),
		'boozurk_post_formats_gallery_title'=> array(
							'group'=>'index',
							'type'=>'sel',
							'default'=>'none',
							'description'=>__( 'title','boozurk' ),
							'info'=>'',
							'options'=>array('post title', 'post date','none'),
							'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'none','boozurk' )),
							'req'=>'boozurk_post_formats_gallery',
							'sub'=>false 
		),
		'boozurk_post_formats_gallery_content'=> array(
							'group'=>'index',
							'type'=>'sel',
							'default'=>'presentation',
							'description'=>__( 'content','boozurk' ),
							'info'=>'',
							'options'=>array( 'presentation', 'content', 'excerpt', 'none'),
							'options_l10n'=>array(__( 'presentation','boozurk' ),__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
							'req'=>'boozurk_post_formats_gallery',
							'sub'=>false 
		),
		'boozurk_post_formats_image'=> array(
							'group'=>'index',
							'type'=>'chk',
							'default'=>1,
							'description'=>get_post_format_string( 'image' ),
							'info'=>sprintf( __( '%s format posts', 'boozurk' ), '&quot;' . get_post_format_string( 'image' ) . '&quot;' ),
							'req'=>'boozurk_post_formats',
							'sub'=>array('boozurk_post_formats_image_title','boozurk_post_formats_image_content') 
		),
		'boozurk_post_formats_image_title'=> array(
							'group'=>'index',
							'type'=>'sel',
							'default'=>'first image title',
							'description'=>__( 'title','boozurk' ),
							'info'=>'',
							'options'=>array( 'post title', 'post date', 'first image title', 'none'),
							'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'first image title','boozurk' ),__( 'none','boozurk' )),
							'req'=>'boozurk_post_formats_image',
							'sub'=>false 
		),
		'boozurk_post_formats_image_content'=> array(
							'group'=>'index',
							'type'=>'sel',
							'default'=>'first image',
							'description'=>__( 'content','boozurk' ),
							'info'=>'',
							'options'=>array( 'first image', 'content', 'excerpt', 'none'),
							'options_l10n'=>array(__( 'first image','boozurk' ),__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
							'req'=>'boozurk_post_formats_gallery',
							'sub'=>false 
		),
		'boozurk_post_formats_link'=> array(
							'group'=>'index',
							'type'=>'chk',
							'default'=>1,
							'description'=>get_post_format_string( 'link' ),
							'info'=>sprintf( __( '%s format posts', 'boozurk' ), '&quot;' . get_post_format_string( 'link' ) . '&quot;' ),
							'req'=>'boozurk_post_formats',
							'sub'=>array('boozurk_post_formats_link_title','boozurk_post_formats_link_content') 
		),
		'boozurk_post_formats_link_title'=> array(
							'group'=>'index',
							'type'=>'sel',
							'default'=>'first link text',
							'description'=>__( 'title','boozurk' ),
							'info'=>'',
							'options'=>array( 'post title', 'post date', 'first link text', 'none'),
							'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'first link text','boozurk' ),__( 'none','boozurk' )),
							'req'=>'boozurk_post_formats_link',
							'sub'=>false 
		),
		'boozurk_post_formats_link_content'=> array(
							'group'=>'index',
							'type'=>'sel',
							'default'=>'none',
							'description'=>__( 'content','boozurk' ),
							'info'=>'',
							'options'=>array( 'content', 'excerpt', 'none'),
							'options_l10n'=>array(__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
							'req'=>'boozurk_post_formats_gallery',
							'sub'=>false 
		),
		'boozurk_post_formats_quote'=> array(
							'group'=>'index',
							'type'=>'chk',
							'default'=>1,
							'description'=>get_post_format_string( 'quote' ),
							'info'=>sprintf( __( '%s format posts', 'boozurk' ), '&quot;' . get_post_format_string( 'quote' ) . '&quot;' ),
							'req'=>'boozurk_post_formats',
							'sub'=>array('boozurk_post_formats_quote_title','boozurk_post_formats_quote_content') 
		),
		'boozurk_post_formats_quote_title'=> array(
							'group'=>'index',
							'type'=>'sel',
							'default'=>'short quote excerpt',
							'description'=>__( 'title','boozurk' ),
							'info'=>'',
							'options'=>array( 'post title', 'post date', 'short quote excerpt', 'none'),
							'options_l10n'=>array(__( 'post title','boozurk' ),__( 'post date','boozurk' ),__( 'short quote excerpt','boozurk' ),__( 'none','boozurk' )),
							'req'=>'boozurk_post_formats_quote',
							'sub'=>false 
		),
		'boozurk_post_formats_quote_content'=> array(
							'group'=>'index',
							'type'=>'sel',
							'default'=>'content',
							'description'=>__( 'content','boozurk' ),
							'info'=>'',
							'options'=>array( 'content', 'excerpt', 'none'),
							'options_l10n'=>array(__( 'content','boozurk' ),__( 'excerpt','boozurk' ),__( 'none','boozurk' )),
							'req'=>'boozurk_post_formats_gallery',
							'sub'=>false 
		),
		'boozurk_post_formats_status'=> array(
							'group'=>'index',
							'type'=>'chk',
							'default'=>1,
							'description'=>get_post_format_string( 'status' ),
							'info'=>sprintf( __( '%s format posts', 'boozurk' ), '&quot;' . get_post_format_string( 'status' ) . '&quot;' ),
							'req'=>'boozurk_post_formats' 
		),
		'boozurk_post_formats_video'=> array(
							'group'=>'index',
							'type'=>'chk',
							'default'=>1,
							'description'=>get_post_format_string( 'video' ),
							'info'=>sprintf( __( '%s format posts', 'boozurk' ), '&quot;' . get_post_format_string( 'video' ) . '&quot;' ),
							'req'=>'boozurk_post_formats' 
		),
		'boozurk_blank_title'=> array(
							'group'=>'content',
							'type'=>'chk',
							'default'=>1,
							'description'=> __( 'blank titles', 'boozurk' ),
							'info' => __( 'set the standard text for blank titles', 'boozurk' ),
							'req'=>'',
							'sub'=>array('boozurk_blank_title_text') 
		),
		'boozurk_blank_title_text' => array(
							'group' => 'content',
							'type' => 'txt',
							'default' => __( '(no title)', 'boozurk' ),
							'description' => __( 'default text', 'boozurk' ),
							'info' => __( '<br>you may use these codes:<br><code>%d</code> for post date<br><code>%f</code> for post format (if any)<br><code>%n</code> for post id', 'boozurk' ),
							'req' => '',
							'sub'=>false
		),
		'boozurk_excerpt' => array(
							'group' => 'content',
							'type' => '',
							'default' => '',
							'description' => __( 'excerpt', 'boozurk' ),
							'info' => '',
							'req' => '',
							'sub'=>array('boozurk_excerpt_length','boozurk_excerpt_more_txt','boozurk_excerpt_more_link') 
		),
		'boozurk_excerpt_length' => array(
							'group' => 'content',
							'type' => 'int',
							'default' => 55,
							'description' => __( 'excerpt length', 'boozurk' ),
							'info' => '',
							'req' => '',
							'sub'=>false 
		),
		'boozurk_excerpt_more_txt' => array(
							'group' => 'content',
							'type' => 'txt',
							'default' => '[...]',
							'description' => __( '<em>excerpt more</em> string', 'boozurk' ),
							'info' => '',
							'req' => '',
							'sub'=>false 
		),
		'boozurk_excerpt_more_link' => array(
							'group' => 'content',
							'type' => 'chk',
							'default' => 0,
							'description' => __( '<em>excerpt more</em> linked', 'boozurk' ),
							'info' => __( 'use the <em>excerpt more</em> string as a link to the full post', 'boozurk' ),
							'req' => '',
							'sub'=>false 
		),
		'boozurk_more_tag' => array(
							'group' => 'content',
							'type' => 'txt',
							'default' => __( '(more...)', 'boozurk' ),
							'description' => __( '"more" tag string', 'boozurk' ),
							'info' => __( 'only plain text. use <code>%t</code> as placeholder for the post title', 'boozurk' ) . ' (<a href="http://codex.wordpress.org/Customizing_the_Read_More" target="_blank">Codex</a>)',
							'req' => ''
		),
		'boozurk_browse_links'=> array(
							'group'=>'content',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'quick browsing links', 'boozurk' ),
							'info'=>__( 'show navigation links before post content', 'boozurk' ),
							'req'=>'' 
		),
		'boozurk_post_info'=> array(
							'group' => 'content',
							'type' => '',
							'default' => '',
							'description' => __( 'Post details', 'boozurk' ),
							'info' => __( 'show post details in index view, right before the post content<br>in single post view you can use the <strong>Post details</strong> widget', 'boozurk' ),
							'req' => '',
							'sub' => array( 'boozurk_post_date', 'boozurk_post_cat', 'boozurk_post_tag' )
		),
		'boozurk_post_date'=> array(
							'group'=>'content',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'date', 'boozurk' ),
							'info'=>'',
							'req'=>'',
							'sub' => false
		),
		'boozurk_post_cat'=> array(
							'group'=>'content',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'categories', 'boozurk' ),
							'info'=>'',
							'req'=>'',
							'sub' => false
		),
		'boozurk_post_tag'=> array(
							'group'=>'content',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'tags', 'boozurk' ),
							'info'=>'',
							'req'=>'',
							'sub' => false
		),
		'boozurk_featured_title'=> array(
							'group'=>'content',
							'type'=>'sel',
							'default'=>'lists',
							'description'=>__( 'enhanced post title','boozurk' ),
							'info'=>__( 'use the featured image as background for the post title','boozurk' ),
							'options'=>array('lists','single','both','none'),
							'options_l10n'=>array(__('in lists','boozurk'),__('in single posts/pages','boozurk'),__('both','boozurk'),__('none','boozurk')),
							'req'=>'',
							'sub'=>array('boozurk_featured_title_thumb') 
		),
		'boozurk_featured_title_thumb'=> array(
							'group'=>'content',
							'type'=>'chk',
							'default'=>0,
							'description'=>__( 'thumbnail','boozurk' ),
							'info'=>'use small thumbnail instead of the full image',
							'req'=>'',
							'sub'=>false 
		),
		'boozurk_smilies' => array(
							'group' => 'content',
							'type' => 'chk',
							'default' => 1,
							'description' => __( 'custom smilies', 'boozurk' ),
							'info' => '(^_^) (ToT) (o_O) ...',
							'req' => ''
		),
		'boozurk_sidebar_primary'=> array(
							'group'=>'widgets',
							'type'=>'sel',
							'default'=>'scroll',
							'description'=>__( 'primary sidebar','boozurk' ),
							'info'=> '',
							'options'=>array('scroll','fixed','hidden'),
							'options_l10n'=>array(__('scroll with page','boozurk'),__('fixed','boozurk'),__('hidden','boozurk')),
							'req'=>'' 
		),
		'boozurk_sidebar_secondary'=> array(
							'group'=>'widgets',
							'type'=>'sel',
							'default'=>'fixed',
							'description'=>__( 'secondary sidebar','boozurk' ),
							'info'=> '',
							'options'=>array('scroll','fixed','hidden'),
							'options_l10n'=>array(__('scroll with page','boozurk'),__('fixed','boozurk'),__('hidden','boozurk')),
							'req'=>'' 
		),
		'boozurk_sidebar_head_split'=> array(
							'group'=>'widgets',
							'type'=>'sel',
							'default'=>'3',
							'description'=>__( 'split Header widget area','boozurk' ),
							'info'=>__( 'number of widget that can stay in the widget area side by side','boozurk' ),
							'options'=>array('1','2','3'),
							'options_l10n'=>array('1','2','3'),
							'req'=>'' 
		),
		'boozurk_sidebar_single_split'=> array(
							'group'=>'widgets',
							'type'=>'sel',
							'default'=>'1',
							'description'=>__( 'split Post widget area','boozurk' ),
							'info'=>__( 'number of widget that can stay in the widget area side by side','boozurk' ),
							'options'=>array('1','2','3'),
							'options_l10n'=>array('1','2','3'),
							'req'=>''
		),
		'boozurk_sidebar_foot_1_width'=> array(
							'group'=>'widgets',
							'type'=>'sel',
							'default'=>'33%',
							'description'=>__( 'footer widget area #1','boozurk' ),
							'info'=>__( 'width of the widget area','boozurk' ),
							'options'=>array('100%','50%','33%'),
							'options_l10n'=>array('100%','50%','33%'),
							'req'=>'' 
		),
		'boozurk_sidebar_foot_2_width'=> array(
							'group'=>'widgets',
							'type'=>'sel',
							'default'=>'33%',
							'description'=>__( 'footer widget area #2','boozurk' ),
							'info'=>__( 'width of the widget area','boozurk' ),
							'options'=>array('100%','50%','33%'),
							'options_l10n'=>array('100%','50%','33%'),
							'req'=>'' 
		),
		'boozurk_sidebar_foot_3_width'=> array(
							'group'=>'widgets',
							'type'=>'sel',
							'default'=>'33%',
							'description'=>__( 'footer widget area #3','boozurk' ),
							'info'=>__( 'width of the widget area','boozurk' ),
							'options'=>array('100%','50%','33%'),
							'options_l10n'=>array('100%','50%','33%'),
							'req'=>'' 
		),
		'boozurk_custom_widgets'=> array(
							'group'=>'widgets',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'custom widgets','boozurk' ),
							'info'=>__( 'add a lot of new usefull widgets','boozurk' ),
							'req'=>'' 
		),
		'boozurk_jsani'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'javascript features','boozurk' ),
							'info'=>__( 'try disable all javascript features if you encountered problems with javascript','boozurk' ),
							'req'=>'' 
		),
		'boozurk_js_basic'=> array(
							'group'=>'javascript',
							'type'=>'',
							'default'=>1,
							'description'=>__( 'basic animations','boozurk' ),
							'info'=>'',
							'req'=>'boozurk_jsani',
							'sub'=>array('boozurk_js_basic_menu','boozurk_js_basic_autoscroll','boozurk_js_basic_video_resize') 
		),
		'boozurk_js_basic_menu'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'main menu','boozurk' ),
							'info'=>__( 'fade in/out menu subitems','boozurk' ),
							'req'=>'boozurk_jsani',
							'sub'=>false 
		),
		'boozurk_js_basic_autoscroll'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'scroll','boozurk' ),
							'info'=>__( 'smooth scroll to top/bottom when click top/bottom buttons','boozurk' ),
							'req'=>'boozurk_jsani',
							'sub'=>false 
		),
		'boozurk_js_basic_video_resize'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'video resize','boozurk' ),
							'info'=>__( 'resize embeded video when window resizes','boozurk' ),
							'req'=>'boozurk_jsani',
							'sub'=>false 
		),
		'boozurk_js_thickbox'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'thickbox preview','boozurk' ),
							'info'=>__( 'add the thickbox effect to each linked image and galleries in post content','boozurk' ),
							'req'=>'boozurk_jsani',
							'sub'=>array('boozurk_js_thickbox_force') 
		),
		'boozurk_js_thickbox_force'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'replace links','boozurk' ),
							'info'=>__( 'force galleries to use links to image instead of links to attachment','boozurk' ),
							'req'=>'',
							'sub'=>false 
		),
		'boozurk_js_post_expander'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'post expander','boozurk' ),
							'info'=>__( 'expands a post to show the full content when the reader clicks the "Read more..." link','boozurk' ),
							'req'=>'boozurk_jsani'
		),
		'boozurk_js_tooltips'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'cool tooltips','boozurk' ),
							'info'=>__( 'replace titles of some links with cool tooltips','boozurk' ),
							'req'=>'boozurk_jsani' 
		),
		'boozurk_js_swfplayer'=> array( 
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'swf audio player','boozurk' ),
							'info'=>__( 'create an audio player for linked audio files (mp3,ogg and m4a) in the audio format posts','boozurk' ),
							'req'=>'boozurk_jsani' 
		),
		'boozurk_quotethis'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'quote link', 'boozurk' ),
							'info'=>__( 'show a link for easily add the selected text as a quote inside the comment form', 'boozurk' ),
							'req'=>'boozurk_jsani' 
		),
		'boozurk_infinite_scroll'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>0,
							'description'=>__( 'infinite pagination','boozurk' ),
							'info'=>__( 'automatically append the next page of posts (via AJAX) to your current page','boozurk' ),
							'req'=>'boozurk_jsani',
							'sub'=>array('boozurk_infinite_scroll_type') 
		),
		'boozurk_infinite_scroll_type'=> array(
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
		'boozurk_tinynav'=> array(
							'group'=>'javascript',
							'type'=>'chk',
							'default'=>1,
							'description'=>'<a href="https://github.com/viljamis/TinyNav.js">Tinynav</a>',
							'info'=>__( 'tiny navigation menu for small screen','boozurk' ),
							'req'=>'boozurk_jsani'
		),
		'boozurk_mobile_css'=> array(
							'group'=>'mobile',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'mobile support','boozurk' ),
							'info'=>__( 'use a dedicated style in mobile devices','boozurk' ),
							'req'=>'',
							'sub' => array('boozurk_mobile_css_color')
		),
		'boozurk_mobile_css_color'=> array(
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
		'boozurk_font_family'=> array(							'group'=>'other',							'type'=>'sel',							'default'=>'monospace',							'description'=>__( 'font family','boozurk' ),							'info'=>'',							'options'=>array('monospace','Arial, sans-serif','Helvetica, sans-serif','Comic Sans MS, cursive','Courier New, monospace','Georgia, serif','Lucida Console, Monaco, monospace','Lucida Sans Unicode, Lucida Grande, sans-serif','Palatino Linotype, Book Antiqua, Palatino, serif','Tahoma, Geneva, sans-serif','Times New Roman, Times, serif','Trebuchet MS, sans-serif','Verdana, Geneva, sans-serif'),							'options_l10n'=>array('monospace','Arial, sans-serif','Helvetica, sans-serif','Comic Sans MS, cursive','Courier New, monospace','Georgia, serif','Lucida Console, Monaco, monospace','Lucida Sans Unicode, Lucida Grande, sans-serif','Palatino Linotype, Book Antiqua, Palatino, serif','Tahoma, Geneva, sans-serif','Times New Roman, Times, serif','Trebuchet MS, sans-serif','Verdana, Geneva, sans-serif'),
							'req'=>'',
							'sub'=>array('boozurk_font_size') 
		),
		'boozurk_font_size'=> array(							'group'=>'other',							'type'=>'sel',							'default'=>'14px',							'description'=>__( 'font size','boozurk' ),							'info'=>'',							'options'=>array('10px','11px','12px','13px','14px','15px','16px'),							'options_l10n'=>array('10px','11px','12px','13px','14px','15px','16px'),
							'req'=>'',
							'sub'=>false 
		),
		'boozurk_google_font_family'=> array(
							'group' => 'other',
							'type' => 'txt',
							'default' => '',
							'description' => __( 'Google web font', 'boozurk' ),
							'info' => __( 'Copy and paste <a href="http://www.google.com/webfonts" target="_blank"><strong>Google web font</strong></a> name here. Example: <code>Architects Daughter</code>', 'boozurk' ),
							'req' => '',
							'sub' => array( 'boozurk_google_font_body', 'boozurk_google_font_post_title', 'boozurk_google_font_post_content' )
		),
		'boozurk_google_font_body' => array(
							'group' => 'other',
							'type' => 'chk',
							'default' => 0,
							'description' => __( 'for whole site', 'boozurk' ),
							'info' => '',
							'req' => '',
							'sub' => false
		),
		'boozurk_google_font_post_title' => array(
							'group' => 'other',
							'type' => 'chk',
							'default' => 1,
							'description' => __( 'for posts/pages title', 'boozurk' ),
							'info' => '',
							'req' => '',
							'sub' => false
		),
		'boozurk_google_font_post_content' => array(
							'group' => 'other',
							'type' => 'chk',
							'default' => 0,
							'description' => __( 'for posts/pages content', 'boozurk' ),
							'info' => '',
							'req' => '',
							'sub' => false
		),

		'boozurk_plusone'=> array(							'group'=>'other',							'type'=>'chk',							'default'=>1,							'description'=>'<a href="https://plus.google.com/" target="_blank">Google +1</a>',							'info'=>__( 'integrates the +1 feature for your contents', 'boozurk' ),							'req'=>'',
							'sub'=>array('boozurk_plusone_official')
		),
		'boozurk_plusone_official'=> array(
							'group'=>'other',
							'type'=>'chk',
							'default'=>0,
							'description'=>'use the official button',
							'info'=>'',
							'req'=>'',
							'sub'=>false
		),
		'boozurk_main_menu'=> array(							'group'=>'other',							'type'=>'sel',							'default'=>'text',							'description'=>__( 'main menu look','boozurk' ),							'info'=>__( 'select the style of the main menu: text, thumbnails or both','boozurk' ),							'options'=>array( 'text', 'thumbnail', 'thumbnail and text' ),							'options_l10n'=>array( __('text','boozurk'), __('thumbnail','boozurk'), __('thumbnail and text','boozurk') ),
							'req'=>'',
							'sub'=>array('boozurk_main_menu_icon_size') 
		),
		'boozurk_main_menu_icon_size'=> array(							'group'=>'other',							'type'=>'sel',							'default'=>'48',							'description'=>__( 'main menu icon size','boozurk' ),							'info'=>__( 'the dimension of the thumbnails in main menu (if "thumbnails" style is selected)','boozurk' ),							'options'=>array ('32', '48', '64', '96'),							'options_l10n'=>array ('32', '48', '64', '96'),
							'req'=>'',
							'sub'=>false 
		),
		'boozurk_logo'=> array(							'group'=>'other',							'type'=>'url',							'default'=>'',							'description'=>__( 'Logo','boozurk' ),							'info'=>__( 'a logo in the upper right corner of the window. paste here the complete path to image location. leave empty to ignore','boozurk' ),							'req'=>'',
							'sub'=>array('boozurk_logo_description') 
		),
		'boozurk_logo_description'=> array(
							'group'=>'other',
							'type'=>'chk',
							'default'=>1,
							'description'=>__( 'tagline','boozurk' ),
							'info'=>__( 'show site description below the logo','boozurk' ),
							'req'=>'',
							'sub'=>false 
		),
		'boozurk_editor_style'=> array(							'group'=>'other',							'type'=>'chk',							'default'=>1,							'description'=>__( 'editor style', 'boozurk' ),							'info'=>__( "add style to the editor in order to write the post exactly how it will appear on the site", 'boozurk' ),							'req'=>'' 
		),
		'boozurk_comment_style'=> array(
							'group'=>'other',
							'type'=>'chk',
							'default'=>0,
							'description'=>__( 'comment style', 'boozurk' ),
							'info'=>__( 'let the commenters to choose their comment background', 'boozurk' ),
							'req'=>'' 
		),
		'boozurk_custom_css' => array(
							'group' => 'other',
							'type' => 'txtarea',
							'default' => '',
							'description' => __( 'custom CSS code', 'boozurk' ),
							'info' => __( '<strong>For advanced users only</strong>: paste here your custom css code. it will be added after the defatult style', 'boozurk' ) . ' (<a href="'. get_stylesheet_uri() .'" target="_blank">style.css</a>)',
							'req' => ''
		),
		'boozurk_tbcred'=> array(							'group'=>'other',							'type'=>'chk',							'default'=>1,							'description'=>__( 'theme credits','boozurk' ),							'info'=>__( 'It is completely optional, but if you like the Theme we would appreciate it if you keep the credit link at the bottom','boozurk' ),							'req'=>'' 
		)
	);
	$boozurk_coa = apply_filters( 'boozurk_options_array', $boozurk_coa );

	if ( $option == 'groups' )
		return $boozurk_groups;
	elseif ( $option )
		return isset( $boozurk_coa[$option] ) ? $boozurk_coa[$option] : false;
	else
		return $boozurk_coa;
}


// retrive the required option. If the option ain't set, the default value is returned
if ( !function_exists( 'boozurk_get_opt' ) ) {
	function boozurk_get_opt( $opt ) {
		global $boozurk_opt;

		if ( isset( $boozurk_opt[$opt] ) ) return apply_filters( 'boozurk_option_override', $boozurk_opt[$opt], $opt );

		$defopt = boozurk_get_coa( $opt );
		
		if ( ! $defopt ) return null;

		if ( ( $defopt['req'] == '' ) || ( boozurk_get_opt( $defopt['req'] ) ) )
			return $defopt['default'];
		else
			return null;

	}
}
