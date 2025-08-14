import { ref, readonly } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { useToast } from '@/components/ui/toast';

export function useSecretActions() {
    const { toast } = useToast();
    const isLoading = ref(false);
    const isDeleted = ref(false);

    async function deleteSecret(uid: string): Promise<boolean> {
        if (isLoading.value) return false;

        isLoading.value = true;

        try {
            await axios.delete(route('secrets.destroy', { secret: uid }));
            isDeleted.value = true;

            toast({
                title: 'Secret obliterated',
                description: 'The secret has been permanently deleted.',
            });

            return true;
        } catch (error: any) {
            toast({
                title: 'Unable to delete secret',
                description: error?.response?.data?.message || 'An error occurred while deleting the secret.',
                variant: 'destructive',
            });

            return false;
        } finally {
            isLoading.value = false;
        }
    }

    function redirectToHome() {
        router.visit(route('home'));
    }

    function reloadPage() {
        window.location.reload();
    }

    return {
        isLoading: readonly(isLoading),
        isDeleted: readonly(isDeleted),
        deleteSecret,
        redirectToHome,
        reloadPage
    };
}
