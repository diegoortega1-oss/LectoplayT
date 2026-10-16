<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadIntento;
use App\Models\Estudiante;
use App\Models\Pregunta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentActivityController extends Controller
{
    public function show(Request $request, Actividad $actividad): View
    {
        $estudiante = $this->student($request);
        $this->ensureAvailable($actividad, $estudiante);

        $preguntas = $this->visibleQuestions($actividad);

        return view('student.activities.show', compact('actividad', 'preguntas'));
    }

    public function start(Request $request, Actividad $actividad): RedirectResponse
    {
        $estudiante = $this->student($request);
        $this->ensureAvailable($actividad, $estudiante);

        abort_if(! $actividad->preguntas()->exists(), 409, 'Esta actividad aún no tiene preguntas.');

        $intento = $estudiante->intentos()->create([
            'actividad_id' => $actividad->getKey(),
            'fecha_inicio' => now(),
            'estado' => 'EN_PROCESO',
        ]);

        return redirect()->route('student.attempts.edit', $intento);
    }

    public function edit(Request $request, ActividadIntento $intento): View
    {
        $estudiante = $this->student($request);
        $this->ensureOwned($intento, $estudiante);
        abort_if($intento->estado !== 'EN_PROCESO', 409, 'Este intento ya fue entregado.');

        $actividad = Actividad::findOrFail($intento->actividad_id);
        $this->ensureAvailable($actividad, $estudiante);
        $preguntas = $this->visibleQuestions($actividad);
        $respuestas = $intento->respuestas()->pluck('alternativa', 'pregunta_id');

        return view('student.activities.attempt', compact('actividad', 'intento', 'preguntas', 'respuestas'));
    }

    public function save(Request $request, ActividadIntento $intento): RedirectResponse
    {
        $estudiante = $this->student($request);
        $this->ensureOwned($intento, $estudiante);

        $selecciones = $this->validatedSelections($request);

        DB::transaction(function () use ($intento, $estudiante, $selecciones): void {
            $actual = $this->lockedAttempt($intento, $estudiante);
            abort_if($actual->estado !== 'EN_PROCESO', 409, 'Este intento ya fue entregado.');

            $actividad = Actividad::findOrFail($actual->actividad_id);
            $this->ensureAvailable($actividad, $estudiante);
            $preguntas = $actividad->preguntas()->get()->keyBy('id_pregunta');
            $this->storeAnswers($actual, $preguntas, $selecciones);
        });

        return redirect()->route('student.attempts.edit', $intento)->with('status', 'Tus respuestas se guardaron.');
    }

    public function finish(Request $request, ActividadIntento $intento): RedirectResponse
    {
        $estudiante = $this->student($request);
        $this->ensureOwned($intento, $estudiante);
        $selecciones = $this->validatedSelections($request);

        DB::transaction(function () use ($intento, $estudiante, $selecciones): void {
            // Todas las entregas de un estudiante toman este bloqueo antes del del intento.
            // Así, dos intentos distintos no pueden decidir la recompensa simultáneamente.
            Estudiante::whereKey($estudiante->getKey())->lockForUpdate()->firstOrFail();
            $actual = $this->lockedAttempt($intento, $estudiante);
            abort_if($actual->estado !== 'EN_PROCESO', 409, 'Este intento ya fue entregado.');

            $actividad = Actividad::findOrFail($actual->actividad_id);
            $this->ensureAvailable($actividad, $estudiante);
            $preguntas = $actividad->preguntas()->get()->keyBy('id_pregunta');
            abort_if($preguntas->isEmpty(), 409, 'Esta actividad aún no tiene preguntas.');
            $this->storeAnswers($actual, $preguntas, $selecciones);

            $correctas = 0;
            foreach ($actual->respuestas()->get() as $respuesta) {
                $pregunta = $preguntas->get($respuesta->pregunta_id);
                if (! $pregunta) {
                    continue;
                }

                $esCorrecta = $respuesta->alternativa === $pregunta->respuesta_correcta;
                if ($respuesta->es_correcta !== $esCorrecta) {
                    $respuesta->es_correcta = $esCorrecta;
                    $respuesta->save();
                }
                if ($esCorrecta) {
                    $correctas++;
                }
            }

            $actual->puntaje = round($correctas * 100 / $preguntas->count(), 2);
            $actual->fecha_fin = now();
            $actual->estado = 'FINALIZADO';

            $cutoff = config('gamification.attempt_cutoff_id');
            // Los históricos no se reconstruyen. En las nuevas entregas registramos
            // cero salvo que se confirme un incremento dentro de esta transacción.
            $actual->puntos_obtenidos = is_int($cutoff) && $cutoff >= 0 && $actual->getKey() <= $cutoff
                ? null
                : 0;
            if (is_int($cutoff) && $cutoff >= 0 && $actual->getKey() > $cutoff) {
                // Lectura bloqueante: debe observar finalizaciones confirmadas tras esperar
                // el bloqueo del estudiante, incluso con el aislamiento MySQL REPEATABLE READ.
                $alreadyRewarded = $estudiante->intentos()
                    ->where('actividad_id', $actual->actividad_id)
                    ->where('id_intento', '>', $cutoff)
                    ->where('id_intento', '!=', $actual->getKey())
                    ->where('estado', 'FINALIZADO')
                    ->lockForUpdate()
                    ->first(['id_intento']) !== null;

                if (! $alreadyRewarded) {
                    $gamificacion = $estudiante->gamificacion()->firstOrCreate([], ['puntos' => 0]);
                    if ($correctas > 0) {
                        $puntosOtorgados = $correctas * 5;
                        if ($gamificacion->increment('puntos', $puntosOtorgados) !== 1) {
                            throw new \RuntimeException('No se pudo registrar la recompensa del intento.');
                        }
                        $actual->puntos_obtenidos = $puntosOtorgados;
                    }
                }
            }

            $actual->save();
        });

        return redirect()->route('student.attempts.result', $intento);
    }

    public function result(Request $request, ActividadIntento $intento): View
    {
        $estudiante = $this->student($request);
        $this->ensureOwned($intento, $estudiante);
        abort_unless($intento->estado === 'FINALIZADO', 404);

        $actividad = Actividad::withTrashed()->find($intento->actividad_id);
        $puedeReintentar = $actividad && ! $actividad->trashed()
            && $actividad->estado === 'ACTIVA'
            && $actividad->nivel_id === $estudiante->nivel_actual_id;
        $preguntas = Pregunta::where('actividad_id', $intento->actividad_id)->orderBy('id_pregunta')->get();
        $respuestas = $intento->respuestas()->get()->keyBy('pregunta_id');
        $correctas = $preguntas->filter(function (Pregunta $pregunta) use ($respuestas): bool {
            $respuesta = $respuestas->get($pregunta->id_pregunta);

            return $respuesta !== null && $respuesta->alternativa === $pregunta->respuesta_correcta;
        })->count();
        $incorrectas = $preguntas->count() - $correctas;
        $gamificacion = $estudiante->gamificacion()->first();

        return view('student.activities.result', compact('actividad', 'intento', 'preguntas', 'respuestas', 'correctas', 'incorrectas', 'puedeReintentar', 'gamificacion'));
    }

    public function history(Request $request): View
    {
        $estudiante = $this->student($request);
        $intentos = $estudiante->intentos()->with('actividad')->orderByDesc('fecha_inicio')->paginate(10);

        $nivelActualId = $estudiante->nivel_actual_id;

        return view('student.activities.history', compact('intentos', 'nivelActualId'));
    }

    private function student(Request $request): Estudiante
    {
        return $request->user()->estudiante()->firstOrFail();
    }

    private function ensureAvailable(Actividad $actividad, Estudiante $estudiante): void
    {
        abort_unless($actividad->estado === 'ACTIVA' && $actividad->nivel_id === $estudiante->nivel_actual_id, 404);
    }

    private function ensureOwned(ActividadIntento $intento, Estudiante $estudiante): void
    {
        abort_unless($intento->estudiante_id === $estudiante->getKey(), 404);
    }

    private function lockedAttempt(ActividadIntento $intento, Estudiante $estudiante): ActividadIntento
    {
        return $estudiante->intentos()->whereKey($intento->getKey())->lockForUpdate()->firstOrFail();
    }

    private function visibleQuestions(Actividad $actividad)
    {
        return $actividad->preguntas()
            ->select('id_pregunta', 'actividad_id', 'enunciado', 'alternativa_a', 'alternativa_b', 'alternativa_c', 'alternativa_d')
            ->orderBy('id_pregunta')
            ->get();
    }

    private function validatedSelections(Request $request): array
    {
        $validated = $request->validate([
            'respuestas' => ['sometimes', 'array'],
            'respuestas.*' => ['nullable', Rule::in(['A', 'B', 'C', 'D'])],
        ]);

        return $validated['respuestas'] ?? [];
    }

    private function storeAnswers(ActividadIntento $intento, Collection $preguntas, array $selecciones): void
    {
        foreach (array_keys($selecciones) as $idPregunta) {
            if (! ctype_digit((string) $idPregunta) || ! $preguntas->has((int) $idPregunta)) {
                throw ValidationException::withMessages([
                    'respuestas' => 'Una pregunta no pertenece a esta actividad.',
                ]);
            }
        }

        foreach ($preguntas as $pregunta) {
            $alternativa = $selecciones[$pregunta->getKey()] ?? null;

            if ($alternativa === null || $alternativa === '') {
                $intento->respuestas()->where('pregunta_id', $pregunta->getKey())->delete();
                continue;
            }

            $intento->respuestas()->updateOrCreate(
                ['pregunta_id' => $pregunta->getKey()],
                [
                    'alternativa' => $alternativa,
                    'es_correcta' => $alternativa === $pregunta->respuesta_correcta,
                ],
            );
        }
    }
}
