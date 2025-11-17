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

                    {{-- Avisos contextuais (ex.: tÃ©cnico sem equipe) --}}
                    @isset($alerta)
                        <div class="mb-4 rounded border border-yellow-200 bg-yellow-50 text-yellow-800 dark:border-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-200 px-4 py-3">
                            {{ $alerta }}
                        </div>
                    @endisset

                    <script>
                        // Aplica filtro client-side pelo parâmetro ?status=
                        (function() {
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
                    {{-- Resumo por status + filtros --}}
                    @php
                        $u = auth()->user();
                        $activeStatus = request('status');
                        $validStatus = ['aberto','em andamento','resolvido','fechado'];
                        if (!isset($counts)) {
                            $base = \App\Models\Chamado::query();
                            if ($u->role === 'tecnica' && $u->equipe_id) {
                                $base->where('equipe_id', $u->equipe_id);
                            } elseif ($u->role !== 'admin') {
                                $base->where('user_id', $u->id);
                            }
                            $rc = $base->selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c','status')->toArray();
                            $counts = [
                                'aberto' => (int)($rc['aberto'] ?? 0),
                                'em andamento' => (int)($rc['em andamento'] ?? 0),
                                'resolvido' => (int)($rc['resolvido'] ?? 0),
                                'fechado' => (int)($rc['fechado'] ?? 0),
                            ];
                        }
                        $cards = [
                            ['key'=>'aberto','label'=>'Abertos','color'=>'amber'],
                            ['key'=>'em andamento','label'=>'Em andamento','color'=>'blue'],
                            ['key'=>'resolvido','label'=>'Resolvidos','color'=>'emerald'],
                            ['key'=>'fechado','label'=>'Fechados','color'=>'gray'],
                        ];
                    @endphp

                    <div class="mb-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($cards as $c)
                            @php($isActive = $activeStatus === $c['key'])
                            <a href="{{ $isActive ? route('chamados.index') : route('chamados.index',['status'=>$c['key']]) }}"
                               class="rounded-lg p-3 shadow border @if($isActive) ring-2 ring-offset-1 ring-{{ $c['color'] }}-500 @endif bg-white dark:bg-gray-800">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $c['label'] }}</div>
                                <div class="text-2xl font-bold text-{{ $c['color'] }}-600">{{ $counts[$c['key']] ?? 0 }}</div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
                        <a href="{{ route('chamados.index') }}" class="px-3 py-1 rounded-full border {{ !$activeStatus ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : 'text-gray-600 dark:text-gray-300' }}">Todos</a>
                        @foreach($cards as $c)
                            <a href="{{ route('chamados.index',['status'=>$c['key']]) }}"
                               class="px-3 py-1 rounded-full border {{ $activeStatus === $c['key'] ? 'bg-'.$c['color'].'-100 text-'.$c['color'].'-800 dark:bg-'.$c['color'].'-900 dark:text-'.$c['color'].'-300' : 'text-gray-600 dark:text-gray-300' }}">
                                {{ $c['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex justify-end mb-4">
                        <a href="{{ route('chamados.create') }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            {{ __('Abrir Novo Chamado') }}
                        </a>
                    </div>

                    @if ($chamados->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('ID') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('TÃ­tulo') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('DescriÃ§Ã£o') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Prioridade') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Equipe') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Criado Em') }}
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">{{ __('AÃ§Ãµes') }}</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($chamados as $chamado)
                                    <tr data-status="{{ $chamado->status }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $chamado->id }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $chamado->titulo }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100 truncate w-48">
                                                {{ $chamado->descricao }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($chamado->prioridade === 'alta') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @elseif($chamado->prioridade === 'media') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @endif">
                                                {{ ucfirst($chamado->prioridade) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if(($chamado->status ?? '') === 'aberto') bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300
                                                @elseif(($chamado->status ?? '') === 'em andamento') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                @elseif(($chamado->status ?? '') === 'resolvido') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                                                {{ ucfirst($chamado->status ?? 'Não definido') }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $chamado->equipe->nome ?? '—' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ optional($chamado->created_at)->format('d/m/Y H:i') }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                            {{-- Ver (se estÃ¡ listado, jÃ¡ pode ver â€” mas mantemos a policy por seguranÃ§a) --}}
                                            @can('view', $chamado)
                                                <a href="{{ route('chamados.show', $chamado) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    {{ __('Ver') }}
                                                </a>
                                            @endcan

                                            {{-- Editar: sÃ³ se autorizado pela Policy --}}
                                            @can('update', $chamado)
                                                <a href="{{ route('chamados.edit', $chamado) }}"
                                                   class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                    {{ __('Editar') }}
                                                </a>
                                            @endcan

                                            {{-- Excluir: sÃ³ se autorizado pela Policy --}}
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
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $chamados->links() }}
                        </div>
                    @else
                        <p class="p-4 text-gray-700 dark:text-gray-300">{{ __('Nenhum chamado encontrado.') }}</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>