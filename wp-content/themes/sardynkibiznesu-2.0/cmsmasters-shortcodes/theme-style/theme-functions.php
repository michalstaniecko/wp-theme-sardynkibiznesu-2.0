<?php
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version 	1.0.7
 * 
 * Theme Functions
 * Created by CMSMasters
 * 
 */


/* Load Framework Parts */
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/admin/theme-settings.php');
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/admin/theme-options.php');

require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/class/theme-widgets.php');

require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/function/single-comment.php');
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/function/theme-colors-primary.php');
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/function/theme-colors-secondary.php');
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/function/theme-fonts.php');
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/function/template-functions.php');
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/function/template-functions-post.php');
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/function/template-functions-project.php');
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/function/template-functions-profile.php');
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/function/template-functions-single.php');
require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/function/template-functions-shortcodes.php');


if (CMSMASTERS_CONTENT_COMPOSER && class_exists('Cmsmasters_Content_Composer')) {
	require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/cmsmasters-c-c/cmsmasters-c-c-theme-functions.php');
	
	require_once(get_template_directory() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/cmsmasters-c-c/cmsmasters-c-c-theme-shortcodes.php');
}



/* Scripts for Admin */
function top_magazine_theme_admin_scripts() {
	wp_enqueue_script('cmsmasters-theme-settings-toggle', get_template_directory_uri() . '/theme-framework/theme-style' . CMSMASTERS_THEME_STYLE . '/admin/js/theme-settings-toggle.js', array('jquery'), '1.0.0', true);
	
	wp_localize_script('cmsmasters-theme-settings-toggle', 'cmsmasters_theme_settings', array( 
		'shortname' => 	'top-magazine' 
	));
}

add_action('admin_enqueue_scripts', 'top_magazine_theme_admin_scripts');



/* Register theme JS Scripts */
function top_magazine_register_theme_js_scripts() {
	$cmsmasters_option = top_magazine_get_global_options();
	
	
	$cmsmasters_localize_array = array(
		'primary_color' => 	$cmsmasters_option['top-magazine' . '_default_link'] 
	);
	
	wp_localize_script('top-magazine-theme-script', 'cmsmasters_theme_script', $cmsmasters_localize_array);
}

add_action('wp_enqueue_scripts', 'top_magazine_register_theme_js_scripts');



/* Register Theme Navigations */
register_nav_menus(array( 
    'primary' => 	esc_html__('Primary Navigation', 'top-magazine'), 
    'footer' => 	esc_html__('Footer Navigation', 'top-magazine'), 
	'top_line' => 	esc_html__('Top Line Navigation', 'top-magazine') 
));



/* Register Showing Home Page on Default WordPress Pages Menu */
function cmsmasters_custom_mega_menu_item_output($args) {
	$shcd_args = $args['args'];
	
	$shcd_attrs = $args['attrs'];
	
	$shcd_depth = $args['depth'];
	
	$shcd_item = $args['item'];
	
	$shcd_cols_count = $args['cols_count'];
	
	
	$item_output = '';
	
	
	if (!empty($shcd_item->color)) {
		$shcd_attrs .= ' style="color:' . $shcd_item->color . ';"';
	}
	
	
	$item_output .= $shcd_args->before . 
		'<a' . $shcd_attrs . '>';
	
	
	$item_output .= $shcd_args->link_before;
	
	
	if ( 
		($shcd_depth <= 1 && empty($shcd_item->hide_text)) || 
		($shcd_depth == 0 && !empty($shcd_item->hide_text) && !empty($shcd_item->icon)) || 
		($shcd_depth == 1 && $shcd_cols_count == NULL && !empty($shcd_item->hide_text)) || 
		($shcd_depth == 1 && $shcd_cols_count != NULL && !empty($shcd_item->hide_text) && !empty($shcd_item->icon)) || 
		($shcd_depth > 1) 
	) {
		$item_output .= '<span class="nav_title' . (!empty($shcd_item->icon) ? ' ' . $shcd_item->icon : '') . '">';
		
			if (empty($shcd_item->hide_text)) {
				$item_output .= apply_filters('the_title', $shcd_item->title, $shcd_item->ID);
			}
			
		$item_output .= '</span>';
		
		
		if (!empty($shcd_item->tag)) {
			$item_output .= '<span class="nav_tag">' . esc_attr($shcd_item->tag) . '</span>';
		}
		
		
		if ( 
			($shcd_depth == 0 && !empty($shcd_item->subtitle)) || 
			($shcd_depth == 1 && $shcd_cols_count != NULL && !empty($shcd_item->subtitle)) 
		) {
			$item_output .= '<span class="nav_subtitle">' . 
				$shcd_item->subtitle . 
			'</span>';
		}
	}
	
	
	$item_output .= $shcd_args->link_after . 
		'</a>' . 
	$shcd_args->after;
	
	
	return $item_output;
}

add_filter('cmsmasters_mega_item_output', 'cmsmasters_custom_mega_menu_item_output');



/* Register wp_nav_menu() Fallback Function */
function top_magazine_fallback_menu($args) {
	$cmsmasters_option = top_magazine_get_global_options();
	
	
	echo '<div class="navigation_wrap">' . "\n" . 
		'<ul id="navigation" class="' . 
			'navigation ' . 
			($cmsmasters_option['top-magazine' . '_header_styles'] == 'default' ? 'mid_nav' : 'bot_nav') . 
		'">' . "\n";
	
	
	wp_list_pages(array( 
		'title_li' => '', 
		'link_before' => '<span class="nav_item_wrap">', 
		'link_after' => '</span>' 
	));
	
	
	echo '</ul>' . "\r" . 
	'</div>' . "\n";
}



/* Get Social Icons Styles Function */
function top_magazine_theme_profiles_subtitles_styles() {
	$cmsmasters_option = top_magazine_get_global_options();
	
	$out = '';
	
	$profiles = new WP_Query(array( 
		'post_type' => 		'profile'
	));
	
	$profiles = $profiles->posts;
	
	if (!empty($profiles) && !is_wp_error($profiles)) {
		
		foreach ($profiles as $profile) {
			
			$profile_id = $profile->ID;
			
			$profile_subtitle_color = get_post_meta($profile_id, 'cmsmasters_profile_subtitle_color', true);
			
			
			if (isset($profile_subtitle_color) && $profile_subtitle_color != '') {
				$out .= "
				
	.cmsmasters_profile #post-{$profile_id} .cmsmasters_profile_subtitle {
		" . cmsmasters_color_css('background-color', $profile_subtitle_color) . "
	}
";
			}
		}
	}
	
	
	return $out;
}



