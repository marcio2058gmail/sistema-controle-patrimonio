<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Dark mode: aplica classe antes do render para evitar flash -->
        <script>
            (function() {
                const saved = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (saved === 'dark' || (!saved && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
        <script>
            function toggleTheme() {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateThemeIcons(isDark);
            }

            function updateThemeIcons(isDark) {
                // Desktop
                document.getElementById('icon-sun')?.classList.toggle('hidden', !isDark);
                document.getElementById('icon-moon')?.classList.toggle('hidden', isDark);
                // Mobile
                document.getElementById('icon-sun-mobile')?.classList.toggle('hidden', !isDark);
                document.getElementById('icon-moon-mobile')?.classList.toggle('hidden', isDark);
                const lbl = document.getElementById('theme-label-mobile');
                if (lbl) lbl.textContent = isDark ? 'Modo claro' : 'Modo escuro';
            }

            // Sincroniza ícones com o estado inicial (definido pelo script anti-flash)
            document.addEventListener('DOMContentLoaded', function () {
                updateThemeIcons(document.documentElement.classList.contains('dark'));
            });
        </script>
    </body>
</html>
