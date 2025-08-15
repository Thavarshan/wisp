<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Toaster } from '@/components/ui/toast';
import { getUidFromRoute } from '@/lib/utils';
import AppLogo from '@/components/AppLogo.vue';
import { Card, CardHeader, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { AlertCircle, Copy, Eye } from 'lucide-vue-next';
import SecretContentInput from '@/components/SecretContentInput.vue';
import InputError from '@/components/InputError.vue';

// Composables
import { useSecretTimer } from '@/composables/useSecretTimer';
import { useClipboard } from '@/composables/useClipboard';
import { useSecretActions } from '@/composables/useSecretActions';

const props = defineProps<{
    secret: string;
    has_password: boolean;
    expired_at: string;
}>();

const revealed = ref(false);
const form = useForm<{ password: string; }>({ password: '' });
const uid = getUidFromRoute();

// Initialize composables
const { countdown } = useSecretTimer(props.expired_at, () => {
    window.location.reload();
});

const { copyToClipboard } = useClipboard();
const { deleteSecret } = useSecretActions();

const content = computed({
    get: () => (revealed.value ? props.secret : ''),
    set: () => '' // Read-only
});

// Function to copy secret to clipboard
function handleCopy() {
    copyToClipboard(props.secret);
}

// Function to reveal the secret
function handleRevealSecret() {
    if (props.has_password) {
        form.post(route('secrets.password', { secret: uid }), {
            preserveScroll: true,
            onSuccess: () => {
                obliterateSecret(() => revealed.value = true);
            }
        });
        return;
    }

    obliterateSecret(() => revealed.value = true);
}

// Function to delete the secret after revealing
async function obliterateSecret(callback?: () => void): Promise<void> {
    if (!uid) return;

    const success = await deleteSecret(uid);

    if (success && callback) {
        callback();
    } else if (!success) {
        // If deletion failed, still allow revealing but show error
        if (callback) callback();
    }
}
</script>

<template>
    <Toaster />
    <Head title="One Time Secrets" />
    <div class="flex min-h-screen flex-col items-center lg:justify-center p-4 md:p-8 w-full">
        <div class="w-full max-w-2xl">
            <Card glassBorder class="shadow-xl">
                <CardHeader>
                    <AppLogo href="/" classes="h-12 mx-auto" title="Wisp" />
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex items-center justify-center">
                        <Badge v-if="countdown !== 'Expired'">
                            Expires in {{ countdown }}
                        </Badge>
                        <Badge v-else variant="destructive">
                            Secret has expired
                        </Badge>
                    </div>
                    <Alert v-if="revealed" variant="destructive">
                        <AlertCircle class="w-4 h-4" />
                        <AlertTitle>This secret has been obliterated.</AlertTitle>
                        <AlertDescription>
                            Please be sure to store it in a safe place before closing this page.
                        </AlertDescription>
                    </Alert>
                    <div v-if="!revealed && props.has_password">
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            type="password"
                            required
                            :tabindex="2"
                            v-model="form.password"
                            placeholder="Password"
                        />
                        <InputError :message="form.errors.password" />
                    </div>
                    <SecretContentInput v-show="revealed" v-model="content" />
                </CardContent>
                <CardFooter>
                    <Button
                        v-if="!revealed"
                        type="button"
                        @click="handleRevealSecret"
                        class="w-full"
                        size="lg"
                        :disabled="props.has_password && !form.password"
                    >
                        <Eye class="size-4 mr-1" /> Reveal secret
                    </Button>
                    <Button
                        v-else
                        type="button"
                        @click="handleCopy"
                        class="w-full"
                        size="lg"
                    >
                        <Copy class="size-4 mr-1" /> Copy secret
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
