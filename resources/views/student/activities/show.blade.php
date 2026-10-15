<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $actividad->titulo }} | LectoPlay</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="student-page">
<div class="student-layout">
    @include('student.activities._header')
    <main class="reading-main">
        <a class="reading-back" href="{{ route('panel.estudiante') }}">← Volver a mi espacio</a>
        <div class="reading-hero"><span aria-hidden="true">📚</span><div><p>ACTIVIDAD DE LECTURA</p><h1>{{ $actividad->titulo }}</h1></div></div>
        <article class="reading-card" aria-labelledby="reading-title">
            <h2 id="reading-title">Vamos a leer</h2>
            <div class="reading-text">{{ $actividad->contenido }}</div>
        </article>
        <div class="reading-actions">
            @if ($preguntas->isNotEmpty())
                <p>Hay {{ $preguntas->count() }} {{ $preguntas->count() === 1 ? 'pregunta' : 'preguntas' }} para responder. Puedes intentarlo las veces que quieras.</p>
                <form method="POST" action="{{ route('student.activities.start', $actividad) }}">@csrf <button class="reading-button" type="submit">Comenzar intento →</button></form>
            @else
                <p>Esta lectura aún no tiene preguntas. Vuelve más tarde para responderla.</p>
            @endif
        </div>
    </main>
</div>
</body>
</html>
