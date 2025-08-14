<script setup lang="ts">
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { computed } from 'vue';

const props = defineProps<{
    modelValue: string;
    error?: string;
}>();

const emit = defineEmits(['update:modelValue']);

// Use a computed property for v-model
const nameValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>

<template>
    <div>
        <Input
            v-model="nameValue"
            type="text"
            placeholder="Name (optional)"
        />
        <InputError v-if="error" :message="error" />
    </div>
</template>
