<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'usuario',
        'password_hash',
        'rol',
        'estado',
    ];

    public const ROL_ESTUDIANTE = 'ESTUDIANTE';

    public const ROL_DOCENTE = 'DOCENTE';

    public const ROL_ADMINISTRADOR = 'ADMINISTRADOR';

    protected $hidden = ['password_hash'];

    protected $authPasswordName = 'password_hash';

    // El esquema existente no dispone de remember_token.
    protected $rememberTokenName = null;

    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    public function administrador(): HasOne
    {
        return $this->hasOne(Administrador::class, 'id_usuario', 'id_usuario');
    }

    public function docente(): HasOne
    {
        return $this->hasOne(Docente::class, 'id_usuario', 'id_usuario');
    }

    public function estudiante(): HasOne
    {
        return $this->hasOne(Estudiante::class, 'id_usuario', 'id_usuario');
    }
}
