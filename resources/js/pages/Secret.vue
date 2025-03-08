<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Toaster, useToast } from '@/components/ui/toast';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { AlertCircle, Copy } from 'lucide-vue-next';
import copy from 'copy-to-clipboard';

import SecretContentInput from '@/components/SecretContentInput.vue';

// Initialize form with a default expiration of 5 minutes
const props = defineProps<{
    secret: string;
}>();

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
</script>

<template>
    <Toaster />
    <Head title="One Time Secrets" />
    <div class="flex min-h-screen flex-col items-center lg:justify-center p-4 md:p-8 w-full">
        <Card class="shadow-xl max-w-2xl">
            <CardHeader>
                <CardTitle>Here is your secret</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <Alert variant="destructive">
                    <AlertCircle class="w-4 h-4" />
                    <AlertTitle>This secret has been obliterated.</AlertTitle>
                    <AlertDescription>
                        Please be sure to store it in a safe place before closing this page.
                    </AlertDescription>
                </Alert>
                <SecretContentInput v-model="content" />
            </CardContent>
            <CardFooter>
                <Button type="button" @click="handleCopy" class="w-full" size="lg">
                    <Copy class="size-4 mr-1" /> Copy secret
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>
