<?php

add_action('widgets_init', 'sb_register_widgets');
function sb_register_widgets() {
  register_sidebar(array(
    'name' => __('Main sidebar', 'sardynkibiznesu20'),
    'id'   => 'sidebar',
    'before_widget'  => '<div id="%1$s" class="widget %2$s">',
    'after_widget'   => "</div>\n",
  ));
}
