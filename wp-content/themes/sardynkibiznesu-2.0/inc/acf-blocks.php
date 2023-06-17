<?php
add_action('acf/init', 'sb_acf_init');
add_action('init', 'sb_register_acf_blocks');

function sb_acf_init() {

  // check function exists
  if (function_exists('acf_register_block')) {

    // register a featured block
    acf_register_block(array(
                         'name'            => 'sb-button',
                         'title'           => __('Button'),
                         'description'     => __('Button.'),
                         'render_callback' => 'sb_acf_block_render_callback',
                         'category'        => 'formatting',
                         'icon'            => 'admin-comments',
                         'keywords'        => array( 'button', 'przycisk' ),
                       ));

  }
}

function sb_acf_block_render_callback($block, $content, $is_preview) {

  // convert name ("acf/testimonial") into path friendly slug ("testimonial")
  $slug = str_replace('acf/', '', $block['name']);

  $category = $block['category'];

  // include a template part from within the "template-parts/block" folder
  if (file_exists(get_theme_file_path("/template-blocks/{$category}-{$slug}.php"))) {
    include( get_theme_file_path("/template-blocks/{$category}-{$slug}.php") );
  }
}

function sb_register_acf_blocks() {
  register_block_type(untrailingslashit(get_stylesheet_directory()) . '/blocks/faq');
}
