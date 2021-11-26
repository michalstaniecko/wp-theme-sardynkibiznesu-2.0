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
  $logo_html = '<img src="%s" width="%s" height="%s" alt="%s" class="header__logo" />';
  $logo_html = sprintf($logo_html, $logo_src[0], $logo_src[1], $logo_src[2], get_bloginfo('name'));
  return $logo_html;
}
