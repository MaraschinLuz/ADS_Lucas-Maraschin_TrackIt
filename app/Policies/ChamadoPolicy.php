<?php

namespace App\Policies;

use App\Models\Chamado;
use App\Models\User;

class ChamadoPolicy
{
    
    protected function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    
    protected function tecnicoMesmoTime(User $user, Chamado $chamado): bool
    {
        return $user->role === 'tecnica'
            && $user->equipe_id !== null
            && $chamado->equipe_id !== null
            && (int) $user->equipe_id === (int) $chamado->equipe_id;
    }

    
    protected function isOwner(User $user, Chamado $chamado): bool
    {
        return (int) $user->id === (int) $chamado->user_id;
    }

    
    public function view(User $user, Chamado $chamado): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->tecnicoMesmoTime($user, $chamado)) {
            return true;
        }

        return $this->isOwner($user, $chamado);
    }

    
    public function update(User $user, Chamado $chamado): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->tecnicoMesmoTime($user, $chamado)) {
            return true;
        }

        
        return $this->isOwner($user, $chamado);
    }

    
    public function updatePriorityAndStatus(User $user, Chamado $chamado): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->tecnicoMesmoTime($user, $chamado);
    }

    
    public function delete(User $user, Chamado $chamado): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->tecnicoMesmoTime($user, $chamado)) {
            return true;
        }

        
        return $this->isOwner($user, $chamado);
    }
}
