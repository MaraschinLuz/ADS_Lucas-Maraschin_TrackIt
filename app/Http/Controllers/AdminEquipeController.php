<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\User;
use Illuminate\Http\Request;

class AdminEquipeController extends Controller
{
    public function index()
    {
        // Equipes com técnicos
        $equipes = Equipe::with(['users' => function ($q) {
            $q->where('role', User::ROLE_TECNICO)->orderBy('name');
        }])->orderBy('nome')->get();

        // Técnicos sem equipe
        $tecnicosSemEquipe = User::where('role', User::ROLE_TECNICO)
            ->whereNull('equipe_id')
            ->orderBy('name')
            ->get();

        // Dropdowns
        $todasEquipes = Equipe::orderBy('nome')->get(['id','nome']);

        return view('admin.equipes.index', compact('equipes','tecnicosSemEquipe','todasEquipes'));
    }

    public function atribuir(Request $request)
    {
        $data = $request->validate([
            'user_id'   => ['required','exists:users,id'],
            'equipe_id' => ['required','exists:equipes,id'],
        ]);

        $user = User::findOrFail($data['user_id']);
        if (!$user->isTecnico()) {
            return back()->withErrors(['user_id' => 'Somente usuários com papel Técnico podem ser atribuídos.']);
        }

        $user->update(['equipe_id' => $data['equipe_id']]);

        return back()->with('success', "Técnico {$user->name} atribuído à equipe com sucesso.");
    }

    public function remover(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
        ]);

        $user = User::findOrFail($data['user_id']);
        if (!$user->isTecnico()) {
            return back()->withErrors(['user_id' => 'Somente usuários com papel Técnico podem ser removidos de equipe.']);
        }

        $user->update(['equipe_id' => null]);

        return back()->with('success', "Técnico {$user->name} removido da equipe.");
    }
}
