import SecretShareResult from '@/components/SecretShareResult.vue';
import { TooltipProvider } from '@/components/ui/tooltip';
import type { CreatedSecret } from '@/types/secret';
import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { defineComponent, nextTick } from 'vue';

const secret: CreatedSecret = {
    accessToken: 'access-token',
    shareUrl: 'https://wisp.test/secrets/access-token',
    revocationToken: 'revocation-token',
    expiresAt: '2026-08-30T12:00:00Z',
    expiration: { value: '1h', label: '1 hour' },
    password: 'password',
};

describe('SecretShareResult', () => {
    function mountResult() {
        const Harness = defineComponent({
            components: { SecretShareResult, TooltipProvider },
            setup() {
                return { secret };
            },
            template: `
                <TooltipProvider>
                    <SecretShareResult :secret="secret" />
                </TooltipProvider>
            `,
        });

        return mount(Harness);
    }

    beforeEach(() => {
        vi.stubGlobal(
            'route',
            vi.fn(() => '/secrets/access-token'),
        );
    });

    it('shows revoke failures and does not report success', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(new Response('', { status: 500 })));
        const wrapper = mountResult();

        const revokeButton = wrapper.findAll('button').find((button) => button.text().includes('Revoke secret'));
        await revokeButton?.trigger('click');
        await nextTick();
        const confirmButton = document.body.querySelector('[role="alertdialog"] button.bg-destructive') as HTMLButtonElement;
        confirmButton.click();
        await flushPromises();

        expect(wrapper.text()).toContain('Revocation failed');
        expect(wrapper.text()).not.toContain('Secret revoked');
    });

    it('reports success only after a successful response', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(new Response(null, { status: 204 })));
        const wrapper = mountResult();

        const revokeButton = wrapper.findAll('button').find((button) => button.text().includes('Revoke secret'));
        await revokeButton?.trigger('click');
        await nextTick();
        const confirmButton = document.body.querySelector('[role="alertdialog"] button.bg-destructive') as HTMLButtonElement;
        confirmButton.click();
        await flushPromises();

        expect(wrapper.text()).toContain('Secret revoked');
    });
});
