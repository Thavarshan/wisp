<script setup lang="ts">
import { Button } from '@/components/ui/button';

const expirationOptions = {
    '5 mins': '5m',
    '30 mins': '30m',
    '1 hour': '1h',
    '6 hours': '6h',
    '12 hours': '12h',
    '1 day': '1d',
};

defineProps<{ modelValue: string; }>();
defineEmits(['update:modelValue']);
</script>

<template>
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-4">
        <Button
            v-for="(value, label) in expirationOptions"
            type="button"
            :key="value"
            @click="$emit('update:modelValue', value)"
            :class="{
                'bg-primary text-primary-foreground': modelValue === value,
                'bg-muted text-accent-foreground': modelValue !== value
            }"
            class="w-full transition-colors hover:text-white dark:hover:text-accent-foreground"
        >
            {{ label }}
        </Button>
    </div>
</template>
