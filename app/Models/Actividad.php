<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actividad extends Model
{
    use SoftDeletes;

    protected $table = 'actividad';

    protected $primaryKey = 'id_actividad';

    protected $fillable = [
        'nivel_id',
        'titulo',
        'contenido',
        'estado',
    ];

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'nivel_id', 'id_nivel');
    }

    public function intentos(): HasMany
    {
        return $this->hasMany(ActividadIntento::class, 'actividad_id', 'id_actividad');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'actividad_id', 'id_actividad');
    }
}
