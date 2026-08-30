import { expect, test } from '@playwright/test';

test.describe('secret workflow', () => {
    test('strict CSP has no browser policy violations', async ({ page }) => {
        const violations: string[] = [];
        page.on('console', (message) => {
            if (message.type() === 'error' && message.text().includes('Content Security Policy')) {
                violations.push(message.text());
            }
        });

        await page.goto('/');
        await page.getByRole('button', { name: 'More about secret content' }).click();

        expect(violations).toEqual([]);
    });

    test('supports appearance selection and contextual help', async ({ page }) => {
        await page.goto('/');

        await expect(page.getByRole('group', { name: 'Choose appearance' })).toBeVisible();
        await page.getByRole('button', { name: 'Use Dark appearance' }).click();
        await expect(page.locator('html')).toHaveClass(/dark/);

        await page.getByRole('button', { name: 'More about secret content' }).click();
        await expect(page.getByText('Only share information you are authorized to send.')).toBeVisible();

        await page.getByRole('button', { name: 'Learn about expiration' }).click();
        await expect(page.getByText('permanently deleted after its first successful reveal')).toBeVisible();
    });

    test('creates and consumes an unprotected secret once', async ({ page, context }) => {
        await page.goto('/');
        await page.getByRole('textbox', { name: 'Secret content' }).fill('a local one-time message');
        await page.getByRole('button', { name: 'Create secure link' }).click();

        await expect(page.getByRole('heading', { name: 'Your secure link is ready' })).toBeVisible();
        const shareUrl = await page.locator('#share-link').inputValue();

        await page.goto(shareUrl);
        expect(new URL(page.url()).hash).toBe('');
        await expect(page.getByRole('alert', { name: /Secure link key missing/ })).toHaveCount(0);
        await page.reload();
        await expect(page.getByText('Secure link key missing')).toBeVisible();

        await page.goto(shareUrl);
        await page.getByRole('button', { name: 'Reveal secret' }).click();

        await expect(page.getByText('The server copy has been deleted.')).toBeVisible();
        await expect(page.locator('#revealed-secret')).toHaveValue('a local one-time message');
        await page.getByRole('button', { name: 'Clear plaintext' }).click();
        await expect(page.getByText('Plaintext cleared from this page.')).toBeVisible();

        const secondVisit = await context.request.get(shareUrl);
        expect(secondVisit.status()).toBe(404);
    });

    test('supports keyboard password protection on a mobile viewport', async ({ page }) => {
        await page.goto('/');
        await page.getByRole('textbox', { name: 'Secret content' }).fill('a protected local message');
        await page.getByRole('checkbox', { name: 'Password protect this secret' }).press('Space');

        await expect(page.locator('#secret-password')).toBeVisible();
        await expect(page.locator('#secret-password')).toHaveValue(/\S{12}/);

        await page.getByRole('button', { name: 'Create secure link' }).click();
        await expect(page.getByRole('heading', { name: 'Your secure link is ready' })).toBeVisible();
        await expect(page.getByText('Separate password')).toBeVisible();
    });

    test('confirms and completes secret revocation', async ({ page }) => {
        await page.goto('/');
        await page.getByRole('textbox', { name: 'Secret content' }).fill('a revocable local message');
        await page.getByRole('button', { name: 'Create secure link' }).click();
        await expect(page.getByRole('heading', { name: 'Your secure link is ready' })).toBeVisible();

        await page.getByRole('button', { name: 'Revoke secret' }).click();
        const dialog = page.getByRole('alertdialog');
        await expect(dialog).toContainText('The share link will stop working immediately.');
        await dialog.getByRole('button', { name: 'Cancel' }).click();
        await expect(dialog).toBeHidden();

        await page.getByRole('button', { name: 'Revoke secret' }).click();
        await page.getByRole('alertdialog').getByRole('button', { name: 'Revoke secret' }).click();
        await expect(page.getByText('The link can no longer be opened.')).toBeVisible();
    });
});
