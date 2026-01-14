---
name: acf-fields
description: Use this skill when working with Advanced Custom Fields (ACF). Covers field group configuration, Local JSON, using fields in templates, and ACF block integration.
---

# Advanced Custom Fields (ACF)

## Overview

This project uses ACF Pro with Local JSON for field group management. Field definitions are stored as JSON files for version control.

## Key Files

- `wp-content/themes/sardynkibiznesu-2.0/acf-fields/` - Local JSON storage
- `inc/acf.php` - ACF configuration
- `inc/acf-blocks.php` - ACF block registration

## Local JSON

### Configuration

ACF Local JSON is configured to save field groups to the theme:

```php
// In inc/acf.php
add_filter('acf/settings/save_json', function($path) {
    return get_stylesheet_directory() . '/acf-fields';
});

add_filter('acf/settings/load_json', function($paths) {
    $paths[] = get_stylesheet_directory() . '/acf-fields';
    return $paths;
});
```

### Workflow

1. Create/edit field group in WordPress Admin > ACF
2. Field group JSON is automatically saved to `acf-fields/`
3. Commit JSON file to version control
4. Other environments load from JSON automatically

## Using ACF in Templates

### Basic Field Retrieval

```php
// Get field value
$value = get_field('field_name');

// Get field from specific post
$value = get_field('field_name', $post_id);

// Display field directly
the_field('field_name');

// Check if field has value
if (get_field('field_name')) {
    // Field has value
}
```

### Repeater Fields

```php
<?php if (have_rows('repeater_field')): ?>
    <ul>
    <?php while (have_rows('repeater_field')): the_row(); ?>
        <li>
            <h3><?php the_sub_field('title'); ?></h3>
            <p><?php the_sub_field('description'); ?></p>
        </li>
    <?php endwhile; ?>
    </ul>
<?php endif; ?>
```

### Flexible Content

```php
<?php if (have_rows('flexible_content')): ?>
    <?php while (have_rows('flexible_content')): the_row(); ?>
        <?php if (get_row_layout() == 'text_block'): ?>
            <div class="text-block">
                <?php the_sub_field('content'); ?>
            </div>
        <?php elseif (get_row_layout() == 'image_block'): ?>
            <div class="image-block">
                <?php $image = get_sub_field('image'); ?>
                <img src="<?php echo esc_url($image['url']); ?>"
                     alt="<?php echo esc_attr($image['alt']); ?>">
            </div>
        <?php endif; ?>
    <?php endwhile; ?>
<?php endif; ?>
```

### Group Fields

```php
<?php $group = get_field('group_field'); ?>
<?php if ($group): ?>
    <h2><?php echo esc_html($group['title']); ?></h2>
    <p><?php echo wp_kses_post($group['description']); ?></p>
<?php endif; ?>
```

### Image Fields

```php
<?php $image = get_field('image_field'); ?>
<?php if ($image): ?>
    <!-- Return format: Array -->
    <img src="<?php echo esc_url($image['sizes']['medium']); ?>"
         alt="<?php echo esc_attr($image['alt']); ?>"
         width="<?php echo esc_attr($image['sizes']['medium-width']); ?>"
         height="<?php echo esc_attr($image['sizes']['medium-height']); ?>">
<?php endif; ?>
```

### Link Fields

```php
<?php $link = get_field('link_field'); ?>
<?php if ($link): ?>
    <a href="<?php echo esc_url($link['url']); ?>"
       target="<?php echo esc_attr($link['target']); ?>">
        <?php echo esc_html($link['title']); ?>
    </a>
<?php endif; ?>
```

## ACF in Gutenberg Blocks

### Block Template Access

In block render templates, ACF fields are accessed the same way:

```php
<?php
// blocks/example/example.php
$title = get_field('title');
$items = get_field('items');
$block_id = 'block-' . $block['id'];
?>

<div id="<?php echo esc_attr($block_id); ?>" class="example-block">
    <?php if ($title): ?>
        <h2><?php echo esc_html($title); ?></h2>
    <?php endif; ?>

    <?php if ($items): ?>
        <?php foreach ($items as $item): ?>
            <div class="item">
                <?php echo esc_html($item['name']); ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
```

### Raw Block Data Access

For schema generation, access raw block data:

```php
// In seo-schema.php
add_filter('wpseo_schema_block_sardynkibiznesu20/faq', function($graph, $block, $context) {
    $data = $block['attrs']['data'];

    for ($i = 0; $i < $data['faq_questions']; $i++) {
        $question = $data["faq_questions_{$i}_question"];
        $answer = $data["faq_questions_{$i}_answer"];
        // Process data...
    }

    return $graph;
}, 10, 3);
```

## Best Practices

1. **Use Local JSON** - Always save field groups to version control
2. **Field naming** - Use snake_case: `field_name`, not `fieldName`
3. **Return formats** - Set appropriate return format in field settings
4. **Always escape output** - Use `esc_html()`, `esc_attr()`, `wp_kses_post()`
5. **Check for values** - Always check if field has value before displaying
6. **Sync after import** - After pulling changes, sync field groups in ACF admin
