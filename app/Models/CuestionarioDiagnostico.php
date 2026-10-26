<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuestionarioDiagnostico extends Model
{
    protected $table = 'cuestionario_diagnostico';

    protected $primaryKey = 'id_cuestionario';

    protected $fillable = [
        'nombre',
        'version',
        'estado',
    ];

    public function lecturas(): HasMany
    {
        return $this->hasMany(LecturaDiagnostica::class, 'cuestionario_id', 'id_cuestionario');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'cuestionario_id', 'id_cuestionario');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'cuestionario_id', 'id_cuestionario');
    }
}
