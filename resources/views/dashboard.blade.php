<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SINAPSE - Sistema de Aprendizaje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        .sinapse-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .card-dashboard {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .card-dashboard:hover {
            transform: translateY(-5px);
        }
        
        .role-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .role-card:hover {
            border-color: var(--primary);
            color: inherit;
            text-decoration: none;
        }
        
        .stat-card {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sinapse-header">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="sinapse-header text-white py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Sistema de Aprendizaje SINAPSE</h1>
            <p class="lead mb-4">Plataforma educativa con gamificación y seguimiento de progreso</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Estadísticas Rápidas -->
<div class="row mb-5">
    <div class="col-md-3 mb-3">
        <a href="/estudiantes" class="text-decoration-none">
            <div class="stat-card clickable">
                <h3>{{ $estadisticas['total_estudiantes'] }}</h3>
                <p class="mb-0">Estudiantes</p>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="/docentes" class="text-decoration-none">
            <div class="stat-card clickable">
                <h3>{{ $estadisticas['total_docentes'] }}</h3>
                <p class="mb-0">Docentes</p>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="/cursos" class="text-decoration-none">
            <div class="stat-card clickable">
                <h3>{{ $estadisticas['total_cursos'] }}</h3>
                <p class="mb-0">Cursos</p>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="/insignias" class="text-decoration-none">
            <div class="stat-card clickable">
                <h3>{{ $estadisticas['total_insignias'] }}</h3>
                <p class="mb-0">Insignias</p>
            </div>
        </a>
    </div>
</div>
        <!-- Selección de Rol -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2>Acceder como:</h2>
            </div>
            <div class="col-md-4 mb-4">
                <a href="/estudiante" class="role-card card-dashboard">
                    <i class="fas fa-user-graduate fa-4x text-primary mb-3"></i>
                    <h3>Estudiante</h3>
                    <p class="text-muted">Accede a tus cursos, progreso y insignias</p>
                    <div class="btn btn-primary">Entrar como Estudiante</div>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="/docente" class="role-card card-dashboard">
                    <i class="fas fa-chalkboard-teacher fa-4x text-success mb-3"></i>
                    <h3>Docente</h3>
                    <p class="text-muted">Gestiona cursos y evalúa estudiantes</p>
                    <div class="btn btn-success">Entrar como Docente</div>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="/admin" class="role-card card-dashboard">
                    <i class="fas fa-cog fa-4x text-warning mb-3"></i>
                    <h3>Administrador</h3>
                    <p class="text-muted">Administra el sistema y usuarios</p>
                    <div class="btn btn-warning">Entrar como Admin</div>
                </a>
            </div>
        </div>

        <!-- Cursos Destacados -->
        <div class="row">
            <div class="col-12 mb-4">
                <h3>Cursos Destacados</h3>
            </div>
            @foreach($cursos_destacados as $curso)
            <div class="col-md-4 mb-3">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $curso->Titulo }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($curso->Descripcion, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">{{ $curso->Duracion }}h</span>
                            <span class="text-success fw-bold">Gratis</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>