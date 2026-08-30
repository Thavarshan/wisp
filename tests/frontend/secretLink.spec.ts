import { consumeAccessToken } from '@/lib/secretLink';
import { beforeEach, describe, expect, it } from 'vitest';

describe('secret link bootstrap', () => {
    beforeEach(() => {
        window.history.replaceState(null, '', '/secrets/'.concat('a'.repeat(64)));
    });

    it('takes a valid access token from the fragment and removes it from history', () => {
        const token = 'b'.repeat(64);
        window.location.hash = `#${token}`;

        expect(consumeAccessToken()).toBe(token);
        expect(window.location.hash).toBe('');
        expect(window.location.pathname).toBe(`/secrets/${'a'.repeat(64)}`);
    });

    it('removes invalid fragments without returning them', () => {
        window.location.hash = '#not-a-token';

        expect(consumeAccessToken()).toBeNull();
        expect(window.location.hash).toBe('');
    });
});
