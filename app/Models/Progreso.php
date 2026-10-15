<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progreso extends Model
{
    protected $table = 'progreso';

    protected $primaryKey = 'id_progreso';

    protected $fillable = [
        'estudiante_id',
        'actividades_completadas',
        'puntaje_acumulado',
    ];

    protected function casts(): array
    {
        return [
            'actividades_completadas' => 'integer',
            'puntaje_acumulado' => 'decimal:2',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'id_estudiante');
    }
}
