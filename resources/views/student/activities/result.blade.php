<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Resultado | LectoPlay</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="student-page">
<div class="student-layout">
    @include('student.activities._header')
    <main class="reading-main">
        <a class="reading-back" href="{{ route('student.activities.history') }}">← Ver historial</a>
        <div class="reading-hero"><span aria-hidden="true">🌟</span><div><p>INTENTO FINALIZADO</p><h1>{{ $actividad?->titulo ?? 'Actividad de lectura' }}</h1></div></div>
        <section class="reading-score" aria-label="Puntaje obtenido"><span>Tu puntaje</span><strong>{{ number_format((float) $intento->puntaje, 2, ',', ' ') }}%</strong><p>Respondiste este intento el {{ $intento->fecha_fin?->format('d/m/Y') }}.</p></section>
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
