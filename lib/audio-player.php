<?php

add_action( 'template_redirect', 'boozurk_init_audio_player' );

// setup for audio player
if ( !function_exists( 'boozurk_init_audio_player' ) ) {
	function boozurk_init_audio_player(){
		global $boozurk_is_mobile_browser, $boozurk_is_printpreview;

		if ( is_admin() || $boozurk_is_mobile_browser || $boozurk_is_printpreview ) return;

		add_action( 'wp_head', 'boozurk_localize_audio_player' );
		add_action( 'boozurk_hook_after_post_content_single', 'boozurk_add_audio_player' );

	}
}

// add scripts
if ( !function_exists( 'boozurk_audioplayer_scripts' ) ) {
	function boozurk_audioplayer_scripts(){
		global $boozurk_version;

		wp_enqueue_script( 'bz-swf-audio-player', get_template_directory_uri() . '/js/audio-player.min.js', array( 'jquery', 'swfobject' ), $boozurk_version, true );

	}
}

// localize script
if ( !function_exists( 'boozurk_localize_audio_player' ) ) {
	function boozurk_localize_audio_player(){

?>

<script type="text/javascript">
	/* <![CDATA[ */
		bz_unknown_media_format = "<?php _e( 'this audio type is not supported by your browser','boozurk' ); ?>";
		bz_audioplayer_path = "<?php echo get_template_directory_uri().'/resources/audio-player/player.swf'; ?>";
	/* ]]> */
</script>

<?php

	}
}

// search for linked mp3's and add an audio player
if ( !function_exists( 'boozurk_add_audio_player' ) ) {
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
			// Add js
			boozurk_audioplayer_scripts();
			

		foreach ($result[0] as $key => $value) {
?>

<div class="bz-player-container">
	<small><?php echo $result[0][$key];?></small>
	<div class="bz-player-content">
		<audio controls="">
			<source src="<?php echo $result[3][$key];?>" />
			<span class="bz-player-notice"><?php _e( 'this audio type is not supported by your browser','boozurk' ); ?></span>
		</audio>
	</div>
</div>

<?php
		}
	}
}

?>