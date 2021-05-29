<?php
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version		1.1.0
 * 
 * Posts Slider Project Template
 * Created by CMSMasters
 * 
 */


$cmsmasters_metadata = explode(',', $cmsmasters_project_metadata);


$title = in_array('title', $cmsmasters_metadata) ? true : false;
$excerpt = (in_array('excerpt', $cmsmasters_metadata) && top_magazine_slider_post_check_exc_cont('project')) ? true : false;
$categories = (get_the_terms(get_the_ID(), 'pj-categs') && in_array('categories', $cmsmasters_metadata)) ? true : false;
$comments = (comments_open() && in_array('comments', $cmsmasters_metadata)) ? true : false;
$likes = in_array('likes', $cmsmasters_metadata) ? true : false;


$cmsmasters_project_link_url = get_post_meta(get_the_ID(), 'cmsmasters_project_link_url', true);
$cmsmasters_project_link_redirect = get_post_meta(get_the_ID(), 'cmsmasters_project_link_redirect', true);
$cmsmasters_project_link_target = get_post_meta(get_the_ID(), 'cmsmasters_project_link_target', true);


$cmsmasters_post_format = get_post_format();

?>
<!-- Start Posts Slider Project Article -->
<article id="post-<?php the_ID(); ?>" <?php post_class('cmsmasters_slider_project'); ?>>
	<div class="cmsmasters_slider_project_outer">
	<?php 
		if ($title || $categories || $excerpt || $likes || $comments) {
			echo '<div class="cmsmasters_slider_project_inner_wrap">' . 
					'<div class="cmsmasters_slider_project_inner">';
					if ($likes || $comments) {
						echo '<div class="cmsmasters_slider_project_meta entry-meta">';
							
							($likes) ? top_magazine_slider_post_like('project') : '';
							
							($comments) ? top_magazine_get_slider_post_comments('project') : '';
							
						echo '</div>';
					}
					
					
					if ($categories) {
						echo '<div class="cmsmasters_slider_project_cont_info entry-meta">';
							
							top_magazine_get_slider_post_category(get_the_ID(), 'pj-categs', 'project');
							
						echo '</div>';
					}
					
					
					$title ? top_magazine_slider_post_heading(get_the_ID(), 'project', 'h4', $cmsmasters_project_link_redirect, $cmsmasters_project_link_url, true, $cmsmasters_project_link_target) : '';
					
					
					$excerpt ? top_magazine_slider_post_exc_cont('project') : '';
				echo '</div>' . 
			'</div>';
		}
		
		
		top_magazine_thumb_rollover(get_the_ID(), 'cmsmasters-project-thumb', false, false, false, false, false, false, false, false, true, $cmsmasters_project_link_redirect, $cmsmasters_project_link_url);
	?>
	</div>
</article>
<!-- Finish Posts Slider Project Article -->

