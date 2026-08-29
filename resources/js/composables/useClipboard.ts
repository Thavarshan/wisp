import { useToast } from '@/components/ui/toast';
import { readonly, ref } from 'vue';

export function useClipboard() {
    const { toast } = useToast();
    const isCopying = ref(false);

    async function copyToClipboard(
        text: string,
        options?: {
            successTitle?: string;
            successDescription?: string;
            errorTitle?: string;
            errorDescription?: string;
        },
    ) {
        if (isCopying.value) return false;

        isCopying.value = true;

        try {
            if (!navigator.clipboard) {
                throw new Error('Clipboard API unavailable');
            }

            await navigator.clipboard.writeText(text);
            const success = true;

            if (success) {
                toast({
                    title: options?.successTitle || 'Copied!',
                    description: options?.successDescription || 'Text copied to clipboard.',
                });
            } else {
                throw new Error('Failed to copy to clipboard');
            }

            return success;
        } catch {
            toast({
                title: options?.errorTitle || 'Copy failed',
                description: options?.errorDescription || 'Failed to copy to clipboard.',
                variant: 'destructive',
            });
            return false;
        } finally {
            isCopying.value = false;
        }
    }

    return {
        copyToClipboard,
        isCopying: readonly(isCopying),
    };
}
