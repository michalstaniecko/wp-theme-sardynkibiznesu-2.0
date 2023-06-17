<?php

/**
 * FAQ Block template
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param int    $post_id    The post ID the block is rendering content against.
 *                           This is either the post ID currently being displayed inside a query loop,
 *                           or the post ID of the post hosting this block.
 * @param array  $context    The context provided to the block by the post or it's parent block.
 * @var bool     $is_preview True during backend preview render.
 */

$title = get_field('faq_title');
$questions = get_field('faq_questions');

?>

<div class="faq">
  <div class="h1 faq__title"><?php echo $title ?></div>
  <div class="faq__list">
    <?php foreach ($questions as $index => $item): ?>
      <div class="faq__item">
        <div class="h2 faq__question"><a class="faq__link" href="#" data-target="question-<?php echo $index ?>"><?php echo $item['question']; ?></a></div>
        <div class="faq__answer" id="question-<?php echo $index ?>">
          <div>
            <?php echo $item['answer']; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
