---
name: price-compare
description: Use this skill when working with the price comparison feature. Covers the custom post type, model architecture, shortcode rendering, and admin customization.
---

# Price Compare Module

## Overview

The Price Compare module is a custom feature for displaying price comparison tables. It uses a custom post type with ACF fields for table data and renders via shortcode.

## Key Files

- `inc/price-compare/price-compare.php` - Main class (singleton)
- `inc/price-compare/shortcodes.php` - Shortcode registration
- `inc/price-compare/model/` - Data model classes
  - `table-model.php`
  - `table-head-model.php`
  - `table-head-cell-model.php`
  - `table-row-model.php`
  - `table-rows-model.php`
  - `table-cell-model.php`
  - `table-section-model.php`
  - `table-sections-model.php`

## Architecture

### Namespace

```php
namespace SardynkiBiznesu\PriceCompare;
```

### Main Class (Singleton)

```php
namespace SardynkiBiznesu\PriceCompare;

use SardynkiBiznesu\PriceCompare\Model\TableModel;

class PriceCompare {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function __construct() {
        // Load model classes
        require_once get_stylesheet_directory() . '/inc/price-compare/model/table-model.php';
        // ... other model files

        $this->init();
    }

    public function init() {
        add_action('init', array($this, 'register_post_type'));
        add_filter('manage_price-compare_posts_columns', array($this, 'add_columns'));
        add_action('manage_price-compare_posts_custom_column', array($this, 'fill_shortcode_column'), 10, 2);

        Shortcodes::getInstance()->init();
    }
}
```

### Custom Post Type Registration

```php
public function register_post_type() {
    $labels = array(
        'name' => __('Price Compare', 'sardynkibiznesu'),
        'singular_name' => __('Price Compare', 'sardynkibiznesu'),
        'menu_name' => __('Price Compare', 'sardynkibiznesu'),
        'add_new' => __('Add New', 'sardynkibiznesu'),
        'add_new_item' => __('Add New Price Compare', 'sardynkibiznesu'),
        'edit_item' => __('Edit Price Compare', 'sardynkibiznesu'),
        'view_item' => __('View Price Compare', 'sardynkibiznesu'),
        'all_items' => __('All Price Compares', 'sardynkibiznesu'),
        'search_items' => __('Search Price Compares', 'sardynkibiznesu'),
        'not_found' => __('No price compares found.', 'sardynkibiznesu'),
        'not_found_in_trash' => __('No price compares found in Trash.', 'sardynkibiznesu')
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => false,
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'supports' => array('title'),
        'exclude_from_search' => true,
    );

    register_post_type('price-compare', $args);
}
```

### Admin Columns

Display shortcode in admin list view:

```php
public function add_columns($columns) {
    $columns['price-compare-shortcode'] = __('Shortcode', 'sardynkibiznesu');
    return $columns;
}

public function fill_shortcode_column($column, $post_id) {
    if ($column !== 'price-compare-shortcode') {
        return;
    }
    echo '[price_compare id="' . $post_id . '"]';
}
```

### Table Model

```php
namespace SardynkiBiznesu\PriceCompare\Model;

class TableModel {
    private $id;
    private $head;
    private $rows;
    private $sections;

    public function __construct($id) {
        $this->id = $id;
        $this->loadData();
    }

    private function loadData() {
        // Load ACF fields and build model
        $this->head = new TableHeadModel($this->id);
        $this->rows = new TableRowsModel($this->id);
        $this->sections = new TableSectionsModel($this->id);
    }

    public function getHead() {
        return $this->head;
    }

    public function getRows() {
        return $this->rows;
    }
}
```

## Shortcode Usage

```php
// In content
[price_compare id="123"]

// Shortcode handler
class Shortcodes {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function init() {
        add_shortcode('price_compare', array($this, 'render'));
    }

    public function render($atts) {
        $atts = shortcode_atts(array(
            'id' => ''
        ), $atts, 'price_compare');

        if (empty($atts['id'])) {
            return '';
        }

        $priceCompare = PriceCompare::getInstance();
        $table = $priceCompare->get_table($atts['id']);

        if ($table === null) {
            return '';
        }

        ob_start();
        include get_stylesheet_directory() . '/inc/price-compare/templates/table.php';
        return ob_get_clean();
    }
}
```

## Best Practices

1. **Use singleton pattern** - For manager classes
2. **Model-based architecture** - Separate data models for each table component
3. **Namespace everything** - Use `SardynkiBiznesu\PriceCompare` namespace
4. **Admin UX** - Show shortcode in admin columns for easy copying
5. **Private post type** - Keep `public => false` for internal-only content
6. **Error handling** - Check for null values before rendering
