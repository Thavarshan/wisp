<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipPortal, TooltipTrigger } from '@/components/ui/tooltip';
import { useAppearance, type Appearance } from '@/composables/useAppearance';
import { Monitor, Moon, Sun } from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();

const appearanceOptions: Array<{
    value: Appearance;
    label: string;
}> = [
    { value: 'light', label: 'Light' },
    { value: 'dark', label: 'Dark' },
    { value: 'system', label: 'System' },
];

const icons = { light: Sun, dark: Moon, system: Monitor };
</script>

<template>
    <div
        class="flex items-center gap-1 rounded-full border border-border/80 bg-background/80 p-1.5 shadow-sm backdrop-blur-md"
        role="group"
        aria-label="Choose appearance"
    >
        <Tooltip v-for="option in appearanceOptions" :key="option.value">
            <TooltipTrigger as-child>
                <Button
                    :variant="appearance === option.value ? 'default' : 'ghost'"
                    size="icon"
                    class="size-10 rounded-full transition-colors"
                    :class="
                        appearance === option.value
                            ? 'bg-primary text-primary-foreground shadow-sm hover:bg-primary/90'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                    :aria-label="`Use ${option.label} appearance`"
                    :aria-pressed="appearance === option.value"
                    :title="`${option.label} appearance`"
                    @click="updateAppearance(option.value)"
                >
                    <component :is="icons[option.value]" class="size-5" aria-hidden="true" />
                </Button>
            </TooltipTrigger>
            <TooltipPortal>
                <TooltipContent class="z-50 rounded-md bg-foreground px-3 py-1.5 text-xs text-background shadow-md">
                    {{ option.label }} appearance
                </TooltipContent>
            </TooltipPortal>
        </Tooltip>
    </div>
</template>
