---
name: gutenberg-blocks
description: Use this skill when creating or modifying Gutenberg blocks with ACF. Covers block.json configuration, render templates, block registration, and JavaScript interactivity.
---

# Gutenberg Blocks Development

## Overview

This project uses two methods for creating Gutenberg blocks:
1. **Native ACF blocks** - Using `block.json` with ACF mode (preferred)
2. **Legacy ACF blocks** - Using `acf_register_block()` function

## Key Files

- `blocks/` - Native block directories with block.json
- `template-blocks/` - Render templates for legacy blocks
- `inc/acf-blocks.php` - Block registration
- `acf-fields/` - ACF field group JSON files

## Method 1: Native ACF Blocks (Preferred)

### Directory Structure

```
blocks/block-name/
├── block.json        # Block definition
├── block-name.php    # Render template
├── block-name.css    # Block styles (optional)
└── block-name.js     # Block interactivity (optional)
```

### block.json Example

```json
{
    "name": "sardynkibiznesu20/block-name",
    "title": "Block Title",
    "description": "Block description.",
    "style": ["file:./block-name.css"],
    "category": "text",
    "icon": "admin-comments",
    "keywords": ["keyword1", "keyword2"],
    "acf": {
        "mode": "edit",
        "renderTemplate": "block-name.php"
    },
    "script": "file:./block-name.js"
}
```

### Block Registration

Register in `inc/acf-blocks.php`:

```php
function sb_register_acf_blocks() {
    register_block_type(
        untrailingslashit(get_stylesheet_directory()) . '/blocks/block-name'
    );
}
add_action('init', 'sb_register_acf_blocks');
```

### Render Template (block-name.php)

```php
<?php
$questions = get_field('questions');
$block_id = 'block-' . $block['id'];
$class_name = 'sb-block-name';

if (!empty($block['className'])) {
    $class_name .= ' ' . $block['className'];
}
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>">
    <?php if ($questions): ?>
        <?php foreach ($questions as $question): ?>
            <div class="item">
                <h3><?php echo esc_html($question['title']); ?></h3>
                <p><?php echo wp_kses_post($question['content']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
```

## Method 2: Legacy ACF Blocks

### Registration with acf_register_block()

```php
add_action('acf/init', 'sb_acf_init');

function sb_acf_init() {
    if (function_exists('acf_register_block')) {
        acf_register_block(array(
            'name' => 'sb-block-name',
            'title' => __('Block Title'),
            'description' => __('Block description.'),
            'render_callback' => 'sb_acf_block_render_callback',
            'category' => 'formatting',
            'icon' => 'admin-comments',
            'keywords' => array('keyword1', 'keyword2'),
        ));
    }
}
```

### Render Callback

```php
function sb_acf_block_render_callback($block, $content, $is_preview) {
    $slug = str_replace('acf/', '', $block['name']);
    $category = $block['category'];

    if (file_exists(get_theme_file_path("/template-blocks/{$category}-{$slug}.php"))) {
        include(get_theme_file_path("/template-blocks/{$category}-{$slug}.php"));
    }
}
```

### Template File Naming

Template files follow pattern: `template-blocks/{category}-{slug}.php`

Example: `template-blocks/formatting-sb-button.php`

## JavaScript Interactivity

```javascript
// blocks/block-name/block-name.js
document.addEventListener('DOMContentLoaded', function() {
    const blocks = document.querySelectorAll('.sb-block-name');

    blocks.forEach(function(block) {
        // Initialize block functionality
        const items = block.querySelectorAll('.item');

        items.forEach(function(item) {
            item.addEventListener('click', function() {
                this.classList.toggle('active');
            });
        });
    });
});
```

## Best Practices

1. **Use native blocks** - Prefer `block.json` method for new blocks
2. **Block naming** - Use `sardynkibiznesu20/` namespace prefix
3. **ACF fields** - Create field group in ACF and export to `acf-fields/`
4. **Escape output** - Use `esc_html()`, `esc_attr()`, `wp_kses_post()`
5. **Block ID** - Use `$block['id']` for unique identifiers
6. **Inline CSS/JS** - Keep styles and scripts in separate files
