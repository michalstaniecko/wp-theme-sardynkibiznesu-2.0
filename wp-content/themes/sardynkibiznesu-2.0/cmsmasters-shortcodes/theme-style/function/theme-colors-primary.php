<?php
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version 	1.0.7
 * 
 * Theme Primary Color Schemes Rules
 * Created by CMSMasters
 * 
 */


function top_magazine_theme_colors_primary() {
	$cmsmasters_option = top_magazine_get_global_options();
	
	
	$cmsmasters_color_schemes = cmsmasters_color_schemes_list();
	
	
	$custom_css = "/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version 	1.0.7
 * 
 * Theme Primary Color Schemes Rules
 * Created by CMSMasters
 * 
 */

";
	
	
	foreach ($cmsmasters_color_schemes as $scheme => $title) {
		$rule = (($scheme != 'default') ? "html .cmsmasters_color_scheme_{$scheme} " : '');
		
		
		$custom_css .= "
/***************** Start {$title} Color Scheme Rules ******************/
	
	/* Start Main Content Font Color */
	" . (($scheme == 'default') ? "body," : '') . "
	" . (($scheme != 'default') ? ".cmsmasters_color_scheme_{$scheme}," : '') . "
	" . (($scheme == 'default') ? ".headline_outer, " : '') . "
	{$rule}input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]),
	{$rule}textarea,
	{$rule}select,
	{$rule}option, 
	{$rule}#page .cmsmasters_social_icon, 
	{$rule}.cmsmasters_open_project .project_details_title, 
	{$rule}.cmsmasters_open_project .project_features_title, 
	{$rule}.cmsmasters_open_profile .cmsmasters_profile_subtitle, 
	{$rule}.cmsmasters_open_profile .profile_details_title, 
	{$rule}.cmsmasters_open_profile .profile_features_title, 
	{$rule}.cmsmasters_open_profile .profile_social_icons_title, 
	{$rule}.cmsmasters_pricing_table .cmsmasters_period, 
	{$rule}.cmsmasters_quotes .cmsmasters_quote_subtitle_wrap *, 
	{$rule}.cmsmasters_quotes_slider .owl-buttons > div:hover, 
	{$rule}.cmsmasters_sitemap_wrap a, 
	{$rule}.cmsmasters_post_timeline a .cmsmasters_mon_year {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_color']) . "
	}
	
	" . (($scheme == 'default') ? "#slide_top:hover, " : '') . "
	{$rule}#page .cmsmasters_project_puzzle .cmsmasters_project_category a, 
	{$rule}#page .cmsmasters_posts_slider .cmsmasters_slider_project .cmsmasters_slider_project_inner_wrap a {
		background-color:transparent;
	}
	
	{$rule}input::-webkit-input-placeholder {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_color']) . "
	}
	
	{$rule}input:-moz-placeholder {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_color']) . "
	}
	/* Finish Main Content Font Color */
	
	
	/* Start Primary Color */
	" . (($scheme == 'default') ? "#slide_top:hover, " : '') . "
	{$rule}a,
	{$rule}h1 a:hover,
	{$rule}h2 a:hover,
	{$rule}h3 a:hover,
	{$rule}h4 a:hover,
	{$rule}h5 a:hover,
	{$rule}h6 a:hover,
	{$rule}.color_2,
	{$rule}.cmsmasters_icon_wrap a .cmsmasters_simple_icon,
	{$rule}#header .cmsmasters_header_search_but:hover, 
	{$rule}#page .cmsmasters_social_icon:hover, 
	{$rule}.cmsmasters_likes a:hover:before, 
	{$rule}.cmsmasters_likes .active:before, 
	{$rule}.cmsmasters_comments a:hover:before, 
	{$rule}.cmsmasters_comments .active:before, 
	{$rule}.cmsmasters_wrap_pagination a:hover, 
	{$rule}.sticky .cmsmasters_post_cont_wrap:before, 
	{$rule}.cmsmasters_post_timeline a:hover .cmsmasters_day, 
	{$rule}.cmsmasters_post_timeline .cmsmasters_post_info a span, 
	{$rule}.share_posts a:hover, 
	{$rule}.post_nav a:hover .post_nav_title, 
	{$rule}.about_author .about_author_cont a:hover, 
	{$rule}.cmsmasters_pings_list .comment-body span a:hover, 
	{$rule}.cmsmasters_comment_item .cmsmasters_comment_item_cont_info a:hover, 
	{$rule}#page .cmsmasters_open_project .cmsmasters_project_category a, 
	{$rule}#page .cmsmasters_open_profile .cmsmasters_profile_category a, 
	{$rule}#page .profile_social_icons_list a:hover, 
	{$rule}.cmsmasters_counters .cmsmasters_counter_wrap .cmsmasters_counter .cmsmasters_counter_inner:before, 
	{$rule}.cmsmasters_toggles.toggles_mode_accordion .current_toggle .cmsmasters_toggle_title, 
	{$rule}.cmsmasters_toggles.toggles_mode_accordion .current_toggle .cmsmasters_toggle_title a, 
	{$rule}.cmsmasters_twitter_wrap .twr_icon, 
	{$rule}.cmsmasters_twitter_wrap .cmsmasters_twitter_item_content a, 
	{$rule}.cmsmasters_quotes .cmsmasters_quote_subtitle_wrap a:hover, 
	{$rule}.cmsmasters_sitemap_wrap a:hover, 
	{$rule}.widget_pages ul li a:hover, 
	{$rule}.widget_categories ul li a:hover, 
	{$rule}.widget_archive ul li a:hover, 
	{$rule}.widget_meta ul li a:hover, 
	{$rule}.widget_recent_comments ul li a:hover, 
	{$rule}.widget_recent_entries ul li a:hover, 
	{$rule}#wp-calendar #today, 
	{$rule}.widget_custom_contact_info_entries > span:before, 
	{$rule}.widget_custom_contact_info_entries .adress_wrap:before, 
	{$rule}.widget_nav_menu ul > li a:hover, 
	{$rule}.widget_custom_twitter_entries .tweet_text a:hover, 
	{$rule}.cmsmasters_wrap_more_items.cmsmasters_loading:before,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_top:before,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_heading_left .icon_box_heading:before,
	{$rule}.cmsmasters_icon_list_items.cmsmasters_color_type_icon .cmsmasters_icon_list_icon:before,
	{$rule}.cmsmasters_stats.stats_mode_bars.stats_type_horizontal .cmsmasters_stat_wrap .cmsmasters_stat .cmsmasters_stat_inner:before, 
	{$rule}.cmsmasters_stats.stats_mode_circles .cmsmasters_stat_wrap .cmsmasters_stat .cmsmasters_stat_inner:before, 
	{$rule}.bypostauthor > .comment-body .alignleft:before,
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap > li > a:hover,
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap > li > ul > li > a:hover,
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap_category > li > a:hover,
	{$rule}.cmsmasters_attach_img .cmsmasters_attach_img_edit a, 
	{$rule}.cmsmasters_attach_img .cmsmasters_attach_img_meta a, 
	{$rule}.cmsmasters_tabs .tab_comments li > a:hover {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	{$rule}.cmsmasters_button:hover, 
	{$rule}.button:hover, 
	{$rule}input[type=submit]:hover, 
	{$rule}input[type=button]:hover, 
	{$rule}button:hover, 
	{$rule}table thead th, 
	{$rule}table thead td, 
	{$rule}.owl-pagination .owl-page:hover, 
	{$rule}.owl-pagination .owl-page.active, 
	{$rule}.cmsmasters_items_filter_wrap .current a, 
	{$rule}.cmsmasters_items_filter_wrap .current, 
	{$rule}.cmsmasters_post_tags > a:hover, 
	{$rule}.cmsmasters_prev_arrow:hover, 
	{$rule}.cmsmasters_next_arrow:hover, 
	{$rule}a.cmsmasters_cat_color, 
	{$rule}.cmsmasters_project_puzzle .project_inner:before, 
	{$rule}.cmsmasters_profile .cmsmasters_profile_subtitle, 
	{$rule}.cmsmasters_contact_form .wpcf7-submit, 
	{$rule}.cmsmasters_contact_form .cmsmasters_button, 
	{$rule}.cmsmasters_icon_list_items .cmsmasters_icon_list_item .cmsmasters_icon_list_icon, 
	{$rule}.cmsmasters_toggles .cmsmasters_toggles_filter a:hover, 
	{$rule}.cmsmasters_toggles.toggles_mode_toggle .current_toggle .cmsmasters_toggle_title, 
	{$rule}.cmsmasters_tabs .cmsmasters_tabs_list_item.current_tab a, 
	{$rule}.cmsmasters_tabs .cmsmasters_tabs_list_item a:hover, 
	{$rule}.cmsmasters_notice .notice_close:hover, 
	{$rule}.cmsmasters_archive_type .cmsmasters_archive_item_type span, 
	{$rule}.widget_custom_contact_form_entries .button, 
	{$rule}.widget_tag_cloud a:hover, 
	{$rule}.widget_custom_twitter_entries .tweet_time:before, 
	{$rule}.search_bar_wrap .search_button button, 
	{$rule}.cmsmasters_mailpoet_form form .wysija-submit {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	" . (($scheme == 'default') ? "#slide_top, " : '') . "
	{$rule}.cmsmasters_button:hover, 
	{$rule}.button:hover, 
	{$rule}input[type=submit]:hover, 
	{$rule}input[type=button]:hover, 
	{$rule}button:hover, 
	{$rule}.owl-pagination .owl-page.active, 
	{$rule}.cmsmasters_items_filter_wrap .current a, 
	{$rule}.cmsmasters_items_filter_wrap .current, 
	{$rule}.cmsmasters_post_tags > a:hover, 
	{$rule}.cmsmasters_prev_arrow:hover, 
	{$rule}.cmsmasters_next_arrow:hover, 
	{$rule}.cmsmasters_contact_form .wpcf7-submit, 
	{$rule}.cmsmasters_contact_form .cmsmasters_button, 
	{$rule}.cmsmasters_toggles .cmsmasters_toggles_filter a:hover, 
	{$rule}.cmsmasters_toggles.toggles_mode_toggle .current_toggle .cmsmasters_toggle_title, 
	{$rule}.cmsmasters_tabs .cmsmasters_tabs_list_item.current_tab a, 
	{$rule}.cmsmasters_tabs .cmsmasters_tabs_list_item a:hover, 
	{$rule}.cmsmasters_notice .notice_close:hover, 
	{$rule}.widget_custom_contact_form_entries .button, 
	{$rule}.widget_tag_cloud a:hover, 
	{$rule}.cmsmasters_mailpoet_form form .wysija-submit, 
	{$rule}.search_bar_wrap .search_button button {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	{$rule}.cmsmasters_slider_project .cmsmasters_slider_project_inner_wrap {
		background-color:rgba(" . cmsmasters_color2rgb($cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . ", 0.92);
	}
	
	{$rule}.cmsmasters_header_search_form {
		background-color:rgba(" . cmsmasters_color2rgb($cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . ", 0.95);
	}
	
	" . (($scheme == 'default') ? "#slide_top, " : '') . "
	" . (($scheme == 'default') ? "mark," : '') . "
	" . (($scheme != 'default') ? ".cmsmasters_color_scheme_{$scheme} mark," : '') . "
	{$rule}.cmsmasters_dropcap.type2,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_box_top:before,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_box_left_top:before,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_box_left:before,
	{$rule}.cmsmasters_icon_list_items.cmsmasters_color_type_bg .cmsmasters_icon_list_item .cmsmasters_icon_list_icon,
	{$rule}.cmsmasters_icon_list_items.cmsmasters_color_type_icon .cmsmasters_icon_list_item:hover .cmsmasters_icon_list_icon,
	{$rule}.cmsmasters_stats.stats_mode_bars .cmsmasters_stat_wrap .cmsmasters_stat .cmsmasters_stat_inner, 
	{$rule}.wpcf7 form.wpcf7-form span.wpcf7-list-item input[type=checkbox] + span.wpcf7-list-item-label:after, 
	{$rule}.cmsmasters-form-builder .check_parent input[type=checkbox] + label:after, 
	{$rule}.wpcf7 form.wpcf7-form span.wpcf7-list-item input[type=radio] + span.wpcf7-list-item-label:after, 
	{$rule}.cmsmasters-form-builder .check_parent input[type=radio] + label:after,
	{$rule}#wp-comment-cookies-consent + label:after, 
	{$rule}.widget .cmsmasters_slider_project .cmsmasters_slider_project_category > a {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	/* Finish Primary Color */
	
	
	/* Start Highlight Color */
	" . (($scheme == 'default') ? ".headline_outer a:hover,  " : '') . " 
	{$rule}a:hover, 
	{$rule}.cmsmasters_icon_wrap a:hover .cmsmasters_simple_icon,
	{$rule}.cmsmasters_header_search_form button:hover, 
	{$rule}.cmsmasters_post_default .cmsmasters_post_info, 
	{$rule}.cmsmasters_post_default .cmsmasters_post_info > a:hover, 
	{$rule}.cmsmasters_post_masonry .cmsmasters_post_footer, 
	{$rule}.cmsmasters_post_masonry .cmsmasters_post_footer > a:hover, 	
	{$rule}.cmsmasters_post_timeline .cmsmasters_post_footer, 
	{$rule}.cmsmasters_post_timeline .cmsmasters_post_footer > a:hover, 
	{$rule}.cmsmasters_post_timeline .cmsmasters_post_info a:hover span, 
	{$rule}.cmsmasters_open_post .cmsmasters_post_cont_info_meta, 
	{$rule}.cmsmasters_open_post .cmsmasters_post_cont_info_meta > a:hover, 	
	{$rule}.cmsmasters_project_grid .cmsmasters_project_footer, 
	{$rule}.cmsmasters_project_grid .cmsmasters_project_footer > a:hover, 	
	{$rule}.cmsmasters_project_puzzle .cmsmasters_project_meta, 	
	{$rule}.cmsmasters_project_puzzle .cmsmasters_project_meta > a:hover, 	
	{$rule}.cmsmasters_post_author a:hover, 
	{$rule}.cmsmasters_likes a, 
	{$rule}.cmsmasters_comments a, 
	{$rule}.cmsmasters_views a, 
	{$rule}.cmsmasters_post_read_more, 
	{$rule}.cmsmasters_wrap_pagination a, 
	{$rule}.post_nav span, 
	{$rule}.about_author .about_author_cont a, 
	{$rule}.cmsmasters_single_slider .cmsmasters_single_slider_item_title a:hover, 
	{$rule}#page .cmsmasters_open_project .cmsmasters_project_category a:hover, 
	{$rule}#page .cmsmasters_open_profile .cmsmasters_profile_category a:hover, 
	{$rule}.cmsmasters_twitter_wrap .cmsmasters_twitter_item_content a:hover, 
	{$rule}.cmsmasters_slider_post .cmsmasters_slider_post_read_more, 
	{$rule}.cmsmasters_slider_post .cmsmasters_slider_post_footer, 
	{$rule}.cmsmasters_slider_post .cmsmasters_slider_post_footer > a:hover, 
	{$rule}.cmsmasters_slider_post_author a:hover, 
	{$rule}.error .error_cont .search_button button:hover, 
	{$rule}.cmsmasters_archive_type .cmsmasters_archive_item_info, 
	{$rule}.cmsmasters_archive_type .cmsmasters_archive_item_info a, 
	{$rule}.widget_custom_popular_projects_entries .cmsmasters_slider_project_footer,
	{$rule}.widget_custom_popular_projects_entries .cmsmasters_slider_project_footer > a:hover,
	{$rule}.widget_custom_latest_projects_entries .cmsmasters_slider_project_footer, 
	{$rule}.widget_custom_latest_projects_entries .cmsmasters_slider_project_footer > a:hover, 
	{$rule}.widget_custom_posts_tabs_entries .cmsmasters_tabs div > a:hover, 
	{$rule}.widget_custom_posts_tabs_entries .cmsmasters_tabs div > .published, 
	{$rule}.cmsmasters_tabs .tab_comments li > .cmsmasters_tab_comments_author, 
	{$rule}.cmsmasters_tabs .tab_comments li > span, 
	{$rule}.cmsmasters_tabs .tab_comments li > small, 
	{$rule}.cmsmasters_attach_img .cmsmasters_attach_img_edit a:hover, 
	{$rule}.cmsmasters_attach_img .cmsmasters_attach_img_meta a:hover {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_hover']) . "
	}
	
	{$rule}input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]):focus,
	{$rule}textarea:focus {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_hover']) . "		
	}
	/* Finish Highlight Color */
	
	
	/* Start Headings Color */	
	" . (($scheme == 'default') ? ".headline_outer a, " : '') . "
	{$rule}h1, 
	{$rule}h2, 
	{$rule}h3, 
	{$rule}h4, 
	{$rule}h5, 
	{$rule}h6, 
	{$rule}h1 a, 
	{$rule}h2 a, 
	{$rule}h3 a, 
	{$rule}h4 a, 
	{$rule}h5 a, 
	{$rule}h6 a, 
	{$rule}.cmsmasters_dropcap.type1, 
	{$rule}.cmsmasters_dropcap.type2, 
	{$rule}.cmsmasters_button, 
	{$rule}.button, 
	{$rule}input[type=submit], 
	{$rule}input[type=button], 
	{$rule}button, 
	{$rule}#header .cmsmasters_header_search_but, 
	{$rule}.cmsmasters_post_default .cmsmasters_post_info > a, 
	{$rule}.cmsmasters_post_masonry .cmsmasters_post_footer > a, 
	{$rule}.cmsmasters_post_timeline .cmsmasters_post_footer > a, 
	{$rule}.cmsmasters_post_timeline .cmsmasters_day, 
	{$rule}.cmsmasters_open_post .cmsmasters_post_cont_info_meta > a, 
	{$rule}.cmsmasters_post_author a, 
	{$rule}.cmsmasters_likes a:hover span, 
	{$rule}.cmsmasters_likes a.active span, 
	{$rule}.cmsmasters_comments a:hover span, 
	{$rule}.cmsmasters_post_read_more:hover, 
	{$rule}.cmsmasters_wrap_pagination a.current, 
	{$rule}.cmsmasters_post_tags > a, 
	{$rule}.post_nav .post_nav_title, 
	{$rule}.post_nav a:hover span, 
	{$rule}.cmsmasters_pings_list .comment-body span a, 
	{$rule}.cmsmasters_comment_item .cmsmasters_comment_item_cont_info a, 
	{$rule}.cmsmasters_comment_item .cmsmasters_comment_item_title, 
	{$rule}.comment-respond .comment-notes, 
	{$rule}.comment-respond .logged-in-as, 
	{$rule}.cmsmasters_open_project .project_details_item_title, 
	{$rule}.cmsmasters_open_project .project_features_item_title, 
	{$rule}.cmsmasters_open_profile .profile_details_item_title, 
	{$rule}.cmsmasters_open_profile .profile_features_item_title, 
	{$rule}.cmsmasters-form-builder label,
	{$rule}.wpcf7-form p, 
	{$rule}.cmsmasters_contact_form .wpcf7-submit:hover, 
	{$rule}.cmsmasters_contact_form .cmsmasters_button:hover, 
	{$rule}.cmsmasters_stats.stats_mode_bars.stats_type_vertical .cmsmasters_stat_wrap .cmsmasters_stat .cmsmasters_stat_inner, 
	{$rule}.cmsmasters_stats.stats_mode_circles .cmsmasters_stat_wrap .cmsmasters_stat .cmsmasters_stat_inner:before, 
	{$rule}.wp-caption-text, 
	{$rule}.cmsmasters_toggles .cmsmasters_toggle_title, 
	{$rule}.cmsmasters_toggles .cmsmasters_toggle_title a, 
	{$rule}.cmsmasters_toggles .cmsmasters_toggles_filter a, 
	{$rule}.cmsmasters_tabs .cmsmasters_tabs_list_item a, 
	{$rule}.cmsmasters_twitter_wrap .published, 
	{$rule}.cmsmasters_notice, 
	{$rule}.cmsmasters_notice .notice_close, 
	{$rule}.cmsmasters_pricing_table .cmsmasters_price_wrap, 
	{$rule}.cmsmasters_quotes_slider .cmsmasters_quote_content, 
	{$rule}.cmsmasters_slider_post .cmsmasters_slider_post_read_more:hover, 
	{$rule}.cmsmasters_slider_post .cmsmasters_slider_post_footer > a, 
	{$rule}.cmsmasters_slider_post_author a, 
	{$rule}.cmsmasters_archive_type .cmsmasters_archive_item_info a:hover, 
	{$rule}.widget_pages ul li a, 
	{$rule}.widget_categories ul li a, 
	{$rule}.widget_archive ul li a, 
	{$rule}.widget_meta ul li a, 
	{$rule}.widget_recent_comments ul li a, 
	{$rule}.widget_recent_entries ul li a, 
	{$rule}	#wp-calendar th, 
	{$rule}.widget_custom_contact_form_entries .button:hover, 
	{$rule}.widget_nav_menu ul > li a, 
	{$rule}.widget_custom_posts_tabs_entries .cmsmasters_tabs div > a, 
	{$rule}.cmsmasters_tabs .tab_comments li > .cmsmasters_tab_comments_author:hover, 
	{$rule}.widget_tag_cloud a, 
	{$rule}.widget_custom_twitter_entries .tweet_time, 
	{$rule}.widget_custom_twitter_entries .tweet_text a, 
	{$rule}fieldset legend,
	{$rule}blockquote footer,
	{$rule}table caption,
	{$rule}.img_placeholder_small, 
	{$rule}.cmsmasters_stats.stats_mode_bars.stats_type_vertical .cmsmasters_stat_wrap .cmsmasters_stat_title,
	{$rule}.cmsmasters_stats.stats_mode_circles .cmsmasters_stat_wrap .cmsmasters_stat .cmsmasters_stat_inner .cmsmasters_stat_counter_wrap,
	{$rule}.cmsmasters_stats.stats_mode_circles .cmsmasters_stat_wrap .cmsmasters_stat_title, 
	{$rule}.cmsmasters_stats.stats_mode_bars.stats_type_vertical .cmsmasters_stat_wrap .cmsmasters_stat .cmsmasters_stat_inner .cmsmasters_stat_title_counter_wrap, 
	{$rule}.cmsmasters_counters .cmsmasters_counter_wrap .cmsmasters_counter .cmsmasters_counter_inner .cmsmasters_counter_counter_wrap, 
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap > li > a,
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap > li > ul > li > a,
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap > li > ul > li > ul li a:before,
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap_category > li > a,
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap_category > li > ul li a:before,
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap_archive > li a:before, 
	{$rule}.cmsmasters_tabs .tab_comments li > a, 
	{$rule}.cmsmasters_mailpoet_form form .wysija-submit:hover, 
	{$rule}.search_bar_wrap .search_button button:hover, 
	{$rule}.cmsmasters_mailpoet_form label {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_heading']) . "
	}
	
	{$rule}.cmsmasters_quotes_slider .cmsmasters_quote_image.cmsmasters_quote_placeholder {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_heading']) . "		
	}
	
	{$rule}form .formError .formErrorContent {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_heading']) . "
	}
	
	{$rule}.widget .widgettitle {
		color:rgba(" . cmsmasters_color2rgb($cmsmasters_option['top-magazine' . '_' . $scheme . '_heading']) . ", 0.8);
	}
	/* Finish Headings Color */
	
	
	/* Start Main Background Color */
	{$rule}.cmsmasters_header_search_form .cmsmasters_header_search_form_close, 
	{$rule}.cmsmasters_button:hover, 
	{$rule}.button:hover, 
	{$rule}input[type=submit]:hover, 
	{$rule}input[type=button]:hover, 
	{$rule}button:hover, 
	{$rule}table thead th, 
	{$rule}table thead td, 
	{$rule}a.cmsmasters_cat_color, 
	{$rule}.cmsmasters_project_puzzle .cmsmasters_project_meta > span:before, 
	{$rule}.cmsmasters_items_filter_wrap .current a, 
	{$rule}.cmsmasters_items_filter_wrap .current, 
	{$rule}.cmsmasters_post_tags > a:hover, 
	{$rule}.cmsmasters_prev_arrow:hover, 
	{$rule}.cmsmasters_next_arrow:hover, 
	{$rule}.cmsmasters_project_grid .cmsmasters_project_title a, 
	{$rule}.cmsmasters_project_puzzle .project_inner .project_inner_aligner	*, 
	{$rule}.cmsmasters_profile .cmsmasters_profile_subtitle, 
	{$rule}.cmsmasters_contact_form .wpcf7-submit, 
	{$rule}.cmsmasters_contact_form .cmsmasters_button, 
	{$rule}.cmsmasters_icon_list_items.cmsmasters_color_type_border .cmsmasters_icon_list_item .cmsmasters_icon_list_icon:before, 
	{$rule}.cmsmasters_toggles .cmsmasters_toggles_filter a:hover, 
	{$rule}.cmsmasters_toggles.toggles_mode_toggle .current_toggle .cmsmasters_toggle_title, 
	{$rule}.cmsmasters_toggles.toggles_mode_toggle .current_toggle .cmsmasters_toggle_title a, 
	{$rule}.cmsmasters_tabs .cmsmasters_tabs_list_item.current_tab a, 
	{$rule}.cmsmasters_tabs .cmsmasters_tabs_list_item a:hover, 
	{$rule}.cmsmasters_notice .notice_close:hover, 
	{$rule}.cmsmasters_quotes_slider .cmsmasters_quote_image.cmsmasters_quote_placeholder, 
	{$rule}.cmsmasters_slider_project .cmsmasters_slider_project_inner_wrap, 
	{$rule}.cmsmasters_slider_project .cmsmasters_slider_project_inner_wrap a, 
	{$rule}#page .cmsmasters_slider_project .cmsmasters_slider_project_inner_wrap *, 
	{$rule}.cmsmasters_archive_type .cmsmasters_archive_item_type span, 
	{$rule}.widget_custom_contact_form_entries .button, 
	{$rule}.widget_custom_popular_projects_entries .cmsmasters_slider_project_header a, 
	{$rule}.widget_custom_latest_projects_entries .cmsmasters_slider_project_header	a, 
	{$rule}.widget_tag_cloud a:hover, 
	{$rule}.widget_custom_twitter_entries .tweet_time:before, 
	{$rule}mark,
	{$rule}form .formError .formErrorContent,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_box_left_top:before,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_box_left:before,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_box_top:before, 
	{$rule}.search_bar_wrap .search_button button, 
	{$rule}.cmsmasters_mailpoet_form form .wysija-submit,
	{$rule}#page .cmsmasters_archive_type .cmsmasters_cat_color  {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_header_search_form input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]) {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_header_search_form input::-webkit-input-placeholder {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_header_search_form input:-moz-placeholder {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_header_search_form input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]):focus {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	" . (($scheme == 'default') ? "body," : '') . "
	" . (($scheme != 'default') ? ".cmsmasters_color_scheme_{$scheme}," : '') . "
	" . (($scheme == 'default') ? ".middle_inner," : '') . "
	" . (($scheme == 'default') ? ".headline_outer, " : '') . "
	{$rule}.cmsmasters_dropcap.type2,
	{$rule}.cmsmasters_button, 
	{$rule}.button, 
	{$rule}input[type=submit], 
	{$rule}input[type=button], 
	{$rule}button, 
	{$rule}.cmsmasters_wrap_pagination .page-numbers, 
	{$rule}.owl-pagination .owl-page, 
	{$rule}.cmsmasters_post_timeline .cmsmasters_post_date, 
	{$rule}.cmsmasters_prev_arrow, 
	{$rule}.cmsmasters_next_arrow, 
	{$rule}.cmsmasters_contact_form .wpcf7-submit:hover, 
	{$rule}.cmsmasters_contact_form .cmsmasters_button:hover, 
	{$rule}.cmsmasters_counters .cmsmasters_counter_wrap .cmsmasters_counter .cmsmasters_counter_inner:before, 
	{$rule}.cmsmasters_tabs .cmsmasters_tabs_list_item a, 
	{$rule}.cmsmasters_notice .notice_close, 
	{$rule}#wp-calendar, 
	{$rule}#wp-calendar th, 
	{$rule}#wp-calendar td, 
	{$rule}.widget_custom_contact_form_entries .button:hover, 
	{$rule}.widget_tag_cloud a, 
	{$rule}.cmsmasters_stats.stats_mode_circles .cmsmasters_stat_wrap .cmsmasters_stat .cmsmasters_stat_inner, 
	{$rule}.search_bar_wrap .search_button button:hover, 
	{$rule}.cmsmasters_mailpoet_form form .wysija-submit:hover, 
	{$rule}#page .search_bar_wrap .search_field input {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_project_puzzle .cmsmasters_project_category a, 
	{$rule}.cmsmasters_counters .cmsmasters_counter_wrap .cmsmasters_counter .cmsmasters_counter_inner:before {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_project_puzzle .project_inner .project_inner_aligner	a:hover, 
	{$rule}.cmsmasters_project_puzzle .cmsmasters_likes a:hover:before, 
	{$rule}.cmsmasters_project_puzzle .cmsmasters_likes .active:before, 
	{$rule}.cmsmasters_project_puzzle .cmsmasters_comments a:hover:before, 
	{$rule}.cmsmasters_project_puzzle .cmsmasters_comments .active:before, 
	{$rule}a.cmsmasters_cat_color:hover, 
	{$rule}#page .cmsmasters_slider_project .cmsmasters_slider_project_inner_wrap a:hover, 
	{$rule}#page .cmsmasters_slider_project .cmsmasters_slider_project_inner_wrap a:hover:before, 
	{$rule}#page .cmsmasters_slider_project .cmsmasters_slider_project_inner_wrap a:hover span, 
	{$rule}#page .cmsmasters_slider_project .cmsmasters_slider_project_inner_wrap a.active:before, 
	{$rule}#page .cmsmasters_slider_project .cmsmasters_slider_project_inner_wrap a.active span,
	{$rule}#page .cmsmasters_archive_type .cmsmasters_cat_color:hover	{
		color:rgba(" . cmsmasters_color2rgb($cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . ", 0.7);
	}
	
	{$rule}.cmsmasters_project_puzzle .cmsmasters_project_category a:hover {
		border-color:rgba(" . cmsmasters_color2rgb($cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . ", 0.7);
	}
	/* Finish Main Background Color */
	
	
	/* Start Alternate Background Color */
	" . (($scheme == 'default') ? "#slide_top," : '') . "
	{$rule}.cmsmasters_icon_list_items.cmsmasters_color_type_icon .cmsmasters_icon_list_icon_wrap, 
	{$rule}.cmsmasters_icon_list_items.cmsmasters_color_type_bg .cmsmasters_icon_list_item .cmsmasters_icon_list_icon:before {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_alternate']) . "
	}
	
	{$rule}input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]),
	{$rule}textarea,
	{$rule}option, 
	{$rule}table tfoot th, 
	{$rule}table tfoot td, 
	{$rule}.cmsmasters_open_project .project_details_title, 
	{$rule}.cmsmasters_open_project .project_features_title, 
	{$rule}.cmsmasters_open_profile .profile_details_title, 
	{$rule}.cmsmasters_open_profile .profile_features_title,
	{$rule}.cmsmasters_open_profile .profile_social_icons_title, 
	{$rule}.cmsmasters_pricing_table .cmsmasters_price_wrap, 
	{$rule}.cmsmasters_quotes_grid .cmsmasters_quote_inner, 
	{$rule}.widget .widgettitle, 
	{$rule}#wp-calendar caption, 
	{$rule}select,
	{$rule}fieldset,
	{$rule}fieldset legend,
	{$rule}.img_placeholder_small, 
	{$rule}.cmsmasters_featured_block,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_box_top,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_box_left,
	{$rule}.cmsmasters_icon_list_items.cmsmasters_color_type_icon .cmsmasters_icon_list_icon,
	{$rule}.gallery-item .gallery-icon,
	{$rule}.gallery-item .gallery-caption,
	{$rule}.cmsmasters_img.with_caption, 
	{$rule}.wpcf7 form.wpcf7-form span.wpcf7-list-item input[type=checkbox] + span.wpcf7-list-item-label:before, 
	{$rule}.cmsmasters-form-builder .check_parent input[type=checkbox] + label:before, 
	{$rule}.wpcf7 form.wpcf7-form span.wpcf7-list-item input[type=radio] + span.wpcf7-list-item-label:before, 
	{$rule}.cmsmasters-form-builder .check_parent input[type=radio] + label:before, 
	{$rule}#page .widget_search .widgettitle + .search_bar_wrap	{
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_alternate']) . "
	}
	/* Finish Alternate Background Color */
	
	
	/* Start Borders Color */
	{$rule}ul li:before, 	
	{$rule}.cmsmasters_post_default .cmsmasters_post_info > span:before, 
	{$rule}.cmsmasters_post_masonry .cmsmasters_post_footer > span:before, 
	{$rule}.cmsmasters_post_timeline .cmsmasters_post_footer > span:before, 
	{$rule}.cmsmasters_open_post .cmsmasters_post_cont_info_meta > span:before, 
	{$rule}.cmsmasters_project_grid .cmsmasters_project_footer > span:before, 
	{$rule}.cmsmasters_comment_item .cmsmasters_comment_item_cont_info > *:before, 
	{$rule}.share_posts a, 
	{$rule}#page .profile_social_icons_list a, 
	{$rule}.cmsmasters_quotes_slider .owl-buttons > div, 
	{$rule}.cmsmasters_slider_post .cmsmasters_slider_post_footer > span:before, 
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap > li > ul > li > ul > li a:before, 
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap_archive > li a:before, 
	{$rule}.cmsmasters_archive_type .cmsmasters_archive_item_info > span:before, 
	{$rule}.widget_custom_popular_projects_entries .cmsmasters_slider_project_footer > span:before, 
	{$rule}.widget_custom_latest_projects_entries .cmsmasters_slider_project_footer > span:before {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_border']) . "		
	}
	
	" . (($scheme == 'footer') ? ".cmsmasters_footer_default .footer_nav > li:before, " : '') . "
	{$rule}.cmsmasters_wrap_pagination ul:before, 
	{$rule}.cmsmasters_wrap_items_loader:before, 
	{$rule}.blog.timeline:before, 
	{$rule}.cmsmasters_stats.stats_mode_bars.stats_type_horizontal .cmsmasters_stat_wrap:before, 
	{$rule}.cmsmasters_icon_list_items.cmsmasters_icon_list_type_block .cmsmasters_icon_list_item:before, 
	{$rule}.cmsmasters_sitemap_wrap .cmsmasters_sitemap > li:before	{
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_border']) . "
	}
	
	" . (($scheme == 'footer') ? "#footer, " : '') . "
	" . (($scheme == 'footer') ? ".footer_copyright, " : '') . "
	" . (($scheme == 'default') ? ".headline_outer .headline_content," : '') . "
	{$rule}.cmsmasters_dropcap.type2, 
	{$rule}.cmsmasters_button, 
	{$rule}.button, 
	{$rule}input[type=submit], 
	{$rule}input[type=button], 
	{$rule}button, 
	{$rule}table tr, 
	{$rule}.owl-pagination .owl-page, 
	{$rule}.cmsmasters_wrap_pagination li, 
	{$rule}.sticky .cmsmasters_post_cont_wrap, 
	{$rule}.cmsmasters_open_post .cmsmasters_post_cont_info_bottom, 
	{$rule}.cmsmasters_post_tags > a, 
	{$rule}.post_nav, 
	{$rule}.about_author .about_author_inner, 
	{$rule}.cmsmasters_prev_arrow, 
	{$rule}.cmsmasters_next_arrow, 
	{$rule}.cmsmasters_comment_item	.cmsmasters_comment_item_cont, 
	{$rule}.cmsmasters_open_project .project_details_title, 
	{$rule}.cmsmasters_open_project .project_features_title, 
	{$rule}.cmsmasters_open_project .project_details > div, 
	{$rule}.cmsmasters_open_project .project_features > div, 
	{$rule}.cmsmasters_open_profile .profile_details_title, 
	{$rule}.cmsmasters_open_profile .profile_features_title, 
	{$rule}.cmsmasters_open_profile .profile_social_icons_title, 
	{$rule}.cmsmasters_open_profile .profile_details > div, 
	{$rule}.cmsmasters_open_profile .profile_features > div, 
	{$rule}.cmsmasters_contact_form .wpcf7-submit:hover,  
	{$rule}.cmsmasters_contact_form .cmsmasters_button:hover, 
	{$rule}.cmsmasters_stats.stats_mode_circles .cmsmasters_stat_wrap .cmsmasters_stat .cmsmasters_stat_inner, 
	{$rule}.wp-caption, 
	{$rule}.cmsmasters_toggles .cmsmasters_toggle_title, 
	{$rule}.cmsmasters_tabs .cmsmasters_tabs_list_item a, 
	{$rule}.cmsmasters_quotes_grid .cmsmasters_quote_inner, 
	{$rule}.widget .widgettitle, 
	{$rule}.widget_pages ul li, 
	{$rule}.widget_categories ul li, 
	{$rule}.widget_archive ul li, 
	{$rule}.widget_meta ul li, 
	{$rule}.widget_recent_comments ul li, 
	{$rule}.widget_recent_entries ul li, 
	{$rule}#wp-calendar caption, 
	{$rule}.widget_custom_contact_form_entries .button:hover, 
	{$rule}.widget_nav_menu ul li a, 
	{$rule}.cmsmasters_tabs .tab_comments li, 
	{$rule}.cmsmasters_attach_img .cmsmasters_attach_img_info, 
	{$rule}input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]),
	{$rule}textarea,
	{$rule}option,
	{$rule}select,
	{$rule}hr,
	{$rule}blockquote,
	{$rule}.cmsmasters_divider,
	{$rule}.cmsmasters_widget_divider,
	{$rule}.cmsmasters_img.with_caption,
	{$rule}.cmsmasters_icon_wrap .cmsmasters_simple_icon, 
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_box_top,
	{$rule}.cmsmasters_icon_box.cmsmasters_icon_box_left,
	{$rule}.cmsmasters_icon_list_items.cmsmasters_icon_list_type_block .cmsmasters_icon_list_item,
	{$rule}.cmsmasters_icon_list_items.cmsmasters_color_type_bg .cmsmasters_icon_list_icon:after,
	{$rule}.cmsmasters_icon_list_items.cmsmasters_color_type_icon .cmsmasters_icon_list_icon:after, 
	{$rule}.wpcf7 form.wpcf7-form span.wpcf7-list-item input[type=checkbox] + span.wpcf7-list-item-label:before, 
	{$rule}.cmsmasters-form-builder .check_parent input[type=checkbox] + label:before, 
	{$rule}.wpcf7 form.wpcf7-form span.wpcf7-list-item input[type=radio] + span.wpcf7-list-item-label:before, 
	{$rule}.cmsmasters-form-builder .check_parent input[type=radio] + label:before,
	{$rule}#wp-comment-cookies-consent + label:before, 
	{$rule}.cmsmasters_icon_list_items.cmsmasters_color_type_border .cmsmasters_icon_list_item .cmsmasters_icon_list_icon:after, 
	{$rule}.cmsmasters_toggles .cmsmasters_toggles_filter a, 
	{$rule}.cmsmasters_notice .notice_close, 
	{$rule}.cmsmasters_pricing_table *, 
	{$rule}.widget_rss ul li, 
	{$rule}.widget_tag_cloud a, 
	{$rule}.search_bar_wrap .search_button button:hover, 
	{$rule}.cmsmasters_mailpoet_form form .wysija-submit:hover, 
	{$rule}#page .widget_search .widgettitle + .search_bar_wrap	{
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_border']) . "	
	}
	/* Finish Borders Color */
	
	
	/* Start Custom Rules */
	{$rule}::selection {
		" . cmsmasters_color_css('background', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . ";
	}
	
	{$rule}::-moz-selection {
		" . cmsmasters_color_css('background', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	";
	
	
	if ($scheme != 'default') {
		$custom_css .= "
		.cmsmasters_color_scheme_{$scheme}.cmsmasters_row_top_zigzag:before, 
		.cmsmasters_color_scheme_{$scheme}.cmsmasters_row_bot_zigzag:after {
			background-image: -webkit-linear-gradient(135deg, " . $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg'] . " 25%, transparent 25%), 
					-webkit-linear-gradient(45deg, " . $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg'] . " 25%, transparent 25%);
			background-image: -moz-linear-gradient(135deg, " . $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg'] . " 25%, transparent 25%), 
					-moz-linear-gradient(45deg, " . $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg'] . " 25%, transparent 25%);
			background-image: -ms-linear-gradient(135deg, " . $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg'] . " 25%, transparent 25%), 
					-ms-linear-gradient(45deg, " . $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg'] . " 25%, transparent 25%);
			background-image: -o-linear-gradient(135deg, " . $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg'] . " 25%, transparent 25%), 
					-o-linear-gradient(45deg, " . $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg'] . " 25%, transparent 25%);
			background-image: linear-gradient(315deg, " . $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg'] . " 25%, transparent 25%), 
					linear-gradient(45deg, " . $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg'] . " 25%, transparent 25%);
		}
		";
	}
	
	
	$custom_css .= "
	/* Finish Custom Rules */

/***************** Finish {$title} Color Scheme Rules ******************/


/***************** Start {$title} Button Color Scheme Rules ******************/
	
	{$rule}.cmsmasters_button.cmsmasters_but_bg_hover {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_bg_hover:hover {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	
	{$rule}.cmsmasters_button.cmsmasters_but_bd_underline {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_bd_underline:hover {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_left, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_right, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_top, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_bottom, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_expand_vert, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_expand_hor, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_expand_diag {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_left:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_right:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_top:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_bottom:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_expand_vert:hover, 
	{$rule}.cmsmasters_button.cm.sms_but_bg_expand_hor:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_expand_diag:hover {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_left:after, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_right:after, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_top:after, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_slide_bottom:after, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_expand_vert:after, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_expand_hor:after, 
	{$rule}.cmsmasters_button.cmsmasters_but_bg_expand_diag:after {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	
	{$rule}.cmsmasters_button.cmsmasters_but_shadow {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_shadow:hover {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_dark_bg, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_light_bg, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_divider {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_dark_bg:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_light_bg:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_divider:hover {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_divider:after {
		" . cmsmasters_color_css('border-right-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_inverse {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_inverse:before {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_inverse:after {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_inverse:hover {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_inverse:hover:before {
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_inverse:hover:after {
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_slide_left, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_slide_right {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_slide_left:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_slide_right:hover {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_hover_slide_left, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_hover_slide_right, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_hover_slide_top, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_hover_slide_bottom {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}
	
	{$rule}.cmsmasters_button.cmsmasters_but_icon_hover_slide_left:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_hover_slide_right:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_hover_slide_top:hover, 
	{$rule}.cmsmasters_button.cmsmasters_but_icon_hover_slide_bottom:hover {
		" . cmsmasters_color_css('border-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('background-color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_link']) . "
		" . cmsmasters_color_css('color', $cmsmasters_option['top-magazine' . '_' . $scheme . '_bg']) . "
	}

/***************** Finish {$title} Button Color Scheme Rules ******************/


";
	}
	
	
	return apply_filters('top_magazine_theme_colors_primary_filter', $custom_css);
}

