<script setup lang="ts">
import { ref } from 'vue';

import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuPortal,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Tooltip, TooltipContent, TooltipPortal, TooltipTrigger } from '@/components/ui/tooltip';
import { useAppearance, type Appearance } from '@/composables/useAppearance';
import { Check, Monitor, Moon, Sun } from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();
const appearanceTrigger = ref<HTMLElement>();

const appearanceOptions: Array<{ value: Appearance; label: string }> = [
    { value: 'system', label: 'System' },
    { value: 'light', label: 'Light' },
    { value: 'dark', label: 'Dark' },
];

const icons = { light: Sun, dark: Moon, system: Monitor };

function selectAppearance(value: string | number | boolean | null) {
    if (value === 'light' || value === 'dark' || value === 'system') {
        updateAppearance(value);
    }
}
</script>

<template>
    <DropdownMenu>
        <Tooltip>
            <TooltipTrigger as-child>
                <DropdownMenuTrigger as-child>
                    <Button
                        ref="appearanceTrigger"
                        variant="outline"
                        size="icon"
                        class="rounded-full"
                        aria-label="Choose appearance"
                        title="Choose appearance"
                    >
                        <component :is="icons[appearance]" class="size-4" aria-hidden="true" />
                    </Button>
                </DropdownMenuTrigger>
            </TooltipTrigger>
            <TooltipPortal>
                <TooltipContent class="z-50 rounded-md bg-foreground px-3 py-1.5 text-xs text-background shadow-md">
                    Choose appearance
                </TooltipContent>
            </TooltipPortal>
        </Tooltip>

        <DropdownMenuPortal>
            <DropdownMenuContent
                align="end"
                :reference="appearanceTrigger"
                :side-offset="8"
                class="z-50 min-w-52 rounded-xl border border-border/80 bg-popover/95 p-2 text-popover-foreground shadow-xl shadow-primary/10 outline-none backdrop-blur-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95"
            >
                <DropdownMenuLabel class="px-3 pb-2 pt-1 text-xs font-semibold uppercase tracking-[0.16em] text-muted-foreground">
                    Appearance
                </DropdownMenuLabel>
                <DropdownMenuSeparator class="mx-2 mb-1 bg-border/60" />
                <DropdownMenuRadioGroup :model-value="appearance" @update:model-value="selectAppearance">
                    <DropdownMenuRadioItem
                        v-for="option in appearanceOptions"
                        :key="option.value"
                        :value="option.value"
                        class="group relative flex min-h-11 cursor-default select-none items-center gap-3 rounded-lg px-3 py-2 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[highlighted]:bg-accent/70 data-[disabled]:opacity-50"
                        :class="appearance === option.value ? 'bg-primary/10 text-foreground' : 'text-muted-foreground'"
                    >
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-background text-foreground shadow-sm ring-1 ring-border/70 transition-colors group-data-[highlighted]:bg-background"
                        >
                            <component :is="icons[option.value]" class="size-4" aria-hidden="true" />
                        </span>
                        <span class="flex-1 font-medium">{{ option.label }}</span>
                        <span
                            class="flex size-5 items-center justify-center rounded-full text-primary transition-opacity"
                            :class="appearance === option.value ? 'opacity-100' : 'opacity-0'"
                        >
                            <Check class="size-4" aria-hidden="true" />
                        </span>
                    </DropdownMenuRadioItem>
                </DropdownMenuRadioGroup>
            </DropdownMenuContent>
        </DropdownMenuPortal>
    </DropdownMenu>
</template>
