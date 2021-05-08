<?php

add_action('wp_enqueue_scripts', 'sb_theme_enqueue');
function sb_theme_enqueue() {
  $js = filemtime(get_stylesheet_directory().'/assets/index.js');
  $css = filemtime(get_stylesheet_directory().'/assets/main.css');
  wp_enqueue_script('index', get_stylesheet_directory_uri().'/assets/index.js', false, $js, true);
  wp_enqueue_style('main', get_stylesheet_directory_uri().'/assets/main.css', false, $css);
}
