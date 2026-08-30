<script setup lang="ts">
import type { ExpirationOption } from '@/types/secret';

defineProps<{
    modelValue: ExpirationOption['value'];
    options: ExpirationOption[];
    error?: string;
}>();

defineEmits<{ 'update:modelValue': [value: ExpirationOption['value']] }>();
</script>

<template>
    <fieldset class="space-y-3" :aria-invalid="Boolean(error)">
        <legend class="text-sm font-medium">Expiration</legend>
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
                <span class="block text-sm font-medium">{{ option.label }}</span>
                <span class="mt-1 block text-xs text-muted-foreground">
                    {{ modelValue === option.value ? 'Selected' : 'Select' }}
                </span>
            </label>
        </div>

        <p v-if="error" class="text-sm text-destructive" role="alert">{{ error }}</p>
    </fieldset>
</template>
