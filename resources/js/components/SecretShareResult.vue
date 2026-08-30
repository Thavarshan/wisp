<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useClipboard } from '@/composables/useClipboard';
import { useRevokeSecret } from '@/composables/useRevokeSecret';
import type { CreatedSecret } from '@/types/secret';
import { AlertTriangle, Check, Copy, ShieldAlert, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    secret: CreatedSecret;
}>();

const emit = defineEmits<{ 'create-another': [] }>();
const showRevokeDialog = ref(false);
const { copyToClipboard } = useClipboard();
const { status, error, revoke } = useRevokeSecret();

function openRevokeDialog() {
    if (status.value === 'revoked' || status.value === 'revoking') {
        return;
    }

    error.value = '';
    showRevokeDialog.value = true;
}

async function confirmRevoke(secret: CreatedSecret) {
    showRevokeDialog.value = false;
    await revoke(secret.accessToken, secret.revocationToken);
}
</script>

<template>
    <section class="w-full max-w-3xl">
        <Card class="border-border/80 bg-card/95 shadow-xl shadow-primary/5 backdrop-blur">
            <CardHeader class="space-y-4 border-b border-border/60">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-2">
                        <div
                            class="inline-flex size-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
                        >
                            <Check class="size-5" aria-hidden="true" />
                        </div>
                        <h1 class="text-2xl font-semibold tracking-tight sm:text-3xl">Your secure link is ready</h1>
                        <p class="max-w-xl text-sm leading-6 text-muted-foreground">
                            Send the link to the intended recipient. It will work once and then the secret is permanently deleted.
                        </p>
                    </div>
                    <Badge variant="secondary" class="shrink-0">{{ secret.expiration.label }}</Badge>
                </div>
            </CardHeader>

            <CardContent class="space-y-6 pt-6">
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-3">
                        <label for="share-link" class="text-sm font-medium">Shareable URL</label>
                        <span class="text-xs text-muted-foreground">Expires {{ new Date(secret.expiresAt).toLocaleString() }}</span>
                    </div>
                    <div class="flex gap-2">
                        <Input id="share-link" :model-value="secret.shareUrl" readonly class="font-mono text-xs" />
                        <Button type="button" variant="outline" size="icon" aria-label="Copy shareable URL" @click="copyToClipboard(secret.shareUrl)">
                            <Copy class="size-4" aria-hidden="true" />
                        </Button>
                    </div>
                </div>

                <div v-if="secret.password" class="space-y-2 rounded-lg border border-primary/20 bg-primary/5 p-4">
                    <div class="flex items-center gap-2 text-sm font-medium">
                        <ShieldAlert class="size-4 text-primary" aria-hidden="true" />
                        Separate password
                    </div>
                    <p class="text-xs leading-5 text-muted-foreground">
                        Send this password through a different channel. It is not included in the link.
                    </p>
                    <div class="flex gap-2">
                        <Input :model-value="secret.password" readonly type="text" class="font-mono" />
                        <Button
                            type="button"
                            variant="outline"
                            size="icon"
                            aria-label="Copy secret password"
                            @click="copyToClipboard(secret.password)"
                        >
                            <Copy class="size-4" aria-hidden="true" />
                        </Button>
                    </div>
                </div>

                <div class="space-y-2 rounded-lg border border-amber-500/30 bg-amber-500/5 p-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-amber-700 dark:text-amber-300">
                        <ShieldAlert class="size-4" aria-hidden="true" />
                        Private revocation token
                    </div>
                    <p class="text-xs leading-5 text-muted-foreground">
                        Keep this token private. Anyone with it can delete the secret before it is viewed. It is shown only once.
                    </p>
                    <div class="flex gap-2">
                        <Input :model-value="secret.revocationToken" readonly type="text" class="font-mono text-xs" />
                        <Button
                            type="button"
                            variant="outline"
                            size="icon"
                            aria-label="Copy private revocation token"
                            @click="copyToClipboard(secret.revocationToken)"
                        >
                            <Copy class="size-4" aria-hidden="true" />
                        </Button>
                    </div>
                </div>

                <Alert v-if="error" variant="destructive">
                    <AlertTriangle class="size-4" aria-hidden="true" />
                    <AlertTitle>Revocation failed</AlertTitle>
                    <AlertDescription>{{ error }}</AlertDescription>
                </Alert>

                <Alert v-if="status === 'revoked'">
                    <Check class="size-4" aria-hidden="true" />
                    <AlertTitle>Secret revoked</AlertTitle>
                    <AlertDescription>The link can no longer be opened.</AlertDescription>
                </Alert>
            </CardContent>

            <CardFooter class="flex-col gap-3 border-t border-border/60 pt-6 sm:flex-row sm:justify-between">
                <Button type="button" variant="destructive" :disabled="status === 'revoking' || status === 'revoked'" @click="openRevokeDialog">
                    <Trash2 class="mr-2 size-4" aria-hidden="true" />
                    {{ status === 'revoking' ? 'Revoking…' : status === 'revoked' ? 'Secret revoked' : 'Revoke secret' }}
                </Button>
                <Button type="button" variant="outline" @click="emit('create-another')">Create another secret</Button>
            </CardFooter>
        </Card>

        <div
            v-if="showRevokeDialog"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-5"
            role="presentation"
            @click.self="showRevokeDialog = false"
        >
            <div
                class="w-full max-w-md rounded-xl border border-border bg-card p-6 text-card-foreground shadow-2xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="revoke-title"
                aria-describedby="revoke-description"
            >
                <div class="flex items-start gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                        <AlertTriangle class="size-5" aria-hidden="true" />
                    </div>
                    <div class="space-y-2">
                        <h2 id="revoke-title" class="font-semibold">Revoke this secret?</h2>
                        <p id="revoke-description" class="text-sm leading-6 text-muted-foreground">
                            This permanently deletes the secret and makes the share link unusable. This cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="showRevokeDialog = false">Keep secret</Button>
                    <Button type="button" variant="destructive" @click="confirmRevoke(secret)">Revoke now</Button>
                </div>
            </div>
        </div>
    </section>
</template>
