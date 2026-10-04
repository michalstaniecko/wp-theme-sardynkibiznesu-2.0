let mix = require( 'laravel-mix' )
let path = require( 'path' )
let tailwindcss = require( 'tailwindcss' )
require( 'laravel-mix-purgecss' )

const themePath = 'wp-content/themes/sardynkibiznesu-2.0'

if (!mix.inProduction()) {
    mix.sourceMaps( false, 'source-map' )
}


mix.js( 'src/js/index.js', `${themePath}/assets/index.js` )
    .sass( 'src/scss/main.scss', `${themePath}/assets/main.css` )
    .options( {
        processCssUrls: false,
        postCss: [ tailwindcss( 'tailwind.config.js' ) ]
    } )
    .purgeCss({
        content: [
            // Theme PHP files
            path.join(__dirname, 'wp-content/themes/sardynkibiznesu-2.0/**/*.php'),
            // Plugin PHP files
            path.join(__dirname, 'wp-content/plugins/**/*.php'),
            // JavaScript source
            path.join(__dirname, 'src/js/**/*.js'),
        ],
        safelist: {
            // Exact class names - only what's actually used
            standard: [
                // JavaScript dynamic classes
                'navigation--pushed',
                'is-navigation--pushed',
                'header--scrolled',
                'header__search--display',
                'scroll-to-top--active',
                'active',
                'disabled',

                // FAQ
                'faq__item--active',
                'faq__item',
                'faq__open-all',
                'faq__close-all',

                // Bootstrap states (essential for JS components)
                'show',
                'hide',
                'fade',
                'collapse',
                'collapsing',
                'collapsed',

                // WordPress alignment
                'aligncenter',
                'alignleft',
                'alignright',
                'alignwide',
                'alignfull',
                'alignnone',
                'has-text-align-center',
                'has-text-align-left',
                'has-text-align-right',

                // Form validation
                'is-valid',
                'is-invalid',
                'was-validated',

                // Glyphicons (legacy)
                'glyphicon',
                'glyphicon-user',
                'glyphicon-envelope',

                // Bootstrap display - specific classes used
                'd-none',
                'd-flex',
                'd-block',
                'd-lg-none',

                // Bootstrap flex - specific classes used
                'flex-grow-1',
                'flex-shrink-0',
                'align-items-center',

                // Bootstrap text
                'text-center',

                // Bootstrap width
                'w-100',

                // Bootstrap specific spacing used in templates
                'mb-0', 'mb-3', 'mb-5', 'mb-n4', 'mb-sm-0', 'mb-md-0',
                'mt-0', 'mt-3', 'mt-5', 'mt-auto',
                'me-3', 'ms-3', 'ms-auto',
                'p-2', 'p-3', 'p-4',
                'pb-3', 'pt-2',
                'px-5', 'px-md-5', 'pt-md-5',

                // cmsmasters specific
                'current_tab',
            ],

            // Regex patterns - more targeted
            deep: [
                // Bootstrap modal/offcanvas (essential for JS)
                /^modal/,
                /^offcanvas/,
                /modal-backdrop/,
                /offcanvas-backdrop/,

                // Bootstrap buttons (need all variants)
                /^btn/,

                // Custom button component
                /^button/,

                // Bootstrap grid (essential)
                /^col-/,
                /^row$/,
                /^container/,

                // cmsmasters (legacy shortcodes - many classes)
                /^cmsmasters/,
                /^owl-/,
                /^toggles_mode_/,
                /^tabs_mode_/,

                // WordPress classes
                /^wp-/,
                /^entry-/,
                /^page-template/,

                // WordPress menu classes (essential for multi-level menus)
                /^menu$/,          // .menu wrapper
                /^menu-item/,      // .menu-item, .menu-item-has-children
                /^sub-menu$/,      // .sub-menu wrapper
                /^current-menu/,   // .current-menu-ancestor, .current-menu-parent, .current-menu-item

                // Newsletter form
                /^newsletter-form/,
                /^single-newsletter/,
                /^notification/,
                /^error$/,

                // Bootstrap form classes
                /^form-control/,
                /^form-group/,
                /^form-check/,
                /^input-group/,

                // Custom theme components (BEM pattern)
                /^article__/,
                /^article--/,
                /^header__/,
                /^footer__/,
                /^sidebar/,
                /^navigation__/,
                /^navigation--/,
                /^product-card/,
                /^table-price/,
                /^table__/,
                /^table-cell/,
                /^table-border/,
                /^faq__/,
                /^gdpr__/,
                /^breadcrumbs/,
                /^scroll-to-top/,
                /^about-author/,
                /^podcast/,
                /^lists__/,
                /^pagination/,
                /^comments/,
                /^nav-posts/,
                /^images/,
                /^overflow/,

                // Anchor plugin
                /^ihumbak-anchor/,

                // Tax calculator
                /^tax__/,

                // PhotoSwipe
                /^pswp/,

                // Contact widget
                /^contact_widget/,
                /^adr$/,
                /^adress_wrap/,

                /^sidebar/,
                /^widget-about/,
            ],

            // Greedy - only patterns that truly need greedy matching
            greedy: [
                // Tailwind grid (essential for price-compare tables)
                /grid-cols-\d/,
                /col-span-\d/,
                /auto-cols-max/,
                /grid-flow-col/,

                // Tailwind gap (used in price-compare)
                /gap-[0-9.]+/,

                // Tailwind flex essentials
                /^flex$/,
                /flex-col/,
                /flex-wrap/,
                /items-center/,
                /items-start/,
                /justify-center/,
                /justify-between/,

                // Tailwind display responsive
                /^hidden$/,
                /lg:flex/,
                /lg:hidden/,
                /md:hidden/,
                /md:block/,

                // Tailwind text sizes actually used
                /text-sm/,
                /text-base/,
                /text-lg/,
                /text-xl/,
                /text-2xl/,
                /text-4xl/,
                /text-6xl/,
                /md:text-lg/,
                /md:text-xl/,
                /md:text-4xl/,
                /md:text-6xl/,

                // Tailwind font weights actually used
                /font-normal/,
                /font-medium/,
                /font-semibold/,
                /font-bold/,
                /font-mono/,

                // Tailwind colors actually used
                /text-white/,
                /text-gray-500/,
                /text-gray-900/,
                /text-red-800/,
                /text-link/,
                /bg-blue-500/,
                /border-gray-200/,

                // Tailwind positioning used
                /^relative$/,
                /^sticky$/,

                // Tailwind sizing used
                /w-full/,
                /h-full/,
                /min-w-0/,
                /object-contain/,

                // Tailwind border
                /^border$/,
                /border-b/,
                /^rounded$/,

                // Tailwind arbitrary values (critical for price-compare)
                /h-\[/,
                /w-\[/,
                /top-\[/,
                /bg-\[/,
                /text-\[/,
                /rounded-\[/,
                /p-[0-9]/,
                /m[tbxy]-\[/,
                /-mx-\[/,

                // Tailwind transitions
                /transition-\[/,
                /duration-300/,

                // Tailwind transforms
                /rotate-90/,

                // Responsive grid patterns (lg: md: sm:)
                /lg:grid-cols-/,
                /lg:col-span-/,
                /md:gap-/,
                /md:-mx-/,
                /lg:-mx-/,
            ]
        }
    })
    .setResourceRoot( '../assets/' )

mix.copyDirectory('src/fonts', `${themePath}/assets/fonts`);

mix.webpackConfig({
    watchOptions: {
        ignored: /node_modules|wp-content\/themes\/sardynkibiznesu-2.0\/assets/,
    }
});

mix.sass( 'src/scss/admin.scss', `${themePath}/assets/admin.css` )
mix.js( 'src/js/admin.js', `${themePath}/assets/admin.js` )
