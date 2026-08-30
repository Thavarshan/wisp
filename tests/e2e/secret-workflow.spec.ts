import { expect, test } from '@playwright/test';

test.describe('secret workflow', () => {
    test('creates and consumes an unprotected secret once', async ({ page }) => {
        await page.goto('/');
        await page.getByLabel('Secret content').fill('a local one-time message');
        await page.getByRole('button', { name: 'Create secure link' }).click();

        await expect(page.getByRole('heading', { name: 'Your secure link is ready' })).toBeVisible();
        const shareUrl = await page.locator('#share-link').inputValue();

        await page.goto(shareUrl);
        await page.getByRole('button', { name: 'Reveal and consume' }).click();
        await page.getByRole('dialog').getByRole('button', { name: 'Reveal now' }).click();

        await expect(page.getByText('This secret has been consumed.')).toBeVisible();
        await expect(page.locator('#revealed-secret')).toHaveValue('a local one-time message');

        const secondVisit = await page.goto(shareUrl);
        expect(secondVisit?.status()).toBe(404);
    });

    test('supports keyboard password protection on a mobile viewport', async ({ page }) => {
        await page.goto('/');
        await page.getByLabel('Secret content').fill('a protected local message');
        await page.getByRole('checkbox', { name: 'Password protect this secret' }).press('Space');

        await expect(page.locator('#secret-password')).toBeVisible();
        await expect(page.locator('#secret-password')).toHaveValue(/\S{12}/);

        await page.getByRole('button', { name: 'Create secure link' }).click();
        await expect(page.getByRole('heading', { name: 'Your secure link is ready' })).toBeVisible();
        await expect(page.getByText('Separate password')).toBeVisible();
    });
});
