import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/css/filament.css',
            'resources/css/filament.css',
            'resources/js/app.js',
            'resources/js/app.js',
            'resources/js/filament/forms/phoneinput',
        ]),
    ],
});
