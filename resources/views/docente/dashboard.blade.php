<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Docente - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #28a745;
        }
        
        .sinapse-header {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
        }
        
        .card-dashboard {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
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
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Inicio</a>
                <a class="nav-link" href="/cursos">Cursos</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Header Bienvenida -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card card-dashboard sinapse-header text-white">
                    <div class="card-body text-center py-4">
                        <h1 class="display-5 fw-bold">¡Bienvenido, Docente!</h1>
                        <p class="lead mb-0">Gestiona tus cursos y estudiantes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Docente -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-primary">
                    <h3>{{ $estadisticas_docente['cursos_impartidos'] }}</h3>
                    <p class="mb-0">Cursos Impartidos</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-success">
                    <h3>{{ $estadisticas_docente['estudiantes_total'] }}</h3>
                    <p class="mb-0">Estudiantes Total</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-warning">
                    <h3>{{ $estadisticas_docente['evaluaciones_pendientes'] }}</h3>
                    <p class="mb-0">Evaluaciones Pendientes</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-info">
                    <h3>{{ $estadisticas_docente['rating_promedio'] }}</h3>
                    <p class="mb-0">Rating Promedio</p>
                </div>
            </div>
        </div>

        <!-- Mis Cursos -->
        <div class="row">
            <div class="col-12">
                <div class="card card-dashboard">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Mis Cursos</h4>
                    </div>
                    <div class="card-body">
                        @if($mis_cursos->count() > 0)
                            <div class="row">
                                @foreach($mis_cursos as $curso)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $curso->Titulo ?? 'Curso de Ejemplo' }}</h5>
                                            <p class="card-text text-muted">
                                                {{ $curso->Descripcion ?? 'Descripción del curso' }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary">{{ $curso->Duracion ?? 0 }} horas</span>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-primary me-1">Gestionar</button>
                                                    <button class="btn btn-sm btn-outline-success">Estudiantes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No tienes cursos asignados.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>