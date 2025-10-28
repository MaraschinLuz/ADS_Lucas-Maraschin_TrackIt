<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gerir Equipes e Técnicos') }}
            </h2>
            <a href="{{ route('equipes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Nova Equipe
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 dark:border-green-700 dark:bg-green-900/40 dark:text-green-200 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 dark:border-red-700 dark:bg-red-900/40 dark:text-red-200 px-4 py-3">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Técnicos sem equipe --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Técnicos sem equipe</h3>
                </div>
                <div class="p-6">
                    @if($tecnicosSemEquipe->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">Nenhum técnico sem equipe.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nome</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">E-mail</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Atribuir</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($tecnicosSemEquipe as $tec)
                                        <tr>
                                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $tec->name }}</td>
                                            <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $tec->email }}</td>
                                            <td class="px-4 py-2 text-right">
                                                <form method="POST" action="{{ route('admin.equipes.atribuir') }}" class="inline-flex items-center gap-2">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $tec->id }}">
                                                    <select name="equipe_id" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500" required>
                                                        <option value="">Selecione...</option>
                                                        @foreach($todasEquipes as $eq)
                                                            <option value="{{ $eq->id }}">{{ $eq->nome }}</option>
                                                        @endforeach
                                                    </select>
                                                    <x-primary-button>Atribuir</x-primary-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Equipes com seus técnicos --}}
            @forelse($equipes as $equipe)
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $equipe->nome }}</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">ID #{{ $equipe->id }}</span>
                    </div>
                    <div class="p-6">
                        @if($equipe->users->isEmpty())
                            <p class="text-gray-600 dark:text-gray-300">Nenhum técnico nesta equipe.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nome</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">E-mail</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($equipe->users as $tec)
                                            <tr>
                                                <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $tec->name }}</td>
                                                <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $tec->email }}</td>
                                                <td class="px-4 py-2 text-right">
                                                    {{-- Remover da equipe --}}
                                                    <form method="POST" action="{{ route('admin.equipes.remover') }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="user_id" value="{{ $tec->id }}">
                                                        <x-secondary-button
                                                            onclick="return confirm('Remover {{ $tec->name }} desta equipe?')">
                                                            Remover
                                                        </x-secondary-button>
                                                    </form>

                                                    {{-- Mover para outra equipe --}}
                                                    <form method="POST" action="{{ route('admin.equipes.atribuir') }}" class="inline-flex items-center gap-2 ml-2">
                                                        @csrf
                                                        <input type="hidden" name="user_id" value="{{ $tec->id }}">
                                                        <select name="equipe_id" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500" required>
                                                            <option value="">Mover para...</option>
                                                            @foreach($todasEquipes as $eq)
                                                                @if($eq->id !== $equipe->id)
                                                                    <option value="{{ $eq->id }}">{{ $eq->nome }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <x-primary-button>Mover</x-primary-button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <p class="text-gray-600 dark:text-gray-300">Nenhuma equipe cadastrada.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
