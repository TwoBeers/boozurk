<?php
/**
 * custom_comments.php
 *
 * adds some style to comments
 *
 * @package Boozurk
 * @since 2.00
 */


class BoozurkCommentStyle {

	var $variants = array();

	function BoozurkCommentStyle() {

		if ( is_admin() || boozurk_is_mobile() || boozurk_is_printpreview() ) return;

		add_action( 'comment_form_after_fields', array( &$this, 'comment_variant_field' ) );
		add_action( 'comment_form_logged_in_after', array( &$this, 'comment_variant_field' ) );
		add_action( 'comment_post', array( &$this, 'save_comment_meta_data' ) );
		add_filter( 'comment_class', array( &$this, 'add_comment_class' ) );
		
		$this->variants = array( 'style-default', 'style-blue', 'style-pink', 'style-orange', 'style-yellow', 'style-green', 'style-gray', 'style-white');

	}

	function comment_variant_field() {

		$variants = $this->variants;

		$outer = '';
		foreach( $variants as $variant ) {
			$outer .= '<label for="style-variant"><input type="radio" name="style-variant" value="' . $variant . '" /><span class="' . $variant . '"></span></label>';
		}
		$outer = '<p class="comment-variants">' . $outer . '</p>';

		echo $outer;

	}

	function save_comment_meta_data( $comment_id ) {

		if( in_array( $_POST[ 'style-variant' ], $this->variants ) )
			add_comment_meta( $comment_id, 'style-variant', $_POST[ 'style-variant' ] );

	}

	function add_comment_class ( $classes ){

		$class = get_comment_meta ( get_comment_ID(), 'style-variant', true );

		if ($class) $classes[] = $class;

		//send the array back
		return $classes;

	}

}

new BoozurkCommentStyle;
