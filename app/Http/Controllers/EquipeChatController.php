<?php

namespace App\Http\Controllers;

use App\Models\EquipeMensagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EquipeNewMessage;
use App\Models\User;

class EquipeChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->equipe_id) {
            return redirect()->route('dashboard')->with('error', 'Você não está atribuído a uma equipe.');
        }

        return view('equipe.chat');
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        if (!$user->equipe_id) {
            return response()->json(['error' => 'Sem equipe'], 403);
        }

        $afterId = (int) $request->query('after_id', 0);

        $query = EquipeMensagem::with('user')
            ->where('equipe_id', $user->equipe_id)
            ->orderBy('id', 'asc');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        } else {
            $query->limit(50);
        }

        $mensagens = $query->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'user' => $m->user ? $m->user->name : 'Usuário',
                'mensagem' => $m->mensagem,
                'created_at' => $m->created_at?->format('d/m H:i'),
            ];
        });

        return response()->json($mensagens);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->equipe_id) {
            return response()->json(['error' => 'Sem equipe'], 403);
        }

        $validated = $request->validate([
            'mensagem' => 'required|string|max:2000',
        ]);

        $msg = EquipeMensagem::create([
            'equipe_id' => $user->equipe_id,
            'user_id' => $user->id,
            'mensagem' => $validated['mensagem'],
        ]);

        // Notifica membros da equipe (sem o remetente)
        $alvos = User::where('equipe_id', $user->equipe_id)
            ->where('id', '!=', $user->id)
            ->get();
        if ($alvos->isNotEmpty()) {
            $preview = mb_substr($msg->mensagem, 0, 120);
            Notification::send($alvos, new EquipeNewMessage((int)$user->equipe_id, (string)$user->name, (string)$preview));
        }

        return response()->json([
            'id' => $msg->id,
            'user' => $user->name,
            'mensagem' => $msg->mensagem,
            'created_at' => $msg->created_at?->format('d/m H:i'),
        ], 201);
    }
}
