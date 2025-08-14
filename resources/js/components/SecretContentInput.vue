<script setup lang="ts">
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { computed } from 'vue';

const props = defineProps<{
    modelValue: string;
    error?: string;
}>();

const emit = defineEmits(['update:modelValue']);

// Use a computed property for v-model
const secretContent = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>

<template>
    <div>
        <Textarea
            v-model="secretContent"
            placeholder="Secret content"
            rows="12"
            required
        />

        <div class="mt-2">
            <InputError v-if="error" :message="error" />
            <p class="text-xs text-muted-foreground">The secret's content is permanently erased after a single view or when it reaches its expiration time.</p>
        </div>
    </div>
</template>
