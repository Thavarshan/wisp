<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased text-foreground bg-background relative isolate overflow-x-hidden lg:overflow-visible">
        <!-- Modern glowing orb background -->
        <div class="fixed inset-0 -z-10">
            <!-- Base gradient - lighter for light mode -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-black dark:via-slate-950 dark:to-black"></div>

            <!-- Glowing orbs with blur effects -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-r from-purple-500/20 to-blue-500/15 dark:from-purple-500/30 dark:to-blue-500/20 rounded-full filter blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/3 right-1/3 w-80 h-80 bg-gradient-to-l from-blue-600/18 to-indigo-600/22 dark:from-blue-600/25 dark:to-indigo-600/30 rounded-full filter blur-3xl animate-pulse animation-delay-2000"></div>
            <div class="absolute top-1/2 right-1/4 w-72 h-72 bg-gradient-to-br from-violet-500/15 to-purple-600/18 dark:from-violet-500/20 dark:to-purple-600/25 rounded-full filter blur-3xl animate-pulse animation-delay-4000"></div>
            <div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-gradient-to-tr from-cyan-500/12 to-blue-500/15 dark:from-cyan-500/15 dark:to-blue-500/20 rounded-full filter blur-3xl"></div>
        </div>
        @inertia
    </body>
</html>
