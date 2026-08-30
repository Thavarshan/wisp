import PasswordProtection from '@/components/PasswordProtection.vue';
import { TooltipProvider } from '@/components/ui/tooltip';
import { mount } from '@vue/test-utils';
import { defineComponent, ref } from 'vue';

describe('PasswordProtection', () => {
    it('uses the modelValue contract and generates and clears passwords predictably', async () => {
        const Harness = defineComponent({
            components: { PasswordProtection, TooltipProvider },
            setup() {
                const enabled = ref(false);
                const password = ref('');

                return { enabled, password };
            },
            template: `
                <TooltipProvider>
                    <PasswordProtection v-model:enabled="enabled" v-model:password="password" />
                </TooltipProvider>
            `,
        });
        const wrapper = mount(Harness);

        await wrapper.get('[role="checkbox"]').trigger('click');

        expect(wrapper.vm.enabled).toBe(true);
        expect(wrapper.vm.password).toHaveLength(12);
        expect(wrapper.find('#secret-password').exists()).toBe(true);

        const generatedPassword = wrapper.vm.password;

        await wrapper.get('[aria-label="Regenerate password"]').trigger('click');

        expect(wrapper.vm.password).toHaveLength(12);
        expect(wrapper.vm.password).not.toBe(generatedPassword);

        await wrapper.get('[aria-label="Show password"]').trigger('click');

        expect(wrapper.find('#secret-password').attributes('type')).toBe('text');

        await wrapper.get('[role="checkbox"]').trigger('click');

        expect(wrapper.vm.enabled).toBe(false);
        expect(wrapper.vm.password).toBe('');
        expect(wrapper.find('#secret-password').exists()).toBe(false);
    });
});
