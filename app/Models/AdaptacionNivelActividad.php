<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Detalle de las actividades consumidas por una decisión adaptativa.
 *
 * La tabla usa una clave primaria compuesta (adaptacion_id, actividad_id),
 * que Eloquent no admite de forma nativa. Este modelo se usa para consultas.
 */
class AdaptacionNivelActividad extends Model
{
    protected $table = 'adaptacion_nivel_actividad';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'adaptacion_id',
        'estudiante_id',
        'actividad_id',
        'intento_id',
        'puntaje',
    ];

    protected function casts(): array
    {
        return [
            'puntaje' => 'decimal:2',
        ];
    }

    public function adaptacion(): BelongsTo
    {
        return $this->belongsTo(AdaptacionNivel::class, 'adaptacion_id', 'id_adaptacion');
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id', 'id_actividad')->withTrashed();
    }

    public function intento(): BelongsTo
    {
        return $this->belongsTo(ActividadIntento::class, 'intento_id', 'id_intento');
    }
}
