<?php

// custom actions
add_action( 'admin_init', 'boozurk_default_options' ); // tell WordPress to run boozurk_default_options()
add_action( 'template_redirect', 'boozurk_allcat' ); // Add custom category page
add_action( 'template_redirect', 'boozurk_media' ); // media select
add_action( 'admin_head', 'boozurk_post_manage_style' ); // column-thumbnail style
add_action( 'manage_posts_custom_column', 'boozurk_addthumbvalue', 10, 2 ); // column-thumbnail for posts
add_action( 'manage_pages_custom_column', 'boozurk_addthumbvalue', 10, 2 ); // column-thumbnail for pages
// custom filters
add_filter( 'get_comment_author_link', 'boozurk_add_quoted_on' );
add_filter( 'user_contactmethods','boozurk_new_contactmethods',10,1 );
add_filter( 'manage_posts_columns', 'boozurk_addthumbcolumn' ); // column-thumbnail for posts
add_filter( 'manage_pages_columns', 'boozurk_addthumbcolumn' ); // column-thumbnail for pages
add_filter( 'the_title', 'boozurk_titles_filter', 10, 2 );
add_filter( 'excerpt_length', 'boozurk_excerpt_length' );
add_filter( 'excerpt_mblength' , 'boozurk_excerpt_length' ); //WP Multibyte Patch support
add_filter( 'excerpt_more', 'boozurk_excerpt_more' );
add_filter( 'the_content_more_link', 'boozurk_more_link', 10, 2 );
add_filter( 'wp_title', 'boozurk_filter_wp_title' );


// get theme version
if ( function_exists( 'wp_get_theme' ) ) {
	$boozurk_theme = wp_get_theme( 'boozurk' );
	$boozurk_current_theme = wp_get_theme();
} else { // Compatibility with versions of WordPress prior to 3.4.
	$boozurk_theme = get_theme( 'Boozurk' );
	$boozurk_current_theme = get_current_theme();
}
$boozurk_version = $boozurk_theme? $boozurk_theme['Version'] : '';

// check if in preview mode or not
$boozurk_is_printpreview = false;
if ( isset( $_GET['style'] ) && md5( $_GET['style'] ) == '8e77921d24c6f82c4bd783895e9d9cf1' ) {
	$boozurk_is_printpreview = true;
}

// check if in allcat view
$boozurk_is_allcat_page = false;
if( isset( $_GET['allcat'] ) && ( md5( $_GET['allcat'] ) == '415290769594460e2e485922904f345d' ) ) {
	$boozurk_is_allcat_page = true;
}

// show all categories list (redirect to allcat.php if allcat=y)
if ( !function_exists( 'boozurk_allcat' ) ) {
	function boozurk_allcat () {
		global $boozurk_is_allcat_page;
		if( $boozurk_is_allcat_page ) {
			get_template_part( 'allcat' );
			exit;
		}
	}
}

// check and set default options 
function boozurk_default_options() {
		global $boozurk_version;
		$the_coa = boozurk_get_coa();
		$the_opt = get_option( 'boozurk_options' );

		// if options are empty, sets the default values
		if ( empty( $the_opt ) || !isset( $the_opt ) ) {
			foreach ( $the_coa as $key => $val ) {
				$the_opt[$key] = $the_coa[$key]['default'];
			}
			$the_opt['version'] = ''; //null value to keep admin notice alive and invite user to discover theme options
			update_option( 'boozurk_options' , $the_opt );
		} else if ( !isset( $the_opt['version'] ) || $the_opt['version'] < $boozurk_version ) {
			// check for unset values and set them to default value -> when updated to new version
			foreach ( $the_coa as $key => $val ) {
				if ( !isset( $the_opt[$key] ) ) $the_opt[$key] = $the_coa[$key]['default'];
			}
			$the_opt['version'] = ''; //null value to keep admin notice alive and invite user to discover theme options
			update_option( 'boozurk_options' , $the_opt );
		}
}

// print a reminder message for set the options after the theme is installed or updated
if ( !function_exists( 'boozurk_setopt_admin_notice' ) ) {
	function boozurk_setopt_admin_notice() {
		echo '<div class="updated"><p><strong>' . sprintf( __( "%s theme says: \"Dont forget to set <a href=\"%s\">my options</a>!\"", 'boozurk' ), 'Boozurk', get_admin_url() . 'themes.php?page=boozurk_functions' ) . '</strong></p></div>';
	}
}
if ( current_user_can( 'manage_options' ) && ( $boozurk_opt['version'] < $boozurk_version ) ) {
	add_action( 'admin_notices', 'boozurk_setopt_admin_notice' );
}

