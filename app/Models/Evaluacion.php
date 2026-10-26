<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluacion extends Model
{
    protected $table = 'evaluacion';

    protected $primaryKey = 'id_evaluacion';

    protected $fillable = [
        'estudiante_id',
        'cuestionario_id',
        'tipo',
        'estado',
        'fecha',
        'fecha_fin',
        'puntaje',
        'nivel_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'fecha_fin' => 'datetime',
            'puntaje' => 'decimal:2',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'id_estudiante');
    }

    public function cuestionario(): BelongsTo
    {
        return $this->belongsTo(CuestionarioDiagnostico::class, 'cuestionario_id', 'id_cuestionario');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'nivel_id', 'id_nivel');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'evaluacion_id', 'id_evaluacion');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(Respuesta::class, 'evaluacion_id', 'id_evaluacion');
    }
}
