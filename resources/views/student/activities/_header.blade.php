<header class="student-header">
    <a class="student-logo" href="{{ route('panel.estudiante') }}" aria-label="LectoPlay, inicio del estudiante">
        <span class="student-logo-icon" aria-hidden="true">📖</span><span>Lecto<span>Play</span></span>
    </a>
    <nav class="student-nav" aria-label="Navegación de actividades">
        <a href="{{ route('panel.estudiante') }}">Mi espacio</a>
        <a href="{{ route('student.activities.history') }}">Historial</a>
    </nav>
    <form method="POST" action="{{ route('logout') }}">@csrf <button class="student-logout" type="submit">Cerrar sesión</button></form>
</header>
