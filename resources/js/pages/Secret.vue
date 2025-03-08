<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Toaster, useToast } from '@/components/ui/toast';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { AlertCircle, Copy, Eye } from 'lucide-vue-next';
import copy from 'copy-to-clipboard';
import SecretContentInput from '@/components/SecretContentInput.vue';
import InputError from '@/components/InputError.vue';

const revealed = ref(false);

// Initialize form with a default expiration of 5 minutes
const props = defineProps<{
    secret: string;
    has_password: boolean;
}>();

const form = useForm({
    password: '',
});

const { toast } = useToast();

// Computed properties for two-way binding
const content = computed({
    get: () => props.secret,
    set: () => ''
});

function handleCopy() {
    copy(content.value);
    toast({
        title: 'Secret copied',
        description: 'The secret has been copied to your clipboard.',
    });
}

function getUidFromRoute() {
    return window.location.pathname.split('/').pop();
}

function handleRevealSecret() {
    if (props.has_password) {
        form.post(route('secrets.password', { secret: getUidFromRoute() }), {
            onSuccess: () => {
                revealed.value = true;
            },
        });

        return;
    }

    revealed.value = true;
}
</script>

<template>
    <Toaster />
    <Head title="One Time Secrets" />
    <div class="flex min-h-screen flex-col items-center lg:justify-center p-4 md:p-8 w-full">
        <div class="w-full max-w-2xl">
            <Card class="shadow-xl">
                <CardHeader>
                    <CardTitle>Secure secret!</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <Alert v-if="revealed" variant="destructive">
                        <AlertCircle class="w-4 h-4" />
                        <AlertTitle>This secret has been obliterated.</AlertTitle>
                        <AlertDescription>
                            Please be sure to store it in a safe place before closing this page.
                        </AlertDescription>
                    </Alert>
                    <div v-if="!revealed && props.has_password">
                        <Label for="password" >Password</Label>
                        <Input v-model="form.password" />
                        <InputError v-if="form.errors.password" :error="form.errors.password" />
                    </div>
                    <SecretContentInput v-show="revealed" v-model="content" />
                </CardContent>
                <CardFooter>
                    <Button v-if="!revealed" type="button" @click="handleRevealSecret" class="w-full" size="lg">
                        <Eye class="size-4 mr-1" /> Reveal secret
                    </Button>
                    <Button v-else type="button" @click="handleCopy" class="w-full" size="lg">
                        <Copy class="size-4 mr-1" /> Copy secret
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
