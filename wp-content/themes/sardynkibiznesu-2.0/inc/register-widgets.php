<?php

add_action('widgets_init', 'sb_register_widgets');
function sb_register_widgets() {
  register_sidebar(array(
    'name' => __('Main sidebar', 'sardynkibiznesu20'),
    'id'   => 'sidebar',
    'before_widget'  => '<div id="%1$s" class="widget %2$s">',
    'after_widget'   => "</div>\n",
  ));

  register_sidebar(array(
    'name' => __('Footer About Us', 'sb'),
    'id' => 'footer_about_us',
    'before_widget'  => '',
    'after_widget'   => "",
    'before_title'   => '',
    'after_title'    => "",
  ));
  register_sidebar(array(
    'name' => __('Footer Nav Menu', 'sb'),
    'id' => 'footer_nav_menu',
    'before_widget'  => '',
    'after_widget'   => "",
    'before_title'   => '',
    'after_title'    => "",
  ));

  register_sidebar(array(
    'name' => __('Footer Bottom Bar', 'sb'),
    'id' => 'footer_bottom_bar',
    'before_widget'  => '',
    'after_widget'   => "",
    'before_title'   => '',
    'after_title'    => "",
  ));
}