/* Get Posts Custom Small Thumbnail Function */
function top_magazine_thumb_custom($cmsmasters_id, $type = 'post-thumbnail', $link = '', $show = true) {
	$args = array( 
		'alt' => cmsmasters_title($cmsmasters_id, false), 
		'title' => cmsmasters_title($cmsmasters_id, false) 
	);
	
	if (has_post_thumbnail($cmsmasters_id)) {
		$out = '<figure class="cmsmasters_img_wrap">'; 
	
			$out .= get_the_post_thumbnail($cmsmasters_id, (($type) ? $type : 'full'), $args);
	
		$out .= '</figure>';
	} else {
		$out = '';
	}
	
	
	if ($show) {
		print $out;
	} else {
		return $out;
	}
}



/* Single Project and Profile Layout */
function top_magazine_single_project_layout($cmsmasters_layout) {
	if (
		is_singular('project') || 
		is_singular('profile') 
	) {
		$cmsmasters_layout = 'fullwidth';
	}
	
	
	return $cmsmasters_layout;
}

add_filter('cmsmasters_theme_page_layout_filter', 'top_magazine_single_project_layout');



/* Search Form */
function top_magazine_search_form($form) {
	$form = "<div class=\"search_bar_wrap\">
		<form method=\"get\" action=\"" . esc_url(home_url('/')) . "\">
			<p class=\"search_field\">
				<input name=\"s\" placeholder=\"" . esc_attr__('Search...', 'top-magazine') . "\" value=\"\" type=\"search\" />
			</p>
			<p class=\"search_button\">
				<button type=\"submit\" class=\"cmsmasters_theme_icon_search\"></button>
			</p>
		</form>
	</div>";
	
	
	return $form;
}

add_filter('get_search_form', 'top_magazine_search_form');

