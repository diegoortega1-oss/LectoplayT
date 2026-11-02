<?php

namespace App\Services;

use App\Models\Actividad;
use App\Models\ActividadIntento;
use App\Models\AdaptacionNivel;
use App\Models\AdaptacionNivelActividad;
use App\Models\Estudiante;
use App\Models\Nivel;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Calcula la decisión de adaptación del nivel lector a partir de las actividades.
 *
 * Servicio de solo lectura: no actualiza el nivel del estudiante ni registra
 * decisiones. Usa el nivel_actual_id del modelo recibido; al integrarlo, el
 * llamador debe pasar un estudiante leído dentro de su transacción.
 */
class AdaptacionNivelService
{
    public const ESTADO_EVALUADA = 'EVALUADA';

    public const ESTADO_PENDIENTE = 'PENDIENTE';

    public const DECISION_SUBIR = 'SUBIR';

    public const DECISION_MANTENER = 'MANTENER';

    public const DECISION_BAJAR = 'BAJAR';

    public const DECISION_PENDIENTE = 'PENDIENTE';

    public const MOTIVO_SIN_NIVEL_ACTUAL = 'SIN_NIVEL_ACTUAL';

    public const MOTIVO_NIVELES_INCONSISTENTES = 'NIVELES_INCONSISTENTES';

    public const MOTIVO_PERIODO_INDETERMINADO = 'PERIODO_INDETERMINADO';

    public const MOTIVO_ACTIVIDADES_INSUFICIENTES = 'ACTIVIDADES_INSUFICIENTES';

    public const MOTIVO_PROMEDIO_ALTO = 'PROMEDIO_ALTO';

    public const MOTIVO_PROMEDIO_MEDIO = 'PROMEDIO_MEDIO';

    public const MOTIVO_PROMEDIO_BAJO = 'PROMEDIO_BAJO';

    public const MOTIVO_LIMITE_SUPERIOR = 'LIMITE_SUPERIOR';

    public const MOTIVO_LIMITE_INFERIOR = 'LIMITE_INFERIOR';

    public const ACTIVIDADES_POR_BLOQUE = 3;

    public const UMBRAL_SUBIR = 80;

    public const UMBRAL_MANTENER = 60;

