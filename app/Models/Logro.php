<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Logro extends Model
{
    protected $table = 'logro';

    protected $primaryKey = 'id_logro';

    protected $fillable = [
        'nombre',
        'descripcion',
        'condicion',
    ];

    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_logro', 'id_logro', 'id_estudiante', 'id_logro', 'id_estudiante')
            ->withPivot('fecha_obtencion')
            ->withTimestamps();
    }
}
