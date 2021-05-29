/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version		1.1.0
 * 
 * Theme Content Composer Schortcodes Extend
 * Created by CMSMasters
 * 
 */



/**
 * Heading Extend
 */

var heading_new_fields = {};


for (var id in cmsmastersShortcodes.cmsmasters_heading.fields) {
	if (id === 'animation') {
		heading_new_fields['custom_check'] = { 
			type : 		'checkbox', 
			title : 	cmsmasters_theme_shortcodes.heading_field_custom_check, 
			descr : 	'', 
			def : 		'', 
			required : 	false, 
			width : 	'half',  
			choises : { 
				'true' : cmsmasters_shortcodes.choice_enable 
			}
		};
		heading_new_fields['width_monitor'] = { 
			type : 		'input', 
			title : 	cmsmasters_theme_shortcodes.heading_field_width_monitor, 
			descr : 	"<span>" + cmsmasters_shortcodes.note + ' ' + cmsmasters_theme_shortcodes.heading_field_size_zero_note + "</span>", 
			def : 		'767', 
			required : 	false, 
			width : 	'number', 
			min : 		'320', 
			depend : 	'custom_check:true' 
		};
		heading_new_fields['custom_font_size'] = { 
			type : 		'input', 
			title : 	cmsmasters_theme_shortcodes.heading_field_custom_font_size, 
			descr : 	"<span>" + cmsmasters_shortcodes.note + ' ' + cmsmasters_theme_shortcodes.heading_field_size_zero_note + "</span>", 
			def : 		'', 
			required : 	false, 
			width : 	'number', 
			min : 		'0', 
			depend : 	'custom_check:true' 
		};
		heading_new_fields['custom_line_height'] = { 
			type : 		'input', 
			title : 	cmsmasters_theme_shortcodes.heading_field_custom_line_height, 
			descr : 	"<span>" + cmsmasters_shortcodes.note + ' ' + cmsmasters_theme_shortcodes.heading_field_size_zero_note + "</span>", 
			def : 		'', 
			required : 	false, 
			width : 	'number', 
			min : 		'0', 
			depend : 	'custom_check:true' 
		};
		
		heading_new_fields[id] = cmsmastersShortcodes.cmsmasters_heading.fields[id];
	} else {
		heading_new_fields[id] = cmsmastersShortcodes.cmsmasters_heading.fields[id];
	}
}


cmsmastersShortcodes.cmsmasters_heading.fields = heading_new_fields;



var heading_new_fields = {};


for (var id in cmsmastersShortcodes.cmsmasters_heading.fields) {
	if (id === 'link') {
		heading_new_fields['custom_second_heading'] = { 
			type : 		'checkbox', 
			title : 	cmsmasters_theme_shortcodes.heading_field_custom_second_heading, 
			descr : 	'', 
			def : 		'', 
			required : 	false, 
			width : 	'half', 
			choises : { 
				'true' : cmsmasters_shortcodes.choice_enable 
			}
		};
		heading_new_fields['custom_second_heading_text'] = { 
			type : 		'input', 
			title : 	cmsmasters_theme_shortcodes.heading_field_custom_second_heading_text, 
			descr : 	'', 
			def : 		cmsmasters_shortcodes.heading_title, 
			required : 	false, 
			width : 	'half', 
			depend : 	'custom_second_heading:true' 
		};
		heading_new_fields['heading_field_custom_second_heading_color'] = { 
			type : 		'rgba', 
			title : 	cmsmasters_theme_shortcodes.heading_field_custom_second_heading_text, 
			descr : 	"<span>" + cmsmasters_shortcodes.note + ' ' + cmsmasters_shortcodes.heading_field_color_descr_note + "</span>", 
			def : 		'', 
			required : 	false, 
			width : 	'half', 
			depend : 	'custom_second_heading:true' 
		};
		heading_new_fields['heading_field_custom_button'] = { 
			type : 		'checkbox', 
			title : 	cmsmasters_theme_shortcodes.heading_field_custom_button, 
			descr : 	'', 
			def : 		'', 
			required : 	false, 
			width : 	'half', 
			choises : { 
				'true' : cmsmasters_shortcodes.choice_enable 
			}
		};
		heading_new_fields['heading_field_custom_button_title'] = { 
			type : 		'input', 
			title : 	cmsmasters_theme_shortcodes.heading_field_custom_button_title, 
			descr : 	cmsmasters_theme_shortcodes.heading_field_custom_button_title_descr, 
			def : 		cmsmasters_shortcodes.button, 
			required : 	false, 
			width : 	'half', 
			depend : 	'heading_field_custom_button:true' 
		};
		heading_new_fields['heading_field_custom_button_link'] = { 
			type : 		'input', 
			title : 	cmsmasters_theme_shortcodes.heading_field_custom_button_link, 
			descr : 	cmsmasters_theme_shortcodes.heading_field_custom_button_link_descr, 
			def : 		cmsmasters_shortcodes.button, 
			required : 	false, 
			width : 	'half', 
			depend : 	'heading_field_custom_button:true' 
		};
		
		heading_new_fields[id] = cmsmastersShortcodes.cmsmasters_heading.fields[id];
	} else if (id === 'divider_type') { 
		cmsmastersShortcodes.cmsmasters_heading.fields[id]['choises']['longbottom'] = cmsmasters_theme_shortcodes.heading_field_divider_lenght_bottom;
		
		
		heading_new_fields[id] = cmsmastersShortcodes.cmsmasters_heading.fields[id];
	} else {
		heading_new_fields[id] = cmsmastersShortcodes.cmsmasters_heading.fields[id];
	}
}


cmsmastersShortcodes.cmsmasters_heading.fields = heading_new_fields;



/**
 * Posts Slider Extend
 */

