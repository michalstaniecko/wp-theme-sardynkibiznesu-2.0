<?php 
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version		1.0.7
 * 
 * Template Functions for Shortcodes
 * Created by CMSMasters
 * 
 */


/**
 * Posts Slider Functions
 */

/* Get Posts Slider Heading Function */
function top_magazine_slider_post_heading($cmsmasters_id, $type = 'post', $tag = 'h1', $link_redirect = false, $link_url = false, $show = true, $link_target = false) { 
	if ($type == 'post') {
		$out = '<header class="cmsmasters_slider_post_header entry-header">' . 
			'<' . esc_html($tag) . ' class="cmsmasters_slider_post_title entry-title">' . 
				'<a href="' . esc_url(get_permalink()) . '">' . esc_html(strip_tags(get_the_title($cmsmasters_id) ? get_the_title($cmsmasters_id) : $cmsmasters_id)) . '</a>' . 
			'</' . esc_html($tag) . '>' . 
		'</header>';
	} elseif ($type == 'project') {
		$out = '<header class="cmsmasters_slider_project_header entry-header">' . 
			'<' . esc_html($tag) . ' class="cmsmasters_slider_project_title entry-title">' . 
				'<a href="' . (($link_redirect == 'true' && $link_url != '') ? esc_url($link_url) : esc_url(get_permalink())) . '"' . ($link_target == 'true' ? ' target="_blank"' : '') . '>' . esc_html(strip_tags(get_the_title($cmsmasters_id) ? get_the_title($cmsmasters_id) : $cmsmasters_id)) . '</a>' . 
			'</' . esc_html($tag) . '>' . 
		'</header>';
	}
	
	
	if ($show) {
		echo wp_kses_post($out);
	} else {
		return wp_kses_post($out);
	}
}



/* Get Posts Slider Content/Excerpt Function */
function top_magazine_slider_post_exc_cont($type = 'post', $show = true) {
	if ($type == 'post') {
		$out = cmsmasters_divpdel('<div class="cmsmasters_slider_post_content entry-content">' . "\n" . 
			wpautop(top_magazine_excerpt(20, false)) . 
		'</div>' . "\n");
	} elseif ($type == 'project') {
		$out = cmsmasters_divpdel('<div class="cmsmasters_slider_project_content entry-content">' . "\n" . 
			wpautop(top_magazine_excerpt(20, false)) . 
		'</div>' . "\n");
	}
	
	
	if ($show) {
		print $out;
	} else {
		return $out;
	}
}



/* Check Posts Slider Content/Excerpt Not Empty Function */
function top_magazine_slider_post_check_exc_cont($type = 'post') {
	$exc = top_magazine_slider_post_exc_cont($type, false);
	
	$no_tags_exc = strip_tags($exc);
	
	$trim_exc = trim($no_tags_exc);
	
	
	if ($trim_exc != '') {
		return true;
	} else {
		return false;
	}
}



/* Get Posts Slider Date Function */
function top_magazine_get_slider_post_date($type = 'post', $show = true) {
	if ($type == 'post') {
		$out = '<span class="cmsmasters_slider_post_date">' . 
			'<abbr class="published" title="' . esc_attr(get_the_date()) . '">' . 
				esc_html(get_the_date()) . 
			'</abbr>' . 
			'<abbr class="dn date updated" title="' . esc_attr(get_the_modified_date()) . '">' . 
				esc_html(get_the_modified_date()) . 
			'</abbr>' . 
		'</span>';
	} elseif ($type == 'project') {
		$out = '<span class="cmsmasters_slider_project_date">' . 
			'<abbr class="published" title="' . esc_attr(get_the_date()) . '">' . 
				esc_html(get_the_date()) . 
			'</abbr>' . 
			'<abbr class="dn date updated" title="' . esc_attr(get_the_modified_date()) . '">' . 
				esc_html(get_the_modified_date()) . 
			'</abbr>' . 
		'</span>';
	}
	
	
	if ($show) {
		echo wp_kses_post($out);
	} else {
		return wp_kses_post($out);
	}
}



