<?php

namespace App\Http\Controllers;

use App\Models\Chamado;
use App\Models\Equipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $role = $user->role ?? 'usuario';
        $now = now();
        $from = $now->copy()->subDays(30);

        $result = [
            'role' => $role,
        ];

        if ($role === 'admin') {
            // Cards por equipe (contagem por status)
            $equipes = Equipe::orderBy('nome')
                ->withCount([
                    'chamados as abertos_count' => function ($q) { $q->where('status', 'aberto'); },
                    'chamados as andamento_count' => function ($q) { $q->where('status', 'em andamento'); },
                    'chamados as resolvidos_count' => function ($q) { $q->where('status', 'resolvido'); },
                    'chamados as fechados_count' => function ($q) { $q->where('status', 'fechado'); },
                ])->get(['id','nome']);

            // Tendência geral (30 dias)
            $trend = Chamado::selectRaw("DATE(created_at) as d, COUNT(*) as total")
                ->where('created_at', '>=', $from)
                ->groupBy('d')
                ->orderBy('d')
                ->get();

            $result['equipes'] = $equipes;
            $result['trend'] = $trend;
            // Dados prontos para gráficos (evita lógica PHP complexa no Blade)
            $result['team_labels'] = $equipes->pluck('nome')->values();
            $result['team_data'] = [
                'aberto'        => $equipes->map(fn($e) => (int) ($e->abertos_count ?? 0))->values(),
                'em_andamento'  => $equipes->map(fn($e) => (int) ($e->andamento_count ?? 0))->values(),
                'resolvido'     => $equipes->map(fn($e) => (int) ($e->resolvidos_count ?? 0))->values(),
                'fechado'       => $equipes->map(fn($e) => (int) ($e->fechados_count ?? 0))->values(),
            ];
        } elseif ($role === 'tecnica' && $user->equipe_id) {
            $equipeId = (int) $user->equipe_id;

            $counts = Chamado::where('equipe_id', $equipeId)
                ->selectRaw("status, COUNT(*) as c")
                ->groupBy('status')
                ->pluck('c', 'status');

            $trend = Chamado::where('equipe_id', $equipeId)
                ->where('created_at', '>=', $from)
                ->selectRaw('DATE(created_at) as d, COUNT(*) as total')
                ->groupBy('d')
                ->orderBy('d')
                ->get();

            $equipes = Equipe::where('id', $equipeId)->get(['id','nome']);

            $result['team_counts'] = [
                'aberto' => (int) ($counts['aberto'] ?? 0),
                'em andamento' => (int) ($counts['em andamento'] ?? 0),
                'resolvido' => (int) ($counts['resolvido'] ?? 0),
                'fechado' => (int) ($counts['fechado'] ?? 0),
            ];
            $result['trend'] = $trend;
            $result['equipes'] = $equipes; // para título
        } else {
            // Usuário comum: seus próprios chamados
            $counts = Chamado::where('user_id', $user->id)
                ->selectRaw('status, COUNT(*) as c')
                ->groupBy('status')
                ->pluck('c', 'status');

            $trend = Chamado::where('user_id', $user->id)
                ->where('created_at', '>=', $from)
                ->selectRaw('DATE(created_at) as d, COUNT(*) as total')
                ->groupBy('d')
                ->orderBy('d')
                ->get();

            $result['my_counts'] = [
                'aberto' => (int) ($counts['aberto'] ?? 0),
                'em andamento' => (int) ($counts['em andamento'] ?? 0),
                'resolvido' => (int) ($counts['resolvido'] ?? 0),
                'fechado' => (int) ($counts['fechado'] ?? 0),
            ];
            $result['trend'] = $trend;
        }

        return view('dashboard', $result);
    }
}
