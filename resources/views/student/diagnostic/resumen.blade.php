<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Respuestas registradas | Diagnóstico lector | LectoPlay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="student-page">
<div class="student-layout">
    @include('student.activities._header')
    <main class="reading-main">
        <div class="reading-hero">
            <span aria-hidden="true">📚</span>
            <div>
                <p>MI DIAGNÓSTICO LECTOR</p>
                <h1>¡Tus respuestas están registradas!</h1>
            </div>
        </div>

        @if (session('status'))
            <p class="reading-notice" role="status">{{ session('status') }}</p>
        @endif

        <section class="reading-card" aria-label="Estado del diagnóstico">
            <p>Guardaste tus respuestas de las {{ $totalLecturas }} lecturas. Tu diagnóstico sigue en proceso y todavía está pendiente de finalización.</p>
            <p>Puedes volver a revisar o cambiar tus respuestas mientras el diagnóstico esté en proceso.</p>
        </section>
        <div class="reading-actions">
            <a class="reading-back" href="{{ route('diagnostico.lectura', ['orden' => $totalLecturas]) }}">Volver a la última lectura</a>
        </div>
    </main>
</div>
</body>
</html>
