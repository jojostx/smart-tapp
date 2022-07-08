const mix = require("laravel-mix");

require("laravel-mix-tailwind");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js("resources/js/app.js", "public/js/app.js")
//     .postCss('resources/css/filament.css', 'public/filament.css')
//     .sass("resources/sass/app.scss", "public/css/app.css")
//     .tailwind("./tailwind.config.js")
//     .sourceMaps();

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ]).postCss('resources/css/filament.css', 'public/css', [
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .sourceMaps();

mix.js('resources/js/filament/forms/phoneinput', 'public/js/phoneinput.js')
    .sourceMaps();

if (mix.inProduction()) {
    mix.version();
}