import { revealSecret, SecretApiError } from '@/services/secretApi';
import type { RevealedSecret } from '@/types/secret';
import { ref } from 'vue';

export type RevealSecretStatus = 'ready' | 'revealing' | 'revealed' | 'cleared' | 'expired' | 'consumed' | 'error';

export function useRevealSecret() {
    const status = ref<RevealSecretStatus>('ready');
    const content = ref<string | null>(null);
    const error = ref('');

    async function reveal(token: string, password: string | null): Promise<RevealedSecret | null> {
        if (status.value === 'revealing' || status.value === 'revealed') {
            return null;
        }

        status.value = 'revealing';
        error.value = '';

        try {
            const result = await revealSecret(token, { password });

            content.value = result.content;
            status.value = 'revealed';

            return result;
        } catch (caught) {
            if (caught instanceof SecretApiError && caught.kind === 'expired') {
                status.value = 'expired';
                error.value = caught.message;
            } else if (caught instanceof SecretApiError && caught.kind === 'not_found') {
                status.value = 'consumed';
                error.value = caught.message;
            } else if (caught instanceof SecretApiError && caught.kind === 'validation') {
                status.value = 'error';
                error.value = caught.fieldErrors.password?.[0] ?? 'The password is incorrect.';
            } else if (caught instanceof SecretApiError) {
                status.value = 'error';
                error.value = caught.message;
            } else {
                status.value = 'error';
                error.value = 'Something went wrong. Please try again.';
            }

            return null;
        }
    }

    function markExpired() {
        if (status.value === 'ready' || status.value === 'error') {
            status.value = 'expired';
            error.value = 'This secret has expired.';
        }
    }

    function clear() {
        content.value = null;
        status.value = 'cleared';
        error.value = '';
    }

    return {
        status,
        content,
        error,
        reveal,
        markExpired,
        clear,
    };
}
