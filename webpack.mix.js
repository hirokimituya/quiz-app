const mix = require('laravel-mix');
require('vuetifyjs-mix-extension');
//require('laravel-mix-bundle-analyzer');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.browserSync('localhost')
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .vuetify('vuetify-loader')
    .vue()
    .version()
    .webpackConfig(require('./webpack.config'));

//if (!mix.inProduction()) {
//    mix.bundleAnalyzer();
//}
