<?php
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version 	1.0.5
 * 
 * Content Composer Heading Shortcode
 * Created by CMSMasters
 * 
 */


extract(shortcode_atts($new_atts, $atts));


$unique_id = $shortcode_id;


if ($font_family != '') {
	$font_family_array = str_replace('+', ' ', explode(':', $font_family));
	
	$font_family_name = "'" . $font_family_array[0] . "'";
	
	$font_family_url = str_replace('+', ' ', $font_family);
	
	
	cmsmasters_theme_google_font($font_family_url, $font_family_array[0]);
}


$shortcode_styles = "\n" . 
	'#cmsmasters_heading_' . esc_attr($unique_id) . ' { ' . 
		"\n\t" . 'text-align:' . esc_attr($text_align) . '; ' . 
		"\n\t" . 'margin-top:' . esc_attr($margin_top) . 'px; ' . 
		"\n\t" . 'margin-bottom:' . esc_attr($margin_bottom) . 'px; ' . 
	"\n" . '} ' . "\n\n" . 
	'#cmsmasters_heading_' . esc_attr($unique_id) . ' .cmsmasters_heading { ' . 
		"\n\t" . 'text-align:' . esc_attr($text_align) . '; ' . 
		(($bg_color != '') ? "\n\t" . cmsmasters_color_css('background-color', $bg_color) : '') . 
		(($bg_color != '') ? "\n\t" . 'padding-left:1em; ' : '') . 
		(($bg_color != '') ? "\n\t" . 'padding-right:1em; ' : '') . 
		(($divider != '' && $text_align != 'left') ? "\n\t" . 'margin-left:1em; ' : '') . 
		(($divider != '' && $text_align != 'right') ? "\n\t" . 'margin-right:1em; ' : '') . 
		(($border_radius != '') ? "\n\t" . '-webkit-border-radius:' . esc_attr($border_radius) . '; ' . "\n\t" . 'border-radius:' . esc_attr($border_radius) . '; ' : '') . 
	"\n" . '} ' . "\n\n" . 
	'#cmsmasters_heading_' . esc_attr($unique_id) . ' .cmsmasters_heading, ' . 
	'#cmsmasters_heading_' . esc_attr($unique_id) . ' .cmsmasters_heading a { ' . 
		(($font_family != '') ? "\n\t" . 'font-family:' . $font_family_name . '; ' : '') . 
		(($font_size != '' && $font_size != '0') ? "\n\t" . 'font-size:' . esc_attr($font_size) . 'px; ' : '') . 
		(($line_height != '' && $line_height != '0') ? "\n\t" . 'line-height:' . esc_attr($line_height) . 'px; ' : '') . 
		(($font_weight != 'default') ? "\n\t" . 'font-weight:' . esc_attr($font_weight) . '; ' : '') . 
		(($font_style != 'default') ? "\n\t" . 'font-style:' . esc_attr($font_style) . '; ' : '') . 
		(($color != '') ? "\n\t" . cmsmasters_color_css('color', $color) : '') . 
	"\n" . '} ' . "\n\n" . 
	'#cmsmasters_heading_' . esc_attr($unique_id) . ' .cmsmasters_second_heading, ' . 
	'#cmsmasters_heading_' . esc_attr($unique_id) . ' .cmsmasters_second_heading a { ' . 
		(($heading_field_custom_second_heading_color != '') ? "\n\t" . cmsmasters_color_css('color', $heading_field_custom_second_heading_color) : '') . 
	"\n" . '} ' . "\n\n" . 
	'#cmsmasters_heading_' . esc_attr($unique_id) . ' .cmsmasters_heading a:hover { ' . 
		(($link_color_h != '') ? "\n\t" . cmsmasters_color_css('color', $link_color_h) : '') . 
	"\n" . '} ' . "\n\n" . 
	'#cmsmasters_heading_' . esc_attr($unique_id) . ' .cmsmasters_heading_divider { ' . 
		(($divider != '') ? "\n\t" . 'border-bottom-width:' . esc_attr($divider_height) . 'px; ' : '') . 
		(($divider != '') ? "\n\t" . 'border-bottom-style:' . esc_attr($divider_style) . '; ' : '') . 
		(($divider != '' && $divider_color != '') ? "\n\t" . cmsmasters_color_css('border-bottom-color', $divider_color) : '') . 
		(($divider != '') ? "\n\t" . 'margin-top:-' . esc_attr(round((int) $divider_height / 2)) . 'px; ' : '') . 
	"\n" . '} ' . "\n\n";
	
	
	if ($underline != '') {
		$shortcode_styles .= '#cmsmasters_heading_' . esc_attr($unique_id) . ' .cmsmasters_heading { ' . 
			"\n\t" . 'text-decoration:none; ' . 
			"\n\t" . 'border-bottom-width:' . esc_attr($underline_height) . 'px; ' . 
			"\n\t" . 'border-bottom-style:' . esc_attr($underline_style) . '; ' . 
			($underline_color != '' ? "\n\t" . cmsmasters_color_css('border-bottom-color', $underline_color) : '') . 
		"\n" . '} ' . "\n\n";
	}
	
	
	if ($custom_check != '') {
		$shortcode_styles .= (($width_monitor != '') ? '@media (max-width: ' . esc_attr($width_monitor) . 'px) {' : '') . "\n\n" . 
			'#cmsmasters_heading_' . esc_attr($unique_id) . ' .cmsmasters_heading, ' . 
			'#cmsmasters_heading_' . esc_attr($unique_id) . ' .cmsmasters_heading a { ' . 
				(($custom_font_size != '' && $custom_font_size != '0') ? "\n\t" . 'font-size:' . esc_attr($custom_font_size) . 'px; ' : '') . 
				(($custom_line_height != '' && $custom_line_height != '0') ? "\n\t" . 'line-height:' . esc_attr($custom_line_height) . 'px; ' : '') . 
			"\n" . '} ' . "\n" . 
		"\n" . '} ' . "\n\n";
	}
	
	
