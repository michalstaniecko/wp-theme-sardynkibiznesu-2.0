<?php

add_action('after_setup_theme', 'remove_junk');
function remove_junk() {
  remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');

  remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
  remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
  remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
  remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
  remove_action('wp_head', 'index_rel_link'); // index link
  remove_action('wp_head', 'parent_post_rel_link', 10, 0); // prev link
  remove_action('wp_head', 'start_post_rel_link', 10, 0); // start link
  remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
  remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('wp_print_styles', 'print_emoji_styles');

  remove_action('wp_head', 'rest_output_link_wp_head', 10);
  remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
  remove_action('rest_api_init', 'wp_oembed_register_route');// Remove the REST API endpoint.
  add_filter('embed_oembed_discover', '__return_false');// Turn off oEmbed auto discovery.
  remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);// Don't filter oEmbed results.
  remove_action('wp_head', 'wp_oembed_add_discovery_links');// Remove oEmbed discovery links.
  remove_action('wp_head', 'wp_oembed_add_host_js');// Remove oEmbed-specific JavaScript from the front-end and back-end.

  // REMOVE WP 5.9 BLOCK EDITOR JUNK;
  // remove SVG and global styles
  remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
  // remove wp_footer actions which add's global inline styles
  remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
  // remove render_block filters which adding unnecessary stuff
  remove_filter('render_block', 'wp_render_duotone_support');
  remove_filter('render_block', 'wp_restore_group_inner_container');
  remove_filter('render_block', 'wp_render_layout_support_flag');
  add_filter('the_generator', 'remove_wp_version_rss');
  add_filter('jetpack_implode_frontend_css', '__return_false', 99);
}

