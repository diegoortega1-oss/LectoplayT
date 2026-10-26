<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultado del diagnóstico | LectoPlay</title>
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
                <h1>¡Terminaste tu diagnóstico!</h1>
            </div>
        </div>

        <section class="reading-card" aria-label="Resultado del diagnóstico">
            <p>Respondiste correctamente {{ $aciertos }} de 15 preguntas.</p>
            <p>Porcentaje de aciertos: {{ number_format((float) $evaluacion->puntaje, 2, ',', '.') }} %.</p>
            <p>Nota vigesimal: {{ number_format($notaVigesimal, 2, ',', '.') }} de 20.</p>
            <p>Tu nivel lector inicial es {{ $evaluacion->nivel->nombre }}.</p>
        </section>
        <div class="reading-actions">
            <a class="reading-button" href="{{ route('panel.estudiante') }}">Ir a mis actividades</a>
        </div>
    </main>
</div>
</body>
</html>