//Image EXIF details
if ( !function_exists( 'boozurk_exif_details' ) ) {
	function boozurk_exif_details(){
		global $post; ?>
		<div class="exif-attachment-info">
			<?php
			$imgmeta = wp_get_attachment_metadata();

			// Convert the shutter speed retrieve from database to fraction
			if ( $imgmeta['image_meta']['shutter_speed'] && (1 / $imgmeta['image_meta']['shutter_speed']) > 1) {
				if ((number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1)) == 1.3
				or number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 1.5
				or number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 1.6
				or number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 2.5){
					$pshutter = "1/" . number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1, '.', '');
				} else {
					$pshutter = "1/" . number_format((1 / $imgmeta['image_meta']['shutter_speed']), 0, '.', '');
				}
			} else {
				$pshutter = $imgmeta['image_meta']['shutter_speed'];
			}

			// Start to display EXIF and IPTC data of digital photograph
			echo __("Width", "boozurk" ) . ": " . $imgmeta['width']."px<br />";
			echo __("Height", "boozurk" ) . ": " . $imgmeta['height']."px<br />";
			if ( $imgmeta['image_meta']['created_timestamp'] ) echo __("Date Taken", "boozurk" ) . ": " . date("d-M-Y H:i:s", $imgmeta['image_meta']['created_timestamp'])."<br />";
			if ( $imgmeta['image_meta']['copyright'] ) echo __("Copyright", "boozurk" ) . ": " . $imgmeta['image_meta']['copyright']."<br />";
			if ( $imgmeta['image_meta']['credit'] ) echo __("Credit", "boozurk" ) . ": " . $imgmeta['image_meta']['credit']."<br />";
			if ( $imgmeta['image_meta']['title'] ) echo __("Title", "boozurk" ) . ": " . $imgmeta['image_meta']['title']."<br />";
			if ( $imgmeta['image_meta']['caption'] ) echo __("Caption", "boozurk" ) . ": " . $imgmeta['image_meta']['caption']."<br />";
			if ( $imgmeta['image_meta']['camera'] ) echo __("Camera", "boozurk" ) . ": " . $imgmeta['image_meta']['camera']."<br />";
			if ( $imgmeta['image_meta']['focal_length'] ) echo __("Focal Length", "boozurk" ) . ": " . $imgmeta['image_meta']['focal_length']."mm<br />";
			if ( $imgmeta['image_meta']['aperture'] ) echo __("Aperture", "boozurk" ) . ": f/" . $imgmeta['image_meta']['aperture']."<br />";
			if ( $imgmeta['image_meta']['iso'] ) echo __("ISO", "boozurk" ) . ": " . $imgmeta['image_meta']['iso']."<br />";
			if ( $pshutter ) echo __("Shutter Speed", "boozurk" ) . ": " . sprintf( '%s seconds', $pshutter) . "<br />"
			?>
		</div>
		<?php
	}
}

//Display navigation to next/previous post when applicable
if ( !function_exists( 'boozurk_single_nav' ) ) {
	function boozurk_single_nav() {
		global $post, $boozurk_opt;
		if ( $boozurk_opt['boozurk_browse_links'] == 0 ) return;
		$next = get_previous_post();
		$prev = get_next_post();
		$next_title = get_the_title( $next ) ? get_the_title( $next ) : __( 'Previous Post', 'boozurk' );
		$prev_title = get_the_title( $prev ) ? get_the_title( $prev ) : __( 'Next Post', 'boozurk' );
	?>
		<div class="nav-single fixfloat">
			<?php if ( $prev ) { ?>
				<span class="nav-previous"><a rel="prev" href="<?php echo get_permalink( $prev ); ?>" title="<?php echo esc_attr(strip_tags( __( 'Next Post', 'boozurk' ) . ': ' . $prev_title ) ); ?>"><?php echo $prev_title; ?><?php echo boozurk_get_the_thumb( $prev->ID, 32, 32, 'tb-thumb-format' ); ?></a></span>
			<?php } ?>
			<?php if ( $next ) { ?>
				<span class="nav-next"><a rel="next" href="<?php echo get_permalink( $next ); ?>" title="<?php echo esc_attr(strip_tags( __( 'Previous Post', 'boozurk' ) . ': ' . $next_title ) ); ?>"><?php echo boozurk_get_the_thumb( $next->ID, 32, 32, 'tb-thumb-format' ); ?><?php echo $next_title; ?></a></span>
			<?php } ?>
		</div><!-- #nav-single -->
	<?php
	}
}

