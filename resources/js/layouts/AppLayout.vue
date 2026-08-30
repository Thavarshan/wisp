<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Toaster } from '@/components/ui/toast';
import { useAppearance, type Appearance } from '@/composables/useAppearance';
import { Monitor, Moon, Sun } from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();

const appearanceOptions: Array<{ value: Appearance; label: string }> = [
    { value: 'light', label: 'Light' },
    { value: 'dark', label: 'Dark' },
    { value: 'system', label: 'System' },
];

const icons = { light: Sun, dark: Moon, system: Monitor };
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
            <div class="absolute left-1/2 top-[-18rem] h-[36rem] w-[36rem] -translate-x-1/2 rounded-full bg-primary/10 blur-3xl dark:bg-primary/5" />
            <div class="absolute bottom-[-20rem] right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-accent/70 blur-3xl dark:bg-accent/10" />
        </div>

        <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-5 py-5 sm:px-8">
            <AppLogo href="/" title="Wisp" classes="h-7 w-auto" />

            <div
                class="flex items-center gap-1 rounded-full border border-border/70 bg-background/80 p-1 shadow-sm backdrop-blur"
                aria-label="Appearance"
            >
                <button
                    v-for="option in appearanceOptions"
                    :key="option.value"
                    type="button"
                    class="rounded-full p-2 text-muted-foreground transition-colors hover:bg-accent hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    :class="{ 'bg-primary text-primary-foreground hover:bg-primary hover:text-primary-foreground': appearance === option.value }"
                    :aria-label="`${option.label} theme`"
                    :aria-pressed="appearance === option.value"
                    @click="updateAppearance(option.value)"
                >
                    <component :is="icons[option.value]" class="size-4" aria-hidden="true" />
                </button>
            </div>
        </header>

        <main class="mx-auto flex min-h-[calc(100vh-9.5rem)] w-full max-w-6xl items-center px-5 pb-12 sm:px-8">
            <slot />
        </main>

        <footer
            class="mx-auto flex w-full max-w-6xl flex-col gap-1 px-5 pb-7 text-center text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between sm:px-8 sm:text-left"
        >
            <span>Wisp keeps secrets private, temporary, and out of your history.</span>
            <span class="font-mono tracking-wide">encrypted · one-time · expiring</span>
        </footer>

        <Toaster />
    </div>
</template>
