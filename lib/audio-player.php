<?php
/**
 * audio-player.php
 *
 * the mediaelement audio player code
 *
 * @package Boozurk
 * @since 2.00
 */


class Boozurk_Audio_Player {

	function __construct() {

		add_action( 'template_redirect', array( $this, 'append_to_entry' ) );

	}


	function append_to_entry(){

		if ( is_admin() || boozurk_is_mobile() || ! boozurk_get_opt( 'boozurk_js_swfplayer' ) || ! is_single() ) return;

		add_action( 'boozurk_hook_entry_bottom', array( $this, 'audio_player' ) );

	}


	function audio_player( $text = '' ) {
		global $post;

		if ( post_password_required() ) return;

		$pattern = "/<a ([^=]+=['\"][^\"']+['\"] )*href=['\"](([^\"']+\.(mp3|ogg|m4a|wma|wav)))['\"]( [^=]+=['\"][^\"']+['\"])*>([^<]+)<\/a>/i";

		if ( $text != '')
			preg_match_all( $pattern, $text, $result );
		elseif ( is_attachment() )
			preg_match_all( $pattern, wp_get_attachment_link( $post->ID ), $result );
		else
			preg_match_all( $pattern, $post->post_content, $result );

		foreach ($result[0] as $key => $value) {
			echo do_shortcode('[audio src="' . $result[3][$key] . '"]');
		}
	}

}

new Boozurk_Audio_Player;
