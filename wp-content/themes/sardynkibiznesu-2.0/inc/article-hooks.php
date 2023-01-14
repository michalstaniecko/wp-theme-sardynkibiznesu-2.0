<?php

add_filter('the_content', 'sb_about_author', 1);
function sb_about_author($content) {
  global $post;

  if (!is_super_admin()) return $content;

  if (!is_single()) return $content;

  $author_id = get_post_field('post_author', $post->ID);
  $author_name = get_the_author_meta('display_name', $author_id);

  $title = __('O autorze', 'sb') . ' - ' . $author_name;
  $img_url = get_avatar_url($author_id);
  $img = '<img src="%s" alt="$s" />';
  $img = sprintf($img, $img_url, $author_name);
  $description = get_the_author_meta('description', $author_id);
  $html = '<div class="article__about-author about-author">
            <div class="about-author__title">%s</div>
            <div class="row">
              <div class="col-auto">
              
                <div class="about-author__image">%s</div>
                </div>
              <div class="col">
              
                <div class="about-author__description">%s</div>
              </div>
            </div>
          </div>';
  $html = sprintf($html, $title, $img, $description);
  $content .= $html;
  return $content;
}
