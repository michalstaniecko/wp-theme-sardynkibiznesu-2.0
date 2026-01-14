---
name: analytics
description: Use this skill when working with analytics, tracking codes, or conversion tracking. Covers Google Analytics (GA4), Facebook Pixel, Google Tag Manager, and affiliate tracking integration.
---

# Analytics & Tracking

## Overview

This project implements various tracking solutions including Google Analytics, Facebook Pixel, and affiliate tracking. Tracking codes load only in production environment.

## Key Files

- `wp-content/plugins/ihumbak-analytics/ihumbak-analytics.php` - Main analytics plugin
- `inc/tracking-codes.php` - Additional tracking (loaded conditionally)

## Environment-Based Loading

Tracking codes only load in production:

```php
// In functions.php
if (false === apply_filters('sb_is_local_env', false)) {
    include_once get_stylesheet_directory() . '/inc/tracking-codes.php';
}
```

## Google Analytics (GA4)

### Enqueue GTM Script

```php
add_action('wp_enqueue_scripts', 'ga_script');

function ga_script() {
    wp_enqueue_script(
        'gtm-script',
        'https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX',
        array(),
        null,
        true
    );
}
```

### Initialize GA4

```php
add_action('wp_head', 'ga_code');

function ga_code() {
    ?>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-XXXXXXXXXX');
    </script>
    <?php
}
```

### Track Custom Events

```php
// In PHP (inline script)
?>
<script>
    gtag('event', 'button_click', {
        'event_category': 'engagement',
        'event_label': 'CTA Button',
        'value': 1
    });
</script>
<?php

// Or in JavaScript
gtag('event', 'purchase', {
    'transaction_id': '12345',
    'value': 99.99,
    'currency': 'PLN'
});
```

### Non-Bounce Tracking

Track engaged users (over 10 seconds on page):

```php
?>
<script>
    setTimeout(function() {
        gtag('event', 'Over 10 seconds', {
            'event_category': 'NoBounce'
        });
    }, 10000);
</script>
<?php
```

## Facebook Pixel

### Initialize Pixel

```php
add_action('wp_head', 'fb_pixel_code');

function fb_pixel_code() {
    ?>
    <script>
        var eventID = Date.now().toString() + "<?php echo md5(uniqid(rand(), true)); ?>";

        !function(f,b,e,v,n,t,s) {
            if(f.fbq)return;
            n=f.fbq=function(){
                n.callMethod ? n.callMethod.apply(n,arguments) : n.queue.push(arguments)
            };
            if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.defer=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)
        }(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');

        fbq('init', 'PIXEL_ID');
        fbq('track', 'PageView', {}, {eventID: eventID});
    </script>
    <?php
}
```

### Server-Side Events (Conversions API)

```php
// AJAX handler for server-side tracking
add_action('wp_ajax_fcapi_page_view', 'handle_fcapi_page_view');
add_action('wp_ajax_nopriv_fcapi_page_view', 'handle_fcapi_page_view');

function handle_fcapi_page_view() {
    $event_id = sanitize_text_field($_POST['eventID']);
    $source_url = esc_url_raw($_POST['sourceUrl']);

    // Send to Facebook Conversions API
    // ... API call implementation

    wp_send_json_success();
}
```

### Track Pixel Events (JavaScript)

```javascript
// Standard events
fbq('track', 'ViewContent', {
    content_name: 'Product Name',
    content_category: 'Category',
    value: 99.99,
    currency: 'PLN'
});

fbq('track', 'AddToCart', {
    content_ids: ['SKU123'],
    content_type: 'product',
    value: 99.99,
    currency: 'PLN'
});

fbq('track', 'Purchase', {
    content_ids: ['SKU123'],
    content_type: 'product',
    value: 99.99,
    currency: 'PLN'
});

// Custom events
fbq('trackCustom', 'NewsletterSignup', {
    source: 'footer_form'
});
```

## Affiliate Tracking

### ShareASale Verification

```php
add_action('wp_head', 'shareasale_verification');

function shareasale_verification() {
    echo '<!-- SHAREASALE-VERIFICATION-TOKEN -->';
}
```

### IR Site Verification

```php
add_action('wp_head', 'ir_verification');

function ir_verification() {
    echo '<meta name="ir-site-verification-token" value="TOKEN_VALUE"/>';
}
```

## Conversion Tracking in JavaScript

```javascript
// src/js/ads-conversions.js
export default class AdsConversions {
    constructor() {
        if (AdsConversions.instance) {
            return AdsConversions.instance;
        }
        AdsConversions.instance = this;
        this.init();
    }

    init() {
        this.trackOutboundLinks();
        this.trackFormSubmissions();
    }

    trackOutboundLinks() {
        document.querySelectorAll('a[href^="http"]').forEach(link => {
            if (link.hostname !== window.location.hostname) {
                link.addEventListener('click', () => {
                    gtag('event', 'click', {
                        'event_category': 'outbound',
                        'event_label': link.href
                    });
                });
            }
        });
    }

    trackFormSubmissions() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
                gtag('event', 'form_submission', {
                    'event_category': 'engagement',
                    'event_label': form.id || 'unknown'
                });
            });
        });
    }
}
```

## Best Practices

1. **Production only** - Never load tracking in development
2. **Event deduplication** - Use unique eventID for Facebook Pixel
3. **Non-blocking** - Load scripts with `defer` or in footer
4. **Privacy compliance** - Integrate with cookie consent
5. **Test tracking** - Use browser extensions to verify events
6. **Document events** - Keep list of tracked events for reference
