<?php

/**
 * Accordion Block template
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param int $post_id The post ID the block is rendering content against.
 *                           This is either the post ID currently being displayed inside a query loop,
 *                           or the post ID of the post hosting this block.
 * @param array $context The context provided to the block by the post or it's parent block.
 * @var bool $is_preview True during backend preview render.
 */

$title = get_field('accordion_title');
$questions = get_field('accordion_questions');

?>

<div class="faq faq--accordion">
    <h2 class="h1 ihumbak-anchor faq__title"><?php echo $title ?></h2>
    <div class="faq__list" data-force-open-all="1">
        <div class="">
            <button class="button button-small faq__open-all">Rozwiń wszystko</button>
            <button class="button button-small button-outline faq__close-all">Zwiń wszystko</button>
        </div>
        <div class="faq__column">
            <?php foreach ($questions as $index => $item): ?>
                <div class="faq__item <?php echo $index == 0 ? 'faq__item--active' : null ?>">
                    <div class="h2 faq__question">
                        <a
                                class="faq__link" href="#"
                                data-target="question-<?php echo $index ?>"
                        >
                            <i class="fa fa-caret-right faq__link-icon"></i>
                            <?php echo $item['question']; ?>
                        </a>
                    </div>
                    <div class="faq__answer" id="question-<?php echo $index ?>">
                        <div>
                            <?php echo $item['answer']; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
