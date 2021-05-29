<?php 
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version		1.0.4
 * 
 * Footer Template
 * Created by CMSMasters
 * 
 */


$cmsmasters_option = top_magazine_get_global_options();
?>
<div class="footer <?php echo 'cmsmasters_color_scheme_' . $cmsmasters_option['top-magazine' . '_footer_scheme'] . ($cmsmasters_option['top-magazine' . '_footer_type'] == 'default' ? ' cmsmasters_footer_default' : ' cmsmasters_footer_small'); ?>">
	<div class="footer_inner">
		<?php 
		if (
			$cmsmasters_option['top-magazine' . '_footer_type'] == 'default' && 
			$cmsmasters_option['top-magazine' . '_footer_logo']
		) {
			top_magazine_footer_logo($cmsmasters_option);
		}
		
		
		if (
			(
				$cmsmasters_option['top-magazine' . '_footer_type'] == 'default' && 
				$cmsmasters_option['top-magazine' . '_footer_html'] !== ''
			) || (
				$cmsmasters_option['top-magazine' . '_footer_type'] == 'small' && 
				$cmsmasters_option['top-magazine' . '_footer_additional_content'] == 'text' && 
				$cmsmasters_option['top-magazine' . '_footer_html'] !== ''
			)
		) {
			echo '<div class="footer_custom_html_wrap">' . 
				'<div class="footer_custom_html">' . 
					do_shortcode(wp_kses(stripslashes($cmsmasters_option['top-magazine' . '_footer_html']), 'post')) . 
				'</div>' . 
			'</div>';
		}
		
		
		if (
			has_nav_menu('footer') && 
			(
				(
					$cmsmasters_option['top-magazine' . '_footer_type'] == 'default' && 
					$cmsmasters_option['top-magazine' . '_footer_nav']
				) || (
					$cmsmasters_option['top-magazine' . '_footer_type'] == 'small' && 
					$cmsmasters_option['top-magazine' . '_footer_additional_content'] == 'nav'
				)
			)
		) {
			echo '<div class="footer_nav_wrap">' . 
				'<nav>';
				
				
				wp_nav_menu(array( 
					'theme_location' => 'footer', 
					'menu_id' => 'footer_nav', 
					'menu_class' => 'footer_nav' 
				));
				
				
				echo '</nav>' . 
			'</div>';
		}
		
		
		if (
			isset($cmsmasters_option['top-magazine' . '_social_icons']) && 
			(
				(
					$cmsmasters_option['top-magazine' . '_footer_type'] == 'default' && 
					$cmsmasters_option['top-magazine' . '_footer_social']
				) || (
					$cmsmasters_option['top-magazine' . '_footer_type'] == 'small' && 
					$cmsmasters_option['top-magazine' . '_footer_additional_content'] == 'social'
				)
			)
		) {
			top_magazine_social_icons();
		}
		
		if ($cmsmasters_option['top-magazine' . '_footer_type'] == 'small') { ?>
			<span class="footer_copyright copyright">
				<?php 
				if (function_exists('the_privacy_policy_link')) {
					the_privacy_policy_link('', ' / ');
				}
				
				echo esc_html(stripslashes($cmsmasters_option['top-magazine' . '_footer_copyright']));
				?>
			</span><?php
		}
		?>
	</div>
	
	<?php
		if ($cmsmasters_option['top-magazine' . '_footer_type'] == 'default') { ?>
			<span class="footer_copyright copyright">
				<?php 
				if (function_exists('the_privacy_policy_link')) {
					the_privacy_policy_link('', ' / ');
				}
				
				echo esc_html(stripslashes($cmsmasters_option['top-magazine' . '_footer_copyright']));
				?>
			</span><?php
		}
	?>
	
</div>