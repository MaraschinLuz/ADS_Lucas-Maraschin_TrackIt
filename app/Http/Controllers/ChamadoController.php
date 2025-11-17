<?php

namespace App\Http\Controllers;

use App\Models\Chamado;
use App\Models\User;
use App\Models\Equipe;
use App\Models\Log;
use App\Models\Mensagem;
use App\Models\NotaInterna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Anexo;

class ChamadoController extends Controller
{
    use AuthorizesRequests;

    /**
     * Exibe a lista de chamados do usuário.
     */
    public function index(): \Illuminate\View\View
{
    $user = \Illuminate\Support\Facades\Auth::user();

    if ($user->role === 'admin') {
        $chamados = \App\Models\Chamado::with(['equipe', 'user'])
            ->latest()
            ->paginate(10);
    } elseif ($user->role === 'tecnica') {
        // técnico sem equipe -> lista vazia
        if (!$user->equipe_id) {
            $chamados = collect([]); // view deve tratar (ou use LengthAwarePaginator vazio)
            return view('chamados.index', [
                'chamados' => new \Illuminate\Pagination\LengthAwarePaginator(
                    [], 0, 10, 1, ['path' => request()->url(), 'query' => request()->query()]
                ),
                'alerta' => 'Você é técnico, mas ainda não foi designado a uma equipe.'
            ]);
        }

        $chamados = \App\Models\Chamado::with(['equipe', 'user'])
            ->where('equipe_id', $user->equipe_id)
            ->latest()
            ->paginate(10);
    } else {
        // usuário comum
        $chamados = \App\Models\Chamado::with(['equipe', 'user'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);
    }

        return view('chamados.index', compact('chamados'));
    }

    /**
     * Quadro kanban para técnicos/admin.
     */
    public function kanban(Request $request): View
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isTecnico()) {
            abort(403);
        }

        $columns = [
            ['key' => 'aberto', 'label' => 'Abertos', 'badge' => 'bg-amber-100 text-amber-800'],
            ['key' => 'em andamento', 'label' => 'Em andamento', 'badge' => 'bg-blue-100 text-blue-800'],
            ['key' => 'resolvido', 'label' => 'Resolvidos', 'badge' => 'bg-emerald-100 text-emerald-800'],
            ['key' => 'fechado', 'label' => 'Fechados', 'badge' => 'bg-gray-200 text-gray-700'],
        ];

        $equipes = Equipe::orderBy('nome')->get(['id', 'nome']);
        $selectedEquipeId = null;
        $alerta = null;

        if ($user->isAdmin()) {
            if ($request->filled('equipe_id')) {
                $valor = (int) $request->input('equipe_id');
                if ($equipes->contains('id', $valor)) {
                    $selectedEquipeId = $valor;
                }
            }
        } else {
            if (!$user->equipe_id) {
                $alerta = 'Você ainda não está vinculado a uma equipe. Solicite a um administrador antes de usar o Kanban.';
            } else {
                $selectedEquipeId = $user->equipe_id;
            }
        }

        $query = Chamado::with(['user', 'equipe']);

        if ($selectedEquipeId) {
            $query->where('equipe_id', $selectedEquipeId);
        } elseif (!$user->isAdmin()) {
            // técnico sem equipe já foi tratado acima; aqui evita vazamento
            $query->whereRaw('1 = 0');
        }

        $chamados = $query->orderByDesc('prioridade')
            ->orderBy('titulo')
            ->get();

        $kanbanData = [];
        foreach ($columns as $column) {
            $kanbanData[$column['key']] = $chamados->where('status', $column['key'])->values();
        }

        return view('chamados.kanban', [
            'columns' => $columns,
            'kanbanData' => $kanbanData,
            'equipes' => $equipes,
            'selectedEquipeId' => $selectedEquipeId,
            'alerta' => $alerta,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    /**
     * Exibe o formulário para criar um novo chamado.
     */
    public function create(): View
    {
        $equipes = Equipe::all();
        return view('chamados.create', compact('equipes'));
    }

    /**
     * Salva um novo chamado no banco de dados.
     */
    public function store(Request $request): RedirectResponse
    {   
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'prioridade' => 'required|in:baixa,media,alta',
            'equipe_id' => 'nullable|exists:equipes,id',
            'arquivos.*' => 'nullable|file|max:5120', // até 5MB por arquivo
        ]);

        $chamado = Chamado::create([
            'user_id' => Auth::id(),
            'titulo' => $validated['titulo'],
            'descricao' => $validated['descricao'],
            'prioridade' => $validated['prioridade'],
            'equipe_id' => $validated['equipe_id'] ?? null,
            'status' => 'aberto',
        ]);

        // Verifica se há múltiplos arquivos enviados
        if ($request->hasFile('arquivos')) {
            foreach ($request->file('arquivos') as $arquivo) {
                $path = $arquivo->store('anexos', 'public');
                $chamado->anexos()->create(['arquivo' => $path]);
            }
        }

        Log::create([
            'user_id' => auth()->id(),
            'acao' => 'Criou um chamado',
            'detalhes' => 'Título: ' . $chamado->titulo,
        ]);

        return redirect()->route('chamados.index')->with('success', 'Chamado criado com sucesso!');
    }

    /**
     * Exibe os detalhes de um chamado específico.
     */
    public function show(Chamado $chamado): View
    {
        $this->authorize('view', $chamado);

        $chamado->load(['mensagens.user', 'equipe', 'anexos', 'notasInternas.user', 'seguidores']); // carrega tudo de uma vez
        return view('chamados.show', compact('chamado'));
    }

