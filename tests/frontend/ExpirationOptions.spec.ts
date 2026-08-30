import ExpirationOptions from '@/components/ExpirationOptions.vue';
import type { ExpirationOption } from '@/types/secret';
import { mount } from '@vue/test-utils';

const options: ExpirationOption[] = [
    { value: '5m', label: '5 minutes' },
    { value: '30m', label: '30 minutes' },
    { value: '1h', label: '1 hour' },
    { value: '6h', label: '6 hours' },
    { value: '12h', label: '12 hours' },
    { value: '1d', label: '1 day' },
    { value: '2d', label: '2 days' },
    { value: '1w', label: '1 week' },
];

describe('ExpirationOptions', () => {
    it('renders an accessible radio grid and emits the selected option', async () => {
        const wrapper = mount(ExpirationOptions, {
            props: { modelValue: '5m', options },
        });

        expect(wrapper.findAll('[role="radio"]').length).toBe(0);
        expect(wrapper.findAll('input[type="radio"]')).toHaveLength(8);

        await wrapper.find('input[value="1h"]').trigger('change');

        expect(wrapper.emitted('update:modelValue')).toEqual([['1h']]);
    });
});
