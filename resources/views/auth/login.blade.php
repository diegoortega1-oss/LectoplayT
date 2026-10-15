<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f7ff">
    <title>Ingresar | LectoPlay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="welcome-panel" aria-labelledby="welcome-title">
            <a class="brand" href="{{ url('/') }}" aria-label="LectoPlay, inicio">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 36 36" fill="none"><path d="M5.5 8.5c4.7-.8 8.8.2 12.5 3v18c-3.7-2.8-7.8-3.8-12.5-3V8.5Z" fill="currentColor"/><path d="M30.5 8.5c-4.7-.8-8.8.2-12.5 3v18c3.7-2.8 7.8-3.8 12.5-3V8.5Z" fill="currentColor" opacity=".65"/><path d="M18 11.5v18" stroke="white" stroke-width="1.7" stroke-linecap="round"/></svg>
                </span>
                <span>Lecto<span>Play</span></span>
            </a>

            <div class="welcome-copy">
                <span class="eyebrow"><span></span> APRENDER TAMBIÉN ES UNA AVENTURA</span>
                <h1 id="welcome-title">Cada historia<br>te lleva <em>más lejos.</em></h1>
                <p>Explora, aprende y descubre todo lo que puedes lograr leyendo.</p>
            </div>

            <div class="reading-art" role="img" aria-label="Ilustración de libros abiertos y una estrella">
                <div class="art-orbit orbit-one"></div>
                <div class="art-orbit orbit-two"></div>
                <span class="art-star star-one">✦</span>
                <span class="art-star star-two">✧</span>
                <span class="art-dot dot-one"></span>
                <span class="art-dot dot-two"></span>
                <div class="book book-back"><span></span><span></span><span></span></div>
                <div class="book book-front">
                    <div class="book-page page-left"><i></i><i></i><i></i><i></i></div>
                    <div class="book-page page-right"><i></i><i></i><i></i><i></i></div>
                    <div class="book-spine"></div>
                </div>
                <div class="art-caption"><span class="caption-icon">✦</span><span>Tu próxima aventura<br><strong>comienza aquí</strong></span></div>
            </div>

            <p class="panel-footer">Lee a tu ritmo. Crece a tu manera.</p>
        </section>

        <section class="form-panel" aria-labelledby="login-title">
            <div class="mobile-brand" aria-hidden="true">Lecto<span>Play</span></div>
            <div class="login-card">
                <div class="form-heading">
                    <span class="form-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/><path d="M4.5 20c.8-3.1 3.4-5 7.5-5s6.7 1.9 7.5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <p class="form-kicker">QUÉ BUENO TENERTE DE VUELTA</p>
                    <h2 id="login-title">Inicia sesión</h2>
                    <p class="form-description">Ingresa tus datos para continuar aprendiendo.</p>
                </div>

                <form class="login-form" method="POST" action="{{ route('login.attempt') }}" autocomplete="on">
                    @csrf
                    <div class="field-group">
                        <label for="login-user">Usuario</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M5 20c.7-3.2 3-5 7-5s6.3 1.8 7 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            <input id="login-user" name="usuario" type="text" placeholder="Tu usuario" value="{{ old('usuario') }}" autocomplete="username" required maxlength="255" aria-describedby="user-hint{{ $errors->has('usuario') ? ' user-error' : '' }}" @error('usuario') aria-invalid="true" @enderror>
                        </div>
                        <span class="field-hint" id="user-hint">Usa el nombre de usuario asociado a tu cuenta.</span>
                        @error('usuario') <span class="field-error" id="user-error" role="alert">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group">
                        <div class="label-row"><label for="login-password">Contraseña</label></div>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4.5" y="10" width="15" height="10" rx="2.2" stroke="currentColor" stroke-width="1.7"/><path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M12 14v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            <input id="login-password" name="password" type="password" placeholder="Escribe tu contraseña" autocomplete="current-password" required aria-describedby="password-hint{{ $errors->has('password') ? ' password-error' : '' }}" @error('password') aria-invalid="true" @enderror>
                            <button class="password-toggle" type="button" aria-label="Mostrar contraseña" aria-pressed="false" data-password-toggle>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 12s3.2-6 9-6 9 6 9 6-3.2 6-9 6-9-6-9-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                            </button>
                        </div>
                        <span class="field-hint" id="password-hint">Ingresa tu contraseña para acceder.</span>
                        @error('password') <span class="field-error" id="password-error" role="alert">{{ $message }}</span> @enderror
                    </div>

                    <button class="submit-button" type="submit">Ingresar <span aria-hidden="true">→</span></button>
                </form>

                <div class="form-divider"><span></span><i>UN ESPACIO PARA CRECER</i><span></span></div>
                <p class="signup-note">¿Primera vez por aquí? <span>¡Qué alegría tenerte!</span></p>
            </div>
            <footer class="form-footer"><span>© {{ date('Y') }} LectoPlay</span><span>Hecho para aprender con alegría <b>✦</b></span></footer>
        </section>
    </main>

    <script>
        const password = document.getElementById('login-password');
        const toggle = document.querySelector('[data-password-toggle]');
        toggle.addEventListener('click', () => {
            const isVisible = password.type === 'text';
            password.type = isVisible ? 'password' : 'text';
            toggle.setAttribute('aria-pressed', String(!isVisible));
            toggle.setAttribute('aria-label', isVisible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });

    </script>
</body>
</html>