// print extra info for posts/pages
if ( !function_exists( 'boozurk_post_details' ) ) {
	function boozurk_post_details( $args = '' ) {
		global $post;

		$defaults = array( 'author' => 1, 'date' => 1, 'tags' => 1, 'categories' => 1, 'avatar_size' => 48, 'featured' => 0 );
		$args = wp_parse_args( $args, $defaults );

		?>
			<?php if ( $args['featured'] &&  has_post_thumbnail( $post->ID ) ) { echo '<div class="bz-post-details-thumb">' . get_the_post_thumbnail( $post->ID, 'thumbnail') . '</div>'; } ?>
			<?php if ( $args['author'] ) {
				$author = $post->post_author;
				
				$name = get_the_author_meta('nickname', $author);
				$alt_name = get_the_author_meta('user_nicename', $author);
				$avatar = get_avatar($author, $args['avatar_size'], 'Gravatar Logo', $alt_name.'-photo');
				$description = get_the_author_meta('description', $author);
				$author_link = get_author_posts_url($author);

				?>
				<div class="tbm-author-bio vcard">
					<ul>
						<li class="author-avatar"><?php echo $avatar; ?></li>
						<li class="author-name"><a class="fn" href="<?php echo $author_link; ?>" ><?php echo $name; ?></a></li>
						<li class="author-description note"><?php echo $description; ?> </li>
						<li class="author-social">
							<?php if ( get_the_author_meta('twitter', $author) ) echo '<a target="_blank" class="url" title="' . esc_attr( sprintf( __('follow %s on Twitter', 'boozurk'), $name ) ) . '" href="'.get_the_author_meta('twitter', $author).'"><img alt="twitter" class="avatar" width="24" height="24" src="' . get_template_directory_uri() . '/images/follow/twitter.png" /></a>'; ?>
							<?php if ( get_the_author_meta('facebook', $author) ) echo '<a target="_blank" class="url" title="' . esc_attr( sprintf( __('follow %s on Facebook', 'boozurk'), $name ) ) . '" href="'.get_the_author_meta('facebook', $author).'"><img alt="facebook" class="avatar" width="24" height="24" src="' . get_template_directory_uri() . '/images/follow/facebook.png" /></a>'; ?>
						</li>
					</ul>
				</div>
			<?php } ?>
			<?php if ( $args['categories'] ) { echo '<span class="bz-post-details-cats">' . __( 'Categories', 'boozurk' ) . ': ' . '</span>'; the_category( ', ' ); echo '<br/>'; } ?>
			<?php if ( $args['tags'] ) { echo '<span class="bz-post-details-tags">' . __( 'Tags', 'boozurk' ) . ': ' . '</span>'; if ( !get_the_tags() ) { _e( 'No Tags', 'boozurk' ); } else { the_tags('', ', ', ''); } echo '<br/>'; } ?>
			<?php if ( $args['date'] ) { echo '<span class="bz-post-details-date">' . __( 'Published on', 'boozurk' ) . ': ' . '</span>'; echo '<b><a href="' . get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d')) . '">' . get_the_time( get_option( 'date_format' ) ) . '</a></b>'; } ?>
		<?php
	}
}

//add share links to post/page
if ( !function_exists( 'boozurk_share_this' ) ) {
	function boozurk_share_this( $args = array() ){
		global $post;
		
		$defaults = array( 'size' => 24, 'echo' => true );
		$args = wp_parse_args( $args, $defaults );
		
		$share = array(
			//'ID' => array( 'NAME', 'LINK' ),
			// LINK -> %1$s: title, %2$s: url, %3$s: image/thumbnail
			'twitter' => array( 'Twitter', 'http://twitter.com/home?status=%1$s - %2$s' ),
			'facebook' => array( 'Facebook', 'http://www.facebook.com/sharer.php?u=%2$s&t=%1$s' ),
			'sina' => array( 'Weibo', 'http://v.t.sina.com.cn/share/share.php?url=%2$s' ),
			'tencent' => array( 'Tencent', 'http://v.t.qq.com/share/share.php?url=%2$s&title=%1$s&pic=%3$s' ),
			'qzone' => array( 'Qzone', 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=%2$s' ),
			'reddit' => array( 'Reddit', 'http://reddit.com/submit?url=%2$s&title=%1$s' ),
			'stumbleupon' => array( 'StumbleUpon', 'http://www.stumbleupon.com/submit?url=%2$s&title=%1$s' ),
			'digg' => array( 'Digg', 'http://digg.com/submit?url=%2$s' ),
			'bookmarks' => array( 'Bookmarks', 'https://www.google.com/bookmarks/mark?op=edit&bkmk=%2$s&title=%1$s' ),
			'blogger' => array( 'Blogger', 'http://www.blogger.com/blog_this.pyra?t&u=%2$s&n=%1$s&pli=1' ),
			'delicious' => array( 'Delicious', 'http://delicious.com/save?v=5&noui&jump=close&url=%2$s&title=%1$s' ),
		);

		$pName = rawurlencode($post->post_title);
		$pHref = rawurlencode(get_permalink($post->ID));
		$pPict = rawurlencode(wp_get_attachment_url(get_post_thumbnail_id($post->ID)));


		$outer = '<div class="bz-article-share fixfloat">';
		foreach( $share as $key => $service ){
			$href = sprintf( $service[1], $pName, $pHref, $pPict );
			$outer .= '<a class="bz-share-item" rel="nofollow" target="_blank" id="bz-' . $key . '" href="' . $href . '"><img src="' . get_template_directory_uri() . '/images/follow/' . $key . '.png" width="' . $args['size'] . '" height="' . $args['size'] . '" alt="' . $service[0] . ' Button"  title="' . esc_attr( sprintf( __( 'Share with %s','boozurk' ), $service[0] ) ) . '" /></a>';
		}

		$outer .= '</div>';
		if ( $args['echo'] ) echo $outer; else return $outer;
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

if ( !function_exists( 'boozurk_theme_admin_scripts' ) ) {
	function boozurk_theme_admin_scripts() {
		global $boozurk_version;
		wp_enqueue_script( 'boozurk-options-script', get_template_directory_uri().'/js/options.dev.js',array('jquery','farbtastic','thickbox'),$boozurk_version, true ); //thebird js
		$data = array(
			'confirm_to_defaults' => __( 'Are you really sure you want to set all the options to their default values?', 'boozurk' )
		);
		wp_localize_script( 'boozurk-options-script', 'bz_l10n', $data );
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

// the custon header style - called only on your theme options page
if ( !function_exists( 'boozurk_theme_admin_styles' ) ) {
	function boozurk_theme_admin_styles() {
		wp_enqueue_style( 'bz-options-style', get_template_directory_uri() . '/css/options.css', array('farbtastic','thickbox'), '', 'screen' );
	}
}

// sanitize options value
if ( !function_exists( 'boozurk_sanitize_options' ) ) {
	function boozurk_sanitize_options($input) {
		global $boozurk_version;

		$the_coa = boozurk_get_coa();

		foreach ( $the_coa as $key => $val ) {
	
			if( $the_coa[$key]['type'] == 'chk' ) {								//CHK
				if( !isset( $input[$key] ) ) {
					$input[$key] = 0;
				} else {
					$input[$key] = ( $input[$key] == 1 ? 1 : 0 );
				}

			} elseif( $the_coa[$key]['type'] == 'sel' ) {						//SEL
				if ( !in_array( $input[$key], $the_coa[$key]['options'] ) )
					$input[$key] = $the_coa[$key]['default'];

			} elseif( $the_coa[$key]['type'] == 'opt' ) {						//OPT
				if ( !in_array( $input[$key], $the_coa[$key]['options'] ) )
					$input[$key] = $the_coa[$key]['default'];

			} elseif( $the_coa[$key]['type'] == 'col' ) {						//COL
				$color = str_replace( '#' , '' , $input[$key] );
				$color = preg_replace( '/[^0-9a-fA-F]/' , '' , $color );
				$input[$key] = '#' . $color;

			} elseif( $the_coa[$key]['type'] == 'url' ) {						//URL
				$input[$key] = esc_url( trim( strip_tags( $input[$key] ) ) );

			} elseif( $the_coa[$key]['type'] == 'txt' ) {						//TXT
				if( !isset( $input[$key] ) ) {
					$input[$key] = '';
				} else {
					$input[$key] = trim( strip_tags( $input[$key] ) );
				}

			} elseif( $the_coa[$key]['type'] == 'int' ) {						//INT
				if( !isset( $input[$key] ) ) {
					$input[$key] = $the_coa[$key]['default'];
				} else {
					$input[$key] = (int) $input[$key] ;
				}

			} elseif( $the_coa[$key]['type'] == 'txtarea' ) {					//TXTAREA
				if( !isset( $input[$key] ) ) {
					$input[$key] = '';
				} else {
					$input[$key] = trim( strip_tags( $input[$key] ) );
				}
			}
		}

		foreach ( $input['boozurk_cat_colors'] as $key => $val ) {				//CATCOL
			$color = str_replace( '#' , '' , $input['boozurk_cat_colors'][$key] );
			$color = preg_replace( '/[^0-9a-fA-F]/' , '' , $color );
			$input['boozurk_cat_colors'][$key] = '#' . $color;
		}

		// check for required options
		foreach ( $the_coa as $key => $val ) {
			if ( $the_coa[$key]['req'] != '' ) { if ( $input[$the_coa[$key]['req']] == ( 0 || '') ) $input[$key] = 0; }
		}

		$input['version'] = $boozurk_version; // keep version number
		return $input;
	}
}

// the theme option page
if ( !function_exists( 'boozurk_edit_options' ) ) {
	function boozurk_edit_options() {

		if ( !current_user_can( 'edit_theme_options' ) ) wp_die( 'You do not have sufficient permissions to access this page.' );

		global $boozurk_opt, $boozurk_current_theme, $boozurk_version;

		$the_coa = boozurk_get_coa();
		$the_groups = boozurk_get_coa( 'groups' );
		$the_option_name = 'boozurk_options';

		if ( isset( $_GET['erase'] ) ) {
			$_SERVER['REQUEST_URI'] = remove_query_arg( 'erase', $_SERVER['REQUEST_URI'] );
			delete_option( $the_option_name );
			boozurk_default_options();
			$boozurk_opt = get_option( $the_option_name );
		}

		// update version value when admin visit options page
		if ( $boozurk_opt['version'] < $boozurk_version ) {
			$boozurk_opt['version'] = $boozurk_version;
			update_option( $the_option_name , $boozurk_opt );
		}

		$the_opt = $boozurk_opt;

		// options have been updated
		if ( isset( $_REQUEST['settings-updated'] ) ) {
			//return options save message
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'Options saved.','boozurk' ) . '</strong></p></div>';
		}

		// options to defaults done
		if ( isset( $_GET['erase'] ) ) {
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'Defaults values loaded.', 'boozurk' ) . '</strong></p></div>';
		}

	?>
		<div class="wrap" id="main-wrap">
			<div class="icon32" id="theme-icon"><br></div>
			<h2><?php echo $boozurk_current_theme . ' - ' . __( 'Theme Options','boozurk' ); ?></h2>
			<ul id="tabselector" class="hide-if-no-js">
<?php
				foreach( $the_groups as $key => $name ) {
?>
				<li id="selgroup-<?php echo $key; ?>"><a href="#" onClick="boozurkOptions.switchTab('<?php echo $key; ?>'); return false;"><?php echo $name; ?></a></li>
<?php 
				}
?>
				<li id="selgroup-info"><a href="#" onClick="boozurkOptions.switchTab('info'); return false;"><?php _e( 'Theme Info' , 'boozurk' ); ?></a></li>
			</ul>
			<ul id="selector" class="hide-if-js">
				<li id="theme-options-li"><a href="#theme-options"><?php _e( 'Options','boozurk' ); ?></a></li>
				<li id="theme-infos-li"><a href="#theme-infos"><?php _e( 'Theme Info','boozurk' ); ?></a></li>
			</ul>
			<div id="tabs-container">
				<div class="clear"></div>
				<div id="theme-options">
					<h2 class="hide-if-js" style="text-align: center;"><?php _e( 'Options','boozurk' ); ?></h2>
					<form method="post" action="options.php">
						<?php settings_fields( 'boozurk_settings_group' ); ?>
						<div id="stylediv">
							<?php foreach ($the_coa as $key => $val) { ?>
								<?php if ( isset( $the_coa[$key]['sub'] ) && !$the_coa[$key]['sub'] ) continue; ?>
								<div class="tab-opt tabgroup-<?php echo $the_coa[$key]['group']; ?>">
									<span class="column-nam"><?php echo $the_coa[$key]['description']; ?></span>
								<?php if ( !isset ( $the_opt[$key] ) ) $the_opt[$key] = $the_coa[$key]['default']; ?>
								<?php if ( $the_coa[$key]['type'] == 'chk' ) { ?>
										<input name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]" value="1" type="checkbox" class="ww_opt_p_checkbox" <?php checked( 1 , $the_opt[$key] ); ?> />
										<?php if ( $the_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $the_coa[$key]['info']; ?></div><?php } ?>
								<?php } elseif ( $the_coa[$key]['type'] == 'sel' ) { ?>
										<select name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]">
										<?php foreach($the_coa[$key]['options'] as $optionkey => $option) { ?>
											<option value="<?php echo $option; ?>" <?php selected( $the_opt[$key], $option ); ?>><?php echo $the_coa[$key]['options_l10n'][$optionkey]; ?></option>
										<?php } ?>
										</select>
										<?php if ( $the_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $the_coa[$key]['info']; ?></div><?php } ?>
								<?php } elseif ( $the_coa[$key]['type'] == 'opt' ) { ?>
									<?php foreach( $the_coa[$key]['options'] as $optionkey => $option ) { ?>
										<label title="<?php echo esc_attr($option); ?>"><input type="radio" <?php checked( $the_opt[$key], $option ); ?> value="<?php echo $option; ?>" name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]"> <span><?php echo $the_coa[$key]['options_readable'][$optionkey]; ?></span></label>
									<?php } ?>
								<?php } elseif ( $the_coa[$key]['type'] == 'url' ) { ?>
										<input class="boozurk_text" id="option_field_<?php echo $key; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]" value="<?php echo $the_opt[$key]; ?>" />
										<?php if ( $key == 'boozurk_logo' ) {
											$boozurk_arr_params['tb_media'] = '1'; 
											$boozurk_arr_params['_wpnonce'] = wp_create_nonce( 'logo-nonce' );
											?>
											<input class="hide-if-no-js button" type="button" value="<?php echo __( 'Select', 'boozurk' ); ?>" onClick="tb_show( '<?php echo __( 'Click an image to select', 'boozurk' ); ?>', '<?php echo add_query_arg( $boozurk_arr_params, home_url() ); ?>&amp;TB_iframe=true'); return false;" />
										<?php } ?>
										<?php if ( $the_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $the_coa[$key]['info']; ?></div><?php } ?>
								<?php } elseif ( $the_coa[$key]['type'] == 'txt' ) { ?>
										<input class="boozurk_text" id="option_field_<?php echo $key; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]" value="<?php echo $the_opt[$key]; ?>" />
										<?php if ( $the_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $the_coa[$key]['info']; ?></div><?php } ?>
								<?php } elseif ( $the_coa[$key]['type'] == 'int' ) { ?>
										<input class="boozurk_text" id="option_field_<?php echo $key; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]" value="<?php echo $the_opt[$key]; ?>" />
										<?php if ( $the_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $the_coa[$key]['info']; ?></div><?php } ?>
								<?php } elseif ( $the_coa[$key]['type'] == 'txtarea' ) { ?>
										<textarea name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]"><?php echo $the_opt[$key]; ?></textarea>
										<?php if ( $the_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $the_coa[$key]['info']; ?></div><?php } ?>
								<?php }	?>
								<?php if ( isset( $the_coa[$key]['sub'] ) ) { ?>
										<div class="sub-opt-wrap">
									<?php foreach ($the_coa[$key]['sub'] as $subkey => $subval) { ?>
										<?php if ( $subval == '' ) { echo '<br />'; continue;} ?>
											<div class="sub-opt">
											<?php if ( !isset ($the_opt[$subval]) ) $the_opt[$subval] = $the_coa[$subval]['default']; ?>
												<?php if ( $the_coa[$subval]['description'] != '' ) { ?><span><?php echo $the_coa[$subval]['description']; ?> : </span><?php } ?>
											<?php if ( $the_coa[$subval]['type'] == 'chk' ) { ?>
													<input name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]" value="1" type="checkbox" class="ww_opt_p_checkbox" <?php checked( 1 , $the_opt[$subval] ); ?> />
													<span><?php echo $the_coa[$subval]['info']; ?></span>
											<?php } elseif ( $the_coa[$subval]['type'] == 'sel' ) { ?>
													<select name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]">
													<?php foreach($the_coa[$subval]['options'] as $optionkey => $option) { ?>
														<option value="<?php echo $option; ?>" <?php selected( $the_opt[$subval], $option ); ?>><?php echo $the_coa[$subval]['options_l10n'][$optionkey]; ?></option>
													<?php } ?>
													</select>
													<span><?php echo $the_coa[$subval]['info']; ?></span>
											<?php } elseif ( $the_coa[$subval]['type'] == 'opt' ) { ?>
												<?php foreach( $the_coa[$subval]['options'] as $optionkey => $option ) { ?>
													<label title="<?php echo esc_attr($option); ?>"><input type="radio" <?php checked( $the_opt[$subval], $option ); ?> value="<?php echo $option; ?>" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]"> <span><?php echo $the_coa[$subval]['options_readable'][$optionkey]; ?></span></label>
												<?php } ?>
											<?php } elseif ( $the_coa[$subval]['type'] == 'url' ) { ?>
													<input class="boozurk_text" id="option_field_<?php echo $subval; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]" value="<?php echo $the_opt[$subval]; ?>" />
													<span><?php echo $the_coa[$subval]['info']; ?></span>
											<?php } elseif ( $the_coa[$subval]['type'] == 'txt' ) { ?>
													<input class="boozurk_text" id="option_field_<?php echo $subval; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]" value="<?php echo $the_opt[$subval]; ?>" />
													<span><?php echo $the_coa[$subval]['info']; ?></span>
											<?php } elseif ( $the_coa[$subval]['type'] == 'int' ) { ?>
													<input class="boozurk_text" id="option_field_<?php echo $subval; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]" value="<?php echo $the_opt[$subval]; ?>" />
													<span><?php echo $the_coa[$subval]['info']; ?></span>
											<?php } elseif ( $the_coa[$subval]['type'] == 'col' ) { ?>
													<div class="col-tools">
														<span><?php echo $the_coa[$subval]['info']; ?></span>
														<input onclick="boozurkOptions.showColorPicker('<?php echo $subval; ?>');" style="background-color:<?php echo $the_opt[$subval]; ?>;" class="color_preview_box" type="text" id="boozurk_box_<?php echo $subval; ?>" value="" readonly="readonly" />
														<div class="boozurk_cp" id="boozurk_colorpicker_<?php echo $subval; ?>"></div>
														<input class="boozurk_input" id="boozurk_input_<?php echo $subval; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]" value="<?php echo $the_opt[$subval]; ?>" />
														<br />
														<a class="hide-if-no-js" href="#" onclick="boozurkOptions.showColorPicker('<?php echo $subval; ?>'); return false;"><?php _e( 'Select a Color' , 'boozurk' ); ?></a>
														<br />
														<a class="hide-if-no-js" style="color:<?php echo $the_coa[$subval]['default']; ?>;" href="#" onclick="boozurkOptions.updateColor('<?php echo $subval; ?>','<?php echo $the_coa[$subval]['default']; ?>'); return false;"><?php _e( 'Default' , 'boozurk' ); ?></a>
														<br class="clear" />
													</div>
											<?php } elseif ( $the_coa[$subval]['type'] == 'catcol' ) { ?>
													<?php
														$args=array(
															'orderby' => 'name',
															'order' => 'ASC'
														);
														$categories=get_categories($args);
														foreach($categories as $category) {
															$hexnumber = '#';
															for ($i2=1; $i2<=3; $i2++) {
																$hexnumber .= dechex( rand(64,255) );
															}
															$catcolor = isset($the_opt[$subval][$category->term_id]) ? $the_opt[$subval][$category->term_id] : $hexnumber;
													?>
														<div class="col-tools">
															<span><?php echo $category->name; ?></span>
															<input onclick="boozurkOptions.showColorPicker('<?php echo $subval.'-'.$category->term_id; ?>');" style="background-color:<?php echo $catcolor; ?>;" class="color_preview_box" type="text" id="boozurk_box_<?php echo $subval.'-'.$category->term_id; ?>" value="" readonly="readonly" />
															<div class="boozurk_cp" id="boozurk_colorpicker_<?php echo $subval.'-'.$category->term_id; ?>"></div>
															<input class="boozurk_input" id="boozurk_input_<?php echo $subval.'-'.$category->term_id; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>][<?php echo $category->term_id; ?>]" value="<?php echo $catcolor; ?>" />
															<br />
															<a class="hide-if-no-js" href="#" onclick="boozurkOptions.showColorPicker('<?php echo $subval.'-'.$category->term_id; ?>'); return false;"><?php _e( 'Select a Color' , 'boozurk' ); ?></a>
															<br />
															<a class="hide-if-no-js" style="color:<?php echo $the_coa[$subval]['defaultcolor']; ?>;" href="#" onclick="boozurkOptions.updateColor('<?php echo $subval.'-'.$category->term_id; ?>','<?php echo $the_coa[$subval]['defaultcolor']; ?>'); return false;"><?php _e( 'Default' , 'boozurk' ); ?></a>
															<br class="clear" />
															<?php if ( $category->description ) { ?><div class="column-des"><?php echo $category->description; ?></div><?php } ?>
														</div>
													<?php }	?>
													
											<?php }	?>
												</div>
										<?php }	?>
											<br class="clear" />
										</div>
								<?php }	?>
									<?php if ( $the_coa[$key]['req'] != '' ) { ?><div class="column-req"><?php echo '<u>' . __('requires','boozurk') . '</u>: ' . $the_coa[$the_coa[$key]['req']]['description']; ?></div><?php } ?>
								</div>
							<?php }	?>
						</div>
						<p id="buttons">
							<input type="hidden" name="<?php echo $the_option_name; ?>[hidden_opt]" value="default" />
							<input class="button-primary" type="submit" name="Submit" value="<?php _e( 'Update Options' , 'boozurk' ); ?>" />
							<a href="themes.php?page=boozurk_functions" target="_self"><?php _e( 'Undo Changes' , 'boozurk' ); ?></a>
							|
							<a id="to-defaults" href="themes.php?page=boozurk_functions&erase=1" target="_self"><?php _e( 'Back to defaults' , 'boozurk' ); ?></a>
						</p>
					</form>
					<p class="stylediv" style="clear: both; text-align: center; border: 1px solid #ccc;">
						<small>
							<?php _e( 'If you like/dislike this theme, or if you encounter any issues using it, please let us know it.', 'boozurk' ); ?><br />
							<a href="<?php echo esc_url( 'http://www.twobeers.net/annunci/tema-per-wordpress-boozurk' ); ?>" title="boozurk theme" target="_blank"><?php _e( 'Leave a feedback', 'boozurk' ); ?></a>
						</small>
					</p>
					<p class="stylediv" style="clear: both; text-align: center; border: 1px solid #ccc; margin-top: 10px;">
						<small>Support the theme in your language, provide a <a href="<?php echo esc_url( 'http://www.twobeers.net/wp-themes/themes-translations-wordpress' ); ?>" title="Themes translation" target="_blank">translation</a>.</small>
					</p>
				</div>
				<div id="theme-infos">
					<h2 class="hide-if-js" style="text-align: center;"><?php _e( 'Theme Info', 'boozurk' ); ?></h2>
					<?php locate_template( 'readme.html',true ); ?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	<?php
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

