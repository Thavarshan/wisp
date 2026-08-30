<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/js/app.ts', 'resources/css/app.css'])
        @inertiaHead
        <script>
            (() => {
                const appearance = localStorage.getItem('appearance');
                const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = appearance === 'dark' || (appearance !== 'light' && systemDark);

                document.documentElement.classList.toggle('dark', isDark);
            })();
        </script>
    </head>

    <body class="bg-background font-sans antialiased text-foreground">
        @inertia
    </body>
</html>
