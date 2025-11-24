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

    
    public function chamados()
    {
        return $this->hasMany(Chamado::class);
    }

    
    public function users()
    {
        return $this->hasMany(User::class, 'equipe_id');
    }

    
    public function mensagens()
    {
        return $this->hasMany(\App\Models\EquipeMensagem::class, 'equipe_id');
    }
}