/* Get Posts Slider Author Function */
function top_magazine_get_slider_post_author($type = 'post', $show = true) {
	if ($type == 'post') {
		$out = '<span class="cmsmasters_slider_post_author">' . 
			esc_html__('By', 'top-magazine') . ' ' . 
			'<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '" title="' . esc_attr__('Posts by', 'top-magazine') . ' ' . esc_attr(get_the_author_meta('display_name')) . '" class="vcard author"><span class="fn" rel="author">' . esc_html(get_the_author_meta('display_name')) . '</span></a>' . 
		'</span>';
	} elseif ($type == 'project') {
		$out = '<span class="cmsmasters_slider_project_author">' . 
			esc_html__('By', 'top-magazine') . ' ' . 
			'<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '" title="' . esc_attr__('Projects by', 'top-magazine') . ' ' . esc_attr(get_the_author_meta('display_name')) . '" class="vcard author"><span class="fn" rel="author">' . esc_html(get_the_author_meta('display_name')) . '</span></a>' . 
		'</span>';
	}
	
	
	if ($show) {
		echo wp_kses_post($out);
	} else {
		return wp_kses_post($out);
	}
}



/* Get Posts Slider Category Function */
function top_magazine_get_slider_post_category($cmsmasters_id, $taxonomy, $type = 'post', $show = true) {
	$out = '';
	
	
	if (get_the_terms($cmsmasters_id, $taxonomy)) {
		if ($type == 'post') {
			$out = '<span class="cmsmasters_slider_post_category">' . 
				top_magazine_get_the_category_list($cmsmasters_id, $taxonomy, '') . 
			'</span>';
		} elseif ($type == 'project') {
			$out = '<span class="cmsmasters_slider_project_category">' . 
				top_magazine_get_the_category_list($cmsmasters_id, $taxonomy, '') . 
			'</span>';
		}
	}
	
	
	if ($show) {
		echo wp_kses_post($out);
	} else {
		return wp_kses_post($out);
	}
}



/* Get Posts Slider Like Function */
function top_magazine_slider_post_like($type = 'post', $show = true) {
	$out = '';
	
	
	if ($type == 'post') {
		$out = cmsmasters_like('cmsmasters_slider_post_likes');
	} elseif ($type == 'project') {
		$out = cmsmasters_like('cmsmasters_slider_project_likes');
	}
	
	
	if ($show) {
		print $out;
	} else {
		return $out;
	}
}



/* Get Posts Slider View Function */
function top_magazine_slider_post_views($type = 'post', $show = true) {
	if ($type == 'post') {
		$out = cmsmastersView();
	}
	
	
	if ($show) {
		print $out;
	} else {
		return $out;
	}
}



/* Get Posts Slider Comments Function */
function top_magazine_get_slider_post_comments($type = 'post', $show = true) {
	$out = '';
	
	
	if (comments_open()) {
		if ($type == 'post') {
			$out = top_magazine_get_comments('cmsmasters_slider_post_comments');
		} elseif ($type == 'project') {
			$out = top_magazine_get_comments('cmsmasters_slider_project_comments');
		}
	}
	
	
	if ($show) {
		print $out;
	} else {
		return $out;
	}
}



/* Get Posts Slider More Button/Link Function */
function top_magazine_slider_post_more($cmsmasters_id, $show = true) {
	$cmsmasters_post_read_more = get_post_meta($cmsmasters_id, 'cmsmasters_post_read_more', true);
	
	
	if ($cmsmasters_post_read_more == '') {
		$cmsmasters_post_read_more = esc_attr__('Read More...', 'top-magazine');
	}
	
	
	$out = '<a class="cmsmasters_slider_post_read_more" href="' . esc_url(get_permalink($cmsmasters_id)) . '">' . esc_html($cmsmasters_post_read_more) . '</a>';
	
	
	if ($show) {
		echo wp_kses_post($out);
	} else {
		return wp_kses_post($out);
	}
}

