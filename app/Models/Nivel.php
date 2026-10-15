<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nivel extends Model
{
    protected $table = 'nivel';

    protected $primaryKey = 'id_nivel';

    protected $fillable = [
        'nombre',
        'rango_min',
        'rango_max',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'rango_min' => 'decimal:2',
            'rango_max' => 'decimal:2',
        ];
    }

    public function estudiantes(): HasMany
    {
        return $this->hasMany(Estudiante::class, 'nivel_actual_id', 'id_nivel');
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'nivel_id', 'id_nivel');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'nivel_id', 'id_nivel');
    }
}