    /**
     * @return array{
     *     estado: string,
     *     motivo: string,
     *     cantidad_actividades: int,
     *     promedio: float|null,
     *     nivel_actual_id: int|null,
     *     nivel_propuesto_id: int|null,
     *     decision: string,
     *     inicio_periodo: string|null,
     *     actividades_consideradas: list<array{actividad_id: int, intento_id: int, puntaje: float}>
     * }
     */
    public function evaluar(Estudiante $estudiante): array
    {
        $nivelActualId = $estudiante->nivel_actual_id !== null ? (int) $estudiante->nivel_actual_id : null;

        if ($nivelActualId === null) {
            return $this->pendiente(self::MOTIVO_SIN_NIVEL_ACTUAL, null);
        }

        $niveles = Nivel::query()
            ->orderBy('rango_min')
            ->orderBy('id_nivel')
            ->get(['id_nivel', 'rango_min']);

        $posicion = $niveles->search(fn (Nivel $nivel): bool => (int) $nivel->getKey() === $nivelActualId);

        if ($posicion === false) {
            return $this->pendiente(self::MOTIVO_SIN_NIVEL_ACTUAL, $nivelActualId);
        }

        // Si dos niveles comparten rango_min, el orden entre ellos no está definido.
        $rangosDistintos = $niveles->pluck('rango_min')->map(fn ($valor): string => (string) $valor)->unique()->count();
        if ($rangosDistintos !== $niveles->count()) {
            return $this->pendiente(self::MOTIVO_NIVELES_INCONSISTENTES, $nivelActualId);
        }

        $inicioPeriodo = $this->inicioPeriodo($estudiante, $nivelActualId);

        if ($inicioPeriodo === null) {
            return $this->pendiente(self::MOTIVO_PERIODO_INDETERMINADO, $nivelActualId);
        }

        $elegibles = $this->actividadesElegibles($estudiante, $nivelActualId, $inicioPeriodo);
        $consideradas = $elegibles->take(self::ACTIVIDADES_POR_BLOQUE)->values();

        if ($consideradas->count() < self::ACTIVIDADES_POR_BLOQUE) {
            return $this->pendiente(
                self::MOTIVO_ACTIVIDADES_INSUFICIENTES,
                $nivelActualId,
                $inicioPeriodo,
                $elegibles->count(),
                $consideradas,
            );
        }

        // Los puntajes son DECIMAL(5,2). Se suman en centésimas enteras para comparar
        // el promedio exacto, sin redondeo previo ni error de coma flotante:
        // promedio >= umbral  <=>  suma_centesimas >= umbral * 100 * cantidad.
        $sumaCentesimas = $consideradas->sum(fn (array $actividad): int => (int) round($actividad['puntaje'] * 100));
        $promedio = round($sumaCentesimas / (100 * self::ACTIVIDADES_POR_BLOQUE), 2);

        if ($sumaCentesimas >= self::UMBRAL_SUBIR * 100 * self::ACTIVIDADES_POR_BLOQUE) {
            $siguiente = $niveles->get($posicion + 1);
            [$decision, $nivelPropuestoId, $motivo] = $siguiente
                ? [self::DECISION_SUBIR, (int) $siguiente->getKey(), self::MOTIVO_PROMEDIO_ALTO]
                : [self::DECISION_MANTENER, $nivelActualId, self::MOTIVO_LIMITE_SUPERIOR];
        } elseif ($sumaCentesimas >= self::UMBRAL_MANTENER * 100 * self::ACTIVIDADES_POR_BLOQUE) {
            [$decision, $nivelPropuestoId, $motivo] = [self::DECISION_MANTENER, $nivelActualId, self::MOTIVO_PROMEDIO_MEDIO];
        } else {
            $anterior = $posicion > 0 ? $niveles->get($posicion - 1) : null;
            [$decision, $nivelPropuestoId, $motivo] = $anterior
                ? [self::DECISION_BAJAR, (int) $anterior->getKey(), self::MOTIVO_PROMEDIO_BAJO]
                : [self::DECISION_MANTENER, $nivelActualId, self::MOTIVO_LIMITE_INFERIOR];
        }

        return [
            'estado' => self::ESTADO_EVALUADA,
            'motivo' => $motivo,
            'cantidad_actividades' => $elegibles->count(),
            'promedio' => $promedio,
            'nivel_actual_id' => $nivelActualId,
            'nivel_propuesto_id' => $nivelPropuestoId,
            'decision' => $decision,
            'inicio_periodo' => $inicioPeriodo->toDateTimeString(),
            'actividades_consideradas' => $consideradas->all(),
        ];
    }

    /**
     * Fecha del evento más reciente que estableció el nivel actual, o null si
     * no hay evidencia suficiente o el evento no coincide con el nivel actual.
     */
    private function inicioPeriodo(Estudiante $estudiante, int $nivelActualId): ?CarbonInterface
    {
        $diagnostico = $estudiante->evaluaciones()
            ->where('tipo', 'DIAGNOSTICA')
            ->where('estado', 'FINALIZADA')
            ->whereNotNull('cuestionario_id')
            ->whereNotNull('fecha_fin')
            ->orderByDesc('fecha_fin')
            ->orderByDesc('id_evaluacion')
            ->first(['id_evaluacion', 'fecha_fin', 'nivel_id']);

        $adaptacion = AdaptacionNivel::query()
            ->where('estudiante_id', $estudiante->getKey())
            ->whereIn('decision', [self::DECISION_SUBIR, self::DECISION_BAJAR])
            ->whereNotNull('fecha')
            ->orderByDesc('fecha')
            ->orderByDesc('id_adaptacion')
            ->first(['id_adaptacion', 'fecha', 'nivel_resultante_id']);

        $eventos = [];
        if ($diagnostico) {
            $eventos[] = ['fecha' => $diagnostico->fecha_fin, 'nivel_id' => $diagnostico->nivel_id];
        }
        if ($adaptacion) {
            $eventos[] = ['fecha' => $adaptacion->fecha, 'nivel_id' => $adaptacion->nivel_resultante_id];
        }

        if ($eventos === []) {
            return null;
        }

        // Con fechas iguales prevalece la adaptación, que se evalúa en segundo lugar.
        $ultimo = null;
        foreach ($eventos as $evento) {
            if ($ultimo === null || $evento['fecha']->gte($ultimo['fecha'])) {
                $ultimo = $evento;
            }
        }

        if ($ultimo['nivel_id'] === null || (int) $ultimo['nivel_id'] !== $nivelActualId) {
            return null;
        }

        return $ultimo['fecha'];
    }

