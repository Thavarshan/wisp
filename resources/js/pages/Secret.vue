<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import InputError from '@/components/InputError.vue';
import SecretContentInput from '@/components/SecretContentInput.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useClipboard } from '@/composables/useClipboard';
import { useSecretTimer } from '@/composables/useSecretTimer';
import { Head } from '@inertiajs/vue3';
import { AlertCircle, Check, Copy, Eye, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    token: string;
    has_password: boolean;
    expired_at: string;
}>();

const password = ref('');
const content = ref('');
const error = ref('');
const revealed = ref(false);
const cleared = ref(false);
const isRevealing = ref(false);
const expired = ref(false);
const { copyToClipboard } = useClipboard();
const { countdown } = useSecretTimer(props.expired_at, () => {
    expired.value = true;
});

function csrfToken(): string {
    return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
}

async function revealSecret() {
    if (isRevealing.value || expired.value || revealed.value) return;

    isRevealing.value = true;
    error.value = '';

    try {
        const response = await fetch(route('secrets.reveal', { token: props.token }), {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({ password: props.has_password ? password.value : null }),
        });
        const payload = await response.json();

        if (response.status === 422) {
            error.value = payload.errors?.password?.[0] ?? 'The provided password is incorrect.';
            return;
        }

        if (response.status === 410) {
            expired.value = true;
            error.value = 'This secret has expired.';
            return;
        }

        if (!response.ok) throw new Error(payload.message ?? 'Unable to reveal this secret.');

        content.value = payload.content;
        revealed.value = true;
    } catch (caught) {
        error.value = caught instanceof Error ? caught.message : 'Unable to reveal this secret.';
    } finally {
        password.value = '';
        isRevealing.value = false;
    }
}

function clearSecret() {
    content.value = '';
    cleared.value = true;
}
</script>

<template>
    <Head title="Reveal secret" />
    <div class="flex min-h-screen w-full flex-col items-center justify-center p-4 md:p-8">
        <div class="w-full max-w-2xl">
            <Card glassBorder class="shadow-xl">
                <CardHeader>
                    <AppLogo href="/" classes="h-12 mx-auto" title="Wisp" />
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex items-center justify-center">
                        <Badge v-if="!expired">Expires in {{ countdown }}</Badge>
                        <Badge v-else variant="destructive">Secret has expired</Badge>
                    </div>

                    <Alert v-if="revealed && !cleared" variant="destructive">
                        <AlertCircle class="h-4 w-4" />
                        <AlertTitle>This secret has been consumed.</AlertTitle>
                        <AlertDescription>Keep a copy only if you are authorized to do so.</AlertDescription>
                    </Alert>
                    <Alert v-if="cleared">
                        <Check class="h-4 w-4" />
                        <AlertTitle>Plaintext cleared from this page.</AlertTitle>
                    </Alert>

                    <div v-if="!revealed" class="space-y-2">
                        <label v-if="has_password" for="password" class="text-sm font-medium">Password</label>
                        <Input
                            v-if="has_password"
                            id="password"
                            v-model="password"
                            type="password"
                            autocomplete="off"
                            placeholder="Password"
                            @keyup.enter="revealSecret"
                        />
                        <InputError :message="error" />
                    </div>
                    <InputError v-else :message="error" />
                    <SecretContentInput v-if="revealed && !cleared" v-model="content" />
                </CardContent>
                <CardFooter class="gap-2">
                    <Button
                        v-if="!revealed"
                        type="button"
                        class="w-full"
                        size="lg"
                        :disabled="isRevealing || expired || (has_password && !password)"
                        @click="revealSecret"
                    >
                        <Eye class="mr-1 size-4" /> {{ isRevealing ? 'Revealing…' : 'Reveal secret' }}
                    </Button>
                    <template v-else-if="!cleared">
                        <Button type="button" class="flex-1" size="lg" @click="copyToClipboard(content)"><Copy class="mr-1 size-4" /> Copy</Button>
                        <Button type="button" variant="outline" size="lg" @click="clearSecret"><Trash2 class="mr-1 size-4" /> Clear</Button>
                    </template>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
