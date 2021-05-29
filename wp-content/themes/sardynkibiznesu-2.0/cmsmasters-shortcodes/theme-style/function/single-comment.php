<?php 
/**
 * @package 	WordPress
 * @subpackage 	Top Magazine
 * @version 	1.1.0
 * 
 * Custom Single Comment Template
 * Created by CMSMasters
 * 
 */


function top_magazine_mytheme_comment($comment, $args, $depth) {
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
        <div id="comment-<?php comment_ID(); ?>" class="comment-body cmsmasters_comment_item">
			<figure class="cmsmasters_comment_item_avatar">
				<?php echo get_avatar($comment->comment_author_email, 50, get_option('avatar_default')) . "\n"; ?>
			</figure>
			<div class="comment-content cmsmasters_comment_item_cont">
				<div class="cmsmasters_comment_item_cont_info">
					<span class="fn cmsmasters_comment_item_title"><?php echo get_comment_author_link(); ?></span>
					<?php 
					echo '<span class="published cmsmasters_comment_item_date" title="' . get_comment_date() . '">' . 
						get_comment_date() . 
					'</span>';
					
					comment_reply_link(array_merge($args, array( 
						'depth' => $depth, 
						'max_depth' => $args['max_depth'], 
						'reply_text' => esc_attr__('Reply', 'top-magazine') 
					)));
					
					
					edit_comment_link(esc_attr__('Edit', 'top-magazine'), '', '');
					?>
				</div>
				<div class="cmsmasters_comment_item_content">
					<?php 
					comment_text();
					
					if ($comment->comment_approved == '0') {
						echo '<p>' . 
							'<em>' . esc_html__('Your comment is awaiting moderation.', 'top-magazine') . '</em>' . 
						'</p>';
					}
					?>
				</div>
			</div>
        </div>
    <?php 
}

