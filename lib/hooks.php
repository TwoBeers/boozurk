<?php
/* Boozurk - Hooks */

/**
 * Contains all hook wrappers.
 */

// boozurk_hook_before_header() replaced by tha_header_before()
// boozurk_hook_after_header() replaced by tha_header_after()
// boozurk_hook_before_site_title() replaced by tha_header_top()
// boozurk_hook_before_pages() replaced by tha_header_after()
// boozurk_hook_before_footer() replaced by tha_footer_before()
// boozurk_hook_footer() replaced by tha_footer_top()
// boozurk_hook_after_footer() replaced by tha_footer_after()
// boozurk_hook_before_posts() replaced by tha_content_before()
// boozurk_hook_after_posts() replaced by tha_content_after()
// boozurk_hook_before_post() replaced by tha_entry_before()
// boozurk_hook_after_post() replaced by tha_entry_after()
// boozurk_hook_before_post_content() replaced by tha_entry_top()
// boozurk_hook_after_post_content() replaced by tha_entry_bottom()

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
