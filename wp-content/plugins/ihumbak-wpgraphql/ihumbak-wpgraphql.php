<?php

/**
 * Plugin Name: iHumbak WPGraphQL
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

add_action('graphql_register_types', 'sardynki_extend_wpgraphql_schema');

function sardynki_extend_wpgraphql_schema() {
  register_graphql_object_type('PageSettingsType', [
    'description' => __('Page settings', 'your-textdomain'),
    'fields' => [
      'logo' => [
        'type' => 'String',
        'description' => __('Page logo', 'your-textdomain'),
      ],
      'footer' => [
        'type' => 'String',
        'description' => __('Footer', 'your-textdomain'),
      ],
    ],
  ]);
  register_graphql_field('RootQuery', 'pageSettings', [
    'type' => 'PageSettingsType',
    'description' => __('Describe what the field should be used for', 'your-textdomain'),
    'resolve' => function() {
      $logo_id = get_field('logo', 'options');
      $logo = wp_get_attachment_image_url($logo_id, 'full');
      $footer = '';
     if (is_active_sidebar('footer_about_us')) {
        ob_start();
        dynamic_sidebar('footer_about_us');
        $footer = ob_get_clean();
      }
      return array(
        'logo' => $logo,
        'footer' => ($footer)
      );
    }
  ]);
}
