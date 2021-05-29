<?php
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version		1.1.0
 * 
 * Profile Horizontal Template
 * Created by CMSMasters
 * 
 */


$columns_num = '';

if ($profile_columns == 1) {
	$columns_num = 'one_first';
} elseif ($profile_columns == 2) {
	$columns_num = 'one_half';
} elseif ($profile_columns == 3) {
	$columns_num = 'one_third';
} elseif ($profile_columns == 4) {
	$columns_num = 'one_fourth';
}


$cmsmasters_profile_social = get_post_meta(get_the_ID(), 'cmsmasters_profile_social', true);

$cmsmasters_profile_subtitle = get_post_meta(get_the_ID(), 'cmsmasters_profile_subtitle', true);

?>
<!-- Start Profile Horizontal Article -->
<article id="post-<?php the_ID(); ?>" <?php post_class('cmsmasters_profile_horizontal ' . $columns_num); ?>>
	<div class="profile_outer">
		<?php 
		if (has_post_thumbnail() || $cmsmasters_profile_subtitle) {
			echo '<div class="profile_image_wrap">';
			
				if (has_post_thumbnail()) {
					top_magazine_thumb(get_the_ID(), 'cmsmasters-square-thumb', true, false, false, false, false);
				}
		
				if ($cmsmasters_profile_subtitle) {
					top_magazine_profile_heading(get_the_ID(), 'h3', $cmsmasters_profile_subtitle, 'h5', true);
				}
				
			echo '</div>'; 
		}
	
		echo '<div class="profile_inner">';
			
			top_magazine_profile_heading(get_the_ID(), 'h3');
			
			top_magazine_profile_exc_cont();
			
			top_magazine_profile_social_icons($cmsmasters_profile_social, $profile_id);
			
		echo '</div>';
		?>
	</div>
</article>
<!-- Finish Profile Horizontal Article -->

