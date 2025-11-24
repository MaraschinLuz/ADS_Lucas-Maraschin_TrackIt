<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Equipe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    
    public function index(Request $request)
    {
        $q        = (string) $request->input('q', '');
        $role     = (string) $request->input('role', '');
        $semEqp   = (bool) $request->boolean('sem_equipe', false);

        $query = User::query()->with('equipe')->orderBy('name');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        
        $validRoles = ['usuario', 'tecnica', 'admin'];
        if (in_array($role, $validRoles, true)) {
            $query->where('role', $role);
        }

        
        if ($semEqp) {
            $query->where('role', 'tecnica')->whereNull('equipe_id');
        }

        $users   = $query->paginate(12)->withQueryString();
        $equipes = Equipe::orderBy('nome')->get(['id','nome']);
        $roles   = [
            'usuario' => 'Usuário',
            'tecnica' => 'Técnico',
            'admin'   => 'Administrador',
        ];

        return view('admin.usuarios.index', compact('users','equipes','roles','q','role','semEqp'));
    }

    
    public function createTecnico()
    {
        $equipes = Equipe::orderBy('nome')->get(['id','nome']);
        return view('admin.usuarios.create-tecnico', compact('equipes'));
    }

    
    public function storeTecnico(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required','string','max:255'],
            'email'      => ['required','string','lowercase','email','max:255','unique:users,email'],
            'password'   => ['required','confirmed', Rules\Password::defaults()],
            'equipe_id'  => ['nullable','exists:equipes,id'],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => 'tecnica',                 
            'equipe_id' => $data['equipe_id'] ?? null,
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Técnico criado com sucesso.');
    }

    
    public function editRole(User $user)
    {
        $equipes = Equipe::orderBy('nome')->get(['id','nome']);
        return view('admin.usuarios.edit-role', compact('user','equipes'));
    }

    
    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role'      => ['required','in:usuario,tecnica,admin'],
            'equipe_id' => ['nullable','exists:equipes,id'],
        ]);

        $update = ['role' => $data['role']];

        if ($data['role'] === 'tecnica') {
            $update['equipe_id'] = $data['equipe_id'] ?? null;
        } else {
            $update['equipe_id'] = null;
        }

        $user->update($update);

        return back()->with('success', 'Papel/equipe atualizados com sucesso.');
    }
}
