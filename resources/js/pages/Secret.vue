<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import SecretContentInput from '@/components/SecretContentInput.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useClipboard } from '@/composables/useClipboard';
import { useRevealSecret } from '@/composables/useRevealSecret';
import { useSecretTimer } from '@/composables/useSecretTimer';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { AlertCircle, Check, Copy, Eye, ShieldAlert, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    token: string;
    has_password: boolean;
    expired_at: string;
}>();

const password = ref('');
const confirmingReveal = ref(false);
const expiredByTimer = ref(false);
const { copyToClipboard } = useClipboard();
const { status, content, error, reveal, markExpired, clear } = useRevealSecret();
const { countdown } = useSecretTimer(props.expired_at, () => {
    expiredByTimer.value = true;
    markExpired();
});

const isUnavailable = computed(() => expiredByTimer.value || status.value === 'expired' || status.value === 'consumed');
const canSubmit = computed(() => !isUnavailable.value && status.value !== 'revealing' && (!props.has_password || Boolean(password.value)));

function requestReveal() {
    if (!canSubmit.value) {
        return;
    }

    confirmingReveal.value = true;
}

async function confirmReveal() {
    confirmingReveal.value = false;
    await reveal(props.token, props.has_password ? password.value : null);
    password.value = '';
}

function clearSecret() {
    clear();
}
</script>

<template>
    <Head title="Reveal secret" />

    <section class="w-full max-w-2xl">
        <Card class="border-border/80 bg-card/95 shadow-xl shadow-primary/5 backdrop-blur">
            <CardHeader class="space-y-3 border-b border-border/60">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.2em] text-primary">Private message</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Ready when you are</h1>
                    </div>
                    <Badge v-if="!isUnavailable" variant="secondary" class="shrink-0">{{ countdown }}</Badge>
                    <Badge v-else variant="destructive" class="shrink-0">
                        {{ status === 'consumed' ? 'Consumed' : 'Unavailable' }}
                    </Badge>
                </div>
                <p class="max-w-xl text-sm leading-6 text-muted-foreground">
                    Revealing this message permanently deletes it. Only continue if you are the intended recipient.
                </p>
            </CardHeader>

            <CardContent class="space-y-5 pt-6">
                <Alert v-if="status === 'ready' && !isUnavailable" class="border-primary/20 bg-primary/5">
                    <ShieldAlert class="size-4 text-primary" aria-hidden="true" />
                    <AlertTitle>One-time access</AlertTitle>
                    <AlertDescription>
                        The message is decrypted only for this request and cannot be retrieved again after a successful reveal.
                    </AlertDescription>
                </Alert>

                <Alert v-if="status === 'revealed'" variant="destructive">
                    <AlertCircle class="size-4" aria-hidden="true" />
                    <AlertTitle>This secret has been consumed.</AlertTitle>
                    <AlertDescription>Keep a copy only if you are authorized to do so.</AlertDescription>
                </Alert>

                <Alert v-if="status === 'cleared'">
                    <Check class="size-4" aria-hidden="true" />
                    <AlertTitle>Plaintext cleared from this page.</AlertTitle>
                    <AlertDescription>This tab no longer holds the revealed message.</AlertDescription>
                </Alert>

                <Alert v-if="status === 'consumed' || status === 'expired'" variant="destructive">
                    <AlertCircle class="size-4" aria-hidden="true" />
                    <AlertTitle>{{ status === 'consumed' ? 'Secret already consumed' : 'Secret expired' }}</AlertTitle>
                    <AlertDescription>{{ error }}</AlertDescription>
                </Alert>

                <div v-if="status !== 'revealed' && status !== 'cleared' && status !== 'consumed'" class="space-y-4">
                    <div v-if="has_password" class="space-y-2">
                        <label for="password" class="text-sm font-medium">Password required</label>
                        <Input
                            id="password"
                            v-model="password"
                            type="password"
                            autocomplete="off"
                            :disabled="isUnavailable || status === 'revealing'"
                            :aria-invalid="Boolean(error)"
                            aria-describedby="password-help password-error"
                            placeholder="Enter the password shared with you"
                            @keyup.enter="requestReveal"
                        />
                        <p id="password-help" class="text-xs text-muted-foreground">The password is never stored by Wisp.</p>
                    </div>
                    <InputError id="password-error" :message="error" />
                </div>

                <div v-if="status === 'revealed' && content" class="space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <label for="revealed-secret" class="text-sm font-medium">Revealed content</label>
                        <span class="text-xs text-muted-foreground">Clear it when finished</span>
                    </div>
                    <SecretContentInput id="revealed-secret" v-model="content" :readonly="true" :required="false" />
                </div>

                <Alert v-if="status === 'error'" variant="destructive">
                    <AlertCircle class="size-4" aria-hidden="true" />
                    <AlertTitle>Reveal failed</AlertTitle>
                    <AlertDescription>{{ error }}</AlertDescription>
                </Alert>
            </CardContent>

            <CardFooter class="flex-col gap-3 border-t border-border/60 pt-6 sm:flex-row sm:justify-end">
                <Button v-if="status === 'revealed'" type="button" variant="outline" @click="copyToClipboard(content ?? '')">
                    <Copy class="mr-2 size-4" aria-hidden="true" /> Copy content
                </Button>
                <Button v-if="status === 'revealed'" type="button" variant="destructive" @click="clearSecret">
                    <Trash2 class="mr-2 size-4" aria-hidden="true" /> Clear plaintext
                </Button>
                <Button
                    v-if="status !== 'revealed' && status !== 'cleared' && status !== 'consumed'"
                    type="button"
                    size="lg"
                    class="w-full sm:w-auto"
                    :disabled="!canSubmit"
                    @click="requestReveal"
                >
                    <Eye class="mr-2 size-4" aria-hidden="true" />
                    {{ status === 'revealing' ? 'Revealing…' : 'Reveal and consume' }}
                </Button>
            </CardFooter>
        </Card>
    </section>

    <div
        v-if="confirmingReveal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-5"
        role="presentation"
        @click.self="confirmingReveal = false"
    >
        <div
            class="w-full max-w-md rounded-xl border border-border bg-card p-6 text-card-foreground shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="reveal-title"
            aria-describedby="reveal-description"
        >
            <div class="flex items-start gap-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                    <AlertCircle class="size-5" aria-hidden="true" />
                </div>
                <div class="space-y-2">
                    <h2 id="reveal-title" class="font-semibold">Reveal and consume this secret?</h2>
                    <p id="reveal-description" class="text-sm leading-6 text-muted-foreground">
                        This action permanently deletes the stored message. It cannot be undone or repeated.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <Button type="button" variant="outline" @click="confirmingReveal = false">Go back</Button>
                <Button type="button" variant="destructive" @click="confirmReveal">Reveal now</Button>
            </div>
        </div>
    </div>
</template>
