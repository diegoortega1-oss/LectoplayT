<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadIntento;
use App\Models\AdaptacionNivel;
use App\Models\Estudiante;
use App\Models\Nivel;
use App\Services\AdaptacionNivelService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Página «Mi progreso»: solo lectura.
 *
 * Muestra el nivel lector actual, el recorrido de niveles (diagnóstico y adaptaciones)
 * y el avance hacia la próxima evaluación adaptativa. No escribe en la base de datos
 * ni usa la tabla `progreso`.
 */
class StudentProgressController extends Controller
{
    public function __invoke(Request $request, AdaptacionNivelService $adaptacion): View
    {
        // Siempre el estudiante autenticado: la ruta no recibe identificadores.
        $estudiante = $request->user()->estudiante()->with('nivelActual:id_nivel,nombre')->firstOrFail();

        $niveles = Nivel::query()->orderBy('rango_min')->orderBy('id_nivel')->get(['id_nivel', 'nombre']);

        // Mismo criterio que la página de resultado del diagnóstico.
        $diagnostico = $estudiante->evaluaciones()
            ->where('tipo', 'DIAGNOSTICA')
            ->where('estado', 'FINALIZADA')
            ->whereNotNull('cuestionario_id')
            ->whereNotNull('fecha_fin')
            ->with('nivel:id_nivel,nombre')
            ->orderByDesc('fecha_fin')
            ->orderByDesc('id_evaluacion')
            ->first(['id_evaluacion', 'fecha_fin', 'nivel_id', 'puntaje']);

        $adaptaciones = AdaptacionNivel::query()
            ->where('estudiante_id', $estudiante->getKey())
            ->with(['nivelAnterior:id_nivel,nombre', 'nivelResultante:id_nivel,nombre'])
            ->orderBy('fecha')
            ->orderBy('id_adaptacion')
            ->get(['id_adaptacion', 'nivel_anterior_id', 'nivel_resultante_id', 'decision', 'promedio', 'fecha']);

        return view('student.progress', [
            'estudiante' => $estudiante,
            'nivelActual' => $estudiante->nivelActual,
            'niveles' => $niveles,
            'recorrido' => $this->recorrido($diagnostico, $adaptaciones),
            'avance' => $this->avance($adaptacion->evaluar($estudiante), $estudiante),
            'puedeVerDiagnostico' => $diagnostico !== null && $diagnostico->puntaje !== null && $diagnostico->nivel !== null,
            'evolucion' => $this->evolucion($estudiante),
        ]);
    }

    /**
     * Primer intento finalizado de cada actividad, en orden cronológico, para la gráfica.
     *
     * Una sola consulta, sin cargar respuestas. El orden es el del motor adaptativo y del
     * historial: intentos sin fecha_fin primero (heredados), luego fecha_fin ascendente y,
     * ante empates, id_intento. Al recorrer las filas en ese orden, la primera aparición de
     * cada actividad es su primer intento, y la secuencia resultante ya queda cronológica.
     * Incluye actividades eliminadas lógicamente para conservar el historial.
     *
     * @return list<array{titulo: string, nivel: string|null, fecha: string|null, porcentaje: float|null}>
     */
    private function evolucion(Estudiante $estudiante): array
    {
        return ActividadIntento::query()
            ->join('actividad', 'actividad.id_actividad', '=', 'actividad_intento.actividad_id')
            ->leftJoin('nivel', 'nivel.id_nivel', '=', 'actividad.nivel_id')
            ->where('actividad_intento.estudiante_id', $estudiante->getKey())
            ->where('actividad_intento.estado', 'FINALIZADO')
            ->orderByRaw('CASE WHEN actividad_intento.fecha_fin IS NULL THEN 0 ELSE 1 END')
            ->orderBy('actividad_intento.fecha_fin')
            ->orderBy('actividad_intento.id_intento')
            ->get([
                'actividad_intento.id_intento',
                'actividad_intento.actividad_id',
                'actividad_intento.puntaje',
                'actividad_intento.fecha_fin',
                'actividad.titulo',
                'nivel.nombre as nivel_nombre',
            ])
            ->unique('actividad_id')
            ->map(fn (ActividadIntento $intento): array => [
                'titulo' => $intento->titulo,
                'nivel' => $intento->nivel_nombre,
                // Sin fecha_fin no se inventa una fecha: la vista lo indica como no registrada.
                'fecha' => $intento->fecha_fin?->format('d/m/Y'),
                'porcentaje' => is_numeric($intento->puntaje) ? (float) $intento->puntaje : null,
            ])
            ->values()
            ->all();
    }

