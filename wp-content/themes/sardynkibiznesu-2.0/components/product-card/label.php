<?php

/**
 * @var $args array
 */

if (empty($args['text'])) return false;

?>
<div class="product-card__label" style="background-color: <?php echo $args['background'] ?>; color: <?php echo $args['color'] ?>">
  <?php echo $args['text'] ?>
</div>
