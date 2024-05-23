<?php

add_theme_support( 'post-thumbnails' );
add_theme_support( 'title-tag' );

add_image_size('article-desktop', 240, 9999);
add_image_size('article-tablet', 627, 9999);
add_image_size('article-mobile', 728, 9999);
add_image_size('single-desktop', 959, 9999);
add_image_size('single-mobile-320', 320, 9999);
add_image_size('single-mobile-540', 600, 9999);
add_image_size('single-mobile-767', 767, 9999);
add_image_size('product-card-486', 486, 9999);

add_action('after_setup_theme', 'sb_load_theme_textdomain',10);
function sb_load_theme_textdomain() {
  load_theme_textdomain( 'sb', get_template_directory() . '/languages' );
}

add_filter( 'image_size_names_choose', 'child_custom_sizes' );

function child_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'article-mobile' => __( 'Landing page 728px' ),
    ) );
}
