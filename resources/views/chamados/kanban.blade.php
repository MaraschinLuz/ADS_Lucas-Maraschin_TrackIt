<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kanban Técnico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if($alerta)
                <div class="rounded-md border border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-800 dark:bg-amber-900/40 dark:text-amber-100 px-4 py-3">
                    {{ $alerta }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">{{ __('Visão geral por status') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Arraste mentalmente as prioridades: os cartões estão agrupados por status para facilitar a priorização diária.') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('chamados.index') }}"
                               class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium shadow hover:bg-indigo-700 focus:outline-none focus:ring">
                                {{ __('Voltar à lista') }}
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        @if($isAdmin)
                            <form method="GET" class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                                <div>
                                    <label for="equipe_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Equipe visível') }}
                                    </label>
                                    <select id="equipe_id" name="equipe_id"
                                            class="mt-1 block w-60 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            onchange="this.form.submit()">
                                        <option value="">{{ __('Todas as equipes') }}</option>
                                        @foreach($equipes as $equipe)
                                            <option value="{{ $equipe->id }}" @selected($selectedEquipeId === $equipe->id)>
                                                {{ $equipe->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <noscript>
                                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">
                                        {{ __('Aplicar') }}
                                    </button>
                                </noscript>
                            </form>
                        @elseif(!$alerta && auth()->user()?->equipe?->nome)
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                {{ __('Equipe atual:') }}
                                <span class="font-semibold">{{ auth()->user()->equipe->nome }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        @foreach($columns as $column)
                            @php($cards = $kanbanData[$column['key']] ?? collect())
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 flex flex-col">
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $column['label'] }}</span>
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $column['badge'] }}">
                                            {{ $cards->count() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 px-3 py-4 space-y-3 min-h-[220px]">
                                    @forelse($cards as $card)
                                        @php
                                            $priorityColors = [
                                                'alta' => 'border-red-500 bg-red-50/50 dark:bg-red-900/30',
                                                'media' => 'border-amber-400 bg-amber-50/40 dark:bg-amber-900/30',
                                                'baixa' => 'border-emerald-500 bg-emerald-50/30 dark:bg-emerald-900/20',
                                            ];
                                            $priorityText = [
                                                'alta' => 'text-red-600 dark:text-red-300',
                                                'media' => 'text-amber-600 dark:text-amber-300',
                                                'baixa' => 'text-emerald-600 dark:text-emerald-300',
                                            ];
                                            $priorityBadges = [
                                                'alta' => 'bg-red-100 text-red-700',
                                                'media' => 'bg-amber-100 text-amber-700',
                                                'baixa' => 'bg-emerald-100 text-emerald-700',
                                            ];
                                            $priorityAccent = $priorityColors[$card->prioridade] ?? 'border-gray-300';
                                            $priorityTextClass = $priorityText[$card->prioridade] ?? 'text-gray-600 dark:text-gray-300';
                                            $priorityBadgeClass = $priorityBadges[$card->prioridade] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <article class="bg-white dark:bg-gray-800 border rounded-md p-3 shadow-sm transition shadow hover:shadow-md border-l-4 {{ $priorityAccent }}">
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                        #{{ $card->id }} — {{ \Illuminate\Support\Str::limit($card->titulo, 48) }}
                                                    </p>
                                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                                        {{ \Illuminate\Support\Str::limit($card->descricao, 90) }}
                                                    </p>
                                                    <p class="mt-1 text-xs font-semibold flex items-center gap-1 {{ $priorityTextClass }}">
                                                        <span aria-hidden="true">⚡</span>
                                                        {{ __('Prioridade:') }} {{ ucfirst($card->prioridade) }}
                                                    </p>
                                                </div>
                                                <span class="px-2 py-0.5 text-xs rounded-full font-semibold tracking-wide {{ $priorityBadgeClass }}">
                                                    {{ ucfirst($card->prioridade) }}
                                                </span>
                                            </div>
                                            <dl class="mt-3 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                                <div class="flex justify-between">
                                                    <dt>{{ __('Solicitante') }}</dt>
                                                    <dd class="text-gray-700 dark:text-gray-200">{{ $card->user->name ?? '-' }}</dd>
                                                </div>
                                                <div class="flex justify-between">
                                                    <dt>{{ __('Equipe') }}</dt>
                                                    <dd class="text-gray-700 dark:text-gray-200">{{ $card->equipe->nome ?? '-' }}</dd>
                                                </div>
                                                <div class="flex justify-between">
                                                    <dt>{{ __('Atualizado em') }}</dt>
                                                    <dd>{{ optional($card->updated_at)->format('d/m H:i') }}</dd>
                                                </div>
                                            </dl>
                                            <div class="mt-3 flex justify-end">
                                                <a href="{{ route('chamados.show', $card) }}"
                                                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                    {{ __('Abrir chamado') }}
                                                </a>
                                            </div>
                                        </article>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Nenhum chamado neste status.') }}
                                        </p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
