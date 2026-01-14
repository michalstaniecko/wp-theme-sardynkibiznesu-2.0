---
name: frontend-design
description: Use this skill when working with CSS, SCSS, design system, responsive layouts, or styling. Covers Tailwind CSS utilities, Bootstrap 5 components, SCSS architecture, fonts, and responsive breakpoints.
---

# Frontend Design

## Overview

This project uses a combination of SCSS, Tailwind CSS, and Bootstrap 5 for styling. Source files are in `src/scss/` and compile to `wp-content/themes/sardynkibiznesu-2.0/assets/`.

## Key Files

- `src/scss/main.scss` - Main frontend stylesheet
- `src/scss/admin.scss` - Admin panel stylesheet
- `tailwind.config.js` - Tailwind configuration
- `src/scss/global/` - Typography, fonts, alignments
- `src/scss/components/` - UI components (buttons, forms, cards)
- `src/scss/objects/` - Page layouts (archive, sidebar)
- `src/scss/vendors/` - Bootstrap and third-party imports

## SCSS Architecture (ITCSS Pattern)

```
src/scss/
├── main.scss           # Entry point - imports all partials
├── admin.scss          # Admin-specific styles
├── global/
│   ├── _fonts.scss     # Font face definitions
│   ├── _typography.scss
│   └── _alignments.scss
├── objects/
│   ├── _archive.scss   # Archive page layout
│   ├── _sidebar.scss   # Sidebar layout
│   └── _comments.scss  # Comments section
├── components/
│   ├── _buttons.scss   # Button styles
│   ├── _header.scss    # Header component
│   ├── _footer.scss    # Footer component
│   ├── _navigation.scss
│   ├── _forms.scss
│   ├── _article.scss
│   ├── _faq.scss
│   └── _tables.scss
└── vendors/
    ├── _bootstrap.scss # Bootstrap imports
    └── _photoswipe.scss
```

## Patterns & Conventions

### Import Order in main.scss

```scss
// 1. Vendors (Bootstrap, third-party)
@import 'vendors/bootstrap';

// 2. Global settings
@import 'global/fonts';
@import 'global/typography';
@import 'global/alignments';

// 3. Objects (layouts)
@import 'objects/archive';
@import 'objects/sidebar';

// 4. Components (UI elements)
@import 'components/buttons';
@import 'components/header';
@import 'components/navigation';
```

### Using Tailwind Utilities

Tailwind is integrated via PostCSS. Use utility classes directly in HTML/PHP:

```html
<div class="flex items-center justify-between mt-4 mb-8">
    <h2 class="text-2xl font-bold">Title</h2>
    <button class="px-4 py-2 bg-blue-500 text-white rounded">Action</button>
</div>
```

### Using Bootstrap Components

Bootstrap 5.2.3 is available. Import specific components:

```scss
// In vendors/_bootstrap.scss
@import "~bootstrap/scss/functions";
@import "~bootstrap/scss/variables";
@import "~bootstrap/scss/mixins";
@import "~bootstrap/scss/grid";
@import "~bootstrap/scss/buttons";
@import "~bootstrap/scss/modal";
@import "~bootstrap/scss/accordion";
```

### Font Definitions

Project uses Montserrat and Font Awesome:

```scss
// In global/_fonts.scss
@font-face {
    font-family: 'Montserrat';
    src: url('../fonts/montserrat/Montserrat-Regular.woff2') format('woff2'),
         url('../fonts/montserrat/Montserrat-Regular.woff') format('woff');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}
```

### Component Styling Pattern

```scss
// In components/_component-name.scss
.component-name {
    // Base styles
    padding: 1rem;
    background: #fff;

    // Child elements
    &__header {
        font-size: 1.25rem;
        font-weight: 600;
    }

    &__content {
        margin-top: 0.5rem;
    }

    // States
    &--active {
        border-color: #007bff;
    }

    // Responsive
    @media (min-width: 768px) {
        padding: 2rem;
    }
}
```

## Responsive Breakpoints

Tailwind breakpoints (from tailwind.config.js):
- `sm`: 640px
- `md`: 768px
- `lg`: 1024px
- `xl`: 1280px
- `2xl`: 1536px

Bootstrap breakpoints:
- `sm`: 576px
- `md`: 768px
- `lg`: 992px
- `xl`: 1200px
- `xxl`: 1400px

## Best Practices

1. **Use BEM naming** - `.block__element--modifier` pattern
2. **Mobile-first** - Write base styles for mobile, add breakpoints for larger screens
3. **Prefer Tailwind** - For utility/spacing classes, use Tailwind
4. **Use Bootstrap** - For complex components (modals, accordions, dropdowns)
5. **Custom components** - Create in `src/scss/components/`
6. **Never edit assets/** - Only edit files in `src/scss/`
7. **Run build** - After changes: `npm run development`