// strip tags and apply title format for blank titles
function boozurk_titles_filter( $title, $id = null ) {
	global $boozurk_opt;

	if ( is_admin() ) return $title;

	$title = strip_tags( $title, '<abbr><acronym><b><em><i><del><ins><bdo><strong>' );

	if ( $id == null ) return $title;

	if ( !$boozurk_opt['boozurk_blank_title'] ) return $title;

	if ( empty( $title ) ) {
		if ( !isset( $boozurk_opt['boozurk_blank_title_text'] ) || empty( $boozurk_opt['boozurk_blank_title_text'] ) ) return __( '(no title)', 'boozurk' );
		$postdata = array( get_post_format( $id )? get_post_format_string( get_post_format( $id ) ): __( 'Post', 'boozurk' ), get_the_time( get_option( 'date_format' ), $id ), $id );
		$codes = array( '%f', '%d', '%n' );
		return str_replace( $codes, $postdata, $boozurk_opt['boozurk_blank_title_text'] );
	} else
		return $title;
}

//set the excerpt length
if ( !function_exists( 'boozurk_excerpt_length' ) ) {
	function boozurk_excerpt_length( $length ) {
		global $boozurk_opt;
		return (int) $boozurk_opt['boozurk_excerpt_length'];
	}
}

// use the "excerpt more" string as a link to the post
function boozurk_excerpt_more( $more ) {
	global $boozurk_opt, $post;
	if ( is_admin() ) return $more;
	if ( isset( $boozurk_opt['boozurk_excerpt_more_txt'] ) && isset( $boozurk_opt['boozurk_excerpt_more_link'] ) ) {
		if ( $boozurk_opt['boozurk_excerpt_more_link'] ) {
			return '<a href="' . get_permalink() . '">' . $boozurk_opt['boozurk_excerpt_more_txt'] . '</a>';
		} else {
			return $boozurk_opt['boozurk_excerpt_more_txt'];
		}
	}
	return $more;
}

