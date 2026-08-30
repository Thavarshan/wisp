<script setup lang="ts">
import CopyButton from '@/components/CopyButton.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/components/ui/toast';
import { Tooltip, TooltipContent, TooltipPortal, TooltipTrigger } from '@/components/ui/tooltip';
import { generateSecurePassword } from '@/lib/utils';
import { Eye, EyeOff, KeyRound, RefreshCw } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const password = defineModel<string>('password', { default: '' });
const enabled = defineModel<boolean>('enabled', { default: false });

defineProps<{
    error?: string;
}>();

const showPassword = ref(false);
const { toast } = useToast();

function handleEnabledChange(isEnabled: boolean | 'indeterminate') {
    if (!isEnabled) {
        password.value = '';
        showPassword.value = false;

        return;
    }

    if (isEnabled === true && !password.value) {
        try {
            password.value = generateSecurePassword();
            toast({
                title: 'Password generated',
                description: 'A secure password is ready to share separately.',
            });
        } catch {
            toast({
                title: 'Password generation failed',
                description: 'Enter a password manually to continue.',
                variant: 'destructive',
            });
        }
    }
}

watch(enabled, handleEnabledChange);

function regeneratePassword() {
    try {
        password.value = generateSecurePassword();
        toast({
            title: 'Password regenerated',
            description: 'Share the new password through a separate channel.',
        });
    } catch {
        toast({
            title: 'Password generation failed',
            description: 'Enter a password manually to continue.',
            variant: 'destructive',
        });
    }
}
</script>

<template>
    <div class="space-y-3 rounded-lg border border-border/80 bg-muted/30 p-4">
        <div class="flex items-start gap-3">
            <Checkbox id="password-protect" v-model="enabled" class="mt-0.5" @update:model-value="handleEnabledChange" />
            <div class="space-y-1">
                <Label for="password-protect" class="cursor-pointer text-sm font-medium"> Password protect this secret </Label>
                <p class="text-xs leading-relaxed text-muted-foreground">Add a second factor and send the password through a separate channel.</p>
            </div>
        </div>

        <Transition name="expand">
            <div v-if="enabled" class="space-y-2">
                <Label for="secret-password">Secret password</Label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <KeyRound
                            class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <Input
                            id="secret-password"
                            v-model="password"
                            :type="showPassword ? 'text' : 'password'"
                            :aria-invalid="Boolean(error)"
                            aria-describedby="secret-password-help secret-password-error"
                            autocomplete="new-password"
                            class="pl-9 pr-10"
                            placeholder="Enter or use the generated password"
                            required
                        />
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <button
                                    type="button"
                                    class="absolute right-1 top-1/2 flex size-8 -translate-y-1/2 items-center justify-center rounded text-muted-foreground hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                    aria-controls="secret-password"
                                    @click="showPassword = !showPassword"
                                >
                                    <EyeOff v-if="showPassword" class="size-4" aria-hidden="true" />
                                    <Eye v-else class="size-4" aria-hidden="true" />
                                </button>
                            </TooltipTrigger>
                            <TooltipPortal>
                                <TooltipContent class="z-50 rounded-md bg-foreground px-3 py-1.5 text-xs text-background shadow-md">
                                    {{ showPassword ? 'Hide password' : 'Show password' }}
                                </TooltipContent>
                            </TooltipPortal>
                        </Tooltip>
                    </div>
                    <CopyButton :text="password" label="Copy password" />
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Button type="button" variant="outline" size="icon" aria-label="Regenerate password" @click="regeneratePassword">
                                <RefreshCw class="size-4" aria-hidden="true" />
                            </Button>
                        </TooltipTrigger>
                        <TooltipPortal>
                            <TooltipContent class="z-50 rounded-md bg-foreground px-3 py-1.5 text-xs text-background shadow-md">
                                Regenerate password
                            </TooltipContent>
                        </TooltipPortal>
                    </Tooltip>
                </div>
                <p id="secret-password-help" class="text-xs text-muted-foreground">
                    Wisp never stores the password in plain text or returns it after creation.
                </p>
                <InputError id="secret-password-error" :message="error" />
            </div>
        </Transition>
    </div>
</template>
