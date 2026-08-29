<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Textarea } from '@/components/ui/textarea';
const model = defineModel<string>({ default: '' });
defineProps<{
    error?: string;
}>();
</script>

<template>
    <div>
        <Textarea v-model="model" placeholder="Secret content" rows="12" required />

        <div class="mt-2">
            <InputError v-if="error" :message="error" />
            <p class="text-xs text-muted-foreground">
                The secret's content is permanently erased after a single view or when it reaches its expiration time.
            </p>
        </div>
    </div>
</template>
