<script setup lang="ts">
import CopyButton from '@/components/CopyButton.vue';
import InputError from '@/components/InputError.vue';
import SecretContentInput from '@/components/SecretContentInput.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useRevealSecret } from '@/composables/useRevealSecret';
import { useSecretTimer } from '@/composables/useSecretTimer';
import AppLayout from '@/layouts/AppLayout.vue';
import { consumeAccessToken } from '@/lib/secretLink';
import { MAX_PASSWORD_BYTES } from '@/types/secret';
import { Head, Link } from '@inertiajs/vue3';
import { AlertCircle, Check, Eye, LoaderCircle, ShieldAlert, Trash2 } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    secret_id: string;
    has_password: boolean;
    expired_at: string;
}>();

const password = ref('');
const accessToken = ref(window.__wispAccessToken ?? null);
window.__wispAccessToken = null;
const expiredByTimer = ref(false);
const { status, content, error, reveal, markExpired, clear } = useRevealSecret();
const { countdown } = useSecretTimer(props.expired_at, () => {
    expiredByTimer.value = true;
    markExpired();
});

const isUnavailable = computed(() => expiredByTimer.value || status.value === 'expired' || status.value === 'consumed');
const canSubmit = computed(
    () =>
        Boolean(accessToken.value) &&
        !isUnavailable.value &&
        (status.value === 'ready' || status.value === 'error') &&
        (!props.has_password || Boolean(password.value)),
);

const heading = computed(() => {
    if (status.value === 'revealed') return 'Secret revealed';
    if (status.value === 'cleared') return 'Plaintext cleared';
    if (status.value === 'consumed') return 'Secret already consumed';
    if (status.value === 'expired') return 'Secret expired';

    return 'Ready to reveal';
});

async function handleReveal() {
    if (!canSubmit.value || !accessToken.value) return;

    await reveal(props.secret_id, accessToken.value, props.has_password ? password.value : null);
    password.value = '';

    if (status.value === 'revealed' || status.value === 'expired' || status.value === 'consumed') {
        accessToken.value = null;
    }
}

onBeforeUnmount(() => {
    window.removeEventListener('hashchange', handleHashChange);
    accessToken.value = null;
    password.value = '';
});

function handleHashChange() {
    const token = consumeAccessToken();

    if (token && !isUnavailable.value && status.value !== 'revealed' && status.value !== 'cleared') {
        accessToken.value = token;
    }
}

onMounted(() => {
    window.addEventListener('hashchange', handleHashChange);
});
</script>

<template>
    <Head title="Reveal secret" />

    <section class="mx-auto w-full max-w-2xl">
        <div class="sr-only" aria-live="polite">{{ heading }}</div>
        <Card class="border-border/80 bg-card/95 shadow-xl shadow-primary/5 backdrop-blur">
            <CardHeader class="space-y-3 border-b border-border/60">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.2em] text-primary">One-time secret</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">{{ heading }}</h1>
                    </div>
                    <Badge v-if="status === 'ready' || status === 'error' || status === 'revealing'" variant="secondary" class="shrink-0">
                        {{ countdown }}
                    </Badge>
                    <Badge v-else-if="status === 'revealed'" variant="outline" class="shrink-0">Revealed</Badge>
                    <Badge v-else variant="destructive" class="shrink-0">Unavailable</Badge>
                </div>
                <p class="max-w-xl text-sm leading-6 text-muted-foreground">
                    A successful reveal permanently deletes the stored message. Only continue if you are the intended recipient.
                </p>
            </CardHeader>

            <CardContent class="space-y-5 pt-6">
                <Alert v-if="status === 'ready' && accessToken" class="border-primary/20 bg-primary/5">
                    <ShieldAlert class="size-4 text-primary" aria-hidden="true" />
                    <AlertTitle>One-time access</AlertTitle>
                    <AlertDescription>
                        The message is decrypted only for this request and cannot be retrieved again after a successful reveal.
                    </AlertDescription>
                </Alert>

                <Alert v-if="status === 'ready' && !accessToken" variant="destructive">
                    <AlertCircle class="size-4" aria-hidden="true" />
                    <AlertTitle>Secure link key missing</AlertTitle>
                    <AlertDescription>Open the original share link again. The key is never retained after this page is refreshed.</AlertDescription>
                </Alert>

                <Alert v-if="status === 'revealed'" class="border-emerald-500/30 bg-emerald-500/5 text-foreground">
                    <Check class="size-4 text-emerald-600 dark:text-emerald-400" aria-hidden="true" />
                    <AlertTitle>Secret revealed</AlertTitle>
                    <AlertDescription>The server copy has been deleted. Clear this screen when you are finished.</AlertDescription>
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

                <div v-if="status === 'ready' || status === 'error' || status === 'revealing'" class="space-y-4">
                    <div v-if="has_password" class="space-y-2">
                        <label for="password" class="text-sm font-medium">Password required</label>
                        <Input
                            id="password"
                            v-model="password"
                            type="password"
                            autocomplete="off"
                            :disabled="isUnavailable || status === 'revealing' || !accessToken"
                            :aria-invalid="Boolean(error)"
                            aria-describedby="password-help password-error"
                            placeholder="Enter the password shared with you"
                            :maxlength="MAX_PASSWORD_BYTES"
                            @keyup.enter="handleReveal"
                        />
                        <p id="password-help" class="text-xs text-muted-foreground">The password is never stored by Wisp.</p>
                    </div>
                    <InputError id="password-error" :message="status === 'error' ? error : ''" />
                </div>

                <SecretContentInput
                    v-if="status === 'revealed' && content"
                    id="revealed-secret"
                    v-model="content"
                    label="Revealed content"
                    helper-text="Plaintext is only held in this page until you clear it."
                    :info-text="''"
                    :readonly="true"
                    :required="false"
                />

                <Alert v-if="status === 'error'" variant="destructive">
                    <AlertCircle class="size-4" aria-hidden="true" />
                    <AlertTitle>Reveal failed</AlertTitle>
                    <AlertDescription>{{ error }}</AlertDescription>
                </Alert>
            </CardContent>

            <CardFooter class="flex-col gap-3 border-t border-border/60 pt-6 sm:flex-row sm:justify-end">
                <CopyButton v-if="status === 'revealed' && content" :text="content" label="Copy revealed content" />
                <Button v-if="status === 'revealed'" type="button" variant="destructive" @click="clear">
                    <Trash2 class="size-4" aria-hidden="true" /> Clear plaintext
                </Button>
                <Link
                    v-if="status === 'cleared'"
                    href="/"
                    as="button"
                    class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-8 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                >
                    Return to Wisp
                </Link>
                <Button
                    v-if="status === 'ready' || status === 'error' || status === 'revealing'"
                    type="button"
                    size="lg"
                    class="w-full sm:w-auto"
                    :disabled="!canSubmit"
                    @click="handleReveal"
                >
                    <LoaderCircle v-if="status === 'revealing'" class="size-4 animate-spin" aria-hidden="true" />
                    <Eye v-else class="size-4" aria-hidden="true" />
                    {{ status === 'revealing' ? 'Revealing…' : 'Reveal secret' }}
                </Button>
            </CardFooter>
        </Card>
    </section>
</template>
