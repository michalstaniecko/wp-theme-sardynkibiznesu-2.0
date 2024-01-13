<?php
/**
 * Block Name: Featured
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'featured-' . $block['id'];
if (!empty($block['anchor'])) {
  $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'sb-block-button';
if (!empty($block['className'])) {
  $className .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
  $className .= ' align' . $block['align'];
}

$label = get_field('sb-block__button--label');
$url = !empty(get_field('sb-block__button--url')) ? get_field('sb-block__button--url')[0] : '';
$url_outer = get_field('sb-block__url-outer');

$url = $url_outer ?: get_permalink($url);

?>

<p class="sb-block <?php echo esc_attr($className); ?>">
  <a href="<?php echo $url ?>" class="button button--bold" rel="nofollow" target="_blank">
    <?php echo $label ?: 'Sprawdź zawartość kursu'; ?>
  </a>
</p>
