<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Estudiante - SINAPSE</title>
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
        
        .stat-card {
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }
        
        .insignia-card {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #333;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .progress-ring {
            width: 80px;
            height: 80px;
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
                <a class="nav-link" href="/cursos">Todos los Cursos</a>
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
                        <h1 class="display-5 fw-bold">¡Bienvenido, Estudiante!</h1>
                        <p class="lead mb-0">Continúa tu viaje de aprendizaje</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-primary">
                    <h3>{{ $progresos['cursos_completados'] }}</h3>
                    <p class="mb-0">Cursos Completados</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-success">
                    <h3>{{ $progresos['cursos_en_progreso'] }}</h3>
                    <p class="mb-0">Cursos en Progreso</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-warning">
                    <h3>{{ $progresos['puntos_totales'] }}</h3>
                    <p class="mb-0">Puntos Totales</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-info">
                    <h3>{{ $progresos['insignias_obtenidas'] }}</h3>
                    <p class="mb-0">Insignias</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Mis Cursos -->
            <div class="col-lg-8">
                <div class="card card-dashboard mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-book me-2"></i>Mis Cursos</h4>
                    </div>
                    <div class="card-body">
                        @if($cursos_inscritos->count() > 0)
                            <div class="row">
                                @foreach($cursos_inscritos as $curso)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $curso->Titulo ?? 'Curso de Programación' }}</h5>
                                            <p class="card-text text-muted small">
                                                {{ $curso->Descripcion ?? 'Descripción del curso' }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary">{{ $curso->Duracion ?? 0 }} horas</span>
                                                <button class="btn btn-sm btn-outline-primary">Continuar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No estás inscrito en ningún curso todavía.</p>
                                <a href="/cursos" class="btn btn-primary">Explorar Cursos</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Insignias y Progreso -->
            <div class="col-lg-4">
                <!-- Mis Insignias -->
                <div class="card card-dashboard mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="fas fa-trophy me-2"></i>Mis Insignias</h4>
                    </div>
                    <div class="card-body">
                        @if($insignias->count() > 0)
                            <div class="row">
                                @foreach($insignias as $insignia)
                                <div class="col-6 mb-3">
                                    <div class="insignia-card">
                                        <i class="fas fa-medal fa-2x mb-2"></i>
                                        <h6 class="mb-1">{{ $insignia->Nombre ?? 'Insignia Ejemplo' }}</h6>
                                        <small class="text-muted">+{{ $insignia->Valor_Puntos ?? 100 }} pts</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-trophy fa-2x text-muted mb-2"></i>
                                <p class="text-muted small">Aún no has ganado insignias.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Progreso General -->
                <div class="card card-dashboard">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Mi Progreso</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="progress-ring mx-auto mb-2">
                                <i class="fas fa-star fa-3x text-warning"></i>
                            </div>
                            <h4 class="text-success">{{ $progresos['puntos_totales'] }} Puntos</h4>
                            <small class="text-muted">Nivel: Aprendiz</small>
                        </div>
                        
                        <div class="row text-center">
                            <div class="col-4">
                                <h5 class="mb-0">{{ $progresos['cursos_completados'] }}</h5>
                                <small class="text-muted">Completados</small>
                            </div>
                            <div class="col-4">
                                <h5 class="mb-0">{{ $progresos['insignias_obtenidas'] }}</h5>
                                <small class="text-muted">Insignias</small>
                            </div>
                            <div class="col-4">
                                <h5 class="mb-0">{{ $progresos['cursos_en_progreso'] }}</h5>
                                <small class="text-muted">En Progreso</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>