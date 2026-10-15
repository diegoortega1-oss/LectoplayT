<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gamificacion extends Model
{
    protected $table = 'gamificacion';

    protected $primaryKey = 'id_gamificacion';

    protected $fillable = [
        'estudiante_id',
        'puntos',
    ];

    protected function casts(): array
    {
        return [
            'puntos' => 'integer',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'id_estudiante');
    }
}
