<?php
/**
 * functions-formats.php
 *
 * Contains some functions for handling the post formats.
 *
 * @package Boozurk
 * @since 1.00
 */


/* Custom filters - WP hooks */

add_filter( 'the_content'							, 'boozurk_quote_content' );


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


// run the gallery preview
if ( !function_exists( 'boozurk_gallery_preview' ) ) {
	function boozurk_gallery_preview() {

			$attrs = boozurk_get_gallery_shortcode();

			return boozurk_gallery_shortcode( '', $attrs );

	}
}


// Get first audio
if ( !function_exists( 'boozurk_get_audio_shortcode' ) ) {
	function boozurk_get_audio_shortcode() {
		global $post;

		$pattern = get_shortcode_regex();

		if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
			&& array_key_exists( 2, $matches )
			&& in_array( 'audio', $matches[2] ) ) // audio shortcode is being used
		{
			$key = array_search( 'audio', $matches[2] );
			echo do_shortcode( $matches[0][$key] );
		}

	}
}

