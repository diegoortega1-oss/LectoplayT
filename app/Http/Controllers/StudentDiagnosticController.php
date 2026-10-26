<?php

namespace App\Http\Controllers;

use App\Models\CuestionarioDiagnostico;
use App\Models\Evaluacion;
use App\Services\DiagnosticoInicialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class StudentDiagnosticController extends Controller
{
    public function index(Request $request, DiagnosticoInicialService $diagnostico): Response
    {
        $estudiante = $request->user()->estudiante()->firstOrFail();
        $estado = $diagnostico->determinarEstado($estudiante);

        if ($estado === DiagnosticoInicialService::DIAGNOSTICO_PENDIENTE) {
            return response()->view('student.diagnostic.index');
        }

        if (in_array($estado, [
            DiagnosticoInicialService::ACCESO_NORMAL,
            DiagnosticoInicialService::DIAGNOSTICO_FINALIZADO,
        ], true)) {
            return redirect()->route('panel.estudiante');
        }

        [$mensaje, $codigo] = match ($estado) {
            DiagnosticoInicialService::DIAGNOSTICO_EN_PROCESO => [
                'Tu diagnóstico ya está en proceso. Pronto podrás retomarlo desde aquí.',
                Response::HTTP_CONFLICT,
            ],
            DiagnosticoInicialService::INCONSISTENCIA => [
                'No podemos mostrar tu diagnóstico porque sus datos necesitan revisión. Pide ayuda a tu docente.',
                Response::HTTP_CONFLICT,
            ],
            DiagnosticoInicialService::SIN_CUESTIONARIO_ACTIVO => [
                'El diagnóstico aún no está disponible. Vuelve más tarde o consulta a tu docente.',
                Response::HTTP_SERVICE_UNAVAILABLE,
            ],
            DiagnosticoInicialService::CONFIGURACION_INVALIDA => [
                'El diagnóstico no está disponible en este momento. Pide ayuda a tu docente.',
                Response::HTTP_SERVICE_UNAVAILABLE,
            ],
            default => [
                'No podemos mostrar tu diagnóstico en este momento. Pide ayuda a tu docente.',
                Response::HTTP_CONFLICT,
            ],
        };

        return response()->view('student.diagnostic.index', ['mensaje' => $mensaje], $codigo);
    }

    public function iniciar(Request $request, DiagnosticoInicialService $diagnostico): Response
    {
        return DB::transaction(function () use ($request, $diagnostico): Response {
            $estudiante = $request->user()->estudiante()->lockForUpdate()->firstOrFail();
            $estado = $diagnostico->determinarEstado($estudiante);

            if ($estado === DiagnosticoInicialService::DIAGNOSTICO_EN_PROCESO) {
                return redirect()->route('diagnostico.en-proceso');
            }

            if ($estado !== DiagnosticoInicialService::DIAGNOSTICO_PENDIENTE) {
                return redirect()->route('diagnostico.index');
            }

            $cuestionario = CuestionarioDiagnostico::query()
                ->where('estado', 'ACTIVO')
                ->first();

            if (! $cuestionario) {
                return redirect()->route('diagnostico.index');
            }

            $estudiante->evaluaciones()->create([
                'cuestionario_id' => $cuestionario->getKey(),
                'tipo' => 'DIAGNOSTICA',
                'estado' => 'EN_PROCESO',
                'fecha' => now(),
                'fecha_fin' => null,
                'puntaje' => null,
                'nivel_id' => null,
            ]);

            return redirect()->route('diagnostico.en-proceso');
        });
    }

    public function enProceso(Request $request, DiagnosticoInicialService $diagnostico): Response
    {
        $estudiante = $request->user()->estudiante()->firstOrFail();

        if ($diagnostico->determinarEstado($estudiante) !== DiagnosticoInicialService::DIAGNOSTICO_EN_PROCESO) {
            return redirect()->route('diagnostico.index');
        }

        return response()->view('student.diagnostic.index', [
            'mensaje' => 'Tu diagnóstico está en proceso. Ya puedes ver la primera lectura y sus preguntas.',
            'continuar' => true,
        ]);
    }

    public function lectura(Request $request, DiagnosticoInicialService $diagnostico, int $orden): Response
    {
        $estudiante = $request->user()->estudiante()->firstOrFail();
        abort_unless(
            $diagnostico->determinarEstado($estudiante) === DiagnosticoInicialService::DIAGNOSTICO_EN_PROCESO,
            Response::HTTP_NOT_FOUND,
        );

        $evaluacion = $estudiante->evaluaciones()
            ->where('estado', 'EN_PROCESO')
            ->whereHas('cuestionario', fn ($query) => $query->where('estado', 'ACTIVO'))
            ->with('cuestionario:id_cuestionario,estado')
            ->firstOrFail(['id_evaluacion', 'estudiante_id', 'cuestionario_id']);

        $cuestionario = $evaluacion->cuestionario;
        $lectura = $cuestionario->lecturas()
            ->where('orden', $orden)
            ->firstOrFail(['id_lectura', 'cuestionario_id', 'orden', 'titulo', 'contenido']);

        $this->asegurarLecturasPreviasCompletas($evaluacion, $orden);

        $preguntas = $lectura->preguntas()
            ->where('cuestionario_id', $cuestionario->getKey())
            ->orderBy('orden')
            ->get([
                'id_pregunta',
                'enunciado',
                'alternativa_a',
                'alternativa_b',
                'alternativa_c',
                'alternativa_d',
                'orden',
            ]);

        $totalLecturas = $cuestionario->lecturas()->count();
        $ordenAnterior = $cuestionario->lecturas()
            ->where('orden', '<', $orden)
            ->orderByDesc('orden')
            ->value('orden');
        $respuestas = $evaluacion->respuestas()
            ->whereIn('pregunta_id', $preguntas->pluck('id_pregunta'))
            ->pluck('alternativa', 'pregunta_id');

        return response()->view('student.diagnostic.lectura', compact('lectura', 'preguntas', 'totalLecturas', 'respuestas', 'ordenAnterior'));
    }

    public function guardar(Request $request, DiagnosticoInicialService $diagnostico, int $orden): Response
    {
        return DB::transaction(function () use ($request, $diagnostico, $orden): Response {
            $estudiante = $request->user()->estudiante()->lockForUpdate()->firstOrFail();
            abort_unless(
                $diagnostico->determinarEstado($estudiante) === DiagnosticoInicialService::DIAGNOSTICO_EN_PROCESO,
                Response::HTTP_NOT_FOUND,
            );

            $evaluacion = $estudiante->evaluaciones()
                ->where('estado', 'EN_PROCESO')
                ->whereHas('cuestionario', fn ($query) => $query->where('estado', 'ACTIVO'))
                ->lockForUpdate()
                ->firstOrFail(['id_evaluacion', 'estudiante_id', 'cuestionario_id', 'estado']);

            abort_unless($evaluacion->estado === 'EN_PROCESO', Response::HTTP_CONFLICT);

            $lectura = $evaluacion->cuestionario->lecturas()
                ->where('orden', $orden)
                ->firstOrFail(['id_lectura', 'cuestionario_id']);

            $this->asegurarLecturasPreviasCompletas($evaluacion, $orden);

            $preguntas = $lectura->preguntas()
                ->where('cuestionario_id', $evaluacion->cuestionario_id)
                ->orderBy('orden')
                ->get(['id_pregunta', 'respuesta_correcta']);

            abort_unless($preguntas->count() === 5, Response::HTTP_CONFLICT, 'Esta lectura no tiene cinco preguntas disponibles.');

            $validated = $request->validate([
                'respuestas' => ['required', 'array', 'size:5'],
                'respuestas.*' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
            ]);

            $selecciones = $validated['respuestas'];
            $recibidos = array_map('strval', array_keys($selecciones));
            $esperados = $preguntas->pluck('id_pregunta')->map(fn ($id) => (string) $id)->all();
            sort($recibidos);
            sort($esperados);

            if ($recibidos !== $esperados) {
                throw ValidationException::withMessages([
                    'respuestas' => 'Responde únicamente las cinco preguntas de esta lectura.',
                ]);
            }

            foreach ($preguntas as $pregunta) {
                $alternativa = $selecciones[$pregunta->getKey()];

                $evaluacion->respuestas()->updateOrCreate(
                    ['pregunta_id' => $pregunta->getKey()],
                    [
                        'alternativa' => $alternativa,
                        'es_correcta' => $alternativa === $pregunta->respuesta_correcta,
                    ],
                );
            }

            $ordenSiguiente = $evaluacion->cuestionario->lecturas()
                ->where('orden', '>', $orden)
                ->orderBy('orden')
                ->value('orden');

            return redirect()->route(
                $ordenSiguiente === null ? 'diagnostico.resumen' : 'diagnostico.lectura',
                $ordenSiguiente === null ? [] : ['orden' => $ordenSiguiente],
            )
                ->with('status', 'Tus respuestas se guardaron correctamente');
        });
    }

    public function resumen(Request $request, DiagnosticoInicialService $diagnostico): Response
    {
        $estudiante = $request->user()->estudiante()->firstOrFail();
        abort_unless(
            $diagnostico->determinarEstado($estudiante) === DiagnosticoInicialService::DIAGNOSTICO_EN_PROCESO,
            Response::HTTP_NOT_FOUND,
        );

        $evaluacion = $estudiante->evaluaciones()
            ->where('estado', 'EN_PROCESO')
            ->whereHas('cuestionario', fn ($query) => $query->where('estado', 'ACTIVO'))
            ->firstOrFail(['id_evaluacion', 'estudiante_id', 'cuestionario_id']);

        $totalLecturas = $evaluacion->cuestionario->lecturas()->count();
        abort_unless($totalLecturas === 3, Response::HTTP_CONFLICT, 'El diagnóstico no tiene tres lecturas disponibles.');
        $this->asegurarLecturasPreviasCompletas($evaluacion, $totalLecturas + 1);

        return response()->view('student.diagnostic.resumen', compact('totalLecturas'));
    }

    private function asegurarLecturasPreviasCompletas(Evaluacion $evaluacion, int $orden): void
    {
        $lecturasPrevias = $evaluacion->cuestionario->lecturas()
            ->where('orden', '<', $orden)
            ->orderBy('orden')
            ->get(['id_lectura', 'orden']);

        abort_unless(
            $lecturasPrevias->count() === $orden - 1,
            Response::HTTP_CONFLICT,
            'No están disponibles todas las lecturas anteriores.',
        );

        foreach ($lecturasPrevias as $indice => $lecturaPrevia) {
            abort_unless(
                (int) $lecturaPrevia->orden === $indice + 1,
                Response::HTTP_CONFLICT,
                'El orden de las lecturas necesita revisión.',
            );

            $preguntaIds = $lecturaPrevia->preguntas()
                ->where('cuestionario_id', $evaluacion->cuestionario_id)
                ->pluck('id_pregunta');

            abort_unless($preguntaIds->count() === 5, Response::HTTP_CONFLICT, 'Una lectura anterior no tiene cinco preguntas.');
            abort_unless(
                $evaluacion->respuestas()
                    ->whereIn('pregunta_id', $preguntaIds)
                    ->whereIn('alternativa', ['A', 'B', 'C', 'D'])
                    ->count() === 5,
                Response::HTTP_FORBIDDEN,
                'Primero guarda las cinco respuestas de cada lectura anterior.',
            );
        }
    }
}
