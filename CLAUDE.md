# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WordPress website "Sardynki Biznesu 2.0" - a Polish business information portal with custom theme and plugins.

## Build Commands

```bash
npm run development    # Webpack watch mode for development
npm run production     # Optimized production build
```

## Local Development

Docker environment with WordPress and MySQL 8.0:
```bash
docker-compose up      # Start local environment
```

Environment variables can be set via `.env` file (DB_USER, DB_PASSWORD, DB_NAME).

## Architecture

### Build System
- **Laravel Mix + Webpack** for asset compilation
- **Source files**: `src/js/` and `src/scss/`
- **Output**: `wp-content/themes/sardynkibiznesu-2.0/assets/`
- **Configuration**: `webpack.mix.js` (primary), `tailwind.config.js`

Entry points:
- `src/js/index.js` → `assets/index.js` (frontend)
- `src/js/admin.js` → `assets/admin.js` (admin panel)
- `src/scss/main.scss` → `assets/main.css`
- `src/scss/admin.scss` → `assets/admin.css`

### Theme Structure (`wp-content/themes/sardynkibiznesu-2.0/`)

```
functions.php           # Main entry, loads all includes
inc/                    # Feature modules
├── acf.php            # ACF field groups
├── acf-blocks.php     # Gutenberg block registration
├── price-compare/     # Price comparison feature (namespaced)
├── one-time-offer/    # Conversion feature
├── theme-enqueue.php  # Asset loading
└── ...
blocks/                 # Custom Gutenberg blocks (with block.json)
template-blocks/        # ACF block render templates
components/             # Reusable UI components
cmsmasters-shortcodes/  # Legacy shortcode system
```

### Custom Plugins (`wp-content/plugins/`)

Project-specific plugins prefixed with `ihumbak-`:
- `ihumbak-anchor-2` - Table of Contents generation
- `ihumbak-newsletter-form` - MailerLite integration
- `ihumbak-analytics` - Analytics tracking
- `ihumbak-cookies` - Cookie consent
- `ihumbak-tax-calculator` - Tax calculation tool

### Key Patterns

**PHP Architecture:**
- Singleton pattern for feature managers: `PriceCompare::getInstance()`
- Namespace: `SardynkiBiznesu\PriceCompare\*`
- Class-based plugins with standard WordPress hooks

**JavaScript:**
- Modular structure with ES6 imports
- Features initialize on `window load` event
- jQuery available globally

**Environment Detection:**
- `apply_filters('sb_is_local_env', false)` controls local vs production behavior
- Tracking codes only load in production

## Key Dependencies

- Bootstrap 5.2.3
- Tailwind CSS 3.4.3
- Advanced Custom Fields Pro (embedded in theme)
- PhotoSwipe 4.1.3
- jQuery Validation 1.19.3
