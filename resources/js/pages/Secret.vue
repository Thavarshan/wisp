<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import duration from 'dayjs/plugin/duration';
import copy from 'copy-to-clipboard';
import { Toaster, useToast } from '@/components/ui/toast';
import { getUidFromRoute } from '@/lib/utils';
import AppLogo from '@/components/AppLogo.vue';
import { Card, CardHeader, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { AlertCircle, Copy, Eye } from 'lucide-vue-next';
import SecretContentInput from '@/components/SecretContentInput.vue';
import InputError from '@/components/InputError.vue';

const props = defineProps<{
    secret: string;
    has_password: boolean;
    expired_at: string;
}>();

const revealed = ref(false);
const expirationTimeout = ref<number | null>(null);
const countdown = ref('');
const intervalId = ref<number | undefined>(undefined);
const { toast } = useToast();
const form = useForm<{ password: string; }>({ password: '' });
const uid = getUidFromRoute();

dayjs.extend(relativeTime);
dayjs.extend(duration);

const expirationTime = computed(() => dayjs(props.expired_at));
const content = computed({
    get: () => (revealed.value ? props.secret : ''),
    set: () => '' // Read-only
});

// Function to update the countdown clock every second
function updateCountdown() {
    const now = dayjs();
    const timeLeft = expirationTime.value.diff(now);

    if (timeLeft <= 0) {
        countdown.value = 'Expired';
        clearInterval(intervalId.value);
        checkExpiration();
    } else {
        const durationLeft = dayjs.duration(timeLeft);
        countdown.value = `${durationLeft.minutes()}m ${durationLeft.seconds()}s`;
    }
}

// Function to check if the secret has expired and refresh the page
function checkExpiration() {
    if (expirationTime.value.isBefore(dayjs())) {
        window.location.reload();
    }
}

// Function to copy secret to clipboard
function handleCopy() {
    copy(props.secret);
    toast({
        title: 'Secret copied',
        description: 'The secret has been copied to your clipboard.',
    });
}

// Function to reveal the secret
function handleRevealSecret() {
    if (props.has_password) {
        form.post(route('secrets.password', { secret: uid }), {
            preserveScroll: true,
            onSuccess: () => {
                obliterateSecret(() => revealed.value = true);
            }
        });
        return;
    }

    obliterateSecret(() => revealed.value = true);
}

// Function to delete the secret after revealing
function obliterateSecret(callback?: () => void): void {
    axios.delete(route('secrets.destroy', { secret: uid }))
        .then(() => {
            if (callback) callback();
        })
        .catch((error) => {
            toast({
                title: 'Unable to reveal secret',
                description: 'An error occurred while trying to reveal the secret: ' + error.message,
            });
        });
}

// Watch for changes in expired_at and refresh when expired
watch(expirationTime, (newExpirationTime) => {
    if (expirationTimeout.value) {
        clearTimeout(expirationTimeout.value);
    }

    const now = dayjs();
    const timeLeft = newExpirationTime.diff(now);

    if (timeLeft <= 0) {
        checkExpiration();
    } else {
        expirationTimeout.value = setTimeout(checkExpiration, timeLeft);
    }
}, { immediate: true });

onMounted(() => {
    updateCountdown();
    intervalId.value = setInterval(updateCountdown, 1000);
});

onUnmounted(() => {
    if (intervalId.value) {
        clearInterval(intervalId.value);
    }
    if (expirationTimeout.value) {
        clearTimeout(expirationTimeout.value);
    }
});
</script>

<template>
    <Toaster />
    <Head title="One Time Secrets" />
    <div class="flex min-h-screen flex-col items-center lg:justify-center p-4 md:p-8 w-full">
        <div class="w-full max-w-2xl">
            <Card class="shadow-xl">
                <CardHeader>
                    <AppLogo href="/" classes="h-12 mx-auto" title="Cryptide" />
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex items-center justify-center">
                        <Badge v-if="countdown !== 'Expired'">
                            Expires in {{ countdown }}
                        </Badge>
                        <Badge v-else variant="destructive">
                            Secret has expired
                        </Badge>
                    </div>
                    <Alert v-if="revealed" variant="destructive">
                        <AlertCircle class="w-4 h-4" />
                        <AlertTitle>This secret has been obliterated.</AlertTitle>
                        <AlertDescription>
                            Please be sure to store it in a safe place before closing this page.
                        </AlertDescription>
                    </Alert>
                    <div v-if="!revealed && props.has_password">
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            type="password"
                            required
                            :tabindex="2"
                            v-model="form.password"
                            placeholder="Password"
                        />
                        <InputError :message="form.errors.password" />
                    </div>
                    <SecretContentInput v-show="revealed" v-model="content" />
                </CardContent>
                <CardFooter>
                    <Button
                        v-if="!revealed"
                        type="button"
                        @click="handleRevealSecret"
                        class="w-full"
                        size="lg"
                        :disabled="props.has_password && !form.password"
                    >
                        <Eye class="size-4 mr-1" /> Reveal secret
                    </Button>
                    <Button
                        v-else
                        type="button"
                        @click="handleCopy"
                        class="w-full"
                        size="lg"
                    >
                        <Copy class="size-4 mr-1" /> Copy secret
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
