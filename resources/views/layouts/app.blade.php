<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SINAPSE - Sistema de Aprendizaje')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
            --dark: #343a40;
        }
        
        .sinapse-bg {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
        }
        
        .card-gamification {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .card-gamification:hover {
            transform: translateY(-5px);
        }
        
        .insignia-card {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #333;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            margin: 0.5rem;
        }
        
        .progress-ring {
            width: 80px;
            height: 80px;
        }
        
        .course-card {
            border-left: 4px solid var(--primary);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sinapse-bg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            
            <div class="navbar-nav ms-auto">
                <!-- En el navbar, cambiar: -->
    @if(session('usuario'))
        <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-user me-1"></i>{{ session('usuario')->Nombre }}
        </a>
        <!-- ... resto del código ... -->
    </div>
    @endif
            </div>
        </div>
    </nav>

    <!-- Contenido -->
    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>