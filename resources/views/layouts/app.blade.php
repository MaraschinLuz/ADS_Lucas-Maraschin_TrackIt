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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white dark:from-gray-900 dark:to-gray-900">
            @include('layouts.navigation')

            {{-- Admin Toolbar: aparece apenas para administradores --}}
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="bg-indigo-50 dark:bg-indigo-900/40 border-b border-indigo-200/70 dark:border-indigo-800">
                        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div class="text-sm font-medium text-indigo-700 dark:text-indigo-200">
                                Você está logado como <span class="font-semibold">Administrador</span>.
                            </div>
                            <div class="flex flex-wrap gap-2">
                                {{-- Atalhos de administração (adicione mais quando criar as telas) --}}
                                <a href="{{ route('admin.equipes.index') }}"
                                   class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium
                                          bg-white text-indigo-700 ring-1 ring-inset ring-indigo-200 hover:bg-indigo-50
                                          dark:bg-indigo-800/60 dark:text-indigo-100 dark:hover:bg-indigo-800">
                                    Gerir Equipes
                                </a>
                                {{-- Exemplo de futuros atalhos:
                                <a href="{{ route('admin.usuarios.index') }}" class="inline-flex ...">Usuários</a>
                                <a href="{{ route('admin.config') }}" class="inline-flex ...">Configurações</a>
                                --}}
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

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
                @include('components.toast')
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </body>
</html>
