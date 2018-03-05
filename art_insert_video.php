<?php
/**
 * Plugin Name: Insert Video with Schema.org
 * Text Domain: ivs-shortcode
 * Domain Path: /languages
 * Plugin URI: https://ru.wordpress.org/plugins/insert-video-with-schemaorg-ivws/
 * Description: The plug-in supports a shortcode, with which you can add YouTube videos directly with microschemas by schema.org. The markup is made according to the minimum requirements of Yandex
 * Contributors: artabr
 * Version: 3.1.4
 * Author: Artem Abramovich
 * Author URI: https://wpruse.ru/?p=67
 * Tags: shortcodes, shortcode, youtube shortcode, schema.org, schemaorg, rich snippet video, microdata, rich snippets, richsnippets, schema, schema.org, schema.org itemprop, seo, serp, structured data
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( 'IVS_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define( 'IVS_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
//require_once( IVS_PLUGIN_DIR . 'include/request.php' );
require_once( IVS_PLUGIN_DIR . 'admin/tinymce.php' );

/**
 *  Подключение переводов
 */
add_action( 'plugins_loaded', 'artabr_ivs_load_plugin_textdomain' );
function artabr_ivs_load_plugin_textdomain() {
	load_plugin_textdomain( 'ivs-shortcode', false, dirname( plugin_basename( __FILE__ ) )  . '/languages/' );
}

add_filter( 'mce_external_languages', 'artabr_ivs_button_lang');
function artabr_ivs_button_lang($locales) {
	$locales['art_insert_yt'] = IVS_PLUGIN_DIR . '/admin/translations.php';
	return $locales;
}

/**
 *  Enqueue_scripts and style
*/
add_action('wp_enqueue_scripts', 'artabr_ivs_add_css');
function artabr_ivs_add_css(){
	wp_register_style( 'ivs_style', IVS_PLUGIN_URI . 'assets/css/style.css', array(), '3.0') ;
	
}

add_action('admin_enqueue_scripts', 'artabr_ivs_add_admin_style_script',99);
function artabr_ivs_add_admin_style_script(){
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jqueryui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css', false, null );
	wp_enqueue_style( 'ivs-admin-style', IVS_PLUGIN_URI . 'assets/css/admin-style.css', array(), '3.0') ;
}

/**
 *  IVS shortcode
 */
add_shortcode('art_yt', 'artabr_ivs_youtube_video');
function artabr_ivs_youtube_video( $atts) {
	$atts             = shortcode_atts( array(
		'id'          => '',
		'urlvideo'    => '',
		'namevideo'   => '',
		'desc'        => '',
		'durationmin' => '',
		'durationsec' => '',
		'upld'        => '',
		'display'     => 'none',
		'wvideo'      => 1280,
		'hvideo'      => 720,
		'position'    => 'left',
		'related'     => false,
		'control'     => false,
		'showinfo'    => false,
	), $atts, 'art_yt' );
	$video_id         = esc_html( $atts['id'] );
	$video_title      = addcslashes( htmlspecialchars( $atts['namevideo'] ), '\34' );
	$video_desc       = esc_html( $atts['desc'] );
	$video_duration   = 'PT' . esc_html( $atts['durationmin'] ) . 'M' . esc_html( $atts['durationsec'] ) . 'S';
	$video_wvideo     = esc_html( $atts['wvideo'] );
	$video_hvideo     = esc_html( $atts['hvideo'] );
	$video_upld       = esc_html( $atts['upld'] );
	$youtube_url      = 'https://www.youtube.com/embed/' . $video_id;
	$video_url_params = array(
		'rel'      => 0,
		'controls' => 0,
		'showinfo' => 0,
	);
	if ( true == esc_html( $atts['related'] ) ) {
		unset( $video_url_params['rel'] );
	}
	if ( true == esc_html( $atts['control'] ) ) {
		unset( $video_url_params['controls'] );
	}
	if ( true == esc_html( $atts['showinfo'] ) ) {
		unset( $video_url_params['showinfo'] );
	}
	$video_url      = esc_url( add_query_arg( $video_url_params, esc_url_raw( $youtube_url ) ) );
	$video_position = esc_html( $atts['position'] );
	switch ( $video_position ) {
		case 'left';
			$video_position = 'ivs-left';
			break;
		case 'right';
			$video_position = 'ivs-right';
			break;
		case 'center';
			$video_position = 'ivs-center';
			break;
	}
	$out = '<div class="art_yt '. $video_position .'" itemscope itemid="" itemtype="http://schema.org/VideoObject">
	<link itemprop="url" href="https://www.youtube.com/watch?v='. $video_id . '">
	<meta itemprop="name" content="' . $video_title . '">
	<meta itemprop="description" content="'.  $video_desc . '">
	<meta itemprop="duration" content="' . $video_duration . '">
	<link itemprop="thumbnailUrl" href="https://i.ytimg.com/vi/' . $video_id . '/maxresdefault.jpg">
	<span itemprop="thumbnail" itemscope itemtype="http://schema.org/ImageObject">
	<link itemprop="contentUrl" href="https://i.ytimg.com/vi/' . $video_id . '/maxresdefault.jpg">
	<meta itemprop="width" content="'. $video_wvideo . '">
	<meta itemprop="height" content="'. $video_hvideo .'">
	</span>
	<link itemprop="embedUrl" href="https://www.youtube.com/embed/' . $video_id . '">
	<meta itemprop="isFamilyFriendly" content="True">
	<meta itemprop="datePublished" content="'. $video_upld .'">
	<meta itemprop="uploadDate" content="'. $video_upld .'"/>';
	$out .= '<iframe width="' . $video_wvideo . '" height="' . $video_hvideo .'" src="' . $video_url . '" frameborder="0" allowfullscreen></iframe></div>';
	wp_enqueue_style('ivs_style');
	return $out;
}


