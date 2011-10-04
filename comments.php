<!-- begin comments -->
<?php
	if ( post_password_required() ) {
		echo '<div id="comments" style="text-align: right;">' . __( 'Enter your password to view comments.','boozurk' ) . '</div>';
		return;
	}
		global $boozurk_opt, $bz_is_printpreview, $bz_is_mobile_browser, $bz_is_ie6;
?>

<?php if ( comments_open() ) { ?>
	<div id="comments" style="text-align: right;">
		<?php comments_number( __( 'No Comments','boozurk' ), __( '1 Comment','boozurk' ), __( '% Comments','boozurk' ) ); ?> - <a href="#respond" title="<?php _e( "Leave a comment",'boozurk' ); ?>" <?php if ( !$bz_is_printpreview && ( $boozurk_opt['boozurk_cust_comrep'] == 1 ) && !$bz_is_mobile_browser ) echo 'onclick="return addComment.viewForm()"'; ?> ><?php _e( "Leave a comment",'boozurk' ); ?></a>
	</div>
	<?php
} elseif ( have_comments() ) { ?>
	<div id="comments" style="text-align: right;">
		<?php comments_number( __( 'No Comments','boozurk' ), __( '1 Comment','boozurk' ), __( '% Comments','boozurk' ) ); ?>
	</div>
	<?php
} ?>

<?php if ( have_comments() ) { ?>
	<?php boozurk_hook_before_comments(); ?>
	<ol id="commentlist">
		<?php //wp_list_comments(array('avatar_size' => 96)); ?>
		<?php wp_list_comments(); ?>
	</ol>
	<?php boozurk_hook_after_comments(); ?>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { ?>

		<div class="navigate_comments">
			<?php paginate_comments_links(); ?>
			<div class="fixfloat"> </div>
		</div>

	<?php
	}
}
//if comments are open
if ( comments_open() && !$bz_is_printpreview ) { 
		//define custom argoments for comment form
		
		$bz_fields =  array(
			'author' => '<p class="comment-form-author">' . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" />' .
						'<label for="author">' . __( 'Name', 'shiword' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .'</p>',
			'email'  => '<p class="comment-form-email">' . '<input id="email" name="email" type="text" value="' . sanitize_email(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" />' .
						'<label for="email">' . __( 'Email', 'shiword' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .'</p>',
			'url'    => '<p class="comment-form-url">' . '<input id="url" name="url" type="text" value="' . esc_url( $commenter['comment_author_url'] ) . '" size="30" />' .
						'<label for="url">' . __( 'Website', 'shiword' ) . '</label>' .'</p>',
		);
		
		$bz_custom_args = array(
			'fields'               => apply_filters( 'comment_form_default_fields', $bz_fields ),
			'comment_field'        => '<p class="comment-form-comment" style="text-align: center;"><textarea id="comment" name="comment" cols="45" rows="7" style="width: 95%;max-width: 95%;" aria-required="true"></textarea></p>',
			'comment_notes_after'  => '<p class="form-allowed-tags"><small>' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s','boozurk' ), allowed_tags() ) . '</small></p>',
			'label_submit'         => __( 'Say It!','boozurk' ),
			'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>.','boozurk' ), admin_url( 'profile.php' ), $user_identity ) . '</p>',
			'cancel_reply_link'    => __( 'Cancel reply','boozurk' ),
			'title_reply'          => __( 'Leave a comment','boozurk' ),
		);
		//output comment form
		comment_form($bz_custom_args); ?>
	<div class="fixfloat"></div>
<?php } ?>
<!-- end comments -->
