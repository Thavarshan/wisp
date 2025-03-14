import { defineConfig } from 'vitepress';

// https://vitepress.dev/reference/site-config
export default defineConfig({
    title: "Cerberus",
    description: "Simple IAM service API",
    themeConfig: {
        // https://vitepress.dev/reference/default-theme-config
        nav: [
            { text: 'Home', link: '/' },
            { text: 'Support', link: '/markdown-examples' }
        ],

        sidebar: [
            {
                text: 'Getting Started',
                link: '/getting-started'
            },
            {
                text: 'Authentication',
                items: [
                    { text: 'Overview', link: '/authentication' },
                    { text: 'Client', link: '/client' }
                ]
            },
            {
                text: 'Authorisation',
                items: [
                    { text: 'Overview', link: '/authorisation' },
                    { text: 'Roles', link: '/authorisation/roles' },
                    { text: 'Permissions', link: '/authorisation/permissions' },
                    { text: 'Resources', link: '/authorisation/resources' }
                ]
            },
            {
                text: 'Users',
                items: [
                    { text: 'Overview', link: '/users' },
                    { text: 'List all', link: '/users/get-all' },
                    { text: 'Retrieve', link: '/users/get-one' },
                    { text: 'Create', link: '/users/create' },
                    { text: 'Update', link: '/users/update' },
                    { text: 'Delete', link: '/users/delete' }
                ]
            }
        ],

        socialLinks: [
            { icon: 'github', link: 'https://github.com/cyber-cosmos/cerberus' }
        ]
    },
    base: '/docs/',
    outDir: '../public/docs',
    ignoreDeadLinks: true
});
