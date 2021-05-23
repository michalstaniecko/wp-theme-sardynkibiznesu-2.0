<?php

//add_action('admin_enqueue_scripts', 'ihumbak_admin_scripts');

function ihumbak_admin_scripts() {
  wp_enqueue_script('ihumbak-highlights', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/highlight.min.js',false,null, true);
  wp_enqueue_style('ihumbak-highlights-css', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/styles/default.min.css');
  wp_enqueue_script('ihumbak-admin', plugin_dir_url(__FILE__).'js/ihumbak-admin.js',false,null,true);
}