/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version		1.0.4
 * 
 * Theme Admin Settings Toggles Scripts
 * Created by CMSMasters
 * 
 */


(function ($) { 
	"use strict";

	/* General 'Header' Tab Fields Load */	
	$('#' + cmsmasters_theme_settings.shortname + '_header_search').parents('tr').show();
	
	if ($('input[name*="' + cmsmasters_theme_settings.shortname + '_header_styles"]:checked').val() === 'default') {
		$('#' + cmsmasters_theme_settings.shortname + '_header_social').parents('tr').hide();
		$('#' + cmsmasters_theme_settings.shortname + '_header_custom_html').parents('tr').hide();
		$('#' + cmsmasters_theme_settings.shortname + '_header_add_cont_cust_html').parents('tr').hide();
	}
	
	if ($('input[name*="' + cmsmasters_theme_settings.shortname + '_header_styles"]:checked').val() === 'c_nav') {
		$('#' + cmsmasters_theme_settings.shortname + '_header_social').parents('tr').show();
		$('#' + cmsmasters_theme_settings.shortname + '_header_custom_html').parents('tr').show();
		
		if ($('#' + cmsmasters_theme_settings.shortname + '_header_custom_html').is(':not(:checked)')) {
			$('#' + cmsmasters_theme_settings.shortname + '_header_add_cont_cust_html').parents('tr').hide();
		} else {
			$('#' + cmsmasters_theme_settings.shortname + '_header_add_cont_cust_html').parents('tr').show();
		}
	} else {
		$('#' + cmsmasters_theme_settings.shortname + '_header_custom_html').parents('tr').hide();
	}
	
	/* General 'Header' Tab Fields Change */
	$('input[name*="' + cmsmasters_theme_settings.shortname + '_header_styles"]').on('change', function () { 
		$('#' + cmsmasters_theme_settings.shortname + '_header_search').parents('tr').show();
		
		if ($('input[name*="' + cmsmasters_theme_settings.shortname + '_header_styles"]:checked').val() === 'default') {
			$('#' + cmsmasters_theme_settings.shortname + '_header_social').parents('tr').hide();
			$('#' + cmsmasters_theme_settings.shortname + '_header_custom_html').parents('tr').hide();
			$('#' + cmsmasters_theme_settings.shortname + '_header_add_cont_cust_html').parents('tr').hide();
		} else if ($('input[name*="' + cmsmasters_theme_settings.shortname + '_header_styles"]:checked').val() === 'c_nav') {
			$('#' + cmsmasters_theme_settings.shortname + '_header_social').parents('tr').show();
			$('#' + cmsmasters_theme_settings.shortname + '_header_custom_html').parents('tr').show();
			
			if ($('#' + cmsmasters_theme_settings.shortname + '_header_custom_html').is(':not(:checked)')) {
				$('#' + cmsmasters_theme_settings.shortname + '_header_add_cont_cust_html').parents('tr').hide();
			} else {
				$('#' + cmsmasters_theme_settings.shortname + '_header_add_cont_cust_html').parents('tr').show();
			}
		} else {
			$('#' + cmsmasters_theme_settings.shortname + '_header_social').parents('tr').hide();
			$('#' + cmsmasters_theme_settings.shortname + '_header_custom_html').parents('tr').hide();
			$('#' + cmsmasters_theme_settings.shortname + '_header_add_cont_cust_html').parents('tr').hide();
		}
	} );
	
	$('#' + cmsmasters_theme_settings.shortname + '_header_custom_html').on('change', function () { 
		if ($('#' + cmsmasters_theme_settings.shortname + '_header_custom_html').is(':checked')) {
			$('#' + cmsmasters_theme_settings.shortname + '_header_add_cont_cust_html').parents('tr').show();
		} else {
			$('#' + cmsmasters_theme_settings.shortname + '_header_add_cont_cust_html').parents('tr').hide();
		}
	} );
	
} )(jQuery);

