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
        'tipo',
        'fecha',
        'puntaje',
        'nivel_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'puntaje' => 'decimal:2',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'id_estudiante');
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
