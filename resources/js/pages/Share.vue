<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Toaster, useToast } from '@/components/ui/toast';
import { Head, router } from '@inertiajs/vue3';
import { Copy, Bomb } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import copy from 'copy-to-clipboard';
import { extractSecretUid } from '@/lib/utils';

const props = defineProps<{
    link: string;
    expired_at: string;
}>();

const { toast } = useToast();
const isDeleted = ref(false);

const shareLink = computed({
    get: () => props.link,
    set: () => ''
});

function handleCopy() {
    copy(props.link);
    toast({
        title: 'Copied!',
        description: 'Link copied to clipboard.',
    });
}

function handleDelete() {
    router.delete(route('secrets.destroy', { secret: extractSecretUid(props.link) }));
    isDeleted.value = true;
    toast({
        title: 'Deleted!',
        description: 'Your secret has been obliterated.',
    });
}
</script>

<template>
    <Toaster />
    <Head title="Share Secret" />
    <div class="flex min-h-screen flex-col items-center lg:justify-center p-4 md:p-8 w-full">
        <div class="w-full max-w-xl text-center space-y-6">
            <AppLogo href="/" classes="h-12 mx-auto" title="Cryptide" />
            <h1 class="text-3xl font-bold text-accent-foreground">Your secret is ready to be shared</h1>
            <p class="text-muted-foreground">Share the link below with the recipient. The secret will be obliterated after a single view or when it expires.</p>
            <div>
                <Badge>
                    Expires {{ expired_at }}
                </Badge>
            </div>

            <div class="relative flex items-center justify-center">
                <Input
                    type="text"
                    v-model="shareLink"
                    readonly
                />
                <Button
                    type="button"
                    size="icon"
                    class="absolute right-0 mr-px"
                    @click="handleCopy"
                >
                    <Copy class="size-4" />
                </Button>
            </div>

            <Button
                type="button"
                variant="destructive"
                size="lg"
                class="w-full"
                @click="handleDelete"
                :disabled="isDeleted"
            >
                <Bomb class="size-4" />
                {{ isDeleted ? 'Secret Obliterated' : 'Obliterate secret' }}
            </Button>
        </div>
    </div>
</template>
