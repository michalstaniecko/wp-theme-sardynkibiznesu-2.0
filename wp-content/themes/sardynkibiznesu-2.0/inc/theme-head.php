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
