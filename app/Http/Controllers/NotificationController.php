<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $items = $user->unreadNotifications()->latest()->limit(10)->get()->map(function($n){
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? 'Nova notificação',
                'type' => $n->data['type'] ?? 'info',
                'created_at' => $n->created_at?->format('d/m H:i'),
            ];
        });
        return response()->json($items);
    }

    public function read(string $id)
    {
        $user = Auth::user();
        $n = $user->unreadNotifications()->where('id', $id)->first();
        if ($n) { $n->markAsRead(); }
        return response()->json(['ok' => true]);
    }

    public function readAll()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }
}

