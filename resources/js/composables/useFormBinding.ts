import { computed, WritableComputedRef } from 'vue';

export function useFormBinding<T extends Record<string, any>>(
    form: T
): { [K in keyof T]: WritableComputedRef<T[K]> } {
    const bindings = {} as { [K in keyof T]: WritableComputedRef<T[K]> };

    for (const key in form) {
        bindings[key] = computed({
            get: () => form[key],
            set: (value) => { form[key] = value; }
        });
    }

    return bindings;
}
