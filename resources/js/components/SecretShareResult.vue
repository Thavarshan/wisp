<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useClipboard } from '@/composables/useClipboard';
import { Head } from '@inertiajs/vue3';
import { Bomb, Copy } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    access_token: string;
    share_url: string;
    revocation_token: string;
    expires_at: string;
    expiration: { value: string; label: string };
}>();

const emit = defineEmits<{ reset: [] }>();
const isRevoking = ref(false);
const revoked = ref(false);
const { copyToClipboard } = useClipboard();

async function revokeSecret() {
    if (isRevoking.value || revoked.value) return;

    isRevoking.value = true;

    try {
        const response = await fetch(route('secrets.revoke', { token: props.access_token }), {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({ revocation_token: props.revocation_token }),
        });

        if (!response.ok) throw new Error('Unable to revoke secret');
        revoked.value = true;
    } finally {
        isRevoking.value = false;
    }
}
</script>

<template>
    <Head title="Secret created" />
    <div class="flex min-h-screen w-full flex-col items-center justify-center p-4 md:p-8">
        <div class="w-full max-w-xl space-y-6 text-center">
            <h1 class="text-3xl font-bold text-accent-foreground">Your secret is ready</h1>
            <p class="text-muted-foreground">Share the link once. The secret is deleted after one successful reveal or when it expires.</p>
            <Badge>Expires {{ new Date(expires_at).toLocaleString() }}</Badge>

            <div class="space-y-2 text-left">
                <label for="share-link" class="text-sm font-medium">Share link</label>
                <div class="relative flex items-center">
                    <Input id="share-link" :model-value="share_url" readonly class="pr-12" />
                    <Button type="button" size="icon" class="absolute right-0 mr-px" @click="copyToClipboard(share_url)"
                        ><Copy class="size-4"
                    /></Button>
                </div>
            </div>

            <div class="space-y-2 text-left">
                <label for="revocation-token" class="text-sm font-medium">Revocation token</label>
                <p class="text-xs text-muted-foreground">Save this separately. It is shown only now and cannot be recovered after refresh.</p>
                <div class="relative flex items-center">
                    <Input id="revocation-token" :model-value="revocation_token" readonly class="pr-12" />
                    <Button type="button" size="icon" class="absolute right-0 mr-px" @click="copyToClipboard(revocation_token)"
                        ><Copy class="size-4"
                    /></Button>
                </div>
            </div>

            <Button type="button" variant="destructive" size="lg" class="w-full" :disabled="isRevoking || revoked" @click="revokeSecret">
                <Bomb class="mr-2 size-4" /> {{ revoked ? 'Secret revoked' : 'Revoke secret' }}
            </Button>
            <Button type="button" variant="outline" class="w-full" @click="emit('reset')">Create another secret</Button>
        </div>
    </div>
</template>
