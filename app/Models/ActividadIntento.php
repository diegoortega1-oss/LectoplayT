<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActividadIntento extends Model
{
    protected $table = 'actividad_intento';

    protected $primaryKey = 'id_intento';

    protected $fillable = [
        'estudiante_id',
        'actividad_id',
        'puntaje',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'puntaje' => 'decimal:2',
            'puntos_obtenidos' => 'integer',
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'id_estudiante');
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id', 'id_actividad');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaActividad::class, 'intento_id', 'id_intento');
    }
}
