<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Add button
 */
add_action('admin_head', 'artabr_ivs_add_mce_button');
function artabr_ivs_add_mce_button() {
	if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
		return;
	}
	add_filter( 'mce_external_plugins', 'artabr_ivs_add_tinymce_script' );
	add_filter( 'mce_buttons', 'artabr_ivs_register_mce_button' );
}

function artabr_ivs_add_tinymce_script( $plugin_array ) {
	$plugin_array['art_insert_yt'] = plugins_url('mce/insert_video.js', __FILE__);
	return $plugin_array;
}
function artabr_ivs_register_mce_button( $buttons ) {
	array_push( $buttons, 'art_insert_yt' );
	return $buttons;
}