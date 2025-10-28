<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipeMensagem extends Model
{
    use HasFactory;

    protected $table = 'equipe_mensagens';

    protected $fillable = [
        'equipe_id',
        'user_id',
        'mensagem',
    ];

    public function equipe()
    {
        return $this->belongsTo(Equipe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

