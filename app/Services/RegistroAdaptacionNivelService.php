<?php

namespace App\Services;

use App\Models\ActividadIntento;
use App\Models\AdaptacionNivel;
use App\Models\AdaptacionNivelActividad;
use App\Models\Estudiante;
use App\Models\Nivel;
use Illuminate\Support\Facades\DB;
use LogicException;
use RuntimeException;

/**
 * Registra de forma atómica la decisión calculada por AdaptacionNivelService.
 *
 * Debe ejecutarse dentro de una transacción abierta por el llamador. No inicia ni
 * confirma transacciones propias: cualquier excepción se propaga para que el
 * llamador revierta todo. La serialización se basa en el bloqueo de la fila del
 * estudiante; los índices únicos de adaptacion_nivel_actividad son una defensa
 * adicional contra consumos duplicados.
 */
class RegistroAdaptacionNivelService
{
    private const DECISIONES_REGISTRABLES = [
        AdaptacionNivelService::DECISION_SUBIR,
        AdaptacionNivelService::DECISION_MANTENER,
        AdaptacionNivelService::DECISION_BAJAR,
    ];

    public function __construct(private readonly AdaptacionNivelService $adaptacion)
    {
    }

    /**
     * Evalúa y, si corresponde, registra la decisión adaptativa del estudiante.
     *
     * Devuelve el resultado de AdaptacionNivelService::evaluar() con la clave
     * adicional id_adaptacion, que es null cuando la decisión es PENDIENTE.
     * La instancia recibida no se actualiza; el llamador debe volver a leerla
     * si necesita el nivel resultante.
     */
    public function aplicar(Estudiante $estudiante): array
    {
        if (DB::connection($estudiante->getConnectionName())->transactionLevel() < 1) {
            throw new LogicException('RegistroAdaptacionNivelService::aplicar() requiere una transacción activa.');
        }

        $bloqueado = Estudiante::query()
            ->whereKey($estudiante->getKey())
            ->lockForUpdate()
            ->firstOrFail();

        $resultado = $this->adaptacion->evaluar($bloqueado);

        if ($resultado['decision'] === AdaptacionNivelService::DECISION_PENDIENTE) {
            return $resultado + ['id_adaptacion' => null];
        }

        $this->validarDecision($bloqueado, $resultado);
        $this->validarIntentos($bloqueado, $resultado['actividades_consideradas']);
        $this->validarNoConsumidas($bloqueado, $resultado['actividades_consideradas']);

        $adaptacion = AdaptacionNivel::query()->create([
            'estudiante_id' => $bloqueado->getKey(),
            'nivel_anterior_id' => $resultado['nivel_actual_id'],
            'nivel_resultante_id' => $resultado['nivel_propuesto_id'],
            'decision' => $resultado['decision'],
            'promedio' => $resultado['promedio'],
            'fecha' => now(),
        ]);

        $detalles = array_map(fn (array $actividad): array => [
            'adaptacion_id' => $adaptacion->getKey(),
            'estudiante_id' => $bloqueado->getKey(),
            'actividad_id' => $actividad['actividad_id'],
            'intento_id' => $actividad['intento_id'],
            'puntaje' => number_format($actividad['puntaje'], 2, '.', ''),
        ], $resultado['actividades_consideradas']);

        if (! AdaptacionNivelActividad::query()->insert($detalles)) {
            throw new RuntimeException('No se pudieron registrar las actividades de la adaptación.');
        }

        if ($resultado['decision'] !== AdaptacionNivelService::DECISION_MANTENER) {
            $afectadas = Estudiante::query()
                ->whereKey($bloqueado->getKey())
                ->where('nivel_actual_id', $resultado['nivel_actual_id'])
                ->update(['nivel_actual_id' => $resultado['nivel_propuesto_id']]);

            if ($afectadas !== 1) {
                throw new RuntimeException('No se pudo actualizar el nivel lector del estudiante.');
            }
        }

        return $resultado + ['id_adaptacion' => (int) $adaptacion->getKey()];
    }

