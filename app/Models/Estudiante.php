<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Estudiante extends Model
{
    protected $table = 'estudiante';

    protected $primaryKey = 'id_estudiante';

    protected $fillable = [
        'id_usuario',
        'nombre',
        'apellido',
        'nivel_actual_id',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function nivelActual(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'nivel_actual_id', 'id_nivel');
    }

    public function intentos(): HasMany
    {
        return $this->hasMany(ActividadIntento::class, 'estudiante_id', 'id_estudiante');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'estudiante_id', 'id_estudiante');
    }

    public function gamificacion(): HasOne
    {
        return $this->hasOne(Gamificacion::class, 'estudiante_id', 'id_estudiante');
    }

    public function progreso(): HasOne
    {
        return $this->hasOne(Progreso::class, 'estudiante_id', 'id_estudiante');
    }

    public function logros(): BelongsToMany
    {
        return $this->belongsToMany(Logro::class, 'estudiante_logro', 'id_estudiante', 'id_logro', 'id_estudiante', 'id_logro')
            ->withPivot('fecha_obtencion')
            ->withTimestamps();
    }
}
