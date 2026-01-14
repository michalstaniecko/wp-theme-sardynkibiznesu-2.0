---
name: wordpress-plugin
description: Use this skill when creating or modifying WordPress plugins. Covers plugin architecture, hooks, shortcodes, asset enqueueing, and the ihumbak-* plugin pattern used in this project.
---

# WordPress Plugin Development

## Overview

This skill covers creating and modifying WordPress plugins following the `ihumbak-*` naming convention used in this project.

## Key Files

- `wp-content/plugins/ihumbak-*/` - Custom plugin directories
- `ihumbak-{name}/{name}.php` - Main plugin file

## Plugin Structure

```
wp-content/plugins/ihumbak-example/
├── ihumbak-example.php    # Main plugin file
├── inc/                   # PHP classes and includes
│   ├── ClassName.php
│   └── AnotherClass.php
├── css/                   # Plugin styles
│   └── style.css
├── js/                    # Plugin scripts
│   └── script.js
├── vendor/                # Composer dependencies (if needed)
│   └── autoload.php
└── languages/             # Translations
    └── ihumbak-example-pl_PL.po
```

## Patterns & Conventions

### Plugin Header

```php
<?php
/**
 *
 * Plugin Name: iHumbak Example
 * Description: Short description of plugin functionality
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: ihumbak-example
 *
 */
```

### Define Plugin Constants

```php
define('IH_EXAMPLE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IH_EXAMPLE_PLUGIN_URL', plugin_dir_url(__FILE__));
```

### Load Dependencies

```php
require_once IH_EXAMPLE_PLUGIN_DIR . 'vendor/autoload.php';
require_once IH_EXAMPLE_PLUGIN_DIR . 'inc/ClassName.php';
```

### Enqueue Assets

```php
add_action('wp_enqueue_scripts', 'ihumbak_example_scripts');

function ihumbak_example_scripts() {
    wp_enqueue_style(
        'ihumbak-example-style',
        plugin_dir_url(__FILE__) . 'css/style.css',
        array(),
        '1.0.0',
        'all'
    );

    wp_enqueue_script(
        'ihumbak-example-script',
        plugin_dir_url(__FILE__) . 'js/script.js',
        array('jquery'),
        '1.0.0',
        true
    );

    // Pass data to JavaScript
    wp_localize_script('ihumbak-example-script', 'ihumbakExample', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ihumbak_example_nonce')
    ));
}
```

### AJAX Handler

```php
add_action('wp_ajax_ihumbak_example_action', 'ihumbak_example_ajax_handler');
add_action('wp_ajax_nopriv_ihumbak_example_action', 'ihumbak_example_ajax_handler');

function ihumbak_example_ajax_handler() {
    check_ajax_referer('ihumbak_example_nonce', 'nonce');

    // Handle request
    $result = array('success' => true);

    wp_send_json($result);
}
```

### Load Text Domain

```php
add_action('init', 'ihumbak_example_load_textdomain');

function ihumbak_example_load_textdomain() {
    load_plugin_textdomain(
        'ihumbak-example',
        false,
        basename(dirname(__FILE__)) . '/languages/'
    );
}
```

### Register Shortcode

```php
add_shortcode('example_shortcode', 'ihumbak_example_shortcode');

function ihumbak_example_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => '',
        'class' => ''
    ), $atts, 'example_shortcode');

    ob_start();
    include IH_EXAMPLE_PLUGIN_DIR . 'templates/shortcode-template.php';
    return ob_get_clean();
}
```

## Best Practices

1. **Naming convention** - Use `ihumbak-` prefix for plugin directory and main file
2. **Prefix functions** - Use `ihumbak_pluginname_` prefix for all functions
3. **Use constants** - Define `PLUGIN_DIR` and `PLUGIN_URL` constants
4. **Security** - Always use nonces for AJAX requests
5. **Dependencies** - Use Composer for external libraries when needed
6. **Scripts location** - Load scripts in footer (`true` as last parameter)
