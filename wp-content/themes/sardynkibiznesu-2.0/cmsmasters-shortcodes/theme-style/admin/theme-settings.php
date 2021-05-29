<?php 
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version		1.0.4
 * 
 * Theme Admin Settings
 * Created by CMSMasters
 * 
 */


/* General Settings */
function top_magazine_theme_options_general_fields($options, $tab) {	
	$options_new = array();

	if ($tab == 'header') {
		foreach ($options as $option) {
			if ($option['id'] == 'top-magazine' . '_header_add_cont') {
				$options_new[] = array( 
					'section' => 'header_section', 
					'id' => 'top-magazine' . '_header_social', 
					'title' => esc_html__('Header Social Icons', 'top-magazine'), 
					'desc' => esc_html__('show', 'top-magazine'), 
					'type' => 'checkbox', 
					'std' => 0 
				);
				
				
				$options_new[] = $option;
			} else if ($option['id'] == 'top-magazine' . '_header_add_cont_cust_html') {
				$options_new[] = array( 
					'section' => 'header_section', 
					'id' => 'top-magazine' . '_header_custom_html', 
					'title' => esc_html__('Header Custom HTML', 'top-magazine'), 
					'desc' => esc_html__('show', 'top-magazine'), 
					'type' => 'checkbox', 
					'std' => 0 
				);
				
				
				$options_new[] = $option;
			} else {
				$options_new[] = $option;
			}
		}
	} else {
		$options_new = $options;
	}
	
	
	return $options_new;
}

add_filter('cmsmasters_options_general_fields_filter', 'top_magazine_theme_options_general_fields', 10, 2);



/* Single Settings */
function top_magazine_theme_options_single_fields($options, $tab) {
	
	$options_new = array();

	if ($tab == 'post') {
		foreach ($options as $option) {
			if ($option['id'] == 'top-magazine' . '_blog_post_nav_box') {
				$options_new[] = array( 
					'section' => 'post_section', 
					'id' => 'top-magazine' . '_blog_post_view', 
					'title' => esc_html__('Post Views', 'top-magazine'), 
					'desc' => esc_html__('show', 'top-magazine'), 
					'type' => 'checkbox', 
					'std' => 1 
				);
				
				
				$options_new[] = $option;
			} else {
				$options_new[] = $option;
			}
		}
	} else {
		$options_new = $options;
	}
	
	
	return $options_new;
}

add_filter('cmsmasters_options_single_fields_filter', 'top_magazine_theme_options_single_fields', 10, 2);


