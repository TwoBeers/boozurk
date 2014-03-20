<?php
/**
 * hooks.php
 *
 * defines every wrapping function for the theme hooks
 *
 * Includes The Hook Alliance support file (https://github.com/zamoose/themehookalliance)
 *
 * @package Boozurk
 * @since 2.00
 */


/** Grab the THA theme hooks file */
require_once( get_template_directory() . '/tha/tha-theme-hooks.php' );

/**
 * the <head> section
 */
function boozurk_hook_head_top() {
	tha_head_top();
	do_action( 'boozurk_hook_head_top' );
}

function boozurk_hook_head_bottom() {
	do_action( 'boozurk_hook_head_bottom' );
	tha_head_bottom();
}

/**
 * the header section
 */
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

/**
 * the content section
 */
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

/**
 * the entry section
 */
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
	do_action( 'boozurk_hook_share_links' );
	do_action( 'boozurk_hook_entry_bottom' );
	tha_entry_bottom();
}

/**
 * the comments section
 */
function boozurk_hook_comments_before() {
	tha_comments_before();
	do_action( 'boozurk_hook_comments_before' );
}

function boozurk_hook_comments_after() {
	do_action( 'boozurk_hook_comments_after' );
	tha_comments_after();
}

function boozurk_hook_comments_list_before() {
	do_action( 'boozurk_hook_comments_list_before' );
}

function boozurk_hook_comments_list_after() {
	do_action( 'boozurk_hook_comments_list_after' );
}

/**
 * the sidebars section
 *
 * currently supported $location:
 * - primary -> sidebar.php
 * - secondary -> sidebar-secondary.php
 * - header -> sidebar-header.php
 * - footer -> sidebar-footer.php
 * - error404 -> sidebar-error404.php
 */
function boozurk_hook_sidebars_before( $location = 'undefined' ) {
	tha_sidebars_before();
	do_action( 'boozurk_hook_sidebars_before' );
	do_action( 'boozurk_hook_' . $location . '_sidebar_before' );
}

function boozurk_hook_sidebars_after( $location = 'undefined' ) {
	do_action( 'boozurk_hook_' . $location . '_sidebar_after' );
	do_action( 'boozurk_hook_sidebars_after' );
	tha_sidebars_after();
}

function boozurk_hook_sidebar_top( $location = 'undefined' ) {
	tha_sidebar_top();
	do_action( 'boozurk_hook_sidebar_top' );
	do_action( 'boozurk_hook_' . $location . '_sidebar_top' );
}

function boozurk_hook_sidebar_bottom( $location = 'undefined' ) {
	do_action( 'boozurk_hook_' . $location . '_sidebar_bottom' );
	do_action( 'boozurk_hook_sidebar_bottom' );
	tha_sidebar_bottom();
}

/**
 * the footer section
 */
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

/**
 * the <body> section
 */
function boozurk_hook_body_top() {
	do_action( 'boozurk_hook_body_top' );
}

function boozurk_hook_body_bottom() {
	do_action( 'boozurk_hook_body_bottom' );
}

/**
 * the posts title section
 */
function boozurk_hook_post_title_before() {
	do_action( 'boozurk_hook_post_title_before' );
}

function boozurk_hook_post_title_after() {
	do_action( 'boozurk_hook_post_title_after' );
}

/**
 * the breadcrumb section
 */
function  boozurk_hook_breadcrumb_navigation () {
	do_action( 'boozurk_hook_breadcrumb_navigation' );
}

/**
 * the post extrainfos section
 */
function  boozurk_hook_post_extrainfo ( $args ) {
	do_action( 'boozurk_hook_post_extrainfo', $args );
}