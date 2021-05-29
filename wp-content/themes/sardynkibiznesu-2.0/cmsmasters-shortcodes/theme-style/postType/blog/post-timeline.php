<?php
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version		1.1.0
 * 
 * Post Timeline Template
 * Created by CMSMasters
 * 
 */


$cmsmasters_post_metadata = !is_home() ? explode(',', $cmsmasters_metadata) : array();


$date = (in_array('date', $cmsmasters_post_metadata) || is_home()) ? true : false;
$excerpt = (in_array('excerpt', $cmsmasters_post_metadata) || is_home()) ? true : false;
$categories = (get_the_category() && (in_array('categories', $cmsmasters_post_metadata) || is_home())) ? true : false;
$author = (in_array('author', $cmsmasters_post_metadata) || is_home()) ? true : false;
$comments = (comments_open() && (in_array('comments', $cmsmasters_post_metadata) || is_home())) ? true : false;
$likes = (in_array('likes', $cmsmasters_post_metadata) || is_home()) ? true : false;
$views = (in_array('views', $cmsmasters_post_metadata) || is_home()) ? true : false;
$more = (in_array('more', $cmsmasters_post_metadata) || is_home()) ? true : false;


$cmsmasters_post_format = get_post_format();

?>
<!-- Start Post Timeline Article -->
<article id="post-<?php the_ID(); ?>" <?php post_class('cmsmasters_post_timeline'); ?>>
	<?php 
	if ($date) {
		echo '<div class="cmsmasters_post_info entry-meta">';
		
			top_magazine_get_post_date('page', 'timeline');
			
		echo '</div>';
	}
	?>
	<div class="cmsmasters_timeline_margin">
		<div class="cmsmasters_post_cont_wrap">
			<?php
				if (!post_password_required()) {
					if ($cmsmasters_post_format == 'video') {
						$cmsmasters_post_video_type = get_post_meta(get_the_ID(), 'cmsmasters_post_video_type', true);
						$cmsmasters_post_video_link = get_post_meta(get_the_ID(), 'cmsmasters_post_video_link', true);
						$cmsmasters_post_video_links = get_post_meta(get_the_ID(), 'cmsmasters_post_video_links', true);
						
						top_magazine_post_type_video(get_the_ID(), $cmsmasters_post_video_type, $cmsmasters_post_video_link, $cmsmasters_post_video_links);
					} elseif (
						has_post_thumbnail() || 
						$categories
					) {
						echo '<div class="cmsmasters_post_img_wrap">';
							if ($cmsmasters_post_format == 'image') {
								$cmsmasters_post_image_link = get_post_meta(get_the_ID(), 'cmsmasters_post_image_link', true);
								
								top_magazine_post_type_image(get_the_ID(), $cmsmasters_post_image_link);
							} elseif ($cmsmasters_post_format == 'gallery') {
								$cmsmasters_post_images = explode(',', str_replace(' ', '', str_replace('img_', '', get_post_meta(get_the_ID(), 'cmsmasters_post_images', true))));
								
								$slider_data = ' data-auto-height="false"';
								
								top_magazine_post_type_slider(get_the_ID(), $cmsmasters_post_images, 'post-thumbnail', false, $slider_data);
							} elseif (has_post_thumbnail()) {
								top_magazine_thumb(get_the_ID(), 'post-thumbnail', true, false, true, false, true, true, false);
							} 
							
							$categories ? top_magazine_get_post_category(get_the_ID(), 'category', 'page') : '';
							
						echo '</div>';
					}
				}
			?>

			<div class="cmsmasters_post_cont">
				<?php
				if ($cmsmasters_post_format == 'video') {
					$categories ? top_magazine_get_post_category(get_the_ID(), 'category', 'page') : '';
				}
				
				top_magazine_post_heading(get_the_ID(), 'h3');
				
				
				if ($cmsmasters_post_format == 'audio') {
					$cmsmasters_post_audio_links = get_post_meta(get_the_ID(), 'cmsmasters_post_audio_links', true);
					
					top_magazine_post_type_audio($cmsmasters_post_audio_links);
				}
				
				
				if ($excerpt) {
					top_magazine_post_exc_cont();
				}
				
				
				$more ? top_magazine_post_more(get_the_ID()) : '';
				
				
				if ($author || $views || $comments || $likes) {
					echo '<footer class="cmsmasters_post_footer entry-meta">';
						
						$author ? top_magazine_get_post_author('page') : '';
						
						$views ? top_magazine_get_post_views('page') : '';
						
						$comments ? top_magazine_get_post_comments('page') : '';
						
						$likes ? top_magazine_get_post_likes('page') : '';
						
					echo '</footer>';
				}
				?>
			</div>
		</div>
	</div>
</article>
<!-- Finish Post Timeline Article -->

