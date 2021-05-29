<?php
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version		1.1.0
 * 
 * Profile Vertical Template
 * Created by CMSMasters
 * 
 */


$cmsmasters_profile_social = get_post_meta(get_the_ID(), 'cmsmasters_profile_social', true);

$cmsmasters_profile_subtitle = get_post_meta(get_the_ID(), 'cmsmasters_profile_subtitle', true);

?>
<!-- Start Profile Vertical Article -->
<article id="post-<?php the_ID(); ?>" <?php post_class('cmsmasters_profile_vertical'); ?>>
	<div class="profile_outer">
		<?php 
		if (has_post_thumbnail()) {
			echo '<div class="profile_image_wrap">';
			
				top_magazine_thumb(get_the_ID(), 'cmsmasters-square-thumb', true, false, false, false, false);
			
				if ($cmsmasters_profile_subtitle) {
					top_magazine_profile_heading(get_the_ID(), 'h3', $cmsmasters_profile_subtitle, 'h5', true);
				}
	
			echo '</div>'; 
		}
	
		echo '<div class="profile_inner">';
		
		top_magazine_profile_social_icons($cmsmasters_profile_social, $profile_id);
		
		if (!has_post_thumbnail() && $cmsmasters_profile_subtitle) {
			top_magazine_profile_heading(get_the_ID(), 'h3', $cmsmasters_profile_subtitle, 'h5');			
		} else {
			top_magazine_profile_heading(get_the_ID(), 'h3');
		}
		
		top_magazine_profile_exc_cont();
		
	echo '</div>';
	?>
	</div>
</article>
<!-- Finish Profile Vertical Article -->

