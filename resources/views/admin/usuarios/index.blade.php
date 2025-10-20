<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Admin · Usuários') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.usuarios.tecnico.create') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    {{ __('Criar Técnico') }}
                </a>
                <a href="{{ route('admin.equipes.index') }}"
                   class="inline-flex items-center rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
                    {{ __('Gerir Equipes') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 dark:border-green-700 dark:bg-green-900/40 dark:text-green-200 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filtros --}}
            <form method="GET" class="mb-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <x-input-label for="q" :value="__('Busca (nome ou e-mail)')" />
                        <x-text-input id="q" name="q" type="text" class="mt-1 block w-full"
                                      :value="$q" placeholder="Ex.: Maria ou maria@..." />
                    </div>
                    <div>
                        <x-input-label for="role" :value="__('Papel')" />
                        <select id="role" name="role"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Todos —</option>
                            @foreach($roles as $k=>$label)
                                <option value="{{ $k }}" @selected($role===$k)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="sem_equipe" value="1" @checked($semEqp)
                                   class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 dark:text-gray-200">Somente técnicos sem equipe</span>
                        </label>
                    </div>
                    <div class="flex items-end justify-end gap-2">
                        <a href="{{ route('admin.usuarios.index') }}"
                           class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
                            Limpar
                        </a>
                        <x-primary-button>Filtrar</x-primary-button>
                    </div>
                </div>
            </form>

            {{-- Tabela --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">E-mail</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Papel</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Equipe</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users as $u)
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $u->name }}</td>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $u->email }}</td>

                                    {{-- Papel inline --}}
                                    <td class="px-4 py-2">
                                        <form method="POST" action="{{ route('admin.usuarios.role.update', $u) }}" class="inline-flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="equipe_id" value="{{ $u->equipe_id }}">
                                            <select name="role"
                                                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                                                onchange="this.form.submit()">
                                                @foreach($roles as $k=>$label)
                                                    <option value="{{ $k }}" @selected($u->role===$k)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>

                                    {{-- Equipe inline (habilita só se papel = técnica) --}}
                                    <td class="px-4 py-2">
                                        <form method="POST" action="{{ route('admin.usuarios.role.update', $u) }}" class="inline-flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="role" value="{{ $u->role }}">
                                            <select name="equipe_id"
                                                @disabled($u->role!=='tecnica')
                                                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                                                onchange="this.form.submit()">
                                                <option value="">— Sem equipe —</option>
                                                @foreach($equipes as $eq)
                                                    <option value="{{ $eq->id }}" @selected($u->equipe_id==$eq->id)>{{ $eq->nome }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>

                                    <td class="px-4 py-2 text-right">
                                        <a href="{{ route('admin.usuarios.role.edit', $u) }}"
                                           class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
                                            Detalhar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-600 dark:text-gray-300">
                                        Nenhum usuário encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginação --}}
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
