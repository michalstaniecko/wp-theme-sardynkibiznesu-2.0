<?php

/**
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @var   bool $is_preview True during backend preview render.
 * @param   int $post_id The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param   array $context The context provided to the block by the post or it's parent block.
 */

$buttons = get_field('button');

if (empty($buttons) && !$is_preview) return;

if (empty($buttons) && $is_preview):
?>

<div>
    <strong>Add podcast buttons</strong>
</div>

<?php
endif;

$link_tag = $is_preview ? 'span' : 'a';

$image_path = get_stylesheet_directory_uri() . '/images/podcast-buttons';

?>

<div class="podcast-buttons">
    <?php foreach ($buttons as $button): ?>
    <?php $image = $image_path . '/' . $button['type'] .'.png' ;?>
    <div>
        <<?php echo $link_tag ?> href="<?php echo $button['url']?>" target="_blank">
            <img src="<?php echo $image ?>" alt="" />
        </<?php echo $link_tag ?>>
    </div>

    <?php endforeach; ?>
</div>
