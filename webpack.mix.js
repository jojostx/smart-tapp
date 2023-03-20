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

mix.js("resources/js/app.js", "public/js")
    .postCss("resources/css/app.css", "public/css", [
        require("postcss-import"),
        require("tailwindcss"),
        require("autoprefixer"),
    ])
    .postCss("resources/css/filament.css", "public/css", [
        require("tailwindcss"),
        require("autoprefixer"),
    ])
    .sourceMaps();

mix.js(
    "resources/js/filament/forms/phoneinput",
    "public/js/phoneinput.js"
).sourceMaps();

mix.js("resources/js/qr-scanner.js", "public/js").version().sourceMaps();
mix.js("resources/js/filament/table/actionable-text-column.js", "public/js/actionable-text-column.js").version().sourceMaps();

mix.js("resources/js/filament/filament-turbo.js", "public/js/filament-turbo.js").version().sourceMaps();
mix.js("resources/js/filament/filament-stimulus.js", "public/js/filament-stimulus.js").version().sourceMaps();
mix.js("resources/js/filament/reload-listener.js", "public/js/reload-listener.js").version().sourceMaps();

if (mix.inProduction()) {
    mix.version();
}
