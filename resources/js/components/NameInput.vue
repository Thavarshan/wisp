<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { computed } from 'vue';

const props = defineProps<{
    modelValue: string;
}>();

const emit = defineEmits(['update:modelValue']);

// Use a computed property for v-model
const nameValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>

<template>
    <Input
        v-model="nameValue"
        type="text"
        placeholder="Name (optional)"
    />
</template>
