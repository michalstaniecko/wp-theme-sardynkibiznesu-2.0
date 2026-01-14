---
name: frontend-develop
description: Use this skill when working with JavaScript, build system, or frontend tooling. Covers ES6 modules, Laravel Mix, Webpack configuration, and JavaScript patterns used in this project.
---

# Frontend Development

## Overview

This project uses Laravel Mix (Webpack wrapper) for asset compilation. JavaScript source files are in `src/js/` and compile to `wp-content/themes/sardynkibiznesu-2.0/assets/`.

## Key Files

- `webpack.mix.js` - Laravel Mix configuration
- `package.json` - NPM dependencies and scripts
- `src/js/index.js` - Main frontend entry point
- `src/js/admin.js` - Admin panel entry point
- `src/js/*.js` - Feature modules

## Build Commands

```bash
npm run development    # Watch mode with source maps
npm run production     # Optimized production build
```

## JavaScript Architecture

### Entry Point (index.js)

```javascript
// Import side-effect modules (auto-initialize)
import './navigation'
import './search-toggler'
import './scroll-to-top'

// Import modules that need manual initialization
import tablePrice from "./table-price";
import AdsConversions from "./ads-conversions";
import { countdownTimer } from "./countdown-timer";

// Initialize on window load
window.addEventListener('load', () => {
    new AdsConversions();
    tablePrice();
    countdownTimer();
});
```

### Module Patterns

**Side-Effect Module** (auto-initializes):

```javascript
// navigation.js
const menuToggle = document.querySelector('.menu-toggle');
const navigation = document.querySelector('.navigation');

if (menuToggle && navigation) {
    menuToggle.addEventListener('click', () => {
        navigation.classList.toggle('is-open');
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (!navigation.contains(e.target) && !menuToggle.contains(e.target)) {
            navigation.classList.remove('is-open');
        }
    });
}
```

**Class-Based Module** (singleton pattern):

```javascript
// ads-conversions.js
export default class AdsConversions {
    constructor() {
        if (AdsConversions.instance) {
            return AdsConversions.instance;
        }
        AdsConversions.instance = this;

        this.init();
    }

    init() {
        this.setupConversionTracking();
    }

    setupConversionTracking() {
        // Implementation
    }
}
```

**Function Module** (default export):

```javascript
// table-price.js
export default function tablePrice() {
    const tables = document.querySelectorAll('.price-table');

    tables.forEach(table => {
        // Initialize table functionality
    });
}
```

**Named Export Module**:

```javascript
// countdown-timer.js
export function countdownTimer() {
    const timers = document.querySelectorAll('[data-countdown]');

    timers.forEach(timer => {
        const targetDate = new Date(timer.dataset.countdown);
        updateTimer(timer, targetDate);
    });
}

function updateTimer(element, targetDate) {
    // Implementation
}
```

## Webpack Configuration

### webpack.mix.js Structure

```javascript
let mix = require('laravel-mix');
let path = require('path');
let tailwindcss = require('tailwindcss');
require('laravel-mix-purgecss');

const themePath = 'wp-content/themes/sardynkibiznesu-2.0';

// Source maps only in development
if (!mix.inProduction()) {
    mix.sourceMaps(false, 'source-map');
}

// JavaScript compilation
mix.js('src/js/index.js', `${themePath}/assets/index.js`)
   .js('src/js/admin.js', `${themePath}/assets/admin.js`);

// SCSS compilation with Tailwind
mix.sass('src/scss/main.scss', `${themePath}/assets/main.css`)
   .sass('src/scss/admin.scss', `${themePath}/assets/admin.css`)
   .options({
       processCssUrls: false,
       postCss: [tailwindcss('tailwind.config.js')]
   });

// Copy fonts
mix.copyDirectory('src/fonts', `${themePath}/assets/fonts`);

// Watch options (ignore compiled files)
mix.webpackConfig({
    watchOptions: {
        ignored: /node_modules|wp-content\/themes\/sardynkibiznesu-2.0\/assets/
    }
});
```

## Working with jQuery

jQuery is available globally via WordPress:

```javascript
// Option 1: Use window.jQuery
window.addEventListener('load', () => {
    jQuery('.element').on('click', function() {
        // ...
    });
});

// Option 2: Use $ alias inside IIFE
(function($) {
    $(document).ready(function() {
        $('.element').on('click', function() {
            // ...
        });
    });
})(jQuery);
```

## Best Practices

1. **Initialize on load** - Use `window.addEventListener('load', ...)` for DOM-dependent code
2. **Use ES6 modules** - Import/export for code organization
3. **Singleton for global features** - Use class pattern with static instance
4. **Keep modules focused** - One feature per file
5. **Check element existence** - Always verify DOM elements before attaching listeners
6. **Never edit assets/** - Only edit files in `src/js/`
7. **Use const/let** - Avoid `var`
