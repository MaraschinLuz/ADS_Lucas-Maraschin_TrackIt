<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin · Criar Usuário Técnico') }}
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
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.usuarios.tecnico.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nome')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('E-mail')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Senha')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                        </div>

                        <div>
                            <x-input-label for="equipe_id" :value="__('Equipe (opcional)')" />
                            <select id="equipe_id" name="equipe_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">— Sem equipe —</option>
                                @foreach($equipes as $eq)
                                    <option value="{{ $eq->id }}" @selected(old('equipe_id') == $eq->id)>{{ $eq->nome }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('equipe_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <x-secondary-button onclick="history.back()">{{ __('Voltar') }}</x-secondary-button>
                            <x-primary-button>{{ __('Criar Técnico') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.usuarios.role.edit', auth()->user()) }}" class="text-sm text-indigo-600 dark:text-indigo-300 hover:underline">
                    {{ __('Editar papéis (exemplo de atalho)') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
