<?php

/**
 * Notice
 */

add_shortcode('cmsmasters_notice', 'cmsmasters_notice');

function cmsmasters_notice($atts, $content = null)
{
  $new_atts = apply_filters('cmsmasters_notice_atts_filter', array(
    'shortcode_id'    => '',
    'type'            => 'cmsmasters_notice_success',
    'icon'            => '',
    'close'           => '',
    'bg_color'        => '',
    'bd_color'        => '',
    'color'           => '',
    'animation'       => '',
    'animation_delay' => '',
    'classes'         => ''
  ));
  ob_start(); ?>
  <div id="cmsmasters_notice_u4nkm5fny" class="cmsmasters_notice cmsmasters_notice_error cmsmasters-icon-ghost">
    <div class="notice_content">
      <p>%s</p>
    </div>
  </div>
  <?php $out = ob_get_clean();

  $out = sprintf($out, $content);

  return $out;
}

