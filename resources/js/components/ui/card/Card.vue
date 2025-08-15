<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
    glassBorder?: boolean;
}>();
</script>

<template>
    <div v-if="props.glassBorder" class="p-2 rounded-xl bg-white/10 dark:bg-white/5 backdrop-blur-lg border border-white/20 dark:border-white/10 shadow-xl">
        <div :class="cn('rounded-lg bg-card text-card-foreground shadow-sm border-0', props.class)">
            <slot />
        </div>
    </div>
    <div v-else :class="cn('rounded-lg border bg-card text-card-foreground shadow-sm', props.class)">
        <slot />
    </div>
</template>
