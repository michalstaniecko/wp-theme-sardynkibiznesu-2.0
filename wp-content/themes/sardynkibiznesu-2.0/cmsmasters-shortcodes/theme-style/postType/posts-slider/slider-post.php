<?php
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version		1.1.0
 * 
 * Posts Slider Post Template
 * Created by CMSMasters
 * 
 */


$cmsmasters_metadata = explode(',', $cmsmasters_post_metadata);


$title = in_array('title', $cmsmasters_metadata) ? true : false;
$excerpt = (in_array('excerpt', $cmsmasters_metadata) && top_magazine_slider_post_check_exc_cont('post')) ? true : false;
$date = in_array('date', $cmsmasters_metadata) ? true : false;
$categories = (get_the_category() && (in_array('categories', $cmsmasters_metadata))) ? true : false;
$author = in_array('author', $cmsmasters_metadata) ? true : false;
$comments = (comments_open() && (in_array('comments', $cmsmasters_metadata))) ? true : false;
$likes = in_array('likes', $cmsmasters_metadata) ? true : false;
$views = in_array('views', $cmsmasters_metadata) ? true : false;
$more = in_array('more', $cmsmasters_metadata) ? true : false;


$cmsmasters_post_format = get_post_format();

?>
<!-- Start Posts Slider Post Article -->
<article id="post-<?php the_ID(); ?>" <?php post_class('cmsmasters_slider_post'); ?>>
	<div class="cmsmasters_slider_post_outer">
	<?php
		if (
			has_post_thumbnail() || 
			$categories
		) {
			echo '<div class="cmsmasters_slider_post_img_wrap">';
				top_magazine_thumb_rollover(get_the_ID(), 'cmsmasters-project-thumb', false, false, false, false, false, false, false, false, true, false, false);
			
				$categories ? top_magazine_get_slider_post_category(get_the_ID(), 'category', 'post') : '';	
			echo '</div>';
		}
		
		
		if ($title || $author || $excerpt || $likes || $comments || $more) {
			echo '<div class="cmsmasters_slider_post_inner">';
				
				$title ? top_magazine_slider_post_heading(get_the_ID(), 'post', 'h3') : '';
				
				$excerpt ? top_magazine_slider_post_exc_cont('post') : '';				
				
				$more ? top_magazine_slider_post_more(get_the_ID()) : '';
				
				if ($date || $author || $views || $comments || $likes) {
					echo '<footer class="cmsmasters_slider_post_footer entry-meta">';
					
						$date ? top_magazine_get_slider_post_date('post') : '';
						
						$author ? top_magazine_get_slider_post_author('post') : '';
						
						$views ? top_magazine_slider_post_views('post') : '';
						
						$comments ? top_magazine_get_slider_post_comments('post') : '';
						
						$likes ? top_magazine_slider_post_like('post') : '';
						
					echo '</footer>';
				}
				
			echo '</div>';
		}
	?>
	</div>
</article>
<!-- Finish Posts Slider Post Article -->

