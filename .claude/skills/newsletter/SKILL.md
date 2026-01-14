---
name: newsletter
description: Use this skill when working with newsletter forms, email marketing integration, or subscription functionality. Covers MailerLite and GetResponse integration, form validation, and AJAX submission.
---

# Newsletter & Email Marketing Integration

## Overview

This project integrates with MailerLite (primary) and GetResponse (fallback) for email marketing. The newsletter plugin handles form rendering, validation, and subscription via AJAX.

## Key Files

- `wp-content/plugins/ihumbak-newsletter-form/ihumbak-newsletter-form.php` - Main plugin file
- `wp-content/plugins/ihumbak-newsletter-form/inc/Newsletter.php` - Base newsletter class
- `wp-content/plugins/ihumbak-newsletter-form/inc/NewsletterMailerLite.php` - MailerLite integration
- `wp-content/plugins/ihumbak-newsletter-form/inc/newsletter-form.php` - Form template
- `wp-content/plugins/ihumbak-newsletter-form/getResponse/` - GetResponse integration

## Plugin Structure

```
ihumbak-newsletter-form/
├── ihumbak-newsletter-form.php   # Main plugin file
├── inc/
│   ├── Newsletter.php            # Base class
│   ├── NewsletterMailerLite.php  # MailerLite API
│   ├── newsletter-form.php       # Form shortcode
│   └── getresponse-form.php      # GetResponse form
├── getResponse/
│   ├── addContact.php            # Add contact handler
│   └── getresponse.js            # Frontend script
├── css/
│   └── newsletter-form.css       # Form styles
├── js/
│   ├── bootstrap.min.js          # Bootstrap for modals
│   └── jquery-validation/        # Form validation
├── vendor/                       # Composer dependencies
└── languages/                    # Translations
```

## Assets Enqueueing

```php
add_action('wp_enqueue_scripts', 'plugin_theme_scripts');

function plugin_theme_scripts() {
    // Styles
    wp_enqueue_style(
        'newsletter-form',
        plugin_dir_url(__FILE__) . '/css/newsletter-form.css',
        array(),
        '1.0.0',
        'all'
    );

    // Bootstrap (for modals)
    wp_enqueue_script(
        'bootstrap.min',
        plugin_dir_url(__FILE__) . '/js/bootstrap.min.js',
        array('jquery'),
        '5.2.3',
        true
    );

    // jQuery Validation
    wp_enqueue_script(
        'jquery-validation',
        plugin_dir_url(__FILE__) . 'js/jquery-validation/dist/jquery.validate.js',
        array('jquery'),
        '1.19.3',
        true
    );

    // Polish validation messages
    wp_enqueue_script(
        'jquery-validation-pl',
        plugin_dir_url(__FILE__) . 'js/jquery-validation/dist/localization/messages_pl.js',
        array('jquery-validation'),
        '1.19.3',
        true
    );

    // Main script
    wp_enqueue_script(
        'newsletter-form-script',
        plugin_dir_url(__FILE__) . 'getResponse/getresponse.js',
        array('bootstrap.min', 'jquery-validation'),
        '1.0.0',
        true
    );

    // Localize AJAX URL
    wp_localize_script('newsletter-form-script', 'grAddContact', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
```

## Form Template

```php
// In inc/newsletter-form.php
function newsletter_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'group_id' => '',
        'style' => 'default'
    ), $atts, 'newsletter_form');

    ob_start();
    ?>
    <form id="newsletter-form" class="newsletter-form newsletter-form--<?php echo esc_attr($atts['style']); ?>">
        <div class="form-group">
            <input type="email"
                   name="email"
                   class="form-control"
                   placeholder="<?php esc_attr_e('Your email address', 'ihumbak-newsletter-form'); ?>"
                   required>
        </div>
        <div class="form-group">
            <input type="text"
                   name="name"
                   class="form-control"
                   placeholder="<?php esc_attr_e('Your name', 'ihumbak-newsletter-form'); ?>">
        </div>
        <input type="hidden" name="group_id" value="<?php echo esc_attr($atts['group_id']); ?>">
        <input type="hidden" name="action" value="newsletter_subscribe">
        <?php wp_nonce_field('newsletter_subscribe', 'newsletter_nonce'); ?>
        <button type="submit" class="btn btn-primary">
            <?php esc_html_e('Subscribe', 'ihumbak-newsletter-form'); ?>
        </button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('newsletter_form', 'newsletter_form_shortcode');
```

## JavaScript Form Handling

```javascript
// getResponse/getresponse.js
(function($) {
    $(document).ready(function() {
        $('#newsletter-form').validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                name: {
                    minlength: 2
                }
            },
            submitHandler: function(form) {
                var $form = $(form);
                var $button = $form.find('button[type="submit"]');

                $button.prop('disabled', true).text('Sending...');

                $.ajax({
                    url: grAddContact.ajax_url,
                    type: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Show success modal
                            $('#newsletter-success-modal').modal('show');
                            $form[0].reset();
                        } else {
                            alert(response.data.message || 'Error occurred');
                        }
                    },
                    error: function() {
                        alert('Connection error. Please try again.');
                    },
                    complete: function() {
                        $button.prop('disabled', false).text('Subscribe');
                    }
                });
            }
        });
    });
})(jQuery);
```

## AJAX Handler (MailerLite)

```php
add_action('wp_ajax_newsletter_subscribe', 'handle_newsletter_subscribe');
add_action('wp_ajax_nopriv_newsletter_subscribe', 'handle_newsletter_subscribe');

function handle_newsletter_subscribe() {
    check_ajax_referer('newsletter_subscribe', 'newsletter_nonce');

    $email = sanitize_email($_POST['email']);
    $name = sanitize_text_field($_POST['name']);
    $group_id = sanitize_text_field($_POST['group_id']);

    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Invalid email address'));
    }

    $newsletter = new NewsletterMailerLite();
    $result = $newsletter->subscribe($email, $name, $group_id);

    if ($result['success']) {
        wp_send_json_success(array('message' => 'Successfully subscribed'));
    } else {
        wp_send_json_error(array('message' => $result['message']));
    }
}
```

## MailerLite Integration Class

```php
// inc/NewsletterMailerLite.php
class NewsletterMailerLite extends Newsletter {
    private $api_key;
    private $api_url = 'https://api.mailerlite.com/api/v2/';

    public function __construct() {
        $this->api_key = defined('MAILERLITE_API_KEY') ? MAILERLITE_API_KEY : '';
    }

    public function subscribe($email, $name, $group_id) {
        $response = wp_remote_post($this->api_url . 'groups/' . $group_id . '/subscribers', array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-MailerLite-ApiKey' => $this->api_key
            ),
            'body' => json_encode(array(
                'email' => $email,
                'name' => $name,
                'resubscribe' => true
            ))
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }

        $code = wp_remote_retrieve_response_code($response);

        if ($code >= 200 && $code < 300) {
            return array('success' => true);
        }

        return array('success' => false, 'message' => 'Subscription failed');
    }
}
```

## Bootstrap Modal for Success

```html
<!-- Success modal -->
<div class="modal fade" id="newsletter-success-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thank you!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>You have successfully subscribed to our newsletter.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
```

## Best Practices

1. **Validate server-side** - Always validate email on server
2. **Use nonces** - Protect AJAX endpoints with wp_nonce
3. **Store API keys** - Use constants in wp-config.php, not in code
4. **Provide feedback** - Show loading states and success/error messages
5. **Polish translations** - Include Polish validation messages
6. **Bootstrap modals** - Use for success/error feedback
7. **Progressive enhancement** - Form should work without JavaScript
