<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role','equipe_id'];
    protected $hidden   = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function equipe() { return $this->belongsTo(Equipe::class); }

    // Papeis
    public const ROLE_USER    = 'usuario';
    public const ROLE_TECNICO = 'tecnica'; // compatível com seu dado atual
    public const ROLE_ADMIN   = 'admin';

    // Helpers
    public function isAdmin(): bool   { return $this->role === self::ROLE_ADMIN; }
    public function isTecnico(): bool { return $this->role === self::ROLE_TECNICO; }
    public function isUsuario(): bool { return $this->role === self::ROLE_USER; }
}