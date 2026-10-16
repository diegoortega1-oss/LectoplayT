<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Resultado | LectoPlay</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="student-page">
<div class="student-layout">
    @include('student.activities._header')
    <main class="reading-main">
        <a class="reading-back" href="{{ route('student.activities.history') }}">← Ver historial</a>
        <div class="reading-hero"><span aria-hidden="true">🌟</span><div><p>INTENTO FINALIZADO</p><h1>{{ $actividad?->titulo ?? 'Actividad de lectura' }}</h1></div></div>
        <section class="reading-result" aria-label="Resultado del intento">
            <div class="reading-result-heading">
                <div>
                    <span class="reading-result-eyebrow">✦ TU RESULTADO</span>
                    <h2>¡Así te fue en esta lectura!</h2>
                </div>
                @if ($intento->fecha_fin)
                    <p class="reading-result-date"><span aria-hidden="true">📅</span> Respondiste este intento el {{ $intento->fecha_fin->format('d/m/Y') }}.</p>
                @endif
            </div>
            <div class="reading-result-grid">
                <article class="reading-result-tile reading-result-percent">
                    <span class="reading-result-icon" aria-hidden="true">🌟</span>
                    <span class="reading-result-label">Porcentaje de este intento</span>
                    <strong>{{ number_format((float) $intento->puntaje, 2, ',', ' ') }}%</strong>
                </article>
                <article class="reading-result-tile reading-result-correct">
                    <span class="reading-result-icon" aria-hidden="true">✓</span>
                    <span class="reading-result-label">Respuestas correctas</span>
                    <strong>{{ $correctas }}</strong>
                </article>
                <article class="reading-result-tile reading-result-incorrect">
                    <span class="reading-result-icon" aria-hidden="true">✎</span>
                    <span class="reading-result-label">Respuestas incorrectas</span>
                    <strong>{{ $incorrectas }}</strong>
                    <small>Incluye las preguntas sin responder</small>
                </article>
                <article class="reading-result-tile reading-result-earned">
                    <span class="reading-result-icon" aria-hidden="true">✦</span>
                    <span class="reading-result-label">Puntos obtenidos en este intento</span>
                    @if ($intento->puntos_obtenidos === null)
                        <p>Dato no disponible para este intento.</p>
                    @elseif ($intento->puntos_obtenidos === 0)
                        <strong>0</strong><small>0 puntos obtenidos. Este intento no sumó puntos.</small>
                    @else
                        <strong>{{ number_format($intento->puntos_obtenidos, 0, ',', ' ') }}</strong>
                        <small>{{ number_format($intento->puntos_obtenidos, 0, ',', ' ') }} puntos obtenidos.</small>
                    @endif
                </article>
                <article class="reading-result-tile reading-result-total">
                    <span class="reading-result-icon" aria-hidden="true">🏆</span>
                    <span class="reading-result-label">Mis puntos acumulados</span>
                    @if ($gamificacion)
                        <strong>{{ number_format($gamificacion->puntos, 0, ',', ' ') }}</strong>
                        <small>{{ number_format($gamificacion->puntos, 0, ',', ' ') }} puntos en total.</small>
                    @else
                        <p>Aún no hay puntos registrados.</p>
                    @endif
                </article>
            </div>
            @if (config('gamification.attempt_cutoff_id') === null)
                <p class="reading-result-note">Las recompensas por actividades aún no están activas.</p>
            @endif
        </section>
        <h2 class="reading-review-title">Revisa tus respuestas</h2>
        @forelse ($preguntas as $pregunta)
            @php
                $respuesta = $respuestas->get($pregunta->id_pregunta);
                $letraCorrecta = strtoupper($pregunta->respuesta_correcta);
                $campoCorrecto = 'alternativa_'.strtolower($letraCorrecta);
                $seleccion = $respuesta?->alternativa;
                $campoSeleccionado = $seleccion ? 'alternativa_'.strtolower($seleccion) : null;
                $esCorrecta = $seleccion !== null && $seleccion === $pregunta->respuesta_correcta;
            @endphp
            <article class="reading-review-card">
                <span class="reading-review-status {{ $esCorrecta ? 'is-correct' : 'is-incorrect' }}">{{ $esCorrecta ? 'Correcta' : 'Incorrecta' }}</span>
                <h3>Pregunta {{ $loop->iteration }}: {{ $pregunta->enunciado }}</h3>
                <p>Tu respuesta: <strong>{{ $seleccion && in_array($seleccion, ['A', 'B', 'C', 'D'], true) ? $seleccion.' — '.$pregunta->{$campoSeleccionado} : 'Sin responder' }}</strong></p>
                <p>Respuesta correcta: <strong>{{ in_array($letraCorrecta, ['A', 'B', 'C', 'D'], true) ? $letraCorrecta.' — '.$pregunta->{$campoCorrecto} : $letraCorrecta }}</strong></p>
            </article>
        @empty
            <p class="reading-card">Las preguntas de esta actividad ya no están disponibles para revisar.</p>
        @endforelse
        @if ($puedeReintentar)
            <form method="POST" action="{{ route('student.activities.start', $actividad) }}">@csrf <button class="reading-button" type="submit">Intentar de nuevo</button></form>
        @endif
    </main>
</div>
</body>
</html>
