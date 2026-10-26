<?php

namespace App\Services;

use App\Models\CuestionarioDiagnostico;
use App\Models\Estudiante;

class DiagnosticoInicialService
{
    public const ACCESO_NORMAL = 'ACCESO_NORMAL';

    public const DIAGNOSTICO_PENDIENTE = 'DIAGNOSTICO_PENDIENTE';

    public const DIAGNOSTICO_EN_PROCESO = 'DIAGNOSTICO_EN_PROCESO';

    public const DIAGNOSTICO_FINALIZADO = 'DIAGNOSTICO_FINALIZADO';

    public const INCONSISTENCIA = 'INCONSISTENCIA';

    public const SIN_CUESTIONARIO_ACTIVO = 'SIN_CUESTIONARIO_ACTIVO';

    public const CONFIGURACION_INVALIDA = 'CONFIGURACION_INVALIDA';

    /**
     * Determina el estado del diagnóstico inicial sin modificar datos.
     */
    public function determinarEstado(Estudiante $estudiante): string
    {
        $cuestionariosActivos = CuestionarioDiagnostico::query()
            ->where('estado', 'ACTIVO')
            ->limit(2)
            ->get(['id_cuestionario']);

        if ($cuestionariosActivos->count() > 1) {
            return self::CONFIGURACION_INVALIDA;
        }

        $cuestionario = $cuestionariosActivos->first();

        if (! $cuestionario) {
            return $estudiante->nivel_actual_id !== null
                ? self::ACCESO_NORMAL
                : self::SIN_CUESTIONARIO_ACTIVO;
        }

        $evaluacion = $estudiante->evaluaciones()
            ->where('cuestionario_id', $cuestionario->getKey())
            ->first(['id_evaluacion', 'estado']);

        if ($evaluacion) {
            if ($evaluacion->estado === 'EN_PROCESO') {
                return self::DIAGNOSTICO_EN_PROCESO;
            }

            if ($evaluacion->estado === 'FINALIZADA') {
                return $estudiante->nivel_actual_id !== null
                    ? self::DIAGNOSTICO_FINALIZADO
                    : self::INCONSISTENCIA;
            }

            return self::INCONSISTENCIA;
        }

        return $estudiante->nivel_actual_id !== null
            ? self::ACCESO_NORMAL
            : self::DIAGNOSTICO_PENDIENTE;
    }
}
