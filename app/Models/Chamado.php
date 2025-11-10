<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equipe;


class Chamado extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titulo',
        'descricao',
        'prioridade',
        'equipe_id',
        'status',
        'arquivo',
    ];

    // Defina os relacionamentos (se houver)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function equipe()
    {
        return $this->belongsTo(Equipe::class);
    }
    public function mensagens()
    {
        return $this->hasMany(Mensagem::class)->latest();
    }

    public function anexos()
    {   
        return $this->hasMany(Anexo::class);
    }

    // Notas internas (somente equipe/técnicos/admin)
    public function notasInternas()
    {
        return $this->hasMany(\App\Models\NotaInterna::class);
    }

    // Seguidores do chamado (usuários que querem ser notificados)
    public function seguidores()
    {
        return $this->belongsToMany(User::class, 'chamado_followers')->withTimestamps();
    }

    // SLA helpers
    public function slaHours(): int
    {
        return match ($this->prioridade) {
            'alta'  => 24,
            'media' => 48,
            default => 72,
        };
    }

    public function slaDueAt(): ?\Illuminate\Support\Carbon
    {
        if (!$this->created_at) return null;
        return $this->created_at->copy()->addHours($this->slaHours());
    }

    public function isFinalizado(): bool
    {
        return in_array($this->status, ['resolvido','fechado'], true);
    }
}
