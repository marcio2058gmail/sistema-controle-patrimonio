{{-- Sidebar --}}
<aside
    id="app-sidebar"
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full': !sidebarOpen,
        'sm:translate-x-0': true,
        'w-64': !sidebarCollapsed,
        'w-16': sidebarCollapsed
    }"
    class="fixed inset-y-0 left-0 z-30 bg-gray-900 flex flex-col w-64 duration-200 ease-in-out"
>
    {{-- Logo + botão colapso --}}
    <div class="flex items-center h-16 px-3 border-b border-gray-700 shrink-0 overflow-hidden">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0">
            <img id="app-logo"
                 src="https://app.locarmais.com/consImages/escuro.png" alt="LocarMais"
                 :class="sidebarCollapsed ? 'h-7' : 'h-8'"
                 class="w-auto shrink-0 h-8 duration-200">
        </a>

        {{-- Botão colapso (desktop) --}}
        <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="hidden sm:flex ml-auto shrink-0 p-1 rounded text-gray-500 hover:text-white hover:bg-gray-800 transition-colors duration-150">
            <svg class="h-4 w-4 transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>

        {{-- Fechar (mobile) --}}
        <button @click="sidebarOpen = false" class="sm:hidden ml-auto text-gray-400 hover:text-white">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Nav links --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-2 py-3 space-y-0.5">

        @php
        $linkBase = 'group flex items-center gap-3 px-2 py-2 rounded-lg text-sm font-medium transition-colors duration-150';
        $active   = 'bg-indigo-600 text-white';
        $inactive = 'text-gray-400 hover:bg-gray-800 hover:text-white';
        @endphp

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            :title="sidebarCollapsed ? 'Dashboard' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate nav-label">Dashboard</span>
        </a>

        {{-- Chamados --}}
        <a href="{{ route('tickets.index') }}"
            :title="sidebarCollapsed ? 'Chamados' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('tickets.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate nav-label">Chamados</span>
        </a>

        @if(auth()->user()->isAdminOrManager())

        {{-- Separador --}}
        <div class="pt-3 pb-1 nav-label" x-show="!sidebarCollapsed" x-transition.opacity>
            <p class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestão</p>
        </div>
        <div class="pt-3 nav-sep-collapsed" x-show="sidebarCollapsed" x-transition.opacity>
            <hr class="border-gray-700">
        </div>

        {{-- Patrimônios --}}
        <a href="{{ route('assets.index') }}"
            :title="sidebarCollapsed ? 'Patrimônios' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('assets.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate nav-label">Patrimônios</span>
        </a>

        {{-- Funcionários --}}
        <a href="{{ route('employees.index') }}"
            :title="sidebarCollapsed ? 'Funcionários' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('employees.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate nav-label">Funcionários</span>
        </a>

        {{-- Departamentos --}}
        <a href="{{ route('departments.index') }}"
            :title="sidebarCollapsed ? 'Departamentos' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('departments.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate nav-label">Departamentos</span>
        </a>

        {{-- Responsabilidades --}}
        <a href="{{ route('responsibilities.index') }}"
            :title="sidebarCollapsed ? 'Responsabilidades' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('responsibilities.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate nav-label">Responsabilidades</span>
        </a>

        @if(auth()->user()->isAdmin())
        {{-- Gestores --}}
        <a href="{{ route('managers.index') }}"
            :title="sidebarCollapsed ? 'Gestores' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('managers.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate nav-label">Gestores</span>
        </a>
        @endif

        @endif

    </nav>

    {{-- Rodapé --}}
    <div class="border-t border-gray-700 px-2 py-2 shrink-0 space-y-0.5 overflow-hidden">

        {{-- Toggle dark mode --}}
        <button onclick="toggleTheme()"
            :title="sidebarCollapsed ? 'Alternar tema' : ''"
            class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition-colors duration-150">
            <svg id="icon-sun" class="hidden h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-9H21M3 12H2m15.36-6.36-.71.71M6.34 17.66l-.71.71M17.66 17.66l.71.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
            </svg>
            <svg id="icon-moon" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity id="theme-label-mobile" class="nav-label">Modo escuro</span>
        </button>

        {{-- Perfil --}}
        <a href="{{ route('profile.edit') }}"
            :title="sidebarCollapsed ? '{{ Auth::user()->name }}' : ''"
            class="{{ $linkBase }} text-gray-400 hover:bg-gray-800 hover:text-white">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate min-w-0 nav-label">
                <span class="block truncate">{{ Auth::user()->name }}</span>
                <span class="block text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</span>
            </span>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                :title="sidebarCollapsed ? 'Sair' : ''"
                class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-400 hover:bg-red-900/40 hover:text-red-400 transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity class="nav-label">Sair</span>
            </button>
        </form>

    </div>
</aside>

{{-- Espaçador reativo para compensar a sidebar no desktop --}}
<div id="app-spacer"
     class="hidden sm:block shrink-0 w-64 duration-200"
     :class="sidebarCollapsed ? 'w-16' : 'w-64'"></div>
