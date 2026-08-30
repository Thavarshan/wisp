import { revokeSecret, SecretApiError } from '@/services/secretApi';
import { ref } from 'vue';

export type RevokeSecretStatus = 'idle' | 'revoking' | 'revoked' | 'error';

export function useRevokeSecret() {
    const status = ref<RevokeSecretStatus>('idle');
    const error = ref('');

    async function revoke(secretId: string, revocationToken: string): Promise<boolean> {
        if (status.value === 'revoking' || status.value === 'revoked') {
            return false;
        }

        status.value = 'revoking';
        error.value = '';

        try {
            await revokeSecret(secretId, revocationToken);
            status.value = 'revoked';

            return true;
        } catch (caught) {
            status.value = 'error';
            error.value = caught instanceof SecretApiError ? caught.message : 'Unable to revoke this secret. Please try again.';

            return false;
        }
    }

    return {
        status,
        error,
        revoke,
    };
}
