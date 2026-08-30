<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipPortal, TooltipTrigger } from '@/components/ui/tooltip';
import { useClipboard } from '@/composables/useClipboard';
import { Copy } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        text: string;
        label?: string;
        disabled?: boolean;
    }>(),
    {
        label: 'Copy',
        disabled: false,
    },
);

const { copyToClipboard } = useClipboard();

function copy() {
    return copyToClipboard(props.text, {
        successTitle: `${props.label} copied`,
        successDescription: 'The value is ready to paste.',
        errorTitle: 'Copy failed',
        errorDescription: 'Select the value manually and copy it instead.',
    });
}
</script>

<template>
    <Tooltip>
        <TooltipTrigger as-child>
            <Button type="button" variant="outline" size="icon" :aria-label="label" :disabled="disabled || !text" @click="copy">
                <Copy class="size-4" aria-hidden="true" />
            </Button>
        </TooltipTrigger>
        <TooltipPortal>
            <TooltipContent class="z-50 rounded-md bg-foreground px-3 py-1.5 text-xs text-background shadow-md">
                {{ label }}
            </TooltipContent>
        </TooltipPortal>
    </Tooltip>
</template>
