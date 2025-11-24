<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;

class EquipeController extends Controller
{
    

     public function atribuirUsuariosForm()
{
    $equipes = Equipe::all();
    $usuarios = User::all(); 

    return view('equipes.atribuir', compact('equipes', 'usuarios'));
}

    public function index(): View
    {
        $equipes = Equipe::latest()->paginate(10);
        return view('equipes.index', compact('equipes'));
    }

    
    public function create(): View
    {
        return view('equipes.create');
    }

    
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $equipe = Equipe::create($validated);

        Log::create([
            'user_id' => Auth::id(),
            'acao' => 'Criou uma equipe',
            'detalhes' => 'Nome: ' . $equipe->nome,
        ]);

        return redirect()->route('equipes.index')->with('success', 'Equipe criada com sucesso!');
    }

    
    public function show(string $id): View
    {
        $equipe = Equipe::findOrFail($id);
        return view('equipes.show', compact('equipe'));
    }

    
    public function edit(string $id): View
    {
        $equipe = Equipe::findOrFail($id);
        return view('equipes.edit', compact('equipe'));
    }

    
    public function update(Request $request, string $id): RedirectResponse
    {
        $equipe = Equipe::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $equipe->update($validated);

        Log::create([
            'user_id' => Auth::id(),
            'acao' => 'Atualizou uma equipe',
            'detalhes' => 'Nome: ' . $equipe->nome,
        ]);

        return redirect()->route('equipes.index')->with('success', 'Equipe atualizada com sucesso!');
    }

    
    public function destroy(string $id): RedirectResponse
    {
        $equipe = Equipe::findOrFail($id);
        $nome = $equipe->nome;

        $equipe->delete();

        Log::create([
            'user_id' => Auth::id(),
            'acao' => 'Excluiu uma equipe',
            'detalhes' => 'Nome: ' . $nome,
        ]);

        return redirect()->route('equipes.index')->with('success', 'Equipe removida com sucesso!');
    }
}
