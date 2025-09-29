<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1>Olá, {{ Auth::user()->name }}!</h1>
                    <p class="mt-2">Bem-vindo ao painel de controle.</p>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('chamados.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-md focus:outline-none focus:shadow-outline">
                            Criar Novo Chamado
                        </a>
                        <a href="{{ route('chamados.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-md focus:outline-none focus:shadow-outline">
                            Ver Meus Chamados
                        </a>
                        <a href="{{ route('profile.edit') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-4 px-6 rounded-md focus:outline-none focus:shadow-outline">
                            Editar Perfil
                        </a>

                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('equipes.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-md focus:outline-none focus:shadow-outline">
                                Criar Nova Equipe
                            </a>
                            <a href="{{ route('equipes.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-md focus:outline-none focus:shadow-outline">
                                Ver Equipes
                            </a>
                            <a href="{{ route('logs.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-4 px-6 rounded-md focus:outline-none focus:shadow-outline">
                                Ver Histórico de Ações
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>