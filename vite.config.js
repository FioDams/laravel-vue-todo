import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        // host: 'localhost',
        hmr: {
            host: 'localhost',
            // port: 5173,
        },
        watch: {
            userPolling: true,
        }
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        vue({
            // template: {
            //     transformAssetUrls: {
            //         base: null,
            //         includeAbsoluteUrls: false,
            //     }
            // }
        }),
        tailwindcss(),
    ],
});
