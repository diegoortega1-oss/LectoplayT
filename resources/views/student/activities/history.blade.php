<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Mis intentos | LectoPlay</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="student-page">
<div class="student-layout">
    @include('student.activities._header')
    <main class="reading-main">
        <a class="reading-back" href="{{ route('panel.estudiante') }}">← Volver a mi espacio</a>
        <div class="reading-hero"><span aria-hidden="true">📚</span><div><p>MIS LECTURAS</p><h1>Historial de intentos</h1></div></div>

        @if ($intentos->total() > 0)
            <section class="reading-card" aria-label="Cómo leer tu historial">
                <p><span class="student-section-tag">Primer intento</span> Es la primera vez que terminaste una lectura. Ese resultado es el que puede contar para revisar tu nivel.</p>
                <p><span class="student-section-tag">Volviste a practicar</span> Repetir una lectura te ayuda a mejorar, pero no cambia el resultado de tu primer intento.</p>
            </section>
        @endif

        @forelse ($intentos as $intento)
            @php
                $actividad = $intento->actividad;
                $finalizado = $intento->estado === 'FINALIZADO';
                $idIntento = (int) $intento->getKey();
                $resumen = $resumenActividades->get($intento->actividad_id);
                $esPrimero = $finalizado && $resumen && $resumen['primer_intento_id'] === $idIntento;
                $esMejor = $finalizado && $resumen && $resumen['finalizados'] > 1 && $resumen['mejor_intento_id'] === $idIntento;
                $decisionUsada = $intentosUsados->get($idIntento);
                $fecha = $finalizado ? ($intento->fecha_fin ?? $intento->fecha_inicio) : $intento->fecha_inicio;
            @endphp
            <article class="reading-history-card">
                <div>
                    <h2>{{ $actividad?->titulo ?? 'Actividad no disponible' }}</h2>
                    <p>
                        @if ($actividad?->nivel)Lectura de nivel {{ $actividad->nivel->nombre }} · @endif
                        {{ $finalizado ? 'Terminaste el' : 'Empezaste el' }} {{ $fecha?->format('d/m/Y') }}
                        @unless ($finalizado) · En proceso @endunless
                    </p>
                    @if ($finalizado)
                        <p>
                            @if ($esPrimero)
                                <span class="student-section-tag">Primer intento</span>
                            @else
                                <span class="student-section-tag">Volviste a practicar</span>
                            @endif
                            @if ($esMejor)
                                <span class="student-section-tag"><span aria-hidden="true">⭐</span> Tu mejor resultado</span>
                            @endif
                        </p>
                        <p>
                            @if ($intento->puntaje !== null)
                                Obtuviste {{ number_format((float) $intento->puntaje, 2, ',', ' ') }} %.
                            @endif
                            @if ($resumen && $resumen['finalizados'] > 1 && $resumen['mejor_puntaje'] !== null)
                                Tu mejor resultado en esta lectura: {{ number_format($resumen['mejor_puntaje'], 2, ',', ' ') }} %.
                            @endif
                        </p>
                        <p><small>
                            @if ($esPrimero && in_array($decisionUsada, ['SUBIR', 'BAJAR'], true))
                                <span aria-hidden="true">🧭</span> Esta lectura contó para un cambio de nivel.
                            @elseif ($esPrimero && $decisionUsada !== null)
                                <span aria-hidden="true">🧭</span> Esta lectura contó para revisar tu nivel.
                            @elseif ($esPrimero && in_array($idIntento, $intentosProximaRevision, true))
                                <span aria-hidden="true">⏳</span> Esta lectura cuenta para tu próxima revisión de nivel.
                            @elseif (! $esPrimero)
                                Este resultado es de práctica y no reemplaza al de tu primer intento.
                            @endif
                        </small></p>
                    @endif
                </div>
                @if ($finalizado)
                    <a href="{{ route('student.attempts.result', $intento) }}">Ver resultado →</a>
                @elseif ($actividad && ! $actividad->trashed() && $actividad->estado === 'ACTIVA' && $actividad->nivel_id === $nivelActualId)
                    <a href="{{ route('student.attempts.edit', $intento) }}">Continuar →</a>
                @else
                    <span>No se puede continuar esta actividad</span>
                @endif
            </article>
        @empty
            <div class="student-empty"><span aria-hidden="true">📖</span><h2>Aún no hay intentos</h2><p>Cuando comiences una actividad, aparecerá aquí.</p></div>
        @endforelse
        <div class="reading-pagination">{{ $intentos->links() }}</div>
    </main>
</div>
</body>
</html>
