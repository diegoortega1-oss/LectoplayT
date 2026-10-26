<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Diagnóstico lector | LectoPlay</title>
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
                <h1>{{ isset($mensaje) ? 'Tu diagnóstico lector' : '¡Bienvenido a tu diagnóstico lector!' }}</h1>
            </div>
        </div>

        @if (isset($mensaje))
            <section class="reading-card" aria-label="Estado del diagnóstico">
                <p>{{ $mensaje }}</p>
            </section>
            @if ($continuar ?? false)
                <div class="reading-actions">
                    <a class="reading-button" href="{{ route('diagnostico.lectura', ['orden' => 1]) }}">Continuar diagnóstico</a>
                </div>
            @endif
        @else
            <section class="reading-card" aria-labelledby="diagnostic-intro-title">
                <h2 id="diagnostic-intro-title">Descubre tu nivel de lectura</h2>
                <p>Leerás tres historias y responderás quince preguntas. Tus respuestas nos ayudarán a conocer cómo comprendes lo que lees.</p>
                <p>Al terminar, conocerás tu nivel inicial: Básico, Intermedio o Avanzado. Así podremos mostrarte actividades adecuadas para ti.</p>
            </section>
            <div class="reading-actions">
                <form method="POST" action="{{ route('diagnostico.iniciar') }}">
                    @csrf
                    <button class="reading-button" type="submit">Comenzar diagnóstico</button>
                </form>
            </div>
        @endif
    </main>
</div>
</body>
</html>
