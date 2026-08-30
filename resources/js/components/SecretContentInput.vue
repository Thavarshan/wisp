<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Popover, PopoverContent, PopoverPortal, PopoverTrigger } from '@/components/ui/popover';
import { Textarea } from '@/components/ui/textarea';
import { MAX_SECRET_LENGTH } from '@/types/secret';
import { Info } from 'lucide-vue-next';

const model = defineModel<string>({ default: '' });

const props = withDefaults(
    defineProps<{
        id?: string;
        label?: string;
        helperText?: string;
        infoText?: string;
        error?: string;
        readonly?: boolean;
        required?: boolean;
    }>(),
    {
        id: 'secret-content',
        label: 'Secret content',
        helperText: 'Deleted after one successful reveal or when it expires.',
        infoText: 'Only share information you are authorized to send. Wisp never logs the secret content.',
        readonly: false,
        required: true,
    },
);
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <label :for="props.id" class="text-sm font-medium">{{ props.label }}</label>
                <Popover v-if="props.infoText">
                    <PopoverTrigger as-child>
                        <button
                            type="button"
                            class="inline-flex size-7 items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-accent hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            aria-label="More about secret content"
                        >
                            <Info class="size-4" aria-hidden="true" />
                        </button>
                    </PopoverTrigger>
                    <PopoverPortal>
                        <PopoverContent
                            class="z-50 w-72 rounded-md border bg-popover p-4 text-sm leading-5 text-popover-foreground shadow-md outline-none"
                        >
                            {{ props.infoText }}
                        </PopoverContent>
                    </PopoverPortal>
                </Popover>
            </div>
            <span class="text-xs tabular-nums text-muted-foreground">
                {{ model.length.toLocaleString() }} / {{ MAX_SECRET_LENGTH.toLocaleString() }}
            </span>
        </div>

        <Textarea
            :id="props.id"
            v-model="model"
            :readonly="props.readonly"
            :required="props.required"
            :maxlength="MAX_SECRET_LENGTH"
            :aria-invalid="Boolean(props.error)"
            :aria-describedby="`${props.id}-help ${props.id}-error`"
            placeholder="Write something confidential…"
            rows="9"
            class="min-h-52 resize-y"
        />

        <div class="flex flex-col gap-1">
            <p :id="`${props.id}-help`" class="text-xs text-muted-foreground">
                {{ props.helperText }}
            </p>
            <InputError :id="`${props.id}-error`" :message="props.error" />
        </div>
    </div>
</template>
