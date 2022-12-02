<?php

add_shortcode('sb_button', 'sb_button');

function sb_button($atts) {
  $a = shortcode_atts(array(
    'url'   => '#',
    'label' => __('Sprawdź zawartość kursu', 'sb'),
    'rel' => 'nofollow'
  ), $atts);
  ob_start();
  ?>
  <a href="<?php echo $a['url'] ?>" target="_blank" class="button-outline" rel="<?php echo $a['rel'] ?>">
    <?php echo $a['label'] ?: 'Sprawdź zawartość kursu'; ?>
  </a>
  <?php

  return ob_get_clean();
}
