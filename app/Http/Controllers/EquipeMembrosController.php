<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EquipeMembrosController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->equipe_id) {
            return redirect()->route('dashboard')->with('error', 'Você não está atribuído a uma equipe.');
        }

        $membros = User::where('equipe_id', $user->equipe_id)->orderBy('name')->get();
        return view('equipe.membros', compact('membros'));
    }
}

