<?php

namespace App\Http\Controllers;

use App\Models\ActividadIntento;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $estudiante = $request->user()->estudiante()
            ->with([
                'nivelActual',
                'gamificacion',
                'logros',
            ])
            ->first();

        $nivelActual = $estudiante?->nivelActual;
        $actividadesCompletadas = $estudiante?->intentos()
            ->where('estado', 'FINALIZADO')
            ->distinct()
            ->count('actividad_id') ?? 0;
        $gamificacion = $estudiante?->gamificacion;
        $logros = $estudiante?->logros ?? collect();
        $actividadesActivas = $nivelActual?->actividades()
            ->where('estado', 'ACTIVA')
            ->withExists(['intentos as tiene_intento_finalizado' => fn ($query) => $query
                ->where('estudiante_id', $estudiante->getKey())->where('estado', 'FINALIZADO')])
            ->addSelect([
                'intento_pendiente_id' => ActividadIntento::select('id_intento')
                    ->whereColumn('actividad_id', 'actividad.id_actividad')
                    ->where('estudiante_id', $estudiante->getKey())->where('estado', 'EN_PROCESO')
                    ->orderByDesc('fecha_inicio')->orderByDesc('id_intento')->limit(1),
                'ultimo_porcentaje' => ActividadIntento::select('puntaje')
                    ->whereColumn('actividad_id', 'actividad.id_actividad')
                    ->where('estudiante_id', $estudiante->getKey())->where('estado', 'FINALIZADO')
                    ->whereNotNull('puntaje')
                    ->orderByDesc('fecha_fin')->orderByDesc('id_intento')->limit(1),
            ])
            ->orderBy('titulo')->get() ?? collect();

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
            'actividadesCompletadas' => $actividadesCompletadas,
            'gamificacion' => $gamificacion,
            'logros' => $logros,
            'actividadesActivas' => $actividadesActivas,
            'ultimosIntentos' => $ultimosIntentos,
            'ultimasEvaluaciones' => $ultimasEvaluaciones,
        ]);
    }
}
