<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotaInterna extends Model
{
    use HasFactory;

    protected $table = 'notas_internas';
    protected $fillable = ['chamado_id','user_id','nota'];

    public function chamado() { return $this->belongsTo(Chamado::class); }
    public function user() { return $this->belongsTo(User::class); }
}

