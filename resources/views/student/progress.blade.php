<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#624bc9">
    <title>Mi progreso | LectoPlay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="student-page">
<div class="student-layout">
    @include('student.activities._header')
    <main class="reading-main">
        <a class="reading-back" href="{{ route('panel.estudiante') }}">← Volver a mi espacio</a>

        <div class="reading-hero">
            <span aria-hidden="true">🗺️</span>
            <div>
                <p>MI PROGRESO</p>
                <h1>Así avanza tu lectura</h1>
            </div>
        </div>

        <section class="reading-card" aria-labelledby="progreso-nivel">
            <h2 id="progreso-nivel">Tu nivel lector</h2>
            @if ($nivelActual)
                <p>Ahora estás en el nivel <strong>{{ $nivelActual->nombre }}</strong>.</p>
                <p aria-label="Niveles de LectoPlay">
                    @foreach ($niveles as $nivel)
                        <span class="student-section-tag">
                            @if ($nivel->id_nivel === $nivelActual->id_nivel)<span aria-hidden="true">⭐</span> <strong>{{ $nivel->nombre }}</strong>@else{{ $nivel->nombre }}@endif
                        </span>
                        @unless ($loop->last)<span aria-hidden="true"> → </span>@endunless
                    @endforeach
                </p>
            @else
                <p>Tu nivel aparecerá aquí cuando termines tu diagnóstico.</p>
            @endif
        </section>

        <section class="reading-card" aria-labelledby="progreso-avance">
            <h2 id="progreso-avance">Tu próxima evaluación</h2>
            @if ($avance['tipo'] === 'contador')
                <p>Llevas <strong>{{ $avance['llevas'] }} de {{ $avance['total'] }}</strong> lecturas para tu próxima evaluación.</p>
                @if ($avance['sinLecturasSuficientes'])
                    <p>Por ahora no hay suficientes lecturas nuevas en tu nivel. ¡Pronto llegarán más! Mientras tanto, puedes repasar las que ya hiciste.</p>
                @else
                    <p>{{ $avance['faltan'] === 1 ? 'Te falta 1 lectura nueva.' : 'Te faltan '.$avance['faltan'].' lecturas nuevas.' }}</p>
                @endif
                <p><small>Solo cuenta la primera vez que terminas cada lectura.</small></p>
            @elseif ($avance['tipo'] === 'completo')
                <p>¡Ya completaste <strong>{{ $avance['total'] }} de {{ $avance['total'] }}</strong> lecturas! Tu nivel se revisará cuando termines tu próxima lectura.</p>
            @else
                <p>Muy pronto podrás ver aquí cuántas lecturas te faltan para tu próxima evaluación. ¡Sigue leyendo!</p>
            @endif
        </section>

        <h2 class="reading-review-title">Tu recorrido</h2>
        @forelse ($recorrido as $evento)
            <article class="reading-history-card">
                <div>
                    <h2><span aria-hidden="true">{{ $evento['icono'] }}</span> {{ $evento['titulo'] }}</h2>
                    <p>{{ $evento['detalle'] }}@if ($evento['promedio']) Promedio de tus 3 lecturas: {{ $evento['promedio'] }}.@endif</p>
                </div>
                @if ($evento['fecha'] !== '')
                    <span>{{ $evento['fecha'] }}</span>
                @endif
            </article>
        @empty
            <div class="student-empty">
                <span aria-hidden="true">🧭</span>
                <h3>Tu recorrido empieza aquí</h3>
                <p>Cuando completes tu diagnóstico y tus lecturas, aquí verás cómo avanza tu nivel.</p>
            </div>
        @endforelse

        @if (count($recorrido) === 1 && $puedeVerDiagnostico)
            <p class="reading-result-note">Cuando termines 3 lecturas nuevas de tu nivel, aquí aparecerá tu siguiente paso.</p>
        @endif

        <div class="reading-actions">
            <p>
                @if ($puedeVerDiagnostico)
                    ¿Quieres recordar cómo te fue en tu diagnóstico inicial?
                @else
                    Sigue leyendo para avanzar en tu camino lector.
                @endif
            </p>
            <div class="reading-actions-row">
                @if ($puedeVerDiagnostico)
                    <a class="reading-button" href="{{ route('diagnostico.resultado') }}">Ver mi diagnóstico</a>
                @endif
                <a class="reading-button reading-button-coral" href="{{ route('panel.estudiante') }}">Ir a mis lecturas</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