    /**
     * Primer intento global finalizado de cada actividad que cumple las reglas,
     * ordenado del más antiguo al más reciente.
     *
     * @return Collection<int, array{actividad_id: int, intento_id: int, puntaje: float}>
     */
    private function actividadesElegibles(Estudiante $estudiante, int $nivelActualId, CarbonInterface $inicioPeriodo): Collection
    {
        $intentos = ActividadIntento::query()
            ->where('estudiante_id', $estudiante->getKey())
            ->where('estado', 'FINALIZADO')
            ->get(['id_intento', 'actividad_id', 'puntaje', 'fecha_inicio', 'fecha_fin']);

        // Primer intento global por actividad, por fecha_fin ascendente y luego id_intento.
        // Un intento sin fecha_fin se ubica primero, igual que NULL en un ORDER BY
        // ascendente de MariaDB. Así no se sustituye por un reintento posterior y la
        // actividad queda excluida al validar ese primer intento.
        $primeros = $intentos
            ->groupBy('actividad_id')
            ->map(fn (Collection $grupo): ActividadIntento => $grupo
                ->sort(fn (ActividadIntento $a, ActividadIntento $b): int => $this->compararIntentos($a, $b))
                ->first())
            ->filter(fn (ActividadIntento $intento): bool => $intento->fecha_fin !== null
                && $intento->fecha_inicio !== null
                && $this->puntajeValido($intento->puntaje)
                && $intento->fecha_inicio->gte($inicioPeriodo)
                && $intento->fecha_fin->gt($inicioPeriodo));

        if ($primeros->isEmpty()) {
            return collect();
        }

        $actividadIds = $primeros->keys()->map(fn ($id): int => (int) $id)->all();

        // Incluye actividades inactivas y eliminadas con borrado lógico.
        $delNivel = Actividad::withTrashed()
            ->whereIn('id_actividad', $actividadIds)
            ->where('nivel_id', $nivelActualId)
            ->pluck('id_actividad')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $consumidas = AdaptacionNivelActividad::query()
            ->where('estudiante_id', $estudiante->getKey())
            ->whereIn('actividad_id', $actividadIds)
            ->pluck('actividad_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        return $primeros
            ->filter(fn (ActividadIntento $intento): bool => in_array((int) $intento->actividad_id, $delNivel, true)
                && ! in_array((int) $intento->actividad_id, $consumidas, true))
            ->sort(fn (ActividadIntento $a, ActividadIntento $b): int => $this->compararIntentos($a, $b))
            ->map(fn (ActividadIntento $intento): array => [
                'actividad_id' => (int) $intento->actividad_id,
                'intento_id' => (int) $intento->getKey(),
                'puntaje' => (float) $intento->puntaje,
            ])
            ->values();
    }

    /**
     * Orden por fecha_fin ascendente, con fecha_fin nula primero, y luego por id_intento.
     */
    private function compararIntentos(ActividadIntento $a, ActividadIntento $b): int
    {
        if ($a->fecha_fin === null || $b->fecha_fin === null) {
            $porFecha = ($a->fecha_fin === null ? 0 : 1) <=> ($b->fecha_fin === null ? 0 : 1);
        } else {
            $porFecha = $a->fecha_fin->getTimestamp() <=> $b->fecha_fin->getTimestamp();
        }

        return $porFecha !== 0 ? $porFecha : ((int) $a->getKey() <=> (int) $b->getKey());
    }

    private function puntajeValido(mixed $puntaje): bool
    {
        return $puntaje !== null
            && is_numeric($puntaje)
            && (float) $puntaje >= 0
            && (float) $puntaje <= 100;
    }

    private function pendiente(
        string $motivo,
        ?int $nivelActualId,
        ?CarbonInterface $inicioPeriodo = null,
        int $cantidad = 0,
        ?Collection $consideradas = null,
    ): array {
        return [
            'estado' => self::ESTADO_PENDIENTE,
            'motivo' => $motivo,
            'cantidad_actividades' => $cantidad,
            'promedio' => null,
            'nivel_actual_id' => $nivelActualId,
            'nivel_propuesto_id' => $nivelActualId,
            'decision' => self::DECISION_PENDIENTE,
            'inicio_periodo' => $inicioPeriodo?->toDateTimeString(),
            'actividades_consideradas' => $consideradas?->all() ?? [],
        ];
    }
}
