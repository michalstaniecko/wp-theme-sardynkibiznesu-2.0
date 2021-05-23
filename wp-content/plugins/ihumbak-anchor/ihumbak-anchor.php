<?php
/**
 *
 * Plugin Name: iHumbak Anchor
 * Version: 1.6
 *
 */
/*
WYŁĄCZONE 20201015
include_once plugin_dir_path(__FILE__).'updater.php';
*/
include_once plugin_dir_path(__FILE__).'admin/admin-scripts.php';
include_once plugin_dir_path(__FILE__).'options.php';

function ihumbak_anchor_assets() {
  if ( is_single() || is_page() ) {
    wp_enqueue_style('ihumbak-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_script( 'ihumbak-anchor-script', plugin_dir_url( __FILE__ ) . 'js/ihumbak-anchor.js?v=1.6', array( 'jquery' ), null, true );
    wp_enqueue_style( 'ihumbak-anchor-style', plugin_dir_url( __FILE__ ) . 'css/ihumbak-anchor-style.css', null, null, 'all' );
  }
}

add_action( 'wp_enqueue_scripts', 'ihumbak_anchor_assets' );

function ihumbak_anchor_shortcode($atts, $content) {
  $a = shortcode_atts(array(
    'type' => 'h2'
  ), $atts);
  return '<'.$a['type'].' class="ihumbak-anchor">'.$content.'</'.$a['type'].'>';
}

add_shortcode('ihumbak_anchor', 'ihumbak_anchor_shortcode');

function ihumbak_anchor_menu_shortcode($atts, $content) {
  return '<style>'. ( get_option('ihumbak_anchor_styles') ).'</style><div class="ihumbak-anchor-menu-wrapper"><strong>Spis Treści</strong>
<div class="ihumbak-anchor-menu-hide">Zwiń <i class="fa fa-chevron-up"></i></div>
<div class="ihumbak-anchor-menu-show ihumbak-anchor-d-none">Rozwiń <i class="fa fa-chevron-down"></i></div></div>';
}

add_shortcode('ihumbak_anchor_menu', 'ihumbak_anchor_menu_shortcode');

add_action( 'init', 'ihumbak_anchor_buttons' );
function ihumbak_anchor_buttons() {
  add_filter( "mce_external_plugins", "ihumbak_anchor_add_buttons" );
  add_filter( 'mce_buttons', 'ihumbak_anchor_register_buttons' );
}
function ihumbak_anchor_add_buttons( $plugin_array ) {
  $plugin_array['ihumbak_anchor'] = plugin_dir_url(__FILE__) . '/js/tinymce_buttons.js';
  return $plugin_array;
}
function ihumbak_anchor_register_buttons( $buttons ) {
  array_push( $buttons, 'ihumbak_anchor_button' ); // dropcap', 'recentposts
  return $buttons;
}
