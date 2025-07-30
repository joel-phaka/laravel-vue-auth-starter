<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.bunny.net">

        <!-- Scripts -->
        <script>
            if (!['dark', 'light'].includes(localStorage.getItem('themeMode'))) {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const themeMode = prefersDark ? 'dark' : 'light';

                localStorage.setItem('themeMode', themeMode);
            }

            if (localStorage.getItem('themeMode') === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        </script>
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body>
        <div id="app">
        </div>
    </body>
</html>
