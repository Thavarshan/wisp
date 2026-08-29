<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import ExpirationOptions from '@/components/ExpirationOptions.vue';
import InputError from '@/components/InputError.vue';
import PasswordProtection from '@/components/PasswordProtection.vue';
import SecretContentInput from '@/components/SecretContentInput.vue';
import SecretShareResult from '@/components/SecretShareResult.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from '@/components/ui/card';
import { Head, useForm } from '@inertiajs/vue3';
import { Lock } from 'lucide-vue-next';
import { ref } from 'vue';

interface ExpirationOption {
    value: string;
    label: string;
}

interface SecretShare {
    access_token: string;
    share_url: string;
    revocation_token: string;
    expires_at: string;
    expiration: ExpirationOption;
}

const props = defineProps<{ expiration_options: ExpirationOption[] }>();
const share = ref<SecretShare | null>(null);
const error = ref('');
const isSubmitting = ref(false);

const form = useForm({
    content: '',
    expiration: props.expiration_options[0]?.value ?? '5m',
    password: '',
    password_protect: false,
});

function csrfToken(): string {
    return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
}

async function handleSubmit() {
    if (isSubmitting.value) return;

    isSubmitting.value = true;
    error.value = '';
    form.clearErrors();

    try {
        const response = await fetch(route('secrets.store'), {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({
                content: form.content,
                expiration: form.expiration,
                password: form.password_protect ? form.password : null,
            }),
        });
        const payload = await response.json();

        if (response.status === 422) {
            const fields = ['content', 'expiration', 'password', 'password_protect'] as const;
            for (const [field, messages] of Object.entries(payload.errors ?? {})) {
                if (fields.includes(field as (typeof fields)[number])) {
                    form.setError(field as (typeof fields)[number], (messages as string[])[0]);
                }
            }
            return;
        }

        if (!response.ok) throw new Error(payload.message ?? 'Unable to create secret.');

        share.value = payload as SecretShare;
        form.reset();
        form.expiration = props.expiration_options[0]?.value ?? '5m';
        form.password_protect = false;
    } catch (caught) {
        error.value = caught instanceof Error ? caught.message : 'Unable to create secret.';
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <SecretShareResult v-if="share" v-bind="share" @reset="share = null" />
    <template v-else>
        <Head title="One-time secrets" />
        <div class="flex min-h-screen w-full flex-col items-center justify-center p-4 md:p-8">
            <form class="w-full max-w-2xl" @submit.prevent="handleSubmit">
                <Card glassBorder class="shadow-xl">
                    <CardHeader>
                        <AppLogo href="/" classes="h-12 mx-auto" title="Wisp" />
                        <CardDescription class="mt-2 space-y-3 text-center">
                            <p class="text-normal text-accent-foreground sm:text-lg">
                                Share a confidential, one-time secret through a secure, expiring link.
                            </p>
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <SecretContentInput v-model="form.content" :error="form.errors.content" />
                        <ExpirationOptions v-model="form.expiration" :options="expiration_options" />
                        <PasswordProtection v-model:password="form.password" v-model:enabled="form.password_protect" :error="form.errors.password" />
                        <InputError :message="error" />
                    </CardContent>
                    <CardFooter>
                        <Button type="submit" class="w-full" size="lg" :disabled="isSubmitting">
                            <Lock class="mr-1 size-4" /> {{ isSubmitting ? 'Creating…' : 'Share secret' }}
                        </Button>
                    </CardFooter>
                </Card>
            </form>
        </div>
    </template>
</template>
