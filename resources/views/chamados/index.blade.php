<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Meus Chamados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @isset($alerta)
                        <div class="mb-4 rounded border border-yellow-200 bg-yellow-50 text-yellow-800 dark:border-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-200 px-4 py-3">
                            {{ $alerta }}
                        </div>
                    @endisset

                    <script>
                        // Aplica filtro client-side pelo parametro ?status=
                        (function () {
                            const params = new URLSearchParams(window.location.search);
                            const status = params.get('status');
                            if (!status) return;
                            document.querySelectorAll('tr[data-status]')?.forEach(tr => {
                                if ((tr.getAttribute('data-status') || '') !== status) {
                                    tr.style.display = 'none';
                                }
                            });
                        })();
                    </script>

                    @php
                        $user = auth()->user();
                        $validStatus = ['aberto', 'em andamento', 'resolvido', 'fechado'];
                        $activeStatus = in_array(request('status'), $validStatus, true) ? request('status') : null;
                        $statusCards = [
                            ['key' => 'aberto', 'label' => 'Abertos', 'color' => 'amber'],
                            ['key' => 'em andamento', 'label' => 'Em andamento', 'color' => 'blue'],
                            ['key' => 'resolvido', 'label' => 'Resolvidos', 'color' => 'emerald'],
                            ['key' => 'fechado', 'label' => 'Fechados', 'color' => 'gray'],
                        ];
                        if (!isset($counts) || !is_array($counts)) {
                            $query = \App\Models\Chamado::query();
                            if ($user->role === 'tecnica' && $user->equipe_id) {
                                $query->where('equipe_id', $user->equipe_id);
                            } elseif ($user->role !== 'admin') {
                                $query->where('user_id', $user->id);
                            }
                            $rawCounts = $query->selectRaw('status, COUNT(*) as total')
                                ->groupBy('status')
                                ->pluck('total', 'status')
                                ->toArray();
                            $counts = [
                                'aberto' => (int) ($rawCounts['aberto'] ?? 0),
                                'em andamento' => (int) ($rawCounts['em andamento'] ?? 0),
                                'resolvido' => (int) ($rawCounts['resolvido'] ?? 0),
                                'fechado' => (int) ($rawCounts['fechado'] ?? 0),
                            ];
                        }
                    @endphp

                    <div class="mb-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach ($statusCards as $card)
                            @php($isActive = $activeStatus === $card['key'])
                            <a href="{{ $isActive ? route('chamados.index') : route('chamados.index', ['status' => $card['key']]) }}"
                               class="rounded-lg p-3 shadow border {{ $isActive ? ('ring-2 ring-offset-1 ring-' . $card['color'] . '-500') : '' }} bg-white dark:bg-gray-800">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</div>
                                <div class="text-2xl font-bold text-{{ $card['color'] }}-600">{{ $counts[$card['key']] ?? 0 }}</div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
                        <a href="{{ route('chamados.index') }}"
                           class="px-3 py-1 rounded-full border {{ !$activeStatus ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : 'text-gray-600 dark:text-gray-300' }}">
                            Todos
                        </a>
                        @foreach ($statusCards as $card)
                            @php($isCurrent = $activeStatus === $card['key'])
                            <a href="{{ route('chamados.index', ['status' => $card['key']]) }}"
                               class="px-3 py-1 rounded-full border {{ $isCurrent ? 'bg-' . $card['color'] . '-100 text-' . $card['color'] . '-800 dark:bg-' . $card['color'] . '-900 dark:text-' . $card['color'] . '-300' : 'text-gray-600 dark:text-gray-300' }}">
                                {{ $card['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex justify-end mb-4">
                        <a href="{{ route('chamados.create') }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            {{ __('Abrir Novo Chamado') }}
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('ID') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Titulo') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Descricao') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Prioridade') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Status') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Equipe') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Criado em') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('SLA') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Acoes') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($chamados as $chamado)
                                    @php
                                        $priority = strtolower($chamado->prioridade ?? '');
                                        $priorityClasses = [
                                            'alta' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'media' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            'baixa' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        ];
                                        $priorityClass = $priorityClasses[$priority] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';

                                        $dueAt = $chamado->slaDueAt();
                                        $now = \Illuminate\Support\Carbon::now();
                                        $totalHours = max(1, (int) $chamado->slaHours());
                                        $spentHours = $chamado->created_at ? $chamado->created_at->diffInHours($now) : 0;
                                        $remainingHours = $totalHours - $spentHours;
                                        $slaState = $chamado->isFinalizado()
                                            ? 'done'
                                            : ($remainingHours <= 0
                                                ? 'over'
                                                : ($remainingHours <= ($totalHours * 0.2) ? 'warn' : 'ok'));
                                        $slaLabel = $chamado->isFinalizado()
                                            ? __('Concluido')
                                            : ($remainingHours <= 0 ? __('Vencido') : ($remainingHours . 'h'));
                                        $slaColor = [
                                            'done' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
                                            'over' => 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-300',
                                            'warn' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
                                            'ok' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        ][$slaState];
                                    @endphp

                                    <tr data-status="{{ $chamado ->status }}"
                                        class="odd:bg-gray-50 dark:odd:bg-gray-800/60 hover:bg-gray-100 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $chamado->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $chamado->titulo }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 truncate max-w-xs"
                                            title="{{ $chamado->descricao }}">
                                            {{ $chamado->descricao }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityClass }}">
                                                {{ ucfirst($chamado->prioridade) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-status-badge :value="$chamado->status" size="sm" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $chamado->equipe->nome ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ optional($chamado->created_at)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $slaColor }}"
                                                  title="{{ $dueAt ? __('Vence em :data', ['data' => $dueAt->format('d/m H:i')]) : '' }}">
                                                {{ $slaLabel }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                            @can('view', $chamado)
                                                <a href="{{ route('chamados.show', $chamado) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    {{ __('Ver') }}
                                                </a>
                                            @endcan

                                            @can('update', $chamado)
                                                <a href="{{ route('chamados.edit', $chamado) }}"
                                                   class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                    {{ __('Editar') }}
                                                </a>
                                            @endcan

                                            @can('delete', $chamado)
                                                <form action="{{ route('chamados.destroy', $chamado) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                            onclick="return confirm('Tem certeza que deseja excluir este chamado?')">
                                                        {{ __('Excluir') }}
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                            {{ __('Nenhum chamado encontrado.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $chamados->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
