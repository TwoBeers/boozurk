<?php
/**
 * hooks.php
 *
 * defines every wrapping function for the theme hooks
 *
 * Includes The Hook Alliance support file (https://github.com/zamoose/themehookalliance)
 *
 * @package boozurk
 * @since boozurk 2.00
 */


/** Grab the THA theme hooks file */
require_once( get_template_directory() . '/tha/tha-theme-hooks.php' );

function boozurk_hook_head_top() {
	tha_head_top();
	do_action( 'boozurk_hook_head_top' );
}

function boozurk_hook_head_bottom() {
	do_action( 'boozurk_hook_head_bottom' );
	tha_head_bottom();
}

function boozurk_hook_header_before() {
	tha_header_before();
	do_action( 'boozurk_hook_header_before' );
}

function boozurk_hook_header_after() {
	do_action( 'boozurk_hook_header_after' );
	tha_header_after();
}

function boozurk_hook_header_top() {
	tha_header_top();
	do_action( 'boozurk_hook_header_top' );
}

function boozurk_hook_header_bottom() {
	do_action( 'boozurk_hook_header_bottom' );
	tha_header_bottom();
}

function boozurk_hook_content_before() {
	tha_content_before();
	do_action( 'boozurk_hook_content_before' );
}

function boozurk_hook_content_after() {
	do_action( 'boozurk_hook_content_after' );
	tha_content_after();
}

function boozurk_hook_content_top() {
	tha_content_top();
	do_action( 'boozurk_hook_content_top' );
}

function boozurk_hook_content_bottom() {
	do_action( 'boozurk_hook_content_bottom' );
	tha_content_bottom();
}

function boozurk_hook_entry_before() {
	tha_entry_before();
	do_action( 'boozurk_hook_entry_before' );
}

function boozurk_hook_entry_after() {
	do_action( 'boozurk_hook_entry_after' );
	tha_entry_after();
}

function boozurk_hook_entry_top() {
	tha_entry_top();
	do_action( 'boozurk_hook_entry_top' );
}

function boozurk_hook_entry_bottom() {
	do_action( 'boozurk_hook_entry_bottom' );
	tha_entry_bottom();
}

function boozurk_hook_comments_before() {
	tha_comments_before();
	do_action( 'boozurk_hook_comments_before' );
}

function boozurk_hook_comments_after() {
	do_action( 'boozurk_hook_comments_after' );
	tha_comments_after();
}

function boozurk_hook_sidebars_before() {
	tha_sidebars_before();
	do_action( 'boozurk_hook_sidebars_before' );
}

function boozurk_hook_sidebars_after() {
	do_action( 'boozurk_hook_sidebars_after' );
	tha_sidebars_after();
}

function boozurk_hook_sidebar_top() {
	tha_sidebar_top();
	do_action( 'boozurk_hook_sidebar_top' );
}

function boozurk_hook_sidebar_bottom() {
	do_action( 'boozurk_hook_sidebar_bottom' );
	tha_sidebar_bottom();
}

function boozurk_hook_footer_before() {
	tha_footer_before();
	do_action( 'boozurk_hook_footer_before' );
}

function boozurk_hook_footer_after() {
	do_action( 'boozurk_hook_footer_after' );
	tha_footer_after();
}

function boozurk_hook_footer_top() {
	tha_footer_top();
	do_action( 'boozurk_hook_footer_top' );
}

function boozurk_hook_footer_bottom() {
	do_action( 'boozurk_hook_footer_bottom' );
	tha_footer_bottom();
}
function boozurk_hook_body_top() {
	do_action('boozurk_hook_body_top');
}

function boozurk_hook_body_bottom() {
	do_action('boozurk_hook_body_bottom');
}

function boozurk_hook_menu_after() {
	do_action('boozurk_hook_menu_after');
}

function boozurk_hook_post_title_before() {
	do_action('boozurk_hook_post_title_before');
}

function boozurk_hook_post_title_after() {
	do_action('boozurk_hook_post_title_after');
}

function boozurk_hook_comments_list_before() {
	do_action('boozurk_hook_comments_list_before');
}

function boozurk_hook_comments_list_after() {
	do_action('boozurk_hook_comments_list_after');
}

function boozurk_hook_primary_sidebar_top() {
	do_action('boozurk_hook_primary_sidebar_top');
}

function boozurk_hook_primary_sidebar_bottom() {
	do_action('boozurk_hook_primary_sidebar_bottom');
}

function boozurk_hook_404_sidebar_before() {
	do_action('boozurk_hook_404_sidebar_before');
}

function boozurk_hook_404_sidebar_after() {
	do_action('boozurk_hook_404_sidebar_after');
}

function boozurk_hook_footer_sidebar_top() {
	do_action('boozurk_hook_footer_sidebar_top');
}

function boozurk_hook_footer_sidebar_bottom() {
	do_action('boozurk_hook_footer_sidebar_bottom');
}
