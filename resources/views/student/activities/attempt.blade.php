<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Responder {{ $actividad->titulo }} | LectoPlay</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="student-page">
<div class="student-layout">
    @include('student.activities._header')
    <main class="reading-main">
        <a class="reading-back" href="{{ route('student.activities.show', $actividad) }}">← Volver a la lectura</a>
        <div class="reading-hero"><span aria-hidden="true">✏️</span><div><p>TU INTENTO ESTÁ EN CURSO</p><h1>{{ $actividad->titulo }}</h1></div></div>
        @if (session('status')) <p class="reading-notice" role="status">{{ session('status') }}</p> @endif
        @if ($errors->any()) <p class="reading-error" role="alert">Revisa tus respuestas: {{ $errors->first() }}</p> @endif
        <details class="reading-card reading-recap"><summary>Volver a leer el texto</summary><div class="reading-text">{{ $actividad->contenido }}</div></details>
        <form id="answers-form" method="POST" action="{{ route('student.attempts.save', $intento) }}">
            @csrf
            @foreach ($preguntas as $pregunta)
                <fieldset class="reading-question">
                    <legend><span>Pregunta {{ $loop->iteration }}</span>{{ $pregunta->enunciado }}</legend>
                    @foreach (['A', 'B', 'C', 'D'] as $letra)
                        @php $campo = 'alternativa_'.strtolower($letra); @endphp
                        <label class="reading-option"><input type="radio" name="respuestas[{{ $pregunta->id_pregunta }}]" value="{{ $letra }}" @checked(old('respuestas.'.$pregunta->id_pregunta, $respuestas->get($pregunta->id_pregunta)) === $letra)><span class="reading-option-letter">{{ $letra }}</span><span>{{ $pregunta->{$campo} }}</span></label>
                    @endforeach
                    <label class="reading-option reading-option-skip"><input type="radio" name="respuestas[{{ $pregunta->id_pregunta }}]" value="" @checked(old('respuestas.'.$pregunta->id_pregunta, $respuestas->get($pregunta->id_pregunta)) === null || old('respuestas.'.$pregunta->id_pregunta, $respuestas->get($pregunta->id_pregunta)) === '')><span>Dejar sin responder</span></label>
                </fieldset>
            @endforeach
            <div class="reading-delivery">
                <p>Puedes cambiar tus respuestas antes de entregar. Las preguntas sin responder contarán como incorrectas.</p>
                <div class="reading-actions-row">
                    <button class="reading-button" type="submit">Guardar respuestas</button>
                    <button class="reading-button reading-button-coral" type="submit" formaction="{{ route('student.attempts.finish', $intento) }}">Entregar intento</button>
                </div>
            </div>
        </form>
    </main>
</div>
</body>
</html>
