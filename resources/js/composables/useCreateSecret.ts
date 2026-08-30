import { createSecret, SecretApiError } from '@/services/secretApi';
import type { CreatedSecret, CreateSecretPayload, ValidationErrors } from '@/types/secret';
import { ref } from 'vue';

export type CreateSecretStatus = 'idle' | 'submitting' | 'success' | 'error';

export function useCreateSecret() {
    const status = ref<CreateSecretStatus>('idle');
    const error = ref('');
    const fieldErrors = ref<ValidationErrors>({});

    async function submit(payload: CreateSecretPayload): Promise<CreatedSecret | null> {
        if (status.value === 'submitting') {
            return null;
        }

        status.value = 'submitting';
        error.value = '';
        fieldErrors.value = {};

        try {
            const result = await createSecret(payload);

            status.value = 'success';

            return result;
        } catch (caught) {
            status.value = 'error';

            if (caught instanceof SecretApiError) {
                error.value = caught.message;
                fieldErrors.value = caught.fieldErrors;
            } else {
                error.value = 'Something went wrong. Please try again.';
            }

            return null;
        }
    }

    function clearErrors() {
        error.value = '';
        fieldErrors.value = {};
        if (status.value === 'error') {
            status.value = 'idle';
        }
    }

    return {
        status,
        error,
        fieldErrors,
        submit,
        clearErrors,
    };
}
