<?php
if ( ! function_exists( '_wp_render_title_tag' ) ) {
  function theme_slug_render_title() {
    ?>
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <?php
  }
  add_action( 'wp_head', 'theme_slug_render_title' );
}

add_action('wp_head', 'favicon');
function favicon() {
  echo '<link rel="icon" href="/favicon.ico" type="image/png">';
}

function sb_get_the_logo() {
  $logo_id = get_field('logo', 'options');
  $logo_src = wp_get_attachment_image_src($logo_id, 'full');
  $logo_html = '<img src="%s" width="%s" height="%s" alt="%s" class="header__logo no-lazy-load" />';
  $logo_html = sprintf($logo_html, $logo_src[0], $logo_src[1], $logo_src[2], get_bloginfo('name'));
  return $logo_html;
}

add_action('wp_head', 'sb_custom_meta_tags');
function sb_custom_meta_tags() {
  echo get_field('custom_head_tags', 'options');
}

add_action('wp_head', 'preload_fonts', -100);
function preload_fonts() {
    $fonts = scandir(get_stylesheet_directory().'/assets/fonts');
    foreach ($fonts as $font) {
        if (strpos($font, '.woff2') !== false) {
            echo '<link rel="preload" href="'.get_stylesheet_directory_uri().'/assets/fonts/'.$font.'" as="font" type="font/woff2" crossorigin="anonymous">';
        }
    }
}

//add_action('wp_head', 'preload_featured_image', -110);
function preload_featured_image() {
    if (is_single() || is_page()) {
        $featured_image_id = get_post_thumbnail_id();
        if (!$featured_image_id) return;
        $featured_image_src = wp_get_attachment_image_src($featured_image_id, 'article-mobile');
        $featured_image_srcset = wp_get_attachment_image_srcset($featured_image_id, 'full');
        echo '<link rel="preload" href="'.$featured_image_src[0].'.webp" as="image">';
    }
}
