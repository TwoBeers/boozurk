<!-- begin comments -->
<?php tha_comments_before(); ?>

<?php
	global $boozurk_is_printpreview;

	if ( post_password_required() ) {
		echo '<div id="comments">' . __( 'Enter your password to view comments.','boozurk' ) . '</div>';
		return;
	}
?>

<?php if ( comments_open() ) { ?>
	<div id="comments">
		<?php comments_number( __( 'No Comments','boozurk' ), __( '1 Comment','boozurk' ), __( '% Comments','boozurk' ) ); ?><span class="hide_if_print"> - <a href="#respond" title="<?php esc_attr_e( "Leave a comment",'boozurk' ); ?>"><?php _e( "Leave a comment",'boozurk' ); ?></a></span>
	</div>
	<?php
} elseif ( have_comments() ) { ?>
	<div id="comments">
		<?php comments_number( __( 'No Comments','boozurk' ), __( '1 Comment','boozurk' ), __( '% Comments','boozurk' ) ); ?>
	</div>
	<?php
} ?>

<?php if ( have_comments() ) { ?>

	<?php boozurk_navigate_comments(); ?> 

	<?php boozurk_hook_comments_list_before(); ?>
	<ol id="commentlist">
		<?php //wp_list_comments(array('avatar_size' => 96)); ?>
		<?php wp_list_comments(); ?>
	</ol>
	<?php boozurk_hook_comments_list_after(); ?>

	<?php boozurk_navigate_comments(); ?> 


<?php
}
//if comments are open
if ( comments_open() && !$boozurk_is_printpreview ) { 
	comment_form(); ?>
	<div class="fixfloat"></div>
<?php } ?>
<!-- end comments -->

<?php tha_comments_after(); ?>
