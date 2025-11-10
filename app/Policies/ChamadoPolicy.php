<?php

namespace App\Policies;

use App\Models\Chamado;
use App\Models\User;

class ChamadoPolicy
{
    /**
     * Admin pode tudo.
     */
    protected function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Técnicos só podem atuar/ver chamados da própria equipe.
     */
    protected function tecnicoMesmoTime(User $user, Chamado $chamado): bool
    {
        return $user->role === 'tecnica'
            && $user->equipe_id !== null
            && $chamado->equipe_id !== null
            && (int) $user->equipe_id === (int) $chamado->equipe_id;
    }

    /**
     * Usuário comum só vê o que ele abriu.
     */
    protected function isOwner(User $user, Chamado $chamado): bool
    {
        return (int) $user->id === (int) $chamado->user_id;
    }

    /**
     * Ver chamado.
     */
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

    /**
     * Atualizar chamado (título/descrição ou status/prioridade).
     * - Admin: tudo
     * - Técnico: apenas se for da própria equipe
     * - Dono do chamado: pode editar título/descrição (se você quiser manter)
     */
    public function update(User $user, Chamado $chamado): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->tecnicoMesmoTime($user, $chamado)) {
            return true;
        }

        // Se quiser permitir que o dono edite o próprio chamado, deixe true:
        return $this->isOwner($user, $chamado);
    }

    /**
     * Permissão específica usada na UI para habilitar edição de Status/Prioridade.
     * - Admin: permitido
     * - Técnico: permitido apenas para chamados da própria equipe
     */
    public function updatePriorityAndStatus(User $user, Chamado $chamado): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->tecnicoMesmoTime($user, $chamado);
    }

    /**
     * Excluir chamado.
     * - Admin: tudo
     * - Técnico: se for da própria equipe (ajuste conforme sua regra)
     * - Dono: se quiser permitir, mantenha.
     */
    public function delete(User $user, Chamado $chamado): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->tecnicoMesmoTime($user, $chamado)) {
            return true;
        }

        // opcional: somente dono pode deletar
        return $this->isOwner($user, $chamado);
    }
}
