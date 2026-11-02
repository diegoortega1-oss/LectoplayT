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
        // Lecturas distintas terminadas al menos una vez, en todos los niveles: los reintentos no suman.
        $actividadesCompletadas = $estudiante?->intentos()
            ->where('estado', 'FINALIZADO')
            ->distinct()
            ->count('actividad_id') ?? 0;
        $gamificacion = $estudiante?->gamificacion;
        $logros = $estudiante?->logros ?? collect();

        // Una sola consulta: los resultados de cada tarjeta se obtienen con subconsultas correlacionadas.
        $intentosFinalizados = fn () => ActividadIntento::query()
            ->whereColumn('actividad_id', 'actividad.id_actividad')
            ->where('estudiante_id', $estudiante->getKey())
            ->where('estado', 'FINALIZADO');

        $actividadesActivas = $nivelActual?->actividades()
            ->where('estado', 'ACTIVA')
            ->withCount(['intentos as intentos_finalizados' => fn ($query) => $query
                ->where('estudiante_id', $estudiante->getKey())->where('estado', 'FINALIZADO')])
            ->addSelect([
                'intento_pendiente_id' => ActividadIntento::select('id_intento')
                    ->whereColumn('actividad_id', 'actividad.id_actividad')
                    ->where('estudiante_id', $estudiante->getKey())->where('estado', 'EN_PROCESO')
                    ->orderByDesc('fecha_inicio')->orderByDesc('id_intento')->limit(1),
                // Primer intento finalizado con el mismo orden que el motor adaptativo:
                // fecha_fin ascendente (en MariaDB los nulos van primero) y luego id_intento.
                'primer_porcentaje' => $intentosFinalizados()->select('puntaje')
                    ->orderBy('fecha_fin')->orderBy('id_intento')->limit(1),
                'ultimo_porcentaje' => $intentosFinalizados()->select('puntaje')
                    ->orderByDesc('fecha_fin')->orderByDesc('id_intento')->limit(1),
                'ultimo_intento_id' => $intentosFinalizados()->select('id_intento')
                    ->orderByDesc('fecha_fin')->orderByDesc('id_intento')->limit(1),
            ])
            ->orderBy('titulo')->get() ?? collect();

        // Se eliminaron $ultimosIntentos y $ultimasEvaluaciones: la vista no los usaba.
        return view('student.dashboard', [
            'titulo' => 'Estudiante',
            'estudiante' => $estudiante,
            'nivelActual' => $nivelActual,
            'actividadesCompletadas' => $actividadesCompletadas,
            'gamificacion' => $gamificacion,
            'logros' => $logros,
            'actividadesActivas' => $actividadesActivas,
        ]);
    }
}
