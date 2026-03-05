<nav x-data="{ open: false }" class="bg-gray-900 border-b border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="https://app.locarmais.com/consImages/escuro.png"
                             alt="Controle Patrimonial"
                             class="h-8 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.*')">
                        Chamados
                    </x-nav-link>

                    @if(auth()->user()->isAdminOrManager())
                    @if(auth()->user()->isAdmin())
                        <x-nav-link :href="route('assets.index')" :active="request()->routeIs('assets.*')">
                            Patrimônios
                        </x-nav-link>
                    @elseif(auth()->user()->isManager())
                        <x-nav-link :href="route('assets.index')" :active="request()->routeIs('assets.*')">
                            Patrimônios
                        </x-nav-link>
                    @endif

                        <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                            Funcionários
                        </x-nav-link>

                        <x-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')">
                            Departamentos
                        </x-nav-link>

                        <x-nav-link :href="route('responsibilities.index')" :active="request()->routeIs('responsibilities.*')">
                            Responsabilidades
                        </x-nav-link>

                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('managers.index')" :active="request()->routeIs('managers.*')">
                                Gestores
                            </x-nav-link>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Dark Mode Toggle -->
            <div class="hidden sm:flex sm:items-center sm:ms-3">
                <button
                    id="theme-toggle"
                    onclick="toggleTheme()"
                    title="Alternar tema"
                    class="p-2 rounded-lg text-gray-400 hover:bg-gray-700 focus:outline-none transition-colors duration-150"
                >
                    <!-- Sol (aparece no dark) -->
                    <svg id="icon-sun" class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-9H21M3 12H2m15.36-6.36-.71.71M6.34 17.66l-.71.71M17.66 17.66l.71.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z" />
                    </svg>
                    <!-- Lua (aparece no light) -->
                    <svg id="icon-moon" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                </button>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-900 hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-gray-400 dark:text-gray-500">
                            Perfil: <span class="font-semibold capitalize">{{ auth()->user()->role }}</span>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-200 hover:bg-gray-700 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.*')">
                Chamados
            </x-responsive-nav-link>

            @if(auth()->user()->isAdminOrManager())
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('assets.index')" :active="request()->routeIs('assets.*')">
                        Patrimônios
                    </x-responsive-nav-link>
                @elseif(auth()->user()->isManager())
                    <x-responsive-nav-link :href="route('assets.index')" :active="request()->routeIs('assets.*')">
                        Patrimônios
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                    Funcionários
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')">
                    Departamentos
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('responsibilities.index')" :active="request()->routeIs('responsibilities.*')">
                    Responsabilidades
                </x-responsive-nav-link>

                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('managers.index')" :active="request()->routeIs('managers.*')">
                        Gestores
                    </x-responsive-nav-link>
                @endif
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Dark Mode Toggle Mobile -->
                <button
                    onclick="toggleTheme()"
                    class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 flex items-center gap-2"
                >
                    <svg id="icon-sun-mobile" class="hidden h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-9H21M3 12H2m15.36-6.36-.71.71M6.34 17.66l-.71.71M17.66 17.66l.71.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z" />
                    </svg>
                    <svg id="icon-moon-mobile" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                    <span id="theme-label-mobile">Modo escuro</span>
                </button>
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
