<script setup lang="ts">
import { Popover, PopoverContent, PopoverPortal, PopoverTrigger } from '@/components/ui/popover';
import type { ExpirationOption } from '@/types/secret';
import { Check, Info } from 'lucide-vue-next';

defineProps<{
    modelValue: ExpirationOption['value'];
    options: ExpirationOption[];
    error?: string;
}>();

defineEmits<{ 'update:modelValue': [value: ExpirationOption['value']] }>();
</script>

<template>
    <fieldset class="space-y-3" :aria-invalid="Boolean(error)">
        <legend class="flex items-center gap-2 text-sm font-medium">
            Expires after
            <Popover>
                <PopoverTrigger as-child>
                    <button
                        type="button"
                        class="inline-flex size-7 items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-accent hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        aria-label="Learn about expiration"
                    >
                        <Info class="size-4" aria-hidden="true" />
                    </button>
                </PopoverTrigger>
                <PopoverPortal>
                    <PopoverContent
                        class="z-50 w-72 rounded-md border bg-popover p-4 text-sm leading-5 text-popover-foreground shadow-md outline-none"
                    >
                        The secret is permanently deleted after its first successful reveal or when this time expires.
                    </PopoverContent>
                </PopoverPortal>
            </Popover>
        </legend>
        <p class="text-xs text-muted-foreground">Choose how long the link should remain available.</p>

        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
            <label
                v-for="option in options"
                :key="option.value"
                class="relative cursor-pointer rounded-lg border p-3 transition-colors focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2"
                :class="
                    modelValue === option.value
                        ? 'border-primary bg-primary/10 text-foreground'
                        : 'border-border bg-background/60 text-muted-foreground hover:border-primary/50 hover:text-foreground'
                "
            >
                <input
                    class="sr-only"
                    type="radio"
                    name="expiration"
                    :value="option.value"
                    :checked="modelValue === option.value"
                    :aria-checked="modelValue === option.value"
                    @change="$emit('update:modelValue', option.value)"
                />
                <span class="flex items-center justify-between gap-2 text-sm font-medium">
                    {{ option.label }}
                    <Check v-if="modelValue === option.value" class="size-4 text-primary" aria-hidden="true" />
                </span>
                <span class="mt-1 block text-xs text-muted-foreground">{{ modelValue === option.value ? 'Selected' : 'Select' }}</span>
            </label>
        </div>

        <p v-if="error" class="text-sm text-destructive" role="alert">{{ error }}</p>
    </fieldset>
</template>
