import SecretShareResult from '@/components/SecretShareResult.vue';
import type { CreatedSecret } from '@/types/secret';
import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

const secret: CreatedSecret = {
    accessToken: 'access-token',
    shareUrl: 'https://wisp.test/secrets/access-token',
    revocationToken: 'revocation-token',
    expiresAt: '2026-08-30T12:00:00Z',
    expiration: { value: '1h', label: '1 hour' },
    password: 'password',
};

describe('SecretShareResult', () => {
    beforeEach(() => {
        vi.stubGlobal(
            'route',
            vi.fn(() => '/secrets/access-token'),
        );
    });

    it('shows revoke failures and does not report success', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(new Response('', { status: 500 })));
        const wrapper = mount(SecretShareResult, { props: { secret } });

        const revokeButton = wrapper.findAll('button').find((button) => button.text().includes('Revoke secret'));
        await revokeButton?.trigger('click');
        await wrapper.get('[role="dialog"] button.bg-destructive').trigger('click');
        await flushPromises();

        expect(wrapper.text()).toContain('Revocation failed');
        expect(wrapper.text()).not.toContain('Secret revoked');
    });

    it('reports success only after a successful response', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(new Response(null, { status: 204 })));
        const wrapper = mount(SecretShareResult, { props: { secret } });

        const revokeButton = wrapper.findAll('button').find((button) => button.text().includes('Revoke secret'));
        await revokeButton?.trigger('click');
        await wrapper.get('[role="dialog"] button.bg-destructive').trigger('click');
        await flushPromises();

        expect(wrapper.text()).toContain('Secret revoked');
    });
});
