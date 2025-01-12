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
    /*.purgeCss({
      content: [
        path.join(__dirname, '**!/!*.php'),
        path.join(__dirname, '../../plugins/!**!/!*.php'),
        path.join(__dirname, 'src/js/!*.js')
      ],
      safelist: {
        deep: [
          /navigation/,
          /widget-about/,
          /single-newsletter-form--widget/,
          /entry-content-wrapper/,
          /alignright/,
          /mt-/,
          /mb-/,
          /w-/,
          /#comments/,
          /wpdcom/,
          /cmsmasters/,
          /wp-caption/,
          /wp-block-image/,
          /button-outline/,
          /aligncenter/,
          /alignleft/,
          /alignright/,
          /page-template-boxed-without-sidebar/,
          /btn/,
          /btn-light/,
          /modal-backdrop/,
          /offcanvas-backdrop/,
          /fade/,
          /show/,
            /button/,
        ]
      }
    })*/
    .setResourceRoot( '../assets/' )

mix.copyDirectory('src/fonts', `${themePath}/assets/fonts`);

mix.sass( 'src/scss/admin.scss', `${themePath}/assets/admin.css` )
mix.js( 'src/js/admin.js', `${themePath}/assets/admin.js` )