    /**
     * Exibe o formulário de edição de um chamado.
     */
    public function edit(Chamado $chamado): View
    {
        $this->authorize('update', $chamado);
        $equipes = Equipe::all();
        return view('chamados.edit', compact('chamado', 'equipes'));
    }

    /**
     * Atualiza um chamado existente.
     */
    public function update(Request $request, Chamado $chamado): RedirectResponse
    {
        $this->authorize('update', $chamado);

        $oldStatus = $chamado->status;

        $rules = [
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'prioridade' => 'required|in:baixa,media,alta',
            'status' => 'required|in:aberto,em andamento,resolvido,fechado',
            'equipe_id' => 'nullable|exists:equipes,id',
        ];

        if (Auth::user()->role !== 'tecnica') {
            $rules['prioridade'] = ['required', Rule::in([$chamado->prioridade])];
            $rules['status'] = ['required', Rule::in([$chamado->status])];
        }

        $validated = $request->validate($rules);

        $chamado->update($validated);

        // Notificação de mudança de status
        if (($validated['status'] ?? $oldStatus) !== $oldStatus) {
            try {
                $by = Auth::user()?->name ?? 'Sistema';
                $notifyUsers = collect();
                // dono do chamado
                if ($chamado->user) { $notifyUsers->push($chamado->user); }
                // equipe do chamado
                if ($chamado->equipe) { $chamado->equipe->loadMissing('users'); }
                if ($chamado->equipe && $chamado->equipe->users) {
                    foreach ($chamado->equipe->users as $u) { $notifyUsers->push($u); }
                }
                $notifyUsers = $notifyUsers->unique('id')->reject(fn($u) => $u->id === Auth::id());
                if ($notifyUsers->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send(
                        $notifyUsers,
                        new \App\Notifications\ChamadoStatusChanged($chamado->id, (string)$chamado->titulo, $oldStatus, (string)$validated['status'], $by)
                    );
                }
            } catch (\Throwable $e) {
                // silencioso
            }
        }

        Log::create([
            'user_id' => auth()->id(),
            'acao' => 'Atualizou um chamado',
            'detalhes' => 'Título: ' . $chamado->titulo,
        ]);

        return redirect()->route('chamados.index')->with('success', 'Chamado atualizado com sucesso!');
    }

    /**
     * Remove um chamado.
     */
    public function destroy(Chamado $chamado): RedirectResponse
    {
        $this->authorize('delete', $chamado);

        $titulo = $chamado->titulo;
        $chamado->delete();

        Log::create([
            'user_id' => auth()->id(),
            'acao' => 'Excluiu um chamado',
            'detalhes' => 'Título: ' . $titulo,
        ]);

        return redirect()->route('chamados.index')->with('success', 'Chamado excluído com sucesso!');
    }

    /**
     * Salva uma nova mensagem (comentário) no histórico do chamado.
     */
    public function storeMensagem(Request $request, Chamado $chamado): RedirectResponse
    {
        $this->authorize('view', $chamado);

        $validated = $request->validate([
            'mensagem' => 'required|string|max:1000',
        ]);

        $chamado->mensagens()->create([
            'user_id' => Auth::id(),
            'mensagem' => $validated['mensagem'],
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'acao' => 'Adicionou uma mensagem ao chamado',
            'detalhes' => 'Chamado: ' . $chamado->titulo,
        ]);

        return redirect()->route('chamados.show', $chamado)->with('success', 'Mensagem enviada com sucesso!');
    }

    public function adicionarAnexo(Request $request, Chamado $chamado)
    {
        $request->validate([
            'arquivo' => 'required|file|max:5120', // 5MB
        ]);

        if ($request->hasFile('arquivo')) {
            $path = $request->file('arquivo')->store('anexos', 'public');

            $chamado->anexos()->create([
                'arquivo' => $path,
            ]);
        }

        return redirect()->route('chamados.show', $chamado)->with('success', 'Anexo adicionado com sucesso!');
    }

    // Notas internas
    public function storeNotaInterna(Request $request, Chamado $chamado): RedirectResponse
    {
        $this->authorize('view', $chamado);
        $user = Auth::user();
        if (!($user->isAdmin() || $user->isTecnico())) {
            abort(403);
        }
        $data = $request->validate(['nota' => 'required|string|max:5000']);
        $chamado->notasInternas()->create([
            'user_id' => $user->id,
            'nota' => $data['nota'],
        ]);
        return redirect()->route('chamados.show', $chamado)->with('success', 'Nota interna adicionada.');
    }

    public function deleteNotaInterna(Chamado $chamado, NotaInterna $nota): RedirectResponse
    {
        $this->authorize('view', $chamado);
        $user = Auth::user();
        if (!($user->isAdmin() || $user->isTecnico()) || $nota->chamado_id !== $chamado->id) {
            abort(403);
        }
        $nota->delete();
        return redirect()->route('chamados.show', $chamado)->with('success', 'Nota removida.');
    }

    // Seguidores
    public function follow(Chamado $chamado): RedirectResponse
    {
        $this->authorize('view', $chamado);
        $chamado->seguidores()->syncWithoutDetaching([Auth::id()]);
        return back()->with('success', 'Você agora segue este chamado.');
    }

    public function unfollow(Chamado $chamado): RedirectResponse
    {
        $this->authorize('view', $chamado);
        $chamado->seguidores()->detach([Auth::id()]);
        return back()->with('success', 'Você deixou de seguir este chamado.');
    }
}
