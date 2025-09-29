<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin · Editar Papel do Usuário') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 dark:border-green-700 dark:bg-green-900/40 dark:text-green-200 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Nome</div>
                            <div class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">E-mail</div>
                            <div class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->email }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.usuarios.role.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="role" :value="__('Papel')" />
                            <select id="role" name="role"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="usuario" @selected($user->role==='usuario')>Usuário</option>
                                <option value="tecnica" @selected($user->role==='tecnica')>Técnico</option>
                                <option value="admin"   @selected($user->role==='admin')>Administrador</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div x-data="{ isTec: '{{ $user->role }}' === 'tecnica' }"
                             x-init="$watch('isTec', v => { if(!v) document.getElementById('equipe_id').value='' })">
                            <x-input-label for="equipe_id" :value="__('Equipe (apenas para técnico)')" />
                            <select id="equipe_id" name="equipe_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                                x-bind:disabled="document.getElementById('role').value !== 'tecnica'">
                                <option value="">— Sem equipe —</option>
                                @foreach($equipes as $eq)
                                    <option value="{{ $eq->id }}" @selected($user->equipe_id == $eq->id)>{{ $eq->nome }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Se o papel não for <strong>Técnico</strong>, a equipe será desligada automaticamente.
                            </p>
                            <x-input-error :messages="$errors->get('equipe_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <x-secondary-button onclick="history.back()">{{ __('Voltar') }}</x-secondary-button>
                            <x-primary-button>{{ __('Salvar Alterações') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.usuarios.tecnico.create') }}" class="text-sm text-indigo-600 dark:text-indigo-300 hover:underline">
                    {{ __('Criar novo Técnico') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
