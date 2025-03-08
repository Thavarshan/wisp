<script setup lang="ts">
import { Button } from '@/components/ui/button';

const expirationOptions = [
    '5 mins',
    '30 mins',
    '1 hour',
    '6 hours',
    '12 hours',
    '1 day',
];

defineProps<{ modelValue: string; }>();
defineEmits(['update:modelValue']);
</script>

<template>
    <div class="flex items-center justify-between gap-4">
        <Button
            v-for="(option, index) in expirationOptions"
            type="button"
            :key="index"
            @click="$emit('update:modelValue', option)"
            :class="{
                'bg-primary text-primary-foreground': modelValue === option,
                'bg-muted text-accent-foreground': modelValue !== option
            }"
            class="w-full transition-colors hover:text-white dark:hover:text-accent-foreground"
        >
            {{ option }}
        </Button>
    </div>
</template>
