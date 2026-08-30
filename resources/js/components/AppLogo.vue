<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        href?: string;
        title?: string;
        classes?: string;
    }>(),
    {
        href: '/',
        title: 'Wisp',
        classes: 'h-8 w-auto',
    },
);
</script>

<template>
    <Link
        :href="href"
        class="inline-flex rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
        :aria-label="`${title} home`"
    >
        <img :src="'/images/logo.svg'" :alt="title" :class="['dark:invert', classes]" />
    </Link>
</template>
