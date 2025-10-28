<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">Olá, {{ Auth::user()->name }}</div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('chamados.create') }}" class="rounded-lg p-4 bg-green-600 text-white shadow hover:bg-green-700 transition">Criar Chamado</a>
                <a href="{{ route('chamados.index') }}" class="rounded-lg p-4 bg-blue-600 text-white shadow hover:bg-blue-700 transition">Ver Chamados</a>
                <a href="{{ route('equipe.chat') }}" class="rounded-lg p-4 bg-indigo-600 text-white shadow hover:bg-indigo-700 transition">Chat da Equipe</a>
                <a href="{{ route('profile.edit') }}" class="rounded-lg p-4 bg-gray-700 text-white shadow hover:bg-gray-800 transition">Editar Perfil</a>
            </div>

            @if(($role ?? null) === 'admin')
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">Visão por Equipe</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach(($equipes ?? []) as $eq)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $eq->nome }}</h4>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="rounded border p-2"><div class="text-gray-500">Abertos</div><div class="text-2xl font-bold text-amber-600">{{ $eq->abertos_count }}</div></div>
                                    <div class="rounded border p-2"><div class="text-gray-500">Em andamento</div><div class="text-2xl font-bold text-blue-600">{{ $eq->andamento_count }}</div></div>
                                    <div class="rounded border p-2"><div class="text-gray-500">Resolvidos</div><div class="text-2xl font-bold text-emerald-600">{{ $eq->resolvidos_count }}</div></div>
                                    <div class="rounded border p-2"><div class="text-gray-500">Fechados</div><div class="text-2xl font-bold text-gray-600">{{ $eq->fechados_count }}</div></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Status por Equipe</h3>
                        </div>
                        <canvas id="teamStatusChart" height="140"></canvas>
                    </div>
                </div>
            @elseif(($role ?? null) === 'tecnica' && isset($team_counts))
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">Minha Equipe{{ isset($equipes[0]) ? ': '.$equipes[0]->nome : '' }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow"><div class="text-gray-500">Abertos</div><div class="text-3xl font-bold text-amber-600">{{ $team_counts['aberto'] ?? 0 }}</div></div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow"><div class="text-gray-500">Em andamento</div><div class="text-3xl font-bold text-blue-600">{{ $team_counts['em andamento'] ?? 0 }}</div></div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow"><div class="text-gray-500">Resolvidos</div><div class="text-3xl font-bold text-emerald-600">{{ $team_counts['resolvido'] ?? 0 }}</div></div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow"><div class="text-gray-500">Fechados</div><div class="text-3xl font-bold text-gray-600">{{ $team_counts['fechado'] ?? 0 }}</div></div>
                    </div>
                </div>
            @else
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">Meus Chamados</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow"><div class="text-gray-500">Abertos</div><div class="text-3xl font-bold text-amber-600">{{ $my_counts['aberto'] ?? 0 }}</div></div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow"><div class="text-gray-500">Em andamento</div><div class="text-3xl font-bold text-blue-600">{{ $my_counts['em andamento'] ?? 0 }}</div></div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow"><div class="text-gray-500">Resolvidos</div><div class="text-3xl font-bold text-emerald-600">{{ $my_counts['resolvido'] ?? 0 }}</div></div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow"><div class="text-gray-500">Fechados</div><div class="text-3xl font-bold text-gray-600">{{ $my_counts['fechado'] ?? 0 }}</div></div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tendência (últimos 30 dias)</h3>
                </div>
                <canvas id="trendChart" height="110"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const trend = @json($trend ?? []);
        const labels = (trend || []).map(x => x.d);
        const data = (trend || []).map(x => x.total);

        const ctx = document.getElementById('trendChart');
        if (ctx && window.Chart) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Chamados criados',
                        data,
                        fill: false,
                        borderColor: '#6366f1',
                        backgroundColor: '#6366f1',
                        tension: 0.25,
                        pointRadius: 2
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    },
                    plugins: {
                        legend: { display: true },
                        tooltip: { intersect: false, mode: 'index' }
                    }
                }
            });
        }
        @if(($role ?? null) === 'admin')
        const teamCtx = document.getElementById('teamStatusChart');
        if (teamCtx && window.Chart) {
            const teamLabels = @json($team_labels ?? []);
            const teamData = @json($team_data ?? []);
            const statusDefs = [
                { key: 'aberto',       label: 'aberto',        color: '#f59e0b' }, // amber-500
                { key: 'em_andamento', label: 'em andamento',  color: '#3b82f6' }, // blue-500
                { key: 'resolvido',    label: 'resolvido',     color: '#10b981' }, // emerald-500
                { key: 'fechado',      label: 'fechado',       color: '#6b7280' }, // gray-500
            ];
            const datasets = statusDefs.map(s => ({
                label: s.label,
                data: teamData[s.key] || [],
                backgroundColor: s.color,
                borderWidth: 0,
            }));

            new Chart(teamCtx, {
                type: 'bar',
                data: { labels: teamLabels, datasets },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
                }
            });
        }
        @endif
    </script>
</x-app-layout>
