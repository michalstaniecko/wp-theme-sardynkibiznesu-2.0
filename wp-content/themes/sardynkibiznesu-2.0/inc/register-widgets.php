<?php

add_action('widgets_init', 'sb_register_widgets');
function sb_register_widgets() {
  register_sidebar(array(
    'name' => __('Main sidebar', 'sardynkibiznesu20'),
    'id'   => 'sidebar'
  ));
}
