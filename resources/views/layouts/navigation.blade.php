<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('img/trackit.png') }}" alt="TrackIt Logo" class="block h-16 w-auto transition-transform duration-300 ease-in-out hover:scale-110 hover:shadow-lg" />
                    </a>
                </div>

                <!-- Navigation Links (desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('chamados.index')" :active="request()->routeIs('chamados.*')">
                        {{ __('Chamados') }}
                    </x-nav-link>

                    @auth
                        @if(auth()->user()->equipe_id)
                            <x-nav-link :href="route('equipe.membros')" :active="request()->routeIs('equipe.membros')">
                                {{ __('Membros') }}
                            </x-nav-link>
                            <x-nav-link :href="route('equipe.chat')" :active="request()->routeIs('equipe.chat')">
                                {{ __('Chat da Equipe') }}
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 rounded-md text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100">
                                        <span>Admin</span>
                                        <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4-4a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.equipes.index')">{{ __('Equipes') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.usuarios.index')">{{ __('Usuários') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.usuarios.tecnico.create')">{{ __('Criar Técnico') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('logs.index')">{{ __('Histórico de Ações') }}</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Notifications Dropdown -->
                <x-dropdown align="right" width="80">
                    <x-slot name="trigger">
                        <button id="notif-bell" class="relative inline-flex items-center px-3 py-2 rounded-md text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path d="M5.25 8.25a6.75 6.75 0 1113.5 0v3.318c0 .877.352 1.718.977 2.337l.307.304c.69.683.202 1.841-.76 1.841H4.726c-.963 0-1.45-1.158-.76-1.841l.307-.304c.625-.62.977-1.46.977-2.337V8.25z" />
                                <path d="M9 18.75a3 3 0 006 0H9z" />
                            </svg>
                            <span id="notif-count" class="absolute -top-1 -right-1 hidden rounded-full bg-red-600 text-white text-xs leading-none px-1.5 py-0.5"></span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="p-2 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Notificações</div>
                            <form method="POST" action="{{ route('notifications.read_all') }}">
                                @csrf
                                <button class="text-xs text-indigo-600 dark:text-indigo-300 hover:underline">Marcar todas como lidas</button>
                            </form>
                        </div>
                        <div id="notif-list" class="max-h-80 overflow-auto p-2 text-sm text-gray-700 dark:text-gray-200">
                            <div class="text-gray-500 dark:text-gray-400">Carregando...</div>
                        </div>
                    </x-slot>
                </x-dropdown>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('chamados.index')" :active="request()->routeIs('chamados.*')">
                {{ __('Chamados') }}
            </x-responsive-nav-link>

            @auth
                @if(auth()->user()->equipe_id)
                    <x-responsive-nav-link :href="route('equipe.membros')" :active="request()->routeIs('equipe.membros')">
                        {{ __('Membros') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('equipe.chat')" :active="request()->routeIs('equipe.chat')">
                        {{ __('Chat da Equipe') }}
                    </x-responsive-nav-link>
                @endif
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.equipes.index')" :active="request()->routeIs('admin.equipes.*')">
                        {{ __('Equipes') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.usuarios.tecnico.create')" :active="request()->routeIs('admin.usuarios.tecnico.*')">
                        {{ __('Criar Técnico') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.usuarios.index')" :active="request()->routeIs('admin.usuarios.index')">
                        {{ __('Usuários') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('logs.index')" :active="request()->routeIs('logs.*')">
                        {{ __('Histórico de Ações') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
