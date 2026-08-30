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
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(text);
            } else {
                const fallback = document.createElement('textarea');
                fallback.value = text;
                fallback.setAttribute('readonly', '');
                fallback.style.position = 'fixed';
                fallback.style.opacity = '0';
                document.body.appendChild(fallback);
                fallback.select();

                const copied = document.execCommand('copy');
                fallback.remove();

                if (!copied) {
                    throw new Error('Clipboard API unavailable');
                }
            }

            toast({
                title: options?.successTitle || 'Copied!',
                description: options?.successDescription || 'Text copied to clipboard.',
            });

            return true;
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
