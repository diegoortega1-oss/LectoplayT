<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $lectura->titulo }} | Diagnóstico lector | LectoPlay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="student-page">
<div class="student-layout">
    @include('student.activities._header')
    <main class="reading-main">
        <a class="reading-back" href="{{ route('diagnostico.en-proceso') }}">← Volver al diagnóstico</a>
        <div class="reading-hero">
            <span aria-hidden="true">📖</span>
            <div>
                <p>Lectura {{ $lectura->orden }} de {{ $totalLecturas }}</p>
                <h1>{{ $lectura->titulo }}</h1>
            </div>
        </div>

        <article class="reading-card" aria-labelledby="diagnostic-reading-title">
            <h2 id="diagnostic-reading-title">Lee con atención</h2>
            <div class="reading-text">{{ $lectura->contenido }}</div>
        </article>

        @if (session('status'))
            <p class="reading-notice" role="status">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p class="reading-error" role="alert">Revisa tus respuestas: {{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('diagnostico.guardar', ['orden' => $lectura->orden]) }}">
            @csrf
            @foreach ($preguntas as $pregunta)
                <fieldset class="reading-question">
                    <legend><span>Pregunta {{ $loop->iteration }}</span>{{ $pregunta->enunciado }}</legend>
                    @foreach (['A', 'B', 'C', 'D'] as $letra)
                        @php $campo = 'alternativa_'.strtolower($letra); @endphp
                        <label class="reading-option">
                            <input type="radio" name="respuestas[{{ $pregunta->id_pregunta }}]" value="{{ $letra }}" @checked(old('respuestas.'.$pregunta->id_pregunta, $respuestas->get($pregunta->id_pregunta)) === $letra) required>
                            <span class="reading-option-letter">{{ $letra }}</span>
                            <span>{{ $pregunta->{$campo} }}</span>
                        </label>
                    @endforeach
                </fieldset>
            @endforeach
            <div class="reading-actions">
                @if ($ordenAnterior !== null)
                    <a class="reading-back" href="{{ route('diagnostico.lectura', ['orden' => $ordenAnterior]) }}">Volver a la lectura anterior</a>
                @endif
                <button class="reading-button" type="submit">Guardar respuestas</button>
            </div>
        </form>
    </main>
</div>
</body>
</html>
