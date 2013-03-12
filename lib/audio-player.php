<?php
/**
 * audio-player.php
 *
 * the swf audio player code
 *
 * @package Boozurk
 * @since 2.00
 */


add_action( 'template_redirect', 'boozurk_init_audio_player' );


// setup for audio player
if ( !function_exists( 'boozurk_init_audio_player' ) ) {
	function boozurk_init_audio_player(){

		if ( is_admin() || boozurk_is_mobile() || boozurk_is_printpreview() || ! boozurk_get_opt( 'boozurk_js_swfplayer' ) || ! is_single() ) return;

		add_action( 'boozurk_hook_entry_bottom', 'boozurk_add_audio_player' );

	}
}


// add scripts
function boozurk_audioplayer_scripts(){

	wp_enqueue_script( 'boozurk-audioplayer-script', get_template_directory_uri() . '/js/audio-player.dev.js', array( 'jquery', 'swfobject' ), boozurk_get_info( 'version' ), true );

	$data = array(
		'unknown_media' => esc_attr( __( 'unknown media format', 'boozurk' ) ),
		'player_path' => get_template_directory_uri().'/resources/audio-player/player.swf',
	);
	wp_localize_script( 'boozurk-audioplayer-script', 'boozurkAudioPlayer_l10n', $data );

}


// search for linked mp3's and add an audio player
function boozurk_add_audio_player( $text = '' ) {
	global $post;
	
	if ( post_password_required() ) return;
	
	$pattern = "/<a ([^=]+=['\"][^\"']+['\"] )*href=['\"](([^\"']+\.(mp3|ogg|m4a)))['\"]( [^=]+=['\"][^\"']+['\"])*>([^<]+)<\/a>/i";
	
	if ( $text != '')
		preg_match_all( $pattern, $text, $result );
	elseif ( is_attachment() )
		preg_match_all( $pattern, wp_get_attachment_link( $post->ID ), $result );
	else
		preg_match_all( $pattern, $post->post_content, $result );

	if ( $result[0] )
		boozurk_audioplayer_scripts(); // Add js

	$instance = 0;

	foreach ($result[0] as $key => $value) {
		$instance++;

?>
	<div class="bz-player-container">
		<small><?php echo $result[0][$key];?></small>
		<div class="bz-player-content">
			<audio controls="" id="sw-player-<?php echo $instance . '-' . $post->ID; ?>" class="no-player">
				<source src="<?php echo $result[3][$key];?>" />
				<span class="bz-player-notice"><?php _e( 'this audio type is not supported by your browser','boozurk' ); ?></span>
			</audio>
		</div>
	</div>
<?php

	}
}
