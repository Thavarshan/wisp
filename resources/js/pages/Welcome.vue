<script setup lang="ts">
import ExpirationOptions from '@/components/ExpirationOptions.vue';
import InputError from '@/components/InputError.vue';
import PasswordProtection from '@/components/PasswordProtection.vue';
import SecretContentInput from '@/components/SecretContentInput.vue';
import SecretShareResult from '@/components/SecretShareResult.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from '@/components/ui/card';
import { useCreateSecret } from '@/composables/useCreateSecret';
import AppLayout from '@/layouts/AppLayout.vue';
import { MAX_SECRET_LENGTH, type CreateSecretPayload, type CreatedSecret, type ExpirationOption } from '@/types/secret';
import { Head } from '@inertiajs/vue3';
import { ArrowRight, LockKeyhole, ShieldCheck } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{ expiration_options: ExpirationOption[] }>();

interface CreateForm {
    content: string;
    expiration: ExpirationOption['value'];
    password: string;
}

const share = ref<CreatedSecret | null>(null);
const form = reactive<CreateForm>({
    content: '',
    expiration: props.expiration_options[0]?.value ?? '5m',
    password: '',
});
const passwordProtected = ref(false);
const { status, error, fieldErrors, submit } = useCreateSecret();

function fieldError(field: keyof CreateSecretPayload) {
    return fieldErrors.value[field]?.[0];
}

function resetForm() {
    form.content = '';
    form.expiration = props.expiration_options[0]?.value ?? '5m';
    form.password = '';
    passwordProtected.value = false;
}

async function handleSubmit() {
    const result = await submit({
        content: form.content,
        expiration: form.expiration,
        password: passwordProtected.value ? form.password : null,
    });

    if (result) {
        share.value = result;
        resetForm();
    }
}

function createAnother() {
    share.value = null;
}
</script>

<template>
    <Head title="Share a secret" />

    <SecretShareResult v-if="share" :secret="share" @create-another="createAnother" />

    <section v-else class="grid w-full items-center gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-16">
        <div class="space-y-6">
            <div
                class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-3 py-1.5 text-xs font-medium text-primary"
            >
                <ShieldCheck class="size-3.5" aria-hidden="true" />
                Built for private handoffs
            </div>
            <div class="space-y-4">
                <h1 class="max-w-xl text-4xl font-semibold tracking-tight text-foreground sm:text-5xl">
                    Share something sensitive, then let it disappear.
                </h1>
                <p class="max-w-lg text-base leading-7 text-muted-foreground sm:text-lg">
                    Wisp creates a private link that can be opened once. No accounts, no plaintext at rest, and no lingering secret in your browser
                    history.
                </p>
            </div>
            <div class="grid gap-3 text-sm text-muted-foreground sm:grid-cols-3 lg:grid-cols-1">
                <div class="flex items-center gap-2"><span class="size-1.5 rounded-full bg-primary" />Encrypted at rest</div>
                <div class="flex items-center gap-2"><span class="size-1.5 rounded-full bg-primary" />One successful reveal</div>
                <div class="flex items-center gap-2"><span class="size-1.5 rounded-full bg-primary" />Automatic expiration</div>
            </div>
        </div>

        <Card class="border-border/80 bg-card/90 shadow-xl shadow-primary/5 backdrop-blur">
            <CardHeader class="space-y-2 border-b border-border/60">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <LockKeyhole class="size-5" aria-hidden="true" />
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">Create a secret</h2>
                        <CardDescription>It will be deleted after it is revealed or expires.</CardDescription>
                    </div>
                </div>
            </CardHeader>

            <form @submit.prevent="handleSubmit">
                <CardContent class="space-y-6 pt-6">
                    <SecretContentInput v-model="form.content" :error="fieldError('content')" />

                    <ExpirationOptions v-model="form.expiration" :options="expiration_options" :error="fieldError('expiration')" />

                    <PasswordProtection v-model:enabled="passwordProtected" v-model:password="form.password" :error="fieldError('password')" />

                    <InputError :message="error" role="alert" />
                </CardContent>

                <CardFooter class="flex-col items-stretch gap-3 border-t border-border/60 pt-6">
                    <Button type="submit" size="lg" class="w-full" :disabled="status === 'submitting' || form.content.length > MAX_SECRET_LENGTH">
                        <span>{{ status === 'submitting' ? 'Creating secure link…' : 'Create secure link' }}</span>
                        <ArrowRight class="ml-auto size-4" aria-hidden="true" />
                    </Button>
                    <p class="text-center text-xs text-muted-foreground">By continuing, you confirm you have permission to share this information.</p>
                </CardFooter>
            </form>
        </Card>
    </section>
</template>
