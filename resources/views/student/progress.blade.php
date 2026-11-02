<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#624bc9">
    <title>Mi progreso | LectoPlay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Estilos propios de la gráfica de evolución: se definen aquí para no requerir recompilar los recursos de Vite. --}}
    <style>
        .evolucion-grafica { display: flex; gap: 10px; margin-top: 18px; }
        .evolucion-escala { display: flex; flex: none; flex-direction: column; justify-content: space-between; height: 180px; margin-top: 26px; color: #8c85a3; font-size: 11px; font-weight: 700; text-align: right; }
        .evolucion-barras { display: flex; flex: 1; gap: 12px; min-width: 0; margin: 0; padding: 0 0 10px; overflow-x: auto; list-style: none; }
        .evolucion-item { display: flex; flex: 0 0 92px; flex-direction: column; align-items: center; gap: 5px; text-align: center; }
        .evolucion-valor { color: #332763; font-size: 14px; font-weight: 850; line-height: 21px; }
        .evolucion-columna { display: flex; align-items: flex-end; width: 44px; height: 180px; overflow: hidden; border-radius: 12px 12px 6px 6px; background: repeating-linear-gradient(to top, #f4f1fc 0, #f4f1fc 89px, #e6e0f7 89px, #e6e0f7 90px); }
        .evolucion-barra { display: block; width: 100%; min-height: 3px; border-radius: 10px 10px 4px 4px; background: #9a8fbd; }
        .evolucion-nivel-basico { background: #2fb5a6; }
        .evolucion-nivel-intermedio { background: #f0a43a; }
        .evolucion-nivel-avanzado { background: #6a52cf; }
        .evolucion-titulo { display: -webkit-box; overflow: hidden; color: #3d315f; font-size: 11px; font-weight: 800; line-height: 1.3; -webkit-box-orient: vertical; -webkit-line-clamp: 3; }
        .evolucion-fecha, .evolucion-nivel { color: #77718e; font-size: 11px; line-height: 1.3; }
        .evolucion-nivel { padding: 3px 8px; border-radius: 999px; background: #ebe5ff; color: #5d47bf; font-weight: 800; }
        @media (max-width: 480px) {
            .evolucion-escala { display: none; }
            .evolucion-item { flex-basis: 80px; }
        }
    </style>
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

        <section class="reading-card" aria-labelledby="progreso-evolucion">
            <h2 id="progreso-evolucion">Evolución de mis lecturas</h2>
            @if (count($evolucion) === 0)
                <div class="student-empty">
                    <span aria-hidden="true">📊</span>
                    <h3>Aún no hay resultados</h3>
                    <p>Cuando termines tus primeras lecturas, podrás ver aquí tus resultados.</p>
                </div>
            @else
                @if (count($evolucion) === 1)
                    <p>Este es el resultado de la primera vez que terminaste una lectura. Cuando termines más lecturas, aquí podrás compararlos.</p>
                @else
                    <p>Cada barra muestra el resultado de la primera vez que terminaste una lectura, de la más antigua a la más reciente.</p>
                @endif
                <p><small>Las veces que volviste a practicar no aparecen aquí. Puedes verlas en tu historial.</small></p>

                <div class="evolucion-grafica">
                    <div class="evolucion-escala" aria-hidden="true"><span>100 %</span><span>50 %</span><span>0 %</span></div>
                    <ol class="evolucion-barras" aria-label="Resultados de tus lecturas, de la más antigua a la más reciente">
                        @foreach ($evolucion as $punto)
                            @php
                                $altura = $punto['porcentaje'] === null ? 0 : max(0, min(100, $punto['porcentaje']));
                                $claseNivel = match ($punto['nivel']) {
                                    'Básico' => 'evolucion-nivel-basico',
                                    'Intermedio' => 'evolucion-nivel-intermedio',
                                    'Avanzado' => 'evolucion-nivel-avanzado',
                                    default => '',
                                };
                            @endphp
                            <li class="evolucion-item">
                                <span class="evolucion-valor">{{ $punto['porcentaje'] === null ? 'Sin dato' : number_format($punto['porcentaje'], fmod($punto['porcentaje'], 1.0) == 0.0 ? 0 : 2, ',', ' ').' %' }}</span>
                                <span class="evolucion-columna" aria-hidden="true">
                                    <span class="evolucion-barra {{ $claseNivel }}" style="height: {{ number_format($altura, 2, '.', '') }}%"></span>
                                </span>
                                <span class="evolucion-titulo" title="{{ $punto['titulo'] }}">{{ $loop->iteration }}. {{ $punto['titulo'] }}</span>
                                <span class="evolucion-fecha">{{ $punto['fecha'] ?? 'Fecha no registrada' }}</span>
                                @if ($punto['nivel'])
                                    <span class="evolucion-nivel">{{ $punto['nivel'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </section>

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
