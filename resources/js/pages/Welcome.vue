<script setup lang="ts">
import { Card, CardHeader, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Toaster, useToast } from '@/components/ui/toast';
import { Head, useForm } from '@inertiajs/vue3';
import { Lock } from 'lucide-vue-next';
import { computed } from 'vue';

import SecretContentInput from '@/components/SecretContentInput.vue';
import NameInput from '@/components/NameInput.vue';
import ExpirationOptions from '@/components/ExpirationOptions.vue';
import PasswordProtection from '@/components/PasswordProtection.vue';
import { SecretsForm } from '@/types';
import AppLogo from '@/components/AppLogo.vue';

// Initialize form with a default expiration of 5 minutes
const form = useForm<SecretsForm>({
    name: '',
    content: '',
    expired_at: '5m',
    password: '',
    password_protect: false,
});

const { toast } = useToast();

// Computed properties for two-way binding
const content = computed({
    get: () => form.content,
    set: (value) => form.content = value
});

const name = computed({
    get: () => form.name,
    set: (value) => form.name = value
});

const expiresAt = computed({
    get: () => form.expired_at,
    set: (value) => form.expired_at = value
});

const password = computed({
    get: () => form.password,
    set: (value) => form.password = value
});

const passwordProtect = computed({
    get: () => form.password_protect,
    set: (value) => form.password_protect = value
});

// Submit the form with ISO string date
function handleSubmit() {
    form.post(route('secrets.store'), {
        onSuccess: () => {
            form.reset();
            toast({
                title: 'Success',
                description: 'Your secret has been stored successfully.',
            });
        },
        onError: () => {
            toast({
                title: 'Error',
                description: 'There was an error storing your secret. Please try again.',
            });
        },
    });
}
</script>

<template>
    <Toaster />
    <Head title="One Time Secrets" />
    <div class="flex min-h-screen flex-col items-center lg:justify-center p-4 md:p-8 w-full">
        <form @submit.prevent="handleSubmit" class="w-full max-w-2xl">
            <Card class="shadow-xl">
                <CardHeader>
                    <AppLogo href="/" classes="h-12 mx-auto" title="Cryptide" />
                    <CardDescription class="mt-2 space-y-3 text-center">
                        <p class="text-accent-foreground text-normal sm:text-lg">Share a confidential, one-time secret through a secure link that automatically expires.</p>
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <SecretContentInput v-model="content" />
                    <NameInput v-model="name" />
                    <ExpirationOptions v-model="expiresAt" />
                    <PasswordProtection
                        v-model:password="password"
                        v-model:enabled="passwordProtect"
                    />
                </CardContent>
                <CardFooter>
                    <Button type="submit" class="w-full" size="lg">
                        <Lock class="size-4 mr-1" /> Share secret
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </div>
</template>
