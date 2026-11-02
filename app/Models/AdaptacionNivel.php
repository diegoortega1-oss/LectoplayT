<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdaptacionNivel extends Model
{
    protected $table = 'adaptacion_nivel';

    protected $primaryKey = 'id_adaptacion';

    protected $fillable = [
        'estudiante_id',
        'nivel_anterior_id',
        'nivel_resultante_id',
        'decision',
        'promedio',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'promedio' => 'decimal:2',
            'fecha' => 'datetime',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'id_estudiante');
    }

    public function nivelAnterior(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'nivel_anterior_id', 'id_nivel');
    }

    public function nivelResultante(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'nivel_resultante_id', 'id_nivel');
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(AdaptacionNivelActividad::class, 'adaptacion_id', 'id_adaptacion');
    }
}
