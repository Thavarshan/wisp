import { toast, useToast } from '@/components/ui/toast';
import Toaster from '@/components/ui/toast/Toaster.vue';
import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';

describe('Toaster', () => {
    it('renders feedback from the shared toast store', async () => {
        const wrapper = mount(Toaster);

        toast({
            title: 'Password copied',
            description: 'Share it separately.',
        });
        await nextTick();

        expect(wrapper.text()).toContain('Password copied');
        expect(wrapper.text()).toContain('Share it separately.');

        useToast().dismiss();
    });
});
