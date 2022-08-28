<?php

add_action('wp_footer', 'sb_custom_footer_scripts', 9999);
function sb_custom_footer_scripts() {
  echo get_field('custom_footer_scripts', 'options');
}
