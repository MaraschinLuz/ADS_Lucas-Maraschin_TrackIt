<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mensagem extends Model
{
    use HasFactory;

    protected $table = 'mensagens';  

    protected $fillable = [
        'chamado_id',
        'user_id',
        'mensagem',
    ];

    public function chamado()
    {
        return $this->belongsTo(Chamado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
