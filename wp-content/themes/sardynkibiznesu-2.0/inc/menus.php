<?php

add_action('after_setup_theme', 'sb_register_nav_menus');
function sb_register_nav_menus() {
  register_nav_menus(array(
    'primary_menu' => __('Primary Menu', 'sardynkibiznesu20')
  ));
}
