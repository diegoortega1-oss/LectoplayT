<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Mis intentos | LectoPlay</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="student-page">
<div class="student-layout">
    @include('student.activities._header')
    <main class="reading-main">
        <a class="reading-back" href="{{ route('panel.estudiante') }}">← Volver a mi espacio</a>
        <div class="reading-hero"><span aria-hidden="true">📚</span><div><p>MIS LECTURAS</p><h1>Historial de intentos</h1></div></div>
        @forelse ($intentos as $intento)
            <article class="reading-history-card">
                <div><h2>{{ $intento->actividad?->titulo ?? 'Actividad no disponible' }}</h2><p>Iniciado el {{ $intento->fecha_inicio?->format('d/m/Y') }} · {{ $intento->estado === 'FINALIZADO' ? 'Finalizado' : 'En proceso' }}</p></div>
                @if ($intento->estado === 'FINALIZADO')
                    <a href="{{ route('student.attempts.result', $intento) }}">Ver resultado{{ $intento->puntaje !== null ? ' · '.number_format((float) $intento->puntaje, 2, ',', ' ').'%' : '' }}</a>
                @elseif ($intento->actividad && $intento->actividad->estado === 'ACTIVA' && $intento->actividad->nivel_id === $nivelActualId)
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
