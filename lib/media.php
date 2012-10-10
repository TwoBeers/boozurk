<?php

check_admin_referer( 'logo-nonce' );

if ( !current_user_can( 'edit_theme_options' ) ) {
	wp_die( 'You do not have sufficient permissions to access this page.' );
}

function boozurk_media_navi( $paged, $ppp, $count ) {

	$arr_params['tb_media'] = '1';
	$arr_params_option['tb_media'] = '1';
	
	$navi = '<div class="bz-media-navi">';
	$navi .= '<span style="font-style:italic;color:#777;font-size:10px;">' . sprintf( __( '%s images', 'boozurk' ), $count ) . '</span>';
	if ( $paged == 2 ) { $navi .= '<a href="' . add_query_arg( $arr_params, home_url() ) . '">&laquo;</a>'; }
	if ( $paged > 2 ) { $arr_params['bzpaged'] = ( $paged - 1 ); $navi .= '<a href="' . add_query_arg( $arr_params, home_url() ) . '">&laquo;</a>'; }
	if ( ( $count / $paged ) > 1 ) {
		$navi .= '<select id="sample" onChange="bzGoTo(this.options[this.selectedIndex].value)">';
		for ($i=1; $i<=ceil( $count / $ppp ); $i++) {
			if ( $i == 1 ) { $navi .= '<option value="' . (add_query_arg( $arr_params_option, home_url() )) . '" ' . selected( $paged, 1 , false) . '>' . $i . '</option>'; }
			if ( $i > 1 ) { $arr_params_option['bzpaged'] = ( $i ); $navi .= '<option value="' . (add_query_arg( $arr_params_option, home_url() )) . '" ' . selected( $paged, $i , false) . '>' . $i . '</option>'; }
		}	
		$navi .= '</select>';
		$navi .= '<span>' . sprintf( __( 'of %s', 'boozurk' ), ceil( $count / $ppp ) ) . '</span>';
	}
	if ( $count > ( $paged * $ppp ) ) { $arr_params['bzpaged'] = ( $paged + 1 ); $navi .= '<a href="' . add_query_arg( $arr_params, home_url() ) . '">&raquo;</a>'; }
	$navi .= '</div>';
	return $navi;
	
}

function boozurk_media_library() {
	$paged = isset( $_GET['bzpaged'] ) ? (int)$_GET['bzpaged'] : 1;
	$ppp = 21;
	$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_mime_type' => 'image', 'post_status' => null, 'post_parent' => null ); 
	$attachments = get_posts( $args );
	
	$navi = boozurk_media_navi( $paged, $ppp, count($attachments) );
	
	$attachments = array_slice( $attachments, ( ( $paged - 1 ) * $ppp ), $ppp );
	
	if ($attachments) { ?>
		<?php echo $navi; ?>
		<div id="bz-media-library">
			<?php foreach ( $attachments as $attachment ) {
				setup_postdata($attachment);
				$details = wp_get_attachment_image_src( $attachment->ID, 'full' ); 
			?>
				<div class="thumb"><a href="javascript:void(0)" onClick="bzSendToInput('<?php echo esc_url($details[0]); ?>')"><?php echo wp_get_attachment_image( $attachment->ID ); ?></a></div>
			<?php } ?>
		</div>
		<?php echo $navi; ?>
	<?php }

}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<title><?php echo bloginfo( 'name' ); ?> - <?php _e( 'Media Library','boozurk' ); ?></title>
	</head>
	<style type="text/css">
		body {
			font-family: verdana,tahoma,monospace;
			font-size: 14px;
		}
		.bz-media-navi a {
			font-weight: bold;
			border-radius: 11px;
			border: 1px solid #bbb;
			line-height: 13px;
			padding: 3px 8px;
			text-decoration: none;
			background: #eee;
			margin: 0 0 0 10px;
			text-shadow:0 1px 0 #fff;
		}
		.bz-media-navi a:hover {
			border: 1px solid #666;
		}
		a {
			color: #464646;
		}
		a:hover {
			color: #000;
		}
		select {
			font-size:12px;
			height:2em;
			padding:2px;
			background:#fff;
			border: 1px solid #ddd;
			border-radius: 3px;
			margin: 0 10px;
		}
		.bz-media-navi {
			border-top: 1px solid #ddd;
			margin: 15px;
			padding: 5px;
			text-align: right;
			clear: both;
		}
		.bz-media-navi:first-child {
			border-top: none;
			border-bottom: 1px solid #ddd;
		}
		.thumb {
			float:left;
			height:162px;
			margin:0 0 15px;
			text-align:center;
			width:33%;
		}
		.thumb img {
			padding: 5px;
			margin: 5px;
			border: 1px solid #ddd;
		}
	</style>
	<body>
		<?php boozurk_media_library(); ?>
		<script type="text/javascript">
			/* <![CDATA[ */
			var win = window.dialogArguments || parent || opener || top;
			function bzSendToInput(the_src) {
				win.document.getElementById('option_field_boozurk_logo').value=the_src;
				win.tb_remove();
			}
			function bzGoTo(the_url) {
				window.open(the_url,'_self');
			}
			/* ]]> */
		</script>
	</body>
</html>