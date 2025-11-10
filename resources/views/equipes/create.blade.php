<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Criar Nova Equipe') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('equipes.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="nome" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nome da Equipe</label>
                        <input type="text" name="nome" id="nome" 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600" 
                               value="{{ old('nome') }}" required autofocus>
                        @error('nome')
                            <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Você pode adicionar mais campos aqui se precisar -->

                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.equipes.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                            Voltar
                        </a>

                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Criar Equipe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
