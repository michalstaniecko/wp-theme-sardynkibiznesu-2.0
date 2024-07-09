<?php

add_action('wp_enqueue_scripts', 'sb_theme_enqueue', 1);
function sb_theme_enqueue() {
  $js = filemtime(get_stylesheet_directory().'/assets/index.js');
  $css = filemtime(get_stylesheet_directory().'/assets/main.css');
  wp_enqueue_script('index', get_stylesheet_directory_uri().'/assets/index.js', false, false, true);
  wp_enqueue_style('main', get_stylesheet_directory_uri().'/assets/main.css', false);
  wp_enqueue_style('cp-popup', plugins_url('convertpro/assets/modules/css').'/cp-popup.min.css', false);

  wp_register_script('faq', get_stylesheet_directory_uri() .'/blocks/faq/faq.js', false, false, true);

}

add_action('admin_enqueue_scripts', 'sb_admin_enqueue');
function sb_admin_enqueue() {
  $css = filemtime(get_stylesheet_directory().'/assets/admin.css');
  $js = filemtime(get_stylesheet_directory().'/assets/admin.js');
  wp_enqueue_style('main', get_stylesheet_directory_uri().'/assets/admin.css', false, $css);
  wp_enqueue_script('admin-main', get_stylesheet_directory_uri().'/assets/admin.js', false, $js, true);
}

function sb_remove_wp_block_library_css(){
  wp_dequeue_style( 'wp-block-library' );
  wp_dequeue_style( 'wp-block-library-theme' );
  wp_dequeue_style( 'wc-blocks-style' ); // Remove WooCommerce block CSS
}
add_action( 'wp_enqueue_scripts', 'sb_remove_wp_block_library_css', 100 );
