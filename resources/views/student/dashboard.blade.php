<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#624bc9">
    <title>Mi espacio | LectoPlay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="student-page">
    <div class="student-layout">
        <header class="student-header">
            <a class="student-logo" href="{{ route('panel.estudiante') }}" aria-label="LectoPlay, inicio del estudiante">
                <span class="student-logo-icon" aria-hidden="true">📖</span>
                <span>Lecto<span>Play</span></span>
            </a>
            <nav class="student-nav" aria-label="Secciones del panel">
                <a href="#inicio">Inicio</a>
                <a href="#actividades">Actividades</a>
                <a href="{{ route('student.activities.history') }}">Historial</a>
                <a href="#logros">Mis logros</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="student-logout" type="submit">Cerrar sesión <span aria-hidden="true">↗</span></button>
            </form>
        </header>

        <main id="inicio" class="student-main">
            <section class="student-hero" aria-labelledby="student-welcome">
                <div class="student-hero-copy">
                    <span class="student-kicker"><span aria-hidden="true">✦</span> TU RINCÓN DE LECTURA</span>
                    <h1 id="student-welcome">¡Hola, <span>{{ $estudiante?->nombre ?? 'lector' }}</span>! <span aria-hidden="true">👋</span></h1>
                    <p>Un nuevo día para descubrir historias y aprender jugando.</p>
                    <div class="student-level-pill">
                        <span aria-hidden="true">🌟</span>
                        @if ($nivelActual)
                            Tu nivel: <strong>{{ $nivelActual->nombre }}</strong>
                        @else
                            Tu nivel aparecerá aquí cuando esté disponible
                        @endif
                    </div>
                </div>
                <div class="student-hero-art" role="img" aria-label="Libro abierto con estrellas de colores">
                    <span class="student-spark student-spark-one" aria-hidden="true">✦</span>
                    <span class="student-spark student-spark-two" aria-hidden="true">✧</span>
                    <span class="student-spark student-spark-three" aria-hidden="true">✦</span>
                    <div class="student-book">
                        <div class="student-book-page"><span></span><span></span><span></span></div>
                        <div class="student-book-page"><span></span><span></span><span></span></div>
                    </div>
                </div>
            </section>

            <section class="student-stats" aria-label="Mi avance">
                <article class="student-stat student-stat-points">
                    <span class="student-stat-icon" aria-hidden="true">⭐</span>
                    <div>
                        <p>Mis puntos acumulados</p>
                        @if ($gamificacion)
                            <strong>{{ number_format($gamificacion->puntos, 0, ',', ' ') }}</strong>
                        @else
                            <span class="student-stat-empty">Aún no hay puntos registrados</span>
                        @endif
                    </div>
                </article>
                <article class="student-stat student-stat-done">
                    <span class="student-stat-icon" aria-hidden="true">📚</span>
                    <div>
                        <p>Actividades completadas</p>
                        <strong>{{ number_format($actividadesCompletadas, 0, ',', ' ') }}</strong>
                    </div>
                </article>
                <article class="student-stat student-stat-badges">
                    <span class="student-stat-icon" aria-hidden="true">🏅</span>
                    <div>
                        <p>Logros obtenidos</p>
                        <strong>{{ $logros->count() }}</strong>
                    </div>
                </article>
            </section>

            <div class="student-content-grid">
                <section id="actividades" class="student-section" aria-labelledby="student-activities-title">
                    <div class="student-section-heading">
                        <div>
                            <span class="student-section-kicker">PARA EXPLORAR</span>
                            <h2 id="student-activities-title">Actividades para ti <span aria-hidden="true">🎨</span></h2>
                        </div>
                        @if ($nivelActual)
                            <span class="student-section-tag">{{ $nivelActual->nombre }}</span>
                        @endif
                    </div>

                    @forelse ($actividadesActivas as $actividad)
                        <article class="student-activity-card">
                            <span class="student-activity-icon" aria-hidden="true">{{ $loop->odd ? '📘' : '📗' }}</span>
                            <div class="student-activity-copy">
                                <span class="student-section-tag">{{ $actividad->tiene_intento_finalizado ? 'Resuelta' : ($actividad->intento_pendiente_id ? 'En progreso' : 'Nueva') }}</span>
                                <h3><a href="{{ route('student.activities.show', $actividad) }}">{{ $actividad->titulo }}</a></h3>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($actividad->contenido), 110) }}</p>
                                @if ($actividad->ultimo_porcentaje !== null)
                                    <p>Último porcentaje: {{ number_format((float) $actividad->ultimo_porcentaje, 2, ',', ' ') }}%</p>
                                @endif
                            </div>
                            @if ($actividad->tiene_intento_finalizado)
                                <a class="student-activity-label" href="{{ route('student.activities.show', $actividad) }}">Repetir actividad →</a>
                            @elseif ($actividad->intento_pendiente_id)
                                <a class="student-activity-label" href="{{ route('student.attempts.edit', $actividad->intento_pendiente_id) }}">Continuar intento →</a>
                            @else
                                <a class="student-activity-label" href="{{ route('student.activities.show', $actividad) }}">Leer actividad →</a>
                            @endif
                        </article>
                    @empty
                        <div class="student-empty">
                            <span aria-hidden="true">📖</span>
                            <h3>Aquí aparecerán tus próximas lecturas</h3>
                            <p>Por ahora no hay actividades activas para tu nivel.</p>
                        </div>
                    @endforelse
                </section>

                <section id="logros" class="student-section student-achievements" aria-labelledby="student-achievements-title">
                    <div class="student-section-heading">
                        <div>
                            <span class="student-section-kicker">PEQUEÑAS GRANDES VICTORIAS</span>
                            <h2 id="student-achievements-title">Mis logros <span aria-hidden="true">✨</span></h2>
                        </div>
                    </div>

                    @forelse ($logros as $logro)
                        <article class="student-achievement-card">
                            <span class="student-achievement-icon" aria-hidden="true">🏆</span>
                            <div>
                                <h3>{{ $logro->nombre }}</h3>
                                <p>{{ $logro->descripcion }}</p>
                                @if ($logro->pivot?->fecha_obtencion)
                                    <small>Obtenido el {{ \Illuminate\Support\Carbon::parse($logro->pivot->fecha_obtencion)->format('d/m/Y') }}</small>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="student-empty">
                            <span aria-hidden="true">🏅</span>
                            <h3>Tus logros llegarán pronto</h3>
                            <p>Cuando consigas uno, podrás verlo aquí.</p>
                        </div>
                    @endforelse
                </section>
            </div>
        </main>
        <footer class="student-footer">LectoPlay <span aria-hidden="true">✦</span> Cada página abre un mundo.</footer>
    </div>
</body>
</html>
