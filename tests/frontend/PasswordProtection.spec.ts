import PasswordProtection from '@/components/PasswordProtection.vue';
import { mount } from '@vue/test-utils';
import { defineComponent, ref } from 'vue';

describe('PasswordProtection', () => {
    it('uses the modelValue contract and generates and clears passwords predictably', async () => {
        const Harness = defineComponent({
            components: { PasswordProtection },
            setup() {
                const enabled = ref(false);
                const password = ref('');

                return { enabled, password };
            },
            template: `
                <PasswordProtection v-model:enabled="enabled" v-model:password="password" />
            `,
        });
        const wrapper = mount(Harness);

        await wrapper.get('[role="checkbox"]').trigger('click');

        expect(wrapper.vm.enabled).toBe(true);
        expect(wrapper.vm.password).toHaveLength(12);
        expect(wrapper.find('#secret-password').exists()).toBe(true);

        await wrapper.get('[role="checkbox"]').trigger('click');

        expect(wrapper.vm.enabled).toBe(false);
        expect(wrapper.vm.password).toBe('');
        expect(wrapper.find('#secret-password').exists()).toBe(false);
    });
});
