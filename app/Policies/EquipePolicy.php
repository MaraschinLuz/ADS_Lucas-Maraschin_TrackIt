<?php

namespace App\Policies;

use App\Models\User;

class EquipePolicy
{
    
    public function __construct()
    {
        
    }

    public function podeAtribuirEquipe(User $user)
    {
        return $user->role === 'tecnica';
    }
}
