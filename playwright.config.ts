import { defineConfig, devices } from '@playwright/test';

const appEnvironment = process.env.E2E_APP_ENV ?? 'local';

export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    reporter: 'list',
    use: {
        baseURL: 'http://127.0.0.1:8001',
        trace: 'retain-on-failure',
    },
    projects: [
        { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
        {
            name: 'mobile-chromium',
            use: { ...devices['iPhone 13'], browserName: 'chromium' },
        },
    ],
    webServer: {
        command: `node tests/e2e/prepare-db.mjs && APP_ENV=${appEnvironment} APP_URL=http://127.0.0.1:8001 CACHE_STORE=array DB_CONNECTION=sqlite DB_DATABASE=storage/wisp-e2e.sqlite php artisan migrate:fresh --force && APP_ENV=${appEnvironment} APP_URL=http://127.0.0.1:8001 CACHE_STORE=array DB_CONNECTION=sqlite DB_DATABASE=storage/wisp-e2e.sqlite php artisan serve --host=127.0.0.1 --port=8001`,
        url: 'http://127.0.0.1:8001',
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
    },
});