    /**
     * Eventos del recorrido en orden cronológico, con textos para estudiantes de primaria.
     *
     * @return list<array{icono: string, titulo: string, detalle: string, fecha: string, promedio: string|null}>
     */
    private function recorrido($diagnostico, Collection $adaptaciones): array
    {
        $eventos = [];

        if ($diagnostico !== null && $diagnostico->nivel !== null) {
            $eventos[] = [
                'momento' => $diagnostico->fecha_fin,
                'orden' => 0,
                'icono' => '🧭',
                'titulo' => 'Tu diagnóstico inicial',
                'detalle' => 'Empezaste tu camino en el nivel '.$diagnostico->nivel->nombre.'.',
                'promedio' => null,
            ];
        }

        foreach ($adaptaciones as $adaptacion) {
            $anterior = $adaptacion->nivelAnterior?->nombre ?? 'tu nivel anterior';
            $nuevo = $adaptacion->nivelResultante?->nombre ?? 'tu nivel actual';

            [$icono, $titulo, $detalle] = match ($adaptacion->decision) {
                'SUBIR' => ['🚀', '¡Subiste de nivel!', "Pasaste de $anterior a $nuevo."],
                'BAJAR' => ['🌱', 'Volviste a practicar', "Pasaste de $anterior a $nuevo para reforzar tu lectura."],
                default => ['📘', 'Seguiste en tu nivel', "Continuaste en el nivel $nuevo."],
            };

            $eventos[] = [
                'momento' => $adaptacion->fecha,
                'orden' => 1,
                'icono' => $icono,
                'titulo' => $titulo,
                'detalle' => $detalle,
                'promedio' => number_format((float) $adaptacion->promedio, 2, ',', ' ').' %',
            ];
        }

        usort($eventos, fn (array $a, array $b): int => [$a['momento']?->getTimestamp() ?? 0, $a['orden']]
            <=> [$b['momento']?->getTimestamp() ?? 0, $b['orden']]);

        return array_map(fn (array $evento): array => [
            'icono' => $evento['icono'],
            'titulo' => $evento['titulo'],
            'detalle' => $evento['detalle'],
            'fecha' => $evento['momento']?->format('d/m/Y') ?? '',
            'promedio' => $evento['promedio'],
        ], $eventos);
    }

    /**
     * Traduce el resultado de solo lectura del motor adaptativo a un mensaje sencillo.
     *
     * @return array{tipo: string, llevas: int, total: int, faltan: int, sinLecturasSuficientes: bool}
     */
    private function avance(array $evaluacion, Estudiante $estudiante): array
    {
        $total = AdaptacionNivelService::ACTIVIDADES_POR_BLOQUE;
        $base = ['tipo' => 'neutro', 'llevas' => 0, 'total' => $total, 'faltan' => $total, 'sinLecturasSuficientes' => false];

        if ($evaluacion['estado'] === AdaptacionNivelService::ESTADO_EVALUADA) {
            return ['tipo' => 'completo', 'llevas' => $total, 'faltan' => 0] + $base;
        }

        if ($evaluacion['motivo'] !== AdaptacionNivelService::MOTIVO_ACTIVIDADES_INSUFICIENTES) {
            // Período indeterminado o datos de nivel no disponibles: mensaje neutro, sin contador.
            return $base;
        }

        $llevas = min((int) $evaluacion['cantidad_actividades'], $total);
        $faltan = $total - $llevas;

        // Lecturas de su nivel que aún no ha terminado nunca: son las únicas que pueden sumar.
        $lecturasNuevas = Actividad::query()
            ->where('nivel_id', $estudiante->nivel_actual_id)
            ->where('estado', 'ACTIVA')
            ->whereHas('preguntas')
            ->whereDoesntHave('intentos', fn ($query) => $query
                ->where('estudiante_id', $estudiante->getKey())
                ->where('estado', 'FINALIZADO'))
            ->count();

        return [
            'tipo' => 'contador',
            'llevas' => $llevas,
            'faltan' => $faltan,
            'sinLecturasSuficientes' => $lecturasNuevas < $faltan,
        ] + $base;
    }
}
