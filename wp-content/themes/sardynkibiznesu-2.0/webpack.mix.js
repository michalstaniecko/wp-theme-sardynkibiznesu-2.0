let mix = require('laravel-mix')
let path = require('path')
require('laravel-mix-purgecss')

if (!mix.inProduction()) {
  mix.sourceMaps(false, 'source-map')
}


mix.js('src/js/index.js', '')
  .sass('src/scss/main.scss', '')
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
  .setResourceRoot('../assets/')
  .setPublicPath('assets')

mix.sass('src/scss/admin.scss', '')
mix.js('src/js/admin.js', '')
