<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title') · Wisp</title>
        @vite(['resources/css/app.css'])
        <script>
            (() => {
                const appearance = localStorage.getItem('appearance');
                const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = appearance === 'dark' || (appearance !== 'light' && systemDark);

                document.documentElement.classList.toggle('dark', isDark);
            })();
        </script>
    </head>

    <body class="relative isolate min-h-screen overflow-x-hidden bg-background font-sans text-foreground antialiased">
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
            <div class="absolute left-1/2 top-[-18rem] h-[36rem] w-[36rem] -translate-x-1/2 rounded-full bg-primary/10 blur-3xl dark:bg-primary/5"></div>
            <div class="absolute bottom-[-20rem] right-[-10rem] h-[34rem] w-[34rem] rounded-full bg-accent/70 blur-3xl dark:bg-accent/10"></div>
        </div>

        <main class="mx-auto flex min-h-screen w-full max-w-2xl flex-col justify-center px-5 py-12 sm:px-8">
            <a href="/" class="mb-8 inline-flex w-fit rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                <img src="/images/logo.svg" alt="Wisp home" class="h-8 w-auto dark:invert">
            </a>

            <section class="rounded-xl border border-border/80 bg-card/95 p-6 shadow-xl shadow-primary/5 backdrop-blur sm:p-8">
                <div class="flex items-start gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-lg font-semibold text-primary">
                        @yield('code')
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs font-medium uppercase tracking-[0.2em] text-muted-foreground">Wisp</p>
                        <h1 class="text-2xl font-semibold tracking-tight">@yield('message')</h1>
                        <p class="text-sm leading-6 text-muted-foreground">@yield('description', 'We could not complete that request. You can safely return to the home page.')</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="/" class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-6 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        Return to Wisp
                    </a>
                    <button type="button" onclick="window.location.reload()" class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-background px-6 text-sm font-medium shadow-sm transition-colors hover:bg-accent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        Try again
                    </button>
                </div>
            </section>

            <p class="mt-6 text-xs text-muted-foreground">Wisp keeps secrets private, temporary, and out of your history.</p>
        </main>
    </body>
</html>
