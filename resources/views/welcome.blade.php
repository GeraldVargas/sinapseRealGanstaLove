<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Sinapse</title>
    <style>
        body { font-family: sans-serif; margin: 0; line-height: 1.6; }
        .navbar {
            position: fixed;
            top: 0;
            right: 0;
            padding: 20px;
            z-index: 10;
        }
        .navbar a {
            margin-left: 15px;
            text-decoration: none;
            font-weight: bold;
            color: #007bff;
        }
        .content {
            display: grid;
            place-content: center;
            min-height: 100vh;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="navbar">
        {{-- ESTA ES LA LÓGICA DE BREEZE --}}
        @if (Route::has('login'))
            @auth
                {{-- Si el usuario YA ESTÁ LOGUEADO --}}
                <a href="{{ url('/dashboard') }}">Panel de Control</a>
            @else
                {{-- Si el usuario NO ESTÁ LOGUEADO --}}
                <a href="{{ route('login') }}">Iniciar Sesión</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Registrarse</a>
                @endif
            @endauth
        @endif
    </div>

    <div class="content">
        <h1>¡Bienvenido a Sinapse!</h1>
        <p>Tu plataforma de aprendizaje.</p>

        @auth
            {{-- Mostramos "Ver Cursos" solo si el usuario está logueado --}}
            {{-- (Esto coincide con el middleware 'auth' que pusimos en la ruta) --}}
            <p><a href="{{ route('cursos.index') }}">Ver Cursos</a></p>
        @endauth
    </div>

</body>
</html>