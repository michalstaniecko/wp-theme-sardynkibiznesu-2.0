<?php
/**
 * @package 	WordPress
 * @subpackage 	Maga Zine
 * @version 	1.0.4
 * 
 * Content Composer Toggles Shortcode
 * Created by CMSMasters
 * 
 */


extract(shortcode_atts($new_atts, $atts));


$unique_id = $shortcode_id;


$this->toggles_atts = array(
	'sort_toggles' => 		array(), 
	'toggle_active' => 		(int) $active, 
	'toggle_counter' => 	0 
);


$toggles_filter = '';

$toggles = do_shortcode($content);

$out = '';

if ($primary_color != '' || $secondary_color != '') {
	$out .= '<style type="text/css"> ' . "\n" . 
		'#cmsmasters_toggles_' . esc_attr($unique_id) . ' .current_toggle .cmsmasters_toggle_title { ' . 
			(($primary_color != '') ? "\n\t" . cmsmasters_color_css('color', $primary_color) : '') . 
			(($secondary_color != '' & $mode != "accordion") ? "\n\t" . cmsmasters_color_css('background-color', $secondary_color) : '') . 
			(($secondary_color != '') ? "\n\t" . cmsmasters_color_css('border-color', $secondary_color) : '') . 
		"\n" . '} ' . "\n\n" . 
		'#cmsmasters_toggles_' . esc_attr($unique_id) . ' .current_toggle .cmsmasters_toggle_title a { ' . 
			(($primary_color != '') ? "\n\t" . cmsmasters_color_css('color', $primary_color) : '') . 
		"\n" . '} ' . "\n\n" . 
		'#cmsmasters_toggles_' . esc_attr($unique_id) . ' .cmsmasters_toggle_title:hover { ' . 
			(($primary_color != '') ? "\n\t" . cmsmasters_color_css('color', $primary_color) : '') . 
			(($secondary_color != '' & $mode != "accordion") ? "\n\t" . cmsmasters_color_css('background-color', $secondary_color) : '') . 
			(($secondary_color != '') ? "\n\t" . cmsmasters_color_css('border-color', $secondary_color) : '') . 
		"\n" . '} ' . "\n\n" . 
		'#cmsmasters_toggles_' . esc_attr($unique_id) . ' .cmsmasters_toggle_title:hover a { ' . 
			(($primary_color != '') ? "\n\t" . cmsmasters_color_css('color', $primary_color) : '') . 
		"\n" . '} ' . "\n\n" . 
	'</style>' . "\n";
}

if ($sort == 'true') {
	$toggles_filter = '<div class="cmsmasters_toggles_filter">' . "\n\t" . 
		'<a href="#" data-key="all" title="' . esc_attr__('All', 'top-magazine') . '" class="current_filter">' . esc_attr__('All', 'top-magazine') . '</a>' . "\n";
	
	foreach ($this->toggles_atts['sort_toggles'] as $sort_toggle_key => $sort_toggle_value) {
		$toggles_filter .= "\t" . '<a href="#" data-key="' . $sort_toggle_key . '" title="' . esc_attr($sort_toggle_value) . '">' . esc_attr($sort_toggle_value) . '</a>' . "\n";
	}
	
	$toggles_filter .= '</div>';
}


$out .= '<div id="cmsmasters_toggles_' . esc_attr($unique_id) . '" class="cmsmasters_toggles toggles_mode_' . esc_attr($mode) . 
(($classes != '') ? ' ' . esc_attr($classes) : '') . 
'"' . 
(($animation != '') ? ' data-animation="' . esc_attr($animation) . '"' : '') . 
(($animation != '' && $animation_delay != '') ? ' data-delay="' . esc_attr($animation_delay) . '"' : '') . 
'>' . 
	$toggles_filter . "\n" . 
	$toggles . 
'</div>';

print $out;