var posts_slider_new_fields = {}; 
 

for (var id in cmsmastersShortcodes.cmsmasters_posts_slider.fields) {

	if (id === 'blog_metadata') {
		cmsmastersShortcodes.cmsmasters_posts_slider.fields[id]['choises'] = {
			'title' : 		cmsmasters_shortcodes.choice_title, 
			'excerpt' : 	cmsmasters_shortcodes.choice_excerpt, 
			'date' : 		cmsmasters_shortcodes.choice_date, 
			'categories' : 	cmsmasters_shortcodes.choice_categories, 
			'author' : 		cmsmasters_shortcodes.choice_author, 
			'comments' : 	cmsmasters_shortcodes.choice_comments, 
			'likes' : 		cmsmasters_shortcodes.choice_likes, 
			'views' : 		cmsmasters_theme_shortcodes.choice_view, 
			'more' : 		cmsmasters_shortcodes.choice_more 
		};
		
		posts_slider_new_fields[id] = cmsmastersShortcodes.cmsmasters_posts_slider.fields[id];
	} else if (id === 'portfolio_metadata') {
		cmsmastersShortcodes.cmsmasters_posts_slider.fields[id]['choises'] = {
			'title' : 		cmsmasters_shortcodes.choice_title, 
			'excerpt' : 	cmsmasters_shortcodes.choice_excerpt, 
			'categories' : 	cmsmasters_shortcodes.choice_categories, 
			'comments' : 	cmsmasters_shortcodes.choice_comments, 
			'likes' : 		cmsmasters_shortcodes.choice_likes
		};
		
		
		posts_slider_new_fields[id] = cmsmastersShortcodes.cmsmasters_posts_slider.fields[id];
	} else if (id === 'amount') {
		delete cmsmastersShortcodes.cmsmasters_posts_slider.fields[id];
		
		
	} else if (id === 'columns') {
		posts_slider_new_fields['controls'] = { 
			type : 		'checkbox', 
			title : 	cmsmasters_theme_shortcodes.posts_slider_controls_enable_title, 
			descr : 	'', 
			def : 		'', 
			required : 	false, 
			width : 	'half', 
			choises : { 
						'true' : 	cmsmasters_shortcodes.choice_enable 
			}
		};
		
		delete cmsmastersShortcodes.cmsmasters_posts_slider.fields[id]['depend'];
		
		
		posts_slider_new_fields[id] = cmsmastersShortcodes.cmsmasters_posts_slider.fields[id];
	}  else {
		posts_slider_new_fields[id] = cmsmastersShortcodes.cmsmasters_posts_slider.fields[id];
	}
}


cmsmastersShortcodes.cmsmasters_posts_slider.fields = posts_slider_new_fields;



/**
 * Blog Extend
 */

var cmsmasters_blog_new_fields = {};


for (var id in cmsmastersShortcodes.cmsmasters_blog.fields) {
	if (id === 'metadata') {
		cmsmastersShortcodes.cmsmasters_blog.fields[id]['choises'] = {
			'date' : 		cmsmasters_shortcodes.choice_date, 
			'excerpt' : 	cmsmasters_shortcodes.choice_excerpt, 
			'categories' : 	cmsmasters_shortcodes.choice_categories, 
			'author' : 		cmsmasters_shortcodes.choice_author, 
			'comments' : 	cmsmasters_shortcodes.choice_comments, 
			'likes' : 		cmsmasters_shortcodes.choice_likes, 
			'views' : 		cmsmasters_theme_shortcodes.choice_view, 
			'more' : 		cmsmasters_shortcodes.choice_more 
		};
		cmsmasters_blog_new_fields['blog_field_featured_post'] = { 
			type : 		'checkbox', 
			title : 	cmsmasters_theme_shortcodes.blog_field_featured, 
			descr : 	cmsmasters_theme_shortcodes.blog_field_featured_descr + "<br /><span>" + cmsmasters_shortcodes.note + ' ' + cmsmasters_theme_shortcodes.blog_field_featured_descr_note + "</span>", 
			def : 		'', 
			required : 	false, 
			width : 	'half', 
			choises : { 
				'true' : cmsmasters_shortcodes.choice_enable 
			}
		};
		
		
		cmsmasters_blog_new_fields[id] = cmsmastersShortcodes.cmsmasters_blog.fields[id];
	} else {
		cmsmasters_blog_new_fields[id] = cmsmastersShortcodes.cmsmasters_blog.fields[id];
	}
}


cmsmastersShortcodes.cmsmasters_blog.fields = cmsmasters_blog_new_fields;



/**
 * Toggles Extend
 */

var toggles_new_fields = {}; 

for (var id in cmsmastersShortcodes.cmsmasters_toggles.fields) {

	if (id === 'animation') {
		toggles_new_fields['primary_color'] = { 
				type : 		'rgba', 
				title : 	cmsmasters_theme_shortcodes.toggles_field_primary_color, 
				descr : 	cmsmasters_theme_shortcodes.toggles_field_primary_color_descr, 
				def : 		'', 
				required : 	false, 
				width : 	'half' 
		};
		
		toggles_new_fields['secondary_color'] = { 
				type : 		'rgba', 
				title : 	cmsmasters_theme_shortcodes.toggles_field_secondary_color, 
				descr : 	cmsmasters_theme_shortcodes.toggles_field_secondary_color_descr, 
				def : 		'', 
				required : 	false, 
				width : 	'half' 
		};
		
		
		toggles_new_fields[id] = cmsmastersShortcodes.cmsmasters_toggles.fields[id];
	} else {
		toggles_new_fields[id] = cmsmastersShortcodes.cmsmasters_toggles.fields[id];
	}
}


cmsmastersShortcodes.cmsmasters_toggles.fields = toggles_new_fields;

