import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue, type Config } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import { consumeAccessToken } from './lib/secretLink';
import { configureSecretApi } from './services/secretApi';

const appName = import.meta.env.VITE_APP_NAME || 'Wisp';

window.__wispAccessToken = consumeAccessToken();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const ziggyConfig = props.initialPage.props.ziggy as Config;

        configureSecretApi(ziggyConfig);

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, ziggyConfig)
            .mount(el);
    },
    progress: false,
});

// This will set light / dark mode on page load...
initializeTheme();
