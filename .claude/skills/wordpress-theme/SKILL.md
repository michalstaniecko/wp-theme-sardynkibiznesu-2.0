---
name: wordpress-theme
description: Use this skill when working with WordPress theme files, templates, hooks, filters, or theme architecture. Covers functions.php, template hierarchy, inc/ modules, and theme customization.
---

# WordPress Theme Development

## Overview

This skill covers WordPress theme development for the Sardynki Biznesu 2.0 theme located at `wp-content/themes/sardynkibiznesu-2.0/`.

## Key Files

- `functions.php` - Main entry point, loads all includes
- `inc/` - Feature modules (acf.php, theme-enqueue.php, menus.php, etc.)
- `header.php`, `footer.php`, `sidebar.php` - Layout partials
- `single.php`, `archive.php`, `page.php`, `index.php` - Templates
- `template-blocks/` - ACF block render templates

## Patterns & Conventions

### Loading Feature Modules

Use `include_once` with `get_stylesheet_directory()`:

```php
include_once get_stylesheet_directory() . '/inc/feature-name.php';
```

### Environment Detection

Use filter for local vs production environment:

```php
if (false === apply_filters('sb_is_local_env', false)) {
    // Production-only code (tracking, analytics)
}
```

### Singleton Pattern for Managers

Feature managers use singleton pattern:

```php
namespace SardynkiBiznesu\FeatureName;

class FeatureManager {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    public function init() {
        add_action('init', array($this, 'register_hooks'));
    }
}
```

### Theme Initialization

Load features in `sb_load_theme()` function:

```php
function sb_load_theme() {
    \SardynkiBiznesu\FeatureName\FeatureManager::getInstance();
    new SomeOtherFeature();
}
sb_load_theme();
```

### Asset Enqueueing

Use `filemtime()` for cache busting in `inc/theme-enqueue.php`:

```php
wp_enqueue_style(
    'main-style',
    get_stylesheet_directory_uri() . '/assets/main.css',
    array(),
    filemtime(get_stylesheet_directory() . '/assets/main.css')
);
```

## Best Practices

1. **Never hardcode paths** - Always use `get_stylesheet_directory()` or `get_stylesheet_directory_uri()`
2. **Separate concerns** - One feature per file in `inc/` directory
3. **Use namespaces** - For complex features like `SardynkiBiznesu\FeatureName`
4. **Environment-aware loading** - Use `sb_is_local_env` filter for production-only features
5. **Text domain** - Use `sardynkibiznesu` for translations: `__('Text', 'sardynkibiznesu')`