$shortcode_styles .= "\n";


$out = $this->cmsmasters_generate_front_css($shortcode_styles);


$out .= '<div id="cmsmasters_heading_' . esc_attr($unique_id) . '" class="cmsmasters_heading_wrap cmsmasters_heading_align_' . esc_attr($text_align) . 
(($divider != '') ? ' cmsmasters_heading_divider_' . esc_attr($divider_type) : '') . 
(($classes != '') ? ' ' . esc_attr($classes) : '') . 
'"' . 
(($animation != '') ? ' data-animation="' . esc_attr($animation) . '"' : '') . 
(($animation != '' && $animation_delay != '') ? ' data-delay="' . esc_attr($animation_delay) . '"' : '') . 
'>' . "\n\t";


if ($divider != '' && $text_align != 'left') {
	$out .= '<span class="cmsmasters_heading_divider_left_wrap"><span class="cmsmasters_heading_divider cmsmasters_heading_divider_left"></span></span>' . "\n";
}


$out .= '<' . esc_attr($type) . ' class="cmsmasters_heading' . 
(($icon != '' && $link == '') ? ' ' . esc_attr($icon) : '') . 
'">';


if ($link != '') {
	$out .= '<a href="' . esc_url($link) . '"' . 
	(($icon != '') ? ' class="' . esc_attr($icon) . '"' : '') . 
	(($target == 'blank') ? ' target="_blank"' : '') . 
	'>';
}


$out .= $content;


if ($custom_second_heading != '') {
	$out .= (($custom_second_heading_text != '') ? '<span class="cmsmasters_second_heading">' . esc_html($custom_second_heading_text) . '</span>' : '') . "\n\t";
}


if ($link != '') {
	$out .= '</a>';
}


$out .= '</' . esc_attr($type) . '>' . "\n";


if ($heading_field_custom_button != '' && $heading_field_custom_button_title != '' && $heading_field_custom_button_link != '') {
	$out .= '<a href="' . esc_url($heading_field_custom_button_link) . '" class="cmsmasters_button button">' . esc_html($heading_field_custom_button_title) . '</a>';
}


if ($divider != '' && $text_align != 'right') {
	$out .= '<span class="cmsmasters_heading_divider_right_wrap"><span class="cmsmasters_heading_divider cmsmasters_heading_divider_right"></span></span>' . "\n";
}


$out .= '</div>';


print $out;