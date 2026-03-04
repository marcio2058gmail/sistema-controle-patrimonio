<x-guest-layout>
<div class="min-h-screen flex">

    {{-- Painel esquerdo — identidade visual --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative overflow-hidden"
         style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4338ca 75%, #6366f1 100%);">

        {{-- Círculos decorativos --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full opacity-10"
             style="background: radial-gradient(circle, #a5b4fc, transparent)"></div>
        <div class="absolute top-1/2 -right-32 w-80 h-80 rounded-full opacity-10"
             style="background: radial-gradient(circle, #c7d2fe, transparent)"></div>
        <div class="absolute -bottom-20 left-1/4 w-64 h-64 rounded-full opacity-10"
             style="background: radial-gradient(circle, #818cf8, transparent)"></div>

        <div class="relative z-10 flex flex-col justify-between p-12 w-full">

            {{-- Logo e nome --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 7h18M3 7l2-2h14l2 2M3 7v12a1 1 0 001 1h16a1 1 0 001-1V7M9 11h6m-6 4h6" />
                    </svg>
                </div>
                <span class="text-white font-semibold text-lg tracking-wide">Controle Patrimonial</span>
            </div>

            {{-- Conteúdo central --}}
            <div class="space-y-8">
                <div class="space-y-4">
                    <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight">
                        Gerencie seu<br>
                        <span class="text-indigo-300">patrimônio</span><br>
                        com eficiência
                    </h1>
                    <p class="text-indigo-200 text-lg leading-relaxed max-w-sm">
                        Controle total sobre os bens da sua organização, responsabilidades e chamados em um só lugar.
                    </p>
                </div>

                {{-- Recursos em destaque --}}
                <div class="space-y-4">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',       'text' => 'Gestão completa de patrimônios'],
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'text' => 'Controle por departamento'],
                        ['icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'text' => 'Termos de responsabilidade em PDF'],
                    ] as $item)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white bg-opacity-15 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                            </svg>
                        </div>
                        <span class="text-indigo-100 text-sm">{{ $item['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Rodapé do painel --}}
            <p class="text-indigo-400 text-xs">
                &copy; {{ date('Y') }} Controle Patrimonial. Todos os direitos reservados.
            </p>
        </div>
    </div>

    {{-- Painel direito — formulário --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-white dark:bg-gray-950">
        <div class="w-full max-w-md space-y-8">

            {{-- Cabeçalho mobile (visível apenas em telas pequenas) --}}
            <div class="lg:hidden text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-600 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 7h18M3 7l2-2h14l2 2M3 7v12a1 1 0 001 1h16a1 1 0 001-1V7M9 11h6m-6 4h6" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Controle Patrimonial</h2>
            </div>

            {{-- Título do formulário --}}
            <div class="space-y-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Bem-vindo de volta</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Entre com suas credenciais para acessar o sistema.</p>
            </div>

            {{-- Mensagem de sessão --}}
            <x-auth-session-status :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- E-mail --}}
                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        E-mail
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}"
                               required autofocus autocomplete="username"
                               placeholder="seu@email.com"
                               class="block w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 dark:border-gray-700 rounded-lg
                                      bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                      transition duration-150 @error('email') border-red-400 focus:ring-red-400 @enderror" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                {{-- Senha --}}
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Senha
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 font-medium">
                                Esqueceu a senha?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password"
                               required autocomplete="current-password"
                               placeholder="••••••••"
                               class="block w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 dark:border-gray-700 rounded-lg
                                      bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                      transition duration-150 @error('password') border-red-400 focus:ring-red-400 @enderror" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                {{-- Lembrar-me --}}
                <div class="flex items-center gap-2">
                    <input id="remember_me" name="remember" type="checkbox"
                           class="rounded border-gray-300 dark:border-gray-600 text-indigo-600
                                  focus:ring-indigo-500 dark:bg-gray-900 w-4 h-4" />
                    <label for="remember_me" class="text-sm text-gray-600 dark:text-gray-400 select-none cursor-pointer">
                        Lembrar-me neste dispositivo
                    </label>
                </div>

                {{-- Botão --}}
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white
                               bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800
                               rounded-lg shadow-sm transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Entrar no sistema
                </button>
            </form>

        </div>
    </div>

</div>
</x-guest-layout>
