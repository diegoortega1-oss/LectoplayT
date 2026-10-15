<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $estudiante = $request->user()->estudiante()
            ->with([
                'nivelActual.actividades' => fn ($query) => $query->where('estado', 'ACTIVA')->orderBy('titulo'),
                'progreso',
                'gamificacion',
                'logros',
            ])
            ->first();

        $nivelActual = $estudiante?->nivelActual;
        $progreso = $estudiante?->progreso;
        $gamificacion = $estudiante?->gamificacion;
        $logros = $estudiante?->logros ?? collect();
        $actividadesActivas = $nivelActual?->actividades ?? collect();

        $ultimosIntentos = $estudiante?->intentos()
            ->with('actividad')
            ->orderByDesc('fecha_inicio')
            ->limit(5)
            ->get() ?? collect();

        $ultimasEvaluaciones = $estudiante?->evaluaciones()
            ->with('nivel')
            ->orderByDesc('fecha')
            ->limit(5)
            ->get() ?? collect();

        return view('student.dashboard', [
            'titulo' => 'Estudiante',
            'estudiante' => $estudiante,
            'nivelActual' => $nivelActual,
            'progreso' => $progreso,
            'gamificacion' => $gamificacion,
            'logros' => $logros,
            'actividadesActivas' => $actividadesActivas,
            'ultimosIntentos' => $ultimosIntentos,
            'ultimasEvaluaciones' => $ultimasEvaluaciones,
        ]);
    }
}
