<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LecturaDiagnostica extends Model
{
    protected $table = 'lectura_diagnostica';

    protected $primaryKey = 'id_lectura';

    protected $fillable = [
        'cuestionario_id',
        'titulo',
        'contenido',
        'orden',
    ];

    public function cuestionario(): BelongsTo
    {
        return $this->belongsTo(CuestionarioDiagnostico::class, 'cuestionario_id', 'id_cuestionario');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'lectura_diagnostica_id', 'id_lectura');
    }
}