    private function validarDecision(Estudiante $bloqueado, array $resultado): void
    {
        if ($resultado['estado'] !== AdaptacionNivelService::ESTADO_EVALUADA
            || ! in_array($resultado['decision'], self::DECISIONES_REGISTRABLES, true)) {
            throw new RuntimeException('La decisión adaptativa no es registrable.');
        }

        if ($bloqueado->nivel_actual_id === null
            || (int) $resultado['nivel_actual_id'] !== (int) $bloqueado->nivel_actual_id) {
            throw new RuntimeException('El nivel anterior no coincide con el nivel actual del estudiante.');
        }

        if ($resultado['nivel_propuesto_id'] === null
            || ! Nivel::query()->whereKey($resultado['nivel_propuesto_id'])->exists()) {
            throw new RuntimeException('El nivel propuesto no existe.');
        }

        $mismoNivel = (int) $resultado['nivel_propuesto_id'] === (int) $resultado['nivel_actual_id'];
        if ($resultado['decision'] === AdaptacionNivelService::DECISION_MANTENER ? ! $mismoNivel : $mismoNivel) {
            throw new RuntimeException('El nivel propuesto no es coherente con la decisión.');
        }

        if (! is_numeric($resultado['promedio']) || $resultado['promedio'] < 0 || $resultado['promedio'] > 100) {
            throw new RuntimeException('El promedio de la adaptación no es válido.');
        }
    }

    /**
     * @param  list<array{actividad_id: int, intento_id: int, puntaje: float}>  $actividades
     */
    private function validarIntentos(Estudiante $bloqueado, array $actividades): void
    {
        $cantidad = AdaptacionNivelService::ACTIVIDADES_POR_BLOQUE;
        $actividadIds = array_map(fn (array $a): int => (int) $a['actividad_id'], $actividades);
        $intentoIds = array_map(fn (array $a): int => (int) $a['intento_id'], $actividades);

        if (count($actividades) !== $cantidad
            || count(array_unique($actividadIds)) !== $cantidad
            || count(array_unique($intentoIds)) !== $cantidad) {
            throw new RuntimeException('La adaptación debe considerar exactamente tres actividades e intentos distintos.');
        }

        $intentos = ActividadIntento::query()
            ->whereIn('id_intento', $intentoIds)
            ->lockForUpdate()
            ->get(['id_intento', 'estudiante_id', 'actividad_id', 'estado', 'puntaje'])
            ->keyBy(fn (ActividadIntento $intento): int => (int) $intento->getKey());

        foreach ($actividades as $actividad) {
            $intento = $intentos->get((int) $actividad['intento_id']);

            if ($intento === null
                || (int) $intento->estudiante_id !== (int) $bloqueado->getKey()
                || (int) $intento->actividad_id !== (int) $actividad['actividad_id']
                || $intento->estado !== 'FINALIZADO') {
                throw new RuntimeException('Un intento considerado no es válido para este estudiante y actividad.');
            }

            if ($intento->puntaje === null
                || ! is_numeric($intento->puntaje)
                || (float) $intento->puntaje < 0
                || (float) $intento->puntaje > 100
                || (int) round((float) $intento->puntaje * 100) !== (int) round($actividad['puntaje'] * 100)) {
                throw new RuntimeException('El puntaje de un intento considerado no es válido.');
            }
        }
    }

    /**
     * @param  list<array{actividad_id: int, intento_id: int, puntaje: float}>  $actividades
     */
    private function validarNoConsumidas(Estudiante $bloqueado, array $actividades): void
    {
        $actividadIds = array_map(fn (array $a): int => (int) $a['actividad_id'], $actividades);
        $intentoIds = array_map(fn (array $a): int => (int) $a['intento_id'], $actividades);

        // Lectura bloqueante con first() y no exists(): exists() colocaría FOR UPDATE
        // dentro de una subconsulta. Se usa el mismo patrón que la gamificación.
        $consumida = AdaptacionNivelActividad::query()
            ->where(fn ($query) => $query
                ->where('estudiante_id', $bloqueado->getKey())
                ->whereIn('actividad_id', $actividadIds))
            ->orWhereIn('intento_id', $intentoIds)
            ->lockForUpdate()
            ->first(['actividad_id']);

        if ($consumida !== null) {
            throw new RuntimeException('Una actividad o intento ya fue utilizado en una adaptación anterior.');
        }
    }
}
