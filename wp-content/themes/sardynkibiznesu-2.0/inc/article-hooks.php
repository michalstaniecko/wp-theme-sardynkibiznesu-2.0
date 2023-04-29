<?php

add_filter('the_content', 'sb_about_author', 1);
function sb_about_author($content) {
  global $post;

  if (!is_single()) return $content;

  $author_id = get_post_field('post_author', $post->ID);
  $author_name = get_the_author_meta('display_name', $author_id);

  $title = $author_name;
  $img_url = get_avatar_url($author_id);
  $img = '<img src="%s" alt="$s" />';
  $img = sprintf($img, $img_url, $author_name);
  $description = get_the_author_meta('description', $author_id);
  $links = sb_get_author_links($author_id);
  $html = '<div class="article__about-author about-author">
                <div class="about-author__image">%s</div>
            <div class="row">
              <div class="col">
                <div class="about-author__meta-title">%s</div>
                <div class="about-author__title">%s</div>
                <div class="about-author__description">%s</div>
                %s
              </div>
            </div>
          </div>';
  $html = sprintf($html, $img, __('Autor', 'sb'), $title, $description, $links);
  $content .= $html;
  return $content;
}

function sb_link_icon($icon) {
  return '<i class="fa fa-'.$icon.'"></i>';
}

function sb_get_author_links($author_id) {
  $links = [];
  $link_schema = '<a href="%s" target="_blank" rel="nofollow">%s</a>';
  $www = get_the_author_meta('user_url', $author_id);
  $socials = [
    ['facebook', 'facebook'],
    ['instagram', 'instagram'],
    ['linkedin', 'linkedin'],
    ['youtube', 'youtube']
  ];
  if ($www) {
    $links[] = sprintf($link_schema, $www, sb_link_icon('link'));
  }
  foreach ($socials as $social) {
    $link = get_the_author_meta($social[0], $author_id);
    if ($link) {
      $links[] = sprintf($link_schema, $link, sb_link_icon($social[1]));
    }
  }

  if (!empty($links)) {
    return '<div class="about-author__links">'. implode(' ',  $links) .'</div>';
  } else {
    return '';
  }
}
