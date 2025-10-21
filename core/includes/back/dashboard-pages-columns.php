<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Yoast SEO columns to the page list table.
 *
 * @param array $columns The existing columns.
 *
 * @return array The modified columns.
 */
function add_yoast_seo_columns( $columns ) {
	$columns['yoast_seo_title']       = __('Yoast Title', TEXTDOMAIN );
	$columns['yoast_seo_description'] = __('Yoast Description', TEXTDOMAIN );

	return $columns;
}

add_filter( 'manage_pages_columns', 'add_yoast_seo_columns' );

/**
 * Display the content for the custom Yoast SEO columns.
 *
 * @param string $column_name The name of the column to display.
 * @param int    $post_id     The ID of the current post.
 */
function display_yoast_seo_columns( $column_name, $post_id ) {
	if ( $column_name === 'yoast_seo_title' ) {
		$yoast_title = get_post_meta( $post_id, '_yoast_wpseo_title', true );
		echo esc_html( $yoast_title );
	}

	if ( $column_name === 'yoast_seo_description' ) {
		$yoast_description = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
		echo esc_html( $yoast_description );
	}
}

add_action( 'manage_pages_custom_column', 'display_yoast_seo_columns', 10, 2 );
