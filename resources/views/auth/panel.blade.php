<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel {{ $titulo }} | LectoPlay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="min-height:100vh;display:grid;place-items:center;background:#f6f7ff;color:#252344;font-family:system-ui,sans-serif">
    <main style="width:min(90vw,480px);padding:2.5rem;border-radius:1.5rem;background:white;box-shadow:0 18px 48px #39317316">
        <p style="color:#6255d6;font-weight:700">LectoPlay</p>
        <h1 style="margin:1rem 0;font-size:1.8rem">Panel de {{ strtolower($titulo) }}</h1>
        <p>Has iniciado sesión como {{ $titulo }}.</p>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:2rem">
            @csrf
            <button type="submit" style="padding:.8rem 1.2rem;border:0;border-radius:.7rem;background:#6255d6;color:white;cursor:pointer">Cerrar sesión</button>
        </form>
    </main>
</body>
</html>
