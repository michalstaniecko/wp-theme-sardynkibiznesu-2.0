<?php

/**
 * @var $args        array
 * @var $title       string
 * @var $image_id    numeric
 * @var $label       array
 * @var $category    array
 * @var $description string
 * @var $button      string
 */

$default_args = array(
  'title'       => 'Sardynki Biznesu',
  'image'       => 250,
  'label'       => array(
    'text'       => null,
    'background' => '#90CBBF',
    'color'      => '#ffffff'
  ),
  'category'    => array(
    'name' => 'Produkt',
    'url'  => 'https://sardynkibiznesu.pl'
  ),
  'description' => 'Tabelki Excel, kalkulatory, plannery, wzory grafik i dokumenty. Niezbędne materiały dla małej firmy.',
  'button'      => 'https://sardynkibiznesu.pl'
);

extract(wp_parse_args($args, $default_args));

$image_src = wp_get_attachment_image_src($image, 'product-card');
$image_alt = get_post_meta($image, '_wp_attachment_image_alt', true);

?>

<div class="product-card">
  <a class="product-card__image no-lazy-load"
     href="<?php echo $button ?>">
    <?php if (!empty($image)): ?>
      <img src="<?php echo $image_src[0] ?>"
           alt="<?php echo $image_alt ?>"
           width="<?php echo $image_src[1] ?>"
           height="<?php echo $image_src[2] ?>"
      />
    <?php endif; ?>
  </a>
  <div class="product-card__body">
    <?php get_template_part('components/product-card/label', '', $label) ?>
    <div class="product-card__title">
      <a href="<?php echo $button ?>" title="<?php echo $title ?>"><?php echo $title ?></a>
    </div>
    <div class="product-card__description">
      <?php echo $description ?>
    </div>
  </div>
  <div class="product-card__footer">
    <a href="<?php echo $button ?>" title="<?php echo $title ?>"
       target="_blank"
       class="button w-100">
      <?php _e('Zobacz więcej', 'sb') ?>
    </a>
  </div>
</div>
