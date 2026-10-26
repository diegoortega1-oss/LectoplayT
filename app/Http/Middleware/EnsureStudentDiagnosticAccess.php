<?php

namespace App\Http\Middleware;

use App\Services\DiagnosticoInicialService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentDiagnosticAccess
{
    public function __construct(private readonly DiagnosticoInicialService $diagnostico)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $estudiante = $request->user()->estudiante()->first();

        if (! $estudiante) {
            return response()->view('student.diagnostic.index', [
                'mensaje' => 'No encontramos tu perfil de estudiante. Pide ayuda a tu docente.',
            ], Response::HTTP_CONFLICT);
        }

        $estado = $this->diagnostico->determinarEstado($estudiante);

        return match ($estado) {
            DiagnosticoInicialService::ACCESO_NORMAL,
            DiagnosticoInicialService::DIAGNOSTICO_FINALIZADO => $next($request),
            DiagnosticoInicialService::DIAGNOSTICO_PENDIENTE => redirect()->route('diagnostico.index'),
            DiagnosticoInicialService::DIAGNOSTICO_EN_PROCESO => redirect()->route('diagnostico.en-proceso'),
            DiagnosticoInicialService::SIN_CUESTIONARIO_ACTIVO => response()->view('student.diagnostic.index', [
                'mensaje' => 'El diagnóstico aún no está disponible. Vuelve más tarde o consulta a tu docente.',
            ], Response::HTTP_SERVICE_UNAVAILABLE),
            DiagnosticoInicialService::INCONSISTENCIA => response()->view('student.diagnostic.index', [
                'mensaje' => 'Tus datos del diagnóstico necesitan revisión. Pide ayuda a tu docente.',
            ], Response::HTTP_CONFLICT),
            default => response()->view('student.diagnostic.index', [
                'mensaje' => 'El diagnóstico no está disponible en este momento. Pide ayuda a tu docente.',
            ], Response::HTTP_SERVICE_UNAVAILABLE),
        };
    }
}
