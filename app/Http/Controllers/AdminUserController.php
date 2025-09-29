<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Equipe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Formulário: criar usuário TÉCNICO
     */
    public function createTecnico()
    {
        $equipes = Equipe::orderBy('nome')->get(['id','nome']);
        return view('admin.usuarios.create-tecnico', compact('equipes'));
    }

    /**
     * Persistir novo usuário técnico
     */
    public function storeTecnico(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required','string','max:255'],
            'email'                 => ['required','string','lowercase','email','max:255','unique:users,email'],
            'password'              => ['required','confirmed', Rules\Password::defaults()],
            'equipe_id'             => ['nullable','exists:equipes,id'],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => User::ROLE_TECNICO,          // força técnico
            'equipe_id' => $data['equipe_id'] ?? null,  // opcional
        ]);

        return redirect()
            ->route('admin.usuarios.role.edit', $user)
            ->with('success', 'Técnico criado com sucesso.');
    }

    /**
     * Formulário: promover/demover (role)
     */
    public function editRole(User $user)
    {
        $equipes = Equipe::orderBy('nome')->get(['id','nome']);
        return view('admin.usuarios.edit-role', compact('user','equipes'));
    }

    /**
     * Atualizar role (usuario/tecnica/admin) e equipe
     */
    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role'      => ['required','in:'.implode(',', [User::ROLE_USER, User::ROLE_TECNICO, User::ROLE_ADMIN])],
            'equipe_id' => ['nullable','exists:equipes,id'],
        ]);

        // Regra: apenas técnicos ficam associados a equipe.
        // Se mudar para usuario/admin, removemos a equipe (para evitar bagunça).
        $update = ['role' => $data['role']];

        if ($data['role'] === User::ROLE_TECNICO) {
            $update['equipe_id'] = $data['equipe_id'] ?? null;
        } else {
            $update['equipe_id'] = null;
        }

        $user->update($update);

        return back()->with('success', 'Papel atualizado com sucesso.');
    }
}
