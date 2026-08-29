<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
    </head>
    <body class="font-sans antialiased text-foreground bg-background relative isolate overflow-x-hidden lg:overflow-visible">
        <div class="absolute -z-10 bottom-0 left-0 right-0 top-0 bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] bg-[size:44px_44px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
        <div class="absolute -z-10 bottom-0 left-0 right-0 top-0 bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] bg-[size:44px_44px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
        <div class="flex min-h-screen flex-col items-center lg:justify-center p-4 md:p-8 w-full">
            <div class="rounded-xl bg-card py-4 sm:px-6 px-5 flex items-center shadow-xl">
                <div class="px-4 text-lg text-primary border-r tracking-wider">
                    @yield('code')
                </div>

                <div class="ml-4 text-lg text-muted-foreground uppercase tracking-wider">
                    @yield('message')
                </div>
            </div>
        </div>
    </body>
</html>
