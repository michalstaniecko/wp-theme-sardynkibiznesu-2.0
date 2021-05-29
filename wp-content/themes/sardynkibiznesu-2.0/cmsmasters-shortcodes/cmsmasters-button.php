<?php

/**
 * Notice
 */

add_shortcode('cmsmasters_button', 'cmsmasters_button');

function cmsmasters_button($atts, $content = null)
{
  $a = shortcode_atts(array(
    'shortcode_id' => 			'',
    'button_title' => 			'',
    'button_link' => 			'#',
    'button_target' => 			'',
    'button_text_align' => 		'center',
    'button_style' => 			'',
    'button_font_family' => 	'',
    'button_font_size' => 		'',
    'button_line_height' => 	'',
    'button_font_weight' => 	'',
    'button_font_style' => 		'',
    'button_text_transform' => 	'',
    'button_padding_hor' => 	'',
    'button_border_width' => 	'',
    'button_border_style' => 	'',
    'button_border_radius' => 	'',
    'button_bg_color' => 		'',
    'button_text_color' => 		'',
    'button_border_color' => 	'',
    'button_bg_color_h' => 		'',
    'button_text_color_h' => 	'',
    'button_border_color_h' => 	'',
    'button_icon' => 			'',
    'animation' => 				'',
    'animation_delay' => 		'',
    'classes' => 				''
  ), $atts);

  ob_start(); ?>
  <div id="cmsmasters_button_tk8778inh" class="button_wrap">
    <a href="%s"
       target="%s"
       class="cmsmasters_button cmsmasters_but_clear_styles cmsmasters_but_bg_hover cmsmasters-icon-download">
      <span>%s</span>
    </a>
  </div>
  <?php $out = ob_get_clean();

  $out = sprintf($out, $a['button_link'], $a['button_target'], $content);

  return $out;
}

