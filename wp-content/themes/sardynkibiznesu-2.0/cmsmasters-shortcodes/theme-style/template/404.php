<?php
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version 	1.1.0
 * 
 * 404 Error Page Template
 * Created by CMSMasters
 * 
 */


$cmsmasters_option = top_magazine_get_global_options();

?>

</div>

<!-- Start Content -->
<div class="entry">
	<div class="error">
		<div class="error_bg">
			<div class="error_inner">
				<h1 class="error_title"><?php echo esc_html__('404', 'top-magazine'); ?></h1>
				<h2 class="error_subtitle"><?php echo esc_html__("We're sorry, but the page you were looking for doesn't exist.", 'top-magazine'); ?></h2>
				
				<div class="error_cont">
					<?php 
					if ($cmsmasters_option['top-magazine' . '_error_search']) { 
						get_search_form(); 
					}
					
					
					if ($cmsmasters_option['top-magazine' . '_error_sitemap_button'] && $cmsmasters_option['top-magazine' . '_error_sitemap_link'] != '') {
						echo '<div class="error_button_wrap"><a href="' . esc_url($cmsmasters_option['top-magazine' . '_error_sitemap_link']) . '" class="button">' . esc_html__('Sitemap', 'top-magazine') . '</a></div>';
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="content_wrap fullwidth">
<!-- Finish Content -->