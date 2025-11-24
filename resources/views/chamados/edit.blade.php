<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Chamado') }} #{{ $chamado->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                
                <form method="POST" action="{{ route('chamados.update', $chamado) }}">
                    @csrf
                    @method('PUT') 

                    <div class="mb-4">
                        <label for="titulo"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Título</label>
                        <input type="text" name="titulo" id="titulo"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600"
                            value="{{ old('titulo', $chamado->titulo) }}" required autofocus>
                        @error('titulo')
                            <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="descricao"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Descrição</label>
                        <textarea name="descricao" id="descricao" rows="5"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600"
                            required>{{ old('descricao', $chamado->descricao) }}</textarea>
                        @error('descricao')
                            <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="prioridade"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Prioridade</label>
                        <select name="prioridade" id="prioridade"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600"
                            required @disabled(!Auth::user()->can('updatePriorityAndStatus', $chamado)) >
                            <option value="baixa" {{ old('prioridade', $chamado->prioridade) == 'baixa' ? 'selected' : '' }}>Baixa</option>
                            <option value="media" {{ old('prioridade', $chamado->prioridade) == 'media' ? 'selected' : '' }}>Média</option>
                            <option value="alta" {{ old('prioridade', $chamado->prioridade) == 'alta' ? 'selected' : '' }}>Alta</option>
                        </select>
                        @error('prioridade')
                            <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="equipe_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Equipe Responsável</label>
                        <select name="equipe_id" id="equipe_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600"
                                @disabled(!Auth::user()->can('updatePriorityAndStatus', $chamado))>
                            <option value="">Selecione uma equipe</option>
                            @foreach ($equipes as $equipe)
                            <option value="{{ $equipe->id }}"
                                {{ old('equipe_id', $chamado->equipe_id) == $equipe->id ? 'selected' : '' }}>
                                {{ $equipe->nome }}
                            </option>
                            @endforeach
                            </select>
                             @error('equipe_id')
                                <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                            @enderror
                    </div>

                    
                    <div class="mb-4">
                        <label for="status"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Status</label>
                        <select name="status" id="status"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600"
                            required @disabled(!Auth::user()->can('updatePriorityAndStatus', $chamado)) >
                            <option value="aberto" {{ old('status', $chamado->status) == 'aberto' ? 'selected' : '' }}>
                                Aberto</option>
                            <option value="em andamento" {{ old('status', $chamado->status) == 'em andamento' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="resolvido" {{ old('status', $chamado->status) == 'resolvido' ? 'selected' : '' }}>Resolvido</option>
                            <option value="fechado" {{ old('status', $chamado->status) == 'fechado' ? 'selected' : '' }}>
                                Fechado</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('chamados.index') }}"
                            class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            {{ __('Atualizar Chamado') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>