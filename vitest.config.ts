import vue from '@vitejs/plugin-vue';
import { resolve } from 'node:path';
import { defineConfig } from 'vitest/config';

export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            '@': resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    test: {
        environment: 'jsdom',
        include: ['tests/frontend/**/*.spec.ts'],
        setupFiles: ['./tests/frontend/setup.ts'],
        globals: true,
    },
});
