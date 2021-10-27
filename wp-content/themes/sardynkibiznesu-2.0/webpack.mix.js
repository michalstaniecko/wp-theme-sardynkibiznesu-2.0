let mix = require('laravel-mix')
let path = require('path')
require('laravel-mix-purgecss')


mix.js('src/js/index.js', '')
  .sass('src/scss/main.scss', '')
  .purgeCss({
    content: [
      path.join(__dirname, '**/*.php'),
      path.join(__dirname, '../../plugins/**/*.php'),
      path.join(__dirname, 'src/js/*.js')
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
        /wp-block-image/
      ]
    }
  })
  .setResourceRoot('../assets/')
  .setPublicPath('assets')
