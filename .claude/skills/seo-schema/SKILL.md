---
name: seo-schema
description: Use this skill when working with SEO, structured data (JSON-LD), Schema.org markup, or meta tags optimization. Covers Yoast SEO integration, FAQ schema, and custom schema generation.
---

# SEO & Schema.org Structured Data

## Overview

This project uses structured data (JSON-LD) for SEO, integrated with Yoast SEO plugin. Schema markup is generated for specific content types like FAQ blocks.

## Key Files

- `inc/seo-schema.php` - Custom schema generation
- Yoast SEO plugin handles base schema

## Schema Integration with Yoast

### Adding Schema for Custom Blocks

Use Yoast's schema filter to add structured data:

```php
add_filter('wpseo_schema_block_sardynkibiznesu20/faq', 'render_faq_block_schema', 10, 3);

function render_faq_block_schema($graph, $block, $context) {
    $data = $block['attrs']['data'];
    $questions = [];

    for ($i = 0; $i < $data['faq_questions']; $i++) {
        $questions[] = array(
            "@type" => "Question",
            "name" => $data["faq_questions_{$i}_question"],
            "acceptedAnswer" => array(
                "@type" => "Answer",
                "text" => $data["faq_questions_{$i}_answer"]
            )
        );
    }

    $graph[] = array(
        "@type" => "FAQPage",
        "mainEntity" => $questions
    );

    return $graph;
}
```

### Filter Naming Convention

Filter name format: `wpseo_schema_block_{namespace}/{block-name}`

Example: `wpseo_schema_block_sardynkibiznesu20/faq`

## Common Schema Types

### FAQPage Schema

```php
$schema = array(
    "@type" => "FAQPage",
    "mainEntity" => array(
        array(
            "@type" => "Question",
            "name" => "What is the question?",
            "acceptedAnswer" => array(
                "@type" => "Answer",
                "text" => "This is the answer."
            )
        )
    )
);
```

### Article Schema

```php
$schema = array(
    "@type" => "Article",
    "headline" => get_the_title(),
    "author" => array(
        "@type" => "Person",
        "name" => get_the_author()
    ),
    "datePublished" => get_the_date('c'),
    "dateModified" => get_the_modified_date('c'),
    "image" => get_the_post_thumbnail_url(null, 'full'),
    "publisher" => array(
        "@type" => "Organization",
        "name" => get_bloginfo('name'),
        "logo" => array(
            "@type" => "ImageObject",
            "url" => get_site_icon_url()
        )
    )
);
```

### Product Schema

```php
$schema = array(
    "@type" => "Product",
    "name" => $product_name,
    "description" => $product_description,
    "image" => $product_image,
    "offers" => array(
        "@type" => "Offer",
        "price" => $price,
        "priceCurrency" => "PLN",
        "availability" => "https://schema.org/InStock",
        "url" => get_permalink()
    )
);
```

### Review Schema

```php
$schema = array(
    "@type" => "Review",
    "itemReviewed" => array(
        "@type" => "Product",
        "name" => $product_name
    ),
    "reviewRating" => array(
        "@type" => "Rating",
        "ratingValue" => "4.5",
        "bestRating" => "5"
    ),
    "author" => array(
        "@type" => "Person",
        "name" => $author_name
    )
);
```

## Manual JSON-LD Output

If not using Yoast filter, output manually:

```php
add_action('wp_head', 'output_custom_schema');

function output_custom_schema() {
    if (!is_singular('product')) {
        return;
    }

    $schema = array(
        "@context" => "https://schema.org",
        "@type" => "Product",
        "name" => get_the_title(),
        // ... other properties
    );

    echo '<script type="application/ld+json">';
    echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    echo '</script>';
}
```

## Accessing Block Data for Schema

### ACF Block Data Structure

When accessing ACF repeater data from block attributes:

```php
$data = $block['attrs']['data'];

// Repeater count
$count = $data['repeater_field'];

// Repeater item access pattern
for ($i = 0; $i < $count; $i++) {
    $item_field = $data["repeater_field_{$i}_subfield_name"];
}
```

### Example: FAQ Block Data

```php
// Field structure: faq_questions (repeater) > question, answer
$data = $block['attrs']['data'];
$question_count = $data['faq_questions'];

for ($i = 0; $i < $question_count; $i++) {
    $question = $data["faq_questions_{$i}_question"];
    $answer = $data["faq_questions_{$i}_answer"];
}
```

## Best Practices

1. **Use Yoast filters** - Integrate with existing schema rather than duplicating
2. **Validate schema** - Test with Google Rich Results Test
3. **Escape output** - Use `wp_json_encode()` for safe JSON output
4. **Match content** - Schema must match visible page content
5. **Required properties** - Include all required properties for each schema type
6. **Test rich results** - Verify in Google Search Console
