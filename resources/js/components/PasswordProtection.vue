<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Copy } from 'lucide-vue-next';
import { computed, watch, nextTick } from 'vue';
import { useToast } from '@/components/ui/toast';
import copy from 'copy-to-clipboard';
import { generateSecurePassword } from '@/lib/utils';

const props = defineProps<{
    password?: string;       // Password value
    enabled: boolean;       // Toggle state for password protection
}>();

const emit = defineEmits(['update:password', 'update:enabled']);

const { toast } = useToast();

// Computed for password value
const passwordValue = computed({
    get: () => props.password,
    set: (value) => emit('update:password', value)
});

// Computed for toggle state
const isEnabled = computed({
    get: () => props.enabled,
    set: (value) => emit('update:enabled', value)
});

// Handle copy to clipboard
function handleCopy() {
    if (passwordValue.value) {
        copy(passwordValue.value);
        toast({
            title: 'Copied!',
            description: 'Password copied to clipboard.',
        });
    }
}

watch(isEnabled, async (value) => {
    if (value && !passwordValue.value) {
        await nextTick();  // Ensure the DOM and state are updated first
        const generatedPassword = generateSecurePassword();
        emit('update:password', generatedPassword);  // Emit the generated password
        toast({
            title: 'Generated!',
            description: 'A secure password has been generated.',
        });
    } else if (!value) {
        emit('update:password', '');  // Clear password if disabled
    }
});

</script>

<template>
    <div class="pt-2 flex flex-col md:flex-row items-start justify-between md:h-16">
        <!-- Password Protect Toggle -->
        <div class="flex items-center">
            <Label class="flex items-start gap-3 cursor-pointer">
                <Checkbox v-model:checked="isEnabled" />
                <div class="flex flex-col">
                    <span>Password protect</span>
                    <p class="mt-1 text-xs text-muted-foreground w-56">
                        For extra security, you have the option to set a password.
                    </p>
                </div>
            </Label>
        </div>

        <div v-if="isEnabled" class="mt-3 md:mt-0 w-full md:w-auto">
            <!-- Password Input and Copy Button (Shown only if enabled) -->
            <div class="relative flex items-center">
                <Input
                    v-model="passwordValue"
                    type="text"
                    placeholder="Enter password"
                    class="w-full md:w-64 pr-10"
                />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="handleCopy"
                    class="absolute right-0 mr-1">
                    <Copy class="size-4" />
                </Button>
            </div>

            <div>

            </div>
        </div>
    </div>
</template>
