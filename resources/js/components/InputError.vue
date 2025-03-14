<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <div v-show="message" class="mt-1">
        <p class="text-sm text-red-600 dark:text-red-500">
            {{ message }}
        </p>
    </div>
</template>
