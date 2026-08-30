import { createSecret, SecretApiError } from '@/services/secretApi';
import { beforeEach, describe, expect, it, vi } from 'vitest';

describe('secretApi', () => {
    beforeEach(() => {
        document.head.innerHTML = '<meta name="csrf-token" content="test-token">';
        vi.restoreAllMocks();
    });

    it('maps the create response at the API boundary', async () => {
        vi.stubGlobal(
            'route',
            vi.fn((name: string) => (name === 'secrets.store' ? '/secrets' : '')),
        );
        vi.stubGlobal(
            'fetch',
            vi.fn().mockResolvedValue(
                new Response(
                    JSON.stringify({
                        access_token: 'access-token',
                        share_url: 'https://wisp.test/secrets/access-token',
                        revocation_token: 'revocation-token',
                        expires_at: '2026-08-30T12:00:00Z',
                        expiration: { value: '1h', label: '1 hour' },
                    }),
                    { status: 201, headers: { 'Content-Type': 'application/json' } },
                ),
            ),
        );

        await expect(createSecret({ content: 'private', expiration: '1h', password: 'passphrase' })).resolves.toMatchObject({
            accessToken: 'access-token',
            revocationToken: 'revocation-token',
            shareUrl: 'https://wisp.test/secrets/access-token',
            password: 'passphrase',
        });
    });

    it('normalizes validation failures without exposing server internals', async () => {
        vi.stubGlobal(
            'route',
            vi.fn(() => '/secrets'),
        );
        vi.stubGlobal(
            'fetch',
            vi.fn().mockResolvedValue(
                new Response(JSON.stringify({ errors: { content: ['Content is required.'] } }), {
                    status: 422,
                    headers: { 'Content-Type': 'application/json' },
                }),
            ),
        );

        await expect(createSecret({ content: '', expiration: '1h', password: null })).rejects.toEqual(
            expect.objectContaining<Partial<SecretApiError>>({
                status: 422,
                kind: 'validation',
                fieldErrors: { content: ['Content is required.'] },
            }),
        );
    });
});