// custom text for the "more" tag
function boozurk_more_link( $more_link, $more_link_text ) {
	global $boozurk_opt;
	
	if ( isset( $boozurk_opt['boozurk_more_tag'] ) && !is_admin() ) {
		$text = str_replace ( '%t', get_the_title(), $boozurk_opt['boozurk_more_tag'] );
		return str_replace( $more_link_text, $text, $more_link );
	}
	return $more_link;
}

//Add new contact methods to author panel
if ( !function_exists( 'boozurk_new_contactmethods' ) ) {
	function boozurk_new_contactmethods( $contactmethods ) {
		//add Twitter
		$contactmethods['twitter'] = 'Twitter';
		//add Facebook
		$contactmethods['facebook'] = 'Facebook';

		return $contactmethods;
	}
}

// Add Thumbnail Column in Manage Posts/Pages List
function boozurk_addthumbcolumn($cols) {
	$cols['thumbnail'] = ucwords( __('thumbnail', 'boozurk') );
	return $cols;
}

// Add Thumbnails in Manage Posts/Pages List
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

// Add Thumbnail Column style in Manage Posts/Pages List
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

// check if in media preview mode
$boozurk_is_media = false;
if ( isset( $_GET['tb_media'] ) ) {
	$boozurk_is_media = true;
}

// media preview
if ( !function_exists( 'boozurk_media' ) ) {
	function boozurk_media () {
		global $boozurk_is_media;
		if ( $boozurk_is_media ) {
			get_template_part( 'lib/media' ); 
			exit;
		}
	}
}