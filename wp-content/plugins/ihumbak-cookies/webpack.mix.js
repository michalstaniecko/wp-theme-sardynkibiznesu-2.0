const mix = require('laravel-mix');

mix.setPublicPath('dist');

mix.sourceMaps(false, 'source-map');

mix.js('src/js/ihumbak-cookies.js', 'js');
mix.sass('src/scss/main.scss', 'css');
