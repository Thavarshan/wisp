<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Textarea } from '@/components/ui/textarea';
import { MAX_SECRET_LENGTH } from '@/types/secret';

const model = defineModel<string>({ default: '' });

withDefaults(
    defineProps<{
        id?: string;
        error?: string;
        readonly?: boolean;
        required?: boolean;
    }>(),
    {
        id: 'secret-content',
        readonly: false,
        required: true,
    },
);
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between gap-4">
            <label for="secret-content" class="text-sm font-medium">Secret content</label>
            <span class="text-xs tabular-nums text-muted-foreground">
                {{ model.length.toLocaleString() }} / {{ MAX_SECRET_LENGTH.toLocaleString() }}
            </span>
        </div>

        <Textarea
            :id="id"
            v-model="model"
            :readonly="readonly"
            :required="required"
            :maxlength="MAX_SECRET_LENGTH"
            :aria-invalid="Boolean(error)"
            :aria-describedby="`${id}-help ${id}-error`"
            placeholder="Write something confidential…"
            rows="9"
            class="min-h-52 resize-y"
        />

        <div class="flex flex-col gap-1">
            <p :id="`${id}-help`" class="text-xs text-muted-foreground">
                Deleted after one successful reveal or when it expires. Never include information you are not authorized to share.
            </p>
            <InputError :id="`${id}-error`" :message="error" />
        </div>
    </div>
</template>
