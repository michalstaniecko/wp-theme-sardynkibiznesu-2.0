<?php
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version 	1.0.5
 * 
 * Theme Content Composer Functions
 * Created by CMSMasters
 * 
 */


/* Register JS Scripts */
function top_magazine_theme_register_c_c_scripts() {
	global $pagenow;
	
	
	if ( 
		$pagenow == 'post-new.php' || 
		($pagenow == 'post.php' && isset($_GET['post']) && get_post_type($_GET['post']) != 'attachment') 
	) {
		wp_enqueue_script('top-magazine-composer-shortcodes-extend', get_template_directory_uri() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/cmsmasters-c-c/js/cmsmasters-c-c-theme-extend.js', array('cmsmasters_composer_shortcodes_js'), '1.0.0', true);
		
		wp_localize_script('top-magazine-composer-shortcodes-extend', 'cmsmasters_theme_shortcodes', array( 
			'heading_field_custom_check' => 				esc_attr__('Set Custom Font Size for Small Screens', 'top-magazine'), 
			'heading_field_width_monitor' => 				esc_attr__('Monitor Width', 'top-magazine'), 
			'heading_field_custom_font_size' => 			esc_attr__('Custom Font Size', 'top-magazine'), 
			'heading_field_size_zero_note' => 				esc_attr__('number, in pixels (default value if empty or 0)', 'top-magazine'), 
			'heading_field_custom_line_height' => 			esc_attr__('Custom Line Height', 'top-magazine'), 
			'heading_field_custom_second_heading' => 		esc_attr__('Second Heading', 'top-magazine'), 
			'heading_field_custom_second_heading_text' => 	esc_attr__('Second Heading Text', 'top-magazine'), 
			'heading_field_custom_second_heading_color' => 	esc_attr__('Second Heading Color', 'top-magazine'), 
			'heading_field_custom_button' => 				esc_attr__('Heading Button', 'top-magazine'), 
			'heading_field_custom_button_title' =>			esc_attr__('Heading Button Label', 'top-magazine'), 
			'heading_field_custom_button_title_descr' =>	esc_attr__('Enter heading button label here', 'top-magazine'), 
			'heading_field_custom_button_link' =>			esc_attr__('Heading Button Link', 'top-magazine'),
			'heading_field_custom_button_link_descr' =>		esc_attr__('Enter heading button link here', 'top-magazine'), 
			'heading_field_divider_lenght_bottom' =>		esc_attr__('Long Bottom', 'top-magazine'), 
			'choice_view' => 								esc_attr__('Views', 'top-magazine'), 
			'blog_field_featured'  => 						esc_attr__('Featured Post', 'top-magazine'), 
			'blog_field_featured_descr'  =>					esc_attr__('First post will be on fullwidth', 'top-magazine'), 
			'blog_field_featured_descr_note'  =>			esc_attr__('Does not work with the timeline and puzzle', 'top-magazine'), 
			'toggles_field_primary_color'  =>				esc_attr__('Custom Active Toggle Primary Color', 'top-magazine'), 
			'toggles_field_primary_color_descr'  =>			esc_attr__('Color for text', 'top-magazine'), 
			'toggles_field_secondary_color'  =>				esc_attr__('Custom Active Toggle Secondary Color', 'top-magazine'), 
			'toggles_field_secondary_color_descr'  =>		esc_attr__('Color for background or border', 'top-magazine'), 
			'posts_slider_controls_enable_title'   =>		esc_attr__('Controls', 'top-magazine')
		));
	}
}

add_action('admin_enqueue_scripts', 'top_magazine_theme_register_c_c_scripts');


// Special Heading Shortcode Attributes Filter
add_filter('cmsmasters_custom_heading_atts_filter', 'cmsmasters_custom_heading_atts');

function cmsmasters_custom_heading_atts() {
	return array( 
		'shortcode_id' => 								'', 
		'type' => 										'h1', 
		'font_family' => 								'', 
		'font_size' => 									'', 
		'line_height' => 								'', 
		'font_weight' => 								'400', 
		'font_style' => 								'normal', 
		'icon' => 										'', 
		'text_align' => 								'left', 
		'color' => 										'', 
		'bg_color' => 									'', 
		'custom_second_heading' => 						'', 
		'custom_second_heading_text' => 				'', 
		'heading_field_custom_second_heading_color' =>	'', 
		'heading_field_custom_button' => 				'', 
		'heading_field_custom_button_title' => 			'', 
		'heading_field_custom_button_link' => 			'', 
		'link' => 										'', 
		'target' => 									'', 
		'link_color_h' => 								'', 
		'margin_top' => 								'0', 
		'margin_bottom' => 								'0', 
		'border_radius' => 								'', 
		'divider' => 									'', 
		'divider_type' => 								'short', 
		'divider_height' => 							'1', 
		'divider_style' => 								'solid', 
		'divider_color' => 								'', 
		'underline' => 									'', 
		'underline_height' => 							'1', 
		'underline_style' => 							'solid', 
		'underline_color' => 							'', 
		'custom_check' =>  								'', 
		'width_monitor' =>  							'767', 
		'custom_font_size' => 							'', 
		'custom_line_height' => 						'', 
		'animation' => 									'', 
		'animation_delay' => 							'', 
		'classes' => 									'' 
	);
}


// Blog Shortcode Attributes Filter
add_filter('cmsmasters_blog_atts_filter', 'cmsmasters_blog_atts');

function cmsmasters_blog_atts() {
	return array( 
		'shortcode_id' => 						'', 
		'orderby' => 							'date', 
		'order' => 								'DESC', 
		'count' => 								'12', 
		'categories' => 						'', 
		'layout' => 							'standard', 
		'layout_mode' => 						'', 
		'columns' => 							'', 
		'blog_field_featured_post' => 			'', 
		'metadata' => 							'', 
		'filter' => 							'', 
		'filter_text' => 						'', 
		'filter_cats_text' => 					'', 
		'pagination' => 						'pagination', 
		'more_text' => 							'', 
		'classes' => 							'' 
	);
}


// Toggles Attributes Filter
add_filter('cmsmasters_toggles_atts_filter', 'cmsmasters_toggles_atts');

function cmsmasters_toggles_atts() {
	return array( 
		'shortcode_id' => 		'', 
		'mode' => 				'toggle', 
		'active' => 			'', 
		'sort' => 				'', 
		'primary_color' => 		'', 
		'secondary_color' => 	'', 
		'animation' => 			'', 
		'animation_delay' => 	'', 
		'classes' => 			'' 
	);
}


// Posts Slider Attributes Filter
add_filter('cmsmasters_posts_slider_atts_filter', 'cmsmasters_posts_slider_atts');

function cmsmasters_posts_slider_atts() {
	return array( 
		'shortcode_id' => 			'', 
		'orderby' => 				'', 
		'order' => 					'', 
		'post_type' => 				'', 
		'blog_categories' => 		'', 
		'portfolio_categories' => 	'', 
		'columns' => 				'', 
		'controls' =>				'', 
		'amount' => 				'', 
		'count' => 					'1000', 
		'pause' => 					'', 
		'speed' => 					'', 
		'blog_metadata' => 			'title,date,categories,comments,likes,views,more', 
		'portfolio_metadata' => 	'title,categories,likes', 
		'animation' => 				'', 
		'animation_delay' => 		'', 
		'classes' => 				'' 
	);
}

