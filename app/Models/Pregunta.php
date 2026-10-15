<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pregunta extends Model
{
    protected $table = 'pregunta';

    protected $primaryKey = 'id_pregunta';

    protected $fillable = [
        'evaluacion_id',
        'actividad_id',
        'enunciado',
        'alternativa_a',
        'alternativa_b',
        'alternativa_c',
        'alternativa_d',
        'respuesta_correcta',
    ];

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id', 'id_evaluacion');
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id', 'id_actividad');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(Respuesta::class, 'pregunta_id', 'id_pregunta');
    }

    public function respuestasActividad(): HasMany
    {
        return $this->hasMany(RespuestaActividad::class, 'pregunta_id', 'id_pregunta');
    }
}
