<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="/favicon.ico">

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
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">

        {{-- Layout wrapper --}}
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

            {{-- Sidebar --}}
            @include('layouts.navigation')

            {{-- Overlay mobile --}}
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
                class="fixed inset-0 z-20 bg-black/50 sm:hidden"
                style="display:none"
            ></div>

            {{-- Main area --}}
            <div class="flex-1 flex flex-col min-w-0">

                {{-- Topbar mobile --}}
                <div class="sm:hidden flex items-center h-14 px-4 bg-gray-900 border-b border-gray-700">
                    <button @click="sidebarOpen = true" class="text-gray-400 hover:text-white mr-3">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <a href="{{ route('dashboard') }}">
                        <img src="https://app.locarmais.com/consImages/escuro.png" alt="LocarMais" class="h-7 w-auto">
                    </a>
                </div>

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow-sm">
                        <div class="px-6 py-4">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
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
