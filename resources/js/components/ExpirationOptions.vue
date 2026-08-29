<script setup lang="ts">
import { Button } from '@/components/ui/button';

defineProps<{ modelValue: string; options: Array<{ value: string; label: string }> }>();
defineEmits<{ 'update:modelValue': [value: string] }>();
</script>

<template>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 xl:grid-cols-6">
        <Button
            v-for="option in options"
            type="button"
            :key="option.value"
            @click="$emit('update:modelValue', option.value)"
            :class="{
                'bg-primary text-primary-foreground': modelValue === option.value,
                'bg-muted text-accent-foreground': modelValue !== option.value,
            }"
            class="w-full transition-colors hover:text-white dark:hover:text-accent-foreground"
        >
            {{ option.label }}
        </Button>
    </div>
</template>
