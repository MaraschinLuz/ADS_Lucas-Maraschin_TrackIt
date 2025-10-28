<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
    ];

    /**
     * Uma equipe pode ter vários chamados.
     */
    public function chamados()
    {
        return $this->hasMany(Chamado::class);
    }

    /**
     * Uma equipe pode ter vários usuários (técnicos).
     */
    public function users()
    {
        return $this->hasMany(User::class, 'equipe_id');
    }

    /**
     * Mensagens do chat desta equipe.
     */
    public function mensagens()
    {
        return $this->hasMany(\App\Models\EquipeMensagem::class, 'equipe_id');
    }
}
