{{-- Sidebar --}}
<aside
    id="app-sidebar"
    @mouseenter="if(sidebarCollapsed) sidebarHovered = true"
    @mouseleave="sidebarHovered = false"
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full': !sidebarOpen,
        'sm:translate-x-0': true,
        'w-64': !sidebarCollapsed || sidebarHovered,
        'w-16': sidebarCollapsed && !sidebarHovered,
        'shadow-2xl': sidebarCollapsed && sidebarHovered
    }"
    class="fixed inset-y-0 left-0 z-30 bg-gray-900 flex flex-col w-64 duration-200 ease-in-out"
>
    {{-- Logo + botão colapso --}}
    <div class="flex items-center justify-between h-16 border-b border-gray-700 shrink-0 overflow-hidden"
         :class="(sidebarCollapsed && !sidebarHovered) ? 'px-1' : 'px-3'">
        <a href="{{ auth()->user()->isAdmin() ? route('dashboard') : route('tickets.index') }}"
           class="flex items-center gap-2 overflow-hidden">
            {{-- Ícone: visível apenas colapsado sem hover --}}
            <img src="{{ asset('images/logo-icon.png') }}" alt="LocarMais"
                 x-show="sidebarCollapsed && !sidebarHovered"
                 class="h-8 w-auto shrink-0 duration-200">
            {{-- Logo completa: visível expandido ou em hover --}}
            <img id="app-logo"
                 src="{{ asset('images/logo-full.png') }}" alt="LocarMais"
                 x-show="!sidebarCollapsed || sidebarHovered"
                 class="h-8 w-auto shrink-0 duration-200">
        </a>

        {{-- Botão colapso (desktop) --}}
        <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="hidden sm:flex shrink-0 p-1 rounded text-gray-500 hover:text-white hover:bg-gray-800 transition-colors duration-150">
            <svg class="h-4 w-4 transition-transform duration-200" :class="(sidebarCollapsed && !sidebarHovered) ? 'rotate-180' : ''"
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

        {{-- Indicador de empresa ativa --}}
        @php $empresaAtiva = auth()->user()->activeCompany(); @endphp
        @if($empresaAtiva || auth()->user()->isSuperAdmin())
        <div x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity
             class="mx-1 mb-2 px-3 py-2 rounded-lg bg-gray-800 border border-gray-700">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium mb-0.5">Empresa</p>
            <p class="text-xs text-gray-200 font-semibold truncate">
                {{ $empresaAtiva?->nome ?? 'Todas as empresas' }}
            </p>
            @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('companies.select') }}" class="text-xs text-purple-400 hover:text-purple-300 transition mt-0.5 inline-block">
                Trocar empresa →
            </a>
            @elseif(auth()->user()->empresas()->where('ativa', true)->count() > 1)
            <a href="{{ route('companies.select') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition mt-0.5 inline-block">
                Trocar empresa →
            </a>
            @endif
        </div>
        @endif

        {{-- Dashboard (somente admin) --}}
        @if(auth()->user()->isAdmin())
        <a href="{{ route('dashboard') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Dashboard' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Dashboard</span>
        </a>
        @endif

        {{-- Dashboards Analíticos --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
        <div class="pt-2 pb-1" x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity>
            <p class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Dashboards</p>
        </div>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('dashboards.global') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Global' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('dashboards.global') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Global</span>
        </a>
        @endif
        <a href="{{ route('dashboards.empresa') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Por Empresa' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('dashboards.empresa') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Por Empresa</span>
        </a>
        <a href="{{ route('dashboards.distribuicao') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Distribuição' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('dashboards.distribuicao') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Distribuição</span>
        </a>
        <a href="{{ route('dashboards.ciclovida') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Ciclo de Vida' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('dashboards.ciclovida') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Ciclo de Vida</span>
        </a>
        <a href="{{ route('dashboards.manutencao') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Manutenções' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('dashboards.manutencao') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Manutenções</span>
        </a>
        @endif

        {{-- Chamados --}}
        <a href="{{ route('tickets.index') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Chamados' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('tickets.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Chamados</span>
        </a>

        @if(auth()->user()->isEmployee() || auth()->user()->isManager())
        {{-- Meus Termos (funcionário ou gestor com equipamentos) --}}
        <a href="{{ route('responsibilities.index') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Meus Termos' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('responsibilities.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Meus Termos</span>
        </a>
        @endif

        @if(auth()->user()->isAdminOrManager() || auth()->user()->isEmployee())

        {{-- Patrimônios --}}
        <a href="{{ route('assets.index') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Patrimônios' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('assets.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Patrimônios</span>
        </a>

        {{-- Manutenções (apenas admin) --}}
        @if(auth()->user()->isAdmin())
        <a href="{{ route('manutencoes.index') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Manutenções' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('manutencoes.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 4a7 7 0 100 14A7 7 0 0011 4zM21 21l-4.35-4.35M15.5 9.5a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Manutenções</span>
        </a>
        @endif

        @endif

        @if(auth()->user()->isAdminOrManager())

        {{-- Separador --}}
        <div class="pt-3 pb-1 nav-label" x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity>
            <p class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestão</p>
        </div>
        <div class="pt-3 nav-sep-collapsed" x-show="sidebarCollapsed && !sidebarHovered" x-transition.opacity>
            <hr class="border-gray-700">
        </div>

        {{-- Departamentos (somente admin) --}}
        @if(auth()->user()->isAdmin())
        <a href="{{ route('departments.index') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Departamentos' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('departments.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Departamentos</span>
        </a>
        @endif

        {{-- Responsabilidades (somente admin) --}}
        @if(auth()->user()->isAdmin())
        <a href="{{ route('responsibilities.index') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Responsabilidades' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('responsibilities.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Responsabilidades</span>
        </a>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
        {{-- Usuários --}}
        <a href="{{ route('users.index') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Usuários' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('users.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Usuários</span>
        </a>
        @endif

        {{-- Empresas (super_admin) --}}
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('companies.index') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Empresas' : ''"
            class="{{ $linkBase }} {{ request()->routeIs('companies.*') ? $active : $inactive }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate nav-label">Empresas</span>
        </a>
        @endif

        @endif

    </nav>

    {{-- Rodapé --}}
    <div class="border-t border-gray-700 px-2 py-2 shrink-0 space-y-0.5 overflow-hidden">

        {{-- Toggle dark mode --}}
        <button onclick="toggleTheme()"
            :title="(sidebarCollapsed && !sidebarHovered) ? 'Alternar tema' : ''"
            class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition-colors duration-150">
            <svg id="icon-sun" class="hidden h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-9H21M3 12H2m15.36-6.36-.71.71M6.34 17.66l-.71.71M17.66 17.66l.71.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
            </svg>
            <svg id="icon-moon" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity id="theme-label-mobile" class="nav-label">Modo escuro</span>
        </button>

        {{-- Perfil --}}
        <a href="{{ route('profile.edit') }}"
            :title="(sidebarCollapsed && !sidebarHovered) ? '{{ Auth::user()->name }}' : ''"
            class="{{ $linkBase }} text-gray-400 hover:bg-gray-800 hover:text-white">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="truncate min-w-0 nav-label">
                <span class="block truncate">{{ Auth::user()->name }}</span>
                <span class="block text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</span>
            </span>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                :title="(sidebarCollapsed && !sidebarHovered) ? 'Sair' : ''"
                class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-400 hover:bg-red-900/40 hover:text-red-400 transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span x-show="!sidebarCollapsed || sidebarHovered" x-transition.opacity class="nav-label">Sair</span>
            </button>
        </form>

    </div>
</aside>

{{-- Espaçador reativo para compensar a sidebar no desktop --}}
<div id="app-spacer"
     class="hidden sm:block shrink-0 w-64 duration-200"
     :class="sidebarCollapsed ? 'w-16' : 'w-64'"></div>
