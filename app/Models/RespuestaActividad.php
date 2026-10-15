<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaActividad extends Model
{
    protected $table = 'respuesta_actividad';

    protected $primaryKey = 'id_respuesta_actividad';

    protected $fillable = [
        'intento_id',
        'pregunta_id',
        'alternativa',
        'es_correcta',
    ];

    protected function casts(): array
    {
        return [
            'es_correcta' => 'boolean',
        ];
    }

    public function intento(): BelongsTo
    {
        return $this->belongsTo(ActividadIntento::class, 'intento_id', 'id_intento');
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id', 'id_pregunta');
    }
}
