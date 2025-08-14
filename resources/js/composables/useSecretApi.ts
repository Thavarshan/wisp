import { ref, readonly } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { useToast } from '@/components/ui/toast';

export interface SecretFormData {
    secret: string;
    expiration: string;
    password?: string;
    single_view?: boolean;
}

export interface SecretResponse {
    uid: string;
    expires_at?: string;
    single_view: boolean;
    password_protected: boolean;
}

export function useSecretApi() {
    const { toast } = useToast();
    const isLoading = ref(false);
    const isSubmitting = ref(false);

    async function createSecret(data: SecretFormData): Promise<SecretResponse | null> {
        if (isSubmitting.value) return null;

        isSubmitting.value = true;
        isLoading.value = true;

        try {
            const response = await axios.post(route('secrets.store'), {
                secret: data.secret,
                expiration: data.expiration,
                password: data.password || null,
                single_view: data.single_view || false
            });

            toast({
                title: 'Secret created successfully',
                description: 'Your secret has been encrypted and is ready to share.',
            });

            return response.data;
        } catch (error: any) {
            const errorMessage = error?.response?.data?.message || 'Failed to create secret. Please try again.';

            toast({
                title: 'Unable to create secret',
                description: errorMessage,
                variant: 'destructive',
            });

            // If there are validation errors, let Inertia handle them
            if (error?.response?.status === 422) {
                throw error;
            }

            return null;
        } finally {
            isSubmitting.value = false;
            isLoading.value = false;
        }
    }

    async function viewSecret(uid: string, password?: string): Promise<any> {
        if (isLoading.value) return null;

        isLoading.value = true;

        try {
            const response = await axios.post(route('secrets.view', { secret: uid }), {
                password: password || null
            });

            return response.data;
        } catch (error: any) {
            const errorMessage = error?.response?.data?.message || 'Failed to retrieve secret.';

            toast({
                title: 'Unable to retrieve secret',
                description: errorMessage,
                variant: 'destructive',
            });

            throw error;
        } finally {
            isLoading.value = false;
        }
    }

    function navigateToSecret(uid: string) {
        router.visit(route('secrets.show', { secret: uid }));
    }

    function navigateToShare(uid: string) {
        router.visit(route('secrets.share', { secret: uid }));
    }

    function navigateHome() {
        router.visit(route('home'));
    }

    return {
        isLoading: readonly(isLoading),
        isSubmitting: readonly(isSubmitting),
        createSecret,
        viewSecret,
        navigateToSecret,
        navigateToShare,
        navigateHome
    };
}
