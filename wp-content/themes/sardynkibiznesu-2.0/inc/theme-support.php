<?php

add_theme_support( 'post-thumbnails' );
add_theme_support( 'title-tag' );

add_image_size('article-desktop', 240, 9999);
add_image_size('article-tablet', 627, 9999);
add_image_size('article-mobile', 728, 9999);

add_action('after_setup_theme', 'sb_load_theme_textdomain',10);
function sb_load_theme_textdomain() {
  load_theme_textdomain( 'sb', get_template_directory() . '/languages' );
}
