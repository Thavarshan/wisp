<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/components/ui/toast';
import { generateSecurePassword } from '@/lib/utils';
import { Copy } from 'lucide-vue-next';
import { nextTick, watch } from 'vue';

const password = defineModel<string>('password', { default: '' });
const enabled = defineModel<boolean>('enabled', { default: false });

defineProps<{
    error?: string;
}>();

const { toast } = useToast();

// Handle copy to clipboard
async function handleCopy() {
    if (password.value && navigator.clipboard) {
        await navigator.clipboard.writeText(password.value);
        toast({
            title: 'Copied!',
            description: 'Password copied to clipboard.',
        });
    }
}

watch(enabled, async (value) => {
    if (value && !password.value) {
        await nextTick();
        try {
            const generatedPassword = generateSecurePassword();
            password.value = generatedPassword;
            toast({
                title: 'Generated!',
                description: 'A secure password has been generated.',
            });
        } catch {
            toast({
                title: 'Error',
                description: 'Failed to generate secure password. Please enter manually.',
                variant: 'destructive',
            });
        }
    } else if (!value) {
        password.value = '';
    }
});
</script>

<template>
    <div class="flex flex-col items-start justify-between pt-2 md:h-16 md:flex-row">
        <!-- Password Protect Toggle -->
        <div class="flex items-center">
            <Label class="flex cursor-pointer items-start gap-3">
                <Checkbox v-model:checked="enabled" />
                <div class="flex flex-col">
                    <span>Password protect</span>
                    <p class="mt-1 w-56 text-xs text-muted-foreground">For extra security, you have the option to set a password.</p>
                </div>
            </Label>
        </div>

        <div v-if="enabled" class="mt-3 w-full md:mt-0 md:w-auto">
            <!-- Password Input and Copy Button (Shown only if enabled) -->
            <div class="relative flex items-center">
                <Input v-model="password" type="text" placeholder="Enter password" class="w-full pr-10 md:w-64" />
                <Button type="button" variant="ghost" size="icon" @click="handleCopy" class="absolute right-0 mr-1">
                    <Copy class="size-4" />
                </Button>
            </div>
            <InputError v-if="error" :message="error" />
        </div>
    </div>
</template>